<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 18.10.2016
 * Time: 17:34
 */
namespace App\Controllers;

use Slim\Container;

class BaseController {
    protected $ci;

    function __construct(Container $ci){
        $this->ci = $ci;
    }

    function __get($method){
        if($this->ci->{$method})
            return $this->ci->{$method};
    }
}