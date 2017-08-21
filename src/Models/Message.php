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
    protected $primaryKey = 'messages_id';
    protected $fillable = [
        'used_id',
        'message',
        'created_at',
        'answer_at',
    ];

    protected $hidden = [


    ];

    public function user()
    {
        return $this->morphOne('App\Models\User', 'entry');
    }
}