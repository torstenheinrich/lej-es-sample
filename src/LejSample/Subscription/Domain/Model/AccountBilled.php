<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Lej\Component\Domain\Model\DomainEvent;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

class AccountBilled implements DomainEvent
{
    /** @var UuidInterface */
    private $accountId;
    /** @var UuidInterface */
    private $invoiceId;
    /** @var Money */
    private $balance;
    /** @var System */
    private $system;
    /** @var \DateTimeImmutable */
    private $occurredOn;

    /**
     * @param UuidInterface $accountId
     * @param UuidInterface $invoiceId
     * @param Money $balance
     * @param System $system
     * @param \DateTimeImmutable $occurredOn
     */
    public function __construct(
        UuidInterface $accountId,
        UuidInterface $invoiceId,
        Money $balance,
        System $system,
        \DateTimeImmutable $occurredOn
    ) {
        $this->accountId = $accountId;
        $this->invoiceId = $invoiceId;
        $this->balance = $balance;
        $this->system = $system;
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
    public function invoiceId() : UuidInterface
    {
        return $this->invoiceId;
    }

    /**
     * @return Money
     */
    public function balance() : Money
    {
        return $this->balance;
    }

    /**
     * @return System
     */
    public function system() : System
    {
        return $this->system;
    }

    /**
     * {@inheritdoc}
     */
    public function occurredOn() : \DateTimeImmutable
    {
        return $this->occurredOn;
    }
}
