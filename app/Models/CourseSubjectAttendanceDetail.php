<?php
/**
 * CourseSubjectAttendanceDetail
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hashids;
use DB;
/**
 * CourseSubjectAttendanceDetail
 * @category Model
 * @author ThinkPace
 */
class CourseSubjectAttendanceDetail extends Model
{

    use SoftDeletes;
 /**
   * [$dates Created_at, updated_at and deleted_at dates]
   * @var [date]
   */
    protected $dates = ['created_at','updated_at','deleted_at'];

/**
 * $hashids encrypted ids
 * @var string
 */
	protected $hashids;

/**
 * $fillable mandatory fields
 * @var array
 */
	protected $fillable = [];

	/**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'clientDB';

    	public function delete() {
		// Delete the post
		return parent::delete();
	}

	/**
	 * Get the author.
	 *
	 * @return User
	 */
	public function user() {
		return $this->belongsTo('App\User', 'created_by');
	}

	/**
	 * attendanceType relationship to the model
	 * @return array of data
	 */
	public function attendanceType()
	{
		return $this->belongsTo('App\AttendanceType');
	}

	/**
	 * scopegetCourseSubjectAttendance get course subject attendance
	 * @param  array $query               attendance data
	 * @param  integer $subjectId           subject id
	 * @param  integer $student_course_id   student course id
	 * @param  date $fromDate            from date
	 * @param  date $toDate              to date
	 * @param  integer $attendance_type_id  attendance type id
	 * @param  integer $courseId            course id
	 * @param  array $coursePeriodDetails course period details
	 * @param  integer $courseSectionId     course section id
	 * @return array                      course subject attendance data
	 */
	public function scopegetCourseSubjectAttendance($query,$subjectId=null,$student_course_id=null,$fromDate=null,$toDate=null,$attendance_type_id=null,$courseId=null,$coursePeriodDetails=null,$courseSectionId=null){

		$result = $query->join('faculty_subject_allocations',function($faculty_subject_allocations){

			$faculty_subject_allocations->on('faculty_subject_allocations.id','=','course_subject_attendance_details.faculty_subject_allocation_id');

		})->join('course_subjects',function($course_subjects){

			$course_subjects->on('course_subjects.id','=','faculty_subject_allocations.course_subject_id');

		})->join('subjects',function($subjects){

			$subjects->on('subjects.id','=','course_subjects.subject_id');

		})->select(DB::raw('sec_to_time(sum(time_to_sec(no_of_hrs))) as working_days'),DB::raw('MONTH(date) as month'),'attendance_type_id as attendance_type_id','date as date','subjects.name as subject_name',DB::raw('YEAR(date) as year'),'faculty_subject_allocation_id as faculty_subject_allocation_id','course_subject_attendance_details.id as id',DB::raw("CONCAT(`faculty_subject_allocation_id`, '-', MONTH(date)) as type_month"))->where('course_subject_attendance_details.institution_course_id',$courseId)->where('course_subject_attendance_details.course_peroid_id',$coursePeriodDetails)->where('course_subject_attendance_details.course_section_id',$courseSectionId)->where('date','>=',$fromDate)->where('date','<=',$toDate)->where('attendance_type_id',$attendance_type_id)->groupBy('attendance_type_id')->groupBy(DB::raw("MONTH(date)"))->groupBy(DB::raw("YEAR(date)"))->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc');

		if(!empty($subjectId))
				$result = $result->where('course_subjects.id',$subjectId)->groupBy('course_subjects.id')->get();
			else
				$result = $result->groupBy('course_subjects.id')->get();

		return $result;

	}

/**
 * scopegetClassCourseSubjectAttendance get class course subject attendance
 * @param  array $query               attendance detials
 * @param  integer $subjectId           subject id
 * @param  $fromDate            from date
 * @param  date $toDate              to date
 * @param  integer $attendance_type_id  attendance type id
 * @param  integer $courseId            course id
 * @param  array $coursePeriodDetails course period details
 * @param  integer $courseSectionId     course section id
 * @return array                      details
 */
public function scopegetClassCourseSubjectAttendance($query,$subjectId=null,$fromDate=null,$toDate=null,$attendance_type_id=null,$courseId=null,$coursePeriodDetails=null,$courseSectionId=null){

		$result = $query->join('faculty_subject_allocations',function($faculty_subject_allocations){

			$faculty_subject_allocations->on('faculty_subject_allocations.id','=','course_subject_attendance_details.faculty_subject_allocation_id');

		})->join('course_subjects',function($course_subjects){

			$course_subjects->on('course_subjects.id','=','faculty_subject_allocations.course_subject_id');

		})->join('subjects',function($subjects){

			$subjects->on('subjects.id','=','course_subjects.subject_id');

		})->select(DB::raw('sec_to_time(sum(time_to_sec(no_of_hrs))) as working_days'),DB::raw('MONTH(date) as month'),'attendance_type_id as attendance_type_id','date as date','subjects.name as subject_name',DB::raw('YEAR(date) as year'),'faculty_subject_allocation_id as faculty_subject_allocation_id',DB::raw('count(faculty_subject_allocation_id) as subject'),DB::raw("CONCAT(`faculty_subject_allocation_id`, '-', MONTH(date)) as type_month"))->where('course_subject_attendance_details.institution_course_id',$courseId)->where('course_subject_attendance_details.course_peroid_id',$coursePeriodDetails)->where('course_subject_attendance_details.course_section_id',$courseSectionId)->where('date','>=',$fromDate)->where('date','<=',$toDate)->where('attendance_type_id',$attendance_type_id)->groupBy('attendance_type_id')->groupBy(DB::raw("MONTH(date)"))->groupBy(DB::raw("YEAR(date)"))->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc');

		if(!empty($subjectId))
				$result = $result->where('course_subjects.id',$subjectId)->groupBy('course_subjects.id')->get();
			else
				$result = $result->groupBy('course_subjects.id')->get();

		return $result;

	}

/**
 * scopegetCourseSubjectAttendanceAppApi get course subject attendance data
 * @param  array $query               course subject attendance
 * @param  integer $subjectId           subject id
 * @param  integer $student_course_id   subject course id
 * @param  integer $attendance_type_id  attendance type if
 * @param  integer $courseId            course id
 * @param  array $coursePeriodDetails course period details
 * @param  integer $courseSectionId     course section id
 * @param  integer $month               month
 * @param  integer $year                year
 * @return array                      attendace details
 */
  public function scopegetCourseSubjectAttendanceAppApi($query,$subjectId=null,$student_course_id=null,$attendance_type_id=null,$courseId=null,$coursePeriodDetails=null,$courseSectionId=null,$month=null,$year=null){

    $result = $query->join('faculty_subject_allocations',function($faculty_subject_allocations){

      $faculty_subject_allocations->on('faculty_subject_allocations.id','=','course_subject_attendance_details.faculty_subject_allocation_id');

    })->join('subjects',function($subjects){

      $subjects->on('subjects.id','=','faculty_subject_allocations.course_subject_id');

    })->select(DB::raw('sec_to_time(sum(time_to_sec(no_of_hrs))) as working_days'),DB::raw('MONTH(date) as month'),'attendance_type_id as attendance_type_id','date as date','subjects.name as subject_name',DB::raw('YEAR(date) as year'),'faculty_subject_allocation_id as faculty_subject_allocation_id','course_subject_attendance_details.id as id',DB::raw("CONCAT(`faculty_subject_allocation_id`, '-', MONTH(date), '-', YEAR(date)) as type_month"))->where('course_subject_attendance_details.institution_course_id',$courseId)->where('course_subject_attendance_details.course_peroid_id',$coursePeriodDetails)->whereRaw("MONTH( DATE ) =$month AND YEAR( DATE ) = $year")->where('attendance_type_id',$attendance_type_id)->groupBy('attendance_type_id')->groupBy(DB::raw("MONTH(date)"))->groupBy(DB::raw("YEAR(date)"))->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc');

    if(!empty($subjectId))
        $result = $result->where('faculty_subject_allocations.id',$subjectId)->groupBy('faculty_subject_allocations.id')->get();
      else
        $result = $result->groupBy('faculty_subject_allocations.id')->get();

    return $result;

  }

/**
 * scopegetAttendanceWorkingDays attendance working days
 * @param  array $query               course subject details
 * @param  integer $month               month
 * @param  integer $year                year
 * @param  integer $attendance_type_id  attendanc type id
 * @param  integer $courseId            course id
 * @param  array $coursePeriodDetails course period details
 * @param  integer $courseSectionId     course section id
 * @return array                      working days data
 */
  public function scopegetAttendanceWorkingDays($query,$month=null,$year=null,$attendance_type_id=null,$courseId=null,$coursePeriodDetails=null,$courseSectionId=null)
	{


		$result = $query->select(DB::raw('count(attendance_type_id) as Working_Days'),DB::raw('MONTH(date) as Month'),'id as id',DB::raw("CONCAT(`attendance_type_id`, '-', MONTH(date)) as type_month"))->where('institution_course_id',$courseId)->where('course_peroid_id',$coursePeriodDetails)->where('course_section_id',$courseSectionId)->whereRaw("MONTH( DATE ) =$month AND YEAR( DATE ) = $year")->where('attendance_type_id',$attendance_type_id)->groupBy('attendance_type_id')->groupBy(DB::raw("MONTH(date)"))->groupBy(DB::raw("YEAR(date)"))->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc')->get();

		return $result;
	}
  
