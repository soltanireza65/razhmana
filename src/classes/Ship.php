<?php


use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Ship
{

    /**
     * get Count ports From Chart
     *
     * @return stdClass
     */
    public static function getCountPortsFromChart()
    {

        $sql = "SELECT COUNT(*) AS countAll,
                    (SELECT COUNT(*) FROM `tbl_ports` WHERE tbl_ports.port_status=:status_inactive) AS countInactive,
                    (SELECT COUNT(*) FROM `tbl_ports` WHERE tbl_ports.port_status=:status_active) AS countActive
                    FROM tbl_ports";
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
     * Set New Port
     *
     * @param $title
     * @param $city
     * @param $status
     * @param $priority
     *
     * @return stdClass
     */
    public static function setNewPort($title, $city, $status, $priority)
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

            $sql = 'INSERT INTO `tbl_ports`(`city_id`, `port_name`, `port_status`, `port_options`,`port_priority`) VALUES 
                    (:city_id,:port_name,:port_status,:port_options,:port_priority)';
            $params = [
                'port_name' => $title,
                'city_id' => $city,
                'port_status' => $status,
                'port_priority' => $priority,
                'port_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }

        return $response;
    }


    /**
     * Get Port By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getPortById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_ports` WHERE port_id=:port_id";
        $params = [
            'port_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * get Port Info By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getPortInfoById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_ports` INNER JOIN tbl_cities ON tbl_ports.city_id=tbl_cities.city_id INNER JOIN tbl_country
                ON tbl_country.country_id=tbl_cities.country_id  WHERE `port_id`=:port_id";
        $params = [
            'port_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }

    /**
     * edit Port By Id
     *
     * @param $id
     * @param $title
     * @param $city
     * @param $status
     * @param $priority
     *
     * @return stdClass
     */
    public static function editPortById($id, $title, $city, $status, $priority)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getPortById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->port_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            $sql = 'UPDATE `tbl_ports` SET `port_name`=:port_name,`city_id`=:city_id,`port_status`=:port_status,`port_options`=:port_options
                    ,`port_priority`=:port_priority WHERE `port_id`=:port_id;';
            $params = [
                'port_name' => $title,
                'city_id' => $city,
                'port_status' => $status,
                'port_options' => json_encode($value),
                'port_priority' => $priority,
                'port_id' => $id,
            ];
            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    /**
     * delete port By Id
     *
     * @param $portId
     *
     * @return stdClass
     */
    public static function deletePort($portId)
    {
        $response = sendResponse(200, '');
        $sql = 'DELETE FROM `tbl_ports` WHERE `port_id`=:port_id';
        $params = [
            'port_id' => $portId,
        ];
        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }

/////////////////////////////////////////////////// Container


    /**
     * get All Container
     *
     * @param null $status
     *
     * @return stdClass
     */
    public static function getAllContainer($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT container_id, container_name, container_status FROM `tbl_container` ORDER BY container_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT container_id, container_name, container_status FROM `tbl_container` WHERE `container_status`=:container_status ORDER BY container_id DESC ;";
            $params = [
                'container_status' => $status
            ];
        }

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Set New container
     *
     * @param $title
     * @param $status
     *
     * @return stdClass
     */
    public static function setNewContainer($title, $status)
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

            $sql = 'INSERT INTO `tbl_container`(`container_name`, `container_status`, `container_options`) VALUES 
                    (:container_name,:container_status,:container_options)';
            $params = [
                'container_name' => $title,
                'container_status' => $status,
                'container_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }

        return $response;
    }


    /**
     * Get container By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getContainerById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_container` WHERE container_id=:container_id";
        $params = [
            'container_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * edit container By Id
     *
     * @param $id
     * @param $title
     * @param $status
     *
     * @return stdClass
     */
    public static function editContainerById($id, $title, $status)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getContainerById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->container_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            $sql = 'UPDATE `tbl_container` SET `container_name`=:container_name,`container_status`=:container_status,`container_options`=:container_options
                    WHERE `container_id`=:container_id;';
            $params = [
                'container_name' => $title,
                'container_status' => $status,
                'container_options' => json_encode($value),
                'container_id' => $id,
            ];
            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }



    //////////////////////////////////////////// category


    /**
     * get All Category Ship Cargo By Status
     *
     * @param null $status
     *
     * @return stdClass
     */
    public static function getAllCategoryShipCargo($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT category_id, category_name, category_status FROM `tbl_ship_categories` ORDER BY `category_id` DESC ;";
            $params = [];
        } else {
            $sql = "SELECT category_id, category_name, category_status FROM `tbl_ship_categories` WHERE `category_status`=:category_status ORDER BY category_id DESC ;";
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
     * set New Category Ship Cargo
     *
     * @param $title
     * @param $status
     *
     * @return stdClass
     */
    public static function setNewCategoryShipCargo($title, $status)
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

            $sql = 'INSERT INTO `tbl_ship_categories`(`category_name`, `category_status`, `category_options`) VALUES 
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
     * get Category Ship Cargo By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getCategoryShipCargoById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_ship_categories` WHERE category_id=:category_id";
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
     * edit Category Ship Cargo By Id
     *
     * @param $id
     * @param $title
     * @param $status
     *
     * @return stdClass
     */
    public static function editCategoryShipCargoById($id, $title, $status)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCategoryShipCargoById($id);
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

            $sql = 'UPDATE `tbl_ship_categories` SET `category_name`=:category_name,`category_status`=:category_status,`category_options`=:category_options
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
     * get All Category Ship packing From Table By Status
     *
     * @return stdClass
     */
    public static function getAllCategoryShipPackingFromTable()
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT `packing_id`, `packing_name`, `packing_status` FROM `tbl_packing_ship` ORDER BY `packing_id` DESC ;";
        $params = [];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * get All Active Packing Ship
     *
     * @return stdClass
     */
    public static function getAllCategoryShipPackingActive()
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT `packing_id`, `packing_name`, `packing_status` FROM `tbl_packing_ship` WHERE `packing_status`=:packing_status ORDER BY `packing_id` DESC ;";
        $params = [
            'packing_status' => "active"
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }

    /**
     * set New Category Ship Packing
     *
     * @param $title
     * @param $status
     *
     * @return stdClass
     */
    public static function setNewShipPacking($title, $status)
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

            $sql = 'INSERT INTO `tbl_packing_ship`(`packing_name`, `packing_status`, `packing_options`) VALUES 
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
     * get Category Ship packing By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getCategoryShipPackingById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_packing_ship` WHERE packing_id=:packing_id";
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
     * edit Category Ship packing By Id
     *
     * @param $id
     * @param $title
     * @param $status
     *
     * @return stdClass
     */
    public static function editCategoryShipPackingById($id, $title, $status)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCategoryShipPackingById($id);
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

            $sql = 'UPDATE `tbl_packing_ship` SET `packing_name`=:packing_name,`packing_status`=:packing_status,`packing_options`=:packing_options
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




    ///////////////////// inquiry Ship from admin


    /**
     * get All Inquiry Ship By Status
     *
     * @param $status
     *
     * @return stdClass
     */
    public static function getAllInquiryShipByStatus($status)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT freight_id,user_mobile,freight_submit_date,user_language,user_firstname,user_lastname,user_avatar FROM `tbl_freight_ship` INNER JOIN tbl_users ON tbl_users.user_id=tbl_freight_ship.user_id WHERE freight_status=:freight_status;";
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
     * get Inquiry Ship Info ById
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getInquiryInfoById($id)
    {
        $response = sendResponse(200, '');
        $sql = 'SELECT * FROM `tbl_freight_ship` WHERE `freight_id`=:freight_id';
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
     * get Multi Ports By Ports Id
     *
     * @param $portsId
     *
     * @return stdClass
     */
    public static function getMultiPortsByIDs($portsId)
    {
        $response = sendResponse(200, '');
        $sql = "SELECT tbl_ports.port_id, city_id, port_name, port_status FROM `tbl_ports` WHERE `port_id` IN  ({$portsId})";
        $params = [];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * edit Inquiry Ship Info By Admin
     *
     * @param $inquiryId
     * @param $type
     * @param $newValue
     *
     * @return stdClass
     */
    public static function editInquiryShipInfoByAdmin($inquiryId, $type, $newValue)
    {

        $response = sendResponse(0, "Error Msg");

        $sql = 'UPDATE `tbl_freight_ship` SET ' . $type . '=:newValue WHERE `freight_id`=:freight_id';
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
     * change Inquiry Ship Status By Admin
     *
     * @param $inquiryId
     * @param $status
     *
     * @return stdClass
     */
    public static function changeInquiryShipStatusByAdmin($inquiryId, $status)
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

            $sql = 'UPDATE `tbl_freight_ship` SET `freight_status`=:freight_status,`freight_options`=:freight_options WHERE `freight_id`=:freight_id';
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
     * get Ports By Country ID
     *
     * @param $CountryId
     *
     * @return stdClass
     */
    public static function getPortsByCountryID($CountryId)
    {
        $response = sendResponse(0, "Error Msg");


        $sql = "SELECT tbl_ports.port_id,tbl_ports.port_name,tbl_ports.city_id FROM `tbl_ports` 
                INNER JOIN `tbl_cities` ON tbl_ports.city_id=tbl_cities.city_id
                INNER JOIN `tbl_country` ON tbl_country.country_id=tbl_cities.country_id WHERE tbl_cities.city_status_ship=:city_status_ship AND tbl_country.country_id=:country_id AND tbl_ports.port_status=:port_status";
        $params = [
            'country_id' => $CountryId,
            'port_status' => 'active',
            'city_status_ship' => 'yes',
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * change Inquiry Ship Location By Admin
     *
     * @param $inquiryId
     * @param $country
     * @param $city
     * @param $customs
     * @param $type
     *
     * @return stdClass
     */
    public static function changeInquiryShipLocationByAdmin($inquiryId, $country, $city, $customs, $type)
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

            $sql = 'UPDATE `tbl_freight_ship` SET `source_city_id`=:source_city_id,`source_port_id`=:source_port_id,
                    `freight_options`=:freight_options WHERE `freight_id`=:freight_id';
            $params = [
                'freight_options' => json_encode($array),
                'source_city_id' => $city,
                'source_port_id' => $customs,
                'freight_id' => $inquiryId,
            ];

        } else {
            $sql = 'UPDATE `tbl_freight_ship` SET `dest_city_id`=:dest_city_id ,`dest_port_id`=:dest_port_id,
                   `freight_options`=:freight_options WHERE `freight_id`=:freight_id';
            $params = [
                'freight_options' => json_encode($array),
                'dest_city_id' => $city,
                'dest_port_id' => $customs,
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
     * Add Description From Inquiry Ship
     *
     * @param $inquiryId
     * @param $type
     * @param $newValue
     *
     * @return stdClass
     */
    public static function addInquiryShipInfoByAdmin($inquiryId, $type, $newValue)
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

        $sql = 'UPDATE `tbl_freight_ship` SET `freight_options`=:freight_options WHERE `freight_id`=:freight_id';
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
    public static function getAllShipCategoryByStatus($status = 'active'): stdClass
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT * FROM `tbl_ship_categories`  where tbl_ship_categories.category_status  = :status ";
        $params = [
            'status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    /**
     * @param $status
     *
     * @return \stdClass
     * @author  morteza
     */
    public static function getAllContainerByStatus($status = 'active'): stdClass
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT * FROM `tbl_container` where tbl_container.container_status = :status";
        $params = [
            'status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


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
     * @author morteza
     */
    public static function inquiryShipInsert($userId, $source_city_id, $dest_city_id, $source_port_id, $dest_port_id, $category_id, $container_id, $packing_id, $freight_name, $freight_description
        ,                                    $container_count, $weight, $volume, $start_date, $token)
    {
        if (!Security::verifyCSRF('inquiry-ship', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('inquiry-ship');

        $sql = "INSERT INTO `tbl_freight_ship`(
    user_id , source_city_id , dest_city_id , source_port_id  , dest_port_id  , category_id , container_id , packing_id  , freight_name ,freight_description
     , freight_count_container ,freight_wieght , freight_volume  ,freight_start_date , freight_submit_date ,freight_status
) VALUES (
 :user_id , :source_city_id , :dest_city_id , :source_port_id  , :dest_port_id  , :category_id , :container_id , :packing_id  , :freight_name ,:freight_description
     , :freight_count_container ,:freight_wieght , :freight_volume  ,:freight_start_date , :freight_submit_date ,:freight_status
)";
        $params = [
            'user_id' => $userId,
            'source_city_id' => $source_city_id,
            'dest_city_id' => $dest_city_id,
            'source_port_id' => $source_port_id,
            'dest_port_id' => $dest_port_id,
            'category_id' => $category_id,
            'container_id' => $container_id,
            'packing_id' => $packing_id,
            'freight_name' => $freight_name,
            'freight_description' => $freight_description,
            'freight_count_container' => $container_count,
            'freight_wieght' => $weight,
            'freight_volume' => $volume,
            'freight_start_date' => $start_date,
            'freight_status' => 'pending',
            'freight_submit_date' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Cargo submitted successfully');
            User::createUserLog($userId, 'uLog_submit_cargo_shio', 'cargo');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }

    public static function getAllPackingByStatus($status = 'active')
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT * FROM `tbl_packing_ship` where tbl_packing_ship.packing_status = :status";
        $params = [
            'status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;

    }


    /**
     * get Count Inquiry Ship By Status
     *
     * @param $status
     *
     * @return stdClass
     */
    public static function getCountInquiryShipByStatus($status)
    {
        $response = sendResponse(0, "Error Msg", 0);
        $sql = "SELECT count(*) AS count FROM `tbl_freight_ship` WHERE freight_status=:freight_status;";
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
     * @param $userId
     * @param $status
     *
     * @return int
     * @author Tjavan
     */
    public static function getInquiryCountByStatus($userId, $status)
    {
        $response = 0;

        $sql = "select count(*) as count
        from tbl_freight_ship
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

    public static function getShipInquiryList($user_id, $status = 'all')
    {
        $response = sendResponse(200, '', []);


        if ($status == 'all') {
            $sql = "select *  from tbl_freight_ship
            where user_id = :user_id 
            order by freight_id desc
             ";
            $params = [
                'user_id' => $user_id
            ];
        } else {
            $sql = "select *  from tbl_freight_ship
            where tbl_freight_ship.user_id = :user_id  and freight_status =:status
            order by freight_id desc 
             ";
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
        $sql = "select *  from tbl_freight_ship 
                inner join tbl_air_categories on tbl_air_categories.category_id  = tbl_freight_ship.category_id
                inner join tbl_container on tbl_container.container_id  = tbl_freight_ship.container_id
                inner join tbl_packing_air tpa on tpa.packing_id = tbl_freight_ship.packing_id
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
     * @param $port_id
     *
     * @return null
     */
    public static function getPortNameById($port_id)
    {
        $sql = "SELECT * FROM `tbl_ports`  WHERE tbl_ports.port_id=:port_id";
        $params = [
            'port_id' => $port_id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $currency = $result->response;
            return $currency[0]->port_name;
        } else {
            return null;
        }
    }

    public static function updateInquiryStatus($freight_id, $status)
    {

        $response = sendResponse(0, "");

        $sql = 'UPDATE tbl_freight_ship set freight_status =:status where freight_id = :freight_id ';
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


    public static function getCountShipFromCensus($max,$min)
    {

        $sql = "SELECT 
                    (SELECT COUNT(*) FROM `tbl_freight_ship` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:pending) AS pending,
                    (SELECT COUNT(*) FROM `tbl_freight_ship` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:process) AS process,
                    (SELECT COUNT(*) FROM `tbl_freight_ship` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:completed) AS completed,
                    (SELECT COUNT(*) FROM `tbl_freight_ship` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:read) AS readC
                    FROM tbl_freight_ship ";
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