<?php

declare(strict_types=1);

namespace LejSample\Subscription\Application;

class CloseAccountCommand
{
    /** @var string */
    private $accountId;
    /** @var string */
    private $customerId;

    /**
     * @param string $accountId
     * @param string $customerId
     */
    public function __construct(string $accountId, string $customerId)
    {
        $this->accountId = $accountId;
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
    public function customerId() : string
    {
        return $this->customerId;
    }
}
