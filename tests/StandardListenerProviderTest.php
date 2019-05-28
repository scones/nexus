<?php

declare(strict_types=1);

namespace Nexus\Tests;

use Nexus\StandardListenerProvider;
use PHPUnit\Framework\TestCase;

class StandardListenerProviderTest extends TestCase
{
    public function setUp(): void
    {
        $this->provider = new StandardListenerProvider();
        $this->listenerOneEventTypeOne = function (TestMessageClassOne $someMessage) {
        };
        $this->listenerTwoEventTypeOne = function (TestMessageClassOne $someMessage) {
        };
        $this->listenerThreeEventTypeTwo = function (TestTaskClassOne $someTask) {
        };
        $this->listenerFourEventTypeThree = function (TestTaskClassTwo $someOtherTask) {
        };
    }

    public function tearDown(): void
    {
        unset($this->provider);
    }

    public function testProviderShouldProvideAttachedListeners()
    {
        $this->provider->addListener($this->listenerOneEventTypeOne);
        $this->provider->addListener($this->listenerTwoEventTypeOne);

        $count = 0;
        $testEvent = new TestMessageClassOne();
        foreach ($this->provider->getListenersForEvent($testEvent) as $listener) {
            $this->assertEquals('Closure', get_class($listener));
            ++$count;
        }
        $this->assertEquals(2, $count);
    }

    public function testProviderShouldProvideNothingForUnknownEvents()
    {
        $count = 0;
        $testEvent = new TestMessageClassOne();
        foreach ($this->provider->getListenersForEvent($testEvent) as $listener) {
            $this->assertEquals('Closure', get_class($listener));
            ++$count;
        }
        $this->assertEquals(0, $count);
    }
}
