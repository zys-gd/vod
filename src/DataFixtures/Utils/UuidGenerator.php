<?php

namespace DataFixtures\Utils;

class UuidGenerator
{
    /**
     * @return string
     * @throws \Exception
     */
    static public function generate()
    {
        return \Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}