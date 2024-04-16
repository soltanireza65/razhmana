<?php

use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class CV
{

    public static function getUserCvCount($user_id)
    {
        $response = 0;

        $sql = "SELECT COUNT(*) as count FROM `tbl_driver_cv` WHERE user_id = :user_id";
        $params = [
            'user_id' => $user_id,

        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->count;
        }
        return $response;
    }

    public static function getCvDetailById($cv_id)
    {
        $sql = "SELECT * FROM `tbl_driver_cv`
                inner join tbl_cities  on tbl_driver_cv.city_id = tbl_cities.city_id
                WHERE tbl_driver_cv.cv_id  = :cv_id limit 1";
        $params = [
            'cv_id' => $cv_id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, '', $result->response);
        } else {
            $response = sendResponse(0, '');
        }
        return $response;
    }

    public static function getCvDetailByUserId($user_id)
    {
        $sql = "SELECT * FROM `tbl_driver_cv`
                inner join tbl_cities  on tbl_driver_cv.city_id = tbl_cities.city_id
                WHERE tbl_driver_cv.user_id  = :user_id limit 1";
        $params = [
            'user_id' => $user_id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            foreach ($result->response as $row) {
                $row->cv_visa_location = explode(',' ,$row->cv_visa_location);
                $row->cv_faviroite_country  = explode(',' ,$row->cv_faviroite_country );
            }
            $response = sendResponse(200, '', $result->response);
        } else {
            $response = sendResponse(0, '',);
        }
        return $response;
    }

    public static function getCvLists($search_text, $page, $fav_countries, $status, $visa_locations)
    {

        $page = $page == 1 ? 10 : $page * 10;

        $sql = "
        SELECT *
        FROM tbl_driver_cv
        inner join tbl_cities on tbl_driver_cv.city_id = tbl_cities.city_id
        WHERE tbl_driver_cv.cv_status = 'accepted'   
    ";

        $conditions = array();

        if (!empty($visa_locations)) {
            $sql .= " and ";
            $visa_conditions = array();
            foreach ($visa_locations as $location) {
                $visa_conditions[] = "cv_visa_location IS NULL OR cv_visa_location = $location";
            }
            $conditions[] = "(" . implode(" and ", $visa_conditions) . ")";
        }

        if (!empty($fav_countries)) {
            $sql .= " and ";
            $country_conditions = array();
            foreach ($fav_countries as $country) {
                $country_conditions[] = "cv_faviroite_country IS NULL OR cv_faviroite_country = $country";
            }
            $conditions[] = "(" . implode(" and ", $country_conditions) . ")";
        }

        $sql .= implode(" or ", $conditions);

        if ($search_text != 'no-search-data') {
            $sql .= " and  concat(tbl_driver_cv.cv_name , tbl_driver_cv.cv_lname) like  '%$search_text%'  ";
        }

        if ($status == 'active') {
            $sql .= " and tbl_driver_cv.cv_role_status = 'no'";
        } elseif ($status == 'inactive') {
            $sql .= " and tbl_driver_cv.cv_role_status = 'yes'";
        }
        $params = [];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = $result->response;
        } else {
            $response = [];
        }
