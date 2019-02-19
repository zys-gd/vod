<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 17.01.19
 * Time: 14:07
 */

namespace App\Twig;


use App\Domain\Service\Translator\DataAggregator;
use App\Domain\Service\Translator\Translator;
use App\Domain\Service\Translator\ShortcodeReplacer;
use App\Exception\WrongTranslationKey;
use ExtrasBundle\Utils\LocalExtractor;
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\KernelInterface;

class TranslatorExtension extends \Twig_Extension
{
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var Session
     */
    private $session;
    /**
     * @var KernelInterface
     */
    private $kernel;
    /**
     * @var LocalExtractor
     */
    private $localExtractor;
    /**
     * @var ShortcodeReplacer
     */
    private $replacer;
    /**
     * @var DataAggregator
     */
    private $dataAggregator;

    /**
     * TranslatorExtension constructor.
     *
     * @param Translator        $translator
     * @param Session           $session
     * @param KernelInterface   $kernel
     * @param LocalExtractor    $localExtractor
     * @param ShortcodeReplacer $replacer
     * @param DataAggregator    $dataAggregator
     */
    public function __construct(Translator $translator,
        Session $session,
        KernelInterface $kernel,
        LocalExtractor $localExtractor,
        ShortcodeReplacer $replacer, DataAggregator $dataAggregator)
    {
        $this->translator = $translator;
        $this->session = $session;
        $this->kernel = $kernel;
        $this->localExtractor = $localExtractor;
        $this->replacer = $replacer;
        $this->dataAggregator = $dataAggregator;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('translate', [$this, 'translate']),
            new \Twig_SimpleFunction('translateWithoutReplace', [$this, 'translateWithoutReplace'])
        ];
    }

    /**
     * @param string $translationKey
     * @param array  $parameters
     *
     * @return string|null
     * @throws WrongTranslationKey
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function translate(string $translationKey, array $parameters = []): ?string
    {
        $translation = $this->translateWithoutReplace($translationKey);
        $detectionData = $this->extractDetectionData();

        $shortcodeValues = $this->dataAggregator->getGlobalParameters($detectionData['billingCarrierId']);
        return $this->replacer->do(array_merge($shortcodeValues, $parameters), $translation);
    }

    /**
     * @param string $translationKey
     *
     * @return string|null
     * @throws WrongTranslationKey
     */
    public function translateWithoutReplace(string $translationKey): ?string
    {
        if ($this->kernel->getEnvironment() == 'test') {
            return $translationKey;
        }

        $detectionData = $this->extractDetectionData();
        $translation = $this->translator->translate($translationKey, $detectionData['billingCarrierId'], $detectionData['languageCode']);

        if (is_null($translation) && $this->kernel->isDebug()) {
            throw new WrongTranslationKey("Translation key doesn't exist: \"{$translationKey}\"");
        }

        return $translation;
    }

    /**
     * @return array
     */
    private function extractDetectionData()
    {
        $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
        $billingCarrierId = $ispDetectionData['carrier_id'];
        $languageCode = $this->localExtractor->getLocal();

        return [
            'billingCarrierId' => $billingCarrierId,
            'languageCode' => $languageCode
        ];
    }
}