<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupAll extends Command
{
    protected $signature = 'all:backup';

    protected $description = 'Backup all';

    protected $process;

    public function __construct()
    {
        parent::__construct();

        // $this->process = new Process(sprintf(
        //     'cd /var/www/api.makwaapp.com/public_html
        //      sudo php artisan backup:run
        //      sudo rm "$(ls -t | tail -1)"',
        // ));
    }

    public function handle()
    {
        try {
            $this->process->mustRun();

            $this->info('The backup has been proceed successfully.');
        } catch (ProcessFailedException $exception) {
            $this->error('The backup process has been failed.');
        }
    }
}
