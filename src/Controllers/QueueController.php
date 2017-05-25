<?php
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 12.05.2017
 * Time: 15:14
 */

namespace App\Controllers;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\NToken;
use App\Models\Queue;
use App\Models\Salon;
use App\Models\Schedule;
use App\Models\Service;
use App\Models\User;
use App\Models\Worker;
use DateTime;
use Illuminate\Support\Facades\DB;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as v;


class QueueController
{
    public function deleteQueue(Request $req, Response $res, $args)
    {
        $user = User::where('user_id', $req->getHeader('User-ID'))->first();
        $queue = Queue::where('queue_id', $args['queue_id'])->first();


        if ($user->entry_id == $queue->customer_id) {
            $id = $queue->queue_id;
            $result = $queue->delete();
            if ($result) {
                return $res->withJson(['message' => 'Queue id No ' . $id . ' deleted successful.', 'error' => ''])->withStatus(200);
            } else {
                return $res->withJson(['message' => 'Queue id No ' . $id . ' not deleted.', 'error' => 'Something wrong'])->withStatus(200);
            }
        }
        return $res->withJson(['message' => 'This Queue make other Customer. Check you User ID ', 'error' => '403'])->withStatus(403);

    }

    public function getSalonQueue(Request $req, Response $res, $args)
    {
        $is_from = $req->getParam('from');
        if (isset($is_from)) {
            $from = \DateTime::createFromFormat("d.m.Y", $is_from)->format("Y-m-d H:m:s");
        } else {
            $today = new DateTime();
            $from = $today->format("Y-m-d H:m:s");
        }
        $is_to = $req->getParam('to');
        if (isset($is_to)) {
            $to = \DateTime::createFromFormat("d.m.Y", $req->getParam('to'))->format("Y-m-d H:m:s");
        } else {
            $to = null;
        }
        // return $res->withJson(['to'=>$to,'from'=>$from])->withStatus(200);

        $customers_id = Queue::join('services', 'services.service_id', '=', 'queue.service_id')->
        where('salon_id', $args['salon_id'])->pluck('customer_id');
        $customers_id = array_unique($customers_id->toArray());
        $i = 0;
        foreach ($customers_id as $value) {
            $customer = Customer::where('customer_id', $value)->first();
            $result[$i]['customer'] = $customer->toArray();
            if (isset($to)) {
                $queue = Queue::join('services', 'services.service_id', '=', 'queue.service_id')->
                where('queue.customer_id', $value)->where('time', '>', $from)->
                where('time', '<', $to)->get();
            } else {
                $queue = Queue::join('services', 'services.service_id', '=', 'queue.service_id')->
                where('queue.customer_id', $value)->where('time', '>', $from)->get();
            }

            $j = 0;
            foreach ($queue as $value1) {
                $result[$i]['customer']['queue'][$j] = $value1;
                $worker = Worker::where('worker_id', $value1['worker_id'])->first();
                $result[$i]['customer']['queue'][$j]['first_name'] = $worker->first_name;
                $result[$i]['customer']['queue'][$j]['last_name'] = $worker->last_name;
                $result[$i]['customer']['queue'][$j]['phone'] = $worker->phone;
                $j++;
            }
            $i++;
        }
        return $res->withJson($result)->withStatus(200);
    }


