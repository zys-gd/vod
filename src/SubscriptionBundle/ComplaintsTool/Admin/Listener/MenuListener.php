<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 21.11.19
 * Time: 16:49
 */

namespace SubscriptionBundle\ComplaintsTool\Admin\Listener;


use Sonata\AdminBundle\Event\ConfigureMenuEvent;
use SubscriptionBundle\ComplaintsTool\Admin\Sonata\ComplaintsAdmin;

class MenuListener
{
    /**
     * @var ComplaintsAdmin
     */
    private $admin;


    /**
     * MenuListener constructor.
     * @param ComplaintsAdmin $admin
     */
    public function __construct(ComplaintsAdmin $admin)
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

        $urlData = $this->admin->generateMenuUrl('report');

        $mainMenu->addChild('report', [
            'label' => 'Make report',
            'route' => $urlData['route'],
        ]);
    }
}