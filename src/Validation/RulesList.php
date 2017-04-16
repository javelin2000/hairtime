<?php

/**
 * Created by PhpStorm.
 * User: marko
 * Date: 05.12.2016
 * Time: 7:12
 */

namespace App\Validation;

use App\Validation\Rules as MyRules;
use Respect\Validation\Rules;


trait RulesList
{
    public static function first_name()
    {
        return new Rules\AllOf(
            new Rules\Alpha(),
            new Rules\NotEmpty(),
            new Rules\NoWhitespace(),
            new Rules\Length(3, 30)
        );
    }

    public static function last_name()
    {
        return new Rules\AllOf(
            new Rules\Alpha(),
            new Rules\NotEmpty(),
            new Rules\NoWhitespace(),
            new Rules\Length(3, 30)
        );
    }

    public static function email()
    {
        return new Rules\AllOf(
            new Rules\Email(),
            new MyRules\EmailNotUsed()
        );
    }

}
