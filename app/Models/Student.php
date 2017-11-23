<?php
/**
 * Student
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StudentCourse as StudentCourse;

/**
 * Student
 * @category Model
 * @author ThinkPace
 */
class Student extends Model
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
    protected $dates = ['created_at','updated_at','deleted_at','admission_date','dob','date_of_leaving'];

/**
 * $STATUS_RECONCILLLED reconcilled
 * @var integer
 */
    protected $STATUS_RECONCILLLED= 4;
    /**
     * $STATUS_PENDING pending
     * @var integer
     */
    protected $STATUS_PENDING = 3;
    /**
     * $STATUS_ADMITTED admitted
     * @var integer
     */
    protected $STATUS_ADMITTED = 1;
    /**
     * $STATUS_CANCELLED canelled
     * @var integer
     */
    protected $STATUS_CANCELLED=5;

/**
 * getAmountInWords get amount in words
 * @return array convert amount to words
 */
    public function getAmountInWords()
    {
      return convert_number_to_words($this->amount)." Rupees Only";
    }


    /**
     * getAmountAttribute get amount attribute
     * @param  double $amount amount
     * @return double         correct number
     */
    public function getAmountAttribute($amount){

        return correctnumber($amount);
    }

    /**
     * getExamPenalfeeAttribute get exam penal fee attribute
     * @param  double $amount amount
     * @return double         correct number
     */
    public function getExamPenalfeeAttribute($amount){

        return correctnumber($amount);
    }

/**
     * getSpecialFineAttribute get special fine Attribute
     * @param  double $amount amount
     * @return double         correct number
     */
    public function getSpecialFineAttribute($amount){

        return correctnumber($amount);
    }

    /**
     * getFullNameAttribute get full name
     * @return string concatinate first middle and last name
     */
    public function getFullNameAttribute(){

        return $this->first_name." ".$this->middle_name." ".$this->last_name;
    }

/**
 * getParentGuardianName get parent guardian name
 * @return string father name/mother name/guardian name
 */
    public function getParentGuardianName(){
        if(!empty($this->fathers_name))
            return $this->fathers_name;
        if(!empty($this->mothers_name))
                    return $this->mothers_name;
        if(!empty($this->guardian_name))
                    return $this->guardian_name;
    }

/**
 * getPhoneNumber get phone number
 * @return string get all contact number
 */
    public function getPhoneNumber(){


            $mobile = '';

            if(!empty($this->fathers_mobile_no))
            $mobile = $this->fathers_mobile_no.',';

             if(!empty($this->mothers_mobile_no))
            $mobile .= $this->mothers_mobile_no.',';

              if(!empty($this->guardian_mobile_no))
            $mobile .= $this->guardian_mobile_no.',';

            if(!empty($this->mobile))
            $mobile .= $this->mobile.',';

           if(!empty($this->phone_two))
            $mobile .= $this->phone_two;


            return $mobile;


    }

    /*public function getPhoneNumberForSMS()
    {
            $mobile = '';

            if(!empty($this->fathers_mobile_no))
            $mobile = $this->fathers_mobile_no.',';

             if(!empty($this->mothers_mobile_no))
            $mobile = $this->mothers_mobile_no.',';

              if(!empty($this->guardian_mobile_no))
            $mobile = $this->guardian_mobile_no.',';

            if(!empty($this->mobile))
            $mobile = $this->mobile;

            return $mobile;
    }
*/
	/**
	 * Set active status
	 *
	 * @param  string  $value
	 * @return string
	 */
	public function setActiveAttribute($value)
	{
		$this->attributes['active'] = ($value) ?: 'N';
	}

/**
 * scopeValid get scope valid
 * @param  string $query student model
 * @return array        get not deleted data
 */
	public static function scopeValid($query){
		return $query->whereNull('deleted_at');
	}

/**
 * getId get id 
 * @return string hashid
 */
	public function getId(){
		return Hashids::encode($this->id);
	}

/**
 * scopeFindId scope find id
 * @param  array $query student
 * @param  integer $id    student id
 * @return array        details
 */
	public function scopeFindId($query,$id){
		return $query->find(Hashids::decode($id))->first();
	}

/**
 * scopeGetChallan scope get challan
 * @param  array $query    student
 * @param  string $paginate yes
 * @param  integer $id       student id
 * @return array           challan data
 */
	public function scopeGetChallan($query,$paginate='Y',$id)
	{

        $result = $query->join('student_courses', function($student_courses) {

      $student_courses->on('student_courses.student_id', '=', 'students.id')->where('student_courses.current_course','=','Y');

        })->join('institution_courses',function($institution_courses){

            $institution_courses->on('institution_courses.id','=','student_courses.institution_course_id');

        })->join('courses', function($courses) {

        $courses->on('courses.id', '=', 'institution_courses.course_id');

        })->join('course_peroid_details', function($course_peroid_details) {

            $course_peroid_details->on('course_peroid_details.id', '=', 'student_courses.course_peroid_id');

        })
		->join('institutions', function($institutions) {

      $institutions->on('institutions.id', '=', 'students.institute_id');

		})->join('challans', function($challans) {

      $challans->on('challans.student_course_id', '=', 'student_courses.id');

        })->join('academic_periods',function($academic_periods){

            $academic_periods->on('academic_periods.id','=','course_peroid_details.academic_period_id');

        })
        ->select('students.first_name as first_name','students.middle_name as middle_name','students.last_name as last_name','students.application_no as application_no','institution_courses.custom_course_name as course_name','institution_courses.course_id as institution_course_id','institutions.name as institute_name','institutions.address1 as address','challans.challan_no as challan_number','challans.id as id','challans.amount_paid as amount','students.roll_no as roll_no','challans.bank_challan_detail as bank_challan_detail','student_courses.course_type_id as course_type_id','challans.created_by as created_by','academic_periods.academic_period as peroid_name');

        	if(!empty($id))
        	{
        		$result->where('challans.id',$id);
        	}

 			if($paginate=='Y'){

           $result = $result->orderBy('students.id','DESC')->get();


            } else {
            	$result = $result->first();
            }
            return $result;
        }