    public function getCustomerQueue(Request $req, Response $res, $args)
    {
        $customer = intval($args['customer_id']);
        $is_from = $req->getParam('from');
        if (isset($is_from)) {
            $from = \DateTime::createFromFormat("d.m.Y", $is_from)->format("Y-m-d H:m:s");
        } else {
            $today = new DateTime();
            $from = $today->format("Y-m-d H:m:s");
        }
        $is_to = $req->getParam('to');
        if (isset($is_to)) {
            $to = \DateTime::createFromFormat("d.m.Y", $req->getParam('to'))->format("Y-m-d H:m:s");
        } else {
            $to = null;
        }
        // return $res->withJson(['to'=>$to,'from'=>$from])->withStatus(200);
        $salons_id = Queue::join('services', 'services.service_id', '=', 'queue.service_id')->
        join('salons', 'salons.salon_id', '=', 'services.salon_id')->
        where('customer_id', $args['customer_id'])->pluck('salons.salon_id');
        $salons_id = array_unique($salons_id->toArray());
        $i = 0;
        foreach ($salons_id as $value) {
            $salon = Salon::where('salon_id', $value)->first();
            $result[$i]['salons'] = $salon->toArray();
            if (isset($to)) {
                $queue = Queue::join('services', 'services.service_id', '=', 'queue.service_id')->
                join('salons', 'salons.salon_id', '=', 'services.salon_id')->
                where('salons.salon_id', $value)->where('time', '>', $from)->
                where('time', '<', $to)->get();
            } else {
                $queue = Queue::join('services', 'services.service_id', '=', 'queue.service_id')->
                join('salons', 'salons.salon_id', '=', 'services.salon_id')->
                where('salons.salon_id', $value)->where('queue.customer_id', intval($customer))->
                where('time', '>', $from)->get();
            }
            $j = 0;
            foreach ($queue as $value1) {
                $result[$i]['salons']['queue'][$j]['queue_id'] = $value1['queue_id'];
                $result[$i]['salons']['queue'][$j]['service_id'] = $value1['service_id'];
                $result[$i]['salons']['queue'][$j]['worker_id'] = $value1['worker_id'];
                $worker = Worker::where('worker_id', $value1['worker_id'])->first();
                $result[$i]['salons']['queue'][$j]['first_name'] = $worker->first_name;
                $result[$i]['salons']['queue'][$j]['last_name'] = $worker->last_name;
                $result[$i]['salons']['queue'][$j]['phone'] = $worker->phone;
                $result[$i]['salons']['queue'][$j]['customer_id'] = $value1['customer_id'];
                $result[$i]['salons']['queue'][$j]['status'] = $value1['status'];
                $result[$i]['salons']['queue'][$j]['time'] = $value1['time'];
                $result[$i]['salons']['queue'][$j]['name'] = $value1['name'];
                $result[$i]['salons']['queue'][$j]['duration'] = $value1['duration'];
                $result[$i]['salons']['queue'][$j]['price_min'] = $value1['price_min'];
                $result[$i]['salons']['queue'][$j]['price_max'] = $value1['price_max'];
                $result[$i]['salons']['queue'][$j]['logo'] = $value1['logo'];
                $j++;
            }

        }
        return $res->withJson($result)->withStatus(200);

    }

    public function getQueue(Request $req, Response $res, $args)
    {
        $from = \DateTime::createFromFormat("d.m.Y", $req->getParam('from'))->format("Y-m-d");
        $to = \DateTime::createFromFormat("d.m.Y", $req->getParam('to'))->format("Y-m-d");

        $queue = Queue::join('services', 'services.service_id', '=', 'queue.service_id')->
        where('worker_id', $args['worker_id'])->where('time', '>', $from)->
        where('time', '<', $to)->orderBy('time')->get();
        return $res->withJson($queue)->withStatus(200);

        /*$schedules = Schedule::where('worker_id', $args['worker_id'])->select('day')->distinct()->get();
        $schedules_count = count($schedules->toArray());
        $today = new DateTime();
        //$tomorrow = new DateTime();
        //$tomorrow->modify('+1 DAY');
        //$tomorrow = $today->modify('+1 DAY');
        $week_day = date("w");
        if ($week_day == 0 ){$week_day = 7;}
        //return $res->withJson(['sf'=>$today->format("Y-m-d"), 'sdf'=>$tomorrow->format("Y-m-d")])->withStatus(200);

        for ($i=$week_day;$schedules_count;$i++){
            $result[$i]['date']= $today->format('Y-m-d H:m');
            $schedules = Schedule::where('day', $i)->orderBy('start')->get();
            //return $res->withJson($schedules)->withStatus(200);
            $queue = Queue::join('services', 'services.service_id', '=', 'queue.service_id')->
                where('worker_id', $args['worker_id'])->where('time', '>', $today->format('Y-m-d'))->
                where('time', '<', $today->modify('+1 DAY')->format('Y-m-d'))->orderBy('time')->get();
            $j=1;
            foreach ($schedules as $schedule){
                $result[$i]['day']= $i;
                $result[$i]['start']= $schedule->start;
                $result[$i]['stop']= $schedule->stop;

                $time_start = mktime(explode(':', $schedule->start)[0], explode(':', $schedule->start)[1]);
                $time_stop = mktime(explode(':', $schedule->stop)[0], explode(':', $schedule->stop)[1]);
                foreach ($queue as $value){
                    if ($time_start <= mktime($value->time) && $time_stop >= mktime($value->time)) {
                        $result[$i][$j]['queue']['start'] = data ('H:m',$value->time);
                        $result[$i][$j]['queue']['stop'] = data ('H:m', mktime($value->time));

                    }
                }

                $result[$i][$j]['start']=
                $j++;
            }
            return $res->withJson($result)->withStatus(200);
        }
        //Sample::select('link')->distinct()->count();
        return $res->withJson($schedules)->withStatus(200);


        //return $res->withJson(['sgf'=>$week_day])->withStatus(201);

        return $res->withJson($schedules)->withStatus(200);*/


    }

