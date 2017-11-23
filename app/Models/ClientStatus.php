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
class ClientStatus extends Model
{
/**
 * client RELATIONSHIP TO THE CLIENT MODEL
 * @return data related
 */
    public function client(){
        return $this->belongsTo(App\Models\Client::class);
    }

}