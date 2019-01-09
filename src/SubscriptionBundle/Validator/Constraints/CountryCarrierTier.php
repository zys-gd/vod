<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 14/09/17
 * Time: 7:08 PM
 */

namespace SubscriptionBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class CountryCarrierTier
 * @Annotation
 * @package SubscriptionBundle\Validator\Constraints
 */
class CountryCarrierTier extends Constraint
{


    public function validatedBy()
    {
        return CountryCarrierTierValidator::class;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}