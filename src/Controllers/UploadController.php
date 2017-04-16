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

}

/*function uploadFile (Request $req, Response $res) {

    $validation = $this->validator;
    $validation->validate($req, array(
        'user_id' => v::notEmpty(),
        'file' => v::image()
    ));
    if ($validation->failed()) {
        return $res->withJson($validation->errors)->withStatus(400);
    }

    if (!isset($_FILES['uploads'])) {
        return $res->withJson(['message' => "No files uploaded!!", 'error' =>"400", 'success' => 'false'])
    ->withStatus(400);
    }
    // $imgs = array();

    $user_id = $req->getAttribute('user_id');
    $files = $_FILES['uploads'];
    // $cnt = count($files['name']);

    if ($files['error'][0] === 0) {
        $name = uniqid('img-'.date('Ymd').'-');
        if (move_uploaded_file($files['tmp_name'][0], 'uploads/' . $name) === true) {
            // $imgs[] = array('url' => '/uploads/' . $name, 'name' => $files['name'][$i]);
            //$user = User::where('user_id', $user_id)->first();
            /*if ( Customer::where('user_id', $user_id)->first != null )
            {
                return $res->withJson(['message' => 'customer'])
                    ->withStatus(200);
            }*//*
                //$user->logo  = true;
                //$user->save();
                return $res->withJson(['url'=>'hairtime.co.il/uploads/'.$name,'message' => 'file '.$files['name'][0].' uploaded'])
                    ->withStatus(200);
            }else{
                return $res->withJson(['message' => "No files uploaded!!", 'error' =>"400", 'success' => 'false'])
                    ->withStatus(400);
            }

        }


        //$plural = ($imageCount == 1) ? '' : 's';

        //foreach($imgs as $img) {
        //    printf('%s <img src="%s" width="50" height="50" /><br/>', $img['name'], $img['url']);
        //}
    }
}*/