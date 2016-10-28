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
 */
class User extends Model {
    public $timestamps = false;
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'email',
        'password',
    ];

    function entry(){
        return $this->morphTo();
    }
    
    function tokens(){
        return $this->hasMany('App\Models\Token');
    }
}