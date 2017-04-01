<?php

declare(strict_types=1);

namespace LejSample\Component\Infrastructure\Serializer;

use JMS\Serializer\Context;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\VisitorInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class UuidSubscribingHandler implements SubscribingHandlerInterface
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
                'type' => 'Uuid',
                'format' => $format,
                'method' => 'deserializeUuid',
            ];

            $methods[] = [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'type' => 'Uuid',
                'format' => $format,
                'method' => 'serializeUuid',
            ];
        }

        return $methods;
    }

    /**
     * @param VisitorInterface $visitor
     * @param string $data
     * @param array $type
     * @param Context $context
     * @return UuidInterface
     */
    public function deserializeUuid(VisitorInterface $visitor, $data, array $type, Context $context)
    {
        return Uuid::fromString($visitor->visitString($data, $type, $context));
    }

    /**
     * @param VisitorInterface $visitor
     * @param UuidInterface $uuid
     * @param array $type
     * @param Context $context
     * @return string
     */
    public function serializeUuid(VisitorInterface $visitor, UuidInterface $uuid, array $type, Context $context)
    {
        return $visitor->visitString($uuid->toString(), $type, $context);
    }
}
