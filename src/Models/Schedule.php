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