<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 18.10.2016
 * Time: 20:43
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package App\Models
 * @method static User find(integer $id)
 * @method static User findOrFail(integer $id)
 * @method static User where($column, $condition, $special = null)
 * @method static User first()
 */
class User extends Model
{
    public $timestamps = false;
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'email',
        'password',
    ];

    function entry()
    {
        return $this->morphTo();
    }

    function getEntry()
    {
        return $this->entry()->get()->first();
    }

    function tokens()
    {
        return $this->hasMany('App\Models\Token');
    }

    static function changePassword($user_id, $password)
    {
        User::where('user_id', $user_id)->update(['password' => $password]);
    }
}