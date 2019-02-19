<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 19.02.19
 * Time: 16:01
 */

namespace ExtrasBundle\Utils;


class ArraySorter
{
    public static function sortArrayByKeys(array $values, array $keys): array
    {
        $keys = array_flip($keys);
        return array_merge(array_intersect_key($keys, $values), array_intersect_key($values, $keys));
    }

}