<?php

namespace App\Request;

use App\Request\Base\Validation;
use App\Request\Base\ValidationInterface;

class CreateUserRequest extends Validation implements ValidationInterface
{
    public function rules()
    {
        return [
            'email' => 'require | email',
            'password' => 'require | min:6',
            'password_confirmation' => 'require'
        ];
    }

    public function validate($data, $constraints = null)
    {
        $constraints = $this->rules();
        return parent::run($data, $constraints);
    }
}