<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 28.11.2016
 * Time: 16:35
 */

namespace App\Controllers;

use App\Models\Salon;
use App\Models\Service;
use App\Models\ServiceWorker;
use App\Models\Worker;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class WorkerController extends BaseController
{
    public function add(Request $req, Response $res, $args)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'extention' => v::alpha()->noWhitespace()->notEmpty()->length(1, 3),
            'image' => v::notEmpty()->noWhitespace()
        ));
        if ($validation->failed()) {
            return $res->withJson($validation->errors)->withStatus(400);
        }
    }

    public function getWorkers(Request $req, Response $res, $args)
    {
        $workers = Worker::where('salon_id', $args['salon_id'])->get();
        return $res->withJson($workers->toArray())->withStatus(200);
    }

    public function getWorkersService(Request $req, Response $res, $args)
    {
        $worker = Worker::where('worker_id', $args['worker_id'])->first();
        $sw = ServiceWorker::where('worker_id', $args['worker_id'])->get();
        $worker = $worker->toArray();
        $i = 0;
        foreach ($sw as $value) {
            $service = Service::where('service_id', $value['service_id'])->first();
            $result[$i] = $service->toArray();
            $i++;
        }
        return $res->withJson($result)->withStatus(200);
    }
}