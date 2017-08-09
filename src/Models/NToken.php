<?php
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 22.05.2017
 * Time: 0:51
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class NToken
 * @package App\Models
 * @method static NToken find(integer $id)
 * @method static NToken findOrFail(integer $id)
 * @method static NToken where($column, $condition, $special = null)
 * @method static NToken first()
 * @method static NToken get()
 * @method static NToken join($table, $col1, $rule, $ol2)
 * @method static NToken orderBy($column, $order)
 * @method static NToken integer count()
 * @method static NToken pluck($col)
 * @method static NToken table($table)
 * @method static NToken distinct()
 */
class NToken extends Model
{
    public $timestamps = false;
    protected $table = 'n_tokens';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'n_token',
    ];
    protected $hidden = [
        'created_at',
    ];
}