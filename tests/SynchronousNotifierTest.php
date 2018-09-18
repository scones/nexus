<?php

declare(strict_types=1);

namespace Nexus\Tests;

use Nexus\StandardListenerProvider;

use Nexus\SynchronousNotifier;
use PHPUnit\Framework\TestCase;

class SynchronousNotifierTest extends TestCase
{
    public function setUp()
    {
        $this->provider = new StandardListenerProvider();
        $this->notifier = new SynchronousNotifier($this->provider);

        $this->listenerOneCount = 0;
        $this->listenerTwoCount = 0;
        $this->listenerThreeCount = 0;
        $this->listenerFourCount = 0;

        $this->listenerOneEventTypeOne = function (TestMessageClassOne $someMessage) {
            $this->listenerOneCount++;
        };
        $this->listenerTwoEventTypeOne = function (TestMessageClassOne $someMessage) {
            $this->listenerTwoCount++;
        };
        $this->listenerThreeEventTypeTwo = function (TestTaskClassOne $someTask) {
            $this->listenerThreeCount++;
        };
        $this->listenerFourEventTypeThree = function (TestTaskClassTwo $someOtherTask) {
            $this->listenerFourCount++;
        };
        $this->listenerFiveEventTypeTwo = function (TestMessageClassTwo $someMessage) {
        };
        $this->listenerSixEventTypeTwo = function (TestMessageClassTwo $someMessage) {
        };
    }

    public function tearDown()
    {
        unset($this->notifier);
        unset($this->provider);
    }

    public function testNotifierShouldNotifyMatchingListeners()
    {
        $this->provider->addListener($this->listenerOneEventTypeOne);
        $this->provider->addListener($this->listenerTwoEventTypeOne);

        $testEvent = new TestMessageClassOne();
        $this->notifier->notify($testEvent);

        $this->assertEquals(1, $this->listenerOneCount);
        $this->assertEquals(1, $this->listenerTwoCount);
        $this->assertEquals(0, $this->listenerThreeCount);
        $this->assertEquals(0, $this->listenerFourCount);
    }

    public function testNotifierShouldProvideImmutableMessages()
    {
        $this->provider->addListener($this->listenerOneEventTypeOne);
        $this->provider->addListener($this->listenerTwoEventTypeOne);
        $this->provider->addListener($this->listenerFiveEventTypeTwo);
        $this->provider->addListener($this->listenerSixEventTypeTwo);

        $testEvent = new TestMessageClassTwo();
        $this->notifier->notify($testEvent);

        $this->addToAssertionCount(1);
    }
}
