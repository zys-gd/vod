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
use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use Symfony\Component\HttpFoundation\RequestStack;
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
     * @var RequestStack
     */
    private $requestStack;


    /**
     * TranslatorExtension constructor.
     *
     * @param Translator      $translator
     * @param Session         $session
     * @param KernelInterface $kernel
     */
    public function __construct(Translator $translator, Session $session, KernelInterface $kernel, RequestStack $requestStack)
    {
        $this->translator = $translator;
        $this->session = $session;
        $this->kernel = $kernel;
        $this->requestStack = $requestStack;
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
        if(is_null($carrierId)) {
            $ispDetectionData = IdentificationFlowDataExtractor::extractIspDetectionData($this->session);
            $carrierId = $ispDetectionData['carrier_id'];
        }
        if(is_null($languageCode)) {
            $languageCode = $this->requestStack->getCurrentRequest()->server->get('HTTP_ACCEPT_LANGUAGE', 'en');
        }
        $translation = $this->translator->translate($translationKey, $carrierId, $languageCode);

        if(is_null($translation) && $this->kernel->isDebug()) {
            throw new WrongTranslationKey("Translation key doesn't exist");
        }
        return $translation;
    }
}