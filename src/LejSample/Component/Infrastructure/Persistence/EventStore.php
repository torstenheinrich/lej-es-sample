<?php

declare(strict_types=1);

namespace LejSample\Component\Infrastructure\Persistence;

use Lej\Component\Domain\Model\DomainEvent;

interface EventStore
{
    /**
     * @param string $id
     * @return DomainEvent[]
     */
    public function load(string $id) : array;

    /**
     * @param string $id
     * @param DomainEvent[] $events
     */
    public function append(string $id, array $events);
}
