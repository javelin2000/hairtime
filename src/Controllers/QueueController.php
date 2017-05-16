<?php
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 12.05.2017
 * Time: 15:14
 */

namespace App\Controllers;

use App\Models\Queue;
use App\Models\Schedule;
use App\Models\User;
use DateTime;
use Illuminate\Support\Facades\DB;
use Slim\Http\Request;
use Slim\Http\Response;

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

    public function addQueue(Request $req, Response $res, $args)
    {

        //$week_day = strftime("%u", strtotime($req->getParam('time')));
        //$customer_id = $req->getParam('customer_id');

        $queue = Queue::create($req->getParams() + $args);

        return $res->withJson($queue)->withStatus(201);
    }
}