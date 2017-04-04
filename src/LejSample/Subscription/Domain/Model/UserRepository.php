<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Ramsey\Uuid\UuidInterface;

interface UserRepository
{
    /**
     * @param UuidInterface $id
     * @return Accountant
     */
    public function accountantOfId(UuidInterface $id) :? Accountant;

    /**
     * @param UuidInterface $id
     * @return Customer
     */
    public function customerOfId(UuidInterface $id) :? Customer;

    /**
     * @param UuidInterface $id
     * @return System
     */
    public function systemOfId(UuidInterface $id) :? System;
}
