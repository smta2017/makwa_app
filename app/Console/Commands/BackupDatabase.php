<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupDatabase extends Command
{
    protected $signature = 'db:backup';

    protected $description = 'Backup the database';

    protected $process;

    public function __construct()
    {
        parent::__construct();

        // $this->process = new Process(sprintf(
        //     'cd /var/www/api.makwaapp.com/public_html
        //      sudo php artisan backup:run --only-db
        //      ',
        // ));
        //  self::_send();
    }

    private static function _send()
    {
        $client = new Client();
        $full_url = config('services.slack.webhook_url', env('SLACK_WEBHOOK_URL'));
        if (empty($full_url)) {
            return;
        }
        $res = $client->request('POST', $full_url, ['headers' => ['Content-Type' => 'application/json'], 'body' => '{"text":"local The backup has been proceed successfully on AWS s3"}']);
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
