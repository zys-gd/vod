<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.05.18
 * Time: 12:17
 */

namespace SubscriptionBundle\Subscription\Renew\Controller;


use SubscriptionBundle\Subscription\MassRenew\Command\MassRenewCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;

class InstantRenewAction
{
    /**
     * @var MassRenewCommand
     */
    private $renewCommand;
    /**
     * @var \Twig_Environment
     */
    private $twigEnvironment;


    /**
     * RenewAction constructor.
     * @param \SubscriptionBundle\Subscription\MassRenew\Command\MassRenewCommand $renewCommand
     * @param \Twig_Environment                                                   $twigEnvironment
     */
    public function __construct(
        MassRenewCommand $renewCommand,
        \Twig_Environment $twigEnvironment
    )
    {
        $this->renewCommand    = $renewCommand;
        $this->twigEnvironment = $twigEnvironment;
    }

    public function __invoke(string $carrierId)
    {

        $input  = new ArrayInput(['carrier_id' => $carrierId]);
        $output = new BufferedOutput();

        try {

            $this->renewCommand->run($input, $output);
            $RESULT = $output->fetch();
        } catch (\Exception $exception) {
            $RESULT = $exception->getMessage();
        }

        $template = $this->twigEnvironment->createTemplate('<body><pre>{{content}}</pre></body>');

        return new Response($template->render([
            'content' => $RESULT
        ]), 200);
    }


}