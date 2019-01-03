<?php
/**
 * Created by PhpStorm.
 * User: Maxim Nevstruev
 * Date: 14.02.2017
 * Time: 11:52
 */

namespace App\Admin;


use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Talentica\SubscriptionBundle\Repository\SubscriptionPack\SubscriptionPackRepository;

class PlaceholderToOperatorAdmin extends AbstractAdmin
{
    /** @var null|SubscriptionPackRepository  */
    protected $subscriptionPackRepository = null;
    /** @var array  */
    protected $subPackOptions = ['...' => 0];

    public function __construct(string $code, string $class, string $baseControllerName, SubscriptionPackRepository $subscriptionPackRepository)
    {
        parent::__construct($code, $class, $baseControllerName);
        $this->subscriptionPackRepository = $subscriptionPackRepository;
    }

    /**
     * @return array
     */
    protected function getSubPackOptions()
    {
        if(count($this->subPackOptions) == 1) {
            $aSubPacks = $this->subscriptionPackRepository->findAll();
            foreach ($aSubPacks as $oSubPack) {
                $name = "[{$oSubPack->getId()}] {$oSubPack->getName()}";
                $this->subPackOptions[$name] = $oSubPack->getId();
            }
        }
        return $this->subPackOptions;
    }

    /**
     * Generate the entries for entity's datagrid.
     *
     * @param DatagridMapper $datagridMapper
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $options = $this->getSubPackOptions();
        $datagridMapper
            ->add('carrier_id')
            ->add('placeholder_id')
            ->add('specificValue')
            ->add('language')
            ->add('subscription_pack_id',
                'doctrine_orm_string',
                [],
                'choice',
                [
                    'choices' => $options
                ]
            );
    }

    /**
     * Generate listing fields for entity
     *
     * @param ListMapper $listMapper
     * @return void
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('carrier_id')
            ->add('placeholder_id')
            ->add('specificValue', 'string', [
                'editable' => true,
            ])
            ->add('language', 'string')
            ->add('_action', null, array(
                'actions' => array(
                    'show'   => array(),
                    'edit'   => array(),
                    'delete' => array(),
                )
            ));;
    }

    /**
     * Generate editing fields for entity
     *
     * @param FormMapper $formMapper
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $options = $this->getSubPackOptions();
        $formMapper
            ->add('carrier_id')
            ->add('placeholder_id')
            ->add('specificValue', 'text', ['required' => false])
            ->add('language')
            ->add('subscription_pack_id',
                'choice',
                [
                    'choices' => $options,
                    'required' => false
                ]
            );
    }

    /**
     * Generate show-page fields for entity
     *
     * @param ShowMapper $showMapper
     * @return void
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('carrier_id')
            ->add('placeholder_id')
            ->add('specificValue')
            ->add('language');
    }
}