<?php

declare(strict_types=1);

namespace LejSample\Subscription\Application;

class AccountData
{
    /** @var string */
    private $id;
    /** @var string */
    private $balance;
    /** @var string */
    private $currency;
    /** @var string */
    private $status;
    /** @var string */
    private $createdBy;
    /** @var \DateTime */
    private $createdOn;
    /** @var string */
    private $updatedBy;
    /** @var \DateTime */
    private $updatedOn;

    /**
     * @param string $id
     * @param string $balance
     * @param string $currency
     * @param string $status
     * @param string $createdBy
     * @param \DateTime $createdOn
     * @param string $updatedBy
     * @param \DateTime $updatedOn
     */
    public function __construct(
        string $id,
        string $balance,
        string $currency,
        string $status,
        string $createdBy,
        \DateTime $createdOn,
        string $updatedBy,
        \DateTime $updatedOn
    ) {
        $this->id = $id;
        $this->balance = $balance;
        $this->currency = $currency;
        $this->status = $status;
        $this->createdBy = $createdBy;
        $this->createdOn = $createdOn;
        $this->updatedBy = $updatedBy;
        $this->updatedOn = $updatedOn;
    }

    /**
     * @return string
     */
    public function id() : string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function balance() : string
    {
        return $this->balance;
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
    public function status() : string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function createdBy() : string
    {
        return $this->createdBy;
    }

    /**
     * @return \DateTime
     */
    public function createdOn() : \DateTime
    {
        return $this->createdOn;
    }

    /**
     * @return string
     */
    public function updatedBy() : string
    {
        return $this->updatedBy;
    }

    /**
     * @return \DateTime
     */
    public function updatedOn() : \DateTime
    {
        return $this->updatedOn;
    }
}
