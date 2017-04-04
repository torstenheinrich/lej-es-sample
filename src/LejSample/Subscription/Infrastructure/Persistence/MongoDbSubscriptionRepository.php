<?php

declare(strict_types=1);

namespace LejSample\Subscription\Infrastructure\Persistence;

use LejSample\Subscription\Domain\Model\Product;
use LejSample\Subscription\Domain\Model\Subscription;
use LejSample\Subscription\Domain\Model\SubscriptionRepository;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class MongoDbSubscriptionRepository extends AbstractMongoDbRepository implements SubscriptionRepository
{
    /**
     * {@inheritdoc}
     */
    public function subscriptionsOfAccount(UuidInterface $accountId) : array
    {
        $documents = $this->client()
            ->selectCollection($this->database(), $this->collection())
            ->find(['accountId' => $accountId->toString()]);

        $subscriptions = [];

        foreach ($documents as $document) {
            $product = new Product(
                $document->product->name,
                new Money($document->product->price, new Currency($document->product->currency))
            );

            $subscription = new Subscription(
                Uuid::fromString($document->_id),
                Uuid::fromString($document->accountId),
                $document->term,
                $product
            );

            $subscriptions[] = $subscription;
        }

        return $subscriptions;
    }
}
