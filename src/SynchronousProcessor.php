<?php

declare(strict_types=1);

namespace Nexus;

use Nexus\Exceptions\TaskResultViolation;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableTaskInterface;
use Psr\EventDispatcher\TaskInterface;
use Psr\EventDispatcher\TaskProcessorInterface;

class SynchronousProcessor implements TaskProcessorInterface
{
    private $listenerProvider;

    public function __construct(ListenerProviderInterface $listenerProvider)
    {
        $this->listenerProvider = $listenerProvider;
    }

    public function process(TaskInterface $task): TaskInterface
    {
        foreach ($this->listenerProvider->getListenersForEvent($task) as $listener) {
            $task = call_user_func($listener, $task);
            if (!is_object($task) || !($task instanceof TaskInterface)) {
                throw new TaskResultViolation();
            }
            if ($task instanceof StoppableTaskInterface && $task->isStopped()) {
                break;
            }
        }
        return $task;
    }
}
