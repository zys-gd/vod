<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 25.05.18
 * Time: 12:17
 */

namespace SubscriptionBundle\Controller\Actions\Fake;


use SubscriptionBundle\Subscription\Renew\Command\IncomingRenewNotificationCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;

class AlertAction
{
    /**
     * @var \SubscriptionBundle\Subscription\Renew\Command\IncomingRenewNotificationCommand
     */
    private $notificationCommand;
    /**
     * @var \Twig_Environment
     */
    private $twigEnvironment;


    /**
     * RenewAction constructor.
     * @param IncomingRenewNotificationCommand $alertAction
     * @param \Twig_Environment                $twigEnvironment
     */
    public function __construct(
        IncomingRenewNotificationCommand $alertAction,
        \Twig_Environment $twigEnvironment
    )
    {
        $this->notificationCommand = $alertAction;
        $this->twigEnvironment     = $twigEnvironment;
    }

    public function __invoke(string $carrierId)
    {

        $input  = new ArrayInput(['carrier_id' => $carrierId]);
        $output = new BufferedOutput();

        try {

            $this->notificationCommand->run($input, $output);
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