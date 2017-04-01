<?php

declare(strict_types=1);

namespace LejSample\Subscription\Infrastructure\Persistence;

use LejSample\Component\Infrastructure\Persistence\EventStore;
use LejSample\Subscription\Domain\Model\Account;
use LejSample\Subscription\Domain\Model\AccountRepository;
use Ramsey\Uuid\UuidInterface;

class EventStoreAccountRepository implements AccountRepository
{
    /** @var EventStore */
    private $eventStore;

    /**
     * @param EventStore $eventStore
     */
    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    /**
     * {@inheritdoc}
     */
    public function accountOfId(UuidInterface $id) :? Account
    {
        $events = $this->eventStore()->load($id->toString());

        if (empty($events)) {
            return null;
        }

        $account = Account::createEmpty();
        $account->applyCommittedEvents($events);

        return $account;
    }

    /**
     * {@inheritdoc}
     */
    public function save(Account $account)
    {
        $this->eventStore()->append($account->id()->toString(), $account->uncommittedEvents());
        $account->resetUncommittedEvents();
    }

    /**
     * @return EventStore
     */
    private function eventStore() : EventStore
    {
        return $this->eventStore;
    }
}
