<?php
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 17.04.2017
 * Time: 15:53
 */

namespace App\Validation\Rules;

use App\Models\User;
use Respect\Validation\Rules\AbstractRule;

class emailUsed extends AbstractRule
{
    public function validate($input)
    {
        return User::where('email', '==', $input)->count() == 0;
    }
}