<?php
require_once 'autoload.php';
use MJ\Utils\Utils;

$prices = json_decode(Utils::getUsdPrice())->response->indicators->data;
echo json_encode($prices);
foreach ($prices as $price) {
    $output = [] ;

    $output =  [
        "id" =>  $price->id,
        "item_id" =>  $price->item_id,
        "category_id" =>  $price->category_id,
        "category" =>  $price->category,
        "key" =>  $price->key,
        "title" =>  $price->title,
        "title_second" =>  $price->title_second,
        "name" =>  $price->name,
        "country" =>  $price->country,
        "currency" =>  $price->currency,
        "price" =>  $price->price,
        "open" =>  $price->open,
        "high" =>  $price->high,
        "low" =>  $price->low,
        "change" =>  $price->change,
        "change_percent" =>  $price->change_percent,
        "time" =>  $price->time,
        "status" =>  'active',
    ] ;
    $sql   =  "
INSERT INTO tbl_live_price 
    (`id`,`item_id`,`category_id`,`category`,`key`,`title`,`title_second`,`name`,`country`,`currency`,`price`,`open`,`high`,`low`,`change`,`change_percent`,`time` , `status`)
VALUES 
    (:id,:item_id,:category_id,:category,:key,:title,:title_second,:name,:country,:currency,:price,:open,:high,:low,:change,:change_percent,:time , :status)
ON DUPLICATE KEY UPDATE  
    `id`=:id,
    `item_id`=:item_id,
    `category_id`=:category_id,
    `category`=:category,
    `key`=:key,
    `title`=:title,
    `title_second`=:title_second,
    `name`=:name,
    `country`=:country,
    `currency`=:currency,
    `price`=:price,
    `open`=:open,
    `high`=:high,
    `low`=:low,
    `change`=:change,
    `change_percent`=:change_percent,
    `time`=:time
    ";
    $result = \MJ\Database\DB::update($sql , $output);
//    print_r($result);
    $filename = 'public_html/prices/' . $price->id . '.json';

    if (file_exists($filename)) {
        $existingData = file_get_contents($filename);
        $existingArray = json_decode($existingData, true);

        $existingArray[] = $output;

        file_put_contents($filename, json_encode($existingArray));
    } else {
        file_put_contents($filename, json_encode([$output]));
    }
}