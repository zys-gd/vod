<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 14/08/17
 * Time: 2:45 PM
 */

namespace SubscriptionBundle\Validator\Constraints;



use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Repository\SubscriptionPack\SubscriptionPackRepository;

/**
 * Class UniqueActiveCarrierValidator
 * @Annotation
 * @package SubscriptionBundle\Validator\Constraints
 */
class ContainsUniqueActiveCarrierValidator extends ConstraintValidator
{
    /** @var SubscriptionPackRepository  */
    private $subscriptionPackRepository;

    public function __construct(SubscriptionPackRepository $subscriptionPackRepository)
    {
        $this->subscriptionPackRepository = $subscriptionPackRepository;
    }

    /**
     * Checks if the passed value is valid.
     *
     * @param SubscriptionPack $value The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     */
    public function validate($value, Constraint $constraint)
    {
        if ($value->isActive()) {

            $existingActivePackForSelectedCarrier =
                $this->subscriptionPackRepository->getOtherActiveSubscriptionPack($value);
            if ($existingActivePackForSelectedCarrier) {

                $this->context->buildViolation($constraint->message)
                    ->setParameter('{{carrier}}', $value->getCarrier())
                    ->addViolation();
            }
        }
    }


}