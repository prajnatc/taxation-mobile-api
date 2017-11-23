<?php
/**
 * @category Model
 * @author ThinkPace
 */
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClientConfiguration as ClientConfiguration;
/**
 * @category Model
 * @author ThinkPace
 */
class Client extends Model
{
	/**
     * The connection name for the model.
     *
     * @var string
     */
	protected $connection = 'adminDB';

	/**
	 * $fillable inserted values
	 * @var array
	 */
    protected $fillable = ['name'];


    /**
     * clientConfiguration relationship to the table
     * @return relationship data
     */
    public function clientConfiguration(){
        return $this->hasOne(ClientConfiguration::class);
    }

    /**
     * clientStatus relationship to the clientStatus Model
     * @return related data
     */
    public function clientStatus(){
        return $this->hasOne(App\Models\ClientStatus::class);
    }

}