/**
 * scopeGetSearchStudent get search student
 * @param  array $query           student details
 * @param  string $paginate        paginate yes
 * @param  integer $application_no  application number
 * @param  integer $student_id      student id
 * @param  integer $academic_year   academic year
 * @param  integer $course_id       course id
 * @param  array  $searchParameter search parameter
 * @param  integer $batchId         batch id
 * @param  integer $peroidId        period id
 * @param  string $flag            flag
 * @param  integer $institute_id    instituiton id
 * @return array                  student data
 */
        public function scopeGetSearchStudent($query,$paginate='Y',$application_no=null,$student_id=null,$academic_year=null,$course_id=null,$searchParameter = array(),$batchId=null,$peroidId=null,$flag=null,$institute_id=null)
        {
              $courseType = (isset($searchParameter['courseType']) ? $searchParameter['courseType']: null);

              $course_id = (isset($searchParameter['course_id']) ? $searchParameter['course_id'] : $course_id);

              $academic_year = (isset($searchParameter['academicYear']) ? $searchParameter['academicYear'] : $academic_year);

              $application_no = (isset($searchParameter['applicationNo']) ? $searchParameter['applicationNo']:$application_no);

              $fatherName = (isset($searchParameter['fatherName']) ? $searchParameter['fatherName']: null);


        	 $result = $query->join('student_courses', function($student_courses) {

		      $student_courses->on('student_courses.student_id', '=', 'students.id');

		        })->join('course_types',function($course_types){

		       $course_types->on('course_types.id', '=', 'student_courses.course_type_id');

		        })->join('institution_courses',function($institution_courses){

                    $institution_courses->on('institution_courses.id','=','student_courses.institution_course_id');

                })->join('courses', function($courses) {

		      $courses->on('courses.id', '=', 'institution_courses.course_id');

               })->join('course_peroid_details', function($course_peroid_details) {

              $course_peroid_details->on('course_peroid_details.id', '=', 'student_courses.course_peroid_id');

               })->join('academic_periods', function($academic_periods) {

                    $academic_periods->on('academic_periods.id', '=', 'course_peroid_details.academic_period_id');

            })->leftJoin('sections', function($sections) {

              $sections->on('sections.id', '=', 'student_courses.course_section_id');

               })->join('statuses', function($statuses) {

		      $statuses->on('statuses.id', '=', 'students.status_id');
				})

        ->select('students.first_name as first_name','students.middle_name as middle_name','students.last_name as last_name','students.application_no as application_no','institution_courses.custom_course_name as course_name','students.admission_date as admission_date','statuses.status_key as status','students.id as id','student_courses.academic_year_id as academic_year_id','students.institute_id as institute_id','statuses.id as status_id','statuses.color_class as color_class','student_courses.institution_course_id as institution_course_id','course_types.id as course_type_id','course_types.course_type_code as course_type_code','students.roll_no as roll_no','students.register_no as register_no','students.date_of_leaving as date_of_leaving','students.fathers_mobile_no as contact_no_1','students.mothers_mobile_no as contact_no_2','student_courses.course_peroid_id as course_peroid_id','sections.id as section_id','student_courses.id as student_courses_id','students.mobile as mobile','students.fathers_name as fathers_name','students.phone_two as phone_two','students.guardian_mobile_no as guardian_mobile_no','academic_periods.academic_period as academic_period','course_types.course_types as course_types','sections.name as section_name');



             if(!empty($course_id) && !empty($academic_year) && !empty($peroidId) && !empty($batchId))
            {
                if($flag == 'Y')
                {
                    $result->where('student_courses.institution_course_id',$course_id)->where('academic_periods.id',$peroidId)->where('student_courses.academic_year_id',$academic_year)->where('student_courses.course_section_id',$batchId)->where('student_courses.current_course','Y')->where('students.institute_id',$institute_id)->orderBy('students.roll_no','asc');
                }
                else
                {
                $result->where('student_courses.institution_course_id',$course_id)->where('student_courses.course_peroid_id',$peroidId)->where('student_courses.course_section_id',$batchId)->where('student_courses.academic_year_id',$academic_year)->where('student_courses.current_course','Y')->orderBy('students.roll_no','asc');
              }
            }
            elseif(!empty($course_id) && !empty($academic_year) && !empty($peroidId))
            {

                if($flag == 'Y')
                {
                    $result->where('student_courses.institution_course_id',$course_id)->where('academic_periods.id',$peroidId)->where('student_courses.academic_year_id',$academic_year)->where('students.institute_id',$institute_id)->where('student_courses.current_course','Y')->orderBy('students.roll_no','asc');
                }
                else
                {
                $result->where('student_courses.institution_course_id',$course_id)->where('student_courses.course_peroid_id',$peroidId)->where('student_courses.academic_year_id',$academic_year)->where('student_courses.current_course','Y')->whereNull('student_courses.course_section_id')->orderBy('students.roll_no','asc');
            }


            }elseif(!empty($peroidId) && !empty($academic_year) )
            {
               if($flag == 'Y')
                {
                   $result = $result->where('academic_periods.id',$peroidId)->where('student_courses.academic_year_id',$academic_year)->where('students.institute_id',$institute_id);
                }
                else
             $result = $result->where('academic_periods.id',$peroidId)->where('student_courses.academic_year_id',$academic_year);

            }
            elseif(!empty($course_id) && !empty($academic_year))
            {
                $result = $result->where('student_courses.institution_course_id',$course_id)->where('student_courses.academic_year_id',$academic_year);
            }
            elseif(!empty($academic_year) && !empty($institute_id))
            {
              $result->where('student_courses.academic_year_id',$academic_year)->where('students.institute_id',$institute_id)->orderBy('students.roll_no','asc')->orderBy('students.roll_no','asc');
            }

        	elseif(!empty($academic_year))
            {
                 if($flag == 'Y')
                {
                  $result->where('student_courses.academic_year_id',$academic_year)->where('students.institute_id',$institute_id)->orderBy('students.roll_no','asc')->orderBy('students.roll_no','asc');
                }
                else
                $result->where('student_courses.academic_year_id',$academic_year)->orderBy('students.roll_no','asc');
            }

            elseif(!empty($courseType))
            {
                if(is_array($courseType)){
                    $result->whereIn('student_courses.course_type_id',$courseType)->where('student_courses.academic_year_id',$academic_year)->orderBy('students.roll_no','asc');
                } else {
                    $result->where('student_courses.course_type_id',$courseType)->where('student_courses.academic_year_id',$academic_year)->orderBy('students.roll_no','asc');
                }

            }


        elseif(!empty($course_id) && !empty($academic_year) && !empty($courseType))
            {
                $result->where('student_courses.institution_course_id',$course_id)->where('student_courses.academic_year_id',$academic_year)->where('student_courses.course_peroid_id',$peroidId)->where('student_courses.course_type_id',$courseType)->orderBy('students.roll_no','asc');
            }



            elseif(!empty($application_no))
            {
            $result->where('students.application_no',$application_no)->orwhere('students.roll_no',$application_no)->orWhere(DB::raw("CONCAT(`students`.`first_name`, ' ', `students`.`middle_name`, ' ', `students`.`last_name`)"), 'LIKE', "%".$application_no."%")->first();
            }



 			if($paginate=='Y'){

           $result = $result->orderBy('students.application_id','asc')->get();


            } else {
            	$result = $result->first();
            }
            return $result;

        }

