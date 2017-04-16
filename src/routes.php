<?php
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 25.01.2017
 * Time: 21:36
 */

use App\Middlewares\AuthChecker;
use App\Middlewares\PermissionChecker;
use App\Middlewares\SalonChecker;
use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/', App\Controllers\HomeController::class);

$app->post('/del', 'App\Controllers\AuthController:delUser');

$app->group('/auth', function () {
    $this->group('/singup', function () {
        $this->post('/customer', 'App\Controllers\AuthController:singupCustomer');
        $this->post('/salon', 'App\Controllers\AuthController:singupSalon');
        $this->group('/worker', function () {
            $this->post('/start', 'App\Controllers\AuthController:startWorker');
            $this->post('/complete', 'App\Controllers\AuthController:singupWorker');
        });
    });

    $this->get('/confirm_email/{user_id}', 'App\Controllers\AuthController:confirmEmail');
    $this->post('/singin', 'App\Controllers\AuthController:singin');
    $this->post('/singout', 'App\Controllers\AuthController:singout')->add(new AuthChecker());
    $this->post('/newPassword', 'App\Controllers\AuthController:newPassword')->add(new AuthChecker());
});

$app->post('/upload', 'App\Controllers\UploadController:uploadFile');

$app->group('/service', function () {
    $this->group('/salon', function () {
        $this->group('/{salon_id:[0-9]*}', function () {
            $this->get('', 'App\Controllers\ServiceController:getBySalon');
            $this->post('', 'App\Controllers\ServiceController:new')->add(new PermissionChecker('salon'));
            $this->put('/{service_id:[0-9]*}', 'App\Controllers\ServiceController:edit')->add(new PermissionChecker('salon'));
            $this->delete('/{service_id:[0-9]*}', 'App\Controllers\ServiceController:delete')->add(new PermissionChecker('salon'));
        });
    });
    $this->group('/worker', function () {
        $this->group('/{worker_id:[0-9]*}', function () {
            $this->get('', 'App\Controllers\ServiceController:getByWorker');
            $this->post('/{service_id:[0-9]*}', 'App\Controllers\ServiceController:newByWorker')->add(new PermissionChecker('worker'))->add(new AuthChecker());
            $this->put('/{service_id:[0-9]*}', 'App\Controllers\ServiceController:editByWorker')->add(new PermissionChecker('worker'))->add(new AuthChecker());
            $this->delete('/{service_id:[0-9]*}', 'App\Controllers\ServiceController:deleteByWorker')->add(new PermissionChecker('worker'))->add(new AuthChecker());
        });
    });
});

$app->group('/salon', function () {
    $this->group('/service', function () {
        $this->group('/{salon_id:[0-9]*}', function () {
            $this->post('', 'App\Controllers\ServiceController:new');
            $this->get('', 'App\Controllers\ServiceController:getBySalon');
            $this->get('/{worker_id:[0-9]*}', 'App\Controllers\ServiceController:getByWorker');
            $this->put('/{service_id:[0-9]*}', 'App\Controllers\ServiceController:edit');
            $this->delete('/{service_id:[0-9]*}', 'App\Controllers\ServiceController:delete');
        });
    });
    $this->group('/search', function () {
        $this->get('/{lat:[-]?[0-9]{1,3}\,[0-9]{6}}/{lng:[-]?[0-9]{1,3}\,[0-9]{6}}/{radius:[0-9]{2,6}}', 'App\Controllers\SearchController:aroundSearch');
        $this->get('/{city}', 'App\Controllers\SearchController:freeSearch');
    });
    $this->group('/rating', function () {
        $this->group('/{salon_id:[0-9]*}', function () {
            $this->post('', 'App\Controllers\RatingController:new');
            $this->get('', 'App\Controllers\RatingController:get');
        })->add(new PermissionChecker('customer'));
    })->add(new SalonChecker());
    $this->group('/comments', function () {
        $this->group('/{salon_id:[0-9]*}', function () {
            $this->post('', 'App\Controllers\CommentController:new');
            $this->get('', 'App\Controllers\CommentController:get');
            $this->put('/{comment_id:[0-9]*}', 'App\Controllers\CommentController:edit');
            $this->delete('/{comment_id:[0-9]*}', 'App\Controllers\CommentController:delete');
        })->add(new PermissionChecker('customer'));
    })->add(new SalonChecker());
})/*->add(new AuthChecker())->add(new PermissionChecker('customer'))*/
;

$app->group('/manage', function () {
    $this->group('/account', function () {

    });

    $this->group('/salon', function () {
        $this->group('/workers', function () {
            $this->get('', 'App\Controllers\WorkerController:get');
            $this->post('', 'App\Controllers\WorkerController:add');
            $this->put('/{worker_id:[0-9]*}', 'App\Controllers\WorkerController:edit');
            $this->delete('/{worker_id:[0-9]*}', 'App\Controllers\WorkerController:delete');
        });
    })->add(new PermissionChecker('salon'));
})->add(new AuthChecker());

