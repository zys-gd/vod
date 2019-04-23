<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Carrier;
use App\Domain\Entity\Translation;
use App\Domain\Service\FaqProviderService;
use App\Exception\WrongTranslationRecordType;
use AppBundle\Entity\Language;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use IdentificationBundle\Entity\CarrierInterface;

class TranslationRepository extends \Doctrine\ORM\EntityRepository
{
}
