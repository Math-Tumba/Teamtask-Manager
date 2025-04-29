<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\KernelEvents;

final class ExceptionListener
{
    /**
     * API Exception listener
     * 
     * Intercepts exceptions and set both status and message based on exception data
     * if it comes from a HTTP one. It sets a default message if not.
     * @param ExceptionEvent $event
     */
    #[AsEventListener(event: KernelEvents::EXCEPTION)]
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/api/')) {
            return;
        }
    
        if ($exception instanceof HttpException) {
            $data['status'] = $exception->getStatusCode();
            $data['message'] = $exception->getMessage();
        } else {
            $data['status'] = 500;
            $data['message'] = 'Une erreur est survenue. Veuillez contacter le support si elle persiste.';
        }

        $event->setResponse(new JsonResponse($data));
    }
}