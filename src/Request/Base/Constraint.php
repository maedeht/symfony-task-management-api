<?php

namespace App\Request\Base;

interface Constraint
{
    public function run($option = null);
}