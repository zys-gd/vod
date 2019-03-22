<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 14-02-19
 * Time: 15:17
 */

namespace App\Twig;
use Twig\Extension\AbstractExtension;
use \Twig\TwigFunction;

class FileRegistrarExtension extends AbstractExtension
{
    /** @var array */
    private $css = [];
    /** @var array */
    private $js = [];

    private $cssPattern = '<link href="%href%" rel="stylesheet">';
    private $jsPattern = '<script type="text/javascript" src="%href%"></script>';

    /**
     * add path to file
     * @param string $css
     */
    public function addCss(string $css): void
    {
        $this->css[] = $css;
    }

    /**
     * add path to file
     * @param string $js
     */
    public function addJs(string $js): void
    {
        $this->js[] = $js;
    }

    /**
     * @return string
     */
    public function compileCss()
    {
        $compiledCss = '';
        $aCss = array_unique($this->css);
        foreach ($aCss as $css) {
            $compiledCss .= str_replace('%href%', $css, $this->cssPattern) . "\n";
        }
        return $compiledCss;
    }

    /**
     * @return string
     */
    public function compileJs()
    {
        $compiledJs = '';
        $aJs = array_unique($this->js);
        foreach ($aJs as $js) {
            $compiledJs .= str_replace('%href%', $js, $this->jsPattern) . "\n";
        }
        return $compiledJs;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('addCss', [$this, 'addCss']),
            new TwigFunction('addJs', [$this, 'addJs']),
            new TwigFunction('compileCss', [$this, 'compileCss']),
            new TwigFunction('compileJs', [$this, 'compileJs'])
        ];
    }
}