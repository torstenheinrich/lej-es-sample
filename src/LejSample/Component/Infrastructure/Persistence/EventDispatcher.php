<?php

declare(strict_types=1);

namespace LejSample\Component\Infrastructure\Persistence;

use Lej\Component\Domain\Model\DomainEvent;

interface EventDispatcher
{
    /**
     * @param DomainEvent $event
     */
    public function dispatch(DomainEvent $event);

    /**
     * @param EventDispatcher $dispatcher
     */
    public function registerEventDispatcher(EventDispatcher $dispatcher);

    /**
     * @param DomainEvent $event
     * @return boolean
     */
    public function understands(DomainEvent $event) : bool;
}
