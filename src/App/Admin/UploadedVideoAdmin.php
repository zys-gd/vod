<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use App\Domain\Entity\UploadedVideo;

class UploadedVideoAdmin extends AbstractAdmin
{

    public function getBatchActions()
    {
        return [];
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('status')
            ->add('remoteId')
            ->add('title');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);


        $listMapper
            ->add('id')
            ->add('remoteId')
            ->add('title')
            ->add('status', 'choice', ['choices' => UploadedVideo::STATUSES])
            ->add('createdAt')
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
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title');
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('remoteId')
            ->add('title')
            ->add('status', 'choice', ['choices' => UploadedVideo::STATUSES])
            ->add('thumbnail', null, [
                'template' => '@App/UploadedVideo/thumbnail.html.twig',
                'mapped'   => false,
                'label'    => 'Thumbnails'
            ])
            ->add('player', null, [
                'template' => '@App/UploadedVideo/player.html.twig',
                'mapped'   => false,
                'label'    => 'Video Preview'
            ])
            ->add('category', null, ['associated_property' => 'title'])
            ->add('createdAt');
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->clearExcept(['show', 'list', 'edit', 'delete']);

        $collection->add('upload', 'upload');


        parent::configureRoutes($collection); // TODO: Change the autogenerated stub
    }

    public function configureActionButtons($action, $object = null)
    {

        $list = parent::configureActionButtons($action, $object);

        $list['import']['template'] = '@App/UploadedVideo/upload_button.html.twig';

        return $list;
    }


}
