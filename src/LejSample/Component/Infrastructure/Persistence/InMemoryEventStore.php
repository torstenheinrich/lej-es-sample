<?php

declare(strict_types=1);

namespace LejSample\Component\Infrastructure\Persistence;

use Lej\Component\Domain\Model\DomainEvent;

class InMemoryEventStore implements EventStore
{
    /** @var DomainEvent[][] */
    private $events = [];

    /**
     * {@inheritdoc}
     */
    public function load(string $id) : array
    {
        return isset($this->events[$id]) ? $this->events[$id] : [];
    }

    /**
     * {@inheritdoc}
     */
    public function append(string $id, array $events)
    {
        if (!isset($this->events[$id])) {
            $this->events[$id] = [];
        }

        $this->events[$id] += $events;
    }
}
