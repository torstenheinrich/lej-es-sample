<?php

declare(strict_types=1);

$loader = require __DIR__ . '/vendor/autoload.php';

use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use LejSample\Component\Infrastructure\Persistence\MongoDbEventStore;
use LejSample\Component\Infrastructure\Serializer\CurrencySubscribingHandler;
use LejSample\Component\Infrastructure\Serializer\MoneySubscribingHandler;
use LejSample\Component\Infrastructure\Serializer\UuidSubscribingHandler;
use LejSample\Subscription\Domain\Model\Account;
use LejSample\Subscription\Domain\Model\Accountant;
use LejSample\Subscription\Domain\Model\Customer;
use LejSample\Subscription\Domain\Model\Product;
use LejSample\Subscription\Domain\Model\Subscription;
use LejSample\Subscription\Domain\Model\System;
use LejSample\Subscription\Infrastructure\Persistence\EventStoreAccountRepository;
use Money\Currency;
use Money\Money;
use MongoDB\Client;
use Ramsey\Uuid\Uuid;

$builder = SerializerBuilder::create();
$builder->addMetadataDirs([
    'LejSample\\Subscription\\Domain\\Model' => __DIR__ . '/src/LejSample/Subscription/Infrastructure/Serializer'
]);
$builder->configureHandlers(function(HandlerRegistry $registry) {
    $registry->registerSubscribingHandler(new CurrencySubscribingHandler());
    $registry->registerSubscribingHandler(new MoneySubscribingHandler());
    $registry->registerSubscribingHandler(new UuidSubscribingHandler());
});
$builder->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy());

$client = new Client();
$database = 'lej-es-sample';
$eventStore = new MongoDbEventStore($client, $database, $builder->build());
$repository = new EventStoreAccountRepository($eventStore);

// clean up the database
$client->dropDatabase('lej-es-sample');

// create all the users
$accountant = new Accountant(Uuid::uuid4(), 'Alice', 'alice@example.org');
$customer = new Customer(Uuid::uuid4(), 'Jason', 'jason@random.org');
$system = new System(Uuid::uuid4());

// create the account
$id = Uuid::uuid4();
$account = Account::create($id, new Currency('EUR'), $customer);

$subscriptions = [
    new Subscription(Uuid::uuid4(), $id, 'monthly', new Product('Plan A', new Money(10, new Currency('EUR')))),
    new Subscription(Uuid::uuid4(), $id, 'monthly', new Product('Plan B', new Money(20, new Currency('EUR'))))
];

// execute a bit of business logic
$account->bill($subscriptions, $system);
$account->discharge(new Money(30, new Currency('EUR')), $customer);
$account->bill($subscriptions, $system);
$account->discharge(new Money(10, new Currency('EUR')), $customer);
$account->discharge(new Money(10, new Currency('EUR')), $customer);
$account->discharge(new Money(10, new Currency('EUR')), $customer);
$account->bill($subscriptions, $system);
$account->bill($subscriptions, $system);
$account->refund('double charge', new Money(30, new Currency('EUR')), $accountant);
$account->discharge(new Money(30, new Currency('EUR')), $customer);
$account->close($customer);

// persist the account
$repository->save($account);

echo PHP_EOL;
$account = $repository->accountOfId($id);
$message = sprintf(
    'Balance: %d %s, Status: %s, Created by: %s, Updated by: %s',
    $account->balance()->getAmount(),
    $account->balance()->getCurrency(),
    $account->status(),
    $account->createdBy()->name(),
    $account->updatedBy()->name()
);
echo $message . PHP_EOL . PHP_EOL;

$i = 1;
$account = Account::createEmpty();
$events = $eventStore->load($id->toString());
echo 'Events: ' . count($events) . PHP_EOL . PHP_EOL;
foreach ($events as $event) {
    $account->applyCommittedEvents([$event]);
    $balance = $account->balance();

    if ($account->createdBy() && !$account->updatedBy()) {
        $format = '    (%02d) %53s with balance %3d %s, created by %6s (%s)';
        $user = $account->createdBy();
    } else {
        $format = '    (%02d) %53s with balance %3d %s, updated by %6s (%s)';
        $user = $account->updatedBy();
    }

    $message = sprintf(
        $format,
        $i,
        get_class($event),
        $balance->getAmount(),
        $balance->getCurrency(),
        $user->name(),
        substr(get_class($user), strrpos(get_class($user), '\\') + 1)
    );

    echo $message . PHP_EOL;
    $i++;
}
echo PHP_EOL;

$i = 1;
$events = $client->selectCollection($database, $id->toString())->find()->toArray();
echo 'Events: ' . count($events) . PHP_EOL . PHP_EOL;
foreach ($events as $event) {
    $message = sprintf(
        '    (%02d) %32s with type %53s, created on %s',
        $i,
        $event['_id'],
        $event['type'],
        $event['createdOn']->toDateTime()->format('Y-m-d H:i:s')
    );

    echo $message . PHP_EOL;
    $i++;
}
echo PHP_EOL;
