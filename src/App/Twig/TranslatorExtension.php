<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 17.01.19
 * Time: 14:07
 */

namespace App\Twig;


use App\Domain\Service\Translator;
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
     * TranslatorExtension constructor.
     *
     * @param Translator      $translator
     * @param Session         $session
     * @param KernelInterface $kernel
     * @param LocalExtractor  $localExtractor
     */
    public function __construct(Translator $translator, Session $session, KernelInterface $kernel, LocalExtractor $localExtractor)
    {
        $this->translator     = $translator;
        $this->session        = $session;
        $this->kernel         = $kernel;
        $this->localExtractor = $localExtractor;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('translate', [$this, 'translate'])
        ];
    }

    /**
     * @param string $translationKey
     * @param        $carrierId
     * @param        $languageCode
     *
     * @return string|null
     * @throws WrongTranslationKey
     */
    public function translate(string $translationKey, $carrierId = null, $languageCode = null)
    {
        if ($this->kernel->getEnvironment() == 'test') {
            return $translationKey;
        }
        if (is_null($carrierId)) {
            $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
            $carrierId        = $ispDetectionData['carrier_id'];
        }
        if (is_null($languageCode)) {
            $languageCode = $this->localExtractor->getLocal();
        }
        $translation = $this->translator->translate($translationKey, $carrierId, $languageCode);


        if (is_null($translation) && $this->kernel->isDebug()) {
            throw new WrongTranslationKey("Translation key doesn't exist: \"{$translationKey}\"");
        }

        return $translation;
    }
}