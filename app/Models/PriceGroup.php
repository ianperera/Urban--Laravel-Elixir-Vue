<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Llama\Database\Eloquent\ModelTrait;

class PriceGroup extends Model
{
    use ModelTrait;

    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    protected $visible = [
        'id',
        'name',
        'category',
        'publish_date',
        'published_at',
        'created_at',
        'updated_at',
        'archived_at',
        'status',
        'notes',
        'publish_dates'
    ];

    public static $rules = [
        'id'       => ['numeric'],
        'name'     => ['string', 'max:255'],
        'category' => ['string', 'max:255', 'in:models,options'],
        'status'   => ['string', 'max:255', 'in:draft,pending,published,archived'],
    ];

    public static $categories = [
        'models' => [
            'id' => 'models',
            'title' => 'Models'
        ],
        'options' => [
            'id' => 'options',
            'title' => 'Options'
        ]
    ];

    public static $statuses = [
        'draft' => [
            'id' => 'draft',
            'title' => 'Draft'
        ],
        'pending' => [
            'id' => 'pending',
            'title' => 'Pending'
        ],
        'published' => [
            'id' => 'published',
            'title' => 'Published'
        ],
        'archived' => [
            'id' => 'archived',
            'title' => 'Archived'
        ],
    ];
}
