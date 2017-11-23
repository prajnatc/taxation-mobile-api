<?php
/**
 * MessageLog
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * MessageLog
 * @category Model
 * @author ThinkPace
 */
class MessageLog extends Model
{

    use SoftDeletes;

    
   /**
   * $dates model dates
   * @var [date]
   */
    protected $dates = ['created_at','updated_at','deleted_at'];

	/**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'clientDB';
}
