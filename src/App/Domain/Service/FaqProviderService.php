<?php
/**
 * Created by PhpStorm.
 * User: Iliya Kobus
 * Date: 1/18/2019
 * Time: 11:21 AM
 */

namespace App\Domain\Service;

use App\Domain\Entity\Translation;
use App\Domain\Repository\TranslationRepository;

class FaqProviderService
{
    const TYPE_FAQ_QUESTION   = 'question';
    const TYPE_FAQ_ANSWER     = 'answer';
    const FAQ_SORTING_PATTERN = '/^faq.[a|q].(\d+)$/';
    /**
     * @var TranslationRepository
     */
    private $repository;

    /**
     * FaqProviderService constructor.
     */
    public function __construct(TranslationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return array|null
     * @throws \App\Exception\WrongTranslationRecordType
     */
    public function getSortedQuestions(): ?array
    {
        $questions = $this->repository->findFAQsByType(self::TYPE_FAQ_QUESTION);

        usort($questions, function (Translation $a, Translation $b) {
            preg_match(self::FAQ_SORTING_PATTERN, $a->getKey(), $a_index);
            preg_match(self::FAQ_SORTING_PATTERN, $b->getKey(), $b_index);

            return $a_index[1] <=> $b_index[1];
        });

        return $questions;
    }

    /**
     * @return array|null
     * @throws \App\Exception\WrongTranslationRecordType
     */
    public function getSortedAnswers(): ?array
    {
        $answers = $this->repository->findFAQsByType(self::TYPE_FAQ_ANSWER);

        usort($answers, function (Translation $a, Translation $b) {
            preg_match(self::FAQ_SORTING_PATTERN, $a->getKey(), $a_index);
            preg_match(self::FAQ_SORTING_PATTERN, $b->getKey(), $b_index);

            return $a_index[1] <=> $b_index[1];
        });

        return $answers;
    }
}
