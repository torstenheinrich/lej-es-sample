<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Ramsey\Uuid\UuidInterface;

interface AccountRepository
{
    /**
     * @param UuidInterface $id
     * @return Account
     */
    public function accountOfId(UuidInterface $id) :? Account;

    /**
     * @param Account $account
     */
    public function save(Account $account);
}
