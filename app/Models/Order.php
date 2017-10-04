<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Llama\Database\Eloquent\ModelTrait;
use Hemp\Presenter\Presentable;

class Order extends Model
{
    use ModelTrait;
    use SoftDeletes;
    use Presentable;

    protected $morphClass = 'order';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'deleted_at',
        'updated_at',
        'created_at',

        'note_admin',
        'note_dealer',

        'type',
        'uuid',
        'status_id',
        'dealer_notes', // dealer notes/descriptions
        'customer_id', // foreign
        'dealer_id', // foreign
        'reference_id', // foreign
        'sales_person',
        'sale_type',
        'payment_type',
        'building_condition',
        'building_id', // foreign
        'rto_type',
        'rto_term',
        'gross_buydown',
        'deposit_received',
        'payment_method',
        'transaction_id',
        'delivery_charge',
        'tax_delivery_charge',
        'dr_level_pad',
        'dr_soft_when_wet',
        'dr_width_restrictions',
        'dr_height_restrictions',
        'dr_requires_site_visit',
        'dr_must_cross_neighboring_prop',
        'dr_notes',
        'order_date',
        'date_submitted',
        'ced_start',
        'ced_end',
        'signature_method_id',

        // calculations
        'total_sales_price',

        'dealer_tax_rate',
        'dealer_commission_rate',
        'dealer_commission',

        'deposit_amount',
        'security_deposit',
        'net_buydown',
        'buydown_tax',
        'balance',
        'rto_amount',
        'rto_advance_monthly_renewal_payment',
        'rto_sales_tax',
        'rto_total_advanceMonthly_renewal_payment',
        'rto_factor',
        'sales_tax',
        'total_amount_due',
        'total_amount',
        
        // relations
        'sale',
        'order_reference',
        'dealer',
        'customer',
        'building',
        'files',

        // custom attributes
        'status',
        'payment_type_data',
        'order_type',
        'delivery_remarks_level_pad',
        'delivery_remarks_soft_when_wet',
        'delivery_remarks_width_restrictions',
        'delivery_remarks_height_restrictions',
        'delivery_remarks_must_cross_neighboring_property',
        'delivery_remarks_requires_site_visit',
        'delivery_remarks_notes',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'note_admin',
        'note_dealer',
        'type', 
        'uuid',
        'status_id',
        'dealer_notes', // dealer notes/descriptions
        'dealer_id', // foreign
        'customer_id', // foreign
        'reference_id', // foreign
        'sales_person',
        'sale_type', 
        'payment_type',
        'building_condition',
        'building_id', // foreign
        'rto_type',
        'rto_term',
        'gross_buydown',
        'deposit_received',
        'payment_method',
        'transaction_id',
        'tax_delivery_charge',
        'delivery_charge',
        'dr_level_pad',
        'dr_soft_when_wet',
        'dr_width_restrictions',
        'dr_height_restrictions',
        'dr_requires_site_visit',
        'dr_must_cross_neighboring_prop',
        'dr_notes',
        'order_date',
        'date_submitted',
        'ced_start',
        'ced_end',
        'signature_method_id',

        // calculations
        'total_sales_price',

        'dealer_tax_rate',
        'dealer_commission_rate',
        'dealer_commission',

