<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 29.10.2016
 * Time: 18:43
 */

namespace App\Controllers;

use App\Models\Salon;
use Slim\Http\Request;
use Slim\Http\Response;

class SearchController extends BaseController
{

    function freeSearch(Request $req, Response $res)
    {
        $city = strtolower($req->getAttribute('city'));
        $list = Salon::where('city', 'like', '%' . $city . '%')->get();
        $name = Salon::where('business_name', 'like', '%' . $city . '%')->get();
        $result = $name->toArray() + $list->toArray();
        return $res->withJson($result);
    }

    function aroundSearch(Request $req, Response $res)
    {
        $lat = str_replace(',', '.', $req->getAttribute('lat'));
        $lng = str_replace(',', '.', $req->getAttribute('lng'));
        $radius = $req->getAttribute('radius');
        $list = Salon::near($lat, $lng, $radius)->toArray();
        return $res->withJson($list);
    }
}