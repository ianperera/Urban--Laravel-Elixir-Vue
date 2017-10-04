<?php

namespace App\Models;

use App\Validators\ColorValidator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Llama\Database\Eloquent\ModelTrait;

class Color extends Model
{
    use SoftDeletes;
    use ModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'type',
        'name',
        'hex',
        'url',
        'option_id',
        'use_body',
        'use_trim',
        'use_roof',
        'is_active',

        'label',

        'created_at',
        'updated_at',
        'deleted_at',

        // relations (jsonable)

        'option',
        'allowable_models',
        'allowable_options',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'name',
        'custom',
        'hex',
        'url',
        'option_id',
        'is_active'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'deleted_at'
    ];

    protected $appends = array('label', 'usage', 'name');

    public static $validator = ColorValidator::class;
    public static $rules = [
        'id' => ['numeric'],
        'type' => ['required', 'string', 'in:standard,custom'],
        'name' => ['required', 'string', 'max:50'],
        'hex' => ['string', 'color_hex'],
        'url' => ['string'],
        'option_id' => ['numeric', 'exists:options,id,deleted_at,NULL'],
        'is_active' => ['in:yes,no']
    ];

    public static $types = [
        'standard' => 'Standard',
        'custom' => 'Custom'
    ];

    public static $isActive = [
        'yes' => [
            'id' => 'yes',
            'name' => 'Yes',
        ],
        'no' => [
            'id' => 'no',
            'name' => 'No',
        ]
    ];

    /**
     * Get the label attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getLabelAttribute($value)
    {
        return $this->getOriginal('name');
    }

    /**
     * Get the list of allowable models IDs for color.
     *
     * @param  string  $value
     * @return string
     */
    public function getAllowableModelsIdAttribute($value)
    {
        return $this->allowable_models->pluck('id')->toArray();
    }

    public function getNameAttribute()
    {
        if (isset($this->attributes['custom'])) return $this->attributes['custom'];
        if (isset($this->pivot) && isset($this->pivot->custom)) {
            return $this->pivot->custom;
        }

        return $this->attributes['name'];
    }

    /**
     * A color belongs to an option
     * @return \App\Models\Option
     */
    public function option()
    {
        return $this->belongsTo('App\Models\Option');
    }

    /**
     * A color has many allowable building models
     * @return \App\Models\BuildingModel
     */
    public function allowable_models()
    {
        return $this->belongsToMany(BuildingModel::class, 'color_allowable_models', 'color_id')->withTimestamps();
    }

    /**
     * A color has many allowable optinos
     * @return \App\Models\Option
     */
    public function allowable_options()
    {
        return $this->belongsToMany(Option::class, 'option_allowable_colors', 'color_id')->withTimestamps();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 'yes');
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
        if (trim($filter) !== '')
        {
            $query->where('name', 'like', '%' . $filter . '%')
                ->orWhere('type', 'like', '%' . $filter . '%')
                ->orWhere('hex', 'like', '%' . $filter . '%')
                ->orWhere('url', 'like', '%' . $filter . '%')
                ->orWhere('option_id', '=', $filter)
                ->orWhere('use_body', 'like', '%' . $filter . '%')
                ->orWhere('use_trim', 'like', '%' . $filter . '%')
                ->orWhere('use_roof', 'like', '%' . $filter . '%');
        }

        return $query->paginate($count);
    }
}
