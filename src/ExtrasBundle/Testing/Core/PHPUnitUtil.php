<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 17.05.18
 * Time: 15:12
 */

namespace ExtrasBundle\Testing\Core;


use ReflectionClass;

class PHPUnitUtil
{
    public static function callMethod($obj, $name, array $args)
    {
        $class  = new \ReflectionClass($obj);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method->invokeArgs($obj, $args);
    }

    public static function setProperty($object, $propertyName, $value)
    {
        $class    = new \ReflectionClass($object);
        $property = $class->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    public static function createWithoutConstructor($classname)
    {
        $reflector = new ReflectionClass($classname);

        $instance = $reflector->newInstanceWithoutConstructor();

        return $instance;
    }

    public static function parseQueryParamsFromUrl(string $url): array
    {
        $query  = parse_url($url, PHP_URL_QUERY);
        $params = [];
        parse_str($query, $params);
        return $params;
    }
}