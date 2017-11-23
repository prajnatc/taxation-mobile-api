<?php
/**
 * TaxType
 * @category Model
 * @author ATCOnline
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * TaxType Model
 * @category Model
 * @author ATCOnline
 */
class TaxType extends Model
{
	use SoftDeletes;
	protected $dates = ['created_at','updated_at','deleted_at'];
}