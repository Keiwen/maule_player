<?php

namespace App\Controller;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ErrorController extends AbstractAppController
{

    public function show(\Throwable $exception, LoggerInterface $logger = null): Response
    {
        $statusCode = 0;
        $statusText = '';
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            $statusText = $exception->getMessage();
        }

        $user = $this->getUser();
        $userId = 0;
        if ($user && $user instanceof User) {
            $userId = $user->getId();
        }

        $customErrorCode = $statusCode . '-' . $userId . '-' . date('YmdHi');

        $templateParameter = array(
            'status_code' => $statusCode,
            'status_text' => $statusText,
            'custom_code' => $customErrorCode,
        );

        try {
            return $this->render('@Twig/Exception/error' . $statusCode . '.html.twig', $templateParameter);
        } catch (\Exception $renderException) {
            //specific template not found, use generic one
        }
        return $this->render('@Twig/Exception/error.html.twig', $templateParameter);
    }


}
