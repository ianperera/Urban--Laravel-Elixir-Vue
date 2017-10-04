<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Llama\Database\Eloquent\ModelTrait;

class OrderContact extends Model
{
    use ModelTrait;

    protected $primaryKey = 'id';
    protected $guarded = ['id'];
    protected $table = 'order_contacts';

    protected $visible = [
        'id',
        'order_id',
        'customer_id',
        'initial_contact',
        'order_submit',
        'created_at',
        'updated_at',
        'total_price',

        // relations
        'customer',
        'building'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id', 'id');
    }

    public function building()
    {
        return $this->hasOne('App\Models\Building', 'order_id', 'order_id');
    }
}
