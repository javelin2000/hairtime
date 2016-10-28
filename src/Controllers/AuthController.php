<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 15.10.2016
 * Time: 22:35
 */

namespace App\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Models\Salon;
use App\Models\Worker;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as v;

class AuthController extends BaseController
{
    function singupCustomer(Request $req, Response $res)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'email' => v::notEmpty()->email()->length(5, 255)->emailUsed(),
            'first_name' => v::noWhitespace()->notEmpty()->length(1, 100),
            'last_name' => v::noWhitespace()->notEmpty()->length(1, 100),
            'password' => v::notEmpty()->length(1, 50), //TODO: turn off debug mode
            'phone' => v::phone(),
            'logo' =>v::optional(v::url()->length(1,100))
        ));
        if($validation->failed())
            return $res->withJson($validation->errors)->withStatus(400);

        $token = $this->makeToken();

        $customer = Customer::create($req->getParams());
        $user = $customer->user()->create($req->getParams());
        $user->tokens()->create(['token' => $token]);
        $res = $res->withHeader('User ID', $user->user_id)
                   ->withHeader('Token', $token);
        return $res->withStatus(201);
    }

    function singupSalon(Request $req, Response $res)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'email' => v::notEmpty()->email()->length(5, 255)->emailUsed(),
            'first_name' => v::notEmpty()->noWhitespace()->length(1, 100),
            'last_name' => v::notEmpty()->noWhitespace()->length(1, 100),
            'business_name' => v::notEmpty()->length(1, 100),
            'founded_in' => v::between(1980, date("Y")),
            'city' => v::alpha()->length(1, 255),
            'address' => v::notEmpty()->length(1, 255),
            'lat' => v::floatType(),
            'lng' => v::floatType(),
            'password' => v::notEmpty()->length(1, 50), //TODO: turn off debug mode
            'phone' => v::phone(),
            'logo' =>v::optional(v::url()->length(1,100))
        ));
        if($validation->failed())
            return $res->withJson($validation->errors)->withStatus(400);

        $token = $this->makeToken();

        $salon = Salon::create($req->getParams());
        $user = $salon->user()->create($req->getParams());
        $user->tokens()->create(['token' => $token]);
        $res = $res->withHeader('User ID', $user->user_id)
                   ->withHeader('Token', $token);
        return $res->withStatus(201);
    }

    function singupWorker(Request $req, Response $res)
    {
        //TODO: Worker singup
        Worker::create($req->getParams());
        return $res->withStatus(201);
    }

    function singin(Request $req, Response $res){
        $validation = $this->validator;
        $validation->validate($req, array(
            'email' => v::notEmpty()->email()->length(5, 255),
            'password' => v::notEmpty()->length(1, 50) //TODO: turn off debug mode
        ));
        if($validation->failed())
            return $res->withJson($validation->errors)->withStatus(400);

        $user = User::where('email', $req->getParam('email'))->first();
        if($user->password !== $req->getParam('password'))
            return $res->withStatus(400)
                       ->withJson(['error' => 'Wrong password']) ;
        else {
            $token = $this->makeToken();
            $user->tokens()->create(['token' => $token]);
            return $res = $res->withHeader('User ID', $user->user_id)
                              ->withHeader('Token', $token);
        }
    }

    protected function makeToken(){
        return sha1(random_bytes(40));
    }


}





