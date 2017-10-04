<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Storage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Hemp\Presenter\Presentable;

class File extends Model
{
    use Presentable;
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'user_id',
        'storable_id',
        'storable_type',
        'category_id',
        'type',
        'mime',
        'path',
        'name',
        'ext',
        'description',
        'source_id',
        'reason',
        'size',
        'width',
        'height',

        //dates
        'created_at',
        'updated_at',
        'deleted_at',

        // custom attrs

        'public_path',
        'direct_path',
        'storage_path',
        'category',

        // relations
        'building',
        'option',
        'order',
        'signs',
        'location'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [ 'created_at', 'updated_at', 'deleted_at' ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'storable_id',
        'storable_type',
        'category_id',
        'type',
        'mime',
        'path',
        'name',
        'ext',
        'description',
        'source_id',
        'reason',
        'size',
        'width',
        'height'
    ];

    protected $appends = array('public_path', 'direct_path', 'storage_path', 'category');

    public static $rules = [
        'id' => ['numeric'],
        'user_id' => ['numeric'],
        'storable_id' => ['numeric'],
        'storable_type' => ['string'],
        'category_id' => ['string'],
        'type' => ['string', 'nullable'],
        'mime' => ['string', 'nullable'],
        'path' => ['string', 'nullable'],
        'name' => ['string', 'nullable'],
        'ext' => ['string', 'nullable'],
        'description' => ['string', 'nullable'],
        'source_id' => ['numeric', 'nullable'],
        'size' => ['numeric', 'nullable'],
        'width' => ['numeric', 'nullable'],
        'height' => ['numeric', 'nullable'],
    ];

    public static $categories = [
        'other' => [
            'id' => 'other',
            'title' => 'Other'
        ],
        'driver_license' => [
            'id' => 'driver_license',
            'title' => 'Driver\'s License'
        ],
        'all_forms' => [ // deprecated
            'id' => 'all_forms',
            'title' => 'All Forms'
        ],
        'quote_forms' => [
            'id' => 'quote_forms',
            'title' => 'Quote Forms'
        ],
        'order_forms' => [ // deprecated
            'id' => 'order_forms',
            'title' => 'Order Forms'
        ],
        'rto_docs' => [ // deprecated
            'id' => 'rto_docs',
            'title' => 'RTO docs'
        ],
        'signed_all_forms' => [ // deprecated
            'id' => 'signed_all_forms',
            'title' => 'Signed Forms'
        ],
        'signed_order_forms' => [ // deprecated
            'id' => 'signed_order_forms',
            'title' => 'Signed Order Forms'
        ],
        'signed_rto_docs' => [ // deprecated
            'id' => 'signed_rto_docs',
            'title' => 'Signed RTO docs'
        ],
        //
        // new based on #154
        'unsigned_order_documents' => [
            'id' => 'unsigned_order_documents',
            'title' => 'Order Documents'
        ],
        'signed_order_documents' => [
            'id' => 'signed_order_documents',
            'title' => 'Signed Order Documents'
        ],
        'complete_order_documents' => [
            'id' => 'complete_order_documents',
            'title' => 'Order Documents'
        ],
        'e_signed_order_documents' => [
            'id' => 'e_signed_order_documents',
            'title' => 'Signed Order Documents (E-Signature)'
        ],
        'building_configuration' => [
            'id' => 'building_configuration',
            'title' => 'Building Configuration'
        ],
        'signed_building_configuration' => [
            'id' => 'signed_building_configuration',
            'title' => 'Signed Building Configuration'
        ],
        'neighbor_release' => [
            'id' => 'neighbor_release',
            'title' => 'Neighbor Release'
        ],
        'signed_neighbor_release' => [
            'id' => 'signed_neighbor_release',
            'title' => 'Signed Neighbor Release'
        ],
        'deposit_receipt' => [
            'id' => 'deposit_receipt',
            'title' => 'Deposit Receipt'
        ],
        'signed_deposit_receipt' => [
            'id' => 'signed_deposit_receipt',
            'title' => 'Signed Deposit Receipt'
        ],
        'build_status' => [
            'id' => 'build_status',
            'title' => 'Build Status'
        ],
    ];

    /**
     * Get the public path attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getPublicPathAttribute($value)
    {
        if ($this->storable_type === 'order' || $this->storable_type === 'building') {
            return '/api/files/'.$this->id;
        }

        return '/storage'.$this->path.$this->name;
    }

    /**
     * Get the public path attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getDirectPathAttribute($value)
    {
        return '/storage'.$this->path.$this->name;
    }

    /**
     * Get the public path attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getStoragePathAttribute($value)
    {
        return storage_path('app/public').$this->path.$this->name;
    }

    /**
     * Get the catogory attrs.
     *
     * @param  string  $value
     * @return string
     */
    public function getCategoryAttribute($value)
    {
        if ($this->category_id && array_key_exists($this->category_id, self::$categories)) {
            return collect(self::$categories[$this->category_id]);
        }
        return null;
    }

    public function storable()
    {
        return $this->morphTo();
    }

    /**
     * A file belongs to a building
     * @return \App\Models\Building
     */
    public function building()
    {
        return $this->belongsTo(Building::class, 'storable_id');
    }

    /**
     * A file belongs to a order
     * @return \App\Models\Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'storable_id');
    }

    /**
     * A file belongs to a building
     * @return \App\Models\Building
     */
    public function option()
    {
        return $this->belongsTo('App\Models\Option');
    }

    /**
     * A file has many file signatures
     * @return HasMany
     */
    public function signs()
    {
        return $this->hasMany(FileSign::class, 'file_id', 'id');
    }

    /**
     * A file has one location info
     * @return \App\Models\LocationFiles
     */
    public function location()
    {
        return $this->hasOne(LocationFiles::class, 'file_id', 'id');
    }

    /**
     * file scope get by storable Id and Type
     * @param  [type]  $query
     * @param  int  $location_id
     * @param  string  $type
     * @return [type]
     */
    public function scopeGetbyIdType($query , $location_id , $type)
    {
        return $query->where('storable_id', $location_id)->where('storable_type', $type);
    }
}
