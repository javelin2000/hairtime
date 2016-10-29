<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 15.10.2016
 * Time: 21:36
 */
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Middlewares\AuthChecker;
use App\Middlewares\PermissionChecker;


$app->get('/hello/{name}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $response->getBody()->write("Hello, $name");
    return $response;

});


$app->get('/', App\Controllers\HomeController::class);


$app->group('/auth', function () {
    $this->group('/singup', function () {
        $this->post('/customer', 'App\Controllers\AuthController:singupCustomer');
        $this->post('/salon', 'App\Controllers\AuthController:singupSalon');
        $this->post('/worker', 'App\Controllers\AuthController:singupWorker');
    });
    $this->post('/singin', 'App\Controllers\AuthController:singin');
    $this->post('/singout', 'App\Controllers\AuthController:singout')->add(new AuthChecker());
});

$app->group('/customer', function () {
    $this->get('/', \App\Controllers\HomeController::class);
})->add(new AuthChecker())->add(new PermissionChecker('customer'));