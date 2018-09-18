<?php

declare(strict_types=1);

namespace Nexus\Tests;

use Nexus\Exceptions\TaskResultViolation;
use Nexus\StandardListenerProvider;
use Nexus\SynchronousProcessor;
use PHPUnit\Framework\TestCase;

class SynchronousProcessorTest extends TestCase
{
    public function setUp()
    {
        $this->provider = new StandardListenerProvider();
        $this->processor = new SynchronousProcessor($this->provider);

        $this->listenerOneEventTypeOne = function (TestMessageClassOne $someMessage) {
        };
        $this->listenerTwoEventTypeOne = function (TestMessageClassOne $someMessage) {
        };
        $this->listenerThreeEventTypeTwo = function (TestTaskClassOne $someTask) {
            $someTask->increaseCounter();
            return $someTask;
        };
        $this->listenerFourEventTypeTwo = function (TestTaskClassOne $someTask) {
            $someTask->increaseCounter();
            $someTask->increaseCounter();
            return $someTask;
        };
        $this->listenerFiveEventTypeThree = function (TestTaskClassTwo $someOtherTask) {
            $someOtherTask->increaseCounter();
            $someOtherTask->stopPropagation();
            return $someOtherTask;
        };
        $this->listenerSixEventTypeThree = function (TestTaskClassTwo $someOtherTask) {
            $someOtherTask->increaseCounter();
            $someOtherTask->increaseCounter();
            return $someOtherTask;
        };
        $this->listenerSevenEventTypeThree = function (TestTaskClassTwo $someOtherTask) {
            $someOtherTask->increaseCounter();
            return 123;
        };
    }

    public function tearDown()
    {
        unset($this->processor);
        unset($this->provider);
    }

    public function testProcessorShouldProcessMatchingTasks()
    {
        $this->provider->addListener($this->listenerThreeEventTypeTwo);
        $this->provider->addListener($this->listenerFourEventTypeTwo);

        $testTask = new TestTaskClassOne();
        $testTaskResult = $this->processor->process($testTask);

        $this->assertEquals(TestTaskClassOne::class, get_class($testTask));
        $this->assertEquals(3, $testTaskResult->getCounter());
    }

    public function testProcessorShouldStopProcessingStoppableTasks()
    {
        $this->provider->addListener($this->listenerFiveEventTypeThree);
        $this->provider->addListener($this->listenerSixEventTypeThree);
        $this->provider->addListener($this->listenerSevenEventTypeThree);

        $testTask = new TestTaskClassTwo();
        $testTaskResult = $this->processor->process($testTask);

        $this->assertEquals(TestTaskClassTwo::class, get_class($testTask));
        $this->assertEquals(2, $testTaskResult->getCounter());
    }

    public function testProcessorShouldThrowExceptionWhenReturnTypeIsInvalid()
    {
        $this->expectException(TaskResultViolation::class);

        $this->provider->addListener($this->listenerSixEventTypeThree);
        $this->provider->addListener($this->listenerSevenEventTypeThree);

        $testTask = new TestTaskClassTwo();
        $testTaskResult = $this->processor->process($testTask);

        $this->assertEquals(TestTaskClassTwo::class, get_class($testTask));
        $this->assertEquals(2, $testTaskResult->getCounter());
    }
}
