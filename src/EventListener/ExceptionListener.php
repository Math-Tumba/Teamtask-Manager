<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Validator\Exception\ValidationFailedException;

final class ExceptionListener
{
    /**
     * API Exception listener
     * 
     * Intercept exceptions and set both status and message based on exception's data
     * if it is a HTTP exception or a failed validation. It sets a default message if not.
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

        if ($exception instanceof ValidationFailedException) {
            $violations = $exception->getViolations();
            $errors = [];

            foreach ($violations as $violation) {
                array_push($errors, [
                    'field' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage()
                ]);
            }

            $data['status'] = JsonResponse::HTTP_BAD_REQUEST;
            $data['message'] = $errors;
        }
        elseif ($exception instanceof HttpException) {
            $data['status'] = $exception->getStatusCode();
            $data['message'] = $exception->getMessage();
        } else {
            $data['status'] = JsonResponse::HTTP_INTERNAL_SERVER_ERROR;
            $data['message'] = $exception->getMessage(); // DEBUG
            // $data['message'] = 'Une erreur est survenue. Veuillez contacter le support si elle persiste.';
        }

        $event->setResponse(new JsonResponse($data, $data['status']));
    }
}