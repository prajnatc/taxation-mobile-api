<?php
/**
 * ParentController
 * @category Controller
 * @author ThinkPace
 */
namespace App\Http\Controllers\Api\V1;

use App\Models\ParentUser;
use App\Models\Client;
use App\Models\Academic;
use DB;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
/**
 * ParentController
 * @category Controller
 * @author ThinkPace
 */
class ParentController extends BaseController
{
/**
 * students get all the student details of particular parent
 * @param  Request $request   client key
 * @param  integer  $parent_id Parent Id
 * @return array             Student Details
 */
    public function students(Request $request, $parent_id)
    {

        if(!is_null($parent = ParentUser::find($parent_id))){
          $parent_students = $parent->parentStudent;
          $client_unique_key = $request->header('client_unique_key', null);
          // return $parent_students;
          $resultStudentDetails = array();

          foreach ($parent_students as $parent_student) {  
            //return @$parent_student->client;
            $clientConfiguration = $parent_student->client->clientConfiguration;
            // return $client_unique_key;
            if(!empty($client_unique_key)){          
              if($client_unique_key==$clientConfiguration->client_unique_key){ 
                // return $client_unique_key;         
                if(!is_null($student_details =  $this->studentDetails($parent_student,$clientConfiguration))){                
                    $resultStudentDetails['data'][] = $student_details;     
                    // return $resultStudentDetails;         
                  }
                }else{            
                  return "invalid client key";          
                }
                // dd($resultStudentDetails);
            } else {

              if(!is_null($student_details =  $this->studentDetails($parent_student,$clientConfiguration))){

                  $resultStudentDetails['data'][] = $student_details;

                } 

                 // return $resultStudentDetails;   
              }
            }
            // return $resultStudentDetails;
          return $this->response->array($resultStudentDetails);
    }
  else
        return $this->response->errorForbidden('Parent Details not found');
    }

/**
 * parent parent details
 * @return array
 */
     public function parent(){
      return $this->response->array($this->user()->parentUser);
    }

/**
 * studentDetails get Student Information
 * @param  array $parent_student      Parent Info and Studnt Id
 * @param  array $clientConfiguration client details
 * @return array                      Student details
 */
    private function studentDetails($parent_student,$clientConfiguration){

      

      $this->connectClient($clientConfiguration);


       $selectQuery = "select st.id as student_id, i.name as Institute, CONCAT(COALESCE(st.first_name,''),' ',COALESCE(st.middle_name,''),' ',COALESCE(st.last_name,'')) As FullName, CONCAT(ic.custom_course_name,' ',ap.academic_period,' ',s.name) as CourseName,  CONCAT(ac.start_date,' - ',ac.end_date) as AcademicYear,spd.fathers_name as FathersName, spd.fathers_mobile_no as FathersMobileNo, spd.mothers_name as MotherName, spd.mothers_mobile_no as MothersMobileNo, spd.guardian_name as GuardianName, spd.guardian_mobile_no as GuardianMobileNo, st.mobile_app_enabled as MobileAppEnabled,st.profile_photo as ProfilePhoto from institutions i, academics ac, student_courses sc,students st,institution_courses ic,sections s,course_peroid_details cpd,academic_periods ap,course_sections cs,student_parent_details spd WHERE st.id = sc.student_id AND sc.institution_course_id = ic.id AND sc.course_section_id = cs.id AND cs.section_id = s.id AND sc.course_peroid_id = cpd.id AND cpd.academic_period_id = ap.id AND sc.academic_year_id = ac.id AND ic.institute_id = i.id and sc.current_course ='Y' AND ac.current_year='Y' and st.id = $parent_student->student_id  AND st.id=spd.student_id";
 
       $student_details = DB::connection('clientDB')->select(DB::raw($selectQuery));

       if($student_details){
         $student_details = array_first($student_details);
         $student_details->client_id = $parent_student->client->id;
         $student_details->client_name = $parent_student->client->client_name;
         $student_details->ProfilePhoto = $parent_student->client->clientConfiguration->client_site_url.'/photos/'.$student_details->ProfilePhoto;
// dd($student_details);
         return $student_details;
       }

    }

/**
 * updateFcmToken Update FCM Mobile Token
 * @param  Request $request User Information
 * @return null
 */
    public function updateFcmToken(Request $request){
      if(!is_null($user = User::where('mobile_number', $request->mobilenumber)->first())){
        $user->fcmtoken = $request->fcmtoken;
        $user->save();
      }
    }

/**
 * sendNotification artisan command will called when messages sent to parent - SendNotifications.php
 * @return null
 */
    public function sendNotification(Request $request){
      // \Log::info($this->request->all());
        $client_unique_key = $request->client_unique_key;
        $parent_number = $request->parent_number;
        $student_id = $request->student_id;
        $notification_title = $request->notification_title;
        $notification_body = $request->notification_body;
        $display_date = $request->display_date;

        $assignee_name = $request->assignee_name;
        $assignment_date = $request->assignment_date;
        $subject_name = $request->subject_name;
        $notification_click_action = $request->click_action;

        Artisan::call('client:send-notification', [
            'client_unique_key' => $client_unique_key, 'parent_number' => $parent_number, 'student_id' => $student_id, 'notification_title' => $notification_title, 'notification_body' => $notification_body, 'display_date'=>$display_date, 'assignee_name'=>$assignee_name, 'assignment_date'=>$assignment_date, 'subject_name'=>$subject_name, 'notification_click_action'=> $notification_click_action
        ]);

        // Artisan::call('client:send-notification', [
        //     'client_unique_key' => $client_unique_key, 'parent_number' => $parent_number, 'student_id' => $student_id, 'notification_title' => $notification_title, 'notification_body' => $notification_body, 'display_date'=>$display_date, 'assignee_name'=>$assignee_name, 'assignment_date'=>$assignment_date, 'subject_name'=>$subject_name, 'notification_click_action'=> $notification_click_action
        // ]);
    }
}