/**
 * scopeGetStudentReport get student report
 * @param  array $query           student
 * @param  string $paginate        paginate
 * @param  integer $application_no  application number
 * @param  integer $student_id      student id
 * @param  integer $academic_year   academic year
 * @param  integer $course_id       course id
 * @param  array  $searchParameter serach parameter
 * @return array                  student report
 */
          public function scopeGetStudentReport($query,$paginate='Y',$application_no=null,$student_id=null,$academic_year=null,$course_id=null,$searchParameter = array())
        {

              $courseType = (isset($searchParameter['courseType']) ? $searchParameter['courseType']: null);

              $course_id = (isset($searchParameter['course_id']) ? $searchParameter['course_id'] : $course_id);

              $academic_year = (isset($searchParameter['academicYear']) ? $searchParameter['academicYear'] : $academic_year);

               $feeStatus = (isset($searchParameter['feeStatus']) ? $searchParameter['feeStatus'] : null);


        	 $result = $query->join('student_courses', function($student_courses) {

              $student_courses->on('student_courses.student_id', '=', 'students.id')->where('student_courses.current_course','=','Y');

                })->join('institution_courses',function($institution_courses){

        $institution_courses->on('institution_courses.id','=','student_courses.institution_course_id');

         })->join('course_types',function($course_types){

		       $course_types->on('course_types.id', '=', 'student_courses.course_type_id');

		        }) ->join('courses', function($courses) {

		      $courses->on('courses.id', '=', 'institution_courses.course_id');
				})
				->join('statuses', function($statuses) {

		      $statuses->on('statuses.id', '=', 'students.status_id');

				})->leftJoin('nationalities', function($nationalities) {

		      $nationalities->on('nationalities.id', '=', 'students.nationality_id');

		        })->leftJoin('religions', function($religions) {

		      $religions->on('religions.id', '=', 'students.religion_id');

		        })->leftJoin('castes', function($castes) {

		      $castes->on('castes.id', '=', 'students.caste_id');

		        })

        ->select('students.first_name as first_name','students.middle_name as middle_name','students.last_name as last_name','students.application_no as application_no','institution_courses.custom_course_name as course_name','students.admission_date as admission_date','statuses.status_key as status','students.id as id','student_courses.academic_year_id as academic_year_id','students.institute_id as institute_id','statuses.id as status_id','student_courses.institution_course_id as course_id','student_courses.course_type_id as course_type_id','course_types.course_type_code as course_type_code','students.roll_no as roll_no','students.admission_no as admission_no','students.fathers_name as fathers_name','students.mothers_name as mothers_name','students.guardian_name as guardian_name','students.mothers_occupation as mothers_occupation','students.fathers_occupation as fathers_occupation','students.guardians_occupation as guardians_occupation','students.dob as dob','nationalities.name as nationality','religions.name as religion','castes.name as caste','students.profile_photo as profile_photo','students.register_no as register_no','students.gender as gender','students.mobile as mobile','students.email_id as email_id','students.annual_income as annual_income');

        	if(!empty($application_no))
        	{
        	$result->where('students.application_no','LIKE','%'.$application_no.'%')->orwhere('students.roll_no','LIKE','%'.$application_no.'%')->orwhere('students.register_no','LIKE','%'.$application_no.'%')->orWhere(DB::raw("CONCAT(`students`.`first_name`, ' ', `students`.`middle_name`, ' ', `students`.`last_name`)"), 'LIKE', "%".$application_no."%")->distinct()->get();
        	}
        	if(!empty($student_id))
        	{
        		$result->where('students.id',$student_id);
        	}

            if(!empty($academic_year))
            {
                $result->where('student_courses.academic_year_id',$academic_year)->orderBy('students.roll_no','asc')->distinct();
            }
            if(!empty($course_id))
            {
                $result->where('student_courses.institution_course_id',$course_id)->orderBy('students.roll_no','asc')->distinct();
            }

            if(!empty($courseType))
            {
                if(is_array($courseType)){
                    $result->whereIn('student_courses.course_type_id',$courseType)->orderBy('students.roll_no','asc');
                } else {
                    $result->where('student_courses.course_type_id',$courseType)->orderBy('students.roll_no','asc');
                }
            }

            if(!empty($course_id) && !empty($courseType))
            {

                if(is_array($courseType)){
                    $result->where('student_courses.institution_course_id',$course_id)->whereIn('student_courses.course_type_id',$courseType)->orderBy('students.roll_no','asc')->distinct();
                } else {
                    $result->where('student_courses.institution_course_id',$course_id)->where('student_courses.course_type_id',$courseType)->orderBy('students.roll_no','asc')->distinct();
                }
            }

        	if(!empty($academic_year) && !empty($course_id))
        	{
        		$result->where('student_courses.academic_year_id',$academic_year)->where('student_courses.institution_course_id',$course_id)->orderBy('students.roll_no','asc')->distinct();
        	}



 			if($paginate=='Y'){

           $result = $result->orderBy('students.application_id','asc')->get();


            } else {
            	$result = $result->first();
            }
            return $result;

        }

