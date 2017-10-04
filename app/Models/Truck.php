<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Llama\Database\Eloquent\ModelTrait;

class Truck extends Model
{
    use SoftDeletes, ModelTrait;

    protected $visible = [
        'id',
        'name',
        'delivery_capacity',
        'notes',
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'name',
        'delivery_capacity',
        'notes'
    ];

    public static $rules = [
        'name' => ['string'],
        'delivery_capacity' => ['numeric'],
        'notes' => ['string']
    ];
}
