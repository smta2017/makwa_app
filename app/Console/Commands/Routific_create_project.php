<?php

namespace App\Console\Commands;

use App\Http\Controllers\routific\routificController;
use App\routific_project;
use Illuminate\Console\Command;
use DB;
class Routific_create_project extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'minute:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'upload routific project every day';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $routificController = routificController::create_project();
    }
}
