<?php
/**
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @category Model
 * @author ThinkPace
 */
class AttendanceType extends Model
{
  /**
   * $dates required dated for the model
   * @var date
   */
    protected $dates = ['created_at','updated_at','deleted_at'];

      const GROUPSUBJECT = 'GROUP_SUBJECT';

      const DAILY_ONCE_MESSAGE = 'whole day';
      const DAILY_TWICE_AM_MESSAGE = 'during morning session';
      const DAILY_TWICE_PM_MESSAGE = 'during afternoon session';

      const GROUP_SUBJECT = 'GROUP_SUBJECT';
      const GROUP_DAILY_ONCE = 'GROUP_DAILY_ONCE';
      const GROUP_DAILY_TWICE = 'GROUP_DAILY_TWICE';
      const GROUP_DAILY_TWICE_AM ='3';
      const GROUP_DAILY_TWICE_PM ='4';

	/**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'clientDB';


}
