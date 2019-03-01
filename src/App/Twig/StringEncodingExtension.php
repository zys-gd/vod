<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 25-02-19
 * Time: 13:00
 */

namespace App\Twig;


class StringEncodingExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('html2string', [$this, 'html2string'])
        ];
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function html2string(string $string): string
    {
        $converted = preg_replace('/\r|\n/m', '', $string);
        // $converted = htmlspecialchars($converted);
        return addslashes($converted);
    }
}