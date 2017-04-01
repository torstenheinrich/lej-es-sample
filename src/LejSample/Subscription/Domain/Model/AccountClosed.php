<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Lej\Component\Domain\Model\DomainEvent;
use Ramsey\Uuid\UuidInterface;

class AccountClosed implements DomainEvent
{
    /** @var UuidInterface */
    private $accountId;
    /** @var string */
    private $status;
    /** @var Customer */
    private $customer;
    /** @var \DateTimeImmutable */
    private $occurredOn;

    /**
     * @param UuidInterface $accountId
     * @param string $status
     * @param Customer $customer
     * @param \DateTimeImmutable $occurredOn
     */
    public function __construct(
        UuidInterface $accountId,
        string $status,
        Customer $customer,
        \DateTimeImmutable $occurredOn
    ) {
        $this->accountId = $accountId;
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
