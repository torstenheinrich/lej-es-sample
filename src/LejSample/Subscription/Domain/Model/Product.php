<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Lej\Component\Domain\Model\ValueObject;
use Money\Money;

class Product implements ValueObject
{
    /** @var string */
    private $name;
    /** @var Money */
    private $price;

    /**
     * @param string $name
     * @param Money $price
     */
    public function __construct(string $name, Money $price)
    {
        $this->name = $name;
        $this->price = $price;
    }

    /**
     * @return string
     */
    public function name() : string
    {
        return $this->name;
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
        return $object instanceof Product
            && $object->name() === $this->name()
            && $object->price()->equals($this->price());
    }
}
