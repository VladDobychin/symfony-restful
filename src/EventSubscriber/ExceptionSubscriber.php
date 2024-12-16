<?php

namespace App\EventSubscriber;

use App\Exception\ValidationFailedException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Handle our custom validation exception
        if ($exception instanceof ValidationFailedException) {
            $response = new JsonResponse(
                ['errors' => $exception->getErrors()],
                $exception->getStatusCode()
            );

            $event->setResponse($response);
        }
    }
}
