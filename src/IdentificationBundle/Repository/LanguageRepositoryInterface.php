<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 05.03.19
 * Time: 17:05
 */

namespace IdentificationBundle\Repository;


use IdentificationBundle\Entity\LanguageInterface;

interface LanguageRepositoryInterface
{
    public function findByCode(string $code): ?LanguageInterface;
}