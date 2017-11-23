<?php

namespace App\Console\Commands;


use App\Models\Client;
use App\Models\User;
use App\Models\ParentUser;
use App\Models\ParentStudent;
use DB;

class ImportSingleStudentParents extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:import-single-parents {client_unique_key : Client Unique Key(s) mulitple with seperated with comma OR ALL to get all clients} {student_number : Student number} {phone_number_lists : Phone Number Lists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Single Student Parents details to Pacifyca mobile app';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $client_unique = $this->argument('client_unique_key');

        $student_number = $this->argument('student_number');
       
        $phone_number_lists = $this->argument('phone_number_lists');

        $client_details = $this->client_details($client_unique);

        //return dd(json_decode($client_details));

        if($phone_number_lists){

            $phone_number_lists = json_decode($phone_number_lists);

            //return dd($phone_number_lists);
            //check for all numbers and if old number exist and if old and new numbers are different, delete old record.
            
            //delete from parent_students table

            foreach($phone_number_lists as $phone_number_list){
                if(!empty($phone_number_list->father_number[0]) || ($phone_number_list->father_number[0] != null)){
                    if($phone_number_list->father_number[0]!=$phone_number_list->father_number[1]){
                        
                        $user = User::where('mobile_number',$phone_number_list->father_number[0])->first();

                         //return dd($client_details->client_id);

                        if(count($user)>0){
                            if(!is_null($parent_students = ParentStudent::where('student_id',$student_number)->where('client_id',$client_details->client_id)->where('parent_id',$user->parentUser->id)->first())){
                                $parent_students->delete();
                              }
                        } 
                    }
                }
                if(!empty($phone_number_list->mother_number[0]) || ($phone_number_list->mother_number[0] != null)){
                    if($phone_number_list->mother_number[0]!=$phone_number_list->mother_number[1]){
                        
                        $user = User::where('mobile_number',$phone_number_list->mother_number[0])->first();
                         if(count($user)>0){
                            if(!is_null($parent_students = ParentStudent::where('student_id',$student_number)->where('client_id',$client_details->client_id)->where('parent_id',$user->parentUser->id)->first())){
                                $parent_students->delete();
                              }
                      }
                    }
                }
                if(!empty($phone_number_list->guardian_number[0]) || ($phone_number_list->guardian_number[0] != null)){
                    if($phone_number_list->guardian_number[0]!=$phone_number_list->guardian_number[1]){
            
                        $user = User::where('mobile_number',$phone_number_list->guardian_number[0])->first();
                        if(count($user)>0){
                            if(!is_null($parent_students = ParentStudent::where('student_id',$student_number)->where('client_id',$client_details->client_id)->where('parent_id',$user->parentUser->id)->first())){
                                $parent_students->delete();
                              }
                      }
                    }
                }
            }
        }

        //return dd($phone_number_lists);
       
        $this->info("Import started!!!!!!!!! ". date('d-m-Y H:i:s'));

 
        $this->connectClient($client_details);

        //return dd($client_details);

        $selectQuery = "select st.id as student_id, CONCAT(st.first_name, ' ', st.middle_name,' ',st.last_name) As FullName,spd.fathers_name as FathersName, spd.fathers_mobile_no as FathersMobileNo, spd.mothers_name as MotherName, spd.mothers_mobile_no as MothersMobileNo, spd.guardian_name as GuardianName, spd.guardian_mobile_no as GuardianMobileNo, st.mobile_app_enabled as MobileAppEnabled, st.mobile as MobileNo from students st INNER JOIN student_parent_details spd ON st.id = spd.student_id where st.deleted_at IS NULL and mobile_app_enabled='Y' and st.id=$student_number";
           $ImportParents = DB::connection('clientDB')->select(DB::raw($selectQuery));

          //return dd($ImportParents);

