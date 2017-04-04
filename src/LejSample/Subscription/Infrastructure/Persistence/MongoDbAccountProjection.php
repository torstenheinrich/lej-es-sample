<?php

declare(strict_types=1);

namespace LejSample\Subscription\Infrastructure\Persistence;

use Lej\Component\Domain\Model\DomainEvent;
use LejSample\Component\Infrastructure\Persistence\EventDispatcher;
use LejSample\Subscription\Domain\Model\AccountBilled;
use LejSample\Subscription\Domain\Model\AccountClosed;
use LejSample\Subscription\Domain\Model\AccountCreated;
use LejSample\Subscription\Domain\Model\AccountDischarged;
use LejSample\Subscription\Domain\Model\AccountRefunded;
use MongoDB\BSON\UTCDateTime;

class MongoDbAccountProjection extends AbstractMongoDbRepository implements EventDispatcher
{
    /** @var bool[] */
    private $understandsEvents = [
        AccountCreated::class => true,
        AccountBilled::class => true,
        AccountDischarged::class => true,
        AccountRefunded::class => true,
        AccountClosed::class => true
    ];

    /**
     * @param AccountCreated $event
     */
    public function onAccountCreated(AccountCreated $event)
    {
        $account = [
            '_id' => $event->accountId()->toString(),
            'balance' => $event->balance()->getAmount(),
            'currency' => $event->balance()->getCurrency()->getCode(),
            'status' => $event->status(),
            'createdBy' => $event->customer()->id()->toString(),
            'createdOn' => new UTCDateTime($event->occurredOn()->format('U') * 1000),
            'updatedBy' => null,
            'updatedOn' => null
        ];

        $this->client()
            ->selectCollection($this->database(), $this->collection())
            ->insertOne($account);
    }

    /**
     * @param AccountBilled $event
     */
    public function onAccountBilled(AccountBilled $event)
    {
        $update = [
            '$set' => [
                'balance' => $event->balance()->getAmount(),
                'updatedBy' => $event->system()->id()->toString(),
                'updatedOn' => new UTCDateTime($event->occurredOn()->format('U') * 1000),
            ]
        ];

        $this->client()
            ->selectCollection($this->database(), $this->collection())
            ->updateOne(['_id' => $event->accountId()->toString()], $update);
    }

    /**
     * @param AccountDischarged $event
     */
    public function onAccountDischarged(AccountDischarged $event)
    {
        $update = [
            '$set' => [
                'balance' => $event->balance()->getAmount(),
                'updatedBy' => $event->customer()->id()->toString(),
                'updatedOn' => new UTCDateTime($event->occurredOn()->format('U') * 1000),
            ]
        ];

        $this->client()
            ->selectCollection($this->database(), $this->collection())
            ->updateOne(['_id' => $event->accountId()->toString()], $update);
    }

    /**
     * @param AccountRefunded $event
     */
    public function onAccountRefunded(AccountRefunded $event)
    {
        $update = [
            '$set' => [
                'balance' => $event->balance()->getAmount(),
                'updatedBy' => $event->accountant()->id()->toString(),
                'updatedOn' => new UTCDateTime($event->occurredOn()->format('U') * 1000),
            ]
        ];

        $this->client()
            ->selectCollection($this->database(), $this->collection())
            ->updateOne(['_id' => $event->accountId()->toString()], $update);
    }

    /**
     * @param AccountClosed $event
     */
    public function onAccountClosed(AccountClosed $event)
    {
        $update = [
            '$set' => [
                'status' => $event->status(),
                'updatedBy' => $event->customer()->id()->toString(),
                'updatedOn' => new UTCDateTime($event->occurredOn()->format('U') * 1000),
            ]
        ];

        $this->client()
            ->selectCollection($this->database(), $this->collection())
            ->updateOne(['_id' => $event->accountId()->toString()], $update);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(DomainEvent $event)
    {
        $this->{'on' . substr(strrchr(get_class($event), '\\'), 1)}($event);
    }

    /**
     * {@inheritdoc}
     */
    public function registerEventDispatcher(EventDispatcher $dispatcher)
    {
        throw new \Exception('Registering additional event dispatchers is not supported.');
    }

    /**
     * {@inheritdoc}
     */
    public function understands(DomainEvent $event) : bool
    {
        return isset($this->understandsEvents[get_class($event)]) && $this->understandsEvents[get_class($event)];
    }
}
