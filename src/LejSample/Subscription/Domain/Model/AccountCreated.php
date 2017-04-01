<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Lej\Component\Domain\Model\DomainEvent;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

class AccountCreated implements DomainEvent
{
    /** @var UuidInterface */
    private $accountId;
    /** @var Money */
    private $balance;
    /** @var string */
    private $status;
    /** @var Customer */
    private $customer;
    /** @var \DateTimeImmutable */
    private $occurredOn;

    /**
     * @param UuidInterface $accountId
     * @param Money $balance
     * @param string $status
     * @param Customer $customer
     * @param \DateTimeImmutable $occurredOn
     */
    public function __construct(
        UuidInterface $accountId,
        Money $balance,
        string $status,
        Customer $customer,
        \DateTimeImmutable $occurredOn
    ) {
        $this->accountId = $accountId;
        $this->balance = $balance;
        $this->status = $status;
        $this->customer = $customer;
        $this->occurredOn = $occurredOn;
    }

    /**
     * @return UuidInterface
     */
    public function accountId() : UuidInterface
    {
        return $this->accountId;
    }

    /**
     * @return Money
     */
    public function balance() : Money
    {
        return $this->balance;
    }

    /**
     * @return string
     */
    public function status() : string
    {
        return $this->status;
    }

    /**
     * @return Customer
     */
    public function customer() : Customer
    {
        return $this->customer;
    }

    /**
     * {@inheritdoc}
     */
    public function occurredOn() : \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
