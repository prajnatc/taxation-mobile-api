<?php
/**
 * BaseController
 * @category Controller
 * @author ThinkPace
 */
namespace App\Http\Controllers\Api\V1;

use Dingo\Api\Routing\Helpers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Database\OnTheFly;
/**
 * BaseController
 * @category Controller
 * @author ThinkPace
 */
class BaseController extends Controller
{

    use Helpers;
    protected $request;
    protected $perPage;

    protected function errorBadRequest($message = '')
    {
        return $this->response->array($message)->setStatusCode(400);
    }

    protected function error($message = '',$error=204)
    {
        return $this->response->array($message)->setStatusCode($error);
    }

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->psrPage = $request->get('per_page') ?: null;
    }

/**
 * connectClient connecting to client DB
 * @param  type $clientConfiguration client details
 * @return type                      description
 */
    protected function connectClient($clientConfiguration){
        // return 'hi';
                return new OnTheFly($clientConfiguration);
    }

/**
 * generateOtp - to genearet otp
 * @return string Generated otp
 */
    protected function generateOtp(){
      return mt_rand(111111, 999999);
    }

/**
 * generateToken - to generate token
 * @return string Generated token
 */
    protected function generateToken(){
      return str_random(32);
    }

/**
 * sendSMS - to send otp SMS to parent
 * @param  number $mobile_number Parent Mobile Number
 * @param  number $otp           OTP
 * @return string                OTP
 */
    public function sendSMS($mobile_number,$otp)
    {
        $curl = curl_init();
        $key = env('2FACTOR_API_KEY');
        curl_setopt_array($curl, array(
        CURLOPT_URL => "http://2factor.in/API/V1/$key/SMS/$mobile_number/$otp",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "{}",
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        return $err;
     //diaplay final message response
    }
}
