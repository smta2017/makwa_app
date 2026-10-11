<?php

namespace App\Observers;

use App\Order;
use App\OrderFastPreference;

class OrderObserver
{
    /**
     * Handle the order "created" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        // if ($order['customised_preference'] == 1) {
        //     foreach ($order['preferences'] as $preferences) {
        //         $orderPreference = new OrderFastPreference();
        //         $orderPreference->order_id = $order->id;
        //         $orderPreference->product_id = $preferences['product_id'];
        //         $orderPreference->preference = $preferences['preference'];
        //         $orderPreference->name = $preferences['name'];
        //         $orderPreference->save();
        //     }
        // }
    }

    /**
     * Handle the order "updated" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        //
    }

    /**
     * Handle the order "deleted" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }

    /**
     * Handle the order "restored" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the order "force deleted" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
