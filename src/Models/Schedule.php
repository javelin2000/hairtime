<?php
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 01.05.2017
 * Time: 10:20
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Schedule
 * @package App\Models
 * @method
 * @method static Schedule find(integer $id)
 * @method static Schedule findOrFail(integer $id)
 * @method static Schedule where($column, $condition, $special = null)
 * @method static Schedule having($column, $condition, $special = null)
 * @method static Schedule first()
 * @method static Schedule select($statement)
 * @method static Schedule selectRaw($statement)
 * @method static Schedule orderBy($column, $order)
 */
class Schedule extends Model
{
    public $timestamps = false;
    protected $table = 'schedules';
    protected $primaryKey = 'schedule_id';
    protected $fillable = [
        'worker_id',
        'day',
        'start',
        'stop',
    ];
    protected $hidden = [
        'created_at',
    ];

    public function worker()
    {
        return $this->belongsTo('App\Models\Worker');
    }
}