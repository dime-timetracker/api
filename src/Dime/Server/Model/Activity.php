<?php

namespace Dime\Server\Model;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{

    use \Eloquence\Database\Traits\CamelCaseModel;

    protected $fillable = [ 'description', 'rate', 'rateReference' ];
    protected $guarded = ['id', 'user_id'];
    protected $hidden = ['customer_id', 'project_id', 'service_id', 'user_id'];

    public function customer()
    {
        return $this->belongsTo('Dime\Server\Model\Customer');
    }

    public function project()
    {
        return $this->belongsTo('Dime\Server\Model\Project');
    }

    public function service()
    {
        return $this->belongsTo('Dime\Server\Model\Service');
    }

    public function user()
    {
        return $this->belongsTo('Dime\Server\Model\User');
    }

    public function timeslices()
    {
        return $this->hasMany('Dime\Server\Model\Timeslice');
    }

    public function tags()
    {
        return $this->belongsToMany('Dime\Server\Model\Tag', 'activity_tags');
    }

    public function scopeBy($query, $name, $id = NULL)
    {
        if (is_null($id)) {
            $result = $query->whereNull($name);
        } else if (is_int($id)) {
            $result = $query->where($name, intval($id));
        } else if (is_array($id)) {
            $result = $query->whereIn($name, intval($id));
        }

        return $result;
    }

    public function scopeDate($query, $date)
    {
        if (is_array($date) && count($date) == 1) {
            $date = array_shift($date);
        }

        if (is_array($date)) {
            $result = $query->whereBetween('updated_at', $date);
        } else {
            $result = $query->where('updated_at', $date);
        }
        return $result;
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('description', 'like', '%' . $search . '%');
    }

    public function scopeOrdered($query)
    {
        return $query->latest('updated_at');
    }

}
