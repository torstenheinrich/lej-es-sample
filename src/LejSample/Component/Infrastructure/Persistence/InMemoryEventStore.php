<?php

declare(strict_types=1);

namespace LejSample\Component\Infrastructure\Persistence;

use Lej\Component\Domain\Model\DomainEvent;

class InMemoryEventStore implements EventStore
{
    /** @var EventDispatcher */
    private $eventDispatcher;
    /** @var DomainEvent[][] */
    private $events = [];

    /**
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

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
        foreach ($events as $event) {
            $this->events[$id][] = $event;
        }

        foreach ($events as $event) {
            $this->eventDispatcher->dispatch($event);
        }
    }
}
