<?php

declare(strict_types=1);

namespace Nexus;

use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\MessageInterface;
use Psr\EventDispatcher\MessageNotifierInterface;

class SynchronousNotifier implements MessageNotifierInterface
{
    private $listenerProvider;

    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;
    }

    public function notify(MessageInterface $event): void
    {
        foreach ($this->listenerProvider->getListenersForEvent($event) as $listener) {
            call_user_func($listener, clone$event);
        }
    }
}
