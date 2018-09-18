<?php

declare(strict_types=1);

namespace Nexus\Tests;

use Psr\EventDispatcher\TaskInterface;

class TestTaskClassOne implements TaskInterface
{
    private $accessCounter = 0;

    public function increaseCounter(): void
    {
        $this->accessCounter++;
    }

    public function getCounter(): int
    {
        return $this->accessCounter;
    }
}
