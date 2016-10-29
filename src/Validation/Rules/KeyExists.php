<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 29.10.2016
 * Time: 18:29
 */

namespace App\Validation\Rules;

use App\Models\Key;
use Respect\Validation\Rules\AbstractRule;


class KeyExists extends AbstractRule
{
    public function validate($input)
    {
        return Key::where('key_body', $input)->count() !== 0;
    }
}
