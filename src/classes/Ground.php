<?php


use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Ground
{

    /**
     * get Count Customs From Chart
     * @return stdClass
     */
    public static function getCountCustomsFromChart()
    {

        $sql = "SELECT COUNT(*) AS countAll,
                    (SELECT COUNT(*) FROM tbl_customs WHERE tbl_customs.customs_status=:status_inactive) AS countInactive,
                    (SELECT COUNT(*) FROM tbl_customs WHERE tbl_customs.customs_status=:status_active) AS countActive
                    FROM tbl_customs";
        $params = [
            'status_active' => "active",
            'status_inactive' => "inactive",
        ];


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            return [
                'all' => $result->response[0]->countAll,
                'active' => $result->response[0]->countActive,
                'inactive' => $result->response[0]->countInactive,
            ];
        }

        return [
            'all' => 0,
            'active' => 0,
            'inactive' => 0,
        ];
    }


    /**
     * get All Customs
     * @return stdClass
     */
    public static function getAllActiveCustoms()
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT customs_id,  customs_name FROM `tbl_customs` ORDER BY `customs_id` DESC ;";
        $params = [];


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Set New Customs
     * @param $title
     * @param $city
     * @param $status
     * @param $priority
     * @return stdClass
     */
    public static function setNewCustoms($title, $city, $status, $priority)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $array = [];
        $array['admin'] = $admin_id;
        $array['date_create'] = time();
        $array['update'] = [];

        if ($admin_id > 0) {

            $sql = 'INSERT INTO `tbl_customs`(`city_id`, `customs_name`, `customs_status`,`customs_priority`, `customs_options`) VALUES 
                    (:city_id,:customs_name,:customs_status,:customs_priority,:customs_options)';
            $params = [
                'city_id' => $city,
                'customs_name' => $title,
                'customs_status' => $status,
                'customs_priority' => $priority,
                'customs_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }

        return $response;
    }


    /**
     * get Customs All Info By Id
     * @param $id
     * @return stdClass
     */
    public static function getCustomsInfoById($id)
    {
        $response = sendResponse(0, "",[]);

        $sql = "SELECT * FROM `tbl_customs` INNER JOIN tbl_cities ON tbl_customs.city_id=tbl_cities.city_id INNER JOIN tbl_country
                ON tbl_country.country_id=tbl_cities.country_id  WHERE `customs_id`=:customs_id";
        $params = [
            'customs_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Get Customs Info By Id
     * @param $id int
     * @return Object
     * @author Tjavan
     * @version 3.0.0
     */
    public static function getCustomsById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_customs` WHERE `customs_id`=:customs_id";
        $params = [
            'customs_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * edit Customs By Id
     * @param $id
     * @param $title
     * @param $city
     * @param $status
     * @param $priority
     * @return stdClass
     */
    public static function editCustomsById($id, $title, $city, $status, $priority)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCustomsById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->customs_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            $sql = 'UPDATE `tbl_customs` SET `customs_name`=:customs_name,`city_id`=:city_id,`customs_status`=:customs_status,
                          `customs_priority`=:customs_priority,`customs_options`=:customs_options WHERE `customs_id`=:customs_id;';
            $params = [
                'customs_name' => $title,
                'city_id' => $city,
                'customs_status' => $status,
                'customs_priority' => $priority,
                'customs_options' => json_encode($value),
                'customs_id' => $id,
            ];
            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    ///////////////////// inquiry Ground from admin


    /**
     * get All Inquiry Ground By Status
     * @param $status
     * @return stdClass
     */
    public static function getAllInquiryGroundByStatus($status)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT freight_id,user_mobile,freight_submit_date,user_language,user_firstname,user_lastname,user_avatar FROM `tbl_freight_ground` INNER JOIN tbl_users ON tbl_users.user_id=tbl_freight_ground.user_id WHERE freight_status=:freight_status;";
        $params = [
            'freight_status' => $status
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * @param $countryId
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getCustomsByCountry($countryId)
    {
        $response = sendResponse(200, '', []);

        $sql = "select *
        from tbl_customs
        inner join tbl_cities on tbl_customs.city_id = tbl_cities.city_id
        where tbl_cities.country_id = :countryId and customs_status = :status;";
        $params = [
            'countryId' => $countryId,
            'status' => 'active'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $customsList = [];
            foreach ($result->response as $item) {
                $custom = new stdClass();
                $custom->city_id = $item->customs_id;
                $custom->city_name = $item->customs_name;

                array_push($customsList, $custom);
            }
            $response = sendResponse(200, '', $customsList);
        }
        return $response;
    }


    /**
     * delete Customs By Id
     * @param $customsId
     * @return stdClass
     */
    public static function deleteCustoms($customsId)
    {
        $response = sendResponse(200, '');
        $sql = 'DELETE FROM `tbl_customs` WHERE `customs_id`=:customs_id';
        $params = [
            'customs_id' => $customsId,
        ];
        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    ///////////////////// inquiry Ground from admin

    public static function getInquiryInfoById($id)
    {
        $response = sendResponse(200, '');
        $sql = 'SELECT * FROM `tbl_freight_ground` WHERE `freight_id`=:freight_id';
        $params = [
            'freight_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * get Multi Customs By customs Id
     * @param $customsId
     * @return stdClass
     */
    public static function getMultiCityAndCustomsId($customsId)
    {
        $response = sendResponse(200, '');
        $sql = "SELECT tbl_customs.customs_id, city_id, customs_name, customs_status FROM `tbl_customs` WHERE `customs_id` IN  ({$customsId})";
        $params = [];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * get Cargo Category Some Values
     * @return stdClass
     */
    public static function getCargoCategory()
    {
        $response = sendResponse(200, '');
        $sql = 'SELECT category_id, category_name,category_status FROM `tbl_cargo_categories`';
        $params = [];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * get Cars Types Some Values
     * @return stdClass
     */
    public static function getCarsTypes()
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT type_id, type_name, type_status FROM `tbl_car_types`";
        $params = [];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * edit Inquiry Ground Info By Admin
     * @param $inquiryId
     * @param $type
     * @param $newValue
     * @return stdClass
     */
    public static function editInquiryGroundInfoByAdmin($inquiryId, $type, $newValue)
    {

        $response = sendResponse(0, "Error Msg");

        $sql = 'UPDATE `tbl_freight_ground` SET ' . $type . '=:newValue WHERE `freight_id`=:freight_id';
        $params = [
            'newValue' => $newValue,
            'freight_id' => $inquiryId,
        ];


        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    /**
     * Add Description From Inquiry Ground
     * @param $inquiryId
     * @param $type
     * @param $newValue
     * @return stdClass
     */
    public static function addInquiryGroundInfoByAdmin($inquiryId, $type, $newValue)
    {

        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $resultDefault = self::getInquiryInfoById($inquiryId);
        $temp = [];
        if ($resultDefault->status == 200 && !empty($resultDefault->response) && isset($resultDefault->response[0])) {
            $temp = $resultDefault->response[0]->freight_options;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = $type;
            $a['value'] = $newValue;
            $a['date'] = time();
            array_push($array['update'], $a);

        } else {
            $a = [];
            $array['update'] = [];

            $a['admin'] = $admin_id;
            $a['type'] = $type;
            $a['value'] = $newValue;
            $a['date'] = time();
            array_push($array['update'], $a);
        }

        $sql = 'UPDATE `tbl_freight_ground` SET `freight_options`=:freight_options WHERE `freight_id`=:freight_id';
        $params = [
            'freight_options' => json_encode($array),
            'freight_id' => $inquiryId,
        ];


        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    /**
     * change Inquiry Ground Status By Admin
     * @param $inquiryId
     * @param $status
     * @return stdClass
     */
    public static function changeInquiryGroundStatusByAdmin($inquiryId, $status)
    {
        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $resultDefault = self::getInquiryInfoById($inquiryId);
        $temp = [];
        $statusDefault = '';
        if ($resultDefault->status == 200 && !empty($resultDefault->response) && isset($resultDefault->response[0])) {
            $temp = $resultDefault->response[0]->freight_options;
            $statusDefault = $resultDefault->response[0]->freight_status;
        }

        if (($statusDefault == "pending" && $status == "process") || ($statusDefault == "process" && $status == "completed")) {
            $array = [];
            if (!empty($temp)) {
                $array = json_decode($temp, true);
                $a = [];
                $a['admin'] = $admin_id;
                $a['type'] = 'status';
                $a['value'] = $status;
                $a['date'] = time();
                array_push($array['update'], $a);

            } else {
                $a = [];
                $array['update'] = [];

                $a['admin'] = $admin_id;
                $a['type'] = 'status';
                $a['value'] = $status;
                $a['date'] = time();
                array_push($array['update'], $a);
            }

            $sql = 'UPDATE `tbl_freight_ground` SET `freight_status`=:freight_status,`freight_options`=:freight_options WHERE `freight_id`=:freight_id';
            $params = [
                'freight_options' => json_encode($array),
                'freight_status' => $status,
                'freight_id' => $inquiryId,
            ];


            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    /**
     * get Customs By Country ID
     * @param $CountryId
     * @return stdClass
     */
    public static function getCustomsByCountryID($CountryId)
    {
        $response = sendResponse(0, "Error Msg");


        $sql = "SELECT tbl_customs.customs_id,tbl_customs.customs_name,tbl_customs.city_id FROM `tbl_customs` 
                INNER JOIN `tbl_cities` ON tbl_customs.city_id=tbl_cities.city_id
                INNER JOIN `tbl_country` ON tbl_country.country_id=tbl_cities.country_id WHERE tbl_cities.city_status_ground=:city_status_ground AND  tbl_country.country_id=:country_id AND tbl_customs.customs_status=:customs_status";
        $params = [
            'country_id' => $CountryId,
            'customs_status' => 'active',
            'city_status_ground' => 'yes',
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * change Inquiry Ground Location By Admin
     * @param $inquiryId
     * @param $country
     * @param $city
     * @param $customs
     * @param $type
     * @return stdClass
     */
    public static function changeInquiryGroundLocationByAdmin($inquiryId, $country, $city, $customs, $type)
    {
        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $resultDefault = self::getInquiryInfoById($inquiryId);
        $temp = [];
        if ($resultDefault->status == 200 && !empty($resultDefault->response) && isset($resultDefault->response[0])) {
            $temp = $resultDefault->response[0]->freight_options;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = 'location';
            $a['value'] = 'change_location_' . $type;
            $a['date'] = time();
            array_push($array['update'], $a);

        } else {
            $a = [];
            $array['update'] = [];

            $a['admin'] = $admin_id;
            $a['type'] = 'location';
            $a['value'] = 'change_location_' . $type;
            $a['date'] = time();
            array_push($array['update'], $a);
        }

        if ($type == "source") {

            $sql = 'UPDATE `tbl_freight_ground` SET `source_city_id`=:source_city_id,`source_customs_id`=:source_customs_id,
                    `freight_options`=:freight_options WHERE `freight_id`=:freight_id';
            $params = [
                'freight_options' => json_encode($array),
                'source_city_id' => $city,
                'source_customs_id' => $customs,
                'freight_id' => $inquiryId,
            ];

        } else {
            $sql = 'UPDATE `tbl_freight_ground` SET `dest_city_id`=:dest_city_id ,`dest_customs_id`=:dest_customs_id,
                   `freight_options`=:freight_options WHERE `freight_id`=:freight_id';
            $params = [
                'freight_options' => json_encode($array),
                'dest_city_id' => $city,
                'dest_customs_id' => $customs,
                'freight_id' => $inquiryId,
            ];
        }


        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }

    /**
     * @author morteza
     * @version 1.0.0
     * start inquiry
     */

    /**
     * @param $userId
     * @param $name
     * @param $categoryId
     * @param $carType
     * @param $startDate
     * @param $weight
     * @param $volume
     * @param $origin
     * @param $customsOfOrigin
     * @param $destination
     * @param $destinationCustoms
     * @param $description
     * @param $token
     *
     * @return \stdClass
     */
    public static function inquiryGroundInsert($userId, $name, $categoryId, $carType, $startDate, $weight, $volume, $origin, $customsOfOrigin, $destination, $destinationCustoms, $description, $token)
    {
        if (!Security::verifyCSRF('inquiry-ground', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('inquiry-ground');

        $sql = "insert into tbl_freight_ground(`user_id`, `source_city_id`, `dest_city_id`, `category_id`, `car_type_id`, `freight_name`,
                               `source_customs_id`, `dest_customs_id`, `freight_description`, `freight_wieght`, `freight_volume`,
                               `freight_start_date`, `freight_status`, `freight_submit_date`)values (:user_id ,
                                :source_city_id ,  :dest_city_id ,  :category_id ,  :car_type_id ,  :freight_name ,  :source_customs_id ,
                                :dest_customs_id ,  :freight_description ,  :freight_wieght ,  :freight_volume ,  :freight_start_date ,
                                :freight_status  ,  :freight_submit_date)";
        $params = [
            'user_id' => $userId,
            'source_city_id' => $origin,
            'dest_city_id' => $destination,
            'category_id' => $categoryId,
            'car_type_id' => $carType,
            'freight_name' => $name,
            'source_customs_id' => $customsOfOrigin,
            'dest_customs_id' => $destinationCustoms,
            'freight_description' => $description,
            'freight_wieght' => $weight,
            'freight_volume' => $volume,
            'freight_start_date' => $startDate,
            'freight_status' => 'pending',
            'freight_submit_date' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Cargo submitted successfully');
            User::createUserLog($userId, 'uLog_submit_cargo', 'cargo');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }


    /**
     * get Count Inquiry Ground By Status
     * @param $status
     * @return stdClass
     */
    public static function getCountInquiryGroundByStatus($status)
    {
        $response = sendResponse(0, "Error Msg", 0);
        $sql = "SELECT count(*) AS count FROM `tbl_freight_ground` WHERE freight_status=:freight_status;";
        $params = [
            'freight_status' => $status
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $count = 0;
            if (isset($result->response[0]->count)) {
                $count = (int)$result->response[0]->count;
            }
            $response = sendResponse(200, "Successful", $count);
        }

        return $response;
    }




    public static function getAllCustomSomeValues()
    {
        $response = sendResponse(0, "Error Msg",[]);
            $sql = "SELECT customs_id,customs_name,city_id FROM `tbl_customs` ORDER BY customs_id DESC ;";
            $params = [];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function getAllCargoCategoryByStatus($status = 'active')
    {
        $response = sendResponse(0, "Error Msg",[]);
        $sql = "SELECT * FROM `tbl_cargo_categories`  where category_status  = :status ";
        $params = [
            'status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

}