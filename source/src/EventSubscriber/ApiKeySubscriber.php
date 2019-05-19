<?php

namespace App\EventSubscriber;

use App\Repository\ApiKeyRepository;
use App\Response\ErrorMessageService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiKeySubscriber implements EventSubscriberInterface {
    /** @var ApiKeyRepository */
    private $apiKeyRepository;

    public function __construct(ApiKeyRepository $apiKeyRepository) {
        $this->apiKeyRepository = $apiKeyRepository;
    }

    public function onKernelController(FilterControllerEvent $event) {
        if (strpos($event->getRequest()->getPathInfo(), '/api') !== 0) {
            return;
        }
        $requestApiKey = $event->getRequest()->query->get('api_key');
        if (is_null($requestApiKey)) {
            throw new BadRequestHttpException(ErrorMessageService::apiKeyRequired());
        }
        $matchedApiKey = $this->apiKeyRepository->getOneByApiKey($requestApiKey);
        if (is_null($matchedApiKey)) {
            throw new BadRequestHttpException(ErrorMessageService::apiKeyInvalid());
        }
    }

    public static function getSubscribedEvents() {
        return array(
            KernelEvents::CONTROLLER => 'onKernelController',
        );
    }
}