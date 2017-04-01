<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Lej\Component\Domain\Model\Entity;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\UuidInterface;

class Invoice implements Entity
{
    /** @var UuidInterface */
    private $id;
    /** @var UuidInterface */
    private $accountId;
    /** @var InvoiceLineItem[] */
    private $lineItems;
    /** @var Currency */
    private $currency;

    /**
     * @param UuidInterface $id
     * @param UuidInterface $accountId
     * @param InvoiceLineItem[] $lineItems
     * @param Currency $currency
     */
    public function __construct(
        UuidInterface $id,
        UuidInterface $accountId,
        array $lineItems,
        Currency $currency
    ) {
        $this->id = $id;
        $this->accountId = $accountId;
        $this->lineItems = $lineItems;
        $this->currency = $currency;
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
     * @return InvoiceLineItem[]
     */
    public function lineItems() : array
    {
        return $this->lineItems;
    }

    /**
     * @return Currency
     */
    public function currency() : Currency
    {
        return $this->currency;
    }

    /**
     * @return Money
     */
    public function amount() : Money
    {
        $total = new Money(0, $this->currency());

        foreach ($this->lineItems() as $lineItem) {
            $total = $total->add($lineItem->price());
        }

        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(Entity $entity) : bool
    {
        return $entity instanceof Account
            && $entity->id()->equals($this->id());
    }
}
