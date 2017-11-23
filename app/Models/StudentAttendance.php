<?php
/**
 * StudentAttendance
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StudentCourse as StudentCourse;
use App\Models\AttendanceType as AttendanceType;
use App\Models\AppUser as AppUser;
use DB;
/**
 * StudentAttendance
 * @category Model
 * @author ThinkPace
 */
class StudentAttendance extends Model
{

        /**
   * $dates model dates
   * @var [date]
   */
    protected $dates = ['created_at','updated_at','deleted_at','date'];

	/**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'clientDB';

	 /* Get the author.
	 *
	 * @return User
	 */

/**
 * studentCourse course details of a student attendance
 * @return array course details
 */
   public function studentCourse(){
 		return $this->belongsTo(StudentCourse::class)->where('current_course','Y');
 	}

/**
 * user user details of a student attendance
 * @return [type] [description]
 */
  public function user() {
		return $this->belongsTo(AppUser::class, 'created_by','id');
	}

/**
 * facultySubjectAllocation faculty subject allocation of a attendance
 * @return array faculty subject allocation details
 */
	public function facultySubjectAllocation(){
		return $this->belongsTo('App\FacultySubjectAllocation');
	}

/**
 * attendanceType attendance type of student attendance
 * @return array attendance type
 */
	public function attendanceType()
	{
		return $this->belongsTo(AttendanceType::class)->select(['name','code','group_code','group_name']);
	}

/**
 * scopegetStudentAttendanceDetails student attendance details
 * @param  array $query              studentattendance
 * @param  integer $subjectId          subject id
 * @param  integer $student_course_id  subject course id
 * @param  date $fromDate           from date
 * @param  date $toDate             to date
 * @param  integer $attendance_type_id attendance type id
 * @return array                     attendance details
 */
	public function scopegetStudentAttendanceDetails($query,$subjectId=null,$student_course_id=null,$fromDate=null,$toDate=null,$attendance_type_id=null)
	{
		$result = $query->join('faculty_subject_allocations',function($faculty_subject_allocations){

			$faculty_subject_allocations->on('faculty_subject_allocations.id','=','student_attendances.faculty_subject_allocation_id');

		})->join('course_subjects',function($course_subjects){

			$course_subjects->on('course_subjects.id','=','faculty_subject_allocations.course_subject_id');

		})->join('subjects',function($subjects){

			$subjects->on('subjects.id','=','course_subjects.subject_id');

		})->select(DB::raw('count(student_course_id) as absentCount'),DB::raw('MONTH(date) as month'),'attendance_type_id as attendance_type_id','subjects.name as subject_name','date as date',DB::raw('YEAR(date) as year'),'faculty_subject_allocation_id as faculty_subject_allocation_id')->where('student_course_id',$student_course_id)->where('date','>=',$fromDate)->where('date','<=',$toDate)->where('attendance_type_id',$attendance_type_id)->groupBy('attendance_type_id')->groupBy(DB::raw("MONTH(date)"))->groupBy(DB::raw("YEAR(date)"))->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc');


			if(!empty($subjectId))
				$result = $result->where('course_subjects.id',$subjectId)->groupBy('course_subjects.id')->get();
			else
				$result = $result->groupBy('course_subjects.id')->get();

		return $result;


	}

/**
 * scopegetAbsentDetails get absent details
 * @param  array $query              student attendance
 * @param  integer $subjectId          subject id
 * @param  integer $student_course_id  student course id
 * @param  date $fromDate           from date
 * @param  date $toDate             to date
 * @param  integer $attendance_type_id attendance type id
 * @return array                     attendance details
 */
	public function scopegetAbsentDetails($query,$subjectId=null,$student_course_id=null,$fromDate=null,$toDate=null,$attendance_type_id=null){

			$result = $query->join('faculty_subject_allocations',function($faculty_subject_allocations){

			$faculty_subject_allocations->on('faculty_subject_allocations.id','=','student_attendances.faculty_subject_allocation_id');

		})->join('course_subjects',function($course_subjects){

			$course_subjects->on('course_subjects.id','=','faculty_subject_allocations.course_subject_id');

		})->join('subjects',function($subjects){

			$subjects->on('subjects.id','=','course_subjects.subject_id');

		})->select(DB::raw('count(student_course_id) as total_absent_days'))->where('student_course_id',$student_course_id)->where('date','>=',$fromDate)->where('date','<=',$toDate)->where('attendance_type_id',$attendance_type_id)->orderBy(DB::raw('MONTH(date)'),'asc');

			if(!empty($subjectId))
				$result = $result->where('course_subjects.id',$subjectId)->groupBy('course_subjects.id')->first();
			else
				$result = $result->first();

		return $result;

	}

/**
 * scopegetClassAttendanceDetails class attendance details
 * @param  array $query              student attenace
 * @param  integer $subjectId          subject id
 * @param  array  $student_details    subject details
 * @param  date $fromDate           from date
 * @param  date $toDate             to date
 * @param  integer $attendance_type_id attendance type id
 * @return array                     attendance details
 */
	public function scopegetClassAttendanceDetails($query,$subjectId=null,$student_details=array(),$fromDate=null,$toDate=null,$attendance_type_id=null)
	{
		$result = $query->join('faculty_subject_allocations',function($faculty_subject_allocations){

			$faculty_subject_allocations->on('faculty_subject_allocations.id','=','student_attendances.faculty_subject_allocation_id');

		})->join('course_subjects',function($course_subjects){

			$course_subjects->on('course_subjects.id','=','faculty_subject_allocations.course_subject_id');

		})->join('subjects',function($subjects){

			$subjects->on('subjects.id','=','course_subjects.subject_id');

		})->select(DB::raw('count(student_course_id) as absentCount'),DB::raw('MONTH(date) as month'),'attendance_type_id as attendance_type_id','subjects.name as subject_name','date as date',DB::raw('YEAR(date) as year'),'faculty_subject_allocation_id as faculty_subject_allocation_id','student_attendances.student_course_id as student_course_id')->whereIn('student_course_id',$student_details->lists('student_course_id')->toArray())->where('date','>=',$fromDate)->where('date','<=',$toDate)->where('attendance_type_id',$attendance_type_id)->groupBy('student_attendances.student_course_id')->groupBy('attendance_type_id')->groupBy(DB::raw("MONTH(date)"))->groupBy(DB::raw("YEAR(date)"))->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc');


			if(!empty($subjectId))
				$result = $result->where('course_subjects.id',$subjectId)->groupBy('course_subjects.id')->get();
			else
				$result = $result->groupBy('course_subjects.id')->get();

		return $result;


	}

/**
 * scopegetStudentSubjectAttendanceDetails get student subject attendance details
 * @param  array $query              student attendance details
 * @param  integer $student_course_id  student course id
 * @param  integer $month              month
 * @param  integer $year               year
 * @param  integer $attendance_type_id attendance type id
 * @param  integer $subjectId          subject id
 * @return array                     attendance details
 */
  public function scopegetStudentSubjectAttendanceDetails($query,$student_course_id=null,$month=null,$year=null,$attendance_type_id=null,$subjectId=null)
  	{
  		$result = $query->join('faculty_subject_allocations',function($faculty_subject_allocations){

  			$faculty_subject_allocations->on('faculty_subject_allocations.id','=','student_attendances.faculty_subject_allocation_id');

  		})->join('subjects',function($subjects){

  			$subjects->on('subjects.id','=','faculty_subject_allocations.course_subject_id');

  		})->select(DB::raw('sec_to_time(sum(time_to_sec(student_attendances.no_of_hrs))) as no_of_hrs'),DB::raw('MONTH(date) as month'),'attendance_type_id as attendance_type_id','subjects.name as subject_name','date as date',DB::raw('YEAR(date) as year'),'faculty_subject_allocation_id as faculty_subject_allocation_id','course_subject_attendance_detail_id as course_subject_attendance_detail_id','student_course_id as student_course_id')->where('student_course_id',$student_course_id)->whereRaw("MONTH( DATE ) =$month AND YEAR( DATE ) = $year")->where('attendance_type_id',$attendance_type_id)->groupBy('attendance_type_id')->groupBy(DB::raw("MONTH(date)"))->groupBy(DB::raw("YEAR(date)"))->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc');


  			if(!empty($subjectId))
  				$result = $result->where('faculty_subject_allocations.id',$subjectId)->groupBy('faculty_subject_allocations.id')->get();
  			else
  				$result = $result->groupBy('faculty_subject_allocations.id')->get();

  		return $result;


  	}

/**
 * scopegetAbsentStudentSubjectAttendanceDetails get student subject attendance details
 * @param  array $query              student attendance
 * @param  integer $subjectId          subject id
 * @param  integer $student_course_id  subject course id
 * @param  date $fromDate           from date
 * @param  date $toDate             to date
 * @param  integer $attendance_type_id attendance type id
 * @return [type]                     attendance details
 */
  public function scopegetAbsentStudentSubjectAttendanceDetails($query,$subjectId=null,$student_course_id=null,$fromDate=null,$toDate=null,$attendance_type_id=null)
  	{
  		$result = $query->join('faculty_subject_allocations',function($faculty_subject_allocations){

  			$faculty_subject_allocations->on('faculty_subject_allocations.id','=','student_attendances.faculty_subject_allocation_id');

  		})->join('course_subjects',function($course_subjects){

  			$course_subjects->on('course_subjects.id','=','faculty_subject_allocations.course_subject_id');

  		})->join('subjects',function($subjects){

  			$subjects->on('subjects.id','=','course_subjects.subject_id');

  		})->select(DB::raw('sec_to_time(sum(time_to_sec(student_attendances.no_of_hrs))) as no_of_absent_hrs'),DB::raw('MONTH(date) as month'),'attendance_type_id as attendance_type_id','subjects.name as subject_name','date as date',DB::raw('YEAR(date) as year'),'faculty_subject_allocation_id as faculty_subject_allocation_id','course_subject_attendance_detail_id as course_subject_attendance_detail_id','student_course_id as student_course_id',DB::raw("CONCAT(`faculty_subject_allocation_id`, '-', MONTH(date),'-',`student_course_id`) as type_month"))->where('student_course_id',$student_course_id)->where('present','N')->where('date','>=',$fromDate)->where('date','<=',$toDate)->where('attendance_type_id',$attendance_type_id)->groupBy('attendance_type_id')->groupBy(DB::raw("MONTH(date)"))->groupBy(DB::raw("YEAR(date)"))->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc');


  			if(!empty($subjectId))
  				$result = $result->where('course_subjects.id',$subjectId)->groupBy('course_subjects.id')->get();
  			else
  				$result = $result->groupBy('course_subjects.id')->get();

  		return $result;


  	}

}
