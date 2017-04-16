<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 28.11.2016
 * Time: 16:35
 */

namespace App\Controllers;

use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class WorkerController extends BaseController
{
    public function add(Request $req, Response $res, $args)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'extention' => v::alpha()->noWhitespace()->notEmpty()->length(1, 3),
            'image' => v::notEmpty()->noWhitespace()
        ));
        if ($validation->failed()) {
            return $res->withJson($validation->errors)->withStatus(400);
        }
    }
}