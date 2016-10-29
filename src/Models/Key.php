<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 29.10.2016
 * Time: 18:23
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Salon
 * @package App\Models
 * @method static Salon find(integer $id)
 * @method static Salon where($column, $condition, $special = null)
 * @method static Salon first()
 */
class Key extends Model
{
    public $timestamps = false;
    protected $table = 'activation_keys';
    protected $primaryKey = 'key_id';
    protected $fillable = [
        'key_body'
    ];
}