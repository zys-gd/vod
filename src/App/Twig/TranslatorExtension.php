<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 17.01.19
 * Time: 14:07
 */

namespace App\Twig;


use App\Domain\Service\Translator;

class TranslatorExtension extends \Twig_Extension
{
    /**
     * @var Translator
     */
    private $translator;


    /**
     * TranslatorExtension constructor.
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('translate', function (string $key) {
                return $this->translator->translate($key);
            })
        ];
    }


}