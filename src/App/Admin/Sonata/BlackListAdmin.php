<?php

namespace App\Admin\Sonata;

use App\Domain\Entity\BlackList;
use App\Utils\UuidGenerator;
use IdentificationBundle\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use SubscriptionBundle\Entity\Subscription;
use SubscriptionBundle\Service\Action\Unsubscribe\Handler\UnsubscriptionHandlerProvider;
use SubscriptionBundle\Service\Action\Unsubscribe\Unsubscriber;
use Symfony\Component\Form\Extension\Core\Type\TextType;

/**
 * Class BlackListAdmin
 */
class BlackListAdmin extends AbstractAdmin
{
    /**
     * @var UnsubscriptionHandlerProvider
     */
    private $unsubscriptionHandlerProvider;

    /**
     * @var Unsubscriber
     */
    private $unsubscriber;

    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        UnsubscriptionHandlerProvider $unsubscriptionHandlerProvider,
        Unsubscriber $unsubscriber
    ) {
        $this->unsubscriptionHandlerProvider = $unsubscriptionHandlerProvider;
        $this->unsubscriber = $unsubscriber;

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @param BlackList $blackList
     */
    public function postPersist($blackList) {
        $doctrine = $this->getConfigurationPool()->getContainer()->get('doctrine');

        /** @var User $user */
        $user = $doctrine
            ->getRepository('IdentificationBundle\Entity\User')
            ->findOneBy(['identifier' => $blackList->getAlias()]);

        if ($user) {
            /** @var Subscription $subscription */
            $subscription = $doctrine
                ->getRepository('SubscriptionBundle\Entity\Subscription')
                ->findOneBy(['user' => $user]);

            if ($subscription && $subscription->getCurrentStage() != Subscription::ACTION_UNSUBSCRIBE) {
                $unsubscribtionHandler = $this->unsubscriptionHandlerProvider->getUnsubscriptionHandler($user->getCarrier());

                $response = $this->unsubscriber->unsubscribe($subscription, $subscription->getSubscriptionPack());
                $unsubscribtionHandler->applyPostUnsubscribeChanges($subscription);

                if ($unsubscribtionHandler->isPiwikNeedToBeTracked($response)) {
                    $this->unsubscriber->trackEventsForUnsubscribe($subscription, $response);
                }
            }
        }
    }

    /**
     * @param BlackList $blacklist
     */
    public function postUpdate($blacklist) {
        $this->postPersist($blacklist);
    }

    /**
     * @return BlackList
     *
     * @throws \Exception
     */
    public function getNewInstance()
    {
        return new BlackList(UuidGenerator::generate());
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('uuid')
            ->add('billingCarrierId')
            ->add('alias');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('uuid')
            ->add('billingCarrierId')
            ->add('alias')
            ->add('_action', null, array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('billingCarrierId')
            ->add('alias', TextType::class, [
                'required' => true
            ]);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('uuid')
            ->add('billingCarrierId')
            ->add('alias');
    }
}
