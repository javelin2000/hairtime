<?php
/**
 * Created by PhpStorm.
 * User: Javelin
 * Date: 21.08.2017
 * Time: 22:44
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $timestamps = false;
    protected $table = 'messages';
    protected $primaryKey = 'message_id';
    protected $fillable = [
        'used_id',
        'message',
        'create_at',
        'answer_at',
        'delete_at',
    ];

    protected $hidden = [


    ];


    public function answer()
    {
        return $this->hasMany('App\Models\Answer');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}