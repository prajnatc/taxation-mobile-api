<?php

namespace App\Console\Commands;


use App\Models\Client;
use App\Models\User;
use App\Models\ParentUser;
use App\Models\ParentStudent;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use DB;

class SendNotifications extends BaseCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'client:send-notification {client_unique_key : Single Client Unique Key} {parent_number : Parent Number(s) mulitple with seperated with comma OR ALL to get all clients} {student_id : Student id} {notification_title : Notification title} {notification_body : Notification Body} {display_date : Display date} {assignee_name : Assignee Name} {assignment_date : Assignment Date} {subject_name : Subject Name} {notification_click_action : Notification Click Action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Notification to Pacifyca mobile app';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $parent_number = $this->argument('parent_number');

        $client_unique_key = $this->argument('client_unique_key');
        $student_id = $this->argument('student_id');
        $notification_title = $this->argument('notification_title');
        $notification_body = $this->argument('notification_body');
        $display_date = $this->argument('display_date');

        $assignee_name = $this->argument('assignee_name');
        $assignment_date = $this->argument('assignment_date');
        $subject_name = $this->argument('subject_name');
        $notification_click_action = $this->argument('notification_click_action');

        $user = User::where('mobile_number',$parent_number)->first();

        // return dd($user);

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($notification_title);
        $notificationBuilder->setBody(strip_tags($notification_body));
        // $notificationBuilder->setData($notification_data);
        $notificationBuilder->setClickAction($notification_click_action);

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['notification_title' => $notification_title, 'notification_body' => $notification_body, 'display_date' => $display_date, 'assignee_name' => $assignee_name, 'assignment_date' => $assignment_date, 'subject_name' => $subject_name]);

        //return dd($notificationBuilder);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();
        if(!is_null($user->android_token)){
            $token = $user->android_token;
            //return dd($user->android_token);
            $downstreamResponse = FCM::sendTo($token, null, $notification, $data);
        }
        return 'success';
    }
}