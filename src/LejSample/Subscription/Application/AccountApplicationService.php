<?php

declare(strict_types=1);

namespace LejSample\Subscription\Application;

use LejSample\Subscription\Domain\Model\Account;
use LejSample\Subscription\Domain\Model\AccountRepository;
use LejSample\Subscription\Domain\Model\SubscriptionRepository;
use LejSample\Subscription\Domain\Model\UserRepository;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;

class AccountApplicationService
{
    /** @var AccountRepository */
    private $accountRepository;
    /** @var SubscriptionRepository */
    private $subscriptionRepository;
    /** @var UserRepository */
    private $userRepository;

    /**
     * @param AccountRepository $accountRepository
     * @param SubscriptionRepository $subscriptionRepository
     * @param UserRepository $userRepository
     */
    public function __construct(
        AccountRepository $accountRepository,
        SubscriptionRepository $subscriptionRepository,
        UserRepository $userRepository
    ) {
        $this->accountRepository = $accountRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param CreateAccountCommand $command
     */
    public function createAccount(CreateAccountCommand $command)
    {
        $customer = $this->userRepository->customerOfId(Uuid::fromString($command->customerId()));

        $account = Account::create(
            Uuid::fromString($command->accountId()),
            new Currency($command->currency()),
            $customer
        );

        $this->accountRepository->save($account);
    }

    /**
     * @param BillAccountCommand $command
     */
    public function billAccount(BillAccountCommand $command)
    {
        $accountId = Uuid::fromString($command->accountId());
        $subscriptions = $this->subscriptionRepository->subscriptionsOfAccount($accountId);
        $system = $this->userRepository->systemOfId(Uuid::fromString($command->systemId()));

        $account = $this->accountRepository->accountOfId($accountId);
        $account->bill($subscriptions, $system);

        $this->accountRepository->save($account);
    }

    /**
     * @param DischargeAccountCommand $command
     */
    public function dischargeAccount(DischargeAccountCommand $command)
    {
        $accountId = Uuid::fromString($command->accountId());
        $amount = new Money($command->amount(), new Currency($command->currency()));
        $customer = $this->userRepository->customerOfId(Uuid::fromString($command->customerId()));

        $account = $this->accountRepository->accountOfId($accountId);
        $account->discharge($amount, $customer);

        $this->accountRepository->save($account);
    }

    /**
     * @param RefundAccountCommand $command
     */
    public function refundAccount(RefundAccountCommand $command)
    {
        $accountId = Uuid::fromString($command->accountId());
        $amount = new Money($command->amount(), new Currency($command->currency()));
        $accountant = $this->userRepository->accountantOfId(Uuid::fromString($command->accountantId()));

        $account = $this->accountRepository->accountOfId($accountId);
        $account->refund($command->reason(), $amount, $accountant);

        $this->accountRepository->save($account);
    }

    /**
     * @param CloseAccountCommand $command
     */
    public function closeAccount(CloseAccountCommand $command)
    {
        $accountId = Uuid::fromString($command->accountId());
        $customer = $this->userRepository->customerOfId(Uuid::fromString($command->customerId()));

        $account = $this->accountRepository->accountOfId($accountId);
        $account->close($customer);

        $this->accountRepository->save($account);
    }
}
