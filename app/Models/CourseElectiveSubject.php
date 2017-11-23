<?php
/**
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
/**
 * @category Model
 * @author ThinkPace
 */
class CourseElectiveSubject extends Model
{
	 /**
   * $dates required dated for the model
   * @var date
   */
    protected $dates = ['created_at','updated_at','deleted_at'];
	/**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'clientDB';
}
