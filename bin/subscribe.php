<?php

declare(strict_types=1);
/**
 * This file is part of Simps.
 *
 * @link     https://simps.io
 * @document https://doc.simps.io
 * @license  https://github.com/simple-swoole/simps/blob/master/LICENSE
 */

require __DIR__ . '/../vendor/autoload.php';

!defined('CONFIG_PATH') && define('CONFIG_PATH', dirname(__DIR__) . '/config/');

use Simps\Client\MQTTClient;
use Simps\Server\Protocol\MQTT;

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

Co\run(function () use ($config, $conConfig) {
    $client = new MQTTClient($conConfig);
    $data = $client->connect();
    if ($data['cmd'] !== 2 && $data['code'] !== 0) {
        throw new RuntimeException("connect error, error code：{$data['code']}, see https://help.aliyun.com/document_detail/148610.html");
    }
    $topics["/{$config['product_key']}/{$config['device_name']}/user/get"] = 1;
    $timeSincePing = time();
    $client->subscribe($topics);
    while (true) {
        $buffer = $client->recv();
        var_dump($buffer);
        if (is_array($buffer)) {
            switch ($buffer['cmd']) {
                case 9:
                    echo "收到订阅确认消息\r\n";
                    break;
                case 3:
                    echo "收到订阅消息：{$buffer['content']}\r\n";
                    // 收到消息如果是qos 1 需要回复
                    if ($buffer['qos'] === 1) {
                        $client->sendBuffer(['cmd' => MQTT::PUBACK, 'message_id' => $buffer['message_id']], false); // 4
                    }
                    break;
            }
        }
        if ($buffer && $buffer !== true) {
            $timeSincePing = time();
        }
        if (isset($config['keepalive']) && $timeSincePing < (time() - $config['keepalive'])) {
            $buffer = $client->ping();
            if ($buffer) {
                echo '发送心跳包成功' . PHP_EOL;
                $timeSincePing = time();
            } else {
                $client->close();
                break;
            }
        }
    }
});


