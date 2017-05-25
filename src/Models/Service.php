<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 28.11.2016
 * Time: 16:49
 */

namespace App\Models;

/**
 * Class User
 * @package App\Models
 * @method static Service find(integer $id)
 * @method static Service findOrFail(integer $id)
 * @method static Service where($column, $condition, $special = null)
 * @method static Service first()
 * @method static Service integer count()
 * @method static Service join($table, $col1, $rule, $ol2)
 * @method static Service pluck($col)
 * @method static Service distinct()
 */

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public $timestamps = false;
    protected $primaryKey = 'service_id';
    protected $table = 'services';
    protected $fillable = [
        'salon_id',
        'name',
        'duration',
        'price_min',
        'price_max',
        'logo',
    ];

    public function workers()
    {
        return $this->belongsToMany('App\Models\Worker', 'service_worker');
    }

    public function salons()
    {
        return $this->belongsTo('App\Models\Salons');
    }

    public function entry()
    {
        return $this->morphTo();
    }

    public function getEntry()
    {
        return $this->entry()->get()[0];
    }
}