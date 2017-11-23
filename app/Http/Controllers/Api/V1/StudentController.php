<?php
/**
 * StudentController
 * @category Controller
 * @author ThinkPace
 */
namespace App\Http\Controllers\Api\V1;

use App\Models\ParentUser;
use App\Models\Client;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\AttendanceType;
use App\Models\AttendanceConfiguration;
use App\Models\CourseSubjectAttendanceDetail;
use App\Models\Assignment;
use App\Models\NoticeBoard;
use App\Models\CourseTimeTable;
use App\Models\CourseElectiveSubject;
use App\Models\StudentElectiveSubject;
use App\Models\InstitutionCalendar;
use DB;
use Illuminate\Support\Facades\Artisan;
use DateTime;
/**
 * StudentController
 * @category Controller
 * @author ThinkPace
 */
class StudentController extends BaseController
{

/**
 * getStudentAttendanceDetails get particular student Attendance details
 * @param  integer $parent_id  Parent id
 * @param  integer $client_id  Client Id
 * @param  integer $student_id Student id
 * @return array             Student Attendance Details
 */
    public function getStudentAttendanceDetails($parent_id,$client_id,$student_id)
    {

        if(!is_null($parent = ParentUser::find($parent_id))){

          $client = Client::find($client_id);

          $this->connectClient($client->clientConfiguration);

          $current_date = $this->request->get('current_date',date('Y-m-d'));

          $student = Student::find($student_id);

          $student_courses = $student->studentCurrentCourses;



          $student_attendances = StudentAttendance::where('student_course_id',$student_courses->id)->where('date',$current_date)->get();

          $student_attendance_details = [];

          if(!is_null($student_attendances)){

            foreach ($student_attendances as $individualId=>$student_attendance) {

              $attandance_details = null;

              $attandance_details = $student_attendance->attendanceType;

              $student_attendance_details['faculty_name'] = $student_attendance->user->name;
              $student_attendance_details['type'] = $attandance_details->group_code;
              $student_attendance_details['attendanceData'][$individualId]['message'] = $this->getStudentAbsentMessage($student_attendance,$attandance_details);
              //$student_attendance_details['attendanceData'][$individualId]['present'] = true;
              $student_attendance_details['attendanceData'][$individualId]['absent'] = true;
            }
            return $this->response->array(['data'=>$student_attendance_details]);

          }
          return $this->response->array(['data'=>[]]);

        }

        return $this->response->errorForbidden('Parent Details not found');

    }

/**
 * getStudentAbsentMessage get student absent message
 * @param  array $student_attendance Student attendance details
 * @param  array $attandance_details attendance etails
 * @return string                     session details
 */
    private function getStudentAbsentMessage($student_attendance,$attandance_details){

        switch ($attandance_details->code) {
          case 'SUBJECT_BASED':
            return $student_attendance->subject.' absent';
            break;
          case 'DAILY_ONCE':
            return "Full day";
            break;
          case 'DAILY_TWICE_AM':
            return 'First session';
            break;
          case 'DAILY_TWICE_PM':
            return 'Second session';
            break;
          default:
            # code...
            break;
        }

    }

/**
 * getStudentDetails get particular setudent details
 * @param  integer $parent_id  Parent Id
 * @param  integer $client_id  Client Id
 * @param  integer $student_id Student Id
 * @return array             particular student Details
 */
    public function getStudentDetails($parent_id,$client_id,$student_id)
    {

        if(!is_null($parent = ParentUser::find($parent_id))){

          $client = Client::find($client_id);

          $this->connectClient($client->clientConfiguration);

          $current_date = $this->request->get('current_date',date('Y-m-d'));

          $student = Student::find($student_id);

          $student_courses = $student->studentCurrentCourses;

          $selectQuery = "select st.id as student_id, i.name as Institute,CONCAT(COALESCE(st.first_name,''),' ',COALESCE(st.middle_name,''),' ',COALESCE(st.last_name,'')) As FullName, CONCAT(ic.custom_course_name,' ',ap.academic_period,' ',s.name) as CourseName,  CONCAT(start_date,' - ',end_date) as AcademicYear,st.admission_date as AdmissionDate,st.roll_no as RegisterNumber,st.profile_photo as ProfilePhoto,st.dob as DateOfBirth, mobile as ContactNumber from institutions i, academics ac, student_courses sc,students st,institution_courses ic,sections s,course_peroid_details cpd,academic_periods ap,course_sections cs WHERE st.id = sc.student_id AND sc.institution_course_id = ic.id AND sc.course_section_id = cs.id AND cs.section_id = s.id AND sc.course_peroid_id = cpd.id AND cpd.academic_period_id = ap.id AND sc.academic_year_id = ac.id AND ic.institute_id = i.id and sc.current_course ='Y' and st.id = $student_id ";

          $student_details = DB::connection('clientDB')->select(DB::raw($selectQuery));

          if($student_details){
            $student_details = array_first($student_details);
            if(!empty($student_details->ProfilePhoto))
              $student_details->ProfilePhoto = $client->clientConfiguration->client_site_url.'/photos/'.$student_details->ProfilePhoto;
            return $this->response->array(['data'=>$student_details]);
          }

        }

        return $this->response->errorForbidden('Parent Details not found');

    }

/**
 * getStudentAttendanceChartDetails Student Attendance Chart display
 * @param  integer $parent_id  Parent Id
 * @param  integer $client_id  Client Id
 * @param  integer $student_id Student Id
 * @return array             attendance details
 */
    public function getStudentAttendanceChartDetails($parent_id,$client_id,$student_id){

      if(!is_null($parent = ParentUser::find($parent_id))){

        $client = Client::find($client_id);

        $this->connectClient($client->clientConfiguration);

        $from_month_year = $this->request->get('from_month_year');
        $to_month_year = $this->request->get('to_month_year');

        $student = Student::find($student_id);

        $student_courses = $student->studentCurrentCourses;

        $from_month_year_details = explode('-',$from_month_year);


        $attendnceConfiguration = AttendanceConfiguration::where('institution_course_id',$student_courses->institution_course_id)->whereNull('deleted_at')->first();

        $attendanceType = AttendanceType::where('group_code',$attendnceConfiguration->attendance_group_code)->orderBy('id','ASC')->get();

        //->whereIn('attendance_type_id',$attendanceType->lists('id')->toArray())
        
        $arr =[];
        foreach($attendanceType as $attendance_type)
        {
          $arr[] = $attendance_type->id;
        }

           $course_id = $student_courses->institution_course_id;
           $course_peroid_id =$student_courses->course_peroid_id;
           $section_id = $student_courses->course_section_id ;

        $final_result = [];
        switch ($attendnceConfiguration->attendance_group_code) {
          case AttendanceType::GROUP_DAILY_ONCE:

          $absentDays = StudentAttendance::select(DB::raw('count(student_course_id) as Total_Absent_Days'))->where('student_course_id',$student_courses->id)->where('present','N')->whereRaw("MONTH( DATE ) =$from_month_year_details[0] AND YEAR( DATE ) = $from_month_year_details[1]")->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc')->first();

          $presentDays = StudentAttendance::select(DB::raw('count(student_course_id) as Total_Present_Days'))->where('student_course_id',$student_courses->id)->where('present','Y')->whereRaw("MONTH( DATE ) =$from_month_year_details[0] AND YEAR( DATE ) = $from_month_year_details[1]")->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc')->first();

            $absentWorkingDays = isset($absentDays)? $absentDays->Total_Absent_Days:0;
            $presentWorkingDays = isset($presentDays)? $presentDays->Total_Present_Days:0;

            $final_result[0]['absent'] =  $absentWorkingDays;
            $final_result[0]['present'] =  $presentWorkingDays;
            $final_result[0]['total'] =  $absentWorkingDays + $presentWorkingDays;
            $final_result[0]['AttendanceType'] =  $attendnceConfiguration->attendance_group_code;
            break;

          case AttendanceType::GROUP_DAILY_TWICE:
                 
            $workingDayDetails = CourseSubjectAttendanceDetail::select(DB::raw('count(attendance_type_id) as Working_Days'),DB::raw('MONTH(date) as Month'),'id as id',DB::raw("CONCAT(`attendance_type_id`, '-', MONTH(date)) as type_month"))->where('institution_course_id',$course_id)->where('course_peroid_id',$course_peroid_id)->where('course_section_id',$section_id)->whereRaw("MONTH( DATE ) =$from_month_year_details[0] AND YEAR( DATE ) = $from_month_year_details[1]")->whereIn('attendance_type_id',$arr)->groupBy('attendance_type_id')->groupBy(DB::raw("MONTH(date)"))->groupBy(DB::raw("YEAR(date)"))->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc')->get();
            $totalWorkingDayDetails = $workingDayDetails->sum('Working_Days');      

             foreach($attendanceType as $key=>$attendance_type){
               
               $absentDays = StudentAttendance::select(DB::raw('count(student_course_id) as Total_Absent_Days'))->where('student_course_id',$student_courses->id)->where('present','N')->whereRaw("MONTH( DATE ) =$from_month_year_details[0] AND YEAR( DATE ) = $from_month_year_details[1]")->where('attendance_type_id',$attendance_type->id)->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc')->first();

              $presentDays = StudentAttendance::select(DB::raw('count(student_course_id) as Total_Present_Days'))->where('student_course_id',$student_courses->id)->where('present','Y')->whereRaw("MONTH( DATE ) =$from_month_year_details[0] AND YEAR( DATE ) = $from_month_year_details[1]")->where('attendance_type_id',$attendance_type->id)->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc')->first();

              $absentWorkingDays = isset($absentDays)? $absentDays->Total_Absent_Days:0;
              $presentWorkingDays = isset($presentDays)? $presentDays->Total_Present_Days:0;
              
            $workingDayDetailsAM = CourseSubjectAttendanceDetail::getAttendanceWorkingDays($from_month_year_details[0],$from_month_year_details[1],AttendanceType::GROUP_DAILY_TWICE_AM,$course_id,$course_peroid_id,$section_id);
            $workingDayDetailsPM = CourseSubjectAttendanceDetail::getAttendanceWorkingDays($from_month_year_details[0],$from_month_year_details[1],AttendanceType::GROUP_DAILY_TWICE_PM,$course_id,$course_peroid_id,$section_id);

            $total_working_day_am =  $workingDayDetailsAM->sum('Working_Days');

            $total_working_day_pm = $workingDayDetailsPM->sum('Working_Days');

            if($total_working_day_am > $total_working_day_pm)
                $total_working_days = $total_working_day_am ;
              elseif ($total_working_day_pm > $total_working_day_am)
                 $total_working_days = $total_working_day_pm ;
              else            
             $total_working_days = $totalWorkingDayDetails / 2;
        
                $total_absent_days = isset($absentDays)?$absentDays->Total_Absent_Days / 2 :0;

              $final_result[$key]['absent'] =  $absentWorkingDays;
              $final_result[$key]['present'] =  $presentWorkingDays;
              $final_result[$key]['total'] =  $absentWorkingDays + $presentWorkingDays;
              $final_result[$key]['totalWorkingDays'] = $total_working_days;
              $final_result[$key]['AttendanceType'] =  $attendnceConfiguration->attendance_group_code;
            }


          case AttendanceType::GROUP_SUBJECT:

          $studentAttendanceDetails = StudentAttendance::getStudentSubjectAttendanceDetails($student_courses->id,$from_month_year_details[0],$from_month_year_details[1],1);

          foreach ($studentAttendanceDetails as $studentAttendanceDetailKey => $studentAttendanceDetail) {

            $absentDays = StudentAttendance::select(DB::raw('sec_to_time(sum(time_to_sec(no_of_hrs))) as Total_Absent_Days'))->where('student_course_id',$student_courses->id)->where('present','N')->whereRaw("MONTH( DATE ) =$from_month_year_details[0] AND YEAR( DATE ) = $from_month_year_details[1]")->where('attendance_type_id',1)->where('faculty_subject_allocation_id',$studentAttendanceDetail->faculty_subject_allocation_id)->orderBy(DB::raw('MONTH(date)'),'asc')->orderBy(DB::raw('YEAR(date)'),'asc')->first();

	          $workingHourDetails = CourseSubjectAttendanceDetail::getCourseSubjectAttendanceAppAPI('',$student_courses->id,1,$course_id,$course_peroid_id,$section_id,$from_month_year_details[0],$from_month_year_details[1]);

          	$working_days = $workingHourDetails->pluck('working_days','type_month');

            $absentWorkingDays = isset($absentDays)? $absentDays->Total_Absent_Days:0;
           $totalWorkingDays = isset($working_days[$studentAttendanceDetail->faculty_subject_allocation_id.'-'.$studentAttendanceDetail->month.'-'.$studentAttendanceDetail->year])?$working_days[$studentAttendanceDetail->faculty_subject_allocation_id.'-'.$studentAttendanceDetail->month.'-'.$studentAttendanceDetail->year]:0;

             $totalWorkingHourDetails = CourseSubjectAttendanceDetail::getCourseSubjectAttendanceTotalWorkingHour('','',$from_month_year_details[0],$from_month_year_details[1],1,$course_id,$course_peroid_id,$section_id);
         
           if($absentWorkingDays > 0)
  			    $absentWorkingDays = decimalHours($absentWorkingDays);
  		    else
  		  	  $absentWorkingDays = 0;
            
            $totalWorkingDays = decimalHours($totalWorkingDays);
            $presentHour = $totalWorkingDays - $absentWorkingDays;
           
            $subjectName = $studentAttendanceDetail->subject_name;
            $final_result[$studentAttendanceDetailKey]['subject_id'] =  $studentAttendanceDetail->faculty_subject_allocation_id;
            $final_result[$studentAttendanceDetailKey]['subject'] =  $subjectName;

            $final_result[$studentAttendanceDetailKey]['absent'] =  convertTime($absentWorkingDays);
            $final_result[$studentAttendanceDetailKey]['present'] = convertTime($presentHour);
            $final_result[$studentAttendanceDetailKey]['total'] =  convertTime($totalWorkingDays);
            $final_result[$studentAttendanceDetailKey]['AttendanceType'] =  $attendnceConfiguration->attendance_group_code;
          }

          default:
            # code...
            break;
        }

        //dd($final_result);


        return $this->response->array(['data'=>$final_result], 200, [], JSON_NUMERIC_CHECK);


      }

      return $this->response->errorForbidden('Parent Details not found');

    }

/**
 * getStudentAttendanceDateDetails Student attendance date details
 * @param  integer $parent_id  Parent Id
 * @param  integer $client_id  Client Id
 * @param  integer $student_id Student Id
 * @return array             student attendance details
 */
   public function getStudentAttendanceDateDetails($parent_id,$client_id,$student_id)
    {
      if(!is_null($parent = ParentUser::find($parent_id))){

        $client = Client::find($client_id);

        $this->connectClient($client->clientConfiguration);

        $from_month_year = $this->request->get('from_month_year');
        $to_month_year = $this->request->get('to_month_year');
        $subject_id = $this->request->get('subject_id');

        $student = Student::find($student_id);

        $student_courses = $student->studentCurrentCourses;

        $from_month_year_details = explode('-',$from_month_year);

        if(!empty($subject_id))
          $student_attendance = StudentAttendance::where('student_course_id',$student_courses->id)->where('present','N')->whereRaw("MONTH( DATE ) =$from_month_year_details[0] AND YEAR( DATE ) = $from_month_year_details[1]")->whereNull('deleted_at')->where('faculty_subject_allocation_id',$subject_id)->get();
        else
          $student_attendance = StudentAttendance::where('student_course_id',$student_courses->id)->where('present','N')->whereRaw("MONTH( DATE ) =$from_month_year_details[0] AND YEAR( DATE ) = $from_month_year_details[1]")->whereNull('deleted_at')->get();

        $final_result = [];
        foreach ($student_attendance as $student_attendance_key => $student_attendance_value) {

          $attandance_details = $student_attendance_value->attendanceType;

          $final_result['monthData'][$student_attendance_key]['message'] = $this->getStudentAbsentMessage($student_attendance_value,$attandance_details);
          $final_result['monthData'][$student_attendance_key]['absent'] = true;
          $final_result['monthData'][$student_attendance_key]['session'] = $attandance_details->group_code;
          $final_result['monthData'][$student_attendance_key]['absent_session'] = $attandance_details->code;
          $final_result['monthData'][$student_attendance_key]['date'] = $student_attendance_value->date->format('d/m/Y');
        }


        return $this->response->array(['data'=>$final_result]);


      }

      return $this->response->errorForbidden('Parent Details not found');
    }

/**
 * getCourseTimeTable Cource time table details
 * @param  integer $parent_id  Parent Id
 * @param  integer $client_id  Client Id
 * @param  integer $student_id Student Id
 * @return array             course time table details
 */
    public function getCourseTimeTable($parent_id,$client_id,$student_id)
    {
      if(!is_null($parent = ParentUser::find($parent_id))){

        $client = Client::find($client_id);

        $this->connectClient($client->clientConfiguration);

        $student = Student::find($student_id);

        $student_courses = $student->studentCurrentCourses;

        $list_of_week_days = ['MONDAY'=>'mondayTxt','TUESDAY'=>'tuesdayTxt','WEDNESDAY'=>'wednesdayTxt','THURSDAY'=>'thursdayTxt','FRIDAY'=>'fridayTxt','SATURDAY'=>'saturdayTxt','SUNDAY'=>'sundayTxt'];
        $hourList = [''=>'Hour'];
        $minutesList = [''=>'Minute'];
        $periodList = ['AM'=>'AM','PM'=>'PM'];


          for($i=1;$i<13;$i++){
              $hourList[$i] = $i;
          }
          for($i=0;$i<61;$i++){
              $minutesList[$i] = $i;
          }


    $course_time_table_info = CourseTimeTable::where('institution_course_id',$student_courses->institution_course_id)->where('course_period_id',$student_courses->course_peroid_id)->where('course_section_id',$student_courses->course_section_id)->get();



         foreach ($course_time_table_info as $key => $course_time_table) {
            $from_hour_minute_period = explode(':',$course_time_table->from_time);
            $end_hour_minute_period = explode(':',$course_time_table->end_time);

            array_add($course_time_table_info[$key],'from_hour',@$from_hour_minute_period[0]);
            array_add($course_time_table_info[$key],'from_minutes',@$from_hour_minute_period[1]);
            array_add($course_time_table_info[$key],'from_period',@$from_hour_minute_period[2]);
            array_add($course_time_table_info[$key],'to_hour',@$end_hour_minute_period[0]);
            array_add($course_time_table_info[$key],'to_minutes',@$end_hour_minute_period[1]);
            array_add($course_time_table_info[$key],'to_period',@$end_hour_minute_period[2]);

            array_add(@$course_time_table_info[$key],'week_text',@$list_of_week_days[$course_time_table->week_name]);
         }
         //return $course_time_table_info;
        $course_time_table_info_period = $course_time_table_info->groupBy('week_name');
        $course_time_table_info_period_old = $course_time_table_info->groupBy('period_no');

        /*Added API work start*/
          $api_list = null;
          $final_result = [];
          foreach ($course_time_table_info_period as $key => $day_wise_list) {
            $api_list[$key] = ['week_name'=>$key];
            $period_list = null;
              foreach ($day_wise_list as  $period) {
                if(!$period['course_subject_id'] == 0)
                  {
                    $period_list[] = ['subName'=>$period->subject->name,'from_time'=>$period['from_time'],'end_time'=>$period['end_time']];
                }else{
                    $period_list[] = ['subName'=>$period['other'],'from_time'=>$period['from_time'],'end_time'=>$period['end_time']];
                }
              }

              $api_list[$key]['item'] =  $period_list;

              // return $api_list;
          }
          if(!is_null($api_list)){
              $final_result = array_values($api_list);
            }else{
              
            }
       
        /*Added API work end*/


        return $this->response->array(['data'=>$final_result]);


      }

      return $this->response->errorForbidden('Parent Details not found');
    }

/**
 * getCourseAssignmentDetails course assignment details
 * @param  integer $parent_id  Parent id
 * @param  integer $client_id Client id
 * @param  integer $student_id Student id
 * @return array            assignment details
 */
    public function getCourseAssignmentDetails($parent_id,$client_id,$student_id)
    {
      if(!is_null($parent = ParentUser::find($parent_id))){

        $client = Client::find($client_id);

        $this->connectClient($client->clientConfiguration);

        $student = Student::find($student_id);

        $studentCourse = $student->studentCurrentCourses;

        $assignmentDetail = Assignment::join('institution_courses',function($institution_courses){

            $institution_courses->on('institution_courses.id','=','assignments.institution_course_id');

        })->select('assignments.id as id','assignments.created_at as created_at','assignments.title as title','assignments.description as description','assignments.start_date as start_date','assignments.expiry_date as expiry_date','assignments.created_by as created_by','assignments.user_id as user_id','subject_id as subject_id','assignments.faculty_subject_allocation_id as faculty_subject_allocation_id')->orderBy('assignments.created_at','desc')->where('institution_courses.institute_id',$student->institute_id)->whereNull('assignments.deleted_at')->get();

        
// return $assignmentDetail;

            $stuednt_eletive_subject = StudentElectiveSubject::where('student_course_id',$studentCourse->id)->get();

            
        
             $elective_sub =[];
            foreach($stuednt_eletive_subject as $elective_subject)
            {
              $elective_sub[] = $elective_subject->course_elective_subject_id;
            }

            $electiveSubject = CourseElectiveSubject::whereIn('id',$elective_sub)->get();

            

             $today_date = \Carbon\Carbon::now()->format('Y-m-d');
             
            $courseSubjects =  Assignment::where('expiry_date', '>=', $today_date)->whereNull('deleted_at')->get();

            //return $studentCourse;

                        

            $elective=[];
            foreach($electiveSubject as $electiveSub)
            {
              $elective[] = $electiveSub->subject_id;
            }

           
            //return $today_date;
        
            $electiveSubjects =  Assignment::where('institution_course_id',$studentCourse->institution_course_id)->where('expiry_date', '>=', $today_date)->where('course_peroid_id',$studentCourse->course_peroid_id)->whereIn('subject_id',$elective)->whereNull('deleted_at')->groupBy('faculty_subject_allocation_id')->get();



            $assignmnetSubjects = $courseSubjects->merge($electiveSubjects);

            // return $assignmnetSubjects;
 

          $final_result_new = array();
          $list_of_assignmennts = array();
          // return $assignmnetSubjects;
           foreach ($assignmnetSubjects as $key=>$assignmnetSubject) {
            //return $assignmnetSubject->id;
            // return @$assignmnetSubject->facultySubjectAllocation->subject->name;
            $final_result_new[$key]['subname'] = @$assignmnetSubject->facultySubjectAllocation->subject->name;
              if(!in_array($assignmnetSubject->id,$list_of_assignmennts)){
              
              foreach ($assignmentDetail as $key2=>$assignmentDetails) {
                  if($assignmentDetails->faculty_subject_allocation_id == $assignmnetSubject->faculty_subject_allocation_id){
                      // dd($assignmentDetails->description);
                    
                        $final_result_new[]['assignmentList'][] = [
                                  'start' =>$assignmentDetails->start_date->format('Ymd'),
                                  'assignedNo' => $assignmentDetails->id,
                                  'title' => $assignmentDetails->title,

                                  'description'=> $assignmentDetails->description,
                                  'assigneddate'=> $assignmentDetails->start_date->format('d-m-Y'),
                                  'expiryDate'=> $assignmentDetails->expiry_date->format('d-m-Y'),
                                  'assigneeName'=> (isset($assignmentDetails->user_id))? @$assignmentDetails->userInstitution->user->name : @$assignmentDetails->facultySubjectAllocation->userInstitution->user->name
                              ];
                              $list_of_assignmennts[]=$assignmentDetails->id;
                    }
                  }
                
              }
          }
          //return $final_result_new[0];
        return $this->response->array(['data'=>$final_result_new]);


      }

      return $this->response->errorForbidden('Parent Details not found');
    }

