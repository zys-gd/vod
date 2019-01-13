<?php
/**
 * Created by PhpStorm.
 * User: Администратор
 * Date: 13.01.2019
 * Time: 20:30
 */

namespace IdentificationBundle\Controller\ParamConverter;


use IdentificationBundle\Identification\Service\IdentificationFlowDataExtractor;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InjectISPDetectionData implements ParamConverterInterface
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
        if (!$ispData = IdentificationFlowDataExtractor::extractIspDetectionData($request->getSession())) {
            throw new BadRequestHttpException('ISP detection data is not found');
        }

        $request->attributes->set($configuration->getName(), $ispData);
    }

    /**
     * Checks if the object is supported.
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return false;
    }
}