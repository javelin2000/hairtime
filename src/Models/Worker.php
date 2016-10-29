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
class Worker extends Model {
    public $timestamps = false;
    protected $table = 'workers';
    protected $primaryKey = 'worker_id';
    protected $fillable = [
        'first_name',
        'last_name',
        'specialization',
        'start_year',
        'phone',
        'logo'
    ];

    public function entries(){
        return $this->morphMany('App\Models\User', 'entry');
    }
}