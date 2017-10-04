<?php
// TODO: deprecate (ready to remove)

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderBuildingOption extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'option_id',
        'quantity',
        'unit_price',
        'total_price',
        ];

    /**
     * An order building option belongs to a order
     * @return \App\Models\Order
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    /**
     * An order building option belongs to an option
     * @return \App\Models\Option
     */
    public function option()
    {
        return $this->belongsTo('App\Models\Option');
    }
}
