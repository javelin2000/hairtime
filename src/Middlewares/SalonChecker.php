<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 31.10.2016
 * Time: 21:57
 */

namespace App\Middlewares;

use App\Models\Salon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class SalonChecker
{
    function __invoke(Request $req, Response $res, $next)
    {
        try {
            $id = $req->getAttribute('route')->getArguments()['salon_id'];
            Salon::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return $res->withStatus(404);
        }
        return $next($req, $res);
    }
}