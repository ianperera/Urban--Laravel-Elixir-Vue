<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Llama\Database\Eloquent\ModelTrait;

class OptionCategory extends Model
{
    use SoftDeletes;
    use ModelTrait; // ability to use JOIN with relations

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'option_categories';

    /**
     * The attributes that are visible.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'name',
        'group',
        'is_required',
        'qty_limit',
        // dates
        'created_at',
        'updated_at',
        'deleted_at',
        // relations
        'options',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'group',
        'is_required',
        'qty_limit',
    ];

    protected $casts = [
        'is_required' => 'boolean'
    ];

    public static $rules = [
        'id' => ['numeric'],
        'name' => ['string', 'max:255'],
        'group' => ['string'],
        'is_required' => ['nullable', 'numeric', 'in:1,0'],
        'qty_limit' => ['nullable', 'numeric'],
    ];

    public static $dealerGroups = ['misc', 'doors', 'windows', 'interior', 'exterior', 'trim', 'roof', 'siding', 'decks', 'discounts', 'dealers'];
    public static $customerGroups = ['misc', 'doors', 'windows', 'interior', 'exterior', 'trim', 'roof', 'siding', 'decks'];

    /**
     * An option category has many options
     * @return \App\Models\Option
     */
    public function options()
    {
        return $this->hasMany(Option::class, 'category_id', 'id');
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
            $query->whereNull('deleted_at')
                ->where(function ($query) use($filter) {
                    $query->where('name', 'like', '%' . $filter . '%')
                        ->orWhere('group', 'like', '%' . $filter . '%')
                        ->orWhere('description', 'like', '%' . $filter . '%');
                });
        }

        return $query->paginate($count);
    }
}