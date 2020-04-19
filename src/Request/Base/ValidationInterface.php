<?php
/**
 * Created by PhpStorm.
 * User: saeed
 * Date: 4/14/2020 AD
 * Time: 16:57
 */

namespace App\Request\Base;


interface ValidationInterface
{
    function rules();

    function validate($data, $constraints=null);
}