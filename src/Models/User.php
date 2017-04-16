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
 * @method static integer count()
 */
class User extends Model
{
    public $timestamps = false;
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'email',
        'password',
        'confirm_email',
    ];

    public function entry()
    {
        return $this->morphTo();
    }

    public function comments()
    {
        return $this->hasMany('App\Models\Comment');
    }

    public function ratings()
    {
        return $this->hasMany('App\Models\Rating');
    }

    public function getEntry()
    {
        return $this->entry()->get()[0];
    }

    public function tokens()
    {
        return $this->hasMany('App\Models\Token');
    }

    public static function changePassword($user_id, $password)
    {
        User::where('user_id', $user_id)->update(['password' => $password]);
    }
}