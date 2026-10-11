<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class sslUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ssl:renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'renew ssl certification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        // $this->process = new Process(sprintf(
        //     'cd /usr/local/letsencrypt 
        //      sudo ./letsencrypt-auto --apache -d makwaapp.com',
        // ));


    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }
}
