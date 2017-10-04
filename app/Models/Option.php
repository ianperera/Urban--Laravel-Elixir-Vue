<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Llama\Database\Eloquent\ModelTrait;

class Option extends Model
{
    use ModelTrait; // ability to use JOIN with relations
    use SoftDeletes;
    
    protected $morphClass = 'option';
    
    /**
     * The attributes that are visible.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'category_id',
        'force_quantity',
        'name',
        'description',
        'unit_price',
        'is_active',
        'created_at',
        'updated_at',
        'deleted_at',
        // appends
        'unit_price',
        'quantity',
        'total',
        'active',
        'force_quantity_flag',
        // 'pivot',
        // relations
        'files',
        'building_options',
        'category',
        'option_packages',
        'allowable_models',
        'allowable_colors',
        // building related
        'color',
        'default_color_id',
        'price_list_price',
        'taxable',
        'rto',
        'constraint_type',
        'constraint_type_flag'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'force_quantity',
        'name',
        'description',
        'unit_price',
        'default_color_id',
        'is_active',
        'taxable',
        'rto',
        'constraint_type'
    ];
    protected $appends = [
        'unit_price',
        'quantity',
        'total',
        'active',
        'force_quantity_flag',
        'constraint_type_flag'
    ];

    public static $rules = [
        'id' => ['numeric'],
        'category_id' => ['numeric', 'nullable'],
        'force_quantity' => ['string', 'in:building_length,wall_area,floor_area', 'nullable'],
        'name' => ['max:255'],
        'description' => ['string', 'max:255', 'nullable'],
        'unit_price' => ['numeric'],
        'is_active' => ['string', 'in:yes,no'],
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

    public static $forceQuantity = [
        'building_length' => [
            'id' => 'building_length',
            'name' => 'Building length',
        ],
        'wall_area' => [
            'id' => 'wall_area',
            'name' => 'Wall area'
        ],
        'floor_area' => [
            'id' => 'floor_area',
            'name' => 'Floor area'
        ]
    ];

    public static $constraintType = [
        'less_than' => [
            'id' => 'less_than',
            'name' => 'Less than'
        ],
        'equal_to' => [
            'id' => 'equal_to',
            'name' => 'Equal to'
        ]
    ];

    public function getActiveAttribute() {
        if (!isset($this->is_active)) return null;
        return collect(self::$isActive[$this->is_active]);
    }

    public function getForceQuantityFlagAttribute() {
        if (!isset($this->force_quantity)) return null;
        return collect(self::$forceQuantity[$this->force_quantity]);
    }
    
    /**
     * Get the list of allowable models IDs for option.
     *
     * @param  string  $value
     * @return string
     */
    public function getAllowableModelsIdAttribute($value)
    {
        return $this->allowable_models->pluck('id')->toArray();
    }

    public function getUnitPriceAttribute()
    {
        if (isset($this->pivot) && isset($this->pivot->unit_price)) 
            return $this->pivot->unit_price;
        
        return $this->getOriginal('unit_price');
    }

    public function getQuantityAttribute()
    {
        if (isset($this->pivot) && isset($this->pivot->quantity)) 
            return $this->pivot->quantity;
        
        return 1;
    }

    public function getTotalAttribute()
    {
        if (isset($this->pivot) && isset($this->pivot->quantity))
            return $this->pivot->quantity * $this->unit_price;

        return $this->unit_price;
    }

    public function getConstraintTypeFlagAttribute() {
        if (!isset($this->constraint_type)) return null;
        return collect(self::$constraintType[$this->constraint_type]);
    }
    /**
     * Get all of the option's files.
     * @return \App\Models\File
     */
    public function files()
    {
        return $this->morphMany('App\Models\File', 'storable');
    }
    
    /**
     * An option has many building options
     * @return \App\Models\BuildingOption
     */
    public function building_options()
    {
        return $this->hasMany('App\Models\BuildingOption');
    }
    
    /**
     * An option has one category
     * @return \App\Models\OptionCategory
     */
    public function category()
    {
        return $this->hasOne('App\Models\OptionCategory', 'id', 'category_id');
    }

    /**
     * A option package has many allowable building models
     * @return \App\Models\BuildingModel
     */
    public function allowable_models()
    {
        return $this->belongsToMany('App\Models\BuildingModel', 'option_allowable_models', 'option_id')->withTimestamps();
    }

    /**
     * A option package has many allowable colors
     * @return \App\Models\Color
     */
    public function allowable_colors()
    {
        return $this->belongsToMany(Color::class, 'option_allowable_colors', 'option_id')->withTimestamps();
    }

    /**
     * Scope a query to only include active option.
     *
     * @param $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
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
        if ($filter !== '')
        {
            $query->where('name', 'like', '%' . $filter . '%')
                  ->orWhere('description', 'like', '%' . $filter . '%');
        }

        return $query->paginate($count);
    }
}
