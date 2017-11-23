<?php
/**
 * CourseSubject
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Subject as Subject;
use DB;
/**
 * CourseSubject
 * @category Model
 * @author ThinkPace
 */
class CourseSubject extends Model
{
  /**
   * [$dates Created_at, updated_at and deleted_at dates]
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
     * @return data belong to
     */
        public function subject()
      {
            return $this->belongsTo(Subject::class,'subject_id','id');
      }
}