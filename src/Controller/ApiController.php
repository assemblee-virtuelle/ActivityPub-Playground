<?php

namespace App\Controller;

use App\Entity\Application;
use App\Service\ActivityPubService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ApiController extends BaseController
{
    /**
     * @Route("/api/{apiKey}/activity", name="api_post_activity", methods={"POST"})
     */
    public function postActivity(Request $request, ActivityPubService $activityPubService)
    {
        /** @var Application $application */
        $application = $this->getUser();
        $json = $this->parseBodyAsJson($request);

        if( !$json ) {
            throw new BadRequestHttpException("You must POST a JSON object to this endpoint");
        }

        $activity = $activityPubService->handleActivity($json, $application);

        return new Response(null, Response::HTTP_CREATED, ['Location' => $activityPubService->getObjectUri($activity)]);
    }
}