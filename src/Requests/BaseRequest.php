<?php

namespace App\Requests;

use App\Traits\HttpResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;

class BaseRequest
{
    use HttpResponse;

    protected array $requestData = [];

    public function __construct(protected ValidatorInterface $validator)
    {
        $this->populate();

        if (!$this->autoValidateRequest()) {
            $this->validate();
        }
    }


    public function validate()
    {
        $errors = $this->validator->validate($this);
        $messages = [];

        foreach ($errors as $error) {
            $messages[] = $error->getMessage();
        }

        if (count($messages)) {
            $response = $this->error_response($messages, Response::HTTP_FORBIDDEN);
            $response->send();
        }
    }


    public function getRequest(): Request
    {
        return Request::createFromGlobals();
    }


    protected function populate(): void
    {
        $this->requestData = $this->getRequest()->toArray();
        foreach ($this->requestData as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }


    protected function autoValidateRequest(): bool
    {
        return true;
    }

    public function getContent(): array
    {
        return $this->requestData;
    }
}
