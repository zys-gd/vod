<?php

namespace App\Admin\Sonata;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Class CarrierAdmin
 */
class CarrierAdmin extends AbstractAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters (DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('uuid')
            ->add('billingCarrierId')
            ->add('operatorId')
            ->add('name')
            ->add('countryCode')
            ->add('defaultLanguage')
            ->add('isp')
            ->add('published')
            ->add('lpOtp')
            ->add('pinIdentSupport')
            ->add('trialInitializer')
            ->add('trialPeriod')
            ->add('subscriptionPeriod')
            ->add('numberOfAllowedSubscriptionsByConstraint')
            ->add('redirectUrl')
            ->add('resubAllowed')
            ->add('isCampaignsOnPause')
            ->add('isUnlimitedSubscriptionAttemptsAllowed')
            ->add('numberOfAllowedSubscription')
            ->add('isCaptcha');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields (ListMapper $listMapper)
    {
        $listMapper
            ->add('uuid')
            ->add('billingCarrierId')
            ->add('operatorId')
            ->add('name')
            ->add('countryCode')
            ->add('defaultLanguage', TextType::class)
            ->add('isp')
            ->add('published')
            ->add('lpOtp')
            ->add('pinIdentSupport')
            ->add('trialInitializer')
            ->add('trialPeriod')
            ->add('subscriptionPeriod')
            ->add('resubAllowed')
            ->add('isCampaignsOnPause')
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * Creation form for carrier
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields (FormMapper $formMapper)
    {
        $formMapper
            ->add('uuid', TextType::class, [
                'required' => false
            ])
            ->add('billingCarrierId')
            ->add('operatorId')
            ->add('name')
            ->add('countryCode')
            ->add('defaultLanguage')
            ->add('isp')
            ->add('published')
            ->add('lpOtp')
            ->add('pinIdentSupport')
            ->add('trialInitializer')
            ->add('trialPeriod')
            ->add('subscriptionPeriod')
            ->add('numberOfAllowedSubscriptionsByConstraint', IntegerType::class, ['attr' => ['min' => 0], 'required' => false,])
            ->add('redirectUrl', UrlType::class, ['required' => false])
            ->add('resubAllowed')
            ->add('isCampaignsOnPause')
            ->add('isUnlimitedSubscriptionAttemptsAllowed', null,[
                'attr' => ["class" => "unlimited-games"]
            ])
            ->add('numberOfAllowedSubscription', null, [
                'attr' => ["class" => "count-of-subs"]
            ])
            ->add('isCaptcha');
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields (ShowMapper $showMapper)
    {
        $showMapper
            ->add('uuid')
            ->add('billingCarrierId')
            ->add('operatorId')
            ->add('name')
            ->add('countryCode')
            ->add('default_language')
            ->add('isp')
            ->add('published')
            ->add('lpOtp')
            ->add('pinIdentSupport')
            ->add('trialInitializer')
            ->add('trialPeriod')
            ->add('subscriptionPeriod')
            ->add('resubAllowed')
            ->add('isCampaignsOnPause')
            ->add('isUnlimitedSubscriptionAttemptsAllowed')
            ->add('numberOfAllowedSubscription')
            ->add('isCaptcha')
            ->add('numberOfAllowedSubscriptionsByConstraint')
            ->add('redirectUrl');
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'edit', 'delete', 'show']);

        parent::configureRoutes($collection);
    }
}
