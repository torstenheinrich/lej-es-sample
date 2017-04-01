<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Lej\Component\Domain\Model\ValueObject;
use Ramsey\Uuid\UuidInterface;

class System implements User
{
    /** @var UuidInterface */
    private $id;
    /** @var string */
    private $name;

    /**
     * @param UuidInterface $id
     */
    public function __construct(UuidInterface $id)
    {
        $this->id = $id;
        $this->name = 'System';
    }

    /**
     * @return UuidInterface
     */
    public function id() : UuidInterface
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function name() : string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObject $object) : bool
    {
        return $object instanceof System
            && $object->id()->equals($this->id())
            && $object->name() === $this->name();
    }
}
