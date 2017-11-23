<?php
/**
 * FacultySubjectAllocation
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\User as User;
use App\Models\Subject as Subject;
use App\Models\UserInstitution as UserInstitution;
use DB;

/**
 * FacultySubjectAllocation
 * @category Model
 * @author ThinkPace
 */
class FacultySubjectAllocation extends Model
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
   * @return array subject information
   */
	public function subject(){
		return $this->belongsTo(Subject::class,'course_subject_id','id');
	}

/**
 * userInstitution reationship to the user information
 * @return array user information details
 */
    public function userInstitution()
  {
    return $this->belongsTo(UserInstitution::class,'user_institution_id','id');
  }
  
}
