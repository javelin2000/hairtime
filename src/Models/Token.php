<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 27.10.2016
 * Time: 7:25
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Token
 * @package App\Models
 * @method static Token find(integer $id)
 * @method static Token findOrFail(integer $id)
 * @method static Token where($column, $condition, $special = null)
 * @method static Token first()
 * @method static Token firstOrFail()
 */
class Token extends Model{
    public $timestamps = false;
    protected $primaryKey = 'token_id';
    protected $table = 'tokens';
    protected $fillable = [
        'token'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public static function deleteAll($user_id)
    {
        static::where('user_id', $user_id)->delete();
    }

    public static function deleteOne($user_id, $token)
    {
        static::where('token', $token)->where('user_id', $user_id)->delete();
    }

}