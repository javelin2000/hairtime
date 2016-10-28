<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 26.10.2016
 * Time: 17:35
 */
namespace App\Validation\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

class EmailUsedException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} already exists',
        ],
    ];
}