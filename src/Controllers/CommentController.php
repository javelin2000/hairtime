<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 31.10.2016
 * Time: 22:21
 */

namespace App\Controllers;

use App\Models\Comment;
use App\Models\Salon;
use App\Models\User;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class CommentController extends BaseController
{

    function new(Request $req, Response $res, $args)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'body' => v::notEmpty()->length(1, 300),
        ));
        if ($validation->failed())
            return $res->withJson($validation->errors)->withStatus(400);

        $user_id = $req->getHeader('User-ID')[0];
        $customer_id = User::find($user_id)->getEntry()->customer_id;
        $salon_id = $args['salon_id'];
        $comment = new Comment();
        $comment->salon_id = $salon_id;
        $comment->customer_id = $customer_id;
        $comment->body = $req->getParam('body');
        $comment->save();
        return $res->withStatus(201);
    }

    function get(Request $req, Response $res, array $args)
    {
        $salon = Salon::find($args['salon_id']);
        return $res->withJson($salon->commentsWithCustomerInfo());
    }

    function edit(Request $req, Response $res, $args)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'body' => v::notEmpty()->length(1, 300),
        ));
        if ($validation->failed())
            return $res->withJson($validation->errors)->withStatus(400);

        $user_id = $req->getHeader('User-ID')[0];
        $comment = Comment::getUserComment($args['comment_id'], $user_id);
        if (!$comment)
            return $res->withStatus(404);
        $comment->body = $req->getParam('body');
        $comment->save();
        return $res->withStatus(200);
    }

    function delete(Request $req, Response $res, $args)
    {
        $user_id = $req->getHeader('User-ID')[0];
        $comment = Comment::getUserComment($args['comment_id'], $user_id);
        if (!$comment)
            return $res->withStatus(404);
        $comment->delete();
        return $res->withStatus(200);
    }

}