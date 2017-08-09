<?php
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 22.05.2017
 * Time: 0:44
 */

namespace App\Controllers;


use App\Models\Notification;
use App\Models\NToken;
use Slim\Http\Request;
use Slim\Http\Response;

class NotificationController
{
    public function setToken(Request $req, Response $res)
    {
        $ntoken = new NToken();
        $ntoken->n_token = $req->getParam('n_token');
        $ntoken->user_id = $req->getHeader('User-ID');
        $ntoken->save();
        return $res->withJson($ntoken)->withStatus(201);

    }

    public function tryNotification(Request $req, Response $res)
    {
        $ntoken = NToken::where('user_id', $req->getParam('user_id'))->pluck('n_token');

        //return $res->withJson($ntoken)->withStatus(200);
        $message = array('message' => 'Dear worker you are have new queue!');

        $notification = new Notification();
        $result = $notification->send_notifications($ntoken, $message);
        return $res->withJson($result)->withStatus(200);
    }

}