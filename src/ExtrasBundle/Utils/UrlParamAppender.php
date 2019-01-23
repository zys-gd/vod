<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 16.07.18
 * Time: 12:03
 */

namespace ExtrasBundle\Utils;


class UrlParamAppender
{

    public function appendUrl(string $url, array $params): string
    {

        $parts = explode('?', $url);

        if (isset($parts[1])) {
            parse_str($parts[1], $parsedParams);
        } else {
            $parsedParams = [];
        }

        $parsedParams = array_merge($parsedParams, $params);

        $updatedUrl = $parts[0] . '?' . http_build_query($parsedParams);

        return $updatedUrl;

    }
}