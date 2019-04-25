<?php

namespace App\Controller;

use App\Service\PushService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PushController extends AbstractController
{
    protected function parseRequestBodyAsJson(Request $request): ParameterBag
    {
        $content = $request->getContent();

        $params = !empty($content)
            ? json_decode($content, true)
            : [];

        return new ParameterBag($params);
    }

    /**
     * @Route("/api/device", name="add_device")
     */
    public function addDeviceAction(Request $request, PushService $pushService)
    {
        $user = $this->getUser();
        $params = $this->parseRequestBodyAsJson($request);

        $pushService->subscribe($user, $params->get('deviceToken'));

        return new JsonResponse(['success' => true]);
    }
}