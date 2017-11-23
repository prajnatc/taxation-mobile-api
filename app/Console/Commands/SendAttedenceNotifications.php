<?php

namespace App\Console\Commands;


use App\Models\Client;
use DB;

class SendAttedenceNotifications extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:send-attendence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to parents on absent students';



    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         $clients = Client::all();

         foreach ($clients as $client) {

           $clientConfiguration = null;

           $clientConfiguration = $client->clientConfiguration;

          $db_connection = $this->connectClient($clientConfiguration);

          if($clientConfiguration->enable_notifications=='Y'){
              $selectQuery = "select i.name as Institute, sa.date as AbsentDate,att.name as AttendanceType,att.group_code as GroupCode ,CONCAT(st.first_name, ' ', st.middle_name,' ',st.last_name) As FullName, CONCAT(ic.custom_course_name,' ',ap.academic_period,' ',s.name) as CourseName, sb.name as Subject, CONCAT(start_date,' - ',end_date) as AcademicYear,st.fathers_name as FathersName, st.fathers_mobile_no as FathersMobileNo, st.mothers_name as MotherName, st.mothers_mobile_no as MothersMobileNo, st.guardian_name as GuardianName, st.guardian_mobile_no as GuardianMobileNo, st.mobile_app_enabled as MobileAppEnabled from institutions i, academics ac,student_attendances sa,student_courses sc,students st,institution_courses ic,sections s,course_peroid_details cpd,academic_periods ap,course_sections cs,subjects sb,attendance_types att, course_subjects csbj,faculty_subject_allocations fsa WHERE sa.date = CURRENT_DATE AND sa.student_course_id=sc.id AND st.id = sc.student_id AND sc.institution_course_id = ic.id AND sc.course_section_id = cs.id AND cs.section_id = s.id AND sc.course_peroid_id = cpd.id AND cpd.academic_period_id = ap.id AND sc.academic_year_id = ac.id AND ic.institute_id = i.id AND sa.attendance_type_id = att.id AND sa.faculty_subject_allocation_id = fsa.id AND fsa.course_subject_id = csbj.id AND csbj.subject_id = sb.id";
               $student_attendances = DB::connection('clientDB')->select(DB::raw($selectQuery));

               $this->info(json_encode($student_attendances));
            } else  {
              $this->comment($client->client_name." Does't have notifications enabled");
            }
         }

    }
}
