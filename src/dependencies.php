<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 15.10.2016
 * Time: 21:31
 */

$container = $app->getContainer();

$container['db'] = function ($c) {
    $db = $c->get('settings')['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'], $db['user'], $db['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$capsule = new \Illuminate\Database\Capsule\Manager();
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();

$container['db'] = function ($c) use ($capsule) {
    return $capsule;
};

$container['notFoundHandler'] = function ($c) {
    return function (\Slim\Http\Request $req, \Slim\Http\Response $res) {
        return $res->withStatus(404);
    };
};

$container['notAllowedHandler'] = function ($c) {
    return function (\Slim\Http\Request $req, \Slim\Http\Response $res) {
        return $res->withStatus(405); //TODO: turn off debug
    };
};


\Respect\Validation\Validator::with('\App\Validation\Rules');

$container['validator'] = function ($c) {
    return new \App\Validation\Validator($c);
};


//TODO: turn off debug

$container['phpErrorHandler'] = function ($c) {
    return function (\Slim\Http\Request $req, \Slim\Http\Response $res, Exception $e) use ($c) {
        return $res->write($e->getMessage());
    };
};


$container['errorHandler'] = function ($c) {
    return function (\Slim\Http\Request $req, \Slim\Http\Response $res, Exception $e) use ($c) {
        return $res->write($e->getMessage());
    };
};