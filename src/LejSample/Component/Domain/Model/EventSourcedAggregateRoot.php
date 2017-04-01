<?php

declare(strict_types=1);

namespace LejSample\Component\Domain\Model;

use Lej\Component\Domain\Model\DomainEvent;

abstract class EventSourcedAggregateRoot
{
    /** @var DomainEvent[] */
    protected $uncommittedEvents = [];

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
     * @param DomainEvent $event
     */
    protected function applyEvent(DomainEvent $event)
    {
        $this->{'on' . substr(strrchr(get_class($event), '\\'), 1)}($event);
    }
}
