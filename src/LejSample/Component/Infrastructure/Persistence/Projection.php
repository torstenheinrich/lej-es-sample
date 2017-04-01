<?php

declare(strict_types=1);

namespace LejSample\Component\Infrastructure\Persistence;

use Lej\Component\Domain\Model\DomainEvent;

abstract class Projection
{
    /**
     * @param DomainEvent $event
     */
    protected function projectEvent(DomainEvent $event)
    {
        $this->{'on' . substr(strrchr(get_class($event), '\\'), 1)}($event);
    }
}
