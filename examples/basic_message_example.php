<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Nexus\StandardListenerProvider;
use Nexus\SynchronousNotifier;
use Psr\EventDispatcher\MessageInterface;

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
