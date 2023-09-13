<?php

namespace App\Controller\API;

use Keiwen\Cacofony\Controller\AppController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class APIController
 * @package App\Controller
 */
class APIController extends AppController
{

    const ATTRIBUTE_API_LINK = 'links';

    /**
     * @param array $data data where to add links
     * @param string $routeName
     * @param array $parameters route parameters
     * @param string $rel link relation
     * @param string $method link HTTP method
     *
     * @see https://www.iana.org/assignments/link-relations/link-relations.xhtml
     */
    protected function addApiLink(array &$data, string $routeName, array $parameters = array(), string $rel = 'self', string $method = 'GET')
    {
        $url = $this->generateUrl($routeName, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);
        $linkData =  array(
            'href' => $url,
            'rel' => $rel,
            'method' => $method
        );
        if (!isset($data[self::ATTRIBUTE_API_LINK])) $data[self::ATTRIBUTE_API_LINK] = array();
        $data[self::ATTRIBUTE_API_LINK][] = $linkData;
    }

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