    /**
     * getNoticeBoardDetails description
     * @param  [type] $parent_id  [description]
     * @param  [type] $client_id  [description]
     * @param  [type] $student_id [description]
     * @return [type]             [description]
     */
    public function getNoticeBoardDetails($parent_id,$client_id,$student_id){
            if(!is_null($parent = ParentUser::find($parent_id))){

        $client = Client::find($client_id);

        $this->connectClient($client->clientConfiguration);

        $student = Student::find($student_id);


        $studentCourse = $student->studentCurrentCourses;


           
        $notice_list_infos = NoticeBoard::where('student_id',$student_id)->whereNull('deleted_at')->orderBy('id','desc')->get();
        // return $notice_list_infos;

        $final_result_new = array();
        foreach ($notice_list_infos as $key2=>$notice_list_info) {
                $final_result_new[]['noticeList'][] = [
                                 
                                  'noticedNo' => $notice_list_info->id,
                                  'title' => $notice_list_info->title,

                                  'description'=> $notice_list_info->description,
                                  'noticeddate'=> $notice_list_info->created_at->format('d-m-Y h:i A'),
                ];
        }
        // return $final_result_new;
        return $this->response->array(['data'=>$final_result_new]);
      }

      return $this->response->errorForbidden('Parent Details not found');
    }

/**
 * getInstitutionCalenderDetails Course institution calendar details
 * @param   integer $parent_id  Parent Id
 * @param   integer $client_id  Client Id
 * @param   integer $student_id Student Id
 * @return  array            institution calender details
 */
    public function getInstitutionCalenderDetails($parent_id,$client_id,$student_id)
    {
      if(!is_null($parent = ParentUser::find($parent_id))){

        $client = Client::find($client_id);

        $this->connectClient($client->clientConfiguration);

        $student = Student::find($student_id);

        $studentCourse = $student->studentCurrentCourses;
        
        $API_events_array = array();
        
       $from_month_year = $this->request->get('from_month_year');
       $from_month_year_details = explode('-',$from_month_year);
      
    $API_eloquentEvents = InstitutionCalendar::where('institution_id',$studentCourse->institutionCourse->institute_id)->orderBy('start_date_time','desc')->get();
   
    $API_events_array = null;
    foreach ($API_eloquentEvents as $key => $eloquentEvent) {
    if($eloquentEvent->type == 'EVENT'){
            $API_events_array[] = ['type'=>$eloquentEvent->type,'title'=>$eloquentEvent->event_name,'description'=>$eloquentEvent->description,'date'=>
            $eloquentEvent->start_date_time->format('d-m-Y'),
           'backgroundColor'=>'#f5974d'];
    }else if ($eloquentEvent->type == 'REMINDER') {
            $API_events_array[] = ['type'=>$eloquentEvent->type,'title' => $eloquentEvent->event_name,'description'=>$eloquentEvent->description, 'date' =>
                       isset($eloquentEvent->start_date_time) ? $eloquentEvent->start_date_time->format('d-m-Y') : null,
                       'backgroundColor' => '#3f5169'];
    }else{
           $API_events_array[] = ['type'=>$eloquentEvent->type,'title'=>$eloquentEvent->event_name,'description'=>$eloquentEvent->description,'date'=>
            $eloquentEvent->start_date_time->format('d-m-Y'),
           'backgroundColor'=>'#c74c4e'];
      }
   }
        return $this->response->array(['data'=>$API_events_array]);
      }

      return $this->response->errorForbidden('Parent Details not found');
    }

/**
 * importParentNumber import parent number artisan command will be called from the web app - ImportSingleStudentParents.php
 * @return type description
 */
    public function importParentNumber(){

        $client_unique_key = $this->request->get('client_unique_key');
        $student_number = $this->request->get('student_number');
        $phone_number_lists = $this->request->get('phone_number_lists');

        Artisan::call('client:import-single-parents', [
            'client_unique_key' => $client_unique_key, 'student_number' => $student_number, 'phone_number_lists' => $phone_number_lists
        ]);

    }
}
