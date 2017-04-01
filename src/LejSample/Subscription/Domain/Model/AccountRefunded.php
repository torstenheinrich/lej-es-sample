<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Lej\Component\Domain\Model\DomainEvent;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

class AccountRefunded implements DomainEvent
{
    /** @var UuidInterface */
    private $accountId;
    /** @var UuidInterface */
    private $refundId;
    /** @var Money */
    private $balance;
    /** @var Accountant */
    private $accountant;
    /** @var \DateTimeImmutable */
    private $occurredOn;

    /**
     * @param UuidInterface $accountId
     * @param UuidInterface $refundId
     * @param Money $balance
     * @param Accountant $accountant
     * @param \DateTimeImmutable $occurredOn
     */
    public function __construct(
        UuidInterface $accountId,
        UuidInterface $refundId,
        Money $balance,
        Accountant $accountant,
        \DateTimeImmutable $occurredOn
    ) {
        $this->accountId = $accountId;
        $this->refundId = $refundId;
        $this->balance = $balance;
        $this->accountant = $accountant;
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
    public function refundId() : UuidInterface
    {
        return $this->refundId;
    }

    /**
     * @return Money
     */
    public function balance() : Money
    {
        return $this->balance;
    }

    /**
     * @return Accountant
     */
    public function accountant() : Accountant
    {
        return $this->accountant;
    }

    /**
     * {@inheritdoc}
     */
    public function occurredOn() : \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
