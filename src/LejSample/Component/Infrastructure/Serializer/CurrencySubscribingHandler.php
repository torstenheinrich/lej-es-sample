<?php

declare(strict_types=1);

namespace LejSample\Component\Infrastructure\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;
use Money\Currency;

class CurrencySubscribingHandler implements SubscribingHandlerInterface
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
                'type' => 'Currency',
                'format' => $format,
                'method' => 'deserializeCurrency',
            ];

            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'Currency',
                'format' => $format,
                'method' => 'serializeCurrency',
            ];
        }

        return $methods;
    }

    /**
     * @param VisitorInterface $visitor
     * @param array $data
     * @param array $type
     * @param Context $context
     * @return Currency
     */
    public function deserializeCurrency(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        return new Currency($visitor->visitString($data['code'], $type, $context));
    }

    /**
     * @param VisitorInterface $visitor
     * @param Currency $currency
     * @param array $type
     * @param Context $context
     * @return array
     */
    public function serializeCurrency(VisitorInterface $visitor, Currency $currency, array $type, Context $context)
    {
        return $visitor->visitArray(['code' => $currency->getCode()], $type, $context);
    }
}