        'deposit_amount',
        'security_deposit',
        'net_buydown',
        'buydown_tax',
        'balance',
        'rto_amount',
        'rto_advance_monthly_renewal_payment',
        'rto_sales_tax',
        'rto_total_advance_monthly_renewal_payment',
        'rto_factor',
        'sales_tax',
        'total_amount_due',
        'total_amount'
    ];

    protected $appends = ['status', 'order_type', 'payment_type_data', 'signature_method'];

    protected $casts = [
        'rto_term' => 'int',
        'promo99' => 'boolean'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'updated_at', 'created_at', 'esigned_on'];

    public static $paymentTypes = [
        'rto' => [
            'id' => 'rto',
            'title' => 'Rent To Own'
        ],
        'cash' => [
            'id' => 'cash',
            'title' => 'Cash'
        ]
    ];

    public static $orderTypes = [
        'custom-order' => [
            'id' => 'custom-order',
            'title' => 'Custom Order'
        ],
        'dealer-inventory' => [
            'id' => 'dealer-inventory',
            'title' => 'Dealer Inventory'
        ]
    ];

    public static $rtoTerms = [
        24 => [
            'value' => 24,
            'name' => '24 months',
            'rto_factor' => 16.8,
            'remaining_percentage' => 70
        ],
        36 => [
            'value' => 36,
            'name' => '36 months',
            'rto_factor' => 21.6,
            'remaining_percentage' => 60
        ],
        48 => [
            'value' => 48,
            'name' => '48 months',
            'rto_factor' => 24.6,
            'remaining_percentage' => 51
        ],
        60 => [
            'value' => 60,
            'name' => '60 months',
            'rto_factor' => 27.2,
            'remaining_percentage' => 45
        ],
    ];

    public static $statuses = [
        'draft' => [
            'id' => 'draft',
            'title' => 'Draft'
        ],
        'signature_pending' => [
            'id' => 'signature_pending',
            'title' => 'Signature Pending'
        ],
        'signed' => [
            'id' => 'signed',
            'title' => 'Signed'
        ],
        'submitted' => [
            'id' => 'submitted',
            'title' => 'Submitted'
        ],
        'review_needed' => [
            'id' => 'review_needed',
            'title' => 'Review Needed'
        ],
        'sale_generated' => [
            'id' => 'sale_generated',
            'title' => 'Sale generated'
        ],
        'cancelled' => [
            'id' => 'cancelled',
            'title' => 'Cancelled'
        ]
    ];

    public static $signature_methods = [
        'manual' => [
            'id' => 'manual',
            'title' => 'Manual'
        ],
        'e_signature' => [
            'id' => 'e_signature',
            'title' => 'E-Signature'
        ],
    ];

    public static $validator = 'App\Validators\OrderValidator';
    public static $rules = [
        'id' => ['numeric'],
        'status_id' => ['in:draft,signature_pending,signed,submitted,review_needed,sale_generated,cancelled'],
        'type' => ['in:order,quote'], // TODO: deprecate
        'uuid' => ['uuid'], // nullable?
        'dealer_notes' => ['string'],
        'dealer_id' => ['numeric'],
        'note_dealer' => ['string', 'nullable'],
        'note_admin' => ['string', 'nullable'],
        'reference_id' => ['numeric'],
        'customer_id' => ['numeric'],
        'sales_person' => ['string', 'regex:'.REGEX_NAME],
        'sale_type' => ['in:dealer-inventory,custom-order'],
        'building_condition' => ['in:new,used'],
        'building_id' => ['numeric'],
        'serial' => ['string', 'nullable'],
        'rto_type' => ['in:buydown,no-buydown'],
        'rto_term' => ['in:24,36,48,60'],
        'promo99' => ['boolean'],
        'gross_buydown' => ['numeric'],
        'deposit_received' => ['numeric'],
        'payment_type' => ['in:cash,rto'],
        'payment_method' => ['in:cash,check,credit_card'],
        'transaction_id' => ['alpha_dash'],
        'tax_delivery_charge' => ['boolean'],
        'delivery_charge' => ['numeric'],
        'dr_level_pad' => ['boolean'],
        'dr_soft_when_wet' => ['boolean'],
        'dr_width_restrictions' => ['boolean'],
        'dr_height_restrictions' => ['boolean'],
        'dr_requires_site_visit' => ['boolean'],
        'dr_must_cross_neighboring_prop' => ['boolean'],
        'dr_notes' => ['string', 'nullable'],
        'order_date' => ['date_format:Y-m-d'],
        'order_date' => ['date_format:Y-m-d H:i:s'],
        'ced_start' => ['date_format:Y-m-d'],
        'ced_end' => ['date_format:Y-m-d'],
        'signature_method_id' => ['in:manual,e_signature']
    ];

    const INITIAL_STATUS_ID = 'draft';

    public $rto_net_buydown; // avoid saving to db (no field)
    public $rto_total_days_advance_monthly_renewal_payment; // avoid saving to db (no field)
    public $min_deposit_amount; // avoid saving to db (no field)

    /**
     * Get rto term params
     *
     * @param  string  $value
     * @return string
     */
    public function getRtoTermParamsAttribute($value)
    {
        if ($this->rto_term && array_key_exists($this->rto_term, self::$rtoTerms)) {
            return self::$rtoTerms[$this->rto_term];
        }
        return null;
    }

    /**
     * Get status
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

    /**
     * Get payment type
     *
     * @param  string  $value
     * @return string
     */
    public function getPaymentTypeDataAttribute($value)
    {
        if ($this->payment_type && array_key_exists($this->payment_type, self::$paymentTypes)) {
            return collect(self::$paymentTypes[$this->payment_type]);
        }
        return null;
    }

    /**
     * Get order type
     *
     * @param  string  $value
     * @return string
     */
    public function getOrderTypeAttribute($value)
    {
        if ($this->sale_type && array_key_exists($this->sale_type, self::$orderTypes)) {
            return collect(self::$orderTypes[$this->sale_type]);
        }
        return null;
    }

    /**
     * Get signature method
     *
     * @param  string  $value
     * @return string
     */
    public function getSignatureMethodAttribute($value)
    {
        if ($this->signature_method && array_key_exists($this->signature_method, self::$signature_methods)) {
            return collect(self::$signature_methods[$this->signature_method]);
        }
        return null;
    }
    
    /**
     * Get the delivery remarks - level pad
     *
     * @return string
     */
    public function getDeliveryRemarksLevelPadAttribute()
    {
        return $this->dr_level_pad;
    }

    /**
     * Get the delivery remarks - soft when wet
     *
     * @return string
     */
    public function getDeliveryRemarksSoftWhenWetAttribute()
    {
        return $this->dr_soft_when_wet;
    }

    /**
     * Get the delivery remarks - width restictions
     *
     * @return string
     */
    public function getDeliveryRemarksWidthRestrictionsAttribute()
    {
        return $this->dr_width_restrictions;
    }

    /**
     * Get the delivery remarks - height restrictions
     *
     * @return string
     */
    public function getDeliveryRemarksHeightRestrictionsAttribute()
    {
        return $this->dr_height_restrictions;
    }

    /**
     * Get the udelivery remarks - requires site visit
     *
     * @return string
     */
    public function getDeliveryRemarksRequiresSiteVisitAttribute()
    {
        return $this->dr_requires_site_visit;
    }

    /**
     * Get the delivery remarks - must cross neighboring property
     *
     * @return string
     */
    public function getDeliveryRemarksMustCrossNeighboringPropAttribute()
    {
        return $this->must_cross_neighboring_prop;
    }

    /**
     * Get the delivery remarks - notes
     *
     * @return string
     */
    public function getDeliveryRemarksNotesAttribute()
    {
        return $this->dr_notes;
    }

    /**
     * Get the delivery remarks
     *
     * @return string
     */
    public function getDeliveryRemarksAttribute()
    {
        return [
            'level_pad' => $this->delivery_remarks_level_pad,
            'dr_soft_when_wet' => $this->delivery_remarks_soft_when_wet,
            'width_restrictions' => $this->delivery_remarks_width_restrictions,
            'height_restrictions' => $this->delivery_remarks_height_restrictions,
            'requires_site_visit' => $this->delivery_remarks_requires_site_visit,
            'must_cross_neighboring_property' => $this->delivery_remarks_must_cross_neighboring_property,
            'notes' => $this->delivery_remarks_notes
        ];
    }

    /**
     * An order has one order reference
     * @return \App\Models\OrderReference
     */
    public function order_reference()
    {
        return $this->hasOne(OrderReference::class, 'id', 'reference_id');
    }

    /**
     * An order belongs to customer
     * @return \App\Models\User
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    /**
     * An order has one dealer
     * @return \App\Models\Dealer
     */
    public function dealer()
    {
        return $this->hasOne('App\Models\Dealer', 'id', 'dealer_id');
    }

    /**
     * An order has one building
     * @return \App\Models\Building
     */
    public function building()
    {
        return $this->hasOne('App\Models\Building', 'id', 'building_id');
    }

    /**
     * Get all of the orders's files.
     * @return MorphMany
     */
    public function files()
    {
        return $this->morphMany('App\Models\File', 'storable');
    }

    /**
     * An order has many building options
     * @return \App\Models\OrderBuildingOption
     */
    public function sale()
    {
        return $this->hasOne(Sale::class);
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
            $query->where('status', 'like', '%' . $filter . '%')
                ->orWhere('user_id', 'like', '%' . $filter . '%');
        }

        return $query->paginate($count);
    }
}
