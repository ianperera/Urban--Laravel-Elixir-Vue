<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Llama\Database\Eloquent\ModelTrait;

class RtoCompany extends Model
{
    use SoftDeletes;
    use Notifiable;
    use ModelTrait;

    public $incrementing = true;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'rto_companies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'name',
        'email',
        'physical_address',
        'mailing_address',
        'primary_phone',
        'primary_contact',
        'logo',
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
        'type',
        'email',
        'physical_address',
        'mailing_address',
        'primary_phone',
        'primary_contact',
        'logo'
    ];

    public static $rules = [
        'id' => ['uuid'],
        'name' => ['string'],
        'email' => ['email'],
        'physical_address' => ['string'],
        'mailing_address' => ['mailing_address'],
        'primary_phone' => ['primary_phone'],
        'logo' => ['logo'],
    ];

    const SIGNER_ROLE = 'rto_company';
}
