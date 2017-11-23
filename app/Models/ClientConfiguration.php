<?php
/**
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Client as Client;

/**
 * @category Model
 * @author ThinkPace
 */
class ClientConfiguration extends Model
{
	/**
     * The connection name for the model.
     *
     * @var string
     */
	protected $connection = 'adminDB';

	/**
	 * client relationsgip to client model
	 * @return related data
	 */
    public function client(){
        return $this->belongsTo(Client::class);
    }

    /**
     * getClientSpecificationsAttribute relationship to the model
     * @param  string $value 
     * @return json array of value
     */
    public function getClientSpecificationsAttribute($value){

    	if(!is_null($value) && !empty($value)){
    		return json_decode($value);
    	}

    }

}