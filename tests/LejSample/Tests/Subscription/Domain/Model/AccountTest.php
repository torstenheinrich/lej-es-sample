<?php

declare(strict_types=1);

namespace LejSample\Tests\Subscription\Domain\Model;

use LejSample\Subscription\Domain\Model\Account;
use LejSample\Subscription\Domain\Model\Accountant;
use LejSample\Subscription\Domain\Model\Customer;
use LejSample\Subscription\Domain\Model\Product;
use LejSample\Subscription\Domain\Model\Subscription;
use LejSample\Subscription\Domain\Model\System;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class AccountTest extends TestCase
{
    public function testCreateCreatesAccountWithBalanceOfZero()
    {
        $customer = new Customer(Uuid::uuid4(), 'test', 'test@example.org');
        $account = Account::create(Uuid::uuid4(), new Currency('EUR'), $customer);

        $this->assertEquals(0, $account->balance()->getAmount());
    }

    public function testCreateCreatesAccountWithActiveStatus()
    {
        $customer = new Customer(Uuid::uuid4(), 'test', 'test@example.org');
        $account = Account::create(Uuid::uuid4(), new Currency('EUR'), $customer);

        $this->assertEquals('active', $account->status());
    }

    public function testBillSubtractsProductPriceFromBalance()
    {
        $id = Uuid::uuid4();
        $customer = new Customer(Uuid::uuid4(), 'test', 'test@example.org');
        $account = Account::create($id, new Currency('EUR'), $customer);

        $subscriptions = [
            new Subscription(Uuid::uuid4(), $id, 'monthly', new Product('Plan A', new Money(10, new Currency('EUR')))),
            new Subscription(Uuid::uuid4(), $id, 'monthly', new Product('Plan B', new Money(20, new Currency('EUR'))))
        ];

        $account->bill($subscriptions, new System(Uuid::uuid4()));

        $this->assertEquals(-30, $account->balance()->getAmount());
    }

    public function testDischargeAddsAmountToBalance()
    {
        $id = Uuid::uuid4();
        $customer = new Customer(Uuid::uuid4(), 'test', 'test@example.org');
        $account = Account::create($id, new Currency('EUR'), $customer);

        $subscriptions = [
            new Subscription(Uuid::uuid4(), $id, 'monthly', new Product('Plan A', new Money(10, new Currency('EUR')))),
            new Subscription(Uuid::uuid4(), $id, 'monthly', new Product('Plan B', new Money(20, new Currency('EUR'))))
        ];

        $account->bill($subscriptions, new System(Uuid::uuid4()));
        $account->discharge(new Money(30, new Currency('EUR')), $customer);

        $this->assertEquals(0, $account->balance()->getAmount());
    }

    public function testRefundAddsAmountToBalance()
    {
        $id = Uuid::uuid4();
        $accountant = new Accountant(Uuid::uuid4(), 'abc', 'def@example.org');
        $customer = new Customer(Uuid::uuid4(), 'test', 'test@example.org');
        $account = Account::create($id, new Currency('EUR'), $customer);

        $subscriptions = [
            new Subscription(Uuid::uuid4(), $id, 'monthly', new Product('Plan A', new Money(10, new Currency('EUR')))),
            new Subscription(Uuid::uuid4(), $id, 'monthly', new Product('Plan B', new Money(20, new Currency('EUR'))))
        ];

        $account->bill($subscriptions, new System(Uuid::uuid4()));
        $account->bill($subscriptions, new System(Uuid::uuid4()));
        $account->refund('double charge', new Money(30, new Currency('EUR')), $accountant);

        $this->assertEquals(-30, $account->balance()->getAmount());
    }

    public function testCloseClosesAccountWithClosedStatus()
    {
        $id = Uuid::uuid4();
        $customer = new Customer(Uuid::uuid4(), 'test', 'test@example.org');
        $account = Account::create($id, new Currency('EUR'), $customer);

        $account->close($customer);

        $this->assertEquals('closed', $account->status());
    }
}
