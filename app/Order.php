<?php

namespace App;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use CrudTrait;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    // protected $table = 'orders';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    // protected $guarded = ['id'];
    protected $fillable = [
        'customer_id',
        'address_id',
        'method_of_payment',
        'selected_card',
        'pickup_date',
        'pickup_start',
        'pickup_end',
        'delivery_date',
        'delivery_start',
        'delivery_end',
        'status_id',
        'promotion_id',
        'nextday_fee',
        'customer_notes',
        'vendor_id',
        'is_express',
        'created_by',
        'affiliate_id',
        'vendor_promotion_id',
        'fast_checkout',
        
    ];
    // protected $hidden = [];
    // protected $dates = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    public function openGoogle($crud = false)
    {
        return '<a class="btn btn-sm btn-link" target="_blank" href="http://google.com?q=laravel' . urlencode($this->text) . '" data-toggle="tooltip" title="Just a demo custom button."><i class="fa fa-search"></i> Google it</a>';
    }
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function OrderProducts()
    {
        return $this->hasMany('App\OrderProduct');
    }

    public function OrderFastPreferencies()
    {
        return $this->hasMany('App\OrderFastPreference');
    }

    public function OrderServices()
    {
        return $this->hasMany('App\OrderService');
    }

    public function orderHangLables()
    {
        return $this->hasMany('App\orderHangLable');
    }

    public function OrderBags()
    {
        return $this->hasMany('App\OrderBag');
    }

    public function Reviews()
    {
        return $this->hasMany('App\Review');
    }

    // -----------
    public function Customer()
    {
        return $this->belongsTo('App\Customer');
    }
    public function Customerx()
    {
        return $this->belongsTo('App\Customer');
    }

    public function TimeSlot()
    {
        return $this->belongsTo('App\TimeSlot', 'pickup_start', 'start_time');
    }

    public function Promotion()
    {
        return $this->belongsTo('App\Promotion');
    }

    public function OrderStatus()
    {
        return $this->belongsTo('App\OrderStatus', 'status_id');
    }

    public function CustomerAddress()
    {
        return $this->belongsTo('App\CustomerAddress', 'address_id');
    }

    public function Vendor()
    {
        return $this->belongsTo('App\Vendor');
    }

    public function VendorPromotion()
    {
        return $this->belongsTo('App\VendorPromotion');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    /**
     * Scope a query to only include none canceled orders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNoneCanceled($query)
    {
        return $query->whereNull('canceled_at');
    }

    public function scopeAffiliate($query)
    {
        return $query->where('affiliate_id', auth()->user()->id);
    }
    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
