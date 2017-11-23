<?php
/**
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\UserInstitution as UserInstitution;
use App\Models\InstitutionCourse as InstitutionCourse;
use App\Models\FacultySubjectAllocation as FacultySubjectAllocation;
use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @category Model
 * @author ThinkPace
 */
class NoticeBoard extends Model
{
      /**
   * [$dates start_date, expiry_date, Created_at, updated_at and deleted_at dates for the model]
   * @var [date]
   */
  use SoftDeletes;
  
       protected $dates = ['start_date','expiry_date','created_at','updated_at','deleted_at'];
       protected $table = 'notice_boards';

	/**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'clientDB';
    
    /**
     * userInstitution get User Institution for the model
     * @return array user institution
     */
    public function userInstitution()
    {
    	return $this->belongsTo(UserInstitution::class,'user_id','id');
    }
  
    /**
     * institutionCourse Get Institution Couse
     * @return array institution course details
     */
      public function institutionCourse()
    {
      return $this->belongsTo(InstitutionCourse::class);
    }

    /**
     * facultySubjectAllocation faculty subject allocation data
     * @return array faculty subject allocation data
     */
      public function facultySubjectAllocation() {
      return $this->belongsTo(FacultySubjectAllocation::class, 'faculty_subject_allocation_id','id');
    }

}
