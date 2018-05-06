<?php

namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends HttpException
{

    public function __construct(ConstraintViolationListInterface $validationErrors)
    {
        $errors = [];

        /** @var ConstraintViolationInterface $validationError */
        foreach ($validationErrors as $validationError) {
            $errors[$validationError->getPropertyPath()] = $validationError->getMessage();
        }

        parent::__construct(400, json_encode($errors));
    }
}
