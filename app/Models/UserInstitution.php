<?php
/**
 * UserInstitution
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\AppUser as AppUser;
use DB;

/**
 * UserInstitution
 * @category Model
 * @author ThinkPace
 */
class UserInstitution extends Model
{
             /**
   * $dates model dates
   * @var [date]
   */
    protected $dates = ['doj','created_at','deleted_at'];
	/**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'clientDB';
    

/**
 * user user of user institution
 * @return array
 */
   public function user(){
        return $this->hasOne(AppUser::class,'id','user_id');
    }
 }
