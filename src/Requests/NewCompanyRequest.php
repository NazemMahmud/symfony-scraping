<?php

namespace App\Requests;


use App\Requests\BaseRequest;

class NewCompanyRequest extends BaseRequest
{
    #[NotBlank()]
    protected $registration_code;

    protected function autoValidateRequest(): bool
    {
        return false;
    }

}