           foreach ($ImportParents as $ImportParent) {

             //fathers name
             if(!empty($ImportParent->FathersMobileNo) && strlen($ImportParent->FathersMobileNo)==10){

                if(!is_null($fathers_details = User::where('mobile_number',trim($ImportParent->FathersMobileNo))->first())){

                    if(is_null($parent_student_details = ParentStudent::where('client_id',$client_details->client_id)->where('parent_id',$fathers_details->parentUser->id)->where('student_id',$ImportParent->student_id)->first())){

                      $parent_students_details = new ParentStudent;
                      $parent_students_details->parent_id = $fathers_details->parentUser->id;
                      $parent_students_details->student_id = $ImportParent->student_id;
                      $parent_students_details->client_id = $client_details->client_id;
                      $parent_students_details->save();
                    }

                } else {
                    $fathers_details = new User;
                    $fathers_details->name = (!empty($ImportParent->FathersName) ? $ImportParent->FathersName : trim($ImportParent->FathersMobileNo));
                    $fathers_details->mobile_number = trim($ImportParent->FathersMobileNo);
                    $fathers_details->save();

                    $parent_details = new ParentUser;
                    $parent_details->user_id = $fathers_details->id;
                    $parent_details->save();

                    $parent_students_details = new ParentStudent;
                    $parent_students_details->parent_id = $parent_details->id;
                    $parent_students_details->student_id = $ImportParent->student_id;
                    $parent_students_details->client_id = $client_details->client_id;
                    $parent_students_details->save();
                }

             }

             //mothers name
             if(!empty($ImportParent->MothersMobileNo) && strlen($ImportParent->MothersMobileNo)==10){

                if(!is_null($mother_details = User::where('mobile_number',trim($ImportParent->MothersMobileNo))->first())){

                    if(is_null($parent_student_details = ParentStudent::where('client_id',$client_details->client_id)->where('parent_id',$mother_details->parentUser->id)->where('student_id',$ImportParent->student_id)->first())){

                      $parent_students_details = new ParentStudent;
                      $parent_students_details->parent_id = $mother_details->parentUser->id;
                      $parent_students_details->student_id = $ImportParent->student_id;
                      $parent_students_details->client_id = $client_details->client_id;
                      $parent_students_details->save();
                    }

                } else {
                    $mother_details = new User;
                    $mother_details->name = (!empty($ImportParent->MotherName) ? $ImportParent->MotherName : trim($ImportParent->MothersMobileNo));
                    $mother_details->mobile_number = trim($ImportParent->MothersMobileNo);
                    $mother_details->save();

                    $parent_details = new ParentUser;
                    $parent_details->user_id = $mother_details->id;
                    $parent_details->save();

                    $parent_students_details = new ParentStudent;
                    $parent_students_details->parent_id = $parent_details->id;
                    $parent_students_details->student_id = $ImportParent->student_id;
                    $parent_students_details->client_id = $client_details->client_id;
                    $parent_students_details->save();
                }

             }

             //Primary Contact Number

             if(!empty($ImportParent->MobileNo) && strlen($ImportParent->MobileNo)==10){

                if(!is_null($primary_contact_details = User::where('mobile_number',trim($ImportParent->MobileNo))->first())){

                    if(is_null($parent_student_details = ParentStudent::where('client_id',$client_details->client_id)->where('parent_id',$primary_contact_details->parentUser->id)->where('student_id',$ImportParent->student_id)->first())){

                      $parent_students_details = new ParentStudent;
                      $parent_students_details->parent_id = $primary_contact_details->parentUser->id;
                      $parent_students_details->student_id = $ImportParent->student_id;
                      $parent_students_details->client_id = $client_details->client_id;
                      $parent_students_details->save();
                    }

                } else {
                    $primary_contact_details = new User;
                    $primary_contact_details->name = trim($ImportParent->MobileNo);
                    $primary_contact_details->mobile_number = trim($ImportParent->MobileNo);
                    $primary_contact_details->save();

                    $parent_details = new ParentUser;
                    $parent_details->user_id = $primary_contact_details->id;
                    $parent_details->save();

                    $parent_students_details = new ParentStudent;
                    $parent_students_details->parent_id = $parent_details->id;
                    $parent_students_details->student_id = $ImportParent->student_id;
                    $parent_students_details->client_id = $client_details->client_id;
                    $parent_students_details->save();
                }

             }

             # code...
           }
         $this->info("Import Ended!!!!!!!!! " . date('d-m-Y H:i:s'));

    }
}
