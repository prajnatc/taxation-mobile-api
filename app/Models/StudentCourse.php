<?php
/**
 * StudentCourse
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\InstitutionCourse as InstitutionCourse;
/**
 * StudentCourse
 * @category Model
 * @author ThinkPace
 */

class StudentCourse extends Model
{


         /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'clientDB';

     /**
   * $dates model dates
   * @var [date]
   */
    protected $dates = ['created_at','updated_at','deleted_at'];

	  const UNPAID = 'UNPAID';

	/**
	 * Set batch active status
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function setActiveAttribute($value)
	{
		$this->attributes['active'] = ($value) ?: 'N';
	}

    /**
     * get scope valid data
     * @param  string $query 
     * @return array   
     */
	public static function scopeValid($query){
		return $query->whereNull('deleted_at');
	}

/**
 * getId get id of model record
 * @return string hashid
 */
	public function getId(){
		return Hashids::encode($this->id);
	}

/**
 * scopeFindId scope find id
 * @param  array $query student course
 * @param  integer $id    student id
 * @return array        scope
 */
	public function scopeFindId($query,$id){
		return $query->find(Hashids::decode($id))->first();
	}

/**
 * getStatus
 * @return string status
 */
	public function getStatus(){
		return ($this->active=='Y') ? 'Active': 'In-active';
	}

/**
 * statusChecked status checked
 * @return string active true/false
 */
	public function statusChecked(){
		return ($this->active=='Y') ? true:false;
	}


/**
 * subjects student course subjects
 * @return array student course subject details
 */
	public function subjects(){
		return $this->hasOne('App\Subject','id','elective_subject_id');
	}

/**
 * languageSubjects language subjects
 * @return array language subject details
 */
	public function languageSubjects()
	{
		return $this->hasMany('App\Subject','id','language_subject_id');
	}

/**
 * challans student course challan details
 * @return array
 */
	public function challans()
	{
		return $this->hasMany('App\Challan','student_course_id','student_id');
	}

/**
 * courseType course type of student course data
 * @return array
 */
 		 public function courseType()
        {
            return $this->belongsTo('App\CourseType','course_type_id','id');
        }

/**
 * feeReceipt fee receipt of student course
 * @return array
 */
       public function feeReceipt()
       {
       	 return $this->hasMany('App\FeeReceipt','student_course_id','id');
       }

/**
 * coursePeroid course period of student course
 * @return array
 */
        public function coursePeroid()
        {
            return $this->belongsTo('App\CoursePeroidDetail','course_peroid_id','id');
        }

/**
 * currentCourseFees course fees of student course
 * @return array
 */
        public function currentCourseFees(){
        	return $this->belongsTo('App\CourseHeadAllocation','course_id','id');
        }

/**
 * institutionCourse institution course of student course
 * @return array
 */
        public function institutionCourse()
    	{
    		return $this->hasOne(InstitutionCourse::class,'id','institution_course_id');
    	}

/**
 * feeType feetype student course
 * @return array
 */
    	public function feeType()
    	{
    		return $this->hasOne('App\FeeType','id','fee_type_id');
    	}


/**
 * student student of student course
 * @return array
 */
    	public function student()
    	{
    		return $this->belongsTo('App\Student','student_id','id');
    	}

/**
 * studentCurrentPeroidDetail student current period details of student course
 * @return array
 */
        public function studentCurrentPeroidDetail(){
            return $this->hasOne('App\CoursePeroidDetail','id','course_peroid_id');
        }

        /**
 * studentCertificate student certificate details of student course
 * @return array
 */
		 public function studentCertificate()
        {
            return $this->hasMany('App\StudentCertificate');
        }

        /**
 * academics student academic details of student course
 * @return array
 */
         public function academics()
    	{
    		return $this->belongsTo('App\Academic','academic_year_id','id');
    	}

        /**
 * studentMarkDetail student mark details of student course
 * @return array
 */
    	public function studentMarkDetail()
    	{
    		return $this->hasMany('App\StudentMarkDetail','student_course_id','id');
    	}

        /**
 * courseSection course section of student course
 * @return array
 */
        public function courseSection(){
            return $this->hasOne('App\CourseSection','id','course_section_id');
        }
}
