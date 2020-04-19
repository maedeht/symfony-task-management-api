<?php

namespace App\Request\Base;

use Symfony\Component\Validator\Constraints as Assert;

class RequestConstraint
{
    private $rules;

    public function __construct($rules)
    {
        $this->rules = $rules;
    }

    public function rule()
    {
        $constraints = [];
        if(!empty($this->rules))
            foreach ($this->rules as $key => $value)
            {
                $constraints[$key] = $this->extract($value);
            }

        return new Assert\Collection($constraints);
    }

    private function extract($fieldRules)
    {
        $conditions = explode(' | ', $fieldRules);
        $asserts = [];
        foreach ($conditions as $condition)
        {
            $class = ucwords(strtolower($this->key($condition))).'Constraint';
            $class = 'App\Request\Base\\'.$class;
            $instance = new $class();
            $asserts[] = $instance->run($this->option($condition));
        }

        return $asserts;
    }

    private function key($condition)
    {
        $split = explode(':',$condition);
        return $split[0];
    }

    private function option($condition)
    {
        $split = explode(':',$condition);
        return count($split) > 1 ? $split[1] : null;
    }
}