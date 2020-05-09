# Simps & Aliyun IOT

使用 [Simps框架](https://github.com/simple-swoole/simps) 调用 [阿里云物联网平台](https://iot.console.aliyun.com) 服务（MQTT）

## 安装

使用`composer`进行安装或者直接使用`git clone`源码

```shell
composer create-project simps-cloud/aliyun-iot

git clone git@github.com:simps-cloud/aliyun-iot.git
```

## 使用

1. 前往 [阿里云物联网平台](https://iot.console.aliyun.com) 创建产品和设备，[参考文档](https://help.aliyun.com/document_detail/73705.html)

2. 创建`config/aliyuniot.php`配置文件

```php
<?php

return [
    'host' => "{$YourProductKey}.iot-as-mqtt.{$region}.aliyuncs.com", // 连接域名
    'port' => 1883, // 端口默认1883
    'keepalive' => 300, // 心跳
    'client_id' => "d812edc1-18da-2085-0edf-a4a588c296d1", // clientID
    'device_name' => "",
    'device_secret' => "",
    'product_key' => "",
    'product_secret' => "",
];
```

3. 订阅

```php
php bin/subscribe.php
```