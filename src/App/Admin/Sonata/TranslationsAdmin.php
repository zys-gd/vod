<?php

namespace App\Admin\Sonata;

use App\Domain\Entity\Carrier;
use App\Domain\Entity\Language;
use App\Domain\Entity\Translation;
use ExtrasBundle\Utils\UuidGenerator;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use SubscriptionBundle\Entity\SubscriptionPack;
use SubscriptionBundle\Repository\SubscriptionPackRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Class TranslationsAdmin
 */
class TranslationsAdmin extends AbstractAdmin
{
    /**
     * @var SubscriptionPackRepository
     */
    protected $subscriptionPackRepository;

    /**
     * @var array
     */
    protected $subscriptionPackOptions = ['...' => 0];

    /**
     * TranslationsAdmin constructor
     *
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param SubscriptionPackRepository $subscriptionPackRepository
     */
    public function __construct(
        string $code,
        string $class,
        string $baseControllerName,
        SubscriptionPackRepository $subscriptionPackRepository
    ) {
        $this->subscriptionPackRepository = $subscriptionPackRepository;
        $this->setSubscriptionPackOptions();

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @return Translation
     *
     * @throws \Exception
     */
    public function getNewInstance(): Translation
    {
        return new Translation(UuidGenerator::generate());
    }

    protected function setSubscriptionPackOptions()
    {
        if(count($this->subscriptionPackOptions) == 1) {
            $subscriptionPacks = $this->subscriptionPackRepository->findAll();

            /** @var SubscriptionPack $subscriptionPack */
            foreach ($subscriptionPacks as $subscriptionPack) {
                $name = "[{$subscriptionPack->getUuid()}] {$subscriptionPack->getName()}";
                $this->subscriptionPackOptions[$name] = $subscriptionPack->getUuid();
            }
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     *
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('carrier')
            ->add('key')
            ->add('language')
//            ->add('subscription_pack_id',
//                'doctrine_orm_string',
//                [],
//                'choice',
//                [
//                    'choices' => $this->subscriptionPackOptions
//                ]
//            )
        ;
    }

    /**
     * @param ListMapper $listMapper
     *
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('carrier')
            ->add('language')
            ->add('key')
            ->add('translation')
            ->add('_action', null, array(
                'actions' => array(
                    'show'   => array(),
                    'edit'   => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param FormMapper $formMapper
     *
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('carrier', EntityType::class, [
                'class' => Carrier::class,
                'required' => false,
                'placeholder' => 'Select carrier'
            ])
            ->add('key', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Length([
                        'max' => 255
                    ])
                ]
            ])
            ->add('translation', TextareaType::class, [
                'required' => true
            ])
            ->add('language', EntityType::class, [
                'class' => Language::class,
                'required' => true
            ])
//            ->add('subscription_pack_id',
//                'choice',
//                [
//                    'choices' => $this->subscriptionPackOptions,
//                    'required' => false
//                ]
//            )
        ;
    }

    /**
     * @param ShowMapper $showMapper
     *
     * @return void
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('carrier', TextType::class)
            ->add('key')
            ->add('translation')
            ->add('language');
    }
}