<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 08.01.19
 * Time: 19:15
 */

namespace IdentificationBundle\Service\Action\Identification\DTO;


use Symfony\Component\HttpFoundation\Response;

class IdentifyResult
{
    private $response;

    /**
     * IdentifyResult constructor.
     * @param $response
     */
    public function __construct(Response $response = null)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getOverridedResponse(): ?Response
    {
        return $this->response;
    }


}