<?php

namespace App\Utils;

class UuidGenerator
{
    /**
     * @return string
     * @throws \Exception
     */
    static public function generate(): string
    {
        return \Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}