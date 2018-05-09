<?php

namespace AppBundle\Controller;

use AppBundle\Exception\ValidationException;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

class ExceptionController extends AbstractController
{
    use ControllerTrait;

    /**
     * @param Request $request
     * @param $exception
     * @param DebugLoggerInterface|null $logger
     *
     * @return View
     */
    public function showAction(Request $request, $exception, ?DebugLoggerInterface $logger)
    {
        if ($exception instanceof ValidationException) {
            return $this->getView(json_decode($exception->getMessage(), true), $exception->getStatusCode());
        }

        if ($exception instanceof HttpException) {
            return $this->getView($exception->getMessage(), $exception->getStatusCode());
        }

        return $this->getView('Something went wrong');
    }

    /**
     * @param $msg
     * @param null|string $statusCode
     *
     * @return View
     */
    private function getView($msg, string $statusCode = null): View
    {
        $statusCode = $statusCode ?? 500;

        return $this->view([
            'statusCode' => $statusCode,
            'message' => $msg,
        ], $statusCode);
    }
}
