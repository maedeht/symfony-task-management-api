<?php

namespace App\Request\Base;

use Symfony\Component\Validator\Constraints as Assert;

class MinConstraint implements Constraint
{
    public function run($option = null)
    {
        return new Assert\Length([
            'min' => $option
        ]);
    }
}