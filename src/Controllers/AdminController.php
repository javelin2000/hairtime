<?php
//session_start();

/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 16.05.2017
 * Time: 12:38
 */

namespace App\Controllers;

use App\Models\Admin;
use App\Models\Answer;
use App\Models\Comment;
use App\Models\Message;
use App\Models\Salon;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Slim\Http\Request;
use Slim\Http\Response;
use duncan3dc\Laravel\BladeInstance;
//use duncan3dc\Laravel\Blade;
use Jenssegers\Blade\Blade;


class AdminController extends BaseController
{
    public function check(Request $req, Response $res)
    {
        $user = User::where('login', $req->getParam('login'))->first();
        if (isset($user)) {

        }

    }

    private function sendEmail($params)
    {
        $mail = new EmailController();

        $user_name = $params['name'];
        $mail->AddAddress($params['email'], $user_name); // Получатель
        $mail->Subject = htmlspecialchars('New mail from HairTaime Admin');  // Тема письма
        $letter_body = '
<head>
<title>New answer on you message</title>
</head>
<body>
<br>
' . $params['text'] . '
<br><br>
You can contact us on <a href="mailto:admin@hairtime.co.il">admin@hairtime.co.il</a></p><br>

<p>With best regards, <br /><br>

The HairTime Team.</p>';

        $mail->MsgHTML($letter_body); // Текст сообщения
        $mail->AltBody = "Dear " . $user_name . ", you have new answer from HairTime Admin";
        return $mail->Send();
    }

    public function workWithMessage(Request $req, Response $res, $args)
    {
        session_start();
        $blade = new BladeInstance(__DIR__ . "/../../public/Views", __DIR__ . "/../../public/Views/cache");
        if (isset($_SESSION['login'])) {
            $admin = Admin::where('login', $_SESSION['login'])->first();
            if (isset($admin)) {
                if ($req->getParam('operator') == 'Delete') {
                    $message = Message::where('message_id', $args['message_id'])->first();
                    $message->delete_at = date('Y-m-d H:i:s');
                    $message->save();
                    $allmessages = Message::where('delete_at', null)->orderBy('create_at', 'desc')->get();
                    $messages = Message::where('answer_at', '=', null)->get();
                    echo $blade->render("messages", [
                        'admin' => $admin,
                        'allmessages' => $allmessages,
                        'messages' => $messages,
                    ]);
                } elseif ($req->getParam('operator') == 'Answer') {
                    $message = Message::where('message_id', $args['message_id'])->first();
                    $messages = Message::where('answer_at', '=', null)->get();
                    $answers = $message->answer;
                    echo $blade->render("answer", [
                        'admin' => $admin,
                        'message' => $message,
                        'messages' => $messages,
                        'answers' => $answers,
                    ]);
                } elseif ($req->getParam('operator') == 'Send') {

                    $answer = new Answer();
                    $answer->message_id = $args['message_id'];
                    $answer->text = $req->getParam('textanswer');
                    $answer->save();
                    $message = Message::where('message_id', $args['message_id'])->first();
                    $message->answer_at = date('Y-m-d H:i:s');
                    $message->save();
                    $params = [
                        'name' => $message->name,
                        'email' => $message->user->email,
                        'text' => $answer->text,
                    ];
                    $result = $this->sendEmail($params);

                    //return $res->withStatus(200)->withJson($result);

                    header('Location: ' . 'https://hairtime.co.il/admin/message/' . $message->message_id . '?operator=Answer');

                } elseif ($req->getParam('operator') == 'Cancel') {
                    header('Location: ' . 'https://hairtime.co.il/admin/message');
                }

                return;
            }
        }
        echo $blade->render("login");
        return;

    }

    public function getAllMessage(Request $req, Response $res)
    {
        session_start();
        $blade = new BladeInstance(__DIR__ . "/../../public/Views", __DIR__ . "/../../public/Views/cache");

        if (isset($_SESSION['login'])) {
            if ($admin = Admin::where('login', $_SESSION['login'])->count() > 0) {
                if ($req->getParam('search') != null) {
                    $allmessages = Message::where('delete_at', null)->where('name', 'LIKE', '%' . $req->getParam('search') . '%')->orderBy('create_at', 'desc')->get();

                } else {
                    $allmessages = Message::where('delete_at', null)->orderBy('create_at', 'desc')->get();
                }
                $admin = Admin::where('login', $_SESSION['login'])->first();
                $messages = Message::where('answer_at', '=', null)->get();
                echo $blade->render("messages", [
                    'admin' => $admin,
                    'allmessages' => $allmessages,
                    'messages' => $messages,
                ]);
                return;
            }
        }
        echo $blade->render("login");
        return;
    }

