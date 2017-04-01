<?php

declare(strict_types=1);

namespace LejSample\Component\Infrastructure\Persistence;

use JMS\Serializer\SerializerInterface;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use Ramsey\Uuid\Uuid;

class MongoDbEventStore implements EventStore
{
    /** @var Client */
    private $client;
    /** @var string */
    private $database;
    /** @var SerializerInterface */
    private $serializer;

    /**
     * @param Client $client
     * @param string $database
     * @param SerializerInterface $serializer
     */
    public function __construct(Client $client, string $database, SerializerInterface $serializer)
    {
        $this->client = $client;
        $this->database = $database;
        $this->serializer = $serializer;
    }

    /**
     * {@inheritdoc}
     */
    public function load(string $id) : array
    {
        $events = [];
        $documents = $this->client()
            ->selectCollection($this->database(), $id)
            ->find();

        foreach ($documents as $document) {
            $events[] = $this->serializer->deserialize($document['data'], $document['type'], 'json');
        }

        return $events;
    }

    /**
     * {@inheritdoc}
     */
    public function append(string $id, array $events)
    {
        $documents = [];

        foreach ($events as $event) {
            $documents[] = [
                '_id' => Uuid::uuid4()->toString(),
                'data' => $this->serializer->serialize($event, 'json'),
                'type' => get_class($event),
                'createdOn' => new UTCDateTime(time() * 1000)
            ];
        }

        $this->client()
            ->selectCollection($this->database(), $id)
            ->insertMany($documents);
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
}
