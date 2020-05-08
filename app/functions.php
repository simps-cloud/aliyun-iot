<?php

if(!function_exists("arr2str")) {
    /**
     * @param $arr
     * @return string
     */
    function arr2str($arr)
    {
        $res = "";
        foreach ($arr as $key => $item) {
            $res .= $key . $item;
        }
        return $res;
    }
}

if(!function_exists("signAliIot")) {
    /**
     * @param $config
     * @param string $signmethod 签名算法类型。支持hmacmd5，hmacsha1和hmacsha256，默认为hmacmd5
     */
    function signAliIot($config, $signmethod = "hmacmd5")
    {
        $sign_config = [
            'clientId' => $config['client_id'],
            'productKey' => $config['product_key'],
            'deviceName' => $config['device_name'],
        ];
        ksort($sign_config);
        $data = arr2str($sign_config);
        switch ($signmethod){
            case "hmacmd5":
                $sign = hash_hmac('md5', $data, $config['device_secret']);
                break;
            case "hmacsha1":
                $sign = hash_hmac('sha1', $data, $config['device_secret']);
                break;
            case "hmacsha256":
                $sign = hash_hmac('sha256', $data, $config['device_secret']);
                break;
        }
        return strtoupper($sign);
    }
}