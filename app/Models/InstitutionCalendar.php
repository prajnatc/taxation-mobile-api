<?php
/**
 * InstitutionCalendar
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use DB;

/**
 * InstitutionCalendar
 * @category Model
 * @author ThinkPace
 */
class InstitutionCalendar extends Model
{
	   /**
   * [$dates model dates]
   * @var [date]
   */
    protected $dates = ['start_date_time', 'end_date_time'];
	/**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'clientDB';
    
}
