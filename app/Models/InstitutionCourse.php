<?php
/**
 * InstitutionCourse
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hashids;
/**
 * InstitutionCourse
 * @category Model
 * @author ThinkPace
 */
class InstitutionCourse extends Model
{

    use SoftDeletes;

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

/**
 * $hashids ecrypted id
 * @var string
 */
	protected $hashids;

/**
 * $fillable mandatory information
 * @var array
 */
    protected $fillable = [];


            // Don't forget to fill this array
    /**
     * $unguarded description
     * @var boolean
     */
    protected static $unguarded = true;


/**
 * setActiveAttribute set active attribute
 * @param boolean $value active or not
 */
public function setActiveAttribute($value)
  {
    $this->attributes['active'] = ($value) ?: 'N';
  }

/**
 * scopeValid scope valid 
 * @param  array $query institution course details
 * @return array        scope valid details
 */
  public static function scopeValid($query){
    return $query->whereNull('deleted_at');
  }

  /**
   * getId get Id
   * @return string encode id to hash id
   */
  public function getId(){
    return Hashids::encode($this->id);
  }

/** 
 * scopeFindId scope Find Id
 * @param  array $query institution course details
 * @param  integer $id    id
 * @return array        data
 */
public function scopeFindId($query,$id){
    return $query->withTrashed()->find(Hashids::decode($id))->first();
  }

/**
 * getStatus get status
 * @return string active or inactive
 */
  public function getStatus(){
    return ($this->deleted_at=='') ? 'Active': 'In-active';
  }

  /**
   * statusChecked status checked
   * @return boolean true or false
   */
  public function statusChecked(){
    return ($this->deleted_at=='') ? true:false;
  }

  /**
   * coursePeroidDetails link course period details
   * @return array linked information
   */
  public function coursePeroidDetails()
  {
    return $this->hasMany('App\CoursePeroidDetail');
  }

/**
 * scopegetInstitutionCourseList get course list information
 * @param  array $query         institution course
 * @param  integer $academic_year academic year
 * @return array                details
 */
    public function scopegetInstitutionCourseList($query,$academic_year=null)
    {
       $array_select = array(''=>'--Select--');

       $result = $query->join('courses',function($courses){

            $courses->on('courses.id','=','institution_courses.course_id');

        })->join('academics',function($academics){

            $academics->on('academics.id','=','institution_courses.academic_year_id');

        })->select('institution_courses.id as key','institution_courses.custom_course_name as value')->where('institution_courses.academic_year_id',$academic_year)->whereIn('institution_courses.institute_id',unserializeConstant(CURRENTINSTITUE))->whereNull('courses.deleted_at')->lists('value','key');

        foreach ($result as $key => $value) {
             $array_select[$key] = $value;
        }

        return $array_select;

    }

/**
 * scopegetCurrentInstitutionCourseList get current institution course list
 * @param  array $query         institution course
 * @param  integer $academic_year academic year
 * @param  integer $institute_id  institution id
 * @return array                current institution course list
 */
    public function scopegetCurrentInstitutionCourseList($query,$academic_year=null,$institute_id=null)
    {
       $array_select = array(''=>'--Select--');

       $result = $query->join('courses',function($courses){

            $courses->on('courses.id','=','institution_courses.course_id');

        })->join('academics',function($academics){

            $academics->on('academics.id','=','institution_courses.academic_year_id');

        })->select('institution_courses.id as key','institution_courses.custom_course_name as value')->where('institution_courses.academic_year_id',$academic_year)->where('institution_courses.institute_id',$institute_id)->whereNull('courses.deleted_at')->lists('value','key');

        foreach ($result as $key => $value) {
             $array_select[$key] = $value;
        }

        return $array_select;

    }

/**
 * course link to course
 * @return array linked data
 */
        public function course()
      {
        return $this->belongsTo('App\Course','course_id','id');
      }

/**
 * institutions relationship to institution model
 * @return array institution details
 */
     public function institutions()
      {
            return $this->belongsTo('App\Institution','institute_id','id');
      }

/**
 * academics relationship to academics
 * @return array academics details
 */
        public function academics()
      {
            return $this->belongsTo('App\Academic','academic_year_id','id');
      }

/**
 * courseType relationship to course type 
 * @return array course type details
 */
       public function courseType(){
            return $this->belongsTo('App\CourseType');
        }

/**
 * courseCategories relationship to course categories details
 * @return array categories details
 */
    public function courseCategories()
    {
      return $this->belongsTo('App\CourseCategory','course_category_id','id');
    }

/**
 * scopeCurrentCourseList scope current course list
 * @param  array $query     institution course
 * @param  integer $course_id course id
 * @return array            current course list
 */
       public function scopeCurrentCourseList($query,$course_id)
    {

       $array_select = array(''=>'--Select--');

       $result = $query->join('courses',function($courses){

            $courses->on('courses.id','=','institution_courses.course_id');

        })->join('academics',function($academics){

            $academics->on('academics.id','=','institution_courses.academic_year_id');

        })->select('institution_courses.id as key','institution_courses.custom_course_name as value')->where('institution_courses.id','!=',$course_id)->whereNull('courses.deleted_at')->lists('value','key');

        foreach ($result as $key => $value) {
             $array_select[$key] = $value;
        }

        return $array_select;


    }

/**
 * courseCategory course category
 * @return array course category details
 */
  public function courseCategory()
      {
        return $this->belongsTo('App\CourseCategory');
      }


/**
 * scopegetInstitutionCourse scope institution course
 * @param  array $query         institution course
 * @param  integer $academic_year academic year
 * @param  integer $institute_id  institution id
 * @return array                institution course
 */
       public function scopegetInstitutionCourse($query,$academic_year=null,$institute_id=null)
    {
       $array_select = array(''=>'--Select--');

       $result = $query->join('courses',function($courses){

            $courses->on('courses.id','=','institution_courses.course_id');

        })->join('academics',function($academics){

            $academics->on('academics.id','=','institution_courses.academic_year_id');

        })->select('institution_courses.id as key','institution_courses.custom_course_name as value')->where('institution_courses.academic_year_id',$academic_year)->where('institution_courses.institute_id',$institute_id)->whereNull('courses.deleted_at')->lists('value','key');

        foreach ($result as $key => $value) {
             $array_select[$key] = $value;
        }

        return $array_select;

    }

/**
 * scopegetInstitutionPeriodCourse period course
 * @param  array $query              instituton course
 * @param  integer $academic_period_id academic period id
 * @param  integer $institute_id       institution id
 * @param  integer $academic_year_id   academic year id
 * @return array                     institution period course details
 */
    public function scopegetInstitutionPeriodCourse($query,$academic_period_id=null,$institute_id=null,$academic_year_id=null)
    {

       $array_select = array(''=>'--Select--');

        $result = CoursePeroidDetail::join('institution_courses',function($institution_courses){

            $institution_courses->on('institution_courses.id','=','course_peroid_details.institution_course_id');
        })->select('institution_courses.custom_course_name as value','institution_courses.id as key')->where('course_peroid_details.academic_period_id',$academic_period_id)->where('institution_courses.institute_id',$institute_id)->where('academic_year_id',$academic_year_id)->whereNull('course_peroid_details.deleted_at')->lists('value','key');

        foreach ($result as $key => $value) {
             $array_select[$key] = $value;
        }

        return $array_select;
    }


}
