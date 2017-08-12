<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 15.10.2016
 * Time: 22:35
 */

namespace App\Controllers; // name declaration

use App\Models\Customer;
use App\Models\Key;
use App\Models\Salon;
use App\Models\Token;
use App\Models\User;
use App\Models\Worker;
use duncan3dc\Laravel\BladeInstance;
use FreakMailer;
use PHPMailer;
use phpmailerException;
use Respect\Validation\Validator as v;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;


class AuthController extends BaseController
{
    public function confirmEmail(Request $req, Response $res)
    {
        $user_id = $req->getAttribute('user_id');
        //$user = User::find($user_id)->first();
        $user = User::where('user_id', $user_id)->first();


        $user->confirm_email = true;
        $user->save();
        $blade = new BladeInstance(__DIR__ . "/../../public/Views", __DIR__ . "/../../public/Views/cache");

        echo $blade->render("email");
        return;
        //return $res->withJson(['message' => $user, 'error' => "", 'success' => $user_id])->withStatus(200);
    }


    public function singupCustomer(Request $req, Response $res)
    { //TODO: redo validation
        $validation = $this->validator;
        $validation->validate($req, array(
            'email' => v::notEmpty()->email()->length(5, 255),
            'first_name' => v::noWhitespace()->notEmpty()->length(1, 100),
            'last_name' => v::noWhitespace()->notEmpty()->length(1, 100),
            'password' => v::notEmpty()->length(1, 50), //TODO: turn off debug mode
            'phone' => v::phone(),
            'logo' => v::optional(v::url()->length(1, 100))
        ));
        if ($validation->failed()) {
            return $res->withJson($validation->errors)->withStatus(400);
        }
        if (User::where('email', $req->getParam('email'))->count() > 0) {
            return $res->withJson(['message' => 'This e-mail already exist. Try other e-mail', 'error' => '400'])->withStatus(400);
        }
        $token = $this->makeToken();

        $customer = Customer::create($req->getParams());
        $user = $customer->user()->create($req->getParams());
        $user->tokens()->create(['token' => $token]);
        $confirm = $user->user_id;

        $mail = new EmailController();
        $user_name = $req->getParam('last_name') . " " . $req->getParam('first_name');
        $mail->AddAddress($req->getParam('email'), $user_name); // Получатель
        $mail->Subject = htmlspecialchars(' אימות כתובת המייל שלך ב ' . ' /  Verify e-mail address, please');  // Тема письма
        $letter_body = '
<head>
<title>אימות כתובת המייל שלך ב / Verify e-mail address, please</title>
</head>
<body>
<img alt="HairTime" src="https://hairtime.co.il/img/image.jpg" style="align-items: center; width: 400px; height: 107px;" />
<br><br>

<p align="right"><b>
 אנו שמחים ומודים לך שנרשמת לאפליקציית HAIR-TIME
 </b><br><a href="http://hairtime.co.il/auth/confirm_email/' . $confirm . '" title="Go!!!"> כנס\י לקישור</a>
רק רצינו לוודא שכתובת המייל שלך הוזנה בצורה תקינה. לשם כך אנא 
 <br>לאחר מכן חשבונך יופעל ותוכל\י להתחיל להשתמש באפליקציה
 <a href="mailto:admin@hairtime.co.il">admin@hairtime.co.il</a>-כל שאלה ניתן לשלוח מייל לתמיכה שלנו בכתובת </p>

<p align="right">,בברכה<br>
  צוות  HAIR-TIME
 </p>
<hr>
<h6><p align="left">We invite you to register in HairTime application! We just need to verify your email address:
<a href="http://hairtime.co.il/auth/confirm_email/' . $confirm . '" title="Go!!!">Verify e-mail</a><br>
If you have any issues confirming your email we will be happy to help you. You can contact us on 
<a href="mailto:admin@hairtime.co.il">admin@hairtime.co.il</a></p>

<p align="left">With best regards,<br /><br>

The HairTime Team.</p></h6>';
        $mail->MsgHTML($letter_body); // Текст сообщения
        $mail->AltBody = "Dear " . $user_name . ", confirm your email, please. Copy next string to your browser and press enter: http://hairtime.co.il/auth/confirm_email/" . $confirm;
        $result = $mail->Send();
        /* $login_data = ['user_id' => $user->user_id, 'token' => $token, 'type' => explode('\\', get_class($user->getEntry()))[2],
             'confirm_email'=> $user->confirm_email];
         return $res->withJson($user->getEntry()->toArray() + $login_data);*/
        $type = mb_strtolower(explode("\\", $user->entry_type)[2]);

        if ($result) {

            return $res->withJson($user->getEntry()->toArray() + ['user_id' => $user->user_id, 'token' => $token, $type . '_id' => $user->entry_id,
                    'confirm_email' => '0', 'email-status' => 'successfully sent'])->withStatus(201);
        } else {

            return $res->withJson($user->getEntry()->toArray() + ['user_id' => $user->user_id, 'token' => $token, $type . '_id' => $user->entry_id,
                    'confirm_email' => '0', 'email-status' => $mail->ErrorInfo])->withStatus(400);
        }
    }

