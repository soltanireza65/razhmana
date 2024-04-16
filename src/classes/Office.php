<?php

use MJ\Database\DB;
use MJ\Security\Security;
use MJ\SMS\SMS;
use MJ\Utils\Utils;
use function MJ\Keys\sendResponse;

class Office
{
    public static function insertOffice($mobile, $email, $city)
    {

        $response = sendResponse(0, "Error Msg");

        $password = Utils::generatePassword(8);
        $sql = "INSERT INTO `tbl_org_office`(office_mobile, office_email, office_referal_code, office_city, office_user_name, office_password, office_create_at, office_update_at, office_status)
                VALUES(:office_mobile, :office_email, :office_referal_code, :office_city, :office_user_name, :office_password, :office_create_at, :office_update_at, :office_status) 
                    ";
        $params = [
            'office_mobile' => $mobile,
            'office_email' => $email,
            'office_referal_code' => $mobile,
            'office_city' => $city,
            'office_user_name' => $mobile,
            'office_password' => $password,
            'office_create_at' => time(),
            'office_update_at' => time(),
            'office_status' => 'active',

        ];

        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            SMS::sendSMS([$mobile], "نام کاربری  : $mobile رمز عبور : $password    سامانه انتراپ");
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }

    public static function updateOffice($office_id, $mobile, $email, $city)
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "UPDATE `tbl_org_office` SET office_mobile = :office_mobile, office_email = :office_email, 
                            office_referal_code = :office_referal_code, office_city = :office_city, office_update_at = :office_update_at,
                            office_status = :office_status WHERE office_id = :office_id";
        $params = [
            'office_id' => $office_id,
            'office_mobile' => $mobile,
            'office_email' => $email,
            'office_referal_code' => $mobile,
            'office_city' => $city,
            'office_update_at' => time(),
            'office_status' => 'active',
        ];

        $result = DB::update($sql, $params);

        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }

    public static function getOfficeDetail($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_org_office`
         inner join tbl_cities on tbl_cities.city_id =tbl_org_office.office_city
         WHERE office_id=:office_id ";
        $params = [
            "office_id" => $id
        ];

        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response[0]);
        }

        return $response;


    }
}