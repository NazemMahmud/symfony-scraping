<?php

namespace App\Requests;

use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class BaseRequest
{
    public function __construct(protected ValidatorInterface $validator)
    {
        $this->populate();

        if ($this->autoValidateRequest()) {
            $this->validate();
        }
    }


    public function validate()
    {
        $errors = $this->validator->validate($this);
        $messages = ['message' => 'validation_failed', 'errors' => []];

        foreach ($errors as $message) {
            $messages['errors'][] = [
                'property' => $message->getPropertyPath(),
                'value' => $message->getInvalidValue(),
                'message' => $message->getMessage(),
            ];
        }

        if (count($messages['errors']) > 0) {
            $response = new JsonResponse($messages, 201);
            $response->send();

            exit;
        }
    }


    public function getRequest(): Request
    {
        return Request::createFromGlobals();
    }


    protected function populate(): void
    {
        foreach ($this->getRequest()->toArray() as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }


    protected function autoValidateRequest(): bool
    {
        return true;
    }

}
