<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 01.10.19
 * Time: 13:32
 */

namespace IdentificationBundle\Twig;


use ExtrasBundle\Cache\ArrayCache\ArrayCacheService;
use IdentificationBundle\Repository\TestUserRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProfilerPanelExtension extends AbstractExtension
{
    /**
     * @var TestUserRepository
     */
    private $testUserRepository;
    /**
     * @var ArrayCacheService
     */
    private $arrayCacheService;


    /**
     * ProfilerPanelExtension constructor.
     */
    public function __construct(TestUserRepository $testUserRepository, ArrayCacheService $arrayCacheService)
    {
        $this->testUserRepository = $testUserRepository;
        $this->arrayCacheService  = $arrayCacheService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('getTestUserData', function () {


                if ($this->arrayCacheService->hasCache('testUsers')) {
                    $users = $this->arrayCacheService->getValue('testUsers');
                } else {
                    $users = $this->testUserRepository->findActiveUsers();
                    $this->arrayCacheService->saveCache('testUsers', $users, 0);
                }

                $preparedUsers = [];


                foreach ($users as $user) {

                    $carrier = $user->getCarrier();
                    $id      = $carrier->getBillingCarrierId();

                    if (!isset($preparedUsers[$id])) {
                        $preparedUsers[$id] = ['msisdns' => [], 'name' => $carrier->getName()];
                    }

                    $preparedUsers[$id]['msisdns'][] = $user->getUserIdentifier();
                }

                $preparedUsers = array_filter($preparedUsers, function (array $data) {
                    return !empty($data['msisdns']);
                });

                return $preparedUsers;

            })
        ];
    }


}