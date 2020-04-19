<?php

namespace App\Request\Base;

use Symfony\Component\Validator\Constraints as Assert;

class OptionalConstraint implements Constraint
{
    public function run($option = null)
    {
        return new Assert\Optional();
    }
}