/**
 * studentPreviousCourses student previous courses
 * @return array previous courses data
 */
        public function studentPreviousCourses(){
            return $this->hasMany(StudentCourse::class,'student_id','id')->valid()->where('current_course','N');
        }

/**
 * studentCurrentCourses student current courses data
 * @return array current course data
 */
        public function studentCurrentCourses(){
            return $this->hasOne(StudentCourse::class,'student_id','id')->valid()->where('current_course','Y');
        }

/**
 * nationality relationship to the nationality model
 * @return string nationality information
 */
        public function nationality()
        {
            return $this->belongsTo('App\Nationality','nationality_id','id');
        }

/**
 * institutions relastionship to the institution model
 * @return string institution details
 */
        public function institutions()
        {
            return $this->belongsTo('App\Institution','institute_id','id');
        }

/**
 * religion relastionship to the Religion model.
 * @return string religion
 */
        public function religion()
        {
            return $this->belongsTo('App\Religion','religion_id','id');
        }


/**
 * applicationAddress relationship to the ApplicationAddress model
 * @return string application address
 */
        public function applicationAddress()
        {
            return $this->hasOne('App\ApplicationAddress');
        }

/**
 * studentCertificate relationship to the StudentCertificate model
 * @return array student certificate information
 */
        public function studentCertificate()
        {
            return $this->hasMany('App\StudentCertificate');
        }

/**
 * feeReceiptOfStudent fee receipt of student
 * @return array feereceipt details
 */
       public function feeReceiptOfStudent()
       {
        return $this->hasMany('App\FeeReceipt')->orderBy('id','DESC');
       }

/**
 * applicationAcademic application academic details of student
 * @return array applicationacademic
 */
        public function applicationAcademic()
        {
            return $this->hasOne('App\ApplicationAcademic');
        }

/**
 * activityClassFee activity class fee information of student
 * @return array activity class fee details
 */
          public function activityClassFee()
       {
        return $this->hasMany('App\ActivityClassFee','id','student_id');
       }

/**
 * institution institution details of a student
 * @return array institution details
 */
       public function institution()
        {
            return $this->belongsTo('App\Institution','institute_id','id');
        }

        /*public function scopeStudentAdmissionReport($query,$paginate='Y',$searchParameter = array())
        {


            $academicYear = (isset($searchParameter['academicYear']) ? $searchParameter['academicYear'] : null);

            $courseName = (isset($searchParameter['courseName']) ? $searchParameter['courseName'] : null);

             $coursePeroid = (isset($searchParameter['coursePeroid']) ? $searchParameter['coursePeroid'] : null);


            $result = $query->join('student_courses', function($student_courses) {

              $student_courses->on('student_courses.student_id', '=', 'students.id')->where('student_courses.current_course','=','Y');

                })->join('institution_courses',function($institution_courses){

        $institution_courses->on('institution_courses.id','=','student_courses.institution_course_id');

         })->join('course_types',function($course_types){

               $course_types->on('course_types.id', '=', 'student_courses.course_type_id');

                })->join('course_peroid_details', function($course_peroid_details) {

                     $course_peroid_details->on('course_peroid_details.id', '=', 'student_courses.course_peroid_id');

                })->join('courses', function($courses) {

              $courses->on('courses.id', '=', 'institution_courses.course_id');

                })->join('course_assignments',function($course_assignments){

                    $course_assignments->on('course_assignments.course_type_id','=','student_courses.course_type_id');

                })->join('academic_periods',function($academic_periods){

            $academic_periods->on('academic_periods.id','=','course_peroid_details.academic_period_id');

        })->select('institution_courses.custom_course_name as course_name', 'course_assignments.total_seats as no_of_seats', DB::raw('sum(case when student_courses.course_type_id in (1) then 1 else 0 end) as aided'), DB::raw('sum(case when student_courses.course_type_id in (2) then 1 else 0 end) as unAided'),'academic_periods.academic_period as peroid_name','course_assignments.total_remaining_seats as total_remaining_seats');

                    if(!empty($courseName) && !empty($coursePeroid) && !empty($academicYear))
                    {
                        $result = $result->where('student_courses.institution_course_id',$courseName)->where('student_courses.course_peroid_id',$coursePeroid)->where('student_courses.academic_year_id',$academicYear)->where('students.status_id','!=',$this->STATUS_CANCELLED)->whereNull('student_courses.deleted_at')->groupBy('student_courses.institution_course_id')->groupBy('student_courses.course_peroid_id');
                    }



               if($paginate=='Y'){

                $result = $result->whereNull('students.deleted_at')->orderBy('courses.course_peroid','ASC')->get();

                 }
                  else
                  {
                        $result = $result->first();
                  }


            return $result;


        }
*/

