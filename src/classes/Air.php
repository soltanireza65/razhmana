<?php


use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Air
{

    /**
     * get Count Airports From Chart
     *
     * @return stdClass
     */
    public static function getCountAirportsFromChart()
    {

        $sql = "SELECT COUNT(*) AS countAll,
                    (SELECT COUNT(*) FROM tbl_airports WHERE tbl_airports.airport_status=:status_inactive) AS countInactive,
                    (SELECT COUNT(*) FROM tbl_airports WHERE tbl_airports.airport_status=:status_active) AS countActive
                    FROM tbl_airports";
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
     * Set New AirPort
     *
     * @param $title
     * @param $city
     * @param $status
     * @param $priority
     *
     * @return stdClass
     */
    public static function setNewAirPort($title, $city, $status, $priority)
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

            $sql = 'INSERT INTO `tbl_airports`(`city_id`, `airport_name`, `airport_status`, `airport_options`,`airport_priority`) VALUES 
                    (:city_id,:airport_name,:airport_status,:airport_options,:airport_priority)';
            $params = [
                'airport_name' => $title,
                'city_id' => $city,
                'airport_status' => $status,
                'airport_priority' => $priority,
                'airport_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }

        return $response;
    }


    /**
     * Get AirPort By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getAirPortById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_airports` WHERE `airport_id`=:airport_id";
        $params = [
            'airport_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }

    /**
     * get Air Port Info By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getAirPortInfoById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_airports` INNER JOIN tbl_cities ON tbl_airports.city_id=tbl_cities.city_id INNER JOIN tbl_country
                ON tbl_country.country_id=tbl_cities.country_id  WHERE `airport_id`=:airport_id";
        $params = [
            'airport_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * edit AirPort By Id
     *
     * @param $id
     * @param $title
     * @param $city
     * @param $status
     * @param $priority
     *
     * @return stdClass
     */
    public static function editAirPortById($id, $title, $city, $status, $priority)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getAirPortById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->airport_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            $sql = 'UPDATE `tbl_airports` SET `airport_name`=:airport_name,`city_id`=:city_id,`airport_status`=:airport_status,
                          `airport_options`=:airport_options,`airport_priority`=:airport_priority WHERE `airport_id`=:airport_id;';
            $params = [
                'airport_name' => $title,
                'city_id' => $city,
                'airport_status' => $status,
                'airport_options' => json_encode($value),
                'airport_priority' => $priority,
                'airport_id' => $id,
            ];
            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    /**
     * delete Airport By Id
     *
     * @param $airportId
     *
     * @return stdClass
     */
    public static function deleteAirport($airportId)
    {
        $response = sendResponse(200, '');
        $sql = 'DELETE FROM `tbl_airports` WHERE `airport_id`=:airport_id';
        $params = [
            'airport_id' => $airportId,
        ];
        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }

    ////////////////////////////////////////// category


    /**
     * get All Category Air Cargo By Status
     *
     * @return stdClass
     */
    public static function getAllCategoryAirCargoFromTable()
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT category_id, category_name, category_status FROM `tbl_air_categories` ORDER BY `category_id` DESC ;";
        $params = [];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * get All Category Air Cargo By Status
     *
     * @param null $status
     *
     * @return stdClass
     */
    public static function getAllCategoryAirCargo($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT category_id, category_name, category_status FROM `tbl_air_categories` ORDER BY `category_id` DESC ;";
            $params = [];
        } else {
            $sql = "SELECT category_id, category_name, category_status FROM `tbl_air_categories` WHERE `category_status`=:category_status ORDER BY category_id DESC ;";
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


    /**
     * set New Category Air Cargo
     *
     * @param $title
     * @param $status
     *
     * @return stdClass
     */
    public static function setNewCategoryAirCargo($title, $status)
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

            $sql = 'INSERT INTO `tbl_air_categories`(`category_name`, `category_status`, `category_options`) VALUES 
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


    /**
     * get Category Air Cargo By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getCategoryAirCargoById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_air_categories` WHERE category_id=:category_id";
        $params = [
            'category_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * edit Category Air Cargo By Id
     *
     * @param $id
     * @param $title
     * @param $status
     *
     * @return stdClass
     */
    public static function editCategoryAirCargoById($id, $title, $status)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCategoryAirCargoById($id);
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

            $sql = 'UPDATE `tbl_air_categories` SET `category_name`=:category_name,`category_status`=:category_status,`category_options`=:category_options
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




    ///////////////////////////////////////////////// packing


    /**
     * get All Category Air packing From Table By Status
     *
     * @return stdClass
     */
    public static function getAllCategoryAirPackingFromTable()
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT `packing_id`, `packing_name`, `packing_status` FROM `tbl_packing_air` ORDER BY `packing_id` DESC ;";
        $params = [];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * set New Category Air Packing
     *
     * @param $title
     * @param $status
     *
     * @return stdClass
     */
    public static function setNewAirPacking($title, $status)
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

            $sql = 'INSERT INTO `tbl_packing_air`(`packing_name`, `packing_status`, `packing_options`) VALUES 
                    (:packing_name,:packing_status,:packing_options)';
            $params = [
                'packing_name' => $title,
                'packing_status' => $status,
                'packing_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }

        return $response;
    }


    /**
     * get Category Air packing By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getCategoryAirPackingById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_packing_air` WHERE packing_id=:packing_id";
        $params = [
            'packing_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * edit Category Air packing By Id
     *
     * @param $id
     * @param $title
     * @param $status
     *
     * @return stdClass
     */
    public static function editCategoryAirPackingById($id, $title, $status)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCategoryAirPackingById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->packing_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            $sql = 'UPDATE `tbl_packing_air` SET `packing_name`=:packing_name,`packing_status`=:packing_status,`packing_options`=:packing_options
                    WHERE `packing_id`=:packing_id;';
            $params = [
                'packing_name' => $title,
                'packing_status' => $status,
                'packing_options' => json_encode($value),
                'packing_id' => $id,
            ];
            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    /**
     * get All Active Packing Air
     *
     * @return stdClass
     */
    public static function getAllCategoryAirPackingActive()
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT `packing_id`, `packing_name`, `packing_status` FROM `tbl_packing_air` WHERE `packing_status`=:packing_status ORDER BY `packing_id` DESC ;";
        $params = [
            'packing_status' => "active"
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }











    ///////////////////// inquiry Air from admin


    /**
     * get All Inquiry Air By Status
     *
     * @param $status
     *
     * @return stdClass
     */
    public static function getAllInquiryAirByStatus($status)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT freight_id,user_mobile,freight_submit_date,user_language,user_firstname,user_lastname,user_avatar FROM `tbl_freight_air` INNER JOIN tbl_users ON tbl_users.user_id=tbl_freight_air.user_id WHERE freight_status=:freight_status;";
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
     * get Inquiry Air Info ById
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getInquiryInfoById($id)
    {
        $response = sendResponse(200, '');
        $sql = 'SELECT * FROM `tbl_freight_air` WHERE `freight_id`=:freight_id';
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
     * get Multi Airports By Airports Id
     *
     * @param $portsId
     *
     * @return stdClass
     */
    public static function getMultiPortsByIDs($airportsId)
    {
        $response = sendResponse(200, '');
        $sql = "SELECT tbl_airports.airport_id, city_id, airport_name, airport_status FROM `tbl_airports` WHERE `airport_id` IN  ({$airportsId})";
        $params = [];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * edit Inquiry Air Info By Admin
     *
     * @param $inquiryId
     * @param $type
     * @param $newValue
     *
     * @return stdClass
     */
    public static function editInquiryAirInfoByAdmin($inquiryId, $type, $newValue)
    {

        $response = sendResponse(0, "Error Msg");

        $sql = 'UPDATE `tbl_freight_air` SET ' . $type . '=:newValue WHERE `freight_id`=:freight_id';
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
     * change Inquiry Air Status By Admin
     *
     * @param $inquiryId
     * @param $status
     *
     * @return stdClass
     */
    public static function changeInquiryAirStatusByAdmin($inquiryId, $status)
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

            $sql = 'UPDATE `tbl_freight_air` SET `freight_status`=:freight_status,`freight_options`=:freight_options WHERE `freight_id`=:freight_id';
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
     * get airports By Country ID
     *
     * @param $CountryId
     *
     * @return stdClass
     */
    public static function getAirportsByCountryID($CountryId)
    {
        $response = sendResponse(0, "Error Msg");


        $sql = "SELECT tbl_airports.airport_id,tbl_airports.airport_name,tbl_airports.city_id FROM `tbl_airports` 
                INNER JOIN `tbl_cities` ON tbl_airports.city_id=tbl_cities.city_id
                INNER JOIN `tbl_country` ON tbl_country.country_id=tbl_cities.country_id WHERE tbl_cities.city_status_air=:city_status_air AND tbl_country.country_id=:country_id AND tbl_airports.airport_status=:airport_status";
        $params = [
            'country_id' => $CountryId,
            'airport_status' => 'active',
            'city_status_air' => 'yes',
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * change Inquiry Air Location By Admin
     *
     * @param $inquiryId
     * @param $country
     * @param $city
     * @param $customs
     * @param $type
     *
     * @return stdClass
     */
    public static function changeInquiryAirLocationByAdmin($inquiryId, $country, $city, $customs, $type)
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

            $sql = 'UPDATE `tbl_freight_air` SET `source_city_id`=:source_city_id,`source_airport_id`=:source_airport_id,
                    `freight_options`=:freight_options WHERE `freight_id`=:freight_id';
            $params = [
                'freight_options' => json_encode($array),
                'source_city_id' => $city,
                'source_airport_id' => $customs,
                'freight_id' => $inquiryId,
            ];

        } else {
            $sql = 'UPDATE `tbl_freight_air` SET `dest_city_id`=:dest_city_id ,`dest_airport_id`=:dest_airport_id,
                   `freight_options`=:freight_options WHERE `freight_id`=:freight_id';
            $params = [
                'freight_options' => json_encode($array),
                'dest_city_id' => $city,
                'dest_airport_id' => $customs,
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
     * Add Description From Inquiry Air
     *
     * @param $inquiryId
     * @param $type
     * @param $newValue
     *
     * @return stdClass
     */
    public static function addInquiryAirInfoByAdmin($inquiryId, $type, $newValue)
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

        $sql = 'UPDATE `tbl_freight_air` SET `freight_options`=:freight_options WHERE `freight_id`=:freight_id';
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

    /** morteza ship base  */
    /**
     * @param $status
     *
     * @return \stdClass
     * @author morteza
     */
    public static function getAllAirCategoryByStatus($status = 'active'): stdClass
    {
        $response = sendResponse(0, "Error Msg",[]);
        $sql = "SELECT * FROM `tbl_air_categories` tac  where category_status  = :status ";
        $params = [
            'status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    public static function getAllPackingByStatus($status = 'active')
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT * FROM `tbl_packing_air`  where packing_status = :status";
        $params = [
            'status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;

    }

    public static function insertInquiryAir($userId, $source_city_id, $dest_city_id, $source_port_id, $dest_port_id, $category_id, $packing_id
        ,                                   $currencyUnit, $freight_name, $freight_description, $price, $weight, $volume, $cargoDischarge, $start_date, $token)
    {

        if (!Security::verifyCSRF('inquiry-air', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('inquiry-air');

        $sql = "INSERT INTO `tbl_freight_air`( `user_id`, `source_city_id`, `dest_city_id`, `source_airport_id`, `dest_airport_id`, `category_id`, `packing_id`,
                               `currency_id_value`, `freight_name`, `freight_description`, `freight_price_value`, `freight_wieght`, `freight_volume`,
                              `freight_discharge`,  `freight_start_date`,
                              `freight_submit_date`, `freight_status` ) 
                              VALUES (:user_id, :source_city_id ,:dest_city_id , :source_airport_id , :dest_airport_id ,:category_id,:packing_id , :currency_id_value,
                              :freight_name ,:freight_description , :freight_price_value ,:freight_wieght , :freight_volume , :freight_discharge ,:freight_start_date ,
                                        :freight_submit_date,:freight_status)";
        $params = [
            'user_id' => $userId,
            'source_city_id' => $source_city_id,
            'dest_city_id' => $dest_city_id,
            'source_airport_id' => $source_port_id,
            'dest_airport_id' => $dest_port_id,
            'category_id' => $category_id,
            'packing_id' => $packing_id,
            'currency_id_value' => $currencyUnit,
            'freight_name' => $freight_name,
            'freight_description' => $freight_description,
            'freight_price_value' => $price,
            'freight_wieght' => $weight,
            'freight_volume' => $volume,
            'freight_discharge' => $cargoDischarge,
            'freight_start_date' => $start_date,
            'freight_status' => "pending",
            'freight_submit_date' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Cargo submitted successfully');
            User::createUserLog($userId, 'uLog_submit_cargo_air', 'cargo');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }


    /**
     * get Count Inquiry Air By Status
     *
     * @param $status
     *
     * @return stdClass
     */
    public static function getCountInquiryAirByStatus($status)
    {
        $response = sendResponse(0, "Error Msg", 0);
        $sql = "SELECT count(*) AS count FROM `tbl_freight_air` WHERE freight_status=:freight_status;";
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

    /**
     * @param        $userId
     * @param string $status
     * @param int    $page
     * @param int    $perPage
     *
     * @return stdClass
     */
    public static function getAirInquiryList($user_id, $status = 'all', $page = 1, $per_page = 10)
    {
        $response = sendResponse(200, '', []);
        $from = ($page == 1) ? 0 : ($page - 1) * $per_page;

        if ($status == 'all') {
            $sql = "select *  from tbl_freight_air
            where user_id = :user_id 
            order by freight_id desc
            limit {$from},{$per_page};";
            $params = [
                'user_id' => $user_id
            ];
        } else {
            $sql = "select *  from tbl_freight_air
            where tbl_freight_air.user_id = :user_id  and freight_status =:status
            order by freight_id desc 
            limit {$from},{$per_page}";
            $params = [
                'user_id' => $user_id,
                'status' => $status
            ];
        }

        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $inquiryList = [];
            foreach ($result->response as $item) {
                $inquiry = new stdClass();
                $inquiry->freight_id = $item->freight_id;
                $inquiry->freight_name = $item->freight_name;
                $inquiry->freight_status = $item->freight_status;
                array_push($inquiryList, $inquiry);
            }
            $response = sendResponse(200, '', $inquiryList);
        }
        return $response;
    }

    /**
     * @param $cargoId
     *
     * @return stdClass
     */
    public static function getInquiryDetail($freight_id, $user_id)
    {
        $response = sendResponse(404, '', null);
        $sql = "select *  from tbl_freight_air 
                inner join tbl_air_categories on tbl_air_categories.category_id  = tbl_freight_air.category_id
                inner join tbl_packing_air tpa on tpa.packing_id = tbl_freight_air.packing_id
                where user_id = :user_id  and freight_id = :freight_id";

        $params = [
            'user_id' => $user_id,
            'freight_id' => $freight_id
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, '', $result->response);
        }
        return $response;
    }

    /**
     * @return null
     */
    public static function getAirPortNameById($air_port_id)
    {
        $sql = "SELECT * FROM `tbl_airports`  WHERE tbl_airports.airport_id=:airport_id";
        $params = [
            'airport_id' => $air_port_id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $currency  =  $result->response ;
            return $currency[0]->airport_name;
        }else{
            return null ;
        }
    }



    /**
     * @param $userId
     * @param $status
     * @return int
     * @author Amir
     */
    public static function getInquiryCountByStatus($userId, $status)
    {
        $response = 0;

        $sql = "select count(*) as count
        from tbl_freight_air
        where `user_id` = :userId and `freight_status` = :status;";
        $params = [
            'userId' => $userId,
            'status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->count;
        }
        return $response;
    }

    public static function updateInquiryStatus($freight_id, $status)
    {

        $response = sendResponse(0, "");

        $sql = 'UPDATE tbl_freight_air set freight_status =:status where freight_id = :freight_id ';
        $params = [
            'freight_id' => $freight_id,
            'status' => $status,

        ];
        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }
        return $response;
    }


    public static function getCountAirFromCensus($max,$min)
    {

        $sql = "SELECT 
                    (SELECT COUNT(*) FROM `tbl_freight_air` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:pending) AS pending,
                    (SELECT COUNT(*) FROM `tbl_freight_air` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:process) AS process,
                    (SELECT COUNT(*) FROM `tbl_freight_air` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:completed) AS completed,
                    (SELECT COUNT(*) FROM `tbl_freight_air` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:read) AS readC
                    FROM tbl_freight_air ";
        $params = [
            'max' => $max,
            'min' => $min,
            'pending' => "pending",
            'process' => "process",
            'completed' => "completed",
            'read' => "read"
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            return [
                'pending' => $result->response[0]->pending,
                'process' => $result->response[0]->process,
                'completed' => $result->response[0]->completed,
                'read' => $result->response[0]->readC
            ];
        }

        return [
            'pending' => 0,
            'process' => 0,
            'completed' => 0,
            'read' => 0
        ];
    }
}