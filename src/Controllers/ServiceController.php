<?php
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 10.04.2017
 * Time: 0:03
 */

namespace App\Controllers;

use App\Models\Salon;
use App\Models\Service;
use App\Models\Service_worker;
use App\Models\ServiceWorker;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Support\Facades\DB;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class ServiceController extends BaseController
{
    function new(Request $req, Response $res, $args)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'name' => v::notEmpty()->length(1, 100),
            'duration' => v::notEmpty()->length(1, 11),
            'price_min' => v::notEmpty()->length(1, 5),
            'price_max' => v::notEmpty()->length(1, 11),
        ));
        if ($validation->failed())
            return $res->withJson($validation->errors)->withStatus(400);

        $salon_id = $args['salon_id'];
        $service = new Service();
        $service->salon_id = $salon_id;
        $service->name = $req->getParam('name');
        $service->duration = $req->getParam('duration');
        $service->price_min = $req->getParam('price_min');
        $service->price_max = $req->getParam('price_max');

        $id = $service->create($service->toArray());

        return $res->withJson($id->toArray())->withStatus(201);
    }

    function getBySalon(Request $req, Response $res, $args)
    {
        $salon_id = $args['salon_id'];
        if (Salon::where('salon_id', $salon_id)->exists()) {
            $services = Service::where('salon_id', $salon_id)->get();
            $i = 0;
            foreach ($services as $service) {
                $sw = ServiceWorker::where('service_id', $service['service_id'])->get();
                $result[$i] = $service;
                $workers = array();
                $j = 0;
                foreach ($sw as $value) {
                    $worker = Worker::where('worker_id', $value['worker_id'])->first();
                    $worker = $worker->toArray();
                    $worker['worker_id'] = $value['worker_id'];
                    $worker['description'] = $value['description'];
                    $workers[$j] = $worker;
                    $j++;
                }
                $result[$i]['workers'] = $workers;
                $i++;
            }
        } else {
            return $res->withJson(['message' => "Salon with such salon_id is not found. Check salon_id.", 'error' => "404"])->withStatus(404);
        }
        return $res->withJson($result)->withStatus(200);


    }

    public function edit(Request $req, Response $res, $args)
    {
        $validation = $this->validator;
        $validation->validate($req, array(
            'name' => v::notEmpty()->length(1, 100),
            'duration' => v::notEmpty()->length(1, 11),
            'price_min' => v::notEmpty()->length(1, 5),
            'price_max' => v::notEmpty()->length(1, 11),
        ));
        if ($validation->failed())
            return $res->withJson($validation->errors)->withStatus(400);

        $service = Service::where('service_id', $args['service_id'])->first();
        if ($service->salon_id == $args['salon_id']) {
            $service->name = $req->getParam('name');
            $service->duration = $req->getParam('duration');
            $service->price_min = $req->getParam('price_min');
            $service->price_max = $req->getParam('price_max');
            $service->save();
            return $res->withJson($service->toArray())->withStatus(200);
        } else {
            return $res->withJson(['message' => "This service created by other Salon. Check service_id.", 'error' => "403"])->withStatus(403);
        }
    }

    public function delete(Request $req, Response $res, $args)
    {
        $service = Service::where('service_id', $args['service_id'])->first();
        if (!isset($service)) {
            return $res->withJson(['message' => "This service does not exist. Check service_id.", 'error' => "403"])->withStatus(403);
        }
        if ($service->salon_id == $args['salon_id']) {
            ServiceWorker::where('service_id', $args['service_id'])->delete();
            $service->delete();
            return $res->withJson(['service_id' => $args['service_id'], 'salon_id' => $args['salon_id'], 'status' => 'deleted'
            ])->withStatus(200);
        } else {
            return $res->withJson(['message' => "This service created by other Salon. Check service_id.", 'error' => "403"])->withStatus(403);
        }
    }

    function getByWorker(Request $req, Response $res, $args)
    {
        $worker_id = $args['worker_id'];
        if (Worker::where('worker_id', $worker_id)->exists()) {
            $worker_id = $args['worker_id'];
            $i = 0;
            $sw = ServiceWorker::where('worker_id', $worker_id)->get();
            foreach ($sw as $value) {
                $result[$i] = Service::where('service_id', $value['service_id'])->first();
                $result[$i]['worker'] = Worker::where('worker_id', $worker_id)->first();
                $result[$i]['worker']['description'] = $value['description'];
                $i++;
            }
            return $res->withJson($result)->withStatus(200);
        } else {
            return $res->withJson(['message' => "Worker with such worker_id is not found. Check worker_id.", 'error' => "404"])->withStatus(404);
        }
    }

    function newByWorker(Request $req, Response $res, $args)
    {
        $worker_id = $args['worker_id'];
        $worker = Worker::where('worker_id', $worker_id)->first();
        $service = Service::where('service_id', $args['service_id'])->first();
        if ($worker->salon_id != $service->salon_id) {
            return $res->withJson(['message' => "This Worker does not work in this Salon. Check worker_id or salon_id.", 'error' => "404"])->withStatus(404);
        }
        if (ServiceWorker::where('service_id', $args['service_id'])->where('worker_id', $args['worker_id'])->exists()) {
            return $res->withJson(['message' => "This Service already added. Check worker_id or service_id.", 'error' => "403"])->withStatus(403);
        }
        if (isset($worker) AND isset($service)) {
            $sw = new ServiceWorker();
            $sw->service_id = $service->service_id;
            $sw->worker_id = $args['worker_id'];
            $sw->description = $req->getParam('description');
            $sw->save();
            $result = $service->toArray();
            $worker = $worker->toArray();
            $worker['worker_id'] = $args['worker_id'];
            $worker ['description'] = $sw->description;
            $result['worker'] = $worker;
            return $res->withJson($result)->withStatus(201);
        } else {
            return $res->withJson(['message' => "Something wrong. Check worker_id or service_id.", 'error' => "404"])->withStatus(404);
        }
    }

    function editByWorker(Request $req, Response $res, $args)
    {
        $worker_id = $args['worker_id'];
        $worker = Worker::where('worker_id', $worker_id)->first();
        $service = Service::where('service_id', $args['service_id'])->first();
        if ($worker->salon_id != $service->salon_id) {
            return $res->withJson(['message' => "This Worker does not work in this Salon. Check worker_id or salon_id.", 'error' => "404"])->withStatus(404);
        }
        if (!ServiceWorker::where('service_id', $args['service_id'])->where('worker_id', $args['worker_id'])->exists()) {
            return $res->withJson(['message' => "This Service does not exists for this Worker. Check worker_id or service_id.", 'error' => "403"])->withStatus(403);
        }
        if (isset($worker) AND isset($service)) {
            $sw = ServiceWorker::where('service_id', $args['service_id'])->where('worker_id', $args['worker_id'])->first();
            $sw->description = $req->getParam('description');
            $sw->save();
            $result = $service->toArray();
            $worker = $worker->toArray();
            $worker['worker_id'] = $args['worker_id'];
            $worker ['description'] = $sw->description;
            $result['worker'] = $worker;
            return $res->withJson($result)->withStatus(201);
        } else {
            return $res->withJson(['message' => "Something wrong. Check worker_id or service_id.", 'error' => "404"])->withStatus(404);
        }
    }

    function deleteByWorker(Request $req, Response $res, $args)
    {
        $worker_id = $args['worker_id'];
        $worker = Worker::where('worker_id', $worker_id)->first();
        $service = Service::where('service_id', $args['service_id'])->first();
        if ($worker->salon_id != $service->salon_id) {
            return $res->withJson(['message' => "This Worker does not work in this Salon. Check worker_id or salon_id.", 'error' => "404"])->withStatus(404);
        }
        $sw = ServiceWorker::where('service_id', $args['service_id'])->where('worker_id', $args['worker_id'])->first();
        if (isset($sw)) {
            $status = $sw->delete();
        } else {
            return $res->withJson(['message' => "This Service for this Worker does not found. Check worker_id or service_id.", 'error' => "404"])->withStatus(404);
        }
        if ($status) {
            return $res->withJson(['service_id' => $args['service_id'], 'worker_id' => $args['worker_id'], 'status' => 'deleted'
            ])->withStatus(200);
        }

    }

    protected function getSalonId(Request $req)
    {
        return User::find($this->gerUserId($req))->getEntry()->salon_id;
    }

    protected function gerUserId(Request $req)
    {
        return $req->getHeader('User-ID')[0];
    }
}