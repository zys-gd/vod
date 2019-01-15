<?php
namespace PriceBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

/**
 * Class TierValuesAdmin
 * @package PriceBundle\Admin
 */
class TierValueAdmin extends AbstractAdmin
{

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add(
            'values',
            'list/{countryCode}',
            array(
                "_format" => "json"
            ),
            array(
                "countryCode" => "[a-zA-Z]{2}"
            )
        );
//        $collection->add(
//            'save',
//            'save',
//            array(
//                "_format" => "json"
//            ),
//            array(),
//            array(),
//            "",
//            array(),
//            array("POST")
//        );
    }

    /**
     * Generate the entries for entity's datagrid.
     *
     * @param DatagridMapper $datagridMapper
     * @return void
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('tier')
            ->add('carrier')
            ->add('value')
            ->add('currency')
            ->add('strategy')
            ->add('description');
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
            ->add('id')
            ->add('tier')
            ->add('carrier')
            ->add('value')
            ->add('currency')
            ->add('strategy')
            ->add('description')
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ]
            ]);
    }

    /**
     * Generate editing fields for entity
     *
     * @param FormMapper $formMapper
     * @return void
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('tier')
            ->add('carrier')
            ->add('value')
            ->add('currency')
            ->add('strategy')
            ->add('description')
            ->end();

    }
}