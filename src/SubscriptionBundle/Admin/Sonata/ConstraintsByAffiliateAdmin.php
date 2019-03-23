<?php

namespace SubscriptionBundle\Admin\Sonata;

use App\Domain\Entity\Affiliate;
use App\Domain\Entity\Carrier;
use App\Utils\UuidGenerator;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\Form\Type\BooleanType;
use SubscriptionBundle\Entity\Affiliate\ConstraintByAffiliate;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

/**
 * Class ConstraintsByAffiliateAdmin
 */
class ConstraintsByAffiliateAdmin extends AbstractAdmin
{
    /**
     * @return ConstraintByAffiliate
     *
     * @throws \Exception
     */
    public function getNewInstance(): ConstraintByAffiliate
    {
        return new ConstraintByAffiliate(UuidGenerator::generate());
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('affiliate')
            ->add('carrier')
            ->add('redirectUrl')
            ->add('capType');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('affiliate')
            ->add('carrier')
            ->add('numberOfActions')
            ->add('redirectUrl')
            ->add('counter')
            ->add('capType')
            ->add('flushDate')
            ->add('isCapAlertDispatch', BooleanType::class, [
                'label' => 'Is email sent today'
            ])
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('affiliate', EntityType::class, [
                'class' => Affiliate::class,
                'placeholder' => 'Select affiliate',
            ])
            ->add('carrier', EntityType::class, [
                'class' => Carrier::class,
                'placeholder' => 'Select carrier',
            ])
            ->add('numberOfActions', IntegerType::class, [
                'attr' => [
                    'min' => 0
                ],
                'label' => 'Number of allowed actions by constraint'
            ])
            ->add('redirectUrl', UrlType::class, [
                'label' => 'Redirect url'
            ])
            ->add('capType', ChoiceType::class, [
                'choices' => [
                    'Subscribe' => ConstraintByAffiliate::CAP_TYPE_SUBSCRIBE,
                    'Visit' => ConstraintByAffiliate::CAP_TYPE_VISIT
                ],
                'label' => 'CAP type'
            ])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('affiliate')
            ->add('carrier')
            ->add('numberOfActions')
            ->add('redirectUrl')
            ->add('counter')
            ->add('capType', TextType::class, [
                'label' => 'CAP Type'
            ])
            ->add('flushDate')
            ->add('isCapAlertDispatch', BooleanType::class, [
                'label' => 'Is email sent today'
            ]);
    }
}