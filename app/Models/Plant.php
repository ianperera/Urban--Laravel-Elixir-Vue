<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Llama\Database\Eloquent\ModelTrait;

class Plant extends Model
{
    use ModelTrait; // ability to use JOIN with relations

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'name',
        'description',
        'location_id',
        // dates
        'created_at',
        'updated_at',
        'deleted_at',
        // relations
        'location',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'location_id'
    ];

    public static $rules = [
        'id' => ['numeric','nullable'],
        'location_id' => ['numeric', 'nullable'],
        'name' => ['string', 'max:255', 'nullable'],
        'description' => ['string', 'max:255', 'nullable'],
    ];

    /**
     * A plant has one location
     * @return \App\Models\Location
     */
    public function location()
    {
        return $this->hasOne('App\Models\Location', 'id', 'location_id');
    }

    /**
     * Filtered & Paginated scope
     * @param  [type]  $query
     * @param  string  $filter
     * @param  integer $count
     * @return [type]
     */
    public function scopeFilteredPaginate($query, $filter = '', $count = 10)
    {
        if ($filter !== '')
        {
            $query->where('name', 'like', '%' . $filter . '%')
                  ->orWhere('description', 'like', '%' . $filter . '%');
        }

        return $query->paginate($count);
    }
}