    public function confirmQueue(Request $req, Response $res, $args)
    {
        $queue = Queue::where('queue_id', $args['queue_id'])->first();
        $queue->status = 2;
        $queue->save();
        $customer = Customer::where('customer_id', $queue->customer_id)->first();
        $email = User::where('entry_id', $queue->customer_id)->
        where('entry_type', 'App\Models\Customer')->pluck('email')->first();
        //return $res->withJson($email)->withStatus(200);
        $mail = new EmailController();
        $user_name = $customer->last_name . " " . $customer->first_name;
        $mail->AddAddress($email, $user_name); // Получатель
        $mail->Subject = htmlspecialchars('You have new queue!');  // Тема письма
        $letter_body = '
<head>
<title>Your queue has been confirmed!</title>
</head>
<body>
<img alt="HairTime" src="https://hairtime.co.il/img/image.jpg" style="float: left; width: 400px; height: 107px;" />
<br>
<h1>&nbsp;</h1>

<h1>&nbsp;</h1>

<h2>Dear ' . $user_name . '</h2>
<p>Your queue has been confirmed! If you want to check it, please visit to the application.</p>
<br>
<p>If you have any question we will be happy to help you. You can contact us on 
<a href="mailto:admin@hairtime.co.il">admin@hairtime.co.il</a></p>

<p>With best regards, <br /><br>

The HairTime Team.</p>';
        $mail->MsgHTML($letter_body); // Текст сообщения
        $mail->AltBody = "Dear " . $user_name . ", You have new queue!";
        $result = $mail->Send();
        return $res->withJson($mail)->withStatus(200);

    }


    public function addQueue(Request $req, Response $res, $args)
    {

        //$week_day = strftime("%u", strtotime($req->getParam('time')));
        //$customer_id = $req->getParam('customer_id');
        $time = date('Y-m-d H:i:s', strtotime($req->getParam('time')));
        $queue = Queue::create(['time' => $time, 'customer_id' => $req->getParam('customer_id')] + $args);
        $message = array('message' => 'Dear worker you are have new queue!');
        $user = User::where('entry_type', 'App\Models\Worker')->where('entry_id', $args['worker_id'])->first();
        $worker = Worker::where('worker_id', $args['worker_id'])->first();
        //$customer = Customer::where('customer_id',$req->getParam('customer_id'))->first();
        $ntoken = NToken::where('user_id', $user->user_id)->pluck('n_token');
        $notification = new Notification();
        $result = $notification->send_notifications($ntoken, $message);
        $notification->queue_id = $queue->queue_id;
        $notification->message = $message;
        $notification->save();
        $email = $user->email;

        $mail = new EmailController();
        $user_name = $worker->last_name . " " . $worker->first_name;
        $mail->AddAddress($email, $user_name); // Получатель
        $mail->Subject = htmlspecialchars('You have new queue!');  // Тема письма
        $letter_body = '
<head>
<title>You have new queue!</title>
</head>
<body>
<img alt="HairTime" src="https://hairtime.co.il/img/image.jpg" style="float: left; width: 400px; height: 107px;" />
<br>
<h1>&nbsp;</h1>

<h1>&nbsp;</h1>

<h2>Dear ' . $user_name . '</h2>
<p>You have new queue! Please visit to the application.</p>
<br>
<p>If you have any question we will be happy to help you. You can contact us on 
<a href="mailto:admin@hairtime.co.il">admin@hairtime.co.il</a></p>

<p>With best regards, <br /><br>

The HairTime Team.</p>';
        $mail->MsgHTML($letter_body); // Текст сообщения
        $mail->AltBody = "Dear " . $user_name . ", You have new queue!";
        $result = $mail->Send();

        return $res->withJson($queue)->withStatus(200);
    }
}