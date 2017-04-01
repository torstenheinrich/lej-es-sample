<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Lej\Component\Domain\Model\DomainEvent;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

class AccountDischarged implements DomainEvent
{
    /** @var UuidInterface */
    private $accountId;
    /** @var UuidInterface */
    private $paymentId;
    /** @var Money */
    private $balance;
    /** @var Customer */
    private $customer;
    /** @var \DateTimeImmutable */
    private $occurredOn;

    /**
     * @param UuidInterface $accountId
     * @param UuidInterface $paymentId
     * @param Money $balance
     * @param Customer $customer
     * @param \DateTimeImmutable $occurredOn
     */
    public function __construct(
        UuidInterface $accountId,
        UuidInterface $paymentId,
        Money $balance,
        Customer $customer,
        \DateTimeImmutable $occurredOn
    ) {
        $this->accountId = $accountId;
        $this->paymentId = $paymentId;
        $this->balance = $balance;
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
     * @return UuidInterface
     */
    public function paymentId() : UuidInterface
    {
        return $this->paymentId;
    }

    /**
     * @return Money
     */
    public function balance() : Money
    {
        return $this->balance;
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
