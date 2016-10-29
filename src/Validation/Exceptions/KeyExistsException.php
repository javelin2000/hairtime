<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 29.10.2016
 * Time: 18:31
 */
namespace App\Validation\Exceptions;

use \Respect\Validation\Exceptions\ValidationException;

class KeyExistsException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Wrong activation key',
        ],
    ];
}