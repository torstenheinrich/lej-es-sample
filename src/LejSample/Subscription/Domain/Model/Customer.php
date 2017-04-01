<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Lej\Component\Domain\Model\ValueObject;
use Ramsey\Uuid\UuidInterface;

class Customer implements User
{
    /** @var UuidInterface */
    private $id;
    /** @var string */
    private $name;
    /** @var string */
    private $email;

    /**
     * @param UuidInterface $id
     * @param string $name
     * @param string $email
     */
    public function __construct(UuidInterface $id, string $name, string $email)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
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
     * @return string
     */
    public function email() : string
    {
        return $this->email;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(ValueObject $object) : bool
    {
        return $object instanceof Customer
            && $object->id()->equals($this->id())
            && $object->name() === $this->name()
            && $object->email() === $this->email();
    }
}
