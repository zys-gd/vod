<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.02.19
 * Time: 12:12
 */

namespace App\Twig;


use App\Domain\Service\Games\ImagePathProvider;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GamesExtension extends AbstractExtension
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
            new TwigFunction('getImageLink', function (string $image) {
                return $this->provider->getFullPath($image);
            })
        ];
    }


}