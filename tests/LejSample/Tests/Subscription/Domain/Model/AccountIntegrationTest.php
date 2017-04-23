<?php

declare(strict_types=1);

namespace LejSample\Tests\Subscription\Domain\Model;

use JMS\Serializer\Handler\HandlerRegistry;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use LejSample\Component\Infrastructure\Persistence\EventStore;
use LejSample\Component\Infrastructure\Persistence\EventStoreEventDispatcher;
use LejSample\Component\Infrastructure\Persistence\MongoDbEventStore;
use LejSample\Component\Infrastructure\Serializer\CurrencySubscribingHandler;
use LejSample\Component\Infrastructure\Serializer\MoneySubscribingHandler;
use LejSample\Component\Infrastructure\Serializer\UuidSubscribingHandler;
use LejSample\Subscription\Application\AccountApplicationService;
use LejSample\Subscription\Application\AccountQueryService;
use LejSample\Subscription\Application\BillAccountCommand;
use LejSample\Subscription\Application\CloseAccountCommand;
use LejSample\Subscription\Application\CreateAccountCommand;
use LejSample\Subscription\Application\DischargeAccountCommand;
use LejSample\Subscription\Application\RefundAccountCommand;
use LejSample\Subscription\Domain\Model\Account;
use LejSample\Subscription\Domain\Model\AccountRefunded;
use LejSample\Subscription\Infrastructure\Persistence\EventStoreAccountRepository;
use LejSample\Subscription\Infrastructure\Persistence\MongoDbAccountProjection;
use LejSample\Subscription\Infrastructure\Persistence\MongoDbSubscriptionRepository;
use LejSample\Subscription\Infrastructure\Persistence\MongoDbUserRepository;
use MongoDB\Client;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AccountIntegrationTest
{
    /** @var Client */
    private $client;
    /** @var string */
    private $database;

    /**
     * @param Client $client
     * @param string $database
     */
    public function __construct(Client $client, string $database)
    {
        $this->client = $client;
        $this->database = $database;
    }

    public function execute()
    {
        $accountId = Uuid::uuid4();

        $this->setUpDatabase();
        list($accountantId, $customerId, $systemId) = $this->setUpUsers();
        $this->setUpSubscriptions($accountId);

        $eventStore = $this->eventStore();

        $accountRepository = new EventStoreAccountRepository($eventStore);
        $subscriptionRepository = new MongoDbSubscriptionRepository($this->client(), $this->database(), 'subscription');
        $userRepository = new MongoDbUserRepository($this->client(), $this->database(), 'user');

        $accountApplicationService = new AccountApplicationService($accountRepository, $subscriptionRepository, $userRepository);
        $accountQueryService = new AccountQueryService($this->client(), $this->database(), 'account');

        // execute a bit of business logic
        $accountApplicationService->createAccount($this->createAccountCommand($accountId, $customerId));
        $accountApplicationService->billAccount($this->billAccountCommand($accountId, $systemId));
        $accountApplicationService->dischargeAccount($this->dischargeAccountCommand($accountId, '30', $customerId));
        $accountApplicationService->billAccount($this->billAccountCommand($accountId, $systemId));
        $accountApplicationService->dischargeAccount($this->dischargeAccountCommand($accountId, '10', $customerId));
        $accountApplicationService->dischargeAccount($this->dischargeAccountCommand($accountId, '10', $customerId));
        $accountApplicationService->dischargeAccount($this->dischargeAccountCommand($accountId, '10', $customerId));
        $accountApplicationService->billAccount($this->billAccountCommand($accountId, $systemId));
        $accountApplicationService->billAccount($this->billAccountCommand($accountId, $systemId));
        $accountApplicationService->refundAccount($this->refundAccountCommand($accountId, 'Double charge', '30', $accountantId));
        $accountApplicationService->dischargeAccount($this->dischargeAccountCommand($accountId, '30', $customerId));
        $accountApplicationService->closeAccount($this->closeAccountCommand($accountId, $customerId));

        echo PHP_EOL;
        echo 'I----------------------------------------------------------------I' . PHP_EOL;
        echo 'I Account (Event store)                                          I' . PHP_EOL;
        echo 'I----------------------------------------------------------------I' . PHP_EOL;
        echo PHP_EOL;
        $account = $accountRepository->accountOfId($accountId);
        $message = sprintf(
            'Balance: %d %s, Status: %s, Created by: %s, Updated by: %s',
            $account->balance()->getAmount(),
            $account->balance()->getCurrency(),
            $account->status(),
            $account->createdBy()->name(),
            $account->updatedBy()->name()
        );
        echo $message . PHP_EOL;

        $i = 1;
        $account = Account::createEmpty();
        $events = $eventStore->load($accountId->toString());
        echo PHP_EOL;
        echo 'I----------------------------------------------------------------I' . PHP_EOL;
        echo 'I Events (Event store): ' . count($events) .' total                                 I' . PHP_EOL;
        echo 'I----------------------------------------------------------------I' . PHP_EOL;
        echo PHP_EOL;
        foreach ($events as $event) {
            $account->applyCommittedEvents([$event]);
            $balance = $account->balance();

            if ($account->createdBy() && !$account->updatedBy()) {
                $format = '(%02d) %53s with balance %3d %s, created by %6s (%s)';
                $user = $account->createdBy();
            } else {
                $format = '(%02d) %53s with balance %3d %s, updated by %6s (%s)';
                $user = $account->updatedBy();
            }

            if (AccountRefunded::class === get_class($event)) {
                $format = "\033[33m" . $format . " \033[0m";
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
        echo 'I----------------------------------------------------------------I' . PHP_EOL;
        echo 'I Account (Projection)                                           I' . PHP_EOL;
        echo 'I----------------------------------------------------------------I' . PHP_EOL;
        echo PHP_EOL;
        $data = $accountQueryService->accountDataOfId($accountId->toString());
        $message = sprintf(
            'Balance: %d %s, Status: %s, Created by: %s, Updated by: %s',
            $data->balance(),
            $data->currency(),
            $data->status(),
            $data->createdBy(),
            $data->updatedBy()
        );
        echo $message . PHP_EOL;

        $i = 1;
        echo PHP_EOL;
        echo 'I----------------------------------------------------------------I' . PHP_EOL;
        echo 'I MongoDB (Collections)                                          I' . PHP_EOL;
        echo 'I----------------------------------------------------------------I' . PHP_EOL;
        echo PHP_EOL;
        $collections = $this->client()->selectDatabase($this->database())->listCollections();
        foreach ($collections as $collection) {
            $message = sprintf('(%02d) Name: %s', $i, $collection->getName());
            echo $message . PHP_EOL;
            $i++;
        }

        $i = 1;
        echo PHP_EOL;
        echo 'I----------------------------------------------------------------I' . PHP_EOL;
        echo 'I MongoDB (Events)                                               I' . PHP_EOL;
        echo 'I----------------------------------------------------------------I' . PHP_EOL;
        echo PHP_EOL;
        $events = $this->client()->selectCollection($this->database(), $accountId->toString());
        foreach ($events->find() as $event) {
            $message = sprintf(
                '(%02d) Id: %s, Data: %s, Type: %s',
                $i,
                $event->_id,
                substr($event->data, 0, 64) . '...',
                $event->type
            );
            echo $message . PHP_EOL;
            $i++;
        }

        echo PHP_EOL;
        echo 'I----------------------------------------------------------------I' . PHP_EOL;
        echo 'I MongoDB (Projection)                                           I' . PHP_EOL;
        echo 'I----------------------------------------------------------------I' . PHP_EOL;
        echo PHP_EOL;
        $accounts = $this->client()->selectCollection($this->database(), 'account');
        $account = $accounts->findOne(['_id' => $accountId->toString()]);
        $message = sprintf('Account: %s', json_encode($account->getArrayCopy()));
        echo $message . PHP_EOL;
    }

    /**
     * @return Client
     */
    public function client() : Client
    {
        return $this->client;
    }

    /**
     * @return string
     */
    public function database() : string
    {
        return $this->database;
    }

    /**
     * @return SerializerInterface
     */
    private function serializer() : SerializerInterface
    {
        $builder = SerializerBuilder::create();

        $builder->addMetadataDirs([
            'LejSample\\Subscription\\Domain\\Model' => __DIR__ . '/../../../../../../src/LejSample/Subscription/Infrastructure/Serializer'
        ]);

        $builder->configureHandlers(function(HandlerRegistry $registry) {
            $registry->registerSubscribingHandler(new CurrencySubscribingHandler());
            $registry->registerSubscribingHandler(new MoneySubscribingHandler());
            $registry->registerSubscribingHandler(new UuidSubscribingHandler());
        });

        $builder->setPropertyNamingStrategy(new IdenticalPropertyNamingStrategy());

        return $builder->build();
    }

    private function eventStore() : EventStore
    {
        $projection = new MongoDbAccountProjection($this->client(), $this->database(), 'account');

        $eventDispatcher = new EventStoreEventDispatcher();
        $eventDispatcher->registerEventDispatcher($projection);

//        $eventStore = new InMemoryEventStore($eventDispatcher);

        $eventStore = new MongoDbEventStore(
            $eventDispatcher,
            $this->client(),
            $this->database(),
            $this->serializer()
        );

        return $eventStore;
    }

    private function setUpDatabase()
    {
        // clean up the database
        $this->client()->dropDatabase($this->database());
    }

    /**
     * @return UuidInterface[]
     */
    private function setUpUsers() : array
    {
        $accountantId = Uuid::uuid4();
        $customerId = Uuid::uuid4();
        $systemId = Uuid::uuid4();

        $users = [
            ['_id' => $accountantId->toString(), 'name' => 'Alice', 'email' => 'alice@example.org', 'type' => 'accountant'],
            ['_id' => $customerId->toString(), 'name' => 'Jason', 'email' => 'jason@random.org', 'type' => 'customer'],
            ['_id' => $systemId->toString(), 'type' => 'system']
        ];

        $userCollection = $this->client()->selectCollection($this->database(), 'user');
        $userCollection->insertMany($users);

        return [$accountantId, $customerId, $systemId];
    }

    /**
     * @param UuidInterface $accountId
     */
    private function setUpSubscriptions(UuidInterface $accountId)
    {
        $subscriptions = [
            [
                '_id' => Uuid::uuid4()->toString(),
                'accountId' => $accountId->toString(),
                'term' => 'monthly',
                'product' => ['name' => 'Plan A', 'price' => 10, 'currency' => 'EUR']
            ],
            [
                '_id' => Uuid::uuid4()->toString(),
                'accountId' => $accountId->toString(),
                'term' => 'monthly',
                'product' => ['name' => 'Plan B', 'price' => 20, 'currency' => 'EUR']
            ]
        ];

        $subscriptionCollection = $this->client()->selectCollection($this->database(), 'subscription');
        $subscriptionCollection->insertMany($subscriptions);
    }

    /**
     * @param UuidInterface $accountId
     * @param UuidInterface $customerId
     * @return CreateAccountCommand
     */
    private function createAccountCommand(
        UuidInterface $accountId,
        UuidInterface $customerId
    ) : CreateAccountCommand {
        return new CreateAccountCommand($accountId->toString(), 'EUR', $customerId->toString());
    }

    /**
     * @param UuidInterface $accountId
     * @param UuidInterface $systemId
     * @return BillAccountCommand
     */
    private function billAccountCommand(
        UuidInterface $accountId,
        UuidInterface $systemId
    ) : BillAccountCommand {
        return new BillAccountCommand($accountId->toString(), $systemId->toString());
    }

    /**
     * @param UuidInterface $accountId
     * @param string $amount
     * @param UuidInterface $customerId
     * @return DischargeAccountCommand
     */
    private function dischargeAccountCommand(
        UuidInterface $accountId,
        string $amount,
        UuidInterface $customerId
    ) : DischargeAccountCommand {
        return new DischargeAccountCommand($accountId->toString(), $amount, 'EUR', $customerId->toString());
    }

    /**
     * @param UuidInterface $accountId
     * @param string $reason
     * @param string $amount
     * @param UuidInterface $accountantId
     * @return RefundAccountCommand
     */
    private function refundAccountCommand(
        UuidInterface $accountId,
        string $reason,
        string $amount,
        UuidInterface $accountantId
    ) : RefundAccountCommand {
        return new RefundAccountCommand($accountId->toString(), $reason, $amount, 'EUR', $accountantId->toString());
    }

    /**
     * @param UuidInterface $accountId
     * @param UuidInterface $customerId
     * @return CloseAccountCommand
     */
    private function closeAccountCommand(
        UuidInterface $accountId,
        UuidInterface $customerId
    ) : CloseAccountCommand {
        return new CloseAccountCommand($accountId->toString(), $customerId->toString());
    }
}
