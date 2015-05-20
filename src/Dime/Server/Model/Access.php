<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;
/**
 * AccessToken
 *
 * @author Danilo Kuehn <dk@nogo-software.de>
 */
class Access extends Model
{
    protected $table = 'access';

    protected $fillable = [ 'user_id', 'client' ];
    protected $guarded = [ 'token' ];
    protected $hidden = [ 'token' ];
    
    public function user()
    {
        return $this->belongsTo('Dime\Server\Model\User');
    }
}
