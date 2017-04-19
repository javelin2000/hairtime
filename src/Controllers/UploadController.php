<?php
/**
 * Created by PhpStorm.
 * User: yemelianov
 * Date: 16.03.17
 * Time: 19:16
 */

namespace App\Controllers;

use App\Models\Customer;
use App\Models\Salon;
use App\Models\Service;
use App\Models\ServiceWorker;
use App\Models\Token;
use App\Models\User;
use App\Models\Worker;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class UploadController extends BaseController
{
    function uploadFile(Request $req, Response $res)
    {

        $validation = $this->validator;
        $validation->validate($req, array(
            'user_id' => v::notEmpty()
        ));
        if ($validation->failed()) {
            return $res->withJson($validation->errors)->withStatus(400);
        }
        //$token = $req->getParam('token');
        $user_id = $req->getParam('user_id');
        //return $res->withJson(['message' =>  $user_id, 'error' =>"400", 'success' => 'false'])->withStatus(400);

        if (!isset($_FILES['uploads'])) {
            return $res->withJson(['message' => "No files uploaded!!", 'error' => "400", 'success' => 'false'])
                ->withStatus(400);
        }
        $files = $_FILES['uploads'];
        //return $res->withJson(['message' => $files, 'error' =>"400", 'success' => $user_id])->withStatus(200);

        if ($files['error'] == 0) {
            $name = uniqid('img-' . date('Ymd') . '-');
            //return $res->withJson(['message' => $files, 'error' =>'uploads/' . $name, 'success' => $user_id])->withStatus(200);

            if (move_uploaded_file($files['tmp_name'], 'uploads/' . $name) == true) {
                //return $res->withJson(['message' => 'loaded!', 'error' =>'uploads/' . $name, 'success' => $user_id])->withStatus(200);

                $user = User::where('user_id', $user_id)->first();
                //$user_type = $user->getEntry();
                //return $res->withJson(['message' => $user, 'error' =>"400", 'success' => ' ok' ])->withStatus(200);
                if ($user->entry_type == 'App\Models\Customer') {
                    $customer = Customer::where('customer_id', $user->entry_id)->first();
                    $customer->logo = 'http://hairtime.co.il/uploads/' . $name;
                    $customer->save();
                    return $res->withJson(['url' => $customer->logo, 'message' => 'file ' . $files['name'][0] . ' uploaded and saved'])
                        ->withStatus(200);
                } elseif ($user->entry_type == 'App\Models\Salon') {
                    $salon = Salon::where('salon_id', $user->entry_id)->first();
                    $salon->logo = 'http://hairtime.co.il/uploads/' . $name;
                    $salon->save();
                    return $res->withJson(['url' => $salon->logo, 'message' => 'file ' . $name . ' uploaded and saved'])
                        ->withStatus(200);
                } elseif ($user->entry_type == 'App\Models\Worker') {

                }
                return $res->withJson(['url' => 'http://hairtime.co.il/uploads/' . $name, 'message' => 'file ' . $files['name'][0] . ' uploaded'])
                    ->withStatus(200);
            } else {
                return $res->withJson(['message' => "No files uploaded!!", 'error' => move_uploaded_file($files['tmp_name'][0], 'uploads/' . $name), 'success' => 'false'])
                    ->withStatus(400);
            }
        } else {
            return $res->withJson(['message' => $files, 'error' => $files['error'][0], 'success' => $user_id])->withStatus(200);
        }

    }


    function uploadService(Request $req, Response $res, $args)
    {
        //return $res->withJson(['message' => 'salon', 'error'=> '403'])->withStatus(403);
        $user_id = $req->getHeader('User-ID');
        $service_id = $args['service_id'];
        $user = User::where('user_id', $user_id)->first();
        //return $res->withJson(['message' =>  $user_id, 'error' =>"400", 'success' => 'false'])->withStatus(400);

        if (!isset($_FILES['uploads'])) {
            return $res->withJson(['message' => "No files uploaded!!", 'error' => "204"])
                ->withStatus(204);
        }
        $files = $_FILES['uploads'];
        //return $res->withJson(['message' => $files, 'error' =>"400", 'success' => $user_id])->withStatus(200);
        if ($files['error'] == 0) {

            if ($user->entry_type == 'App\Models\Salon') {
                $name = uniqid('salon-service-' . date('Ymd') . '-');
            } elseif ($user->entry_type == 'App\Models\Worker') {
                $name = uniqid('worker-service-' . date('Ymd') . '-');
            } elseif ($user->entry_type == 'App\Models\Customer') {
                return $res->withJson(['message' => 'User can\'t load Service logo.', 'error' => '403'])
                    ->withStatus(403);
            }

            //return $res->withJson(['message' => $files, 'error' =>'uploads/' . $name, 'success' => $user_id])->withStatus(200);

            if (move_uploaded_file($files['tmp_name'], 'uploads/' . $name) == true) {
                //return $res->withJson(['message' => 'loaded!', 'error' =>'uploads/' . $name, 'success' => $user_id])->withStatus(200);


                //$user_type = $user->getEntry();
                //return $res->withJson(['message' => $user, 'error' =>"400", 'success' => ' ok' ])->withStatus(200);
                if ($user->entry_type == 'App\Models\Salon') {
                    $service = Service::where('salon_id', $user->entry_id)->where('service_id', $service_id)->first();
                    $service->logo = 'http://hairtime.co.il/uploads/' . $name;
                    $service->save();
                    return $res->withJson(['url' => $service->logo, 'message' => 'file ' . $name . ' uploaded and saved'])
                        ->withStatus(200);
                } elseif ($user->entry_type == 'App\Models\Worker') {
                    $service = ServiceWorker::where('worker_id', $user->entry_id)->where('service_id', $service_id)->first();
                    $service->logo = 'http://hairtime.co.il/uploads/' . $name;
                    $service->save();
                    return $res->withJson(['url' => $service->logo, 'message' => 'file ' . $name . ' uploaded and saved'])
                        ->withStatus(200);

                }
                return $res->withJson(['url' => 'http://hairtime.co.il/uploads/' . $name, 'message' => 'file ' . $files['name'][0] . ' uploaded'])
                    ->withStatus(200);
            } else {
                return $res->withJson(['message' => "No files uploaded!!", 'error' => move_uploaded_file($files['tmp_name'][0], 'uploads/' . $name)])
                    ->withStatus(400);
            }
        } else {
            return $res->withJson(['message' => $files, 'error' => $files['error'][0]])->withStatus(200);
        }
    }
}