/**
 * scopeStudentAdmissionReport student admisssion report of a student
 * @param  array $query           student
 * @param  string $paginate        yes
 * @param  array  $searchParameter search parameter
 * @return array                  student admission report
 */
        public function scopeStudentAdmissionReport($query,$paginate='Y',$searchParameter = array())
        {


            $academicYear = (isset($searchParameter['academicYear']) ? $searchParameter['academicYear'] : null);

            $courseName = (isset($searchParameter['courseName']) ? $searchParameter['courseName'] : null);

             $coursePeroid = (isset($searchParameter['coursePeroid']) ? $searchParameter['coursePeroid'] : null);

              $instituteId = (isset($searchParameter['instituteId']) ? $searchParameter['instituteId'] : null);


            $result = $query->join('institutions', function($institutions) {

              $institutions->on('institutions.id', '=', 'students.institute_id');

                })->join('student_courses', function($student_courses) {

                $student_courses->on('student_courses.student_id', '=', 'students.id')->where('student_courses.current_course','=','Y');

                })->join('institution_courses',function($institution_courses){

                $institution_courses->on('institution_courses.id','=','student_courses.institution_course_id');

                 })->join('course_peroid_details', function($course_peroid_details) {

                     $course_peroid_details->on('course_peroid_details.id', '=', 'student_courses.course_peroid_id');

                })->join('academic_periods',function($academic_periods){

                    $academic_periods->on('academic_periods.id','=','course_peroid_details.academic_period_id');

                })->join('courses', function($courses) {

                      $courses->on('courses.id', '=', 'institution_courses.course_id');

                  })->select('institution_courses.custom_course_name as course_name',DB::raw('sum(case when student_courses.course_type_id in (1,3) then 1 else 0 end) as aided'), DB::raw('sum(case when student_courses.course_type_id in (2,4) then 1 else 0 end) as unAided'));


                if(!empty($instituteId) && !empty($academicYear))
                {
                  $result = $result->where('student_courses.academic_year_id',$academicYear)->where('institution_courses.institute_id',$instituteId)->where('students.status_id','!=',$this->STATUS_CANCELLED)->groupBy('student_courses.institution_course_id');
                }
                elseif(!empty($courseName)  && !empty($academicYear))
                    {
                        $result = $result->where('student_courses.institution_course_id',$courseName)->where('student_courses.course_peroid_id',$coursePeroid)->where('student_courses.academic_year_id',$academicYear)->where('students.status_id','!=',$this->STATUS_CANCELLED)->whereNull('student_courses.deleted_at')->groupBy('student_courses.institution_course_id')->groupBy('student_courses.course_peroid_id');
                    }
                elseif(!empty($academicYear))
                    {
                        $result = $result->where('student_courses.academic_year_id',$academicYear)->where('students.status_id','!=',$this->STATUS_CANCELLED)->groupBy('student_courses.institution_course_id');
                    }


               if($paginate=='Y'){

                $result = $result->whereNull('students.deleted_at')->orderBy('institution_courses.custom_course_name','ASC')->get();

                 }
                  else
                  {
                        $result = $result->first();
                  }


            return $result;


        }

/**
 * scopeStudentAdmissionChallanRaisedReport student admission challan raised report
 * @param  array $query           student
 * @param  string $paginate        yes
 * @param  array  $searchParameter search parameter
 * @return array                  challan raised information
 */
        public function scopeStudentAdmissionChallanRaisedReport($query,$paginate='Y',$searchParameter = array())
        {
            $academic_year = (isset($searchParameter['academicYear']) ? $searchParameter['academicYear'] : null);

            $fromDate = (isset($searchParameter['fromDate']) ? $searchParameter['fromDate'] : null);

             $toDate = (isset($searchParameter['toDate']) ? $searchParameter['toDate'] : null);


            $result = $query->join('student_courses', function($student_courses) {

              $student_courses->on('student_courses.student_id', '=', 'students.id')->where('student_courses.current_course','=','Y');

                })->join('institution_courses',function($institution_courses){

                 $institution_courses->on('institution_courses.id','=','student_courses.institution_course_id');

         })->join('courses', function($courses) {

              $courses->on('courses.id', '=', 'institution_courses.course_id');

                })->join('course_peroid_details', function($course_peroid_details) {

              $course_peroid_details->on('course_peroid_details.id', '=', 'student_courses.course_peroid_id');

            })->join('challans', function($challans) {

              $challans->on('challans.student_id', '=', 'student_courses.student_id');

            })->join('academic_periods',function($academic_periods){

            $academic_periods->on('academic_periods.id','=','course_peroid_details.academic_period_id');

        })->select(DB::raw('sum(case when students.status_id='.$this->STATUS_ADMITTED.' then 1 else 0 end) as no_of_admissions'),DB::raw('sum(case when challans.status_id='.$this->STATUS_PENDING.' then 1 else 0 end) as challan_raised'),'courses.total_seats as total_seats','institution_courses.custom_course_name as course_name','academic_periods.academic_period as peroid_name');

                if(!empty($fromDate) && !empty($toDate) && !empty($academic_year))
              {
                $result= $result->where('students.admission_date','>=',$fromDate)->where('students.admission_date','<=',$toDate)->where('student_courses.academic_year_id',$academic_year)->groupBy('student_courses.institution_course_id')->groupBy('student_courses.course_peroid_id')->orderBy('institution_courses.custom_course_name','ASC');
              }

               if($paginate=='Y'){

                $result = $result->get();

                 } else {
              $result = $result->first();
            }


            return $result;




        }


