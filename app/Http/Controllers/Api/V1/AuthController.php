<?php
/**
 * AuthController
 * @category Controller
 * @author ThinkPace
 */
namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\ParentStudent;
use Carbon\Carbon;
/**
 * AuthController
 * @category Controller
 * @author ThinkPace
 */
class AuthController extends BaseController
{

/**
 * signinOtp - This is to return otp number to enetered mobile number
 * @return array token, mobile number and otp will be sent 
 */
    public function signinOtp(){

      $validator = \Validator::make($this->request->all(), [
          'mobile_number' => 'required|numeric',
          'user_type' => 'required',
      ]);


      $credentials = $this->request->only('mobile_number', 'user_type');

      $mobile_number = $this->request->get('mobile_number');


      if(!is_null($user = User::where('mobile_number', $mobile_number)->first())){

          if ($validator->fails()) {
              return $this->errorBadRequest($validator->messages());
          }

          //Check if the user as students
          if($user->parentUser->id){
            
            if(is_null($parent_student_details = ParentStudent::where('parent_id',$user->parentUser->id)->first())){
                return $this->errorBadRequest(array('mobile_number'=>array(trans('auth.empty_student'))));
            }
          }


          $token = $this->generateToken();
          $otp = $this->generateOtp();

          //$message_sent = $this->sendSMS($mobile_number,$otp);

          //if(!$message_sent){
            $user->token = $token;
            $user->password = bcrypt($otp);
            $user->save();
          //}

          return $this->response->array(compact('token','mobile_number','otp'));
      }
      return $this->errorBadRequest(array('mobile_number'=>array(trans('auth.failed'))));
    }
    
/**
 * [signin - app signin based on the token generated]
 * @return [array] [token, token type]
 */
    public function signin()
    {
        $validator = \Validator::make($this->request->all(), [
            'mobile_number' => 'required|numeric',
            'password' => 'required',
            'token' => 'required',
        ]);

        $credentials = $this->request->only('mobile_number', 'password');

        // return $credentials;

        // 手动验证一下用户
        if(!is_null($user = User::where('mobile_number', $this->request->get('mobile_number'))->where('token', $this->request->get('token'))->first())){

            if (!$token = \JWTAuth::attempt($credentials)) {
                $validator->after(function ($validator) {
                    $validator->errors()->add('password', trans('auth.failed'));
                });
            }

            // return $token;

            if ($validator->fails()) {
                return $this->errorBadRequest($validator->messages());
            }

            $current_date = Carbon::now();

            $name = $user->name;
            $activated_on = $user->activated_on;
            $last_updated = $user->updated_at->addMinutes(env('OTP_TTL', 5));

            if(!$last_updated->gte($current_date)){
                return $this->errorBadRequest(array('message'=>array(trans('auth.otp_failed'))));
            }

            $token_type = 'Bearer';

            // return $this->request->get('android_token');

            if(!empty($this->request->get('android_token'))){
                // return $this->request->get('android_token');
                $user = User::where('mobile_number', $this->request->get('mobile_number'))->first();
                $user->android_token = $this->request->get('android_token');
                $user->save();
            }
            return $this->response->array(compact('token','token_type','name','activated_on','last_updated','current_date'));
        }
        return $this->errorBadRequest(array('password'=>array(trans('auth.failed'))));
    }

/**
 * [refreshToken description]
 * @return [array] [token]
 */
    public function refreshToken()
    {
        $token = \JWTAuth::parseToken()->refresh();
        return $this->response->array(compact('token'));
    }

/**
 * [logout - APP Logout]
 * @return [array] [token]
 */
    public function logout(){
      $token = \JWTAuth::getToken();
      return $this->response->array(['test'=>$token]);
      $token = \JWTAuth::invalidate($token);
      return $this->response->array(compact('token'));
    }
}
