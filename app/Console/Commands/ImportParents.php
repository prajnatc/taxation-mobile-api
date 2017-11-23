<?php

namespace App\Console\Commands;


use App\Models\Client;
use App\Models\User;
use App\Models\ParentUser;
use App\Models\ParentStudent;
use DB;

class ImportParents extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:import-parents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Parents details to Pacifyca mobile app';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         $clients = Client::all();

          $this->info("Import started!!!!!!!!! ". date('d-m-Y H:i:s'));

         foreach ($clients as $client) {

              $this->connectClient($client->clientConfiguration);

              $selectQuery = "select st.id as student_id, CONCAT(st.first_name, ' ', st.middle_name,' ',st.last_name) As FullName,spd.fathers_name as FathersName, spd.fathers_mobile_no as FathersMobileNo, spd.mothers_name as MotherName, spd.mothers_mobile_no as MothersMobileNo, spd.guardian_name as GuardianName, spd.guardian_mobile_no as GuardianMobileNo, st.mobile_app_enabled as MobileAppEnabled, st.mobile as MobileNo from students st INNER JOIN student_parent_details spd ON st.id = spd.student_id where st.deleted_at IS NULL and mobile_app_enabled='Y'";
               $ImportParents = DB::connection('clientDB')->select(DB::raw($selectQuery));

               foreach ($ImportParents as $ImportParent) {

                 //fathers name
                 if(!empty($ImportParent->FathersMobileNo) && strlen($ImportParent->FathersMobileNo)==10){

                    if(!is_null($fathers_details = User::where('mobile_number',trim($ImportParent->FathersMobileNo))->first())){

                        if(is_null($parent_student_details = ParentStudent::where('client_id',$client->id)->where('parent_id',$fathers_details->parentUser->id)->where('student_id',$ImportParent->student_id)->first())){
                          

                          $parent_students_details = new ParentStudent;
                          $parent_students_details->parent_id = $fathers_details->parentUser->id;
                          $parent_students_details->student_id = $ImportParent->student_id;
                          $parent_students_details->client_id = $client->id;
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
                        $parent_students_details->client_id = $client->id;
                        $parent_students_details->save();
                    }

                 }

                 //mothers name
                 if(!empty($ImportParent->MothersMobileNo) && strlen($ImportParent->MothersMobileNo)==10){

                    if(!is_null($mother_details = User::where('mobile_number',trim($ImportParent->MothersMobileNo))->first())){

                        if(is_null($parent_student_details = ParentStudent::where('client_id',$client->id)->where('parent_id',$mother_details->parentUser->id)->where('student_id',$ImportParent->student_id)->first())){

                          $parent_students_details = new ParentStudent;
                          $parent_students_details->parent_id = $mother_details->parentUser->id;
                          $parent_students_details->student_id = $ImportParent->student_id;
                          $parent_students_details->client_id = $client->id;
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
                        $parent_students_details->client_id = $client->id;
                        $parent_students_details->save();
                    }

                 }

                 //Primary Contact Number

                 if(!empty($ImportParent->MobileNo) && strlen($ImportParent->MobileNo)==10){

                    if(!is_null($primary_contact_details = User::where('mobile_number',trim($ImportParent->MobileNo))->first())){

                        if(is_null($parent_student_details = ParentStudent::where('client_id',$client->id)->where('parent_id',$primary_contact_details->parentUser->id)->where('student_id',$ImportParent->student_id)->first())){

                          $parent_students_details = new ParentStudent;
                          $parent_students_details->parent_id = $primary_contact_details->parentUser->id;
                          $parent_students_details->student_id = $ImportParent->student_id;
                          $parent_students_details->client_id = $client->id;
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
                        $parent_students_details->client_id = $client->id;
                        $parent_students_details->save();
                    }

                 }

                 # code...
               }

         }

         $this->info("Import Ended!!!!!!!!! " . date('d-m-Y H:i:s'));

    }
}
