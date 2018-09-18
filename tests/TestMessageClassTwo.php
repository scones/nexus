<?php

namespace Nexus\Tests;

use Psr\EventDispatcher\MessageInterface;

class TestMessageClassTwo implements MessageInterface
{
    private $accessCounter = 0;

    public function checkCounter()
    {
        $this->accessCounter++;
        if ($this->accessCounter > 1) {
            throw new \RuntimeException();
        }
    }
}
