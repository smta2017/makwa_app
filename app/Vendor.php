<?php

namespace App;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Backpack\CRUD\app\Models\Traits\SpatieTranslatable\HasTranslations;
use Codebyray\ReviewRateable\Contracts\ReviewRateable;
use Codebyray\ReviewRateable\Traits\ReviewRateable as ReviewRateableTrait;

class Vendor extends Authenticatable implements JWTSubject
{
    // use HasTranslations;
    // public $translatable = ['name'];


    use CrudTrait;
    use Notifiable;
    use HasRoles;
    use ReviewRateableTrait;


    /**
     *
     * @var $round
     * @var $onlyApproved
     * @return mixed
     */
    public function averageDeliveryTimeRating($round = null, $onlyApproved = false)
    {
        $where = $onlyApproved ? [['approved', '1']] : [];

        if ($round) {
            return $this->ratings()
                ->selectRaw('ROUND(AVG(delivery_time_rating), ' . $round . ') as averageDeliveryTimeRateable')
                ->where($where)
                ->pluck('averageDeliveryTimeRateable');
        }

        return $this->ratings()
            ->selectRaw('AVG(delivery_time_rating) as averageDeliveryTimeRateable')
            ->where($where)
            ->pluck('averageDeliveryTimeRateable');
    }

    public function averageOrderPackaging($round = null, $onlyApproved = false)
    {
        $where = $onlyApproved ? [['approved', '1']] : [];

        if ($round) {
            return $this->ratings()
                ->selectRaw('ROUND(AVG(order_packaging_rating), ' . $round . ') as averageOrderPackagingRateable')
                ->where($where)
                ->pluck('averageOrderPackagingRating');
        }

        return $this->ratings()
            ->selectRaw('AVG(order_packaging_rating) as averageOrderPackagingRateable')
            ->where($where)
            ->pluck('averageOrderPackagingRateable');
    }

    public function averageValueMoney($round = null, $onlyApproved = false)
    {
        $where = $onlyApproved ? [['approved', '1']] : [];

        if ($round) {
            return $this->ratings()
                ->selectRaw('ROUND(AVG(value_money_rating), ' . $round . ') as averageValueMoneyRateable')
                ->where($where)
                ->pluck('averageValueMoneyRateable');
        }

        return $this->ratings()
            ->selectRaw('AVG(value_money_rating) as averageValueMoneyRateable')
            ->where($where)
            ->pluck('averageValueMoneyRateable');
    }

    public function averageServiceQuality($round = null, $onlyApproved = false)
    {
        $where = $onlyApproved ? [['approved', '1']] : [];

        if ($round) {
            return $this->ratings()
                ->selectRaw('ROUND(AVG(service_quality_rating), ' . $round . ') as averageServiceQualityRateable')
                ->where($where)
                ->pluck('averageServiceQualityRateable');
        }

        return $this->ratings()
            ->selectRaw('AVG(service_quality_rating) as averageServiceQualityRateable')
            ->where($where)
            ->pluck('averageServiceQualityRateable');
    }
 


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

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

    public function Orders()
    {
        return $this->hasMany('App\Order');
    }

    public function VendorSlotsTimes()
    {
        return $this->hasMany('App\VendorSlotsTime');
    }

    public function OrderServices()
    {
        return $this->hasMany('App\OrderService');
    }

    public function vendorLocations()
    {
        return $this->hasMany('App\vendorLocation');
    }

    public function VendorServices()
    {
        return $this->hasMany('App\VendorService');
    }

    public function VendorPackags()
    {
        return $this->hasMany('App\VendorPackag');
    }

    public function VendorProducts()
    {
        return $this->hasMany('App\VendorProduct');
    }

    public function VendorServiceTags()
    {
        return $this->hasMany('App\VendorServiceTag');
    }

    public function VendorProductPrices()
    {
        return $this->hasMany('App\VendorProductPrice');
    }

    public function FavouritVendors()
    {
        return $this->hasMany('App\FavouritVendor');
    }

    public function VendorPromotions()
    {
        return $this->hasMany('App\VendorPromotion');
    }
}
