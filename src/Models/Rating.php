<?php
/**
 * Created by PhpStorm.
 * User: marko
 * Date: 05.11.2016
 * Time: 21:30
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use DB;

//use App\Http\Controllers\Controller;

/**
 * Class Rating
 * @package App\Models
 * @method static Rating find(integer $id)
 * @method static Rating findOrFail(integer $id)
 * @method static Rating where($column, $condition, $special = null)
 * @method static Rating having($column, $condition, $special = null)
 * @method static Rating first()
 * @method static Rating select($statement)
 * @method static Rating selectRaw($statement)
 * @method static Rating firstOrNew($args)
 * @method static Collection get()
 */
class Rating extends Model
{
    public $timestamps = false;
    protected $table = 'rating';
    protected $primaryKey = 'rate_id';
    protected $fillable = [
        'rating',
        'salon_id',
        'customer_id'
    ];
    protected $hidden = [
        'rate_id',
    ];

    /**
     * Function return averaged rating of the Salon
     * @param $salon_id
     * return int
     */
    public function averagedRate($salon_id)
    {
        $rating = DB::table('orders')->max('price');
    }

    function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    function salon()
    {
        return $this->belongsTo('App\Models\Salon');
    }

}