<?php

declare(strict_types=1);

namespace LejSample\Subscription\Application;

class DischargeAccountCommand
{
    /** @var string */
    private $accountId;
    /** @var string */
    private $amount;
    /** @var string */
    private $currency;
    /** @var string */
    private $customerId;

    /**
     * @param string $accountId
     * @param string $amount
     * @param string $currency
     * @param string $customerId
     */
    public function __construct(string $accountId, string $amount, string $currency, string $customerId)
    {
        $this->accountId = $accountId;
        $this->amount = $amount;
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
    public function amount() : string
    {
        return $this->amount;
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
