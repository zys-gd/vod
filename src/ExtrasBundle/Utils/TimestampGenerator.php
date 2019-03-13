<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 29.10.18
 * Time: 10:12
 */

namespace ExtrasBundle\Utils;

use DateTime;

class TimestampGenerator
{
    public static function generateMicrotime(): string
    {
        try {
            $now  = DateTime::createFromFormat('U.u', microtime(true));
            $date = $now->format("Y-m-d H:i:s.u");
        } catch (\Throwable $error) {
            $date = strftime("%Y-%m-%d %H:%M:%S");
        }

        return $date;

    }
}