<?php
/**
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hashids;
use DB;

/**
 * Attendance Configuration
 * @category Model
 * @author ThinkPace
 */
class AttendanceConfiguration extends Model
{

    use SoftDeletes;

    /**
     * $dates dates of Model
     * @var date
     */
    protected $dates = ['created_at','updated_at','deleted_at'];

    /**
     * $fillable mandatory fields of Model
     * @var array
     */
    protected $fillable = ['institution_course_id','attendance_group_code','created_by'];

    /**
     * $hashids encrypted id
     * @var integer
     */
	protected $hashids;

	/**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'clientDB';

    	public function delete() {
		// Delete the post
		return parent::delete();
	}

	/**
	 * getId idof the Model
	 * @return hashid 
	 */
	public function getId(){
		return Hashids::encode($this->id);
	}

	/**
	 * [scopeFindId description]
	 * @param  array $query data
	 * @param  integer $id    id to be decoded
	 * @return integer        decoded id
	 */
	public function scopeFindId($query,$id){
		return $query->find(Hashids::decode($id))->first();
	}


	/**
	 * Get the author.
	 *
	 * @return User
	 */
	public function user() {
		return $this->belongsTo('App\User', 'created_by');
	}

	/**
	 * institutionCourse data
	 * @return array Institution course data
	 */
	public function institutionCourse(){
		return $this->belongsTo('App\InstitutionCourse');
	}

	/**
	 * attendanceType attendance type relationship
	 * @return relationship attendance type
	 */
	public function attendanceType(){
		return $this->belongsTo('App\AttendanceType','attendance_group_code','group_code');
	}

}