/**
 * scopegetStudentFeeReport get student fee report
 * @param  array $query           student
 * @param  string $paginate        paginate yes
 * @param  array  $searchParameter search parameter
 * @return array                  student fee report
 */
        public function scopegetStudentFeeReport($query,$paginate='Y',$searchParameter = array())
        {
            $academic_year = (isset($searchParameter['academicYear']) ? $searchParameter['academicYear'] : null);


             $course_id = (isset($searchParameter['courseName']) ? $searchParameter['courseName'] : null);

             $coursePeriodId = (isset($searchParameter['coursePeriodId']) ? $searchParameter['coursePeriodId'] : null);

                $instituteId = (isset($searchParameter['instituteId']) ? $searchParameter['instituteId'] : null);



                $result=$query->join('student_courses', function($student_courses) {

              $student_courses->on('student_courses.student_id', '=', 'students.id')->where('student_courses.current_course','=','Y');

                })->join('fee_receipts',function($fee_receipts)
                {
                    $fee_receipts->on('fee_receipts.student_course_id','=','student_courses.id');

                })->join('institution_courses',function($institution_courses){

                 $institution_courses->on('institution_courses.id','=','student_courses.institution_course_id');

                  })->join('courses', function($courses) {

                     $courses->on('courses.id', '=', 'institution_courses.course_id');

                })->join('course_peroid_details', function($course_peroid_details) {

                    $course_peroid_details->on('course_peroid_details.id', '=', 'student_courses.course_peroid_id');

            })->join('course_types',function($course_types){

               $course_types->on('course_types.id', '=', 'student_courses.course_type_id');

                })->join('challans', function($challans) {

                     $challans->on('challans.id','=','fee_receipts.challan_id');

                })->join('discounts', function($discounts) {

                     $discounts->on('discounts.challan_id','=','challans.id');

                })->join('academic_periods',function($academic_periods){

            $academic_periods->on('academic_periods.id','=','course_peroid_details.academic_period_id');

        })->select('students.application_no as application_no', 'students.roll_no as roll_no', 'students.first_name as first_name','student_courses.institution_course_id as institution_course_id','student_courses.course_type_id as course_type_id','institution_courses.custom_course_name as course_name','student_courses.total_fee_amount as total_fee_amount','course_types.course_types as course_types','academic_periods.academic_period as peroid_name',DB::raw('sum(fee_receipts.fees_collected) as amount_paid'),DB::raw('sum(discounts.discount_amount) as discount_amount'))->groupBy('fee_receipts.student_course_id')->where('fee_receipts.fee_type',FeeReceipt::ADMISSION);




                    if(!empty($course_id))
                    {
                        $result = $result->where('student_courses.institution_course_id',$course_id)->where('student_courses.course_peroid_id',$coursePeriodId)->where('fee_receipts.status_id',$this->STATUS_RECONCILLLED);
                    }

                if(!empty($instituteId) && !empty($academic_year))
                {
                  $result = $result->where('student_courses.academic_year_id',$academic_year)->where('institution_courses.institute_id',$instituteId)->where('students.status_id','!=',$this->STATUS_CANCELLED);
                }


                if(!empty($instituteId) && !empty($academic_year) && !empty($course_id))
                {
                  $result = $result->where('student_courses.academic_year_id',$academic_year)->where('institution_courses.institute_id',$instituteId)->where('student_courses.institution_course_id',$course_id)->where('students.status_id','!=',$this->STATUS_CANCELLED);
                }

              if(!empty($instituteId) && !empty($academic_year) && !empty($course_id) && !empty($coursePeriodId))
                {
                  $result = $result->where('student_courses.academic_year_id',$academic_year)->where('institution_courses.institute_id',$instituteId)->where('student_courses.institution_course_id',$course_id)->where('student_courses.course_peroid_id',$coursePeriodId)->where('students.status_id','!=',$this->STATUS_CANCELLED);
                }


                    if($paginate=='Y'){

                        $result = $result->orderBy('students.id','DESC')->get();

                        } else {
                          $result = $result->first();
                        }

                        return $result;

            }



/**
 * scopegetStudentHistory student history
 * @param  array $query               student
 * @param  string $paginate            paginate yes
 * @param  integer $academic_year_id    academic year id
 * @param  string $courseName          course name
 * @param  array $coursePeriodDetails course period details
 * @param  integer $institute_id        institution id
 * @return array                      student history
 */
            public function scopegetStudentHistory($query,$paginate='Y',$academic_year_id=null,$courseName=null,$coursePeriodDetails=null,$institute_id=null)
            {
                //return $coursePeriodDetails;
                 $result = $query->join('student_courses', function($student_courses) {

              $student_courses->on('student_courses.student_id', '=', 'students.id');

                })->join('academics',function($academics){

                    $academics->on('academics.id','=','student_courses.academic_year_id');

                })->join('course_types',function($course_types){

               $course_types->on('course_types.id', '=', 'student_courses.course_type_id');

                })->join('institution_courses',function($institution_courses){

                 $institution_courses->on('institution_courses.id','=','student_courses.institution_course_id');

             })->join('course_peroid_details', function($course_peroid_details) {

                    $course_peroid_details->on('course_peroid_details.id', '=', 'student_courses.course_peroid_id');

            })->join('academic_periods', function($academic_periods) {

                    $academic_periods->on('academic_periods.id', '=', 'course_peroid_details.academic_period_id');

            })->join('courses', function($courses) {

              $courses->on('courses.id', '=', 'institution_courses.course_id');
                })
                ->leftJoin('statuses', function($statuses) {

              $statuses->on('statuses.id', '=', 'students.status_id');

                })->leftJoin('nationalities', function($nationalities) {

              $nationalities->on('nationalities.id', '=', 'students.nationality_id');

                })->leftJoin('religions', function($religions) {

              $religions->on('religions.id', '=', 'students.religion_id');

                })->leftJoin('citizen_groups', function($citizen_groups) {

              $citizen_groups->on('citizen_groups.id', '=', 'students.citizen_group_id');

                })->leftJoin('course_sections', function($course_sections) {

              $course_sections->on('course_sections.id', '=', 'student_courses.course_section_id');

                })->leftJoin('sections',function($sections){

                    $sections->on('sections.id','=','course_sections.section_id');
                })

                ->leftJoin('student_elective_subjects',function($student_elective_subjects){

                    $student_elective_subjects->on('student_elective_subjects.student_course_id','=','student_courses.id');

                })->leftJoin('course_elective_subjects',function($course_elective_subjects){

                    $course_elective_subjects->on('course_elective_subjects.id','=','student_elective_subjects.course_elective_subject_id');

                })->leftJoin('subjects',function($subjects){

                    $subjects->on('subjects.id','=','course_elective_subjects.subject_id');

                })->select('students.first_name as first_name','students.middle_name as middle_name','students.last_name as last_name','students.application_no as application_no','institution_courses.custom_course_name as course_name','students.admission_date as admission_date','statuses.status_key as status','course_types.course_types as course_types','students.roll_no as roll_no','students.admission_no as admission_no','students.fathers_name as fathers_name','students.mothers_name as mothers_name','students.guardian_name as guardian_name','students.dob as dob','nationalities.name as nationality','religions.name as religion','students.caste as caste','students.register_no as register_no','students.gender as gender','students.mobile as mobile','citizen_groups.name as citizen_group_name','sections.name as section_name','students.admission_no as admission_no','students.adhaar_card_number as adhaar_card_number','subjects.name as subject_name','students.id as id','academic_periods.academic_period as academic_period');

                      if(!empty($courseName) && !empty($academic_year_id) && !empty($coursePeriodDetails) && !empty($institute_id))
                        {
                            //return $coursePeriodDetails;
                            $result = $result->where('student_courses.institution_course_id',$courseName)->where('student_courses.academic_year_id',$academic_year_id)->where('course_peroid_details.academic_period_id',$coursePeriodDetails)->where('institution_courses.institute_id',$institute_id);
                        }

                           else if(!empty($courseName) && !empty($academic_year_id) &&  !empty($institute_id))
                        {
                            $result = $result->where('student_courses.institution_course_id',$courseName)->where('student_courses.academic_year_id',$academic_year_id)->where('institution_courses.institute_id',$institute_id);
                        }

                           else if(!empty($coursePeriodDetails) && !empty($academic_year_id) )
                        {

                            $result = $result->where('academic_periods.id',$coursePeriodDetails)->where('student_courses.academic_year_id',$academic_year_id)->where('institution_courses.institute_id',$institute_id);

                        }

                        else if(!empty($academic_year_id) &&  !empty($institute_id))
                        {
                             $result = $result->where('student_courses.academic_year_id',$academic_year_id)->where('institution_courses.institute_id',$institute_id);
                        }

                        else if(!empty($courseName) &&  !empty($institute_id))
                        {
                            $result = $result->where('student_courses.institution_course_id',$courseName)->where('institution_courses.institute_id',$institute_id);
                        }
                        else if(!empty($coursePeriodDetails) &&  !empty($institute_id))
                        {
                             $result = $result->where('student_courses.course_peroid_id',$coursePeriodDetails)->where('institution_courses.institute_id',$institute_id);
                        }


                        if($paginate=='Y'){

                        $result = $result->whereNull('students.deleted_at')->orderBy('institution_courses.custom_course_name','asc')->orderBy('course_types.course_types','asc')->orderBy('sections.name','asc')->orderBy('subjects.name','asc')->orderBy('students.gender','desc')->orderBy('students.first_name','asc')->get();

                        }

                        return $result;




            }


