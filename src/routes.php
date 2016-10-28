<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 15.10.2016
 * Time: 21:36
 */
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");
    return $response;

});


$app->get('/', App\Controllers\HomeController::class);


$app->group('/auth', function(){
    $this->group('/singup', function (){
        $this->post('/customer', 'App\Controllers\AuthController:singupCustomer');
        $this->post('/salon', 'App\Controllers\AuthController:singupSalon');
        $this->post('/worker', 'App\Controllers\AuthController:singupWorker');
    });
    $this->post('/singin', 'App\Controllers\AuthController:singin');
});
