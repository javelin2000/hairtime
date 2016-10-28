<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 25.10.2016
 * Time: 15:15
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salon extends Model {
    public $timestamps = false;
    protected $table = 'salons';
    protected $primaryKey = 'salon_id';
    protected $fillable = [
        'first_name',
        'last_name',
        'business_name',
        'founded_in',
        'city',
        'address',
        'lat',
        'lng',
        'phone',
        'logo'
    ];

    public function user(){
        return $this->morphOne('App\Models\User', 'entry');
    }
}