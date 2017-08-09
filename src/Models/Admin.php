<?php
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 17.05.2017
 * Time: 16:49
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package App\Models
 * @method static Admin find(integer $id)
 * @method static Admin findOrFail(integer $id)
 * @method static Admin where($column, $condition, $special = null)
 * @method static Admin get()
 * @method static Admin join($table, $col1, $rule, $ol2)
 * @method static Admin orderBy($column, $order)
 * @method static Admin first()
 * @method static Admin paginate($num)
 * @method static Admin integer count()
 */
class Admin extends Model
{
    public $timestamps = false;
    protected $table = 'admins';
    protected $primaryKey = 'admin_id';
    protected $fillable = [
        'user_id',
        'login',
        'password',
        'first_name',
        'last_name',
        'email',
        'status',
        'created_at',
    ];


    public static function changePassword($admin_id, $password)
    {
        Admin::where('admin_id', $admin_id)->update(['password' => $password]);
    }
}