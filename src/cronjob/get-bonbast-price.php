<?php
require_once 'autoload.php';

use MJ\Utils\Utils;
$arrContextOptions = array();
$data = array('hash' => 'fbfc3c76c92b60ac4080458e265ecfb5');
$opts = array('http' =>
    array(
        'method' => 'POST',
        'header' => "Content-type: application/x-www-form-urlencoded",
        'content' => http_build_query($data)
    ),
    "ssl" => array(
        "verify_peer" => false,
        "verify_peer_name" => false,
    ),
);

$context = stream_context_create($opts);

$content = file_get_contents('https://bonbast.net/api/ntirapp', false, $context);
$json = json_decode($content, true);


$output = [
    'USD_SELL' => $json['usd1'], // dollar
    'USD_BUY' => $json['usd2'], // dollar
    'EUR_SELL' => $json['eur1'], // euro
    'EUR_BUY' => $json['eur2'], // euro
    'AED_SELL' => $json['aed1'], // AED DIRHAM
    'AED_BUY' => $json['aed2'], // AED DIRHAM
    'CHF_SELL' => $json['chf1'], // frank swiss
    'CHF_BUY' => $json['chf2'], // frank swiss
    'TRY_SELL' => $json['try1'], // turkey lir
    'TRY_BUY' => $json['try2'], // turkey lir
    'RUB_SELL' => $json['rub1'], // russian rubl
    'RUB_BUY' => $json['rub2'], // russian rubl
];

echo json_encode($output);

$sql = 'UPDATE `tbl_live_price_bonbast` 
SET 
 `price_buy`=:price_buy,`price_sell`=:price_sell , `updated_at`=:updated_at WHERE tbl_live_price_bonbast.id = :id';

$params  =  [
    'price_buy'=> $json['usd2'],
    'price_sell'=>$json['usd1'],
    'updated_at'=>time(),
    'id'=>1
];
$result = \MJ\Database\DB::update($sql, $params);
$params  =  [
    'price_buy'=> $json['eur2'],
    'price_sell'=>$json['eur1'],
    'updated_at'=>time(),
    'id'=>2
];
$result = \MJ\Database\DB::update($sql, $params);
$params  =  [
    'price_buy'=> $json['aed2'],
    'price_sell'=>$json['aed1'],
    'updated_at'=>time(),
    'id'=>3
];
$result = \MJ\Database\DB::update($sql, $params);
$params  =  [
    'price_buy'=> $json['chf2'],
    'price_sell'=>$json['chf1'],
    'updated_at'=>time(),
    'id'=>4
];
$result = \MJ\Database\DB::update($sql, $params);
$params  =  [
    'price_buy'=> $json['try2'],
    'price_sell'=>$json['try1'],
    'updated_at'=>time(),
    'id'=>5
];
$result = \MJ\Database\DB::update($sql, $params);
$params  =  [
    'price_buy'=> $json['rub2'],
    'price_sell'=>$json['rub1'],
    'updated_at'=>time(),
    'id'=>6
];
$result = \MJ\Database\DB::update($sql, $params);