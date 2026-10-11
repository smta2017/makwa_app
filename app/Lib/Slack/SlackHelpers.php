<?php

namespace App\Lib\Slack;

use GuzzleHttp\Client;

class SlackHelpers
{

    private static function _send($msg)
    {
        $client = new Client();
        $full_url = config('services.slack.webhook_url', env('SLACK_WEBHOOK_URL'));
        if (empty($full_url)) {
            return;
        }
        $body = json_encode(['text' => $msg]);
        $res = $client->request('POST', $full_url, ['headers' => ['Content-Type' => 'application/json'], 'body' => $body]);
    }

    public function sendNotify($msg)
    {
        return $this->_send($msg);
    }
}
