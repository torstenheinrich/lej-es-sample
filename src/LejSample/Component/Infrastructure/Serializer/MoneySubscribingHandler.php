<?php

declare(strict_types=1);

namespace LejSample\Component\Infrastructure\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;
use Money\Currency;
use Money\Money;

class MoneySubscribingHandler implements SubscribingHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribingMethods()
    {
        $methods = [];
        $formats = ['json'];

        foreach ($formats as $format) {
            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_DESERIALIZATION,
                'type' => 'Money',
                'format' => $format,
                'method' => 'deserializeMoney',
            ];

            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'Money',
                'format' => $format,
                'method' => 'serializeMoney',
            ];
        }

        return $methods;
    }

    /**
     * @param VisitorInterface $visitor
     * @param array $data
     * @param array $type
     * @param Context $context
     * @return Money
     */
    public function deserializeMoney(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        $amount = $visitor->visitString($data['amount'], $type, $context);
        $currencyCode = $visitor->visitString($data['currencyCode'], $type, $context);

        return new Money($amount, new Currency($currencyCode));
    }

    /**
     * @param VisitorInterface $visitor
     * @param Money $money
     * @param array $type
     * @param Context $context
     * @return array
     */
    public function serializeMoney(VisitorInterface $visitor, Money $money, array $type, Context $context)
    {
        $data = [
            'amount' => $money->getAmount(),
            'currencyCode' => $money->getCurrency()->getCode()
        ];

        return $visitor->visitArray($data, $type, $context);
    }
}
