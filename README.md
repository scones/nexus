[![Build Status](https://travis-ci.com/scones/nexus.svg?branch=master)](https://travis-ci.com/scones/nexus)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/scones/nexus/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/scones/nexus/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/scones/nexus/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/scones/nexus/?branch=master)

# Nexus
psr-14 event dispatcher implementation

(honoring psr-12)

## Install

In most cases it should suffice to just install it via composer.

`composer require scones/nexus "*@stable"`

## Usage

These exmaples are taken from the tests, so there is of course a bit of redundancy.

### Messages

The usage is quite basic.
- You define a Message class.
- You provide a callable listener with one argument, which has to match the Message Type it should listen on.
- You call the notfier with an instance of the Message you wish to propagate

```php

use Psr\EventDispatcher\MessageInterface;
use Nexus\StandardListenerProvider;
use Nexus\SynchronousNotifier;

class SomeMessage implements MessageInterface
{
}

class SomeOtherMessage implements MessageInterface
{
}

$provider = new StandardListenerProvider();
$notifier = new SynchronousNotifier($provider);

$provider->addListener(function (SomeMessage $someMessage) {
    echo "some cool message reaction code goes here\n";
});
$provider->addListener(function (SomeMessage $someMessage) {
    echo "this message seems worth logging!\n";
});
$provider->addListener(function (SomeOtherMessage $someOtherMessage) {
    echo "oh, the other message was dispatched!\n";
});

$testMessage = new SomeMessage();
$notifier->notify($testMessage);

```

which would of course result in:

```
some cool message reaction code goes here
this message seems worth logging!
```

### Tasks

Again the usage is quite simplistic.
- You define a Task class.
- You provide a callable listener with one argument, which has to match the Task Type it should listen on.
- You call the processor with an instance of the Task you wish to propagate
- After all Tasks have processed, the resulting modified task instance is returned.


```php

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

```

which would of course result in:

```
some task working!
some task working again!
task result was: 3
```
