<?php
/**
 * Academic
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Academic Model
 * @category Model
 * @author ThinkPace
 */
class Academic extends Model
{
	/**
	 * [$dates Created_at, updated_at and deleted_at dates]
	 * @var [date]
	 */
    protected $dates = ['created_at','updated_at','deleted_at'];

	/**
     * The connection name for the model.
     *
     * @var string
     */
    protected $connection = 'clientDB';
}
