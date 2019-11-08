<?php

namespace App\Admin\Sonata;

use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Campaign;
use App\Domain\Entity\Carrier;
use Doctrine\ORM\EntityManagerInterface;
use ExtrasBundle\Cache\ICacheService;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use SubscriptionBundle\CAPTool\DTO\CarrierLimiterData;
use SubscriptionBundle\CAPTool\Subscription\Limiter\LimiterDataConverter;
use SubscriptionBundle\CAPTool\Subscription\Limiter\LimiterDataExtractor;
use SubscriptionBundle\CAPTool\Subscription\Limiter\LimiterStorage;
use SubscriptionBundle\CAPTool\Subscription\Limiter\StorageKeyGenerator;
use SubscriptionBundle\CAPTool\Subscription\SubscriptionLimiter;
use SubscriptionBundle\Entity\Affiliate\AffiliateInterface;
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
     * @var LimiterStorage
     */
    private $limiterDataStorage;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var StorageKeyGenerator
     */
    private $storageKeyGenerator;
    /**
     * @var ICacheService
     */
    private $cacheService;

    /**
     * CarrierAdmin constructor
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param SubscriptionLimiter $subscriptionLimiter
     * @param LimiterStorage $limiterDataStorage
     * @param StorageKeyGenerator $storageKeyGenerator
     * @param EntityManagerInterface $entityManager
     * @param ICacheService $cacheService
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        SubscriptionLimiter $subscriptionLimiter,
        LimiterStorage $limiterDataStorage,
        StorageKeyGenerator $storageKeyGenerator,
        EntityManagerInterface $entityManager,
        ICacheService $cacheService
    ) {
        $this->subscriptionLimiter = $subscriptionLimiter;
        $this->limiterDataStorage  = $limiterDataStorage;
        parent::__construct($code, $class, $baseControllerName);
        $this->storageKeyGenerator = $storageKeyGenerator;
        $this->code                = $code;
        $this->entityManager       = $entityManager;
        $this->cacheService = $cacheService;
    }

    /**
     * @param Carrier $object
     */
    public function preUpdate($object)
    {
        $this->cacheService->deleteCache();

        $originalData = $this->entityManager->getUnitOfWork()->getOriginalEntityData($object);

        if ($originalData['numberOfAllowedSubscriptionsByConstraint']
            !== $object->getNumberOfAllowedSubscriptionsByConstraint()
        ) {
            $object->setIsCapAlertDispatch(false);
        }

        if ($object->isOneClickFlow() != $originalData['isOneClickFlow']) {
            $isOneClickFlow = $object->isOneClickFlow();

            $object->getCampaigns()->map(function (Campaign $campaign) use ($isOneClickFlow) {
                $campaign->setIsOneClickFlow($isOneClickFlow);
                $campaign->getAffiliate()->setIsOneClickFlow($isOneClickFlow);
            });
        }

        if ($object->getIsCampaignsOnPause() != $originalData['isCampaignsOnPause']) {
            $isCampaignsOnPause = $object->getIsCampaignsOnPause();
            $object->getCampaigns()->map(function (Campaign $campaign) use ($isCampaignsOnPause) {
                $campaign->setIsPause($isCampaignsOnPause);
            });
        }

        if ($object->isClickableSubImage() != $originalData['isClickableSubImage']) {
            $isClickableSubImage = $object->isClickableSubImage();
            $object->getCampaigns()->map(function (Campaign $campaign) use ($isClickableSubImage) {
                $campaign->setIsClickableSubImage($isClickableSubImage);
            });
        }

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
            ->add('isOneClickFlow')
            ->add('trialInitializer')
            ->add('trialPeriod')
            ->add('subscriptionPeriod')
            ->add('numberOfAllowedSubscriptionsByConstraint')
            ->add('redirectUrl')
            ->add('isCampaignsOnPause')
            ->add('subscribeAttempts')
            ->add('isClickableSubImage');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('billingCarrierId')
            ->add('operatorId')
            ->add('name')
            ->add('countryCode')
            ->add('defaultLanguage', TextType::class)
            ->add('isp')
            ->add('published')
            ->add('isOneClickFlow')
            ->add('trialInitializer')
            ->add('trialPeriod')
            ->add('subscriptionPeriod')
            ->add('isCampaignsOnPause')
            ->add('isClickableSubImage', null, [
                'label' => 'Clickable image'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show'   => [],
                    'edit'   => [],
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
            ->add('isOneClickFlow')
            ->add('trialInitializer')
            ->add('trialPeriod')
            ->add('subscriptionPeriod')
            ->add('numberOfAllowedSubscriptionsByConstraint', IntegerType::class, ['attr' => ['min' => 0], 'required' => false,])
            ->add('redirectUrl', UrlType::class, ['required' => false])
            ->add('isCampaignsOnPause')
            ->add('subscribeAttempts', null, [
                'attr' => ["class" => "count-of-subs"]
            ])
            ->add('isClickableSubImage', null, [
                'label' => 'Clickable image'
            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        /** @var Carrier $subject */
        $subject = $this->getSubject();

        $key = $this->storageKeyGenerator->generateKey($subject);

        $pending = $this->limiterDataStorage->getPendingSubscriptionAmount($key);

        $finished = $this->limiterDataStorage->getFinishedSubscriptionAmount($key);

        $available = $pending + $finished;

        $subject->setCounter($available);

        $showMapper
            ->add('uuid')
            ->add('billingCarrierId')
            ->add('operatorId')
            ->add('name')
            ->add('countryCode')
            ->add('default_language')
            ->add('isp')
            ->add('published')
            ->add('isOneClickFlow')
            ->add('trialInitializer')
            ->add('trialPeriod')
            ->add('subscriptionPeriod')
            ->add('isCampaignsOnPause')
            ->add('subscribeAttempts')
            ->add('numberOfAllowedSubscriptionsByConstraint')
            ->add('counter')
            ->add('isCapAlertDispatch')
            ->add('isClickableSubImage', null, [
                'label' => 'Clickable image'
            ])
            ->add('flushDate')
            ->add('redirectUrl');
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['list', 'edit', 'show']);

        parent::configureRoutes($collection);
    }
}
