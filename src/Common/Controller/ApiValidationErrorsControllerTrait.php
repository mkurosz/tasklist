<?php

namespace Tasklist\Common\Controller;

use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Throwable;

/**
 * Trait for Controllers using FOSRestBundle, which contains validation.
 */
trait ApiValidationErrorsControllerTrait
{
    /**
     * Create validation errors response.
     *
     * @param ConstraintViolationListInterface $validationErrors
     *
     * @return View
     */
    private function createValidationErrorsResponse(ConstraintViolationListInterface $validationErrors): View
    {
        $errors = [];

        /** @var ConstraintViolationInterface $validationError */
        foreach ($validationErrors as $validationError) {
            $errors[] = [
                'property' => $validationError->getPropertyPath(),
                'message' => $validationError->getMessage(),
            ];
        }

        return $this->view(['errors' => $errors], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Create exception response.
     *
     * @param Throwable $exception
     *
     * @return View
     */
    private function createExceptionResponse(Throwable $exception): View
    {
        return $this->view(
            [
                'errors' => [
                    'property' => '',
                    'message' => $exception->getMessage(),
                ]
            ],
            Response::HTTP_BAD_REQUEST
        );
    }
}
