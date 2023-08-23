<?php

namespace App\Requests;


use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Type;

class NewCompanyRequest extends BaseRequest
{

    #[Type(type: 'string', message: 'The registration code must be a string.')]
    #[Assert\NotBlank(message: 'The registration code cannot be blank.')]
    protected $registration_code;

    protected function autoValidateRequest(): bool
    {
        return false;
    }

}
