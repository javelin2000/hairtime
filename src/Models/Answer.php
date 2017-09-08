<?php
/**
 * Created by PhpStorm.
 * User: Javelin
 * Date: 05.09.2017
 * Time: 23:02
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public $timestamps = false;
    protected $table = 'answers';
    protected $primaryKey = 'answer_id';
    protected $fillable = [
        'message_id',
        'text',
        'create_at',
        'delete_at',
    ];

    protected $hidden = [


    ];

    public function message()
    {
        return $this->belongsTo('App\Models\Message');
    }
}