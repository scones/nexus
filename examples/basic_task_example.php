<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nexus\StandardListenerProvider;
use Nexus\SynchronousProcessor;
use Psr\EventDispatcher\TaskInterface;

class SomeTask implements TaskInterface
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

class SomeOtherTask implements TaskInterface
{
}

$provider = new StandardListenerProvider();
$processor = new SynchronousProcessor($provider);

$provider->addListener(function (SomeTask $someTask) {
    echo "some task working!\n";
    $someTask->increaseCounter();
    return $someTask;
});
$provider->addListener(function (SomeTask $someTask) {
    echo "some task working again!\n";
    $someTask->increaseCounter();
    $someTask->increaseCounter();
    return $someTask;
});
$provider->addListener(function (SomeOtherTask $someOtherTask) {
    echo "the other Task!";
    return $someOtherTask;
});

$testTask = new SomeTask();
$testTaskResult = $processor->process($testTask);

echo "task result was: {$testTaskResult->getCounter()}\n";