/**
 * scopeGetStudentSections get student sections
 * @param  array $query           student
 * @param  string $paginate        paginate yes
 * @param  integer $application_no  application number
 * @param  integer $student_id      student id
 * @param  integer $academic_year   academic year
 * @param  integer $course_id       course id
 * @param  array  $searchParameter serach parameter
 * @param  integer $batchId         batch id
 * @param  integer $peroidId        period id
 * @return array                  student sections details
 */
             public function scopeGetStudentSections($query,$paginate='Y',$application_no=null,$student_id=null,$academic_year=null,$course_id=null,$searchParameter = array(),$batchId=null,$peroidId=null)
        {
              $courseType = (isset($searchParameter['courseType']) ? $searchParameter['courseType']: null);

              $course_id = (isset($searchParameter['course_id']) ? $searchParameter['course_id'] : $course_id);

              $academic_year = (isset($searchParameter['academicYear']) ? $searchParameter['academicYear'] : $academic_year);

              $application_no = (isset($searchParameter['applicationNo']) ? $searchParameter['applicationNo']:$application_no);

              $fatherName = (isset($searchParameter['fatherName']) ? $searchParameter['fatherName']: null);

             $result = $query->join('student_courses', function($student_courses) {

              $student_courses->on('student_courses.student_id', '=', 'students.id');

                })->join('institution_courses',function($institution_courses){

        $institution_courses->on('institution_courses.id','=','student_courses.institution_course_id');

         })->join('course_types',function($course_types){

               $course_types->on('course_types.id', '=', 'student_courses.course_type_id');

                })->join('courses', function($courses) {

              $courses->on('courses.id', '=', 'institution_courses.course_id');

               })->join('course_peroid_details', function($course_peroid_details) {

              $course_peroid_details->on('course_peroid_details.id', '=', 'student_courses.course_peroid_id');

               })->join('sections', function($sections) {

              $sections->on('sections.id', '=', 'student_courses.course_section_id');

               })->join('statuses', function($statuses) {

              $statuses->on('statuses.id', '=', 'students.status_id');
                })

        ->select('students.first_name as first_name','students.middle_name as middle_name','students.last_name as last_name','students.application_no as application_no','institution_courses.custom_course_name as course_name','students.admission_date as admission_date','statuses.status_key as status','students.id as id','student_courses.academic_year_id as academic_year_id','students.institute_id as institute_id','statuses.id as status_id','statuses.color_class as color_class','student_courses.institution_course_id as course_id','course_types.id as course_type_id','course_types.course_type_code as course_type_code','students.roll_no as roll_no',
            'students.register_no as register_no','students.date_of_leaving as date_of_leaving','student_courses.course_peroid_id as course_peroid_id','sections.id as section_id','student_courses.id as student_courses_id');

             if(!empty($course_id) && !empty($academic_year) && !empty($peroidId) && !empty($batchId))
            {
                $result->where('student_courses.institution_course_id',$course_id)->where('student_courses.course_peroid_id',$peroidId)->where('student_courses.course_section_id',$batchId)->where('student_courses.academic_year_id',$academic_year)->where('student_courses.current_course','Y')->orderBy('students.roll_no','asc');
            }

            if($paginate=='Y'){

           $result = $result->orderBy('students.application_id','asc')->get();


            } else {
                $result = $result->first();
            }
            return $result;

        }

