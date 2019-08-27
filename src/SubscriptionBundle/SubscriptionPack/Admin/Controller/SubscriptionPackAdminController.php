<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 22.05.18
 * Time: 14:26
 */

namespace SubscriptionBundle\SubscriptionPack\Admin\Controller;


use Sonata\AdminBundle\Controller\CRUDController;
use SubscriptionBundle\Entity\SubscriptionPack;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SubscriptionPackAdminController extends CRUDController
{

    public function editAction($id = null)
    {
        $form = $this->admin->getForm();

        $result = parent::editAction($id);
        if (
            ($result instanceof RedirectResponse)
            && $result->getTargetUrl() !== $this->admin->generateUrl('edit', ['id' => $id])
        ) {
            return $result;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('subscription_pack_save_success', true);
        }

        return $result;
    }

    public function textsAction(SubscriptionPack $object)
    {
        return new RedirectResponse($this->generateUrl('admin_app_placeholdertooperator_list', [
            'filter[subscription_pack_id][value]' => $object->getUuid(),
            'filter[subscription_pack_id][type]' => 3,
        ]));
    }

    public function successAction(SubscriptionPack $object)
    {

        return $this->render('SubscriptionBundle:Admin/SubscriptionPack:success.html.twig', array(
            'action'   => 'show',
            'object'   => $object,
            'elements' => $this->admin->getShow(),
            'link'     => $this->admin->generateUrl('texts', ['id' => $object->getUuid()])
        ), null);
    }
}