<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 25.10.2016
 * Time: 15:18
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Worker
 * @package App\Models
 * @method static Worker find(integer $id)
 * @method static Worker where($column, $condition, $special = null)
 * @method static Worker first()
 */
class Worker extends Model
{
    public $timestamps = false;
    protected $table = 'workers';
    protected $primaryKey = 'worker_id';
    protected $fillable = [
        'first_name',
        'last_name',
        'specialization',
        'start_year',
        'salon_id',
        'phone',
        'logo'
    ];

    protected $hidden = [
        'created_at',
    ];

    public function entries()
    {
        return $this->morphMany('App\Models\User', 'entry');
    }

    public function salon()
    {
        return $this->belongsTo('App\Models\Salon', 'salon_id', 'salon_id');
    }

    public function user()
    {
        return $this->morphOne('App\Models\User', 'entry');
    }

    public function schedules()
    {

        return $this->hasMany('App\models\Schedule');
    }

    public function getUserID()
    {

    }

    public function services()
    {
        return $this->belongsToMany('App\Models\Service', 'server_worker');
    }
}