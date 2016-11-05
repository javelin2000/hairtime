<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 05.11.2016
 * Time: 19:22
 */

namespace App\Controllers;

use App\Models\Rating;
use App\Models\User;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class RatingController extends BaseController
{

    function new(Request $req, Response $res, $args)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'rating' => v::intType()->between(1, 5),
        ));
        if ($validation->failed())
            return $res->withJson($validation->errors)->withStatus(400);


        $customer_id = $this->getCustomerId($req);

        $rating = Rating::where('customer_id', $customer_id)->where('salon_id', $args['salon_id'])->firstOrNew([]);
        $rating->rating = $req->getParam('rating');
        $rating->salon_id = $args['salon_id'];
        $rating->customer_id = $customer_id;
        $rating->save();

        return $res->withStatus(201);
    }

    function get(Request $req, Response $res, array $args)
    {
        $customer_id = $this->getCustomerId($req);
        $rating = Rating::where('customer_id', $customer_id)->where('salon_id', $args['salon_id'])->first();
        if ($rating === null)
            return $res->withJson(['rating' => 0]);
        else
            return $res->withJson(['rating' => $rating->rating]);
    }


    protected function getCustomerId(Request $req)
    {
        return User::find($this->gerUserId($req))->getEntry()->customer_id;
    }

    protected function gerUserId(Request $req)
    {
        return $req->getHeader('User-ID')[0];
    }

}