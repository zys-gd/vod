<?php

namespace SubscriptionBundle\Piwik\Service;

use SubscriptionBundle\Repository\ExchangeRateRepository;

class CurrencyConverter
{
    /**
     * @var ExchangeRateRepository
     */
    private $exchangeRateRepository;

    public function __construct(ExchangeRateRepository $exchangeRateRepository)
    {
        $this->exchangeRateRepository = $exchangeRateRepository;
    }

    public function convert(string $from, $sum)
    {
        if (!$from) {
            return false;
        }

        $currencyRow = $this->exchangeRateRepository->findOneBy([
            'currencyCode' => strtoupper($from)
        ]);

        if (!$currencyRow) {
            throw new \RuntimeException(sprintf('Currency `%s` is not supported', $from));
        }

        return round($sum / $currencyRow->getExchangeRate(), 4);
    }
}