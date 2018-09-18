<?php

declare(strict_types=1);

namespace Nexus;

use Psr\EventDispatcher\EventInterface;
use Psr\EventDispatcher\ListenerProviderInterface;

class StandardListenerProvider implements ListenerProviderInterface
{
    private $listeners = [];

    public function addListener(callable $listener): void
    {
        $reflection = new \ReflectionFunction($listener);
        $eventClass = $reflection->getParameters()[0]->getType()->getName();
        $this->listeners[$eventClass][] = $listener;
    }

    public function getListenersForEvent(EventInterface $event): iterable
    {
        $eventClass = get_class($event);
        if (empty($this->listeners[$eventClass]) || !is_array($this->listeners[$eventClass])) {
            $this->listeners[$eventClass] = [];
        }
        foreach ($this->listeners[$eventClass] as $listener) {
            yield $listener;
        }
    }
}