//        echo $sql;
        return $response;
    }

    public static function insertDriverCV($city_id, $cv_name, $cv_lname, $cv_brith_date, $cv_gender, $cv_marital_status, $cv_military_status,
                                          $cv_military_image, $cv_military_number, $cv_military_date, $cv_smartcard_status, $cv_smartcard_image, $cv_smartcard_number,
                                          $cv_smartcard_date, $cv_passport_status, $cv_passport_image, $cv_passport_number, $cv_passport_date, $cv_visa_status,
                                          $cv_visa_image, $cv_visa_number, $cv_visa_date, $cv_visa_location, $cv_workbook_status, $cv_workbook_image, $cv_workbook_number, $cv_workbook_date,
                                          $cv_driver_license_status, $cv_driver_license_image, $cv_driver_license_number, $cv_driver_license_date, $cv_mobile, $cv_whatsapp,
                                          $cv_address, $cv_faviroite_country, $cv_role_status, $cv_user_avatar)
    {


        $user_id = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
        $sql = "INSERT INTO tbl_driver_cv (user_id, city_id,cv_user_avatar ,cv_name, cv_lname, cv_brith_date, cv_gender, cv_marital_status, cv_military_status,
                           cv_military_image, cv_military_number, cv_military_date, cv_smartcard_status, cv_smartcard_image, cv_smartcard_number, 
                           cv_smartcard_date, cv_passport_status, cv_passport_image, cv_passport_number, cv_passport_date, cv_visa_status, cv_visa_image,
                           cv_visa_number, cv_visa_date,cv_visa_location, cv_workbook_status, cv_workbook_image, cv_workbook_number, cv_workbook_date, cv_driver_license_status,
                           cv_driver_license_image, cv_driver_license_number, cv_driver_license_date, cv_mobile, cv_whatsapp, cv_address, cv_faviroite_country, 
                           cv_role_status, cv_status, cv_submit_date) 
                  VALUES (:user_id, :city_id,:cv_user_avatar, :cv_name, :cv_lname, :cv_brith_date, :cv_gender, :cv_marital_status, :cv_military_status,
                          :cv_military_image, :cv_military_number, :cv_military_date, :cv_smartcard_status, :cv_smartcard_image, :cv_smartcard_number,
                          :cv_smartcard_date, :cv_passport_status, :cv_passport_image, :cv_passport_number, :cv_passport_date, :cv_visa_status, :cv_visa_image,
                          :cv_visa_number, :cv_visa_date,:cv_visa_location, :cv_workbook_status, :cv_workbook_image, :cv_workbook_number, :cv_workbook_date, :cv_driver_license_status, 
                          :cv_driver_license_image, :cv_driver_license_number, :cv_driver_license_date, :cv_mobile, :cv_whatsapp, :cv_address, :cv_faviroite_country,
                          :cv_role_status,:cv_status,:cv_submit_date)";

        $params = [
            "user_id" => $user_id,
            "city_id" => $city_id,
            "cv_user_avatar" => $cv_user_avatar,
            "cv_name" => $cv_name,
            "cv_lname" => $cv_lname,
            "cv_brith_date" => $cv_brith_date,
            "cv_gender" => $cv_gender,
            "cv_marital_status" => $cv_marital_status,
            "cv_military_status" => $cv_military_status,
            "cv_military_image" => json_encode($cv_military_image),
            "cv_military_number" => $cv_military_number,
            "cv_military_date" => $cv_military_date,
            "cv_smartcard_status" => $cv_smartcard_status,
            "cv_smartcard_image" => json_encode($cv_smartcard_image),
            "cv_smartcard_number" => $cv_smartcard_number,
            "cv_smartcard_date" => $cv_smartcard_date,
            "cv_passport_status" => $cv_passport_status,
            "cv_passport_image" => json_encode($cv_passport_image),
            "cv_passport_number" => $cv_passport_number,
            "cv_passport_date" => $cv_passport_date,
            "cv_visa_status" => $cv_visa_status,
            "cv_visa_image" => json_encode($cv_visa_image),
            "cv_visa_number" => $cv_visa_number,
            "cv_visa_date" => $cv_visa_date,
            "cv_visa_location" => implode(',', $cv_visa_location),
            "cv_workbook_status" => $cv_workbook_status,
            "cv_workbook_image" => json_encode($cv_workbook_image),
            "cv_workbook_number" => $cv_workbook_number,
            "cv_workbook_date" => $cv_workbook_date,
            "cv_driver_license_status" => $cv_driver_license_status,
            "cv_driver_license_image" => json_encode($cv_driver_license_image),
            "cv_driver_license_number" => $cv_driver_license_number,
            "cv_driver_license_date" => $cv_driver_license_date,
            "cv_mobile" => $cv_mobile,
            "cv_whatsapp" => $cv_whatsapp,
            "cv_address" => $cv_address,
            "cv_faviroite_country" => implode(',', $cv_faviroite_country),
            "cv_role_status" => $cv_role_status,
            "cv_status" => 'pending',
            "cv_submit_date" => time(),
        ];

        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'cv submitted successfully');
        } else {
            $response = sendResponse(0, 'error', $sql);
        }
        return $response;
    }

    public static function updateDriverCv($city_id, $cv_id, $cv_name, $cv_lname, $cv_brith_date, $cv_gender, $cv_marital_status, $cv_military_status,
                                          $cv_military_image, $cv_military_number, $cv_military_date, $cv_smartcard_status, $cv_smartcard_image, $cv_smartcard_number,
                                          $cv_smartcard_date, $cv_passport_status, $cv_passport_image, $cv_passport_number, $cv_passport_date, $cv_visa_status,
                                          $cv_visa_image, $cv_visa_number, $cv_visa_date, $cv_visa_location, $cv_workbook_status, $cv_workbook_image, $cv_workbook_number, $cv_workbook_date,
                                          $cv_driver_license_status, $cv_driver_license_image, $cv_driver_license_number, $cv_driver_license_date, $cv_mobile, $cv_whatsapp,
                                          $cv_address, $cv_faviroite_country, $cv_role_status, $cv_user_avatar)
    {


        $user_id = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
        $sql = "UPDATE tbl_driver_cv SET
    user_id = :user_id,
    city_id = :city_id,
    cv_user_avatar = :cv_user_avatar,
    cv_name = :cv_name,
    cv_lname = :cv_lname,
    cv_brith_date = :cv_brith_date,
    cv_gender = :cv_gender,
    cv_marital_status = :cv_marital_status,
    cv_military_status = :cv_military_status,
    cv_military_image = :cv_military_image,
    cv_military_number = :cv_military_number,
    cv_military_date = :cv_military_date,
    cv_smartcard_status = :cv_smartcard_status,
    cv_smartcard_image = :cv_smartcard_image,
    cv_smartcard_number = :cv_smartcard_number,
    cv_smartcard_date = :cv_smartcard_date,
    cv_passport_status = :cv_passport_status,
    cv_passport_image = :cv_passport_image,
    cv_passport_number = :cv_passport_number,
    cv_passport_date = :cv_passport_date,
    cv_visa_status = :cv_visa_status,
    cv_visa_image = :cv_visa_image,
    cv_visa_number = :cv_visa_number,
    cv_visa_date = :cv_visa_date,
    cv_visa_location = :cv_visa_location,
    cv_workbook_status = :cv_workbook_status,
    cv_workbook_image = :cv_workbook_image,
    cv_workbook_number = :cv_workbook_number,
    cv_workbook_date = :cv_workbook_date,
    cv_driver_license_status = :cv_driver_license_status,
    cv_driver_license_image = :cv_driver_license_image,
    cv_driver_license_number = :cv_driver_license_number,
    cv_driver_license_date = :cv_driver_license_date,
    cv_mobile = :cv_mobile,
    cv_whatsapp = :cv_whatsapp,
    cv_address = :cv_address,
    cv_faviroite_country = :cv_faviroite_country,
    cv_role_status = :cv_role_status,
    cv_status = :cv_status,
    cv_submit_date = :cv_submit_date
    where cv_id  = :cv_id";

        $params = [
            "user_id" => $user_id,
            "cv_id" => $cv_id,
            "city_id" => $city_id,
            "cv_user_avatar" => $cv_user_avatar,
            "cv_name" => $cv_name,
            "cv_lname" => $cv_lname,
            "cv_brith_date" => $cv_brith_date,
            "cv_gender" => $cv_gender,
            "cv_marital_status" => $cv_marital_status,
            "cv_military_status" => $cv_military_status,
            "cv_military_image" => json_encode($cv_military_image),
            "cv_military_number" => $cv_military_number,
            "cv_military_date" => $cv_military_date,
            "cv_smartcard_status" => $cv_smartcard_status,
            "cv_smartcard_image" => json_encode($cv_smartcard_image),
            "cv_smartcard_number" => $cv_smartcard_number,
            "cv_smartcard_date" => $cv_smartcard_date,
            "cv_passport_status" => $cv_passport_status,
            "cv_passport_image" => json_encode($cv_passport_image),
            "cv_passport_number" => $cv_passport_number,
            "cv_passport_date" => $cv_passport_date,
            "cv_visa_status" => $cv_visa_status,
            "cv_visa_image" => json_encode($cv_visa_image),
            "cv_visa_number" => $cv_visa_number,
            "cv_visa_date" => $cv_visa_date,
            "cv_visa_location" => implode(',', $cv_visa_location),
            "cv_workbook_status" => $cv_workbook_status,
            "cv_workbook_image" => json_encode($cv_workbook_image),
            "cv_workbook_number" => $cv_workbook_number,
            "cv_workbook_date" => $cv_workbook_date,
            "cv_driver_license_status" => $cv_driver_license_status,
            "cv_driver_license_image" => json_encode($cv_driver_license_image),
            "cv_driver_license_number" => $cv_driver_license_number,
            "cv_driver_license_date" => $cv_driver_license_date,
            "cv_mobile" => $cv_mobile,
            "cv_whatsapp" => $cv_whatsapp,
            "cv_address" => $cv_address,
            "cv_faviroite_country" => implode(',', $cv_faviroite_country),
            "cv_role_status" => $cv_role_status,
            "cv_status" => 'pending',
            "cv_submit_date" => time(),
        ];

        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'cv submitted successfully');
        } else {
            $response = sendResponse(0, 'error', $params);
        }
        return $response;
    }


    public static function updateCvStatus($cv_id, $status)
    {
        $response = sendResponse(0, "");

        $sql = "UPDATE `tbl_driver_cv` SET tbl_driver_cv.`cv_status`=:status WHERE tbl_driver_cv.cv_id=:cv_id";
        $params = [
            'status' => $status,
            'cv_id' => $cv_id,
        ];
        $result = DB::update($sql, $params);

        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, "");
        }

        return $response;
    }

    public static function rejectCv($cv_id, $status, $reject_desc)
    {
        $response = sendResponse(0, "");

        $sql = "UPDATE `tbl_driver_cv` SET tbl_driver_cv.`cv_status`=:status , tbl_driver_cv.rejected_desc = :rejected_desc  WHERE tbl_driver_cv.cv_id=:cv_id";
        $params = [
            'status' => $status,
            'cv_id' => $cv_id,
            'rejected_desc' => $reject_desc,
        ];
        $result = DB::update($sql, $params);

        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, "");
        }

        return $response;
    }

    public static function updateCvRoleStatus($cv_id, $status)
    {
        $response = sendResponse(0, "");

        $sql = "UPDATE `tbl_driver_cv` SET tbl_driver_cv.`cv_role_status`=:status WHERE tbl_driver_cv.cv_id=:cv_id";
        $params = [
            'status' => $status,
            'cv_id' => $cv_id,
        ];
        $result = DB::update($sql, $params);

        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    public static function getPersonelById($personel_ref_code)
    {
        $sql = "SELECT * FROM `tbl_personls_card` 
                WHERE tbl_personls_card.personel_ref_code  = :personel_ref_code  ";
        $params = [
            'personel_ref_code' => $personel_ref_code,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, '', $result->response);
        } else {
            $response = sendResponse(0, '',);
        }
        return $response;
    }

    public static function checkPersonelRefCodeExists($personel_ref_code)
    {
        $sql = "SELECT count(*) as register_flag FROM `tbl_personls_card` 
                WHERE tbl_personls_card.personel_ref_code  = :personel_ref_code  ";
        $params = [
            'personel_ref_code' => $personel_ref_code,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            return $result->response[0]->register_flag;
        } else {
            return 0;
        }

    }

    public static function addPersonels($personel_name_fa_IR, $personel_name_en_US, $personel_name_tr_Tr, $personel_name_ru_RU,
                                        $personel_lname_fa_IR, $personel_lname_en_US, $personel_lname_tr_Tr, $personel_lname_ru_RU,
                                        $personel_job_fa_IR, $personel_job_en_US, $personel_job_tr_Tr, $personel_job_ru_RU, $personel_email, $phone,
                                        $home_numer, $whatsapp, $phone_country_code, $home_country_code, $whatsapp_country_code, $personel_ref_code,
                                        $personel_desc_fa_IR, $personel_desc_en_US, $personel_desc_tr_Tr, $personel_desc_ru_RU, $personel_avatar_url)
    {

        $sql = "INSERT INTO tbl_personls_card (
                tbl_personls_card.personel_name_fa_IR,tbl_personls_card.personel_name_en_US,tbl_personls_card.personel_name_tr_Tr,tbl_personls_card.personel_name_ru_RU,
                tbl_personls_card.personel_lname_fa_IR,tbl_personls_card.personel_lname_en_US,tbl_personls_card.personel_lname_tr_Tr,tbl_personls_card.personel_lname_ru_RU,
                tbl_personls_card.personel_home_number, tbl_personls_card.personel_whatsapp, tbl_personls_card.personel_mobile, tbl_personls_card.personel_email, 
                tbl_personls_card.personel_ref_code,
                tbl_personls_card.personel_description_fa_IR,tbl_personls_card.personel_description_en_US,tbl_personls_card.personel_description_tr_Tr,tbl_personls_card.personel_description_ru_RU, 
                tbl_personls_card.personel_avatar,
                               tbl_personls_card.personel_side_fa_IR, tbl_personls_card.personel_side_en_US, tbl_personls_card.personel_side_tr_Tr, tbl_personls_card.personel_side_ru_RU, 
                               tbl_personls_card.personel_status, tbl_personls_card.personel_create_at,
                                   tbl_personls_card.personel_whatsapp_code,tbl_personls_card.personel_mobile_code,tbl_personls_card.personel_home_number_code
                ) values (
                :personel_name_fa_IR,:personel_name_en_US,:personel_name_tr_Tr,:personel_name_ru_RU, 
                          :personel_lname_fa_IR,:personel_lname_en_US,:personel_lname_tr_Tr,:personel_lname_ru_RU,
                          :personel_home_number, :personel_whatsapp,
                :personel_mobile, :personel_email, :personel_ref_code, :personel_description_fa_IR, :personel_description_en_US, :personel_description_tr_Tr, :personel_description_ru_RU, 
                :personel_avatar, :personel_side_fa_IR,:personel_side_en_US,:personel_side_tr_Tr,:personel_side_ru_RU, :personel_status, :personel_create_at
                , :personel_whatsapp_code,:personel_mobile_code,:personel_home_number_code
                )";

        $params = [
            "personel_name_fa_IR" => $personel_name_fa_IR,
            "personel_name_en_US" => $personel_name_en_US,
            "personel_name_tr_Tr" => $personel_name_tr_Tr,
            "personel_name_ru_RU" => $personel_name_ru_RU,
            "personel_lname_fa_IR" => $personel_lname_fa_IR,
            "personel_lname_en_US" => $personel_lname_en_US,
            "personel_lname_tr_Tr" => $personel_lname_tr_Tr,
            "personel_lname_ru_RU" => $personel_lname_ru_RU,
            "personel_home_number" => $home_numer,
            "personel_whatsapp" => $whatsapp,
            "personel_mobile" => $phone,
            "personel_email" => $personel_email,
            "personel_ref_code" => $personel_ref_code,
            "personel_description_fa_IR" => $personel_desc_fa_IR,
            "personel_description_en_US" => $personel_desc_en_US,
            "personel_description_tr_Tr" => $personel_desc_tr_Tr,
            "personel_description_ru_RU" => $personel_desc_ru_RU,
            "personel_avatar" => $personel_avatar_url,
            "personel_side_fa_IR" => $personel_job_fa_IR,
            "personel_side_en_US" => $personel_job_en_US,
            "personel_side_tr_Tr" => $personel_job_tr_Tr,
            "personel_side_ru_RU" => $personel_job_ru_RU,
            "personel_whatsapp_code" => $whatsapp_country_code,
            "personel_mobile_code" => $phone_country_code,
            "personel_home_number_code" => $home_country_code,
            "personel_status" => 'active',
            "personel_create_at" => time(),
        ];

        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'cv submitted successfully', $result->response);
        } else {
            $response = sendResponse(0, 'error', $sql);
        }
        return $response;


    }

    public static function editPersonels($personel_id, $personel_name_fa_IR, $personel_name_en_US, $personel_name_tr_Tr, $personel_name_ru_RU,
                                         $personel_lname_fa_IR, $personel_lname_en_US, $personel_lname_tr_Tr, $personel_lname_ru_RU, $personel_job_fa_IR, $personel_job_en_US,
                                         $personel_job_tr_Tr, $personel_job_ru_RU, $personel_email, $phone, $home_numer, $whatsapp, $phone_country_code, $home_country_code,
                                         $whatsapp_country_code, $personel_ref_code, $personel_desc_fa_IR, $personel_desc_en_US, $personel_desc_tr_Tr, $personel_desc_ru_RU,
                                         $personel_avatar_url)
    {

        $sql = "UPDATE tbl_personls_card
        SET
            personel_name_fa_IR = :personel_name_fa_IR,
            personel_name_en_US = :personel_name_en_US,
            personel_name_tr_Tr = :personel_name_tr_Tr,
            personel_name_ru_RU = :personel_name_ru_RU,
            personel_lname_fa_IR = :personel_lname_fa_IR,
            personel_lname_en_US = :personel_lname_en_US,
            personel_lname_tr_Tr = :personel_lname_tr_Tr,
            personel_lname_ru_RU = :personel_lname_ru_RU,
            personel_home_number = :personel_home_number,
            personel_whatsapp = :personel_whatsapp,
            personel_mobile = :personel_mobile,
            personel_email = :personel_email,
            personel_ref_code = :personel_ref_code,
            personel_description_fa_IR = :personel_description_fa_IR,
            personel_description_en_US = :personel_description_en_US,
            personel_description_tr_Tr = :personel_description_tr_Tr,
            personel_description_ru_RU = :personel_description_ru_RU,
            personel_avatar = :personel_avatar,
            personel_side_fa_IR = :personel_side_fa_IR,
            personel_side_en_US = :personel_side_en_US,
            personel_side_tr_Tr = :personel_side_tr_Tr,
            personel_side_ru_RU = :personel_side_ru_RU,
            personel_status = :personel_status,
            personel_create_at = :personel_create_at,
            personel_whatsapp_code = :personel_whatsapp_code,
            personel_mobile_code = :personel_mobile_code,
            personel_home_number_code = :personel_home_number_code
        WHERE   personel_ref_code = :personel_ref_code";

        $params = [
            "personel_name_fa_IR" => $personel_name_fa_IR,
            "personel_name_en_US" => $personel_name_en_US,
            "personel_name_tr_Tr" => $personel_name_tr_Tr,
            "personel_name_ru_RU" => $personel_name_ru_RU,
            "personel_lname_fa_IR" => $personel_lname_fa_IR,
            "personel_lname_en_US" => $personel_lname_en_US,
            "personel_lname_tr_Tr" => $personel_lname_tr_Tr,
            "personel_lname_ru_RU" => $personel_lname_ru_RU,
            "personel_home_number" => $home_numer,
            "personel_whatsapp" => $whatsapp,
            "personel_mobile" => $phone,
            "personel_email" => $personel_email,
            "personel_description_fa_IR" => $personel_desc_fa_IR,
            "personel_description_en_US" => $personel_desc_en_US,
            "personel_description_tr_Tr" => $personel_desc_tr_Tr,
            "personel_description_ru_RU" => $personel_desc_ru_RU,
            "personel_avatar" => $personel_avatar_url,
            "personel_side_fa_IR" => $personel_job_fa_IR,
            "personel_side_en_US" => $personel_job_en_US,
            "personel_side_tr_Tr" => $personel_job_tr_Tr,
            "personel_side_ru_RU" => $personel_job_ru_RU,
            "personel_whatsapp_code" => $whatsapp_country_code,
            "personel_mobile_code" => $phone_country_code,
            "personel_home_number_code" => $home_country_code,
            "personel_status" => 'active',
            "personel_create_at" => time(),
            "personel_ref_code" => $personel_ref_code,
        ];

        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'cv update successfully', $params);
        } else {
            $response = sendResponse(0, 'error', $sql);
        }
        return $response;


    }

    public static function countDriverService($status)
    {
        $response = 0;
        $sql = "SELECT count(*) AS count FROM `tbl_driver_cv`  WHERE `tbl_driver_cv`.cv_status=:status";
        $params = [
            'status' => $status,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->count;
        }
        return $response;
    }


    public static function deletePersonels($personel_id)
    {
        $sql = 'DELETE FROM `tbl_personls_card` WHERE personel_id = :personel_id';
        $params = [
            'personel_id' => $personel_id
        ];
        $result = DB::delete($sql, $params);
        if ($result->status == 200) {
            return sendResponse(200, 'success');
        } else {
            return sendResponse(0, 'unknow error', $result);
        }

    }
}