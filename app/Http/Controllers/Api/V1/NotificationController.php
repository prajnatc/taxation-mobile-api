<?php
/**
 * NotificationController
 * @category Controller
 * @author ThinkPace
 */
namespace App\Http\Controllers\Api\V1;

use App\Models\ParentUser;
use App\Models\Client;
use App\Models\MessageLog;
use Illuminate\Http\Request;
/**
 * NotificationController
 * @category Controller
 * @author ThinkPace
 */
class NotificationController extends BaseController
{

/**
 * parentMessages - retrieves parent mesages from the web app
 * @param  Request $request   request sent from the App, client Id, API Key, Authorisation
 * @param  integer  $parent_id Parent Id
 * @return array  Message deatils
 */

    public function parentMessages(Request $request, $parent_id)
    {
        $parent = ParentUser::find($parent_id);

        $client_id = $request->header('client_id', null);
        if(!empty($client_id)){
             $parent_students = $parent->parentStudent->where('client_id',$client_id);
        }else{
             $parent_students = $parent->parentStudent;
        }
        // return $parent_students;

        $messages['data'] = array();
        $totalMessagses = array();
        $totalCount = 0;
        foreach ($parent_students as $parent_student) {

            $clientConfiguration = $parent_student->client->clientConfiguration;

            if(!empty($client_unique_key)){
                 if($client_unique_key==$clientConfiguration->client_unique_key){
                    $this->connectClient($parent_student->client->clientConfiguration);
                 }
            } else {
                $this->connectClient($parent_student->client->clientConfiguration);
            }

        	$messageData =  MessageLog::where('student_id',$parent_student->student_id)->where('send_mobile_number',$parent->user->mobile_number)->orderBy('created_at','desc')->get(['id','student_name','institution_name','course_name','academic_year','section_name','message_content','created_at as sent_at'])->toArray();

            // return dd($messageData);

            // return $messageData;

          if($messageData){

            foreach ($messageData as $key => $value) {

                $totalMessagses[$totalCount]['id'] = $value['id'];
                $totalMessagses[$totalCount]['student_name'] = $value['student_name'];
                $totalMessagses[$totalCount]['institution_name'] = $value['institution_name'];
                $totalMessagses[$totalCount]['course_name'] = $value['course_name'];
                $totalMessagses[$totalCount]['academic_year'] = $value['academic_year'];
                $totalMessagses[$totalCount]['section_name'] = $value['section_name'];
                $totalMessagses[$totalCount]['message_content'] = $value['message_content'];
                $totalMessagses[$totalCount]['sent_at'] = $value['sent_at'];
                $totalMessagses[$totalCount]['client_id'] = $parent_student->client->id;
                $totalCount++;
            }
          }
        }

        if(empty($totalMessagses)){
            $messages['data'] = array();
        } else {
          $messages['data'] = $totalMessagses;
        }


        return $this->response->array($messages);
    }

/**
 * archiveStudentMessageDetails delete a message
 * @param  integer $parent_id  Parent Id
 * @param  integer $client_id  Cient Id
 * @param  integer $message_id Message Id
 * @return array   message
 */
    public function archiveStudentMessageDetails($parent_id,$client_id,$message_id)
    {
        if(!is_null($parent = ParentUser::find($parent_id))){

          $client = Client::find($client_id);

          $this->connectClient($client->clientConfiguration);

          $message = MessageLog::find($message_id);
          $message->delete();

          return $this->response->array(array('message'=>'Message deleted'));

        }

        return $this->response->errorForbidden('Parent Details not found');

    }
}
