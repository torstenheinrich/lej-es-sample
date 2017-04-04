<?php

declare(strict_types=1);

namespace LejSample\Subscription\Application;

class BillAccountCommand
{
    /** @var string */
    private $accountId;
    /** @var string */
    private $systemId;

    /**
     * @param string $accountId
     * @param string $systemId
     */
    public function __construct(string $accountId, string $systemId)
    {
        $this->accountId = $accountId;
        $this->systemId = $systemId;
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
    public function systemId() : string
    {
        return $this->systemId;
    }
}
