<?php

namespace App\Requests;


use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Type;

class UpdateCompanyRequest extends BaseRequest
{

    #[Type(type: 'string', message: 'The registration code must be a string.')]
    #[Assert\NotBlank(message: 'The registration code cannot be blank.')]
    protected $registration_code;

    #[Type(type: 'string', message: 'The vat must be a string.')]
    #[Assert\NotBlank(message: 'The vat cannot be blank.')]
    protected $vat;

    #[Type(type: 'string', message: 'The name must be a string.')]
    #[Assert\NotBlank(message: 'The name cannot be blank.')]
    protected $name;

    #[Type(type: 'string', message: 'The address must be a string.')]
    #[Assert\NotBlank(message: 'The address cannot be blank.')]
    protected $address;

    #[Type(type: 'string', message: 'The mobile phone must be a string.')]
    #[Assert\NotBlank(message: 'The mobile phone cannot be blank.')]
    protected $mobile_phone;

    protected function autoValidateRequest(): bool
    {
        return false;
    }

}
