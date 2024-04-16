<?php


use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Railroad
{

    /**
     * get Count Railroad From Chart
     * @return stdClass
     */
    public static function getCountRailroadFromChart()
    {

        $sql = "SELECT COUNT(*) AS countAll,
                    (SELECT COUNT(*) FROM tbl_railroad WHERE tbl_railroad.railroad_status=:status_inactive) AS countInactive,
                    (SELECT COUNT(*) FROM tbl_railroad WHERE tbl_railroad.railroad_status=:status_active) AS countActive
                    FROM tbl_railroad";
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
     * Set New Railroad
     * @param $title
     * @param $city
     * @param $status
     * @param $priority
     * @return stdClass
     */
    public static function setNewRailroad($title, $city, $status, $priority)
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

            $sql = 'INSERT INTO `tbl_railroad`(`city_id`, `railroad_name`, `railroad_status`, `railroad_options`,`railroad_priority`) VALUES 
                    (:city_id,:railroad_name,:railroad_status,:railroad_options,:railroad_priority)';
            $params = [
                'railroad_name' => $title,
                'city_id' => $city,
                'railroad_status' => $status,
                'railroad_priority' => $priority,
                'railroad_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }

        return $response;
    }


    /**
     * Get Railroad By Id
     * @param $id
     * @return stdClass
     */
    public static function getRailroadById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_railroad` WHERE railroad_id=:railroad_id";
        $params = [
            'railroad_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * get Railroad Info By Id
     * @param $id
     * @return stdClass
     */
    public static function getRailroadInfoById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_railroad` INNER JOIN tbl_cities ON tbl_railroad.city_id=tbl_cities.city_id INNER JOIN tbl_country
                ON tbl_country.country_id=tbl_cities.country_id  WHERE `railroad_id`=:railroad_id";
        $params = [
            'railroad_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }

    /**
     * edit Port By Id
     * @param $id
     * @param $title
     * @param $city
     * @param $status
     * @param $priority
     * @return stdClass
     */
    public static function editRailroadById($id, $title, $city, $status, $priority)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getRailroadById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->railroad_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            $sql = 'UPDATE `tbl_railroad` SET `railroad_name`=:railroad_name,`city_id`=:city_id,`railroad_status`=:railroad_status,`railroad_options`=:railroad_options
                    ,`railroad_priority`=:railroad_priority WHERE `railroad_id`=:railroad_id;';
            $params = [
                'railroad_name' => $title,
                'city_id' => $city,
                'railroad_status' => $status,
                'railroad_options' => json_encode($value),
                'railroad_priority' => $priority,
                'railroad_id' => $id,
            ];
            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    /**
     * delete Railroad By Id
     * @param $railroadId
     * @return stdClass
     */
    public static function deleteRailroad($railroadId)
    {
        $response = sendResponse(200, '');
        $sql = 'DELETE FROM `tbl_railroad` WHERE `railroad_id`=:railroad_id';
        $params = [
            'railroad_id' => $railroadId,
        ];
        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    /////////////////////////////////////////////////// wagon


    /**
     * get All Wagon
     * @param null $status
     * @return stdClass
     */
    public static function getAllWagon($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT wagon_id, wagon_name, wagon_status FROM `tbl_wagons` ORDER BY wagon_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT wagon_id, wagon_name, wagon_status FROM `tbl_wagons` WHERE `wagon_status`=:wagon_status ORDER BY wagon_id DESC ;";
            $params = [
                'wagon_status' => $status
            ];
        }

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Set New Wagon
     * @param $title
     * @param $status
     * @return stdClass
     */
    public static function setNewWagon($title, $status)
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

            $sql = 'INSERT INTO `tbl_wagons`(`wagon_name`, `wagon_status`, `wagon_options`) VALUES 
                    (:wagon_name,:wagon_status,:wagon_options)';
            $params = [
                'wagon_name' => $title,
                'wagon_status' => $status,
                'wagon_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }

        return $response;
    }


    /**
     * Get Wagon By Id
     * @param $id
     * @return stdClass
     */
    public static function getWagonById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_wagons` WHERE wagon_id=:wagon_id";
        $params = [
            'wagon_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * edit wagon By Id
     * @param $id
     * @param $title
     * @param $status
     * @return stdClass
     */
    public static function editWagonById($id, $title, $status)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getWagonById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->wagon_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            $sql = 'UPDATE `tbl_wagons` SET `wagon_name`=:wagon_name,`wagon_status`=:wagon_status,`wagon_options`=:wagon_options
                    WHERE `wagon_id`=:wagon_id;';
            $params = [
                'wagon_name' => $title,
                'wagon_status' => $status,
                'wagon_options' => json_encode($value),
                'wagon_id' => $id,
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
     * get All Category Railroad Cargo By Status
     * @param null $status
     * @return stdClass
     */
    public static function getAllCategoryRailroadCargoFromTable($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT category_id, category_name, category_status FROM `tbl_railroad_categories` ORDER BY `category_id` DESC ;";
            $params = [];
        } else {
            $sql = "SELECT category_id, category_name, category_status FROM `tbl_railroad_categories` WHERE `category_status`=:category_status ORDER BY category_id DESC ;";
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
     * set New Category Railroad Cargo
     * @param $title
     * @param $status
     * @return stdClass
     */
    public static function setNewCategoryRailroadCargo($title, $status)
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

            $sql = 'INSERT INTO `tbl_railroad_categories`(`category_name`, `category_status`, `category_options`) VALUES 
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
     * get Category Railroad Cargo By Id
     * @param $id
     * @return stdClass
     */
    public static function getCategoryRailroadCargoById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_railroad_categories` WHERE category_id=:category_id";
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
     * edit Category Railroad Cargo By Id
     * @param $id
     * @param $title
     * @param $status
     * @return stdClass
     */
    public static function editCategoryRailroadCargoById($id, $title, $status)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCategoryRailroadCargoById($id);
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

            $sql = 'UPDATE `tbl_railroad_categories` SET `category_name`=:category_name,`category_status`=:category_status,`category_options`=:category_options
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
     * get All Category Railroad packing From Table By Status
     * @return stdClass
     */
    public static function getAllCategoryRailroadPackingFromTable()
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT `packing_id`, `packing_name`, `packing_status` FROM `tbl_packing_railroad` ORDER BY `packing_id` DESC ;";
        $params = [];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * get All Active Packing Railroad
     * @return stdClass
     */
    public static function getAllCategoryRailroadPackingActive()
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT `packing_id`, `packing_name`, `packing_status` FROM `tbl_packing_railroad` WHERE `packing_status`=:packing_status ORDER BY `packing_id` DESC ;";
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
     * set New Category Railroad Packing
     * @param $title
     * @param $status
     * @return stdClass
     */
    public static function setNewRailroadPacking($title, $status)
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

            $sql = 'INSERT INTO `tbl_packing_railroad`(`packing_name`, `packing_status`, `packing_options`) VALUES 
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
     * get Category Railroad packing By Id
     * @param $id
     * @return stdClass
     */
    public static function getCategoryRailroadPackingById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_packing_railroad` WHERE packing_id=:packing_id";
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
     * edit Category Railroad packing By Id
     * @param $id
     * @param $title
     * @param $status
     * @return stdClass
     */
    public static function editCategoryRailroadPackingById($id, $title, $status)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCategoryRailroadPackingById($id);
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

            $sql = 'UPDATE `tbl_packing_railroad` SET `packing_name`=:packing_name,`packing_status`=:packing_status,`packing_options`=:packing_options
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






    /////////////////////////////////////////////////// Container


    /**
     * get All Container
     * @param null $status
     * @return stdClass
     */
    public static function getAllContainer($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT container_id, container_name, container_status FROM `tbl_container_railroad` ORDER BY container_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT container_id, container_name, container_status FROM `tbl_container_railroad` WHERE `container_status`=:container_status ORDER BY container_id DESC ;";
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
     * @param $title
     * @param $status
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

            $sql = 'INSERT INTO `tbl_container_railroad`(`container_name`, `container_status`, `container_options`) VALUES 
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
     * @param $id
     * @return stdClass
     */
    public static function getContainerById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_container_railroad` WHERE container_id=:container_id";
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
     * @param $id
     * @param $title
     * @param $status
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

            $sql = 'UPDATE `tbl_container_railroad` SET `container_name`=:container_name,`container_status`=:container_status,`container_options`=:container_options
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



    ///////////////////// inquiry Railroad from admin


    /**
     * get All Inquiry Railroad By Status
     * @param $status
     * @return stdClass
     */
    public static function getAllInquiryRailroadByStatus($status)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT freight_id,user_mobile,freight_submit_date,user_language,user_firstname,user_lastname,user_avatar FROM `tbl_freight_railroad` INNER JOIN tbl_users ON tbl_users.user_id=tbl_freight_railroad.user_id WHERE freight_status=:freight_status;";
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
     * get Inquiry Railroad Info ById
     * @param $id
     * @return stdClass
     */
    public static function getInquiryInfoById($id)
    {
        $response = sendResponse(200, '');
        $sql = 'SELECT * FROM `tbl_freight_railroad` WHERE `freight_id`=:freight_id';
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
     * get Multi Railroad By Ports Id
     * @param $railroadId
     * @return stdClass
     */
    public static function getMultiPortsByIDs($railroadId)
    {
        $response = sendResponse(200, '');
        $sql = "SELECT railroad_id, city_id, railroad_name, railroad_status FROM `tbl_railroad` WHERE `railroad_id` IN  ({$railroadId})";
        $params = [];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * edit Inquiry Railroad Info By Admin
     * @param $inquiryId
     * @param $type
     * @param $newValue
     * @return stdClass
     */
    public static function editInquiryRailroadInfoByAdmin($inquiryId, $type, $newValue)
    {

        $response = sendResponse(0, "Error Msg");

        $sql = 'UPDATE `tbl_freight_railroad` SET ' . $type . '=:newValue WHERE `freight_id`=:freight_id';
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
     * change Inquiry Railroad Status By Admin
     * @param $inquiryId
     * @param $status
     * @return stdClass
     */
    public static function changeInquiryRailroadStatusByAdmin($inquiryId, $status)
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

            $sql = 'UPDATE `tbl_freight_railroad` SET `freight_status`=:freight_status,`freight_options`=:freight_options WHERE `freight_id`=:freight_id';
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
     * get Railroad By Country ID
     * @param $CountryId
     * @return stdClass
     */
    public static function getRailroadByCountryID($CountryId)
    {
        $response = sendResponse(0, "Error Msg");


        $sql = "SELECT tbl_railroad.railroad_id,tbl_railroad.railroad_name,tbl_railroad.city_id FROM `tbl_railroad` 
                INNER JOIN `tbl_cities` ON tbl_railroad.city_id=tbl_cities.city_id
                INNER JOIN `tbl_country` ON tbl_country.country_id=tbl_cities.country_id WHERE tbl_cities.city_status_ship=:city_status_ship AND tbl_country.country_id=:country_id AND tbl_railroad.railroad_status=:railroad_status";
        $params = [
            'country_id' => $CountryId,
            'railroad_status' => 'active',
            'city_status_ship' => 'yes',
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * change Inquiry Railroad Location By Admin
     * @param $inquiryId
     * @param $country
     * @param $city
     * @param $customs
     * @param $type
     * @return stdClass
     */
    public static function changeInquiryRailroadLocationByAdmin($inquiryId, $country, $city, $customs, $type)
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

            $sql = 'UPDATE `tbl_freight_railroad` SET `source_city_id`=:source_city_id,`source_railroad_id`=:source_railroad_id,
                    `freight_options`=:freight_options WHERE `freight_id`=:freight_id';
            $params = [
                'freight_options' => json_encode($array),
                'source_city_id' => $city,
                'source_railroad_id' => $customs,
                'freight_id' => $inquiryId,
            ];

        } else {
            $sql = 'UPDATE `tbl_freight_railroad` SET `dest_city_id`=:dest_city_id ,`dest_railroad_id`=:dest_railroad_id,
                   `freight_options`=:freight_options WHERE `freight_id`=:freight_id';
            $params = [
                'freight_options' => json_encode($array),
                'dest_city_id' => $city,
                'dest_railroad_id' => $customs,
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
     * Add Description From Inquiry Railroad
     * @param $inquiryId
     * @param $type
     * @param $newValue
     * @return stdClass
     */
    public static function addInquiryRailroadInfoByAdmin($inquiryId, $type, $newValue)
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

        $sql = 'UPDATE `tbl_freight_railroad` SET `freight_options`=:freight_options WHERE `freight_id`=:freight_id';
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



    public static function getAllCargoCategory($status = 'active')
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT * FROM `tbl_railroad_categories`  ORDER BY category_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_railroad_categories` WHERE `category_status`=:category_status ORDER BY category_id DESC ;";
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
     * @return stdClass
     */
    public static function getWagonType($status = 'active')
    {
        $response = sendResponse(200, '', []);

        $sql = "select *
        from tbl_wagons 
        where wagon_status = :status";
        $params = [
            'status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $typeList = [];
            foreach ($result->response as $item) {
                $type = new stdClass();
                $type->wagon_id = $item->wagon_id;
                $type->wagon_name = array_column(json_decode($item->wagon_name), 'value', 'slug')[$_COOKIE['language']];

                array_push($typeList, $type);
            }
            $response = sendResponse(200, '', $typeList);
        }
        return $response;
    }
    /**
     * @return stdClass
     */
    public static function getContainerType($status = 'active')
    {
        $response = sendResponse(200, '', []);

        $sql = "select *
        from tbl_container  
        where container_status = :status";
        $params = [
            'status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $typeList = [];
            foreach ($result->response as $item) {
                $type = new stdClass();
                $type->container_id = $item->container_id;
                $type->container_name = array_column(json_decode($item->container_name), 'value', 'slug')[$_COOKIE['language']];

                array_push($typeList, $type);
            }
            $response = sendResponse(200, '', $typeList);
        }
        return $response;
    }
    /**
     * @return stdClass
     */
    public static function getPackingType($status = 'active')
    {
        $response = sendResponse(200, '', []);

        $sql = "select *
        from tbl_packing_railroad   
        where packing_status = :status";
        $params = [
            'status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $typeList = [];
            foreach ($result->response as $item) {
                $type = new stdClass();
                $type->packing_id= $item->packing_id;
                $type->packing_name = array_column(json_decode($item->packing_name), 'value', 'slug')[$_COOKIE['language']];

                array_push($typeList, $type);
            }
            $response = sendResponse(200, '', $typeList);
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
     */
    public static function insertInquiryRailroad($userId, $name, $categoryId, $wagonType,$containerType , $packingType ,$startDate, $weight, $volume,
                                                 $origin, $source_railroad_id, $destination, $dest_railroad_id, $description, $token)
    {
        if (!Security::verifyCSRF('inquiry-railroad', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('inquiry-railroad');

        $sql = "insert into tbl_freight_railroad(`user_id`, `freight_name`, `category_id`, `wagon_id`, `container_id`, `packing_id`,
                               `freight_start_date`, `freight_wieght`, `freight_volume`, `source_city_id`, `source_railroad_id`,
                               `dest_city_id`, `dest_railroad_id`, `freight_submit_date` , `freight_status` , freight_description)values (:user_id,:freight_name,:category_id,:wagon_id,:container_id,:packing_id
                               ,:freight_start_date,:freight_wieght,:freight_volume,:source_city_id,:source_railroad_id,:dest_city_id,:dest_railroad_id,:freight_submit_date,:freight_status  , :freight_description)";
        $params = [
            'user_id' => $userId,
            'freight_name' => $name,
            'category_id' => $categoryId,
            'wagon_id' => $wagonType,
            'container_id' => $containerType,
            'packing_id' => $packingType,
            'freight_start_date' => $startDate,
            'freight_wieght' => $weight,
            'freight_volume' => $volume,
            'source_city_id' => $origin,
            'source_railroad_id' => $source_railroad_id,
            'dest_city_id' => $destination,
            'dest_railroad_id' => $dest_railroad_id,
            'freight_description' => $description,
            'freight_status' => 'pending',
            'freight_submit_date' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Cargo submitted successfully');
            User::createUserLog($userId, 'uLog_submit_cargo_railroad', 'cargo');
        } else {
            $response = sendResponse(-10, 'Error', $csrf );
        }
        return $response;
    }



    /**
     * get Count Inquiry Railroad By Status
     * @param $status
     * @return stdClass
     */
    public static function getCountInquiryRailroadByStatus($status)
    {
        $response = sendResponse(0, "Error Msg", 0);
        $sql = "SELECT count(*) AS count FROM `tbl_freight_railroad` WHERE freight_status=:freight_status;";
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
     * @return int
     * @author Amir
     */
    public static function getInquiryCountByStatus($userId, $status)
    {
        $response = 0;

        $sql = "select count(*) as count
        from tbl_freight_railroad
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

    public static function getRailroadInquiryList($user_id, $status = 'all' )
    {
        $response = sendResponse(200, '', []);


        if ($status == 'all') {
            $sql = "select *  from tbl_freight_railroad
            where user_id = :user_id 
            order by freight_id desc
          ";
            $params = [
                'user_id' => $user_id
            ];
        } else {
            $sql = "select *  from tbl_freight_railroad
            where tbl_freight_railroad.user_id = :user_id  and freight_status =:status
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
        $sql = "select *  from tbl_freight_railroad 
                inner join tbl_railroad_categories on tbl_railroad_categories.category_id  = tbl_freight_railroad.category_id
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
     * @param $railroad_id
     * @return null
     * @author Amir
     */
    public static function getRailroadNameById($railroad_id)
    {
        $sql = "SELECT * FROM `tbl_railroad`  WHERE tbl_railroad.railroad_id=:railroad_id";
        $params = [
            'railroad_id' => $railroad_id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $currency  =  $result->response ;
            return $currency[0]->railroad_name;
        }else{
            return null ;
        }
    }


    /**
     * @param $packing_id
     * @return null
     * @author Amir
     */
    public static function getRailroadPackingNameById($packing_id)
    {
        $sql = "SELECT * FROM `tbl_packing_railroad`  WHERE tbl_packing_railroad.packing_id=:packing_id";
        $params = [
            'packing_id' => $packing_id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $currency  =  $result->response ;
            return $currency[0]->packing_name;
        }else{
            return null ;
        }
    }


    /**
     * @param $container_id
     * @return null
     * @author Amir
     */
    public static function getRailroadContainerNameById($container_id)
    {
        $sql = "SELECT * FROM `tbl_container_railroad`  WHERE tbl_container_railroad.container_id=:container_id";
        $params = [
            'container_id' => $container_id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $currency  =  $result->response ;
            return $currency[0]->container_name;
        }else{
            return null ;
        }
    }


    /**
     * @param $wagon_id
     * @return null
     * @author Amir
     */
    public static function getRailroadWagonNameById($wagon_id)
    {
        $sql = "SELECT * FROM `tbl_wagons`  WHERE tbl_wagons.wagon_id=:wagon_id";
        $params = [
            'wagon_id' => $wagon_id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $currency  =  $result->response ;
            return $currency[0]->wagon_name;
        }else{
            return null ;
        }
    }

    public static function updateInquiryStatus($freight_id, $status)
    {

        $response = sendResponse(0, "");

        $sql = 'UPDATE tbl_freight_railroad set freight_status =:status where freight_id = :freight_id ';
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

    public static function getCountRailroadFromCensus($max,$min)
    {

        $sql = "SELECT 
                    (SELECT COUNT(*) FROM `tbl_freight_railroad` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:pending) AS pending,
                    (SELECT COUNT(*) FROM `tbl_freight_railroad` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:process) AS process,
                    (SELECT COUNT(*) FROM `tbl_freight_railroad` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:completed) AS completed,
                    (SELECT COUNT(*) FROM `tbl_freight_railroad` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:read) AS readC
                    FROM tbl_freight_railroad ";
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