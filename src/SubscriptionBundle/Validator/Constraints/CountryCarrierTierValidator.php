<?php
/**
 * Created by IntelliJ IDEA.
 * User: bharatm
 * Date: 14/09/17
 * Time: 7:10 PM
 */

namespace SubscriptionBundle\Validator\Constraints;


use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use SubscriptionBundle\Entity\SubscriptionPack;

/**
 * Class CountryCarrierTierValidator
 * @Annotation
 * @package SubscriptionBundle\Validator\Constraints
 */
class CountryCarrierTierValidator extends ConstraintValidator
{

    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }


    /**
     * @param SubscriptionPack $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $country = $value->getCountry();
        $carrier = $value->getCarrierId();
        $tier = $value->getTierId();

        if (!$this->requestStack->getCurrentRequest()->isXmlHttpRequest()) {
            if (!$country) {
                $this->context->buildViolation('Please select country.')
                    ->atPath('country')
                    ->addViolation();

            } elseif (!$carrier) {
                $this->context->buildViolation('Please select carrier.')
                    ->atPath('carrier')
                    ->addViolation();
            } elseif (!$tier) {
                $this->context->buildViolation('Please select tier. 
                If tier field is missing try selecting carrier which has tiers.')
                    ->addViolation();
            }

        }
    }
}