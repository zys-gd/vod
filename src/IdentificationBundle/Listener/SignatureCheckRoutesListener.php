<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 21.01.19
 * Time: 18:12
 */

namespace IdentificationBundle\Listener;


use Doctrine\Common\Annotations\AnnotationReader;
use IdentificationBundle\Controller\Annotation\SignatureCheckIsRequired;
use IdentificationBundle\Identification\Service\SignatureHandler;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class SignatureCheckRoutesListener
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;
    /**
     * @var SignatureHandler
     */
    private $checker;


    /**
     * SignatureCheckRoutesListener constructor.
     */
    public function __construct(AnnotationReader $annotationReader, SignatureHandler $checker)
    {
        $this->annotationReader = $annotationReader;
        $this->checker          = $checker;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        $args = $event->getController();
        if (is_array($args)) {
            $controller = $args[0] ?? null;
            $method     = $args[1] ?? null;
        } else {
            $method = $controller = $args;
        }

        if (!$controller || !$method) {
            return;
        }

        if (!is_string($method)) {
            // Avoid `callback as controller` errors
            return;
        }

        $controllerReflection = new \ReflectionObject($controller);
        $methodReflection     = $controllerReflection->getMethod($method);

        $annotation = $this->annotationReader->getMethodAnnotation($methodReflection, SignatureCheckIsRequired::class);

        if (!$annotation) {
            return;
        }

        $request = $event->getRequest();

        $signatureParam = $request->get('signature', '');

        if (!$signatureParam) {
            throw new BadRequestHttpException('Signature is missing');
        }

        $this->checker->performSignatureCheck($signatureParam, array_merge($request->query->all(), $request->request->all()));

        return (bool)!$annotation;


    }
}