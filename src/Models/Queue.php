<?php
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 12.05.2017
 * Time: 15:10
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class User
 * @package App\Models
 * @method static Queue find(integer $id)
 * @method static Queue findOrFail(integer $id)
 * @method static Queue where($column, $condition, $special = null)
 * @method static Queue first()
 * @method static Queue get()
 * @method static Queue join($table, $col1, $rule, $ol2)
 * @method static Queue orderBy($column, $order)
 * @method static Queue integer count()
 * @method static Queue pluck($col)
 * @method static Queue table($table)
 */
class Queue extends Model
{
    public $timestamps = false;
    protected $table = 'queue';
    protected $primaryKey = 'queue_id';
    protected $fillable = [
        'service_id',
        'worker_id',
        'customer_id',
        'status',
        'time',
    ];
    protected $hidden = [
        'created_at',
    ];


}