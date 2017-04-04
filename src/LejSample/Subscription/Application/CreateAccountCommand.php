<?php

declare(strict_types=1);

namespace LejSample\Subscription\Application;

class CreateAccountCommand
{
    /** @var string */
    private $accountId;
    /** @var string */
    private $currency;
    /** @var string */
    private $customerId;

    /**
     * @param string $accountId
     * @param string $currency
     * @param string $customerId
     */
    public function __construct(string $accountId, string $currency, string $customerId)
    {
        $this->accountId = $accountId;
        $this->currency = $currency;
        $this->customerId = $customerId;
    }

    /**
     * @return string
     */
    public function accountId() : string
    {
        return $this->accountId;
    }

    /**
     * @return string
     */
    public function currency() : string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function customerId() : string
    {
        return $this->customerId;
    }
}