/**
 * scopeGetStudentStatisticReportByCategory get student statistic report
 * @param  array $query        student
 * @param  string $paginate     paginate y
 * @param  integer $academicYear academic year
 * @return array               student statistic report
 */
        public function scopeGetStudentStatisticReportByCategory($query,$paginate='Y',$academicYear=null){


            $result = $query->leftJoin('citizen_groups', function( $citizen_groups ){
                $citizen_groups->on('citizen_groups.id', '=', 'students.citizen_group_id');
            })->leftJoin('student_courses', function($student_courses) {
                    $student_courses->on('student_courses.student_id', '=', 'students.id');
            })->leftJoin('institution_courses',function($institution_courses){
                $institution_courses->on('institution_courses.id','=','student_courses.institution_course_id');
            })->join('course_peroid_details',function($course_peroid_details){
                $course_peroid_details->on('course_peroid_details.id','=','student_courses.course_peroid_id');
            })->join('course_sections',function($course_sections){
                $course_sections->on('course_sections.id','=','student_courses.course_section_id');
            })->join('courses',function($courses){
                $courses->on('courses.id','=','institution_courses.course_id');
            })->join('academic_periods',function($academic_periods){
                $academic_periods->on('academic_periods.id','=','course_peroid_details.academic_period_id');
            })->join('sections',function($sections){
                $sections->on('sections.id','=','course_sections.section_id');
            })->select('institution_courses.custom_course_name as course_name','sections.name as section_name','citizen_groups.name as citizen_name','students.gender as student_gender','academic_periods.academic_period as academic_period','academic_periods.id as academic_period_id', DB::raw('count(students.gender) as gender'))->where('students.status_id','!=',$this->STATUS_CANCELLED)->where('student_courses.academic_year_id',$academicYear)->where('students.status_id','!=','5')->groupBy('institution_courses.custom_course_name')->groupBy('academic_periods.academic_period')->groupBy('sections.name')->groupBy('citizen_groups.name')->groupBy('students.gender')->orderBy('academic_periods.academic_period','ASC')->orderBy('institution_courses.custom_course_name','ASC')->orderBy('sections.name','ASC')->orderBy('citizen_groups.name','ASC')->orderBy('students.gender','ASC')->get();


        return $result;
    }




/**
 * scopeGetStudentAdmitted student admitted
 * @param  array $query student
 * @return array        student admitted information
 */
    public function scopeGetStudentAdmitted($query)
  {
     $result = $query->join('student_courses',function($student_courses){

        $student_courses->on('student_courses.student_id','=','students.id')->where('current_course','=','Y');

     })->join('institution_courses',function($institution_courses){

        $institution_courses->on('institution_courses.id','=','student_courses.institution_course_id');

     })->join('academics',function($academics)
     {
        $academics->on('academics.id','=','student_courses.academic_year_id')->whereIn('institution_courses.institute_id',unserializeConstant(CURRENTINSTITUE));

    })->join('institutions',function($institutions){

        $institutions->on('institutions.id','=','institution_courses.institute_id');

     })->select(DB::raw('count(students.id) as total_students'),'academics.start_date as academic_year','institutions.name as institute_name')->where('status_id',$this->STATUS_ADMITTED)->groupBy('student_courses.academic_year_id')->groupBy('institution_courses.institute_id')->get();

    return $result;
  }

/**
 * scopeGetStudentStatisticReportByLanguage student statistic report
 * @param  array $query        student
 * @param  string $paginate     paginate y
 * @param  integer $academicYear academic year
 * @return array student statistic report by language
 */
    public function scopeGetStudentStatisticReportByLanguage($query,$paginate='Y',$academicYear=null){

        $result = $query->leftJoin('student_courses', function($student_courses) {
                    $student_courses->on('student_courses.student_id', '=', 'students.id');
            })->leftJoin('student_elective_subjects', function( $student_elective_subjects ){
                $student_elective_subjects->on('student_elective_subjects.student_course_id', '=', 'student_courses.id');
            })->leftJoin('course_elective_subjects', function( $course_elective_subjects ){
                $course_elective_subjects->on('course_elective_subjects.id', '=', 'student_elective_subjects.course_elective_subject_id');
            })->leftJoin('subjects', function( $subjects ){
                $subjects->on('subjects.id', '=', 'course_elective_subjects.subject_id');
            })->leftJoin('institution_courses',function($institution_courses){
                $institution_courses->on('institution_courses.id','=','student_courses.institution_course_id');
            })->leftJoin('course_peroid_details',function($course_peroid_details){
                $course_peroid_details->on('course_peroid_details.id','=','student_courses.course_peroid_id');
            })->leftJoin('course_sections',function($course_sections){
                $course_sections->on('course_sections.id','=','student_courses.course_section_id');
            })->join('courses',function($courses){
                $courses->on('courses.id','=','institution_courses.course_id');
            })->join('academic_periods',function($academic_periods){
                $academic_periods->on('academic_periods.id','=','course_peroid_details.academic_period_id');
            })->join('sections',function($sections){
                $sections->on('sections.id','=','course_sections.section_id');
            })->select('institution_courses.custom_course_name as course_name','sections.name as section_name','subjects.name as subject_name','students.gender as student_gender','academic_periods.academic_period as academic_period','academic_periods.id as academic_period_id', DB::raw('count(students.gender) as gender'))->where('students.status_id','!=',$this->STATUS_CANCELLED)->where('student_courses.academic_year_id',$academicYear)->where('students.status_id','!=','5')->groupBy('institution_courses.custom_course_name')->groupBy('academic_periods.academic_period')->groupBy('sections.name')->groupBy('subjects.name')->groupBy('students.gender')->orderBy('academic_periods.academic_period','ASC')->orderBy('institution_courses.custom_course_name','ASC')->orderBy('sections.name','ASC')->orderBy('subjects.name','ASC')->orderBy('students.gender','ASC')->get();

        return $result;
    }


/**
 * institutionCourse institution course details of a student
 * @return array institution course details
 */
        public function institutionCourse(){
            return $this->belongsTo('App\InstitutionCourse');
        }

/**
 * studentCourse student course details of a student
 * @return array student course details
 */
        public function studentCourse(){
            return $this->hasOne(StudentCourse::class,'student_id','id');
        }


  }
