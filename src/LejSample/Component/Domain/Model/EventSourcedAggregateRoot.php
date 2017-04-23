<?php

declare(strict_types=1);

namespace LejSample\Component\Domain\Model;

use Lej\Component\Domain\Model\DomainEvent;

abstract class EventSourcedAggregateRoot
{
    /** @var \Closure[] */
    protected $eventHandlers;
    /** @var DomainEvent[] */
    protected $uncommittedEvents;

    public function __construct()
    {
        $this->eventHandlers = $this->createEventHandlers();
        $this->uncommittedEvents = [];
    }

    /**
     * @return DomainEvent[]
     */
    public function uncommittedEvents() : array
    {
        return $this->uncommittedEvents;
    }

    /**
     * @param DomainEvent[] $events
     */
    public function applyCommittedEvents(array $events)
    {
        foreach ($events as $event) {
            $this->applyEvent($event);
        }
    }

    /**
     * @param DomainEvent $event
     */
    public function applyUncommittedEvent(DomainEvent $event)
    {
        $this->applyEvent($event);
        $this->uncommittedEvents[] = $event;
    }

    public function resetUncommittedEvents()
    {
        $this->uncommittedEvents = [];
    }

    /**
     * @return \Closure[]
     */
    abstract protected function createEventHandlers() : array;

    /**
     * @param DomainEvent $event
     * @throws \Exception
     */
    private function applyEvent(DomainEvent $event)
    {
        $eventClass = get_class($event);
        if (!isset($this->eventHandlers[$eventClass])) {
            throw new \Exception(sprintf('No event handler found for type %s.', $eventClass));
        }

        $this->eventHandlers[$eventClass]->call($this, $event);
    }
}
