<?php

namespace App\Jobs;

use App\Http\Controllers\payment2\paymentController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\OrderController;

class withdrawalJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public $oid;

    public function __construct($oid)
    {
        $this->oid=$oid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $withdrawal= OrderController::withdrawal($this->oid);
    }
}