    public function editCustomer(Request $req, Response $res)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'email' => v::notEmpty()->email()->length(5, 255)->EmailNotUsed(),
            'first_name' => v::noWhitespace()->notEmpty()->length(1, 100),
            'last_name' => v::noWhitespace()->notEmpty()->length(1, 100),
            'password' => v::notEmpty()->length(1, 50),
            'phone' => v::phone(),
            'logo' => v::optional(v::url()->length(1, 100))
        ));
        if ($validation->failed()) {
            return $res->withJson($validation->errors)->withStatus(400);
        }
        $user = User::where('user_id', $req->getHeader('User-ID')[0])->first();


        $customer = Customer::where('customer_id', $user->entry_id)->first();
        $customer->first_name = $req->getParam('first_name');
        $customer->last_name = $req->getParam('last_name');
        $customer->phone = $req->getParam('phone');
        $customer->password = $req->getParam('password');
        $customer->save();
        return $res->withJson($customer->toArray())->withStatus(201);

    }

    public function singupSalon(Request $req, Response $res)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'email' => v::notEmpty()->email()->length(5, 255)->EmailNotUsed(),
            'first_name' => v::notEmpty()->noWhitespace()->length(1, 100),
            'last_name' => v::notEmpty()->noWhitespace()->length(1, 100),
            'business_name' => v::notEmpty()->length(1, 100),
            'founded_in' => v::between(1980, date("Y")),
            'city' => v::notEmpty()->length(1, 255),
            'address' => v::notEmpty()->length(1, 255),
            'house' => v::notEmpty()->length(1, 10),
            /*'lat' => v::notEmpty(),
            'lng' => v::notEmpty(), //TODO: regex validator*/
            'password' => v::notEmpty()->length(1, 50), //TODO: turn off debug mode
            'phone' => v::phone(),
            'logo' => v::optional(v::url()->length(1, 100)),
            // 'activation_key' => v::notEmpty()->length(1, 100)->keyExists()
        ));
        if ($validation->failed()) {
            return $res->withJson($validation->errors)->withStatus(400);
        }
        if (User::where('email', $req->getParam('email'))->count() > 0) {
            return $res->withJson(['message' => 'This e-mail already exist. Try other e-mail', 'error' => '400'])->withStatus(400);
        }
        //Key::where('key_body', $req->getParam('activation_key'))->first()->delete();

        $token = $this->makeToken();

        $salon = Salon::create($req->getParams());
        $user = $salon->user()->create($req->getParams());
        $user->tokens()->create(['token' => $token]);

        $confirm = $user->user_id;

        $mail = new EmailController();

        $user_name = $req->getParam('last_name') . " " . $req->getParam('first_name');
        $mail->AddAddress($req->getParam('email'), $user_name); // Получатель
        $mail->Subject = htmlspecialchars(' אימות כתובת המייל שלך ב ' . ' /  Verify e-mail address, please');  // Тема письма
        $letter_body = '
