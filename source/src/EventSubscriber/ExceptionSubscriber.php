<?php

namespace App\EventSubscriber;

use App\Service\ResponseService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /** @var ResponseService */
    private $responseService;

    public function __construct(ResponseService $responseService) {
        $this->responseService = $responseService;
    }

    public function onKernelException(GetResponseForExceptionEvent $event){
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }
        $exception = $event->getException();
        if ($exception instanceof HttpException) {
            $response = $this->responseService->getErrorResponse([$exception->getMessage()], $exception->getStatusCode());
        } else if ($_ENV['APP_ENV'] === 'dev') {
            $response = $this->responseService->getErrorResponse(['message' => $exception->getMessage(), 'trace' => $exception->getTrace()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } else {
            $response = $this->responseService->getErrorResponse(['Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
        $response->send();
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::EXCEPTION => 'onKernelException',
        );
    }
}