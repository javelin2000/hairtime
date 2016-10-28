<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 27.10.2016
 * Time: 15:41
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model {
    public $timestamps = false;
    protected $table = 'customers';
    protected $primaryKey = 'customer_id';
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
    ];

    public function user(){
        return $this->morphOne('App\Models\User', 'entry');
    }


}