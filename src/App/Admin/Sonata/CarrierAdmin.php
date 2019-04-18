<?php

namespace App\Admin\Sonata;

use App\Domain\Entity\Carrier;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use SubscriptionBundle\Service\SubscriptionLimiter\DTO\CarrierLimiterData;
use SubscriptionBundle\Service\SubscriptionLimiter\Limiter\LimiterDataExtractor;
use SubscriptionBundle\Service\SubscriptionLimiter\Limiter\LimiterDataStorage;
use SubscriptionBundle\Service\SubscriptionLimiter\Limiter\LimiterDataConverter;
use SubscriptionBundle\Service\SubscriptionLimiter\SubscriptionLimiter;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Class CarrierAdmin
 */
class CarrierAdmin extends AbstractAdmin
{
    /**
     * @var SubscriptionLimiter
     */
    private $subscriptionLimiter;
    /**
     * @var LimiterDataStorage
     */
    private $limiterDataStorage;
    /**
     * @var LimiterDataExtractor
     */
    private $limiterDataExtractor;

    /**
     * CarrierAdmin constructor
     *
     * @param string               $code
     * @param string               $class
     * @param string               $baseControllerName
     * @param SubscriptionLimiter  $subscriptionLimiter
     * @param LimiterDataStorage   $limiterDataStorage
     * @param LimiterDataExtractor $limiterDataExtractor
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        SubscriptionLimiter $subscriptionLimiter,
        LimiterDataStorage $limiterDataStorage,
        LimiterDataExtractor $limiterDataExtractor
    )
    {
        $this->subscriptionLimiter = $subscriptionLimiter;
        $this->limiterDataStorage  = $limiterDataStorage;
        $this->limiterDataExtractor = $limiterDataExtractor;
        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
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
            ->add('numberOfAllowedSubscription');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
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
            ->add('_action', null, [
                'actions' => [
                    'show'   => [],
                    'edit'   => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * Creation form for carrier
     *
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
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
            ->add('isUnlimitedSubscriptionAttemptsAllowed', null, [
                'attr' => ["class" => "unlimited-games"]
            ])
            ->add('numberOfAllowedSubscription', null, [
                'attr' => ["class" => "count-of-subs"]
            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        /** @var Carrier $subject */
        $subject = $this->getSubject();

        $carrierLimiterData = new CarrierLimiterData($subject);
        $counter = $this->limiterDataExtractor->getCarrierSlots($carrierLimiterData)[LimiterDataConverter::OPEN_SUBSCRIPTION_SLOTS];

        $subject->setCounter($subject->getNumberOfAllowedSubscriptionsByConstraint() - $counter);

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
            ->add('numberOfAllowedSubscriptionsByConstraint')
            ->add('counter')
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

    /**
     * @param Carrier $carrier
     */
    public function postUpdate($carrier)
    {
        if ($carrier->getNumberOfAllowedSubscriptionsByConstraint() > 0) {
            $carrierLimiterData = new CarrierLimiterData($carrier, $carrier->getNumberOfAllowedSubscriptionsByConstraint(), $carrier->getNumberOfAllowedSubscriptionsByConstraint());
            $this->limiterDataStorage->saveCarrierConstraint($carrierLimiterData);
        }

        if ($carrier->getNumberOfAllowedSubscriptionsByConstraint() === null) {
            $this->limiterDataStorage->removeCarrierConstraint($carrier->getBillingCarrierId());
        }
    }

    /**
     * @param Carrier $carrier
     */
    public function postPersist($carrier)
    {
        if ($carrier->getNumberOfAllowedSubscriptionsByConstraint() > 0) {
            $carrierLimiterData = new CarrierLimiterData($carrier, $carrier->getNumberOfAllowedSubscriptionsByConstraint(), $carrier->getNumberOfAllowedSubscriptionsByConstraint());
            $this->limiterDataStorage->saveCarrierConstraint($carrierLimiterData);
        }
    }
}
