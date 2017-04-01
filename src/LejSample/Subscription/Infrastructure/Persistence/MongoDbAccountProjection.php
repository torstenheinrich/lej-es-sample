<?php

declare(strict_types=1);

namespace LejSample\Subscription\Infrastructure\Persistence;

use LejSample\Component\Infrastructure\Persistence\Projection;
use LejSample\Subscription\Domain\Model\AccountBilled;
use LejSample\Subscription\Domain\Model\AccountClosed;
use LejSample\Subscription\Domain\Model\AccountCreated;
use LejSample\Subscription\Domain\Model\AccountDischarged;
use LejSample\Subscription\Domain\Model\AccountRefunded;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;

class MongoDbAccountProjection extends Projection
{
    /** @var Client */
    private $client;
    /** @var string */
    private $database;
    /** @var string */
    private $collection;

    /**
     * @param Client $client
     * @param string $database
     * @param string $collection
     */
    public function __construct(Client $client, string $database, string $collection)
    {
        $this->client = $client;
        $this->database = $database;
        $this->collection = $collection;
    }

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
            'createdBy' => $event->customer()->id(),
            'createdOn' => new UTCDateTime($event->occurredOn()->format('U')),
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
                'updatedBy' => $event->system()->id(),
                'updatedOn' => new UTCDateTime($event->occurredOn()->format('U')),
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
                'updatedBy' => $event->customer()->id(),
                'updatedOn' => new UTCDateTime($event->occurredOn()->format('U')),
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
                'updatedBy' => $event->accountant()->id(),
                'updatedOn' => new UTCDateTime($event->occurredOn()->format('U')),
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
                'updatedBy' => $event->customer()->id(),
                'updatedOn' => new UTCDateTime($event->occurredOn()->format('U')),
            ]
        ];

        $this->client()
            ->selectCollection($this->database(), $this->collection())
            ->updateOne(['_id' => $event->accountId()->toString()], $update);
    }

    /**
     * @return Client
     */
    private function client() : Client
    {
        return $this->client;
    }

    /**
     * @return string
     */
    private function database() : string
    {
        return $this->database;
    }

    /**
     * @return string
     */
    private function collection() : string
    {
        return $this->collection;
    }
}
