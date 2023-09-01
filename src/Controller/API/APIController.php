<?php

namespace App\Controller\API;

use Keiwen\Cacofony\Controller\AppController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class APIController
 * @package App\Controller
 */
class APIController extends AppController
{

    /**
     * @param array $data
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function renderJson(array $data, int $status = Response::HTTP_OK, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }


    /**
     * @param string $errorMessage
     * @param int $status
     * @param array $headers
     * @return JsonResponse
     */
    protected function renderErrorJson(string $errorMessage, int $status = Response::HTTP_BAD_REQUEST, array $headers = []): JsonResponse
    {
        $data = array(
            'errorMessage' => $errorMessage,
        );
        return $this->renderJson($data, $status, $headers);
    }

}
