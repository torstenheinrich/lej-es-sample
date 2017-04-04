<?php

declare(strict_types=1);

namespace LejSample\Component\Infrastructure\Persistence;

use Lej\Component\Domain\Model\DomainEvent;

class EventStoreEventDispatcher implements EventDispatcher
{
    /** @var EventDispatcher[] */
    private $eventDispatchers = [];

    /**
     * {@inheritdoc}
     */
    public function dispatch(DomainEvent $event)
    {
        foreach ($this->eventDispatchers as $dispatcher) {
            $dispatcher->dispatch($event);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function registerEventDispatcher(EventDispatcher $dispatcher)
    {
        $this->eventDispatchers[] = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function understands(DomainEvent $event) : bool
    {
        return true;
    }
}
