<?php
/**
 * ParentUser
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ParentStudent;
use App\Models\User;
/**
 * ParentUser
 * @category Model
 * @author ThinkPace
 */

class ParentUser extends Model
{
	/**
 * $fillable mandatory fields
 * @var array
 */
    protected $fillable = ['user_id'];

      /**
   * $dates model dates
   * @var [date]
   */
    protected $table = 'parents';

    /**
     * $timestamps time stamps
     * @var boolean false
     */
    public $timestamps = false;

    /**
     * user relationship to the User model
     * @return array user information
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

/**
 * parentStudent relationship to ParentStudent model 
 * @return array parent student information
 */
    public function parentStudent(){
    	return $this->hasMany(ParentStudent::class,'parent_id','id');
    }
}
