<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 14.01.19
 * Time: 17:47
 */

namespace IdentificationBundle\Controller\ParamConverter;


use IdentificationBundle\Identification\DTO\IdentificationData;
use IdentificationBundle\Identification\Service\Session\IdentificationFlowDataExtractor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class IdentificationDataConverter implements ParamConverterInterface
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
        if (!$identificationToken = IdentificationFlowDataExtractor::extractIdentificationToken($request->getSession())) {
            throw new BadRequestHttpException('Identification data is not found');
        }

        $object = new IdentificationData($identificationToken);


        $request->attributes->set($configuration->getName(), $object);
    }

    /**
     * Checks if the object is supported.
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() == IdentificationData::class;
    }
}