  /**
   * scopegetCourseSubjectAttendanceTotalWorkingHour total working hor details
   * @param  array $query               subject attendance details
   * @param  integer $subjectId           subject id
   * @param  integer $student_course_id   subject course id
   * @param  integer $month               month
   * @param  integer $year                year
   * @param  integer $attendance_type_id  attendance type id
   * @param  integer $courseId            course id
   * @param  array $coursePeriodDetails course period details
   * @param  integer $courseSectionId     course section id
   * @return array                      working hour data
   */
  public function scopegetCourseSubjectAttendanceTotalWorkingHour($query,$subjectId=null,$student_course_id=null,$month=null,$year=null,$attendance_type_id=null,$courseId=null,$coursePeriodDetails=null,$courseSectionId=null){
		$result = $query->join('faculty_subject_allocations',function($faculty_subject_allocations){
			$faculty_subject_allocations->on('faculty_subject_allocations.id','=','course_subject_attendance_details.faculty_subject_allocation_id');
		})->join('subjects',function($subjects){
			$subjects->on('subjects.id','=','faculty_subject_allocations.course_subject_id');
		})->select(DB::raw('sec_to_time(sum(time_to_sec(no_of_hrs)))  as working_days'),DB::raw('MONTH(date) as month'),'attendance_type_id as attendance_type_id','date as date','subjects.name as subject_name',DB::raw('YEAR(date) as year'),'faculty_subject_allocation_id as faculty_subject_allocation_id','course_subject_attendance_details.id as id',DB::raw("CONCAT(`faculty_subject_allocation_id`, '-', MONTH(date)) as type_month"))->where('course_subject_attendance_details.institution_course_id',$courseId)->where('course_subject_attendance_details.course_peroid_id',$coursePeriodDetails)->where('attendance_type_id',$attendance_type_id)->groupBy('attendance_type_id')->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc');
		if(!empty($fromDate) && !empty($toDate)){
				$result = $result->whereRaw("MONTH( DATE ) =$month AND YEAR( DATE ) = $year");
			}
		if(!empty($courseSectionId));
		$result = $result->where('course_subject_attendance_details.course_section_id',$courseSectionId);
		if(!empty($subjectId))
				$result = $result->where('faculty_subject_allocations.id',$subjectId)->groupBy('faculty_subject_allocations.id')->first();
			else
				$result = $result->groupBy('faculty_subject_allocations.id')->first();
		return $result;
	}

}