    public function message(Request $req, Response $res)
    {
        $user = User::where('email', $req->getParam('email'))->first();
        if ($user != null) {
            $message = new Message();
            $message->user_id = $user->user_id;
            $message->message = $req->getParam('message');
            $message->name = $req->getParam('name');
            $message->save();

            return $res->withJson($message)->withStatus(201);
        } else {
            return $res->withJson(['message' => "User with this e-mail does't found.", 'error' => "404"])->withStatus(404);
        }

    }

    public function comments(Request $req, Response $res, $args)
    {
        session_start();
        $blade = new BladeInstance(__DIR__ . "/../../public/Views", __DIR__ . "/../../public/Views/cache");
        if (isset($_SESSION['login'])) {
            if ($admin = Admin::where('login', $_SESSION['login'])->count() > 0) {
                //return $res->withJson($req->getParams())->withStatus(200);
                $admin = Admin::where('login', $_SESSION['login'])->first();
                if ($req->getMethod() === 'POST') {
                    if ($req->getParam('search') != null) {
                        if (intval($req->getParam('search')) == 0) {
                            $result = array();
                            $salons = Salon::where('business_name', 'like', $req->getParam('search'))->get();
                            foreach ($salons as $salon) {
                                $comments = Comment::where('salon_id', $salon['salon_id'])->where('del', false)->orderBy('created_at')->get();
                                $result = $result + $comments->toArray();
                            }
                        } else {
                            $result = Comment::where('comment_id', $req->getParam('search'))->first();
                        }
                        echo $blade->render("comments", ['comments' => $result, 'menu' => 'comments', 'admin' => $admin, 'vis' => 'visible']);
                    }
                    if ($req->getParam('operator') == 'Delete') {
                        //return $res->withJson($req->getParams())->withStatus(200);
                        $comment = Comment::where('comment_id', $args['comment_id'])->first();
                        $comment->del = true;
                        $comment->save();
                    }
                    if ($req->getParam('operator') == 'Edit') {
                        //return $res->withJson($req->getParams())->withStatus(200);
                        $comment = Comment::where('comment_id', $args['comment_id'])->first();
                        $comment->body = $req->getParam('comment');
                        $comment->save();

                    }
                    //return $res->withJson($req->getParams())->withStatus(200);
                }
                $comments = Comment::orderBy('created_at', 'desc')->where('del', false)->take(10)->get();
                $messages = Message::where('answer_at', '=', null)->get();
                echo $blade->render("comments", [
                    'comments' => $comments,
                    'menu' => 'comments',
                    'admin' => $admin,
                    'vis' => 'visible',
                    'messages' => $messages,
                ]);
                return;
            }

        } else {
            echo $blade->render("login");
            return;
        }
    }

    public function profile(Request $req, Response $res, $args)
    {
        session_start();
        $blade = new BladeInstance(__DIR__ . "/../../public/Views", __DIR__ . "/../../public/Views/cache");
        $messages = Message::where('answer_at', '=', null)->get();
        if (isset($_SESSION['login'])) {
            if (isset($args['admin_id'])) {
                $admin = Admin::where('login', $_SESSION['login'])->first();
                echo $blade->render("profile", [
                    'edit' => true,
                    'admin' => $admin,
                    'menu' => 'profile',
                    'messages' => $messages,
                ]);

            } else {
                $admin = Admin::where('login', $_SESSION['login'])->first();
                echo $blade->render("profile", [
                    'edit' => false,
                    'admin' => $admin,
                    'menu' => 'profile',
                    'messages' => $messages,
                ]);
            }
            return;
        } else {
            echo $blade->render("login");
            return;
        }
    }

