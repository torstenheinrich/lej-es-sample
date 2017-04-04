<?php

declare(strict_types=1);

namespace LejSample\Subscription\Infrastructure\Persistence;

use LejSample\Subscription\Domain\Model\Accountant;
use LejSample\Subscription\Domain\Model\Customer;
use LejSample\Subscription\Domain\Model\System;
use LejSample\Subscription\Domain\Model\UserRepository;
use MongoDB\Model\BSONDocument;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class MongoDbUserRepository extends AbstractMongoDbRepository implements UserRepository
{
    /**
     * {@inheritdoc}
     */
    public function accountantOfId(UuidInterface $id) :? Accountant
    {
        $document = $this->userOfType($id->toString(), 'accountant');

        if (!$document) {
            return null;
        }

        $accountant = new Accountant(
            Uuid::fromString($document->_id),
            $document->name,
            $document->email
        );

        return $accountant;
    }

    /**
     * {@inheritdoc}
     */
    public function customerOfId(UuidInterface $id) :? Customer
    {
        $document = $this->userOfType($id->toString(), 'customer');

        if (!$document) {
            return null;
        }

        $customer = new Customer(
            Uuid::fromString($document->_id),
            $document->name,
            $document->email
        );

        return $customer;
    }

    /**
     * {@inheritdoc}
     */
    public function systemOfId(UuidInterface $id) :? System
    {
        $document = $this->userOfType($id->toString(), 'system');

        if (!$document) {
            return null;
        }

        $system = new System(Uuid::fromString($document->_id));

        return $system;
    }

    /**
     * @param string $id
     * @param string $type
     * @return BSONDocument
     */
    private function userOfType(string $id, string $type) :? BSONDocument
    {
        $filter = [
            '_id' => $id,
            'type' => $type
        ];

        /** @var BSONDocument $document */
        $document = $this->client()
            ->selectCollection($this->database(), $this->collection())
            ->findOne($filter);

        return $document;
    }
}
