<?php
/**
 * ParentStudent
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * ParentStudent
 * @category Model
 * @author ThinkPace
 */
class ParentStudent extends Model
{
		use SoftDeletes;

/**
 * $fillable mandatory fields
 * @var array
 */
    protected $fillable = ['parent_id'];

/**
 * parent relationship to the ParentUser model
 * @return array parent user data
 */
    public function parent(){
        return $this->belongsTo(App\Models\ParentUser::class);
    }

/**
 * client relationship to the Client model
 * @return array Client data
 */
    public function client(){
        return $this->belongsTo(Client::class,'client_id','id');
    }
}
