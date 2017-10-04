<?php

namespace App\Models;

use Llama\Database\Eloquent\ModelTrait;

use Illuminate\Notifications\Notifiable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract,
    AuthorizableContract,
    CanResetPasswordContract
{
    use Authenticatable, /*Authorizable*/ CanResetPassword;
    use EntrustUserTrait {
        roles as rolesNotUsed;
    }

    use ModelTrait;

    use Notifiable;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are visible.
     *
     * @var array
     */
    protected $visible = [
        'id',
        'first_name',
        'last_name',
        'email',
        // custom attributes
        'full_name',
        // relations
        'orders',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $appends = array('full_name');

    public static $rules = [
        'id' => ['numeric'],
        'first_name' => ['string', 'max:255'],
        'last_name' => ['string', 'max:255'],
        'email' => ['string', 'max:255'],
    ];

    /**
     * Get the full name attribute.
     *
     * @param  string  $value
     * @return string
     */
    public function getFullNameAttribute($value)
    {
        return $this->first_name .' '. $this->last_name;
    }

    /**
     * A customer can have many orders
     * @return \App\Models\Order
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'customer_id', 'id');
    }
}
