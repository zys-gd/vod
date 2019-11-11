<?php

namespace App\Domain\Service\OneClickFlow;

/**
 * Interface HasCustomOneClickRedirectRules
 */
interface HasCustomOneClickRedirectRules
{
    /**
     * @return string
     */
    public function getRedirectUrl(): string;
}