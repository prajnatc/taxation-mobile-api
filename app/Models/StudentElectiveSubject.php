<?php
/**
 * StudentElectiveSubject
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * StudentElectiveSubject
 * @category Model
 * @author ThinkPace
 */
class StudentElectiveSubject extends Model
{

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
