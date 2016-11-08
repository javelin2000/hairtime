<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 15.10.2016
 * Time: 21:36
 */

use App\Middlewares\AuthChecker;
use App\Middlewares\PermissionChecker;
use App\Middlewares\SalonChecker;




$app->get('/', App\Controllers\HomeController::class);


$app->group('/auth', function () {
    $this->group('/singup', function () {
        $this->post('/customer', 'App\Controllers\AuthController:singupCustomer');
        $this->post('/salon', 'App\Controllers\AuthController:singupSalon');
        $this->post('/worker', 'App\Controllers\AuthController:singupWorker');
    });
    $this->post('/singin', 'App\Controllers\AuthController:singin');
    $this->post('/singout', 'App\Controllers\AuthController:singout')->add(new AuthChecker());
    $this->post('/newPassword', 'App\Controllers\AuthController:newPassword')->add(new AuthChecker());
});

$app->group('/salon', function () {
    $this->group('/search', function () {
        $this->get('/{lat:[-]?[0-9]{1,3}\,[0-9]{6}}/{lng:[-]?[0-9]{1,3}\,[0-9]{6}}/{radius:[0-9]{2,5}}', 'App\Controllers\SearchController:aroundSearch');
        $this->get('/{city:[a-zA-Z][a-zA-Z\s]*}', 'App\Controllers\SearchController:freeSearch');
    });
    $this->group('/{salon_id:[0-9]*}', function () {
        $this->group('/comments', function () {
            $this->post('', 'App\Controllers\CommentController:new');
            $this->get('', 'App\Controllers\CommentController:get');
            $this->put('/{comment_id:[0-9]*}', 'App\Controllers\CommentController:edit');
            $this->delete('/{comment_id:[0-9]*}', 'App\Controllers\CommentController:delete');
        });
        $this->group('/rating', function () {
            $this->post('', 'App\Controllers\RatingController:new');
            $this->get('', 'App\Controllers\RatingController:get');
        });
    })->add(new SalonChecker());
})->add(new AuthChecker())->add(new PermissionChecker('customer'));