<head>
<title>אימות כתובת המייל שלך ב / Verify e-mail address, please</title>
</head>
<body>
<img alt="HairTime" src="https://hairtime.co.il/img/image.jpg" style="align-items: center; width: 400px; height: 107px;" />
<br><br>

<p align="right"><b>
 אנו שמחים ומודים לך שנרשמת לאפליקציית HAIR-TIME
 </b><br><a href="http://hairtime.co.il/auth/confirm_email/' . $confirm . '" title="Go!!!"> כנס\י לקישור</a>
רק רצינו לוודא שכתובת המייל שלך הוזנה בצורה תקינה. לשם כך אנא 
 <br>לאחר מכן חשבונך יופעל ותוכל\י להתחיל להשתמש באפליקציה
 <a href="mailto:admin@hairtime.co.il">admin@hairtime.co.il</a>-כל שאלה ניתן לשלוח מייל לתמיכה שלנו בכתובת </p>

<p align="right">,בברכה<br>
  צוות  HAIR-TIME
 </p>
<hr>
<h6><p align="left">We invite you to register in HairTime application! We just need to verify your email address:
<a href="http://hairtime.co.il/auth/confirm_email/' . $confirm . '" title="Go!!!">Verify e-mail</a><br>
If you have any issues confirming your email we will be happy to help you. You can contact us on 
<a href="mailto:admin@hairtime.co.il">admin@hairtime.co.il</a></p>

<p align="left">With best regards,<br /><br>

