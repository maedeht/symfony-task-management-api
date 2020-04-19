<?php

namespace App\Request\Base;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validation as Validator;

class Validation
{
    public function run($data, $constraints = null)
    {
        $validator = Validator::createValidator();

        $constraint = (new RequestConstraint($constraints))->rule();

        $violations = $validator->validate($data, $constraint);

        if($violations->count() > 0)
        {
            $errors = explode("\nArray", "\n".(string)$violations);
            unset($errors[0]);

            return new JsonResponse([
                'errors' => $errors,
                'success' => false
            ], 422);
        }
        return null;
    }
}