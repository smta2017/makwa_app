<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class UploadeS3Backups extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 's3:upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'upload backups to aws s3 packet';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        // $this->process = new Process(sprintf(
        //     'sudo s3cmd put --ssl --human-readable-size --disable-multipart --recursive --progress --access_key=%s --secret_key=%s /var/www/api.makwaapp.com/public_html/storage/app/Makwa/ s3://makwadb2/Makwa/',
        //     config('filesystems.disks.s3.key'),
        //     config('filesystems.disks.s3.secret')
        // ));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $this->process->mustRun();

            $this->info('The backup has been upload successfully.');
        } catch (ProcessFailedException $exception) {
            $this->error('The backup upload process has been failed.');
        }
    }
}
