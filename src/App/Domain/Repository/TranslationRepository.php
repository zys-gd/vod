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
    const FAQ_QUESTIONS_KEY_PATTERN = 'faq.q%';
    const FAQ_ANSWERS_KEY_PATTERN   = 'faq.a%';

    /**
     * @param string $type
     *
     * @return array
     * @throws WrongTranslationRecordType
     */
    public function findFAQsByType(string $type): array
    {
        $query = $this->createQueryBuilder('t')
            ->where('t.key Like :key_val');

        switch ($type) {
            case FaqProviderService::TYPE_FAQ_QUESTION:
                $query->setParameter('key_val', self::FAQ_QUESTIONS_KEY_PATTERN);
                break;
            case FaqProviderService::TYPE_FAQ_ANSWER:
                $query->setParameter('key_val', self::FAQ_ANSWERS_KEY_PATTERN);
                break;
            default:
                throw new WrongTranslationRecordType();
        }

        return $query->getQuery()->execute();
    }

    public function findTextsForCarriers(): array
    {

        $query = $this
            ->createQueryBuilder('t')
            ->where('t.carrier is not NULL');

        return $query->getQuery()->execute();
    }
}
