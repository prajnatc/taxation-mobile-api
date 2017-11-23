<?php
/**
 * AdminController
 * @category Controller
 * @author ThinkPace
 */
namespace App\Http\Controllers\Api\V1;

use App\Models\Client;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

/**
 * AdminController
 * @category Controller
 * @author ThinkPace
 */
class AdminController extends BaseController
{
/**
 * $loginAvailableTypes get login available types
 * @var array
 */
    protected $loginAvailableTypes = ['PARENT'=>'parent'];

/**
 * clients to retrieve client information
 * @return array all client info
 */
    public function clients()
    {
        return $this->response->array(Client::all());
    }

/**
 * loginAvailableTypes - Retrieves Login User Types
 * @return array User Types
 */
    public function loginAvailableTypes()
    {
        $user_types = [];
        foreach ($this->loginAvailableTypes as $loginAvailableTypeKey => $loginAvailableType) {
          $user_types['userType'][]['type'] = $loginAvailableTypeKey;
        }
        return $this->response->array($user_types);
    }

}
