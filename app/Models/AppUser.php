<?php
/**
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\StudentCourse as StudentCourse;

/**
 * @category Model
 * @author ThinkPace
 */
class AppUser extends Model
{

    /**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'clientDB';

    /**
     * 
   * [$dates deleted_at dates]
   * @var [date]
   */
    protected $dates = ['deleted_at'];


    /**
   * The database table used by the model.
   *
   * @var string
   */
   protected $table = 'users';

   /**
    * statusChecked User Status Checked
    * @return boolean Whether status checked on not, True/Fale
    */
   public function statusChecked(){
     return ($this->confirmed=='1') ? true:false;
  }

  /**
   * getStatus getStatus of User
   * @return String Active or Inactive
   */
  public function getStatus(){
     return ($this->confirmed=='1') ? 'Active': 'In-active';
  }


  }
