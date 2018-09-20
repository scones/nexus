<?php

declare(strict_types=1);

namespace Nexus\Tests;

use Psr\EventDispatcher\StoppableTaskInterface;

class TestTaskClassTwo implements StoppableTaskInterface
{
    private $accessCounter = 0;
    private $isPropagationStopped = false;

    public function increaseCounter(): void
    {
        $this->accessCounter++;
        $this->accessCounter++;
    }

    public function getCounter(): int
    {
        return $this->accessCounter;
    }

    public function stopPropagation(): StoppableTaskInterface
    {
        $this->isPropagationStopped = true;
        return $this;
    }

    public function isPropagationStopped(): bool
    {
        return $this->isPropagationStopped;
    }
}
