<?php

use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Hire
{

    public static function getAllCategoryEmployTitleFromTable($status = null)
    {
        $response = sendResponse(0, "Error Msg", []);
        if (empty($status)) {
            $sql = "SELECT * FROM `tbl_employ_title` ORDER BY `category_id` DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_employ_title` WHERE `category_status`=:category_status ORDER BY category_id DESC ;";
            $params = [
                'category_status' => $status
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function setNewCategoryHire($title, $status)
    {
        $response = sendResponse(0, "", 0);

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $array = [];
        $array['admin'] = $admin_id;
        $array['date_create'] = time();
        $array['update'] = [];

        if ($admin_id > 0) {
            $sql = 'INSERT INTO `tbl_employ_title` (`category_name`, `category_status`, `category_options`) VALUES 
                    (:category_name,:category_status,:category_options)';
            $params = [
                'category_name' => $title,
                'category_status' => $status,
                'category_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }
        return $response;
    }


    public static function getCategoryHireById($id)
    {
        $response = sendResponse(0, "", []);
        $sql = "SELECT * FROM `tbl_employ_title` WHERE `category_id`=:category_id";
        $params = [
            'category_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }
        return $response;
    }


    public static function editCategoryHireById($id, $title, $status)
    {
        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCategoryHireById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->category_options;
        }

        if (!empty($temp)) {
            $value = json_decode($temp, true);
            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));
        }

        if ($admin_id > 0) {
            $sql = 'UPDATE `tbl_employ_title` SET `category_name`=:category_name,`category_status`=:category_status,`category_options`=:category_options
                    WHERE `category_id`=:category_id;';
            $params = [
                'category_name' => $title,
                'category_status' => $status,
                'category_options' => json_encode($value),
                'category_id' => $id,
            ];
            $result = DB::update($sql, $params);
            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    public static function getAllEmploy()
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_employ` ";
        $params = [];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function getEmployInfoById($id)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_employ` WHERE `employ_id`=:id ";
        $params = [
            'id' => $id
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response[0]);
        }
        return $response;
    }


    public static function getEmployTitle($id)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT category_id, category_name, category_status FROM `tbl_employ_title` WHERE `category_id` IN (" . $id . ") ";
        $params = [

        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, $id, $result->response);
        }
        return $response;
    }


    public static function updateEmployStatusByID($employID, $status)
    {
        $response = sendResponse(0, "Error Msg", []);

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $res = self::getEmployInfoById($employID)->response;
        $temp = [];
        if (!empty($res)) {
            $temp = $res->employ_options;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];

            $a['admin'] = $admin_id;
            $a['type'] = 'status';
            $a['old'] = $res->employ_status;
            $a['new'] = $status;
            $a['date'] = time();
            array_push($array, $a);

        } else {
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = 'status';
            $a['old'] = $res->employ_status;
            $a['new'] = $status;
            $a['date'] = time();
            array_push($array, $a);
        }


        $sql = "update `tbl_employ` set employ_status=:status , employ_options=:options WHERE `employ_id`=:id ";
        $params = [
            'status' => $status,
            'options' => json_encode($array),
            'id' => $employID,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }

    public static function deleteEmployByID($employID)
    {
        $response = sendResponse(0, "Error Msg", []);

        $sql = "DELETE FROM `tbl_employ` WHERE employ_id=:id";
        $params = [
            'id' => $employID,
        ];
        $result = DB::delete($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }

    public static function updateEmployDescByID($employID, $desc)
    {
        $response = sendResponse(0, "Error Msg", []);

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $res = self::getEmployInfoById($employID)->response;
        $temp = [];
        if (!empty($res)) {
            $temp = $res->employ_options;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];

            $a['admin'] = $admin_id;
            $a['type'] = 'desc';
            $a['old'] = null;
            $a['new'] = $desc;
            $a['date'] = time();
            array_push($array, $a);

        } else {
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = 'desc';
            $a['old'] = null;
            $a['new'] = $desc;
            $a['date'] = time();
            array_push($array, $a);
        }


        $sql = "update `tbl_employ` set employ_options=:options WHERE `employ_id`=:id ";
        $params = [
            'options' => json_encode($array),
            'id' => $employID,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    public static function countEmployStatus($status)
    {
        $response = 0;
        $sql = "SELECT count(*) AS count FROM `tbl_employ` WHERE `employ_status`=:employ_status";
        $params = [
            'employ_status' => $status,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->count;
        }
        return $response;
    }

    public static function setNewEmploy($token, $name, $lname, $father, $birthdayLocation, $birthdayTime, $codeNational,
                                        $gender, $military, $exemptionType, $marital, $countChild, $homeStatus,
                                        $insuranceStatus, $insuranceTime, $mobile, $phone, $addressLocation, $company,
                                        $eduName1, $eduName2, $eduName3, $eduName4, $eduName5, $eduAddress1,
                                        $eduAddress2, $eduAddress3, $eduAddress4, $eduAddress5, $language, $record, $work,
                                        $guarantee, $transfer, $price, $representativeName, $representativePhone, $representativeJob,
                                        $representativeAddress, $employ, $category, $liveLocationCountry,
                                        $liveLocationCity, $profile, $attachmentFiles)
    {
        if (!Security::verifyCSRF('employ', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('employ');

        if ($category == 0 || $category == '') {
            $category = null;
        }
        $sql = "INSERT INTO `tbl_employ` (`employ_title`,`employ_first_name`, `employ_last_name`, `employ_father_name`, `employ_birthday_location`, `employ_birthday_date`,
                         `employ_code_national`, `employ_gender`, `employ_military`, `employ_exemption_type`, `employ_marital`, `employ_count_child`,
                         `employ_home_status`, `employ_insurance_status`, `employ_insurance_date`, `employ_mobile`, `employ_phone`, `employ_address_location`,
                          `employ_country`,`employ_city`,
                         `employ_company`, `employ_edu_name_1`, `employ_edu_address_1`, `employ_edu_name_2`, `employ_edu_address_2`, `employ_edu_name_3`, 
                         `employ_edu_address_3`, `employ_edu_name_4`, `employ_edu_address_4`, `employ_edu_name_5`, `employ_edu_address_5`, `employ_language`,`employ_record`,
                         `employ_work`, `employ_guarantee`, `employ_transfer`, `employ_price`, `employ_representative_name`, `employ_representative_phone`, 
                         `employ_representative_job`, `employ_representative_address`, `employ_employ`,`employ_profile`,`employ_files`, `employ_status`, `employ_submit_date`) 
                         VALUES (:employ_title,:employ_first_name,:employ_last_name,:employ_father_name,:employ_birthday_location,:employ_birthday_date,
                                 :employ_code_national,:employ_gender,:employ_military,:employ_exemption_type,:employ_marital,:employ_count_child,
                                 :employ_home_status,:employ_insurance_status,:employ_insurance_date,:employ_mobile,:employ_phone,:employ_address_location,
                                 :employ_country,:employ_city,
                                 :employ_company,:employ_edu_name_1,:employ_edu_address_1,:employ_edu_name_2,:employ_edu_address_2,:employ_edu_name_3,
                                 :employ_edu_address_3,:employ_edu_name_4,:employ_edu_address_4,:employ_edu_name_5,:employ_edu_address_5,:employ_language,:employ_record,
                                 :employ_work,:employ_guarantee,:employ_transfer,:employ_price,:employ_representative_name,:employ_representative_phone,
                                 :employ_representative_job,:employ_representative_address,:employ_employ,:employ_profile,:employ_files,:employ_status,:employ_submit_date)";
        $params = [
            'employ_title' => $category,
            'employ_first_name' => $name,
            'employ_last_name' => $lname,
            'employ_father_name' => $father,
            'employ_birthday_location' => $birthdayLocation,
            'employ_birthday_date' => $birthdayTime,
            'employ_code_national' => $codeNational,
            'employ_gender' => $gender,
            'employ_military' => $military,
            'employ_exemption_type' => $exemptionType,
            'employ_marital' => $marital,
            'employ_count_child' => (int)$countChild,
            'employ_home_status' => $homeStatus,
            'employ_insurance_status' => $insuranceStatus,
            'employ_insurance_date' => $insuranceTime,
            'employ_mobile' => $mobile,
            'employ_phone' => $phone,
            'employ_address_location' => $addressLocation,
            'employ_country' => $liveLocationCountry,
            'employ_city' => $liveLocationCity,
            'employ_company' => $company,
            'employ_edu_name_1' => $eduName1,
            'employ_edu_address_1' => $eduAddress1,
            'employ_edu_name_2' => $eduName2,
            'employ_edu_address_2' => $eduAddress2,
            'employ_edu_name_3' => $eduName3,
            'employ_edu_address_3' => $eduAddress3,
            'employ_edu_name_4' => $eduName4,
            'employ_edu_address_4' => $eduAddress4,
            'employ_edu_name_5' => $eduName5,
            'employ_edu_address_5' => $eduAddress5,
            'employ_language' => $language,
            'employ_record' => $record,
            'employ_work' => $work,
            'employ_guarantee' => $guarantee,
            'employ_transfer' => $transfer,
            'employ_price' => $price,
            'employ_representative_name' => $representativeName,
            'employ_representative_phone' => $representativePhone,
            'employ_representative_job' => $representativeJob,
            'employ_representative_address' => $representativeAddress,
            'employ_employ' => $employ,
            'employ_profile' => $profile,
            'employ_files' => json_encode($attachmentFiles),
            'employ_status' => 'pending',
            'employ_submit_date' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'successfully', $csrf);
        } else {
            $response = sendResponse(-10, $sql, $csrf);
        }
        return $response;
    }

    public static function getHireTitle()
    {
        $response = sendResponse(-10, 'Error', []);
        $sql = "SELECT * From `tbl_employ_title` WHERE `category_status`=:status ";
        $params = [
            'status' => 'active',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(-10, 'Successful', $result->response);
        }
        return $response;
    }


    public static function getHireTitleAll()
    {
        $response = sendResponse(-10, 'Error', []);
        $sql = "SELECT * From `tbl_employ_title` ";
        $params = [];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(-10, 'Successful', $result->response);
        }
        return $response;
    }


    public static function getHireBycodeNational($code)
    {
        $response = 200;
        $sql = "SELECT * From `tbl_employ` WHERE `employ_code_national`=:code AND `employ_status`=:status";
        $params = [
            'code' => $code,
            'status' => 'pending',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = -1;
        }
        return $response;
    }

    public static function editHireCountryCity($hire_id ,$country, $city)
    {
        $response = sendResponse(0, "Error Msg", []);


        $sql = "update `tbl_employ` set tbl_employ.employ_country=:country   , tbl_employ.employ_city = :city  WHERE `employ_id`=:id ";
        $params = [
            'country' => $country,
            'city' => $city,
            'id' => $hire_id,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }
}