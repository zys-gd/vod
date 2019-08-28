<?php
/**
 * Created by PhpStorm.
 * User: dmitriy
 * Date: 06.08.19
 * Time: 13:54
 */

namespace SubscriptionBundle\Piwik\Formatter;


use SubscriptionBundle\Piwik\DTO\ConversionEvent;

interface FormatterInterface
{
    public function prepareFormattedData(ConversionEvent $event);
}