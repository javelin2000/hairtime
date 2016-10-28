<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 27.10.2016
 * Time: 7:25
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model{
    public $timestamps = false;
    protected $primaryKey = 'token_id';
    protected $table = 'tokens';
    protected $fillable = [
        'token'
    ];

    function user(){
        return $this->belongsTo('App\Models\User');
    }
}