    public function edit(Request $req, Response $res, $args)
    {
        session_start();
        $blade = new BladeInstance(__DIR__ . "/../../public/Views", __DIR__ . "/../../public/Views/cache");
        if (isset($_SESSION['login'])) {
            $admin = Admin::where('login', $_SESSION['login'])->first();
            if (isset($admin)) {
                $admin = $admin->toArray();
                $method = $req->getMethod();
                $messages = Message::where('answer_at', '=', null)->get();

                if ($req->getParam('Save') !== null) {
                    $params = $req->getParams();
                    $salon = Salon::where('salon_id', $args['salon_id'])->first();
                    $salon->first_name = $params["first_name"];
                    $salon->last_name = $params["last_name"];
                    $salon->business_name = $params["business_name"];
                    $salon->founded_in = $params["founded_in"];
                    $salon->staff_number = $params["staff_number"];
                    $salon->city = $params["city"];
                    $salon->address = $params["address"];
                    $salon->lat = $params["lat"];
                    $salon->lng = $params["lng"];
                    $salon->phone = $params["phone"];
                    $salon->logo = $params["logo"];
                    $salon->waze = $params["waze"];
                    $salon->save();
                    $salon = Salon::where('salon_id', $args['salon_id'])->first();

                    echo $blade->render("edit_salon", ['admin' => $admin, 'method' => $method, 'salon' => $salon,
                        'req' => $params, 'menu' => 'salons']);
                    return;
                } elseif ($req->getParam('Delete') !== null) {
                    $salon = Salon::where('salon_id', $args['salon_id'])->first();
                    $user = User::where('entry_id', $salon->salon_id)->
                    where('entry_type', 'App\Models\Salon')->first();
                    $salon->delete();
                    $user->delete();
                } elseif ($req->getParam('Edit') !== null) {
                    $salon = Salon::where('salon_id', $args['salon_id'])->first();
                    echo $blade->render("edit_salon", [
                        'admin' => $admin,
                        'method' => $method,
                        'salon' => $salon,
                        'req' => $req->getParams(),
                        'menu' => 'salons',
                        'messages' => $messages,
                    ]);
                    return;
                } elseif ($req->getParam('status') != null) {
                    $salons = Salon::where('salon_id', $args['salon_id'])->first();
                    $salons->status = $req->getParam('status');
                    $salons->save();
                }
                $salons = Salon::all();
                echo $blade->render("index", [
                    'admin' => $admin,
                    'method' => $method,
                    'salons' => $salons,
                    'req' => $req->getParams(),
                    'menu' => 'salons',
                    'messages' => $messages,
                ]);
                return;
            }
        } else {
            echo $blade->render("login");
            return;
        }


    }

    public function logout(Request $req, Response $res)
    {
        session_start();
        $_SESSION['login'] = null;
        $blade = new BladeInstance(__DIR__ . "/../../public/Views", __DIR__ . "/../../public/Views/cache");
        echo $blade->render("login");
        return;

    }

    public function login(Request $req, Response $res)
    {
        $admin = Admin::where('login', $req->getParam('login'))->first();
        $blade = new BladeInstance(__DIR__ . "/../../public/Views", __DIR__ . "/../../public/Views/cache");
        if ($admin->password == $req->getParam('password')) {
            //return $res->withJson($admin)->withStatus(200);
            session_start();
            @$_SESSION['login'] = $admin->login;
            header('Location: ' . 'https://hairtime.co.il/admin');

        } else {
            echo $blade->render("login", ['error' => 'User name or login incorrect!']);
            return;

        }
    }


    public function salons(Request $req, Response $res)
    {
        session_start();
        $blade = new BladeInstance(__DIR__ . "/../../public/Views", __DIR__ . "/../../public/Views/cache");

        if (isset($_SESSION['login'])) {

            $admin = Admin::where('login', $_SESSION['login'])->first();
            $salons = null;
            if ($req->getMethod() == 'POST') {
                if ($req->getParam('search') != null) {
                    if (intval($req->getParam('search')) == 0) {
                        $salons = Salon::where('business_name', 'like', $req->getParam('search'))->get();
                    } else {
                        $salons = Salon::where('salon_id', $req->getParam('search'))->get();

                    }
                }
            } else {
                $salons = Salon::all();
            }
            $method = $req->getMethod();
            $admin = $admin->toArray();
            //return $res->withJson($salons->toArray())->withStatus(200);
            $messages = Message::where('answer_at', '=', null)->get();
            echo $blade->render("index", [
                'admin' => $admin,
                'method' => $method,
                'salons' => $salons->toArray(),
                'req' => $req->getParams(),
                'menu' => 'salons',
                'messages' => $messages,
            ]);
            return;
        } else {
            echo $blade->render("login");
            return;
        }
        //return $res->withJson($admin)->withStatus(200);*/

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