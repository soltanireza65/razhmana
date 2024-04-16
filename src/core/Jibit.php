<?php


namespace MJ\Payment;


use MJ\Utils\Utils;

class Jibit
{
    private static $ideURL = 'https://napi.jibit.ir/ide';
    private static $ppgURL = 'https://napi.jibit.ir/ppg';
    private static $apiKey = '';
    private static $secretKey = '';


    public static function init()
    {
        self::$apiKey = 'W6HLqqtums';
        self::$secretKey = 'i7BwXPEDSWftfJLFBdniRzsWCMEYUg7Fo3_8HeJ6q5Rksx2KSz';
    }


    public static function generateToken()
    {
        $headers = [
            'Content-Type: application/json'
        ];

        $params = [
            'apiKey' => self::$apiKey,
            'secretKey' => self::$secretKey
        ];

        $result = self::runCURL(self::$ppgURL, 'v3/tokens', $params, $headers, true);

//        Utils::setFileText('settings.txt', 'jibit_access_token', '');
//        Utils::setFileText('settings.txt', 'jibit_refresh_token', '');

        return $result;
    }


    public static function refreshToken()
    {
        $headers = [
            'Content-Type: application/json'
        ];

        $params = [
            'accessToken' => Utils::getFileValue('settings.txt', 'jibit_access_token'),
            'refreshToken' => Utils::getFileValue('settings.txt', 'jibit_refresh_token')
        ];

        $result = self::runCURL('v1/tokens/refresh', $params, $headers, true);

        Utils::setFileText('settings.txt', 'jibit_access_token', '');
        Utils::setFileText('settings.txt', 'jibit_refresh_token', '');

        return $result;
    }


    public static function ibanInquiryService($iban, $name)
    {
        $headers = [
            'Authorization: Bearer accessToken ' . Utils::getFileValue('settings.txt', 'jibit_access_token')
        ];

        $params = [
            'iban' => $iban,
            'name' => $name,
        ];

        $result = self::runCURL('v1/services/matching', $params, $headers);

        return $result;
    }


    public static function requestPayment($token,$amount,$currency="IRR")
    {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ];

        $params = [
            'amount' => $amount,
            'currency' => $currency,
            "expiresInMinutes" => 600,
            'clientReferenceNumber' => 'ntirapp-'.rand(0,100000000),
            'callbackUrl' => 'https://ntirapp.com/purchases/ntirapp-www/callback',
        ];

        $result = self::runCURL(self::$ppgURL, 'v3/purchases', $params, $headers, true);

        return $result;
    }


    public static function paymentVerify($purchaseId)
    {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJFenprRSIsImF1ZCI6InBwZyIsImFjY2VzcyI6dHJ1ZSwiaXNzIjoiaHR0cHM6Ly9qaWJpdC5pciIsImV4cCI6MTY2Mjk4MTQyNCwiaWF0IjoxNjYyODk1MDI0fQ.vXJlhAQakamWbK3XEzdoN_nnNyUwLQWaHqB7fuUYW5n2p7qPWrZZvgDoWG95JZCtc3qr__9U5t1nd_xVcQiH7w'
        ];

        $params = [];

        $result = self::runCURL(self::$ppgURL, "/purchases/{$purchaseId}/verify", $params, $headers, false);

        return $result;
    }


    public static function getOrderById($orderId)
    {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJzdWIiOiJFenprRSIsImF1ZCI6InBwZyIsImFjY2VzcyI6dHJ1ZSwiaXNzIjoiaHR0cHM6Ly9qaWJpdC5pciIsImV4cCI6MTY2Mjk4MTQyNCwiaWF0IjoxNjYyODk1MDI0fQ.vXJlhAQakamWbK3XEzdoN_nnNyUwLQWaHqB7fuUYW5n2p7qPWrZZvgDoWG95JZCtc3qr__9U5t1nd_xVcQiH7w'
        ];

        $params = [];

        $result = self::runCURL(self::$ppgURL, "/purchases?purchaseId={$orderId}", $params, $headers, false);

        return $result;
    }


    private static function runCURL($base, $path, $params = [], $headers = [], $isPost = false)
    {
        $curl = curl_init($base . "/{$path}");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, $isPost);
        if (!empty($params)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        }
        $result = curl_exec($curl);
        return $result;
    }
}

Jibit::init();