The HairTime Team.</p></h6>';

        $mail->MsgHTML($letter_body); // Текст сообщения
        $mail->AltBody = "Confirm your email, please. Copy next string to your browser and press enter: http://hairtime.co.il/auth/confirm_email/" . $confirm;
        $result = $mail->Send();

        if ($result) {
            return $res->withJson($user->getEntry()->toArray() + ['user_id' => $user->user_id, 'salon_id' => $user->entry_id, 'token' => $token,
                    'confirm_email' => '0', 'email-status' => 'successfully sent'])->withStatus(201);
        } else {

            return $res->withJson($user->getEntry()->toArray() + ['user_id' => $user->user_id, 'salon_id' => $user->entry_id, 'token' => $token,
                    'confirm_email' => '0', 'email-status' => $mail->ErrorInfo])->withStatus(400);
        }

    }

    public function editSalon(Request $req, Response $res)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'email' => v::notEmpty()->email()->length(5, 255)->EmailNotUsed(),
            'first_name' => v::notEmpty()->noWhitespace()->length(1, 100),
            'last_name' => v::notEmpty()->noWhitespace()->length(1, 100),
            'business_name' => v::notEmpty()->length(1, 100),
            'founded_in' => v::between(1980, date("Y")),
            'city' => v::alpha()->length(1, 255),
            'address' => v::notEmpty()->length(1, 255),
            'house' => v::notEmpty()->length(1, 10),
            //'lat' => v::notEmpty(),
            //'lng' => v::notEmpty(),
            'password' => v::notEmpty()->length(1, 50),
            'phone' => v::phone(),
            'logo' => v::optional(v::url()->length(1, 100)),
            // 'activation_key' => v::notEmpty()->length(1, 100)->keyExists()
        ));
        if ($validation->failed()) {
            return $res->withJson($validation->errors)->withStatus(400);
        }

        //Key::where('key_body', $req->getParam('activation_key'))->first()->delete();
        $user = User::where('email', $req->getParam('email'))->first();
        //return $res->withJson(['sdgasdg'=>$user->user_id])->withStatus(201);
        $salon = Salon::where('salon_id', $user->entry_id)->first();
        $salon->first_name = $req->getParam('first_name');
        $salon->last_name = $req->getParam('last_name');
        $salon->business_name = $req->getParam('business_name');
        $salon->founded_in = $req->getParam('founded_in');
        $salon->address = $req->getParam('address');
        $salon->house = $req->getParam('house');
        $salon->password = $req->getParam('password');
        $salon->lat = $req->getParam('lat');
        $salon->lng = $req->getParam('lng');
        $salon->phone = $req->getParam('phone');
        $salon->save();
        return $res->withJson($salon->toArray())->withStatus(201);

    }

    public function forgotPassword(Request $req, Response $res, $args)
    {
        if (User::where('email', $args['email'])->count() > 0) {
            $user = User::where('email', $args['email'])->first();
            $chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
            $max = 8;
            $password = null;
            while ($max--)
                $password .= $chars[rand(0, 61)];
            $user->password = $password;
            $user->save();

            $mail = new EmailController();

            $user_name = $user->last_name . " " . $user->first_name;
            $mail->AddAddress($args['email'], $user_name); // Получатель
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
<p>temporary password for your account in HairTime application is: <b>' . $password . '</b></p>
Please, login in your application and change this temporary password!
<br><br>
If you have any issues confirming your email we will be happy to help you. You can contact us on 
<a href="mailto:admin@hairtime.co.il">admin@hairtime.co.il</a></p><br>

<p>With best regards, <br /><br>

The HairTime Team.</p>';

            $mail->MsgHTML($letter_body); // Текст сообщения
            $mail->AltBody = "Dear " . $user_name . ", temporary password for your account in HairTime application is: " . $password;
            $result = $mail->Send();
            if ($result) {
                return $res->withJson(['message' => "New temporary password sent to e-mail ", 'error' => null])->withStatus(200);
            } else {
                return $res->withJson(['message' => "Something wrong. Pasword don't sent.", 'error' => '520'])->withStatus(520);
            }
        }
        return $res->withJson(['message' => "User not found", 'error' => "404"])->withStatus(404);

    }

    public function startWorker(Request $req, Response $res)
    {
        $validation = $this->validator;
        $validation->validate($req, [
            'email' => v::notEmpty()->email()->length(5, 255)->EmailNotUsed(),
            'salon_id' => v::notEmpty(),
        ]);
        if ($validation->failed()) {
            return $res->withJson($validation->errors)->withStatus(400);
        }
        if (User::where('email', $req->getParam('email'))->count() > 0) {
            return $res->withJson(['message' => 'This e-mail already exist. Try other e-mail', 'error' => '400'])->withStatus(400);
        }
        //$token = $this->makeToken();
        $salon = Salon::where('salon_id', $req->getParam('salon_id'))->first();
        $worker = $salon->workers()->create(['email' => $req->getParam('email')]);
        //$worker = Worker::create($req->getParams());
        $user = $worker->user()->create($req->getParams());
        $chars = "qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP";
        $max = 8;
        $password = null;
        while ($max--)
            $password .= $chars[rand(0, 61)];
        $user->password = $password;
        $user->confirm_email = true;
        $user->save();

        //$confirm = $user->user_id;

        $mail = new EmailController();

        $user_name = $req->getParam('last_name') . " " . $req->getParam('first_name');
        $mail->AddAddress($req->getParam('email'), $user_name); // Получатель
        $mail->Subject = htmlspecialchars('Salon ' . $salon->business_name . ' added you as HairMaster');  // Тема письма
        $letter_body = '
<head>
<title>Download HairTime application and register</title>
</head>
<body>
<img alt="HairTime" src="https://hairtime.co.il/img/image.jpg" style="float: right; align-items:right; width: 400px; height: 107px;" />
<br>
<h1>&nbsp;</h1>

<h1>&nbsp;</h1>

<h2>Dear ' . $user_name . '</h2>
<p>We invite you to register in HairTime application as Workers! We just need to complete your registration. Download 
"HairTime" applications and complete your registration.<br><br>
<a href="https://play.google.com/apps/testing/com.haduken.hairtime" title="Go!!!">Download application</a><br><br>
For SingIn in to application use this e-mail as Username: ' . $req->getParam('email') . ' <br> and temporary password: ' . $password . '
<br><br>
If you have any issues confirming your email we will be happy to help you. You can contact us on 
<a href="mailto:admin@hairtime.co.il">admin@hairtime.co.il</a></p><br>

<p>With best regards, <br /><br>

The HairTime Team.</p>';

        $mail->MsgHTML($letter_body); // Текст сообщения
        $mail->AltBody = "Dear " . $user_name . ", confirm your email, please. Copy next string to your browser and press enter: https://play.google.com/apps/testing/com.haduken.hairtime";
        $result = $mail->Send();

        if ($result) {
            return $res->withJson(['user_id' => $user->user_id, 'worker_id' => $user->entry_id,
                'confirm_email' => '0', 'email-status' => 'successfully sent'])->withStatus(201);
        } else {

            return $res->withJson(['user_id' => $user->user_id, 'worker_id' => $user->entry_id,
                'confirm_email' => '0', 'email-status' => $mail->ErrorInfo])->withStatus(400);
        }
    }
    public function singupWorker(Request $req, Response $res)
    {
        $validation = $this->validator;
        $validation->validate($req, [
            'email' => v::notEmpty()->email()->length(5, 255),
            'first_name' => v::noWhitespace()->notEmpty()->length(1, 100),
            'last_name' => v::noWhitespace()->notEmpty()->length(1, 100),
            'specialization' => v::notEmpty()->length(1, 100),
            'start_year' => v::between(1980, date("Y")),
            'password' => v::notEmpty()->length(1, 50), //TODO: turn off debug mode
            //'salon_id' => v::notEmpty(),
            'phone' => v::phone(),
            'logo' => v::optional(v::url()->length(1, 100))
        ]);
        if ($validation->failed()) {
            return $res->withJson($validation->errors)->withStatus(400);
        }

        $token = $this->makeToken();
        $user = User::where('email', $req->getParam('email'))->first();
        if ($user == null) {
            return $res->withJson(['message' => 'User with this email not found', 'error' => '404'])->withStatus(404);
        }
        $user->confirm_email = true;
        $user->password = $req->getParam('password');
        $user->save();
        $user->tokens()->create(['token' => $token]);
        $worker = Worker::where('worker_id', $user->entry_id)->first();
        $worker->first_name = $req->getParam('first_name');
        $worker->last_name = $req->getParam('last_name');
        $worker->specialization = $req->getParam('specialization');
        $worker->start_year = $req->getParam('start_year');
        $worker->phone = $req->getParam('phone');
        $worker->logo = $req->getParam('logo');
        $worker->save();
        return $res->withJson($worker->toArray() + $user->toArray())->withStatus(201);

    }

    public function editWorker(Request $req, Response $res)
    {
        $validation = $this->validator;
        $validation->validate($req, [
            'email' => v::notEmpty()->email()->length(5, 255),
            'first_name' => v::noWhitespace()->notEmpty()->length(1, 100),
            'last_name' => v::noWhitespace()->notEmpty()->length(1, 100),
            'specialization' => v::notEmpty()->length(1, 100),
            'start_year' => v::between(1980, date("Y")),
            'password' => v::notEmpty()->length(1, 50),
            //'salon_id' => v::notEmpty(),
            'phone' => v::phone(),
            //'logo' => v::optional(v::url()->length(1, 100))
        ]);
        if ($validation->failed()) {
            return $res->withJson($validation->errors)->withStatus(400);
        }
        $user = User::where('email', $req->getParam('email'))->first();

        $worker = Worker::where('worker_id', $user->entry_id)->first();
        $worker->first_name = $req->getParam('first_name');
        $worker->last_name = $req->getParam('last_name');
        $worker->specialization = $req->getParam('specialization');
        $worker->start_year = $req->getParam('start_year');
        $worker->phone = $req->getParam('phone');
        $worker->password = $req->getParam('password');
        $worker->save();
        //$token = $this->makeToken();
        //$user->tokens()->create(['token' => $token]);
        //$user->save();

        return $res->withJson($worker)->withStatus(201);

    }

    public function singin(Request $req, Response $res)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'email' => v::notEmpty()->email()->length(5, 255),
            'password' => v::notEmpty()->length(1, 50) //TODO: turn off debug mode
        ));
        if ($validation->failed()) {
            return $res->withJson($validation->errors)->withStatus(400);
        }

        $user = User::where('email', $req->getParam('email'))->first();

        if ($user->password !== $req->getParam('password')) {
            return $res->withStatus(403)
                ->withJson(['message' => 'Wrong password', 'error' => '403']);
        } else {

            $token = $this->makeToken();
            $user->tokens()->create(['token' => $token]);
            $type = mb_strtolower(explode("\\", $user->entry_type)[2]);

            $login_data = ['user_id' => $user->user_id, 'token' => $token, $type . '_id' => $user->entry_id,
                'confirm_email' => $user->confirm_email];

            return $res->withJson($user->getEntry()->toArray() + $login_data)->withStatus(201);
        }
    }

    public function singout(Request $req, Response $res)
    {
        $id = intval($req->getHeader('User-ID')[0]);
        $token = $req->getHeader('Token')[0];
        $user = User::where('user_id', 63)->first();


        $login_data = ['user_id' => $user->user_id, 'token' => $token, 'type' => explode('\\', $user->entry_type)[2],
            'status' => 'singout'];
        Token::deleteOne($id, $token);
        return $res->withJson($login_data)->withStatus(200);
    }


    public function newPassword(Request $req, Response $res)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'password' => v::notEmpty()->length(1, 50) //TODO: turn off debug mode
        ));
        if ($validation->failed()) {
            return $res->withJson($validation->errors)->withStatus(400);
        }

        $id = $req->getHeader('User-ID');
        Token::deleteAll($id);
        User::changePassword($id, $req->getParam('password'));

        return $res;
    }

    public function delUser(Request $req, Response $res)
    {
        $validation = $this->validator;
        $validation->validate($req, [
            'email' => v::notEmpty()->email()->length(5, 255)
        ]);
        if ($validation->failed()) {
            return $res->withJson($validation->errors)->withStatus(400);
        }
        $user = User::where('email', $req->getParam('email'))->first();
        $user_name = $user->email;
        $user_id = $user->user_id;

        if (isset($user)) {
            if (explode('\\', get_class($user->getEntry()))[2] == "Salon") {
                $user_type = 'Salon';
                $salon = Salon::where('salon_id', $user->entry_id)->first();
                $workers = Worker::all()->where('salon_id', 19);
                $i = 0;
                foreach ($workers as $worker) {
                    $w_array = ['Workers_' . $i => 'id: ' . $worker->worker_id . ' deleted'];
                    $i++;
                    //$worker->delete();
                }
                $salon->delete();
            } elseif (explode('\\', get_class($user->getEntry()))[2] == "Worker") {
                $user_type = 'Worker';
                $worker = Worker::where('worker_id', $user->entry_id)->first();
                $worker->delete();
            } elseif (explode('\\', get_class($user->getEntry()))[2] == "Customer") {
                $user_type = 'Customer';
                $customer = Customer::where('customer_id', $user->entry_id)->first();
                $customer->delete();
            }
        } else {
            return $res->withJson(['user' => 'not found'])->withStatus(400);
        }
        $user->delete();
        if (isset($w_array)) {
            return $res->withJson(['user' => $user_name . ' ID ' . $user_id, 'user_type' => $user_type, 'status' => 'deleted'
                ] + $w_array)->withStatus(200);
        } else {
            return $res->withJson(['user' => $user_name . ' ID ' . $user_id, 'user_type' => $user_type, 'status' => 'deleted'
            ])->withStatus(200);
        }
    }

    protected function makeToken()
    {
        return sha1(random_bytes(40));
    }


}





