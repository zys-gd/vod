<?php

namespace App\Admin\Sonata;

use App\Domain\Entity\Carrier;
use Doctrine\ORM\EntityManagerInterface;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use SubscriptionBundle\Service\CapConstraint\ConstraintCounterRedis;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Class CarrierAdmin
 */
class CarrierAdmin extends AbstractAdmin
{
    /**
     * @var ConstraintCounterRedis
     */
    private $constraintCounterRedis;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * CarrierAdmin constructor
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param ConstraintCounterRedis $constraintCounterRedis
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        ConstraintCounterRedis $constraintCounterRedis,
        EntityManagerInterface $entityManager
    ) {
        $this->constraintCounterRedis = $constraintCounterRedis;
        $this->entityManager = $entityManager;

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @param Carrier $object
     */
    public function preUpdate($object)
    {
        $originalData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($object);

        if ($originalData['numberOfAllowedSubscriptionsByConstraint']
            !== $object->getNumberOfAllowedSubscriptionsByConstraint()
        ) {
            $object->setIsCapAlertDispatch(false);
        }
    }

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
            ->add('isConfirmationClick')
            ->add('pinIdentSupport')
            ->add('trialInitializer')
            ->add('trialPeriod')
            ->add('subscriptionPeriod')
            ->add('numberOfAllowedSubscriptionsByConstraint')
            ->add('redirectUrl')
            ->add('resubAllowed')
            ->add('isCampaignsOnPause')
            ->add('isUnlimitedSubscriptionAttemptsAllowed')
            ->add('subscribeAttempts')
            ->add('isLpOff');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields (ListMapper $listMapper)
    {
        $listMapper
            ->add('billingCarrierId')
            ->add('operatorId')
            ->add('name')
            ->add('countryCode')
            ->add('defaultLanguage', TextType::class)
            ->add('isp')
            ->add('published')
            ->add('isConfirmationClick')
            ->add('pinIdentSupport')
            ->add('trialInitializer')
            ->add('trialPeriod')
            ->add('subscriptionPeriod')
            ->add('resubAllowed')
            ->add('isCampaignsOnPause')
            ->add('isLpOff')
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
            ->add('isConfirmationClick')
            ->add('pinIdentSupport')
            ->add('isLpOff', null, [
                'label' => 'Turn off LP showing',
                'help' => 'If consent page exist, then show it. Otherwise will try to subscribe'
            ])
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
            ->add('subscribeAttempts', null, [
                'attr' => ["class" => "count-of-subs"]
            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields (ShowMapper $showMapper)
    {
        /** @var Carrier $subject */
        $subject = $this->getSubject();

        $counter = $this->constraintCounterRedis->getCounter($subject->getBillingCarrierId());

        $subject->setCounter((int) $counter);

        $showMapper
            ->add('uuid')
            ->add('billingCarrierId')
            ->add('operatorId')
            ->add('name')
            ->add('countryCode')
            ->add('default_language')
            ->add('isp')
            ->add('published')
            ->add('isConfirmationClick')
            ->add('pinIdentSupport')
            ->add('isLpOff', null, [
                'label' => 'Turn off LP showing',
                'help' => 'If consent page exist, then show it. Otherwise will try to subscribe'
            ])
            ->add('trialInitializer')
            ->add('trialPeriod')
            ->add('subscriptionPeriod')
            ->add('resubAllowed')
            ->add('isCampaignsOnPause')
            ->add('isUnlimitedSubscriptionAttemptsAllowed')
            ->add('subscribeAttempts')
            ->add('numberOfAllowedSubscriptionsByConstraint')
            ->add('counter')
            ->add('isCapAlertDispatch')
            ->add('flushDate')
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
