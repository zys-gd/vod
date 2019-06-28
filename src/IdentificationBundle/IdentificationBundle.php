<?php

namespace IdentificationBundle;

use IdentificationBundle\DependencyInjection\Compiler\AlreadySubscribedHandlerPass;
use IdentificationBundle\DependencyInjection\Compiler\IdentificationCallbackHandlerPass;
use IdentificationBundle\DependencyInjection\Compiler\IdentificationHandlerPass;
use IdentificationBundle\DependencyInjection\Compiler\ErrorCodeMapperPass;
use IdentificationBundle\DependencyInjection\Compiler\WifiIdentificationHandlerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class IdentificationBundle
 */
class IdentificationBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new IdentificationHandlerPass());
        $container->addCompilerPass(new IdentificationCallbackHandlerPass());
        $container->addCompilerPass(new WifiIdentificationHandlerPass());
        $container->addCompilerPass(new ErrorCodeMapperPass());
        $container->addCompilerPass(new AlreadySubscribedHandlerPass());
    }

}