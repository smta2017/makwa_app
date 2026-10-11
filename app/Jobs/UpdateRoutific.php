<?php

namespace App\Jobs;

use App\Http\Controllers\routific\routificController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateRoutific implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $oid;

    public function __construct($oid)
    {
        $this->oid = $oid;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        routificController::update_project($this->oid);
    }
}
