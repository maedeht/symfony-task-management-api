<?php

namespace App\Request;

use App\Request\Base\Validation;
use App\Request\Base\ValidationInterface;

class LoginRequest extends Validation implements ValidationInterface
{
    public function rules()
    {
        return [
            'email' => 'require | email',
            'password' => 'require'
        ];
    }

    public function validate($data, $constraints=null)
    {
        $constraints = $this->rules();
        return parent::run($data, $constraints);
    }
}