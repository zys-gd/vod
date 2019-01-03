<?php

namespace App\Admin;

use function Sodium\add;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CarrierAdmin extends AbstractAdmin
{
//todo default_lang
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters (DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('id_carrier')
            ->add('operator_id')
            ->add('name')
            ->add('countryCode')
            ->add('default_language')
            ->add('isp')
            ->add('published')
            ->add('lpOtp')
            ->add('pinIdentSupport')
            ->add('trial_initializer')
            ->add('trial_period')
            ->add('trial_credits')
            ->add('subscription_period')
            ->add('subscription_credits')
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
            ->add('id')
            ->add('id_carrier')
            ->add('operator_id')
            ->add('name')
            ->add('countryCode')
            ->add('default_language', 'string')
            ->add('isp')
            ->add('published')
            ->add('lpOtp')
            ->add('pinIdentSupport')
            ->add('trial_initializer')
            ->add('trial_period')
            ->add('trial_credits')
            ->add('subscription_period')
            ->add('subscription_credits')
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
            ->add('id', TextType::class, [
                'required' => false
            ])
            ->add('id_carrier')
            ->add('operator_id')
            ->add('name')
            ->add('countryCode')
            ->add('default_language')
            ->add('isp')
            ->add('published')
            ->add('lpOtp')
            ->add('pinIdentSupport')
            ->add('trial_initializer')
            ->add('trial_period')
            ->add('trial_credits')
            ->add('subscription_period')
            ->add('subscription_credits')
            ->add('numberOfAllowedSubscriptionsByConstraint', 'integer', ['attr' => ['min' => 0], 'required' => false,])
            ->add('redirectUrl','url', ['required' => false])
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
            ->add('id')
            ->add('id_carrier')
            ->add('operator_id')
            ->add('name')
            ->add('countryCode')
            ->add('default_language')
            ->add('isp')
            ->add('published')
            ->add('lpOtp')
            ->add('pinIdentSupport')
            ->add('trial_initializer')
            ->add('trial_period')
            ->add('trial_credits')
            ->add('subscription_period')
            ->add('subscription_credits')
            ->add('resubAllowed')
            ->add('isCampaignsOnPause')
            ->add('isUnlimitedSubscriptionAttemptsAllowed')
            ->add('numberOfAllowedSubscription')
            ->add('isCaptcha')
            ->add('numberOfAllowedSubscriptionsByConstraint')
            ->add('redirectUrl');
    }
}
