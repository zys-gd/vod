<?php
/**
 * Created by PhpStorm.
 * User: Yurii Z
 * Date: 29-03-19
 * Time: 14:40
 */

namespace ExtrasBundle\Cache;

interface PureRedisInterface
{
    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasKey(string $key): bool;

    /**
     * @param string $key
     *
     * @return string
     */
    public function get(string $key): string;

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value): void;
}