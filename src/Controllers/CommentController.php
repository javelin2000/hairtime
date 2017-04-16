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

        $customer_id = $this->getCustomerId($req);
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
        // Check type of User ID, must be int
        if (gettype($this->gerUserId($req)) == "string") {
            $user_id = intval($this->gerUserId($req));
        } elseif (gettype($this->gerUserId($req) == "integer")) {
            $user_id = $this->gerUserId($req);
        }
        // check type of Comment ID, must be int
        if (gettype($args['comment_id']) == 'string') {
            $comment = Comment::getUserComment(intval($args['comment_id']), $user_id);
        } else {
            $comment = Comment::getUserComment($args['comment_id'], $user_id);
        }


        //$comment = Comment::getUserComment($args['comment_id'], $user_id); // Both args has string type, but must be int
        if (!$comment)
            return $res->withJson(['error' => "comment not found", 'user_id' => $user_id, 'comment_id' => intval($args['comment_id'])])->withStatus(404);
        $comment->body = $req->getParam('body');
        $comment->save();
        return $res->withStatus(200);
    }

    function delete(Request $req, Response $res, $args)
    {
        $user_id = $this->gerUserId($req);
        $comment = Comment::getUserComment($args['comment_id'], $user_id);
        if (!$comment)
            return $res->withStatus(404);
        $comment->delete();
        return $res->withStatus(200);
    }

    protected function getCustomerId(Request $req)
    {
        return User::find($this->gerUserId($req))->getEntry()->customer_id;
    }

    protected function gerUserId(Request $req)
    {
        return $req->getHeader('User-ID')[0];
    }

}