<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 13.01.2019
 * Time: 20:30
 */

namespace IdentificationBundle\Controller\ParamConverter;


use IdentificationBundle\Identification\DTO\ISPData;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ISPDetectionConverter
 */
class ISPDetectionConverter implements ParamConverterInterface
{

    /**
     * Stores the object in the request.
     *
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        if (!$billingCarrierId = IdentificationFlowDataExtractor::extractBillingCarrierId($request->getSession())) {
            throw new BadRequestHttpException('ISP detection data is not found');
        }

        $object = new ISPData($billingCarrierId);


        $request->attributes->set($configuration->getName(), $object);
    }

    /**
     * Checks if the object is supported.
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() == ISPData::class;
    }
}