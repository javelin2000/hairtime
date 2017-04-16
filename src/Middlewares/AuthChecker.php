<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 29.10.2016
 * Time: 15:02
 */

namespace App\Middlewares;

use App\Models\Token;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

class AuthChecker
{
    public function __invoke(Request $req, Response $res, $next)
    {
        try {
            $id = $req->getHeader('User-ID')[0];
            $token = $req->getHeader('Token')[0];
            Token::where('token', $token)->where('user_id', $id)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            return $res->withStatus(401);
        }
        return $next($req, $res);
    }
}