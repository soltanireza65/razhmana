<?php

use Ghasedak\GhasedakApi;
use MJ\Payment\Jibit;
use MJ\Security\Security;
use MJ\SMS\Ghasedak;
use MJ\SMS\SMS;
use MJ\Utils\Utils;
use MJ\Database\DB;
use function MJ\Keys\sendResponse;
//setcookie('name', 'value', strtotime('1 year'), '/', null, false, true);
//print_r($_SESSION);
//echo "<br>";

//$result1 = Utils::setFileText("settings.txt", 'poster_expire_time', 30);

//$result1 = Utils::getFileValue("settings.txt");
//print_r(($result1));

//print_r(Security::decrypt('Q1VFQlg3QW03UEZRMlZ1WFMzRmQ0UT09'));

//$numbers=(Utils::getFileValue('settings.txt', 'ghasedak_sadmins_mobile'))?Utils::getFileValue('settings.txt', 'ghasesdak_admins_mobile'):'09361933944';

//print_r (explode(",",$numbers));
//$resultSettings = Utils::getFileValue("settings.txt",'ghasedak_price_low');
//var_dump($numbers);


    //$s=Admin::addNewSlugForRole('tickets_customs','yes','yes','yes','yes',3);
    //print_r($s);
//$s=Ghasedak::getCredit();

//global $lang;
//$file = SITE_ROOT . "/www.php" ;
//$item=[];
//foreach ($lang as $index=>$loop){
//    array_push($item,"'".$loop."'=>'".$index."',");
//}
//
//
//file_put_contents($file, ($item));
//$api = new GhasedakApi(Utils::getFileValue('settings.txt', 'ghasedak_api'));
//$result = $api->AccountInfo();
//$result=Admin::sendSMS('ad');
//print_r(Location::getCountryByNameLike('ایران'));
//echo time();
//$sql = 'SELECT * FROM `tbl_cities` ';
//$params = [
//];
//$result = DB::rawQuery($sql, $params);
////print_r($result)
//echo time();
//$rr=Jibit::generateToken();
//print_r(json_decode($rr)->accessToken);
//if(isset($rr->accessToken)){

//    print_r(Jibit::requestPayment(json_decode($rr)->accessToken,'698000'));
//}else{
//    echo 5555555;
//}
//print_r(str_replace(['=','and','or','&','|','>','<',',',';',":",'delete','insert','update','select','*','user_id',''],'',htmlspecialchars(stripslashes(strtolower('555 AND user_id=5 And 1==1')))));
//print_r(Utils::backupFiles('ddd','/views','/db'));
//print_r(Security::decrypt('TXA2SFY5MlRMYWJvR0s3UzExYjZYbCtEdWhsQ0dFT3VPWDZ1bXBESzdicGNTM3hHaCtaZldmcnF1SGY4aXlpV0JLZEVWc1BvdHVlVldvQjVWRXR3dXc9PQ%3D%3D'));

//$countriesData = Location::getAllCountriesFromLoginPage();
//print_r($countriesData);


//print_r(SMS::sendSMS(['09361933944'], 'سلاممممممممم'));

//$file = SITE_ROOT . "/db/poster.json";
//$a = [];
//if(!empty(file_get_contents($file))){
//    $array = json_decode(file_get_contents($file),true);
//}else{
//    $array = [];
//}
//
//
//$a['admin'] = 1;
//$a['status_old'] = 2;
//$a['status_new'] = 3;
//$a['poster_id'] = 4;
//$a['date'] = time();
//array_push($array, $a);
//
////$ttt=file_get_contents($file);
//
//file_put_contents($file,json_encode($array));

//print_r(SMS::sendSMS(['09142393101' ], 'notification',['مرتضی قاسم خانی']));

//print_r(Transactions::backToWalletByAdmin(169));
//$validation = User::loginValidSystem('+989361933944')->message;
//print_r(Transactions::transactionPosterExpertWallet(1,1,17,20));
//print_r($validation);







