<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 01.11.2016
 * Time: 7:21
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Comment
 * @package App\Models
 * @method static Comment find(integer $id)
 * @method static Comment findOrFail(integer $id)
 * @method static Comment where($column, $condition, $special = null)
 * @method static Comment having($column, $condition, $special = null)
 * @method static Comment first()
 * @method static Comment select($statement)
 * @method static Comment selectRaw($statement)
 * @method static Collection get()
 */
class Comment extends Model
{
    public $timestamps = false;
    protected $table = 'comments';
    protected $primaryKey = 'comment_id';
    protected $fillable = [
        'customer_id',
        'salon_id',
        'body'
    ];
    protected $hidden = [
        'comment_id',
    ];

    function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    function salon()
    {
        return $this->belongsTo('App\Models\Salon');
    }

    static function getUserComment($comment_id, $user_id)
    {
        $customer = User::find($user_id)->getEntry();
        $comment = Comment::find($comment_id);
        if ($comment === null or $comment->customer_id !== $customer->customer_id)
            return false;
        else
            return $comment;
    }
}