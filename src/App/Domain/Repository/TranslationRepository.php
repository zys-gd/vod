<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Carrier;
use App\Domain\Service\FaqProviderService;
use App\Exception\WrongTranslationRecordType;
use AppBundle\Entity\Language;
use Doctrine\ORM\NativeQuery;
use Doctrine\ORM\Query\ResultSetMapping;

class TranslationRepository extends \Doctrine\ORM\EntityRepository
{
    const FAQ_QUESTIONS_KEY_PATTERN = 'faq.q%';
    const FAQ_ANSWERS_KEY_PATTERN = 'faq.a%';

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

    /**
     * @param string $carrierUuid
     * @param array $languageUuids
     *
     * @return array
     */
    public function findTranslationByCarrierAndOrderedLanguages(string $carrierUuid, array $languageUuids)
    {
        $rsm = new ResultSetMapping();
        /** @var NativeQuery $query */
        $query = $this->getEntityManager()->createNativeQuery("
                SELECT * FROM translations
                        WHERE language_id in (?)
                        ORDER BY FIELD(language_id, ?)",$rsm);
//        $query->setParameter(0, $carrierUuid);
        $query->setParameter(0, "'5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");
        $query->setParameter(1, "'5179f17c-ebd4-11e8-95c4-02bb250f0f22', '5179ee29-ebd4-11e8-95c4-02bb250f0f22'");

//        $query = $this->createQueryBuilder('t');
//        $query->where('t.carrier = :carrierUuid')
//            ->orWhere('t.language IN (:languageUuids)')
//            ->setParameters([
//               'carrierUuid' => $carrierUuid,
//               'languageUuids' => $languageUuids
//            ])
//            ->add('orderBy', "FIELD(t.language, '" . implode("', '", $languageUuids) . "')");


        return $query->getResult();
    }
}