//$sql1= "select * from tbl_cargo";
//$params1 = [];
//$result1 = DB::rawQuery($sql1, $params1);
//
//foreach ($result1->response as $loop){
//    $sql = "UPDATE `tbl_cargo` SET `cargo_origin_id`=:cargo_origin_id,`cargo_origin_customs_id`=:cargo_origin_customs_id,
//                       `cargo_destination_id`=:cargo_destination_id,`cargo_destination_customs_id`=:cargo_destination_customs_id
//WHERE `cargo_id`=:cargo_id";
//    $params = [
//'cargo_origin_id'=> json_decode($loop->cargo_origin)->id,
//'cargo_origin_customs_id'=> json_decode($loop->cargo_customs_of_origin)->id,
//'cargo_destination_id'=> json_decode($loop->cargo_destination)->id,
//'cargo_destination_customs_id'=> json_decode($loop->cargo_destination_customs)->id,
//'cargo_id'=> $loop->cargo_id,
//    ];
//    $result = DB::update($sql, $params);
//    print_r($result->status);
//    echo "<br>";
//}




//$sql1= "select * from tbl_users";
//$params1 = [];
//$result1 = DB::rawQuery($sql1, $params1);
//
//foreach ($result1->response as $loop){
//    $sql = "UPDATE `tbl_users` SET `user_score`=:user_score,`user_rate`=:user_rate,`user_rate_count`=:user_rate_count,
//                       `user_gift`=:user_gift WHERE `user_id`=:user_id";
//    $params = [
//'user_score'=> json_decode($loop->user_options)->score,
//'user_rate'=> json_decode($loop->user_options)->rate,
//'user_rate_count'=> json_decode($loop->user_options)->rate_count,
//'user_gift'=> json_decode($loop->user_options)->gift,
//'user_id'=> $loop->user_id,
//    ];
//    $result = DB::update($sql, $params);
//    print_r($result->status);
//    echo "<br>";
//}











//$sql1= "select * from tbl_cars";
//$params1 = [];
//$result1 = DB::rawQuery($sql1, $params1);
//
//foreach ($result1->response as $loop){
//    $sql = "UPDATE `tbl_cars` SET `car_plaque`=:car_plaque WHERE `car_id`=:car_id";
//    $params = [
//'car_plaque'=> implode('-',json_decode($loop->car_plaque,true)),
//'car_id'=> $loop->car_id,
//    ];
//    $result = DB::update($sql, $params);
//    print_r($result->status);
//
//    echo "<br>";
//}



//print_r(Cargo::rejectedRequestsAfterCargoExpired(164));




//print_r(Admin::addAdminPermissionAfterSlug(80,2,'yes','yes','yes','yes'));
//print_r(Admin::addAdminPermissionAfterSlug(80,4,'yes','yes','yes','yes'));
//print_r(Admin::addAdminPermissionAfterSlug(80,5,'yes','yes','yes','yes'));

//print_r(Admin::addAdminPermissionAfterSlug(79,2,'no','no','no','no'));
//print_r(Admin::addAdminPermissionAfterSlug(79,4,'no','no','no','no'));
//print_r(Admin::addAdminPermissionAfterSlug(79,5,'no','no','no','no'));
//print_r(Admin::addAdminPermissionAfterSlug(79,6,'no','no','no','no'));

//$test=InquiryJibit::matchPhoneAndNationalCode('+989361933944','1361689315');
//if(isset( json_decode($test)->matched )){
//    if(json_decode($test)->matched){
//        echo "true";
//    }else{
//        echo "غلطه";
//    }
//}else{
//    if(@json_decode($test)->message && json_decode($test)->message=="کدملی نامعتبر است"){
//        echo "کدملی نامعتبر است";
//    }else{
//        echo "بعدا تلاش کنید";
//    }
//}
//print_r(json_decode($test)->matched);












//    $sql = "select * from tbl_users ";
//    $params = [
//    ];
//    $result = DB::rawQuery($sql, $params);
//    foreach ($result->response as $loop){
//
//        $sql0 = "UPDATE `tbl_users` SET `user_unique_code`=:user_unique_code WHERE `user_id`=:user_id";
//        $params0 = [
//            'user_unique_code'=> User::generateReferralCode(),
//            'user_id'=>$loop->user_id,
//        ];
//        $result0 = DB::update($sql0, $params0);
//print_r($result0);
//echo "<br>";
//    }


