<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 07-02-19
 * Time: 14:34
 */

namespace App\Domain\Service\Games;


use App\Domain\Entity\Game;
use App\Domain\Entity\GameBuild;
use App\Domain\Service\AWSS3\S3Client;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\RequestStack;

class DrmApkProvider
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var S3Client
     */
    private $s3Client;
    /**
     * @var string
     */
    private $uploadedBuildsPath;
    /**
     * @var string
     */
    private $drmApiUrl;
    /**
     * @var string
     */
    private $drmAuthorizeKey;
    /**
     * @var string
     */
    private $usedFileSystemStorage;
    /**
     * @var string
     */
    private $s3rootUrl;
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(EntityManager $entityManager,
        s3Client $s3Client,
        RequestStack $requestStack,
        string $uploadedBuildsPath,
        string $drmApiUrl,
        string $drmAuthorizeKey,
        string $usedFileSystemStorage,
        string $s3rootUrl)
    {
        $this->entityManager = $entityManager;
        $this->s3Client = $s3Client;
        $this->uploadedBuildsPath = $uploadedBuildsPath;
        $this->drmApiUrl = $drmApiUrl;
        $this->drmAuthorizeKey = $drmAuthorizeKey;
        $this->usedFileSystemStorage = $usedFileSystemStorage;
        $this->s3rootUrl = $s3rootUrl;
        $this->requestStack = $requestStack;
    }

    public function getDRMApkUrl(GameBuild $build)
    {
        $apk_path = sprintf("/%s/%s", $this->uploadedBuildsPath, $build->getGameApk());
        switch ($this->usedFileSystemStorage) {
            case 's3':
                $uri = $this->s3rootUrl . $apk_path;
                $uri = $this->makeRequestToDRM($uri, $build);
                if (!$apkKey = $this->extractKeyFromUri($uri)) {
                    return null;
                }
                $command = $this->s3Client->getCommand('GetObject', [
                    'Bucket'                     => 'drm-apk',
                    'Key'                        => $apkKey,
                    'ResponseContentDisposition' => sprintf(
                        'attachment; filename=%s',
                        $this->generateFilename($build->getGame())
                    ),
                ]);
                $request = $this->s3Client->createPresignedRequest($command, '+30 second');
                return (string)$request->getUri();
            case 'local':
            default:
                $uri = $this->requestStack->getCurrentRequest()->getUriForPath($apk_path);
                return $this->makeRequestToDRM($uri, $build);
        }
    }

    private function makeRequestToDRM(string $apkURI, GameBuild $build)
    {
        if (stripos($apkURI, '.vxp') !== false) {
            return $apkURI;
        }
        $drm_apk_version = $build->getApkVersion();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->drmApiUrl."get_apk");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, sprintf("_format=json&id=%s&location=%s", $drm_apk_version, $apkURI));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-type: application/x-www-form-urlencoded',
            'X-AUTHORIZE-KEY: ' . $this->drmAuthorizeKey
        ));
        $drm_api_answer = curl_exec($ch);
        $status         = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status != 200) {

            return null;
        };

        try {
            $resp = \GuzzleHttp\json_decode($drm_api_answer);
            return $resp->location_url;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function extractKeyFromUri($uri)
    {
        $matches = [];
        preg_match('/.*drm-apk\/(.*)/', $uri, $matches);
        $key = $matches[1] ?? null;
        $key = urldecode($key);
        return $key;
    }

    private function generateFilename(Game $game)
    {
        if ($game->getIsBookmark()) {
            $fileExtension = '.vxp';
        } else {
            $fileExtension = '.apk';
        }
        return $game->getSlug() . $fileExtension;
    }
}