<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Lej\Component\Domain\Model\Entity;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

class Payment implements Entity
{
    /** @var UuidInterface */
    private $id;
    /** @var UuidInterface */
    private $accountId;
    /** @var Money */
    private $amount;

    /**
     * @param UuidInterface $id
     * @param UuidInterface $accountId
     * @param Money $amount
     */
    public function __construct(UuidInterface $id, UuidInterface $accountId, Money $amount)
    {
        $this->id = $id;
        $this->accountId = $accountId;
        $this->amount = $amount;
    }

    /**
     * @return UuidInterface
     */
    public function id() : UuidInterface
    {
        return $this->id;
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
    public function amount() : Money
    {
        return $this->amount;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(Entity $entity) : bool
    {
        return $entity instanceof Payment
            && $entity->id()->equals($this->id());
    }
}
