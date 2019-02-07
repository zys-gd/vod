<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 07.02.19
 * Time: 12:12
 */

namespace App\Twig;


class GamesExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $imagesHost;


    /**
     * GamesExtension constructor.
     */
    public function __construct(string $imagesHost)
    {
        $this->imagesHost = $imagesHost;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('getImageLink', function (string $image) {
                return sprintf('%s/uploads/cache/similar_game/%s', $this->imagesHost, $image);
            })
        ];
    }


}