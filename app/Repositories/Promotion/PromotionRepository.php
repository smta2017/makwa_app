<?php

namespace App\Repositories\Promotion;

use App\Http\Resources\V1\Promotion\Promotion as PromotionResource;
use App\Promotion;
use App\Repositories\Order\OrderRepository;
use Carbon\Carbon;

class PromotionRepository implements IPromotion
{
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }


    public function store($request)
    {
    }
    public function all()
    {
    }
    public function show($id)
    {
    }
    public function update($request)
    {
    }
    public function delete($id)
    {
    }
    public function isPromoValid($promoCode, $CustomerId, $theDate = null)
    {
        if (isset($promoCode) && !empty($promoCode)) {

            $promotion = Promotion::where('name', $promoCode)
                ->where('start_date', '<=', ($theDate) ? $theDate : Carbon::now()->toDateString())
                ->where('end_date', '>=', ($theDate) ? $theDate : Carbon::now()->toDateString())
                ->where('is_archive', 0)
                ->first();
            if (!$promotion == null) {
                $promoUsedCount =  $this->orderRepository->getPromoCount($CustomerId, $promotion->id);
                if ($promotion->usage_limit == 0 || ($promotion->usage_limit - $promoUsedCount) >= 0) {
                    return new PromotionResource($promotion);
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
    }
}
