<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 14/08/17
 * Time: 2:41 PM
 */

namespace SubscriptionBundle\Validator\Constraints;


use Symfony\Component\Validator\Constraint;

/**
 * Class ContainsUniqueActiveCarrier
 * @Annotation
 * @package SubscriptionBundle\Validator\Constraints
 */
class ContainsUniqueActiveCarrier extends Constraint
{
    public $message = "There is an existing active subscription pack for {{carrier}} carrier. ".
     "Webstore can have only one active subscription pack per carrier";

    public function validatedBy()
    {
        return ContainsUniqueActiveCarrierValidator::class;
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}