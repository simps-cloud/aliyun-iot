<?php

declare(strict_types=1);
/**
 * This file is part of Simps.
 *
 * @link     https://simps.io
 * @document https://doc.simps.io
 * @license  https://github.com/simple-swoole/simps/blob/master/LICENSE
 */

namespace App\Controller;

use Simps\Client\MQTTClient;

class IndexController
{
    public function index($request, $response)
    {
        $config = config('aliyuniot', []);
        $mqttUsername = $config['device_name'] . "&" . $config['product_key'];
        $mqttPassword = signAliIot($config, "hmacsha1");
        $conConfig = [
            'host' => $config['host'],
            'port' => $config['port'],
            'time_out' => 5,
            'client_id' => $config['client_id'] . "|securemode=3,signmethod=hmacsha1|",
            'username' => $mqttUsername,
            'password' => $mqttPassword,
            'keepalive' => $config['keepalive'],
        ];
        $client = new MQTTClient($conConfig);
        $data = $client->connect();
        if ($data['cmd'] !== 2 && $data['code'] !== 0) {
            $response->end("connect error, see https://help.aliyun.com/document_detail/148610.html");
        }
        $time = time();
        $client->publish("/{$config['product_key']}/{$config['device_name']}/user/update", "Hello,Simps " . $time);
        $client->close();
        $response->end("Hello,Simps " . $time);
    }
}
