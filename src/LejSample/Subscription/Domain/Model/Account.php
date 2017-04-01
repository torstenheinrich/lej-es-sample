<?php

declare(strict_types=1);

namespace LejSample\Subscription\Domain\Model;

use Assert\Assert;
use Lej\Component\Domain\Model\Entity;
use LejSample\Component\Domain\Model\EventSourcedAggregateRoot;
use Money\Currency;
use Money\Money;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Account extends EventSourcedAggregateRoot implements Entity
{
    /** @var UuidInterface */
    private $id;
    /** @var Money */
    private $balance;
    /** @var string */
    private $status;
    /** @var Customer */
    private $createdBy;
    /** @var User */
    private $updatedBy;

    /**
     * Factory methods
     */

    /**
     * @param UuidInterface $id
     * @param Currency $currency
     * @param Customer $customer
     * @return Account
     */
    public static function create(UuidInterface $id, Currency $currency, Customer $customer) : Account
    {
        $account = self::createEmpty();
        $balance = new Money(0, $currency);

        $event = new AccountCreated($id, $balance, 'active', $customer, new \DateTimeImmutable());
        $account->applyUncommittedEvent($event);

        return $account;
    }

    /**
     * @return Account
     */
    public static function createEmpty() : Account
    {
        return new self();
    }

    /**
     * @return UuidInterface
     */
    public function id() : UuidInterface
    {
        return $this->id;
    }

    /**
     * @return Money
     */
    public function balance() : Money
    {
        return $this->balance;
    }

    /**
     * @return string
     */
    public function status() : string
    {
        return $this->status;
    }

    /**
     * @return Customer
     */
    public function createdBy() : Customer
    {
        return $this->createdBy;
    }

    /**
     * @return User
     */
    public function updatedBy() :? User
    {
        return $this->updatedBy;
    }

    /**
     * Behavioural methods
     */

    /**
     * @param Subscription[] $subscriptions
     * @param System $system
     * @return Invoice
     */
    public function bill(array $subscriptions, System $system) : Invoice
    {
        // check invariants
        $this->ensureAccountActive();
        $this->ensureSubscriptionsOfAccount($subscriptions);

        $lineItems = [];
        foreach ($subscriptions as $subscription) {
            $lineItems[] = InvoiceLineItem::createFromSubscription($subscription);
        }

        // create a new invoice
        $invoice = new Invoice(
            Uuid::uuid4(),
            $this->id(),
            $lineItems,
            $this->balance()->getCurrency()
        );

        // adjust the balance of the account
        $balance = $this->balance()->subtract($invoice->amount());

        $event = new AccountBilled($this->id(), $invoice->id(), $balance, $system, new \DateTimeImmutable());
        $this->applyUncommittedEvent($event);

        return $invoice;
    }

    /**
     * @param Money $amount
     * @param Customer $customer
     * @return Payment
     */
    public function discharge(Money $amount, Customer $customer) : Payment
    {
        // check invariants
        $this->ensureAccountActive();
        $this->ensureBalanceNegative();

        // create a new payment
        $payment = new Payment(Uuid::uuid4(), $this->id(), $amount);

        // adjust the balance of the account
        $balance = $this->balance()->add($amount);

        $event = new AccountDischarged($this->id(), $payment->id(), $balance, $customer, new \DateTimeImmutable());
        $this->applyUncommittedEvent($event);

        return $payment;
    }

    /**
     * @param string $reason
     * @param Money $amount
     * @param Accountant $accountant
     * @return Refund
     */
    public function refund(string $reason, Money $amount, Accountant $accountant) : Refund
    {
        // check invariants
        $this->ensureAccountActive();
        $this->ensureBalanceNegative();

        // create a new refund
        $refund = new Refund(Uuid::uuid4(), $this->id(), $reason, $amount);

        // adjust the balance of the account
        $balance = $this->balance()->add($amount);

        $event = new AccountRefunded($this->id(), $refund->id(), $balance, $accountant, new \DateTimeImmutable());
        $this->applyUncommittedEvent($event);

        return $refund;
    }

    /**
     * @param Customer $customer
     */
    public function close(Customer $customer)
    {
        // check invariants
        $this->ensureAccountActive();
        $this->ensureBalanceZero();

        $event = new AccountClosed($this->id(), 'closed', $customer, new \DateTimeImmutable());
        $this->applyUncommittedEvent($event);
    }

    /**
     * Callback methods
     */

    /**
     * @param AccountCreated $event
     */
    public function onAccountCreated(AccountCreated $event)
    {
        $this->id = $event->accountId();
        $this->balance = $event->balance();
        $this->status = $event->status();
        $this->createdBy = $event->customer();
    }

    /**
     * @param AccountBilled $event
     */
    public function onAccountBilled(AccountBilled $event)
    {
        $this->balance = $event->balance();
        $this->updatedBy = $event->system();
    }

    /**
     * @param AccountDischarged $event
     */
    public function onAccountDischarged(AccountDischarged $event)
    {
        $this->balance = $event->balance();
        $this->updatedBy = $event->customer();
    }

    /**
     * @param AccountRefunded $event
     */
    public function onAccountRefunded(AccountRefunded $event)
    {
        $this->balance = $event->balance();
        $this->updatedBy = $event->accountant();
    }

    /**
     * @param AccountClosed $event
     */
    public function onAccountClosed(AccountClosed $event)
    {
        $this->status = $event->status();
        $this->updatedBy = $event->customer();
    }

    /**
     * {@inheritdoc}
     */
    public function equals(Entity $entity) : bool
    {
        return $entity instanceof Account
            && $entity->id()->equals($this->id());
    }

    private function ensureAccountActive()
    {
        Assert::that($this->status())->eq('active');
    }

    /**
     * @param Subscription[] $subscriptions
     */
    private function ensureSubscriptionsOfAccount(array $subscriptions)
    {
        foreach ($subscriptions as $subscription) {
            Assert::that($subscription->accountId())->eq($this->id());
        }
    }

    private function ensureBalanceNegative()
    {
        Assert::that($this->balance()->isNegative())->true();
    }

    private function ensureBalanceZero()
    {
        Assert::that($this->balance()->isZero())->true();
    }
}
