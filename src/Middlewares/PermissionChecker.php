<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 29.10.2016
 * Time: 16:45
 */
namespace App\Middlewares;

use App\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class PermissionChecker
{
    protected $role;
    protected $roles = [
        'customer',
        'salon',
        'worker'
    ];

    function __construct($role)
    {
        if (!in_array($role, $this->roles))
            throw new \Exception('Wrong role');
        $this->role = $role;
    }

    function __invoke(Request $req, Response $res, $next)
    {
        $id = $req->getHeader('User-ID');
        $user = User::find($id)->first();
        $role_name = 'App\Models\\' . ucfirst($this->role);
        print_r($user->entry_type);
        if ($role_name !== $user->entry_type)
            return $res->withStatus(403);
        return $next($req, $res);
    }
}