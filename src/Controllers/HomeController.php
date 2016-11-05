<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 25.10.2016
 * Time: 12:18
 */

namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

class HomeController extends BaseController
{
    function __invoke(Request $req, Response $res, $args)
    {
//        phpinfo();
        echo "<h1>Hello</h1>";
        return $res;
    }
}