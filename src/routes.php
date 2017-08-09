<?php
//session_start();
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 25.01.2017
 * Time: 21:36
 */

use App\Middlewares\AuthChecker;
use App\Middlewares\PermissionChecker;
use App\Middlewares\SalonChecker;
use App\Models\NToken;
use Slim\Http\Request;
use Slim\Http\Response;

$app->get('/', App\Controllers\HomeController::class);

$app->post('/del', 'App\Controllers\AuthController:delUser');

/*$app->get('/forgot_password/{email}', function (Request $req, Response $res)
{
    if (User::where('email', $req->getParam('email'))->count() > 0) {
        $user = User::where('email', $req->getParam('email'))->first();
        $chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
        $max = 8;
        $password = null;
        while ($max--)
            $password .= $chars[rand(0, 61)];
        $user->password = $password;
        $user->save();

        $mail = new EmailController();

        $user_name = $user->last_name." " . $user->first_name;
        $mail->AddAddress($req->getParam('email'), $user_name); // Получатель
        $mail->Subject = htmlspecialchars('New password for HairTime application');  // Тема письма
        $letter_body = '
<head>
<title>New password for HairTime application</title>
</head>
<body>
<img alt="HairTime" src="https://hairtime.co.il/img/image.jpg" style="float: right; align-items:right; width: 400px; height: 107px;" />
<br>
<h1>&nbsp;</h1>

<h1>&nbsp;</h1>

<h2>Dear ' . $user_name . ',</h2>
<p>temporary password for your account in HairTime application is: <b>'.$password.'</b></p>
Please, login in your application and change this temporary password!
<br><br>
If you have any issues confirming your email we will be happy to help you. You can contact us on 
<a href="mailto:admin@hairtime.co.il">admin@hairtime.co.il</a></p><br>

<p>With best regards, <br /><br>

The HairTime Team.</p>';

        $mail->MsgHTML($letter_body); // Текст сообщения
        $mail->AltBody = "Dear " . $user_name . ", temporary password for your account in HairTime application is: ".$password;
        $result = $mail->Send();
        return $res->withJson(['message' => "New temporary password sent to e-mail ", 'error' => ""])->withStatus(200);
    }
    return $res->withJson(['message' => "User not found", 'error' => "404"])->withStatus(404);

});*/


$app->get('/recalccomments', 'App\Controllers\CommentController:recalc');

$app->group('/queue', function () {
    $this->get('/worker/{worker_id}', 'App\Controllers\QueueController:getQueue');
    $this->get('/salon/{salon_id}', 'App\Controllers\QueueController:getSalonQueue');
    $this->get('/customer/{customer_id}', 'App\Controllers\QueueController:getCustomerQueue');
    $this->post('/{worker_id}/{service_id}', 'App\Controllers\QueueController:addQueue');
    $this->delete('/{queue_id}', 'App\Controllers\QueueController:deleteQueue');
    $this->get('/confirm/{queue_id}', 'App\Controllers\QueueController:confirmQueue');
})->add(new AuthChecker());

$app->group('/notification', function () {
    $this->post('/set_token', function (Request $req, Response $res) {
        $user = \App\Models\User::where('user_id', ($req->getHeader('User-ID')))->first();
        $ntoken = new NToken();
        $ntoken->n_token = $req->getParam('n_token');
        $ntoken->user_id = $user->user_id;
        $ntoken->save();
        return $res->withJson($ntoken)->withStatus(201);
    });
    $this->post('', 'App\Controllers\NotificationController:tryNotification');

});

