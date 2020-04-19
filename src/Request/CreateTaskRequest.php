<?php

namespace App\Request;


use App\Request\Base\Validation;
use App\Request\Base\ValidationInterface;

class CreateTaskRequest extends Validation implements ValidationInterface
{
    function rules()
    {
        return [
            'title' => 'require',
            'description' => 'optional',
            'start_time' => 'require',
            'duration' => 'optional'
        ];
    }

    public function validate($data, $constraints = null)
    {
        $constraints = $this->rules();
        return parent::run($data, $constraints);
    }
}