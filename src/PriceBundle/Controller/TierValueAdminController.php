<?php
/**
 * Created by PhpStorm.
 * User: tiberiu.popovici
 * Date: 19.09.2016
 * Time: 09:22
 */

namespace PriceBundle\Controller;


use Herrera\Box\Compactor\Json;
use PriceBundle\Entity\TierValue;
use PriceBundle\Form\Type\TierValueType;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class TierValueAdminController
 * @package PriceBundle\Controller
 */
class TierValueAdminController extends CRUDController
{
//
//    /**
//     * @return Response
//     */
//    public function listAction()
//    {
//        $form = $this->admin->getForm();
//        $countries = $this->getDoctrine()->getRepository("AppBundle:Country")->findAll();
//        $tiers = $this->getDoctrine()->getRepository("PriceBundle:Tier")->findAll();
//
//        $js = $this->get('zitec.js_data.data_handler');
//        $js->add('[tiers]', $tiers);
//
//        return $this->render("PriceBundle:TierValue:create.html.twig", array(
//            "action" => "create",
//            "object"=> new TierValue(),
//            "admin"=>$this->admin,
//            "form"=> $form->createView(),
//            "countries"=>$countries,
//            "tiers"=>$tiers
//        ));
//    }
//
//    public function saveAction(Request $request){
//        $data = $request->request->all()['data'];
//        $em = $this->getDoctrine()->getManager();
//        foreach($data as $value){
//            if(!empty($value['id'])) {
//                $tierValue = $em->getRepository("PriceBundle:TierValue")->find($value['id']);
//            } else {
//                $tierValue = new TierValue();
//            }
//            $tierValue->setBillingAgregatorId((int)$value['billing_provider_id'])
//                ->setCarrierId((int)$value['carrier_id'])
//                ->setValue(floatval($value['value']))
//                ->setCurrency($value['currency'])
//                ->setTier($em->getRepository("PriceBundle:Tier")->find($value['tier_id']));
//            $em->persist($tierValue);
//            $em->flush();
//            $em->clear();
//        }
//         // Detaches all objects from Doctrine!
//        return new JsonResponse(array(), 201);
//    }

    /**
     * Returns a list of countries, carriers and price tier values
     *
     * @return JsonResponse
     */
    public function valuesAction()
    {
        $country = $this->getRequest()->get("countryCode");

        $service = $this->get("price.service.carrier");

//        return new JsonResponse($service->getCarrierPrices($country), 200);
////        $request = $this->getRequest();
////
////        $this->admin->checkAccess('list');
////
////        $preResponse = $this->preList($request);
////        if ($preResponse !== null) {
////            return $preResponse;
////        }
////
////        if ($listMode = $request->get('_list_mode')) {
////            $this->admin->setListMode($listMode);
////        }
////
//////        $datagrid = $this->admin->getDatagrid();
////        $datagrid = $this->getDoctrine()->getRepository("PriceBundle:TierValue")->findAll();
////        $formView = $datagrid->getForm()->createView();
////
////        // set the theme for the current Admin Form
////        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());
////
////        return $this->render($this->admin->getTemplate('list'), array(
////            'action' => 'list',
////            'form' => $formView,
////            'datagrid' => $datagrid,
////            'csrf_token' => $this->getCsrfToken('sonata.batch'),
////        ), null, $request);
    }
}