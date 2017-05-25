<?php
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 16.05.2017
 * Time: 12:38
 */

namespace App\Controllers;

use App\Models\Admin;
use App\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;
use duncan3dc\Laravel\BladeInstance;
use duncan3dc\Laravel\Blade;


class AdminController
{
    public function check(Request $req, Response $res)
    {
        $user = User::where('login', $req->getParam('login'))->first();
        if (isset($user)) {
        }

    }

    public function login(Request $req, Response $res)
    {
        //session_start();
        //$blade = new BladeInstance(__DIR__ . "/../../public/Views", __DIR__ .  "/../../public/Views/cache");
        $user = Admin::join('customers', 'customer_id', '=', 'customer_id')->
        where('login', 'hanter')->first();
        //$result = getcont
        Blade::addPath(__DIR__ . "/../../public/Views");
        echo Blade::render("register", $user->toArray());
        //echo $blade->render("register", $user->toArray());
        return;
        /*
        if (isset($_SESSION['login'])) {

            $admin = Admin::where('login', $_SESSION['login'])->first();
            $user = Admin::join('customers', 'customer_id', '=', 'customer_id')->
                where('login', $_SESSION['login'])->first();

            echo $blade->render("index", $user->toArray());
        }else{
            echo $blade->render("login");
        }*/

    }

    public function index($user)
    {

        $blade = new BladeInstance(__DIR__ . "/../../public/Views", __DIR__ . "/../../public/Views/cache");
        $result = $blade->exists('index');
        echo $blade->render("index", $user);

        echo $blade->render("index", ['url' => 'http://hairtime.co.il/uploads/img-20170325-58d66c0f72c26', 'name' => 'Vitaliy ZALYOTIN']);
        //return $res->withBody($blade->render("index"))->withStatus(200);


    }

}