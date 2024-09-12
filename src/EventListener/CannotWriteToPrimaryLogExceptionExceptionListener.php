<?php

declare(strict_types = 1);

namespace Surfnet\StepupBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CannotWriteToPrimaryLogExceptionExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => [
                ['onKernelException', 0],
            ]
        ];
    }

    /**
     * Displays an error message to the user/client and attempts to mail the administrator to inform him/her about the
     * final throes of our application.
     *
     */
    public function onKernelException(ExceptionEvent $event): void
    {
    }
}
