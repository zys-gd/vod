<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 17.01.19
 * Time: 14:07
 */

namespace App\Twig;


class TranslatorExtension extends \Twig_Extension
{

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('translate', function (string $key) {
                return $key;
            })
        ];
    }


}