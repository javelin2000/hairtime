<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 26.10.2016
 * Time: 7:30
 */
namespace App\Validation\Rules;


use App\Models\User;
use Respect\Validation\Rules\AbstractRule;


class EmailNotUsed extends AbstractRule
{
    public function validate($input)
    {
        return User::where('email', '==', $input)->count() === 0;
    }
}
