<?php

declare(strict_types=1);

namespace LejSample\Subscription\Infrastructure\Persistence;

use MongoDB\Client;

class AbstractMongoDbRepository
{
    /** @var Client */
    protected $client;
    /** @var string */
    protected $database;
    /** @var string */
    protected $collection;

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
     * @return Client
     */
    protected function client() : Client
    {
        return $this->client;
    }

    /**
     * @return string
     */
    protected function database() : string
    {
        return $this->database;
    }

    /**
     * @return string
     */
    protected function collection() : string
    {
        return $this->collection;
    }
}
