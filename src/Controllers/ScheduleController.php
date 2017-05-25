<?php
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 01.05.2017
 * Time: 11:16
 */

namespace App\Controllers;

use App\Models\Schedule;
use App\Models\Worker;
use Slim\Http\Request;
use Slim\Http\Response;
use Respect\Validation\Validator as v;

class ScheduleController extends BaseController
{


    public function getSchedule(Request $req, Response $res, $args)
    {
        $schedules = Schedule::where('worker_id', $args['worker_id'])->orderBy('day')->orderBy('start')->get();
        foreach ($schedules as $schedule) {

        }
        return $res->withJson($schedules)->withStatus(200);
    }

    public function newJSONSchedule(Request $req, Response $res, $args)
    {
        $in = $req->getParams();
        return $res->withJson($in)->withStatus(200);
    }

    public function newSchedule(Request $req, Response $res, $args)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'day' => v::notEmpty()->digit()
        ));
        if ($validation->failed()) {
            return $res->withJson($validation->errors)->withStatus(400);
        }
        $worker = Worker::where('worker_id', $args['worker_id'])->first();
        $schedules = Schedule::where('worker_id', $args['worker_id'])->where('day', $req->getParam('day'))->orderBy('start')->get();

        //return $res->withJson($schedules)->withStatus(200);
        $time = explode(':', $req->getParam('start'));
        $in_time_start = mktime($time[0] . ':' . $time[1]);
        $time = explode(':', $req->getParam('stop'));
        $in_time_stop = mktime($time[0] . ':' . $time[1]);
        //return $res->withJson(['start'=>$in_time_start, 'stop'=> $in_time_stop])->withStatus(200);
        foreach ($schedules as $schedule) {
            $db_time_start = mktime(explode(':', $schedule->start)[0], explode(':', $schedule->start)[1]);
            $db_time_stop = mktime(explode(':', $schedule->stop)[0], explode(':', $schedule->stop)[1]);
            if ($db_time_stop > $in_time_start AND $db_time_stop < $in_time_stop) {
                return $res->withJson(['message' => 'in this day No ' . $schedule->day . ' error! The schedule is overpaid', 'error' => '400'])->withStatus(400);
            }
            if (($db_time_start < $in_time_stop) && ($db_time_start > $in_time_start)) {
                return $res->withJson(['message' => 'in this day No ' . $schedule->day . ' error! The schedule is overpaid', 'error' => '400'])->withStatus(400);

            }
        }
        $newSchedule = Schedule::create($req->getParams() + ['worker_id' => $args['worker_id']]);
        $newSchedule = $newSchedule->toArray();
        return $res->withJson($newSchedule)->withStatus(201);
    }

    public function deleteSchedule(Request $req, Response $res, $args)
    {
        $schedule = Schedule::where('worker_id', $args['worker_id'])->where('schedule_id', $args['schedule_id'])->first();
        //return $res->withJson($schedule)->withStatus(201);
        $result = $schedule->delete();
        if ($result) {
            return $res->withJson(['message' => "Successfully deleted", 'error' => ""])->withStatus(201);
        } else {
            return $res->withJson(['message' => "Something wrong, NOT deleted", 'error' => ""])->withStatus(400);
        }
    }
}