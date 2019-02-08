<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.02.19
 * Time: 12:12
 */

namespace App\Twig;


use App\Domain\Service\Games\ImagePathProvider;

class GamesExtension extends \Twig_Extension
{
    /**
     * @var ImagePathProvider
     */
    private $provider;


    /**
     * GamesExtension constructor.
     */
    public function __construct(ImagePathProvider $provider)
    {
        $this->provider = $provider;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getImageLink', function (string $image) {
                return $this->provider->getFullPath($image);
            })
        ];
    }


}