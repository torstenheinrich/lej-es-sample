<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Lej\Component\Domain\Model\ValueObject;
use Money\Money;

class InvoiceLineItem implements ValueObject
{
    /** @var string */
    private $description;
    /** @var Money */
    private $price;

    /**
     * @param string $description
     * @param Money $price
     */
    public function __construct(string $description, Money $price)
    {
        $this->description = $description;
        $this->price = $price;
    }

    /**
     * @param Subscription $subscription
     * @return InvoiceLineItem
     */
    public static function createFromSubscription(Subscription $subscription) : InvoiceLineItem
    {
        $description = sprintf(
            'Subscription (%s) for Product %s',
            $subscription->term(),
            $subscription->product()->name()
        );

        return new self($description, $subscription->product()->price());
    }

    /**
     * @return string
     */
    public function description() : string
    {
        return $this->description;
    }

    /**
     * @return Money
     */
    public function price() : Money
    {
        return $this->price;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObject $object) : bool
    {
        return $object instanceof InvoiceLineItem
            && $object->description() === $this->description()
            && $object->price()->equals($this->price());
    }
}
