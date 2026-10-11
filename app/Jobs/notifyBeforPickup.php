<?php

namespace App\Jobs;

use App\Lib\NotifyGetaway;
use App\Notifications\BeforePickup;
use App\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class notifyBeforPickup implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $body = "Get ready " . $this->order->Customer->first_name . ", your Makwa pickup is scheduled between " . $this->order->pickup_start . " - " . $this->order->pickup_end  ;
        $noti = new BeforePickup($this->order->Customer->first_name, "Get Ready", $body);
        NotifyGetaway::sendNotification($this->order->Customer, $noti, $body, "orderDetails",$this->order->id,"Get Ready",);

    }
}
