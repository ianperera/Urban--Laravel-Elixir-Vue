<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Llama\Database\Eloquent\ModelTrait;

class PriceGroupPrice extends Model
{
    use ModelTrait;

    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    protected $visible = [
        'id',
        'price_group_id',
        'category',
        'item_id',
        'item_name',
        'item_description',
        'price',
        'created_at',
        'updated_at'
    ];
}
