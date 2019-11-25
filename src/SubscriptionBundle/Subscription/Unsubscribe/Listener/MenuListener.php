<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 21.11.19
 * Time: 16:39
 */

namespace SubscriptionBundle\Subscription\Unsubscribe\Listener;


use Sonata\AdminBundle\Event\ConfigureMenuEvent;
use SubscriptionBundle\Subscription\Unsubscribe\Admin\Sonata\UnsubscriptionAdmin;

class MenuListener
{
    /**
     * @var UnsubscriptionAdmin
     */
    private $admin;


    /**
     * MenuListener constructor.
     * @param UnsubscriptionAdmin $admin
     */
    public function __construct(UnsubscriptionAdmin $admin)
    {
        $this->admin = $admin;
    }

    public function addMenuItems(ConfigureMenuEvent $event)
    {
        $menu = $event->getMenu();

        if (!$mainMenu = $menu->getChild('user_management')) {
            $mainMenu = $menu
                ->addChild('user_management', [
                    'label' => 'User Management',
                ])
                ->setExtras([
                    'icon'  => '<i class="fa fa-users"></i>',
                    'roles' => ['ROLE_COMMON_ADMIN', 'ROLE_SUPER_ADMIN']
                ]);
        }

        $urlData = $this->admin->generateMenuUrl('unsubscribe_form');

        $mainMenu->addChild('unsubscribe_form', [
            'label' => 'Unsubscribe users',
            'route' => $urlData['route'],
        ]);
    }
}