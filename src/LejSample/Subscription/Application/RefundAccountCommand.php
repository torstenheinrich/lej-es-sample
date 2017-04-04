<?php

declare(strict_types=1);

namespace LejSample\Subscription\Application;

class RefundAccountCommand
{
    /** @var string */
    private $accountId;
    /** @var string */
    private $reason;
    /** @var string */
    private $amount;
    /** @var string */
    private $currency;
    /** @var string */
    private $accountantId;

    /**
     * @param string $accountId
     * @param string $reason
     * @param string $amount
     * @param string $currency
     * @param string $accountantId
     */
    public function __construct(
        string $accountId,
        string $reason,
        string $amount,
        string $currency,
        string $accountantId
    ) {
        $this->accountId = $accountId;
        $this->reason = $reason;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->accountantId = $accountantId;
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
    public function reason() : string
    {
        return $this->reason;
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
    public function accountantId() : string
    {
        return $this->accountantId;
    }
}
