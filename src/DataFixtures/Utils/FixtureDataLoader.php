<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 13.07.18
 * Time: 13:47
 */

namespace DataFixtures\Utils;


class FixtureDataLoader
{
    public static function loadDataFromJSONFile(string $fileName)
    {
        $content = file_get_contents(__DIR__ . sprintf('/../Data/%s', $fileName));
        return json_decode($content, true);
    }
}