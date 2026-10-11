<?php

namespace App;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use QCod\Gamify\Gamify;
use Bavix\Wallet\Traits\HasWalletFloat;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Traits\CanPay;
use Bavix\Wallet\Interfaces\Customer as WalletCustomer;
use Questocat\Referral\Traits\UserReferral;
use Illuminate\Database\Eloquent\SoftDeletes;
class Customer extends Authenticatable implements JWTSubject, Wallet, WalletCustomer
{
    use Notifiable, HasWalletFloat, CanPay, CrudTrait, UserReferral,SoftDeletes;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    // protected $table = 'customers';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];

    public function Posts()
    {
        return $this->hasMany('App\Post');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // 'name', 
        'first_name',
        'last_name',
        'phone',
        'country_code',
        'email',
        'password',
        'jwt_token',
        'google_id',
        'facebook_id',
        'update_app',
        'message_to_update',
        'DOB',
        'gender',
        'disable_notification',
        'firebase_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function CustomerAddresses()
    {
        return $this->hasMany('App\CustomerAddress');
    }

    public function CustomerPreferencies()
    {
        return $this->hasMany('App\CustomerPreference');
    }

    public function Orders()
    {
        return $this->hasMany('App\Order')->orderBy('id', 'Desc');
    }

    public function FavouritVendors()
    {
        return $this->hasMany('App\FavouritVendor');
    }

    public function CustomerBags()
    {
        return $this->hasMany('App\customerBag');
    }

    public function CustomerLocations()
    {
        return $this->hasMany('App\CustomerLocation');
    }
    public function Bags()
    {
        return $this->hasMany('App\Bag');
    }
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
