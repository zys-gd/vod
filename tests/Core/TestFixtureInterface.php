<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 18.06.18
 * Time: 16:03
 */

namespace Tests\Core;


/**
 * This class is replacement for DependentFixtureInterface, which is not works good combined with OrderedFixtureInterface.
 * Refactor candidate.
 *
 * Interface TestFixtureInterface
 * @package Tests\subscription\SubscriptionBundle
 */
interface TestFixtureInterface
{
    public function getDependencies(): array;
}