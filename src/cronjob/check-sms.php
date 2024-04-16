<?php
use MJ\Security\Security;
require_once 'autoload.php';

$api="d5a6d0803d0807dce66e1e0ecb9c6f1ebab027d9910c76ab9482b5437c1d944e";
$mobile="09148922383";
$template="mizansharj";
$price="700000";
$temp = json_decode(Security::decrypt(file_get_contents("public_html/db/settings.txt")));

foreach ($temp as $index => $loop) {
    if ($index == 'ghasedak_admins_mobile') {
        $mobile= $loop;
    }
    if ($index == 'ghasedak_price_low') {
        $price= $loop;
    }
    if ($index == 'ghasedak_template_low_price') {
        $template= $loop;
    }
    if ($index == 'ghasedak_api') {
        $api= $loop;
    }
}
$re=Admin::sendSmsLowPrice($api,$mobile,$template,$price);