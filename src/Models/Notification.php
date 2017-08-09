<?php
/**
 * Created by PhpStorm.
 * User: javelin
 * Date: 22.05.2017
 * Time: 0:43
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Notification
 * @package App\Models
 * @method static Notification find(integer $id)
 * @method static Notification findOrFail(integer $id)
 * @method static Notification where($column, $condition, $special = null)
 * @method static Notification first()
 * @method static Notification get()
 * @method static Notification join($table, $col1, $rule, $ol2)
 * @method static Notification orderBy($column, $order)
 * @method static Notification integer count()
 * @method static Notification pluck($col)
 * @method static Notification table($table)
 * @method static Notification distinct()
 */
class Notification extends Model
{
    public $timestamps = false;
    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';
    protected $fillable = [
        'queue_id',
        'title',
        'message',
        'status'
    ];
    protected $hidden = [
        'created_at',
    ];

    public function send_notifications($tokens, $message)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $fields = array(
            'registration_ids' => $tokens,
            'data' => $message
        );
        $headers = array(
            'Authorization:key=AAAA4nbJNwg:APA91bEFbWGvk-4xSE9YQAwl3Rw7LJJBp-YIx4FeCwv0YC1B2SSnmT-g7Bh68nbNyrXt0YY4eo1tayxbrX6SJkpeWabFiJ1lKgKghZ8suBiKBJdFgjZ9_LneX380J5vT_GRqkc8ZMsj9',
            'Content-Type: application/json'
        );
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        if ($result === false) {
            return false;
        }
        curl_close($ch);
        return $result;
    }
}