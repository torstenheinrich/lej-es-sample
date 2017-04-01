<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Lej\Component\Domain\Model\Entity;
use Ramsey\Uuid\UuidInterface;

class Subscription implements Entity
{
    /** @var UuidInterface */
    private $id;
    /** @var UuidInterface */
    private $accountId;
    /** @var string */
    private $term;
    /** @var Product */
    private $product;

    /**
     * @param UuidInterface $id
     * @param UuidInterface $accountId
     * @param string $term
     * @param Product $product
     */
    public function __construct(UuidInterface $id, UuidInterface $accountId, $term, Product $product)
    {
        $this->id = $id;
        $this->accountId = $accountId;
        $this->term = $term;
        $this->product = $product;
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
     * @return string
     */
    public function term() : string
    {
        return $this->term;
    }

    /**
     * @return Product
     */
    public function product() : Product
    {
        return $this->product;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(Entity $entity) : bool
    {
        return $entity instanceof Subscription
            && $entity->id()->equals($this->id());
    }
}
