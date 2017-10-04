<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Llama\Database\Eloquent\ModelTrait;

class Style extends Model
{
    use ModelTrait; // ability to use JOIN with relations
    
    /**
     * The attributes that are visible.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'name',
        'description',
        'short_code',
        'icon_path',
        'is_active',
        'created_at',
        'updated_at',

        'building_models'
    ];
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'short_code',
        'icon_path',
        'is_active'
    ];

    public static $validator = 'App\Validators\StyleValidator';
    public static $rules = [
        'id' => ['numeric'],
        'name' => ['string', 'max:255'],
        'description' => ['string', 'max:255'],
        'short_code' => ['string'],
        'icon_path' => ['string'],
        'is_active' => ['string', 'in:yes,no'],
        'updated_at' => ['date:Y-m-d'],
        'created_at' => ['date:Y-m-d'],
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

    public function getActiveAttribute() {
        return collect(self::$isActive[$this->is_active]);
    }

    /**
     * Style can have more than one building model
     * @return \App\Models\BuildingModel
     */
    public function building_models()
    {
        return $this->hasMany('App\Models\BuildingModel');
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
                  ->orWhere('description', 'like', '%' . $filter . '%')
                  ->orWhere('short_code', 'like', '%' . $filter . '%');
        }

        return $query->paginate($count);
    }
}