$app->group('/auth', function () {
    $this->group('/singup', function () {
        $this->post('/customer', 'App\Controllers\AuthController:singupCustomer');
        $this->post('/salon', 'App\Controllers\AuthController:singupSalon');
        $this->group('/worker', function () {
            $this->post('/start', 'App\Controllers\AuthController:startWorker');
            $this->post('/complete', 'App\Controllers\AuthController:singupWorker');
        });
    });
    $this->group('/edit', function () {
        $this->post('/customer', 'App\Controllers\AuthController:editCustomer');
        $this->post('/salon', 'App\Controllers\AuthController:editSalon');
        $this->post('/worker', 'App\Controllers\AuthController:editWorker');
    })/*->add(new AuthChecker())*/
    ;

    $this->get('/confirm_email/{user_id}', 'App\Controllers\AuthController:confirmEmail');
    $this->get('/forgot_password', 'App\Controllers\AuthController::forgotPassword');
    $this->post('/singin', 'App\Controllers\AuthController:singin');
    $this->post('/singout', 'App\Controllers\AuthController:singout')->add(new AuthChecker());
    $this->post('/newPassword', 'App\Controllers\AuthController:newPassword')->add(new AuthChecker());
});

$app->post('/upload', 'App\Controllers\UploadController:uploadFile');

$app->group('/admin', function () {
    $this->any('', 'App\Controllers\AdminController:salons');
    $this->post('/login', 'App\Controllers\AdminController:login');
    $this->get('/logout', 'App\Controllers\AdminController:logout');
    $this->post('/salon/{salon_id}', 'App\Controllers\AdminController:edit');
    $this->get('/profile', 'App\Controllers\AdminController:profile');
    $this->get('/profile/{admin_id:[0-9]*}', 'App\Controllers\AdminController:profile');
    $this->get('/comments', 'App\Controllers\AdminController:comments');
    $this->post('/comments', 'App\Controllers\AdminController:comments');
    $this->post('/comments/{comment_id:[0-9]*}', 'App\Controllers\AdminController:comments');
});

//$app->get('/public[/css/{[a-z]+}]', '');

$app->group('/worker', function () {
    $this->group('/schedule/{worker_id:[0-9]*}', function () {
        $this->get('', 'App\Controllers\ScheduleController:getSchedule');
        $this->post('', 'App\Controllers\ScheduleController:newSchedule')->add(new PermissionChecker('worker'))->add(new AuthChecker());
        $this->delete('/{schedule_id:[0-9]*}', 'App\Controllers\ScheduleController:deleteSchedule')->add(new PermissionChecker('worker'))->add(new AuthChecker());
    });
    $this->post('/schedules/{worker_id:[0-9]*}', 'App\Controllers\ScheduleController:newJSONSchedule')->add(new PermissionChecker('worker'))->add(new AuthChecker());

});

$app->group('/service', function () {
    $this->group('/salon', function () {
        $this->group('/{salon_id:[0-9]*}', function () {
            $this->get('', 'App\Controllers\ServiceController:getBySalon');
            $this->post('', 'App\Controllers\ServiceController:new')->add(new PermissionChecker('salon'));
            $this->put('/{service_id:[0-9]*}', 'App\Controllers\ServiceController:edit')->add(new PermissionChecker('salon'));
            $this->delete('/{service_id:[0-9]*}', 'App\Controllers\ServiceController:delete')->add(new PermissionChecker('salon'));
            $this->post('/upload/{service_id:[0-9]*}', 'App\Controllers\UploadController:uploadService')/*->add(new PermissionChecker('salon'))*/
            ;
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
    /*$this->group('/service', function () {
        $this->group('/{salon_id:[0-9]*}', function () {
            $this->post('', 'App\Controllers\ServiceController:new');
            $this->get('', 'App\Controllers\ServiceController:getBySalon');
            $this->get('/{worker_id:[0-9]*}', 'App\Controllers\ServiceController:getByWorker');
            $this->put('/{service_id:[0-9]*}', 'App\Controllers\ServiceController:edit');
            $this->delete('/{service_id:[0-9]*}', 'App\Controllers\ServiceController:delete');
        });
    });*/
    $this->group('/workers', function () {
        $this->get('/{salon_id:[0-9]*}', 'App\Controllers\WorkerController:getWorkers');
        $this->get('/service/{worker_id:[0-9]*}', 'App\Controllers\WorkerController:getWorkersService');
    })->add(new AuthChecker());

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

