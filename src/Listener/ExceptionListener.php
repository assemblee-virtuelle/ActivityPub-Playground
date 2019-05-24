<?php

namespace App\Listener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ExceptionListener
{
    public function __construct()
    {

    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
//        // Only for ApiBundle
//        if (!$this->requestTargetsBundle($event->getRequest(), ApiBundle::class)) {
//            return $event->getResponse();
//        }
//
//        $exception = $event->getException();
//
//        $response = new JsonResponse();
//
//        if ($exception instanceof ApiException) { // One of our own exceptions
//            $response->setStatusCode($exception->getStatusCode());
//
//            $data = ['success' => false];
//
//            if (!empty($exception->getErrors())) {
//                $data['errors'] = array_map(function (ApiError $error) {
//                    return $error->asArray();
//                }, $exception->getErrors());
//            }
//
//            $response->setData($data);
//        } elseif ($exception instanceof AccessDeniedException) { // Common SF exception
//            $response->setStatusCode($exception->getCode());
//            $response->setData([
//                'success' => false,
//            ]);
//        } elseif ($exception instanceof HttpExceptionInterface) { // HTTP exception
//            $response->setStatusCode($exception->getStatusCode());
//            $response->headers->replace($exception->getHeaders());
//
//            $response->setData([
//                'success' => false,
//                'errors' => [],
//            ]);
//        } else { // Unhandled exception
//            $this->logger->error($exception->getMessage(), [$exception->getTrace()]);
//            // uncomment the below for exception debugging in phpunit.
//            // error_log(json_encode($exception->getTrace()));
//            // Display pretty Symfony errors on dev environment
//            if ($this->env === 'dev') {
//                return $event->getResponse();
//            }
//
//            $response
//                ->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR)
//                ->setData([
//                    'success' => false,
//                    'unhandled' => true,
//                    'code' => $exception->getCode(),
//                ]);
//        }
//
//        $event->setResponse($response);
//
//        return $response;
    }
}
