<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Ramsey\Uuid\UuidInterface;

interface SubscriptionRepository
{
    /**
     * @param UuidInterface $accountId
     * @return Subscription[]
     */
    public function subscriptionsOfAccount(UuidInterface $accountId) : array;
}
