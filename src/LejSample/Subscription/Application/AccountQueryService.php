<?php

declare(strict_types=1);

namespace LejSample\Subscription\Application;

use MongoDB\Client;

class AccountQueryService
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
     * @param string $id
     * @return AccountData
     */
    public function accountDataOfId(string $id) :? AccountData
    {
        $document = $this->client()
            ->selectCollection($this->database(), $this->collection())
            ->findOne(['_id' => $id]);

        if (!$document) {
            return null;
        }

        $accountData = new AccountData(
            $document->_id,
            $document->balance,
            $document->currency,
            $document->status,
            $document->createdBy,
            $document->createdOn->toDateTime(),
            $document->updatedBy,
            $document->updatedOn->toDateTime()
        );

        return $accountData;
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
