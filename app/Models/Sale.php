<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Llama\Database\Eloquent\ModelTrait;

class Sale extends Model
{
    use SoftDeletes;
    use ModelTrait;

    protected $morphClass = 'sale';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'status_id',
        'order_id',
        'building_id',
        'location_id',
        'invoice_id',
        'invoice_date',

        'created_at',
        'updated_at',
        'deleted_at',

        // custom attributes
        'status',

        // relations (jsonable)
        'order',
        'order_reference',
        'order_reference_name',
        'building',
        'location',
        'delivery'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status_id',
        'order_id',
        'building_id',
        'location_id',
        'invoice_id',
        'invoice_date',
    ];

    protected $appends = array('status');

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'updated_at', 'created_at'];

    public static $statuses = [
        'open' => [
            'id' => 'open',
            'title' => 'Open'
        ],
        'invoiced' => [
            'id' => 'invoiced',
            'title' => 'Invoiced'
        ],
        'closed' => [
            'id' => 'closed',
            'title' => 'Closed'
        ],
        'cancelled' => [
            'id' => 'cancelled',
            'title' => 'Cancelled'
        ],
        'updated' => [
            'id' => 'updated',
            'title' => 'Updated'
        ]
    ];
    
    public static $rules = [
        'id' => ['numeric'],
        'status_id' => ['in:open,invoiced,closed,cancelled,updated'],
        'order_id' => ['numeric'],
        'building_id' => ['numeric'],
        'location_id' => ['numeric'],
        'invoice_id' => ['string', 'max:255'],
        'invoice_date' => ['date_format:Y-m-d'],
    ];

    /**
     * Get the catogory attrs.
     *
     * @param  string  $value
     * @return string
     */
    public function getStatusAttribute($value)
    {
        if ($this->status_id && array_key_exists($this->status_id, self::$statuses)) {
            return collect(self::$statuses[$this->status_id]);
        }
        return null;
    }

    public function getCustomerNameAttribute()
    {
        if ( !array_key_exists('order_reference', $this->relations)) {
            $this->load('order_reference');
        }
        
        return $this->order_reference->first_name;
    }
    
    /**
     * An order has one order
     * @return \App\Models\Order
     */
    public function order()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }
    
    /**
     * An order reference has one order
     * @return \App\Models\Order
     */
    public function order_reference()
    {
        return $this->hasOne(OrderReference::class, 'id', 'order_id');
    }
    
    /**
     * An order reference has one order
     * @return \App\Models\Order
     */
    public function order_reference_name()
    {
        return $this->hasOne(OrderReference::class, 'id', 'order_id')->select(['id', 'first_name', 'last_name']);
    }

    /**
     * An order has one building
     * @return \App\Models\Building
     */
    public function building()
    {
        return $this->hasOne(Building::class, 'id', 'building_id');
    }

    /**
     * An order has one location
     * @return \App\Models\Location
     */
    public function location()
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    /**
     * An order has one delivery
     * @return \App\Models\Delivery
     */
    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }
}
