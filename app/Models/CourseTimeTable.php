<?php
/**
 * CourseTimeTable
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subject as Subject;
use DB;

/**
 * CourseTimeTable
 * @category Model
 * @author ThinkPace
 */
class CourseTimeTable extends Model
{
     /**
   * [$dates model dates]
   * @var [date]
   */
   protected $dates = ['created_at','updated_at','deleted_at'];
	/**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'clientDB';
    
    /**
     * subject relationship to the subject model
     * @return array related information
     */
    public function subject()    
    {        
      return $this->hasOne(Subject::class,'id','course_subject_id');  
    }
}
