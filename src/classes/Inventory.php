<?php

use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Inventory
{

    /**
     * get Count Inventory From Chart
     * @return stdClass
     */
    public static function getCountInventoryFromChart()
    {

        $sql = "SELECT COUNT(*) AS countAll,
                    (SELECT COUNT(*) FROM tbl_inventory WHERE tbl_inventory.inventory_status=:status_inactive) AS countInactive,
                    (SELECT COUNT(*) FROM tbl_inventory WHERE tbl_inventory.inventory_status=:status_active) AS countActive
                    FROM tbl_inventory";
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
     * Set New inventory
     * @param $title
     * @param $city
     * @param $status
     * @param $priority
     * @return stdClass
     */
    public static function setNewInventory($title, $city, $status, $priority)
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

            $sql = 'INSERT INTO `tbl_inventory`(`city_id`, `inventory_name`, `inventory_status`, `inventory_options`,`inventory_priority`) VALUES 
                    (:city_id,:inventory_name,:inventory_status,:inventory_options,:inventory_priority)';
            $params = [
                'inventory_name' => $title,
                'city_id' => $city,
                'inventory_status' => $status,
                'inventory_priority' => $priority,
                'inventory_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }

        return $response;
    }


    /**
     * Get Inventory By Id
     * @param $id
     * @return stdClass
     */
    public static function getInventoryById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_inventory` WHERE inventory_id=:inventory_id";
        $params = [
            'inventory_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * get Inventory Info By Id
     * @param $id
     * @return stdClass
     */
    public static function getInventoryInfoById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_inventory` INNER JOIN tbl_cities ON tbl_inventory.city_id=tbl_cities.city_id INNER JOIN tbl_country
                ON tbl_country.country_id=tbl_cities.country_id  WHERE `inventory_id`=:inventory_id";
        $params = [
            'inventory_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }

    /**
     * edit Inventory By Id
     * @param $id
     * @param $title
     * @param $city
     * @param $status
     * @param $priority
     * @return stdClass
     */
    public static function editInventoryById($id, $title, $city, $status, $priority)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getInventoryById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->inventory_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            $sql = 'UPDATE `tbl_inventory` SET `inventory_name`=:inventory_name,`city_id`=:city_id,`inventory_status`=:inventory_status,`inventory_options`=:inventory_options
                    ,`inventory_priority`=:inventory_priority WHERE `inventory_id`=:inventory_id;';
            $params = [
                'inventory_name' => $title,
                'city_id' => $city,
                'inventory_status' => $status,
                'inventory_options' => json_encode($value),
                'inventory_priority' => $priority,
                'inventory_id' => $id,
            ];
            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    /**
     * delete Inventory By Id
     * @param $inventoryId
     * @return stdClass
     */
    public static function deleteInventory($inventoryId)
    {
        $response = sendResponse(200, '');
        $sql = 'DELETE FROM `tbl_inventory` WHERE `inventory_id`=:inventory_id';
        $params = [
            'inventory_id' => $inventoryId,
        ];
        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    //////////////////////////////////////////// category


    /**
     * get All Category Inventory Cargo By Status
     * @param null $status
     * @return stdClass
     */
    public static function getAllCategoryInventoryCargoFromTable($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT category_id, category_name, category_status FROM `tbl_inventory_categories` ORDER BY `category_id` DESC ;";
            $params = [];
        } else {
            $sql = "SELECT category_id, category_name, category_status FROM `tbl_inventory_categories` WHERE `category_status`=:category_status ORDER BY category_id DESC ;";
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
     * set New Category Inventory Cargo
     * @param $title
     * @param $status
     * @return stdClass
     */
    public static function setNewCategoryInventoryCargo($title, $status)
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

            $sql = 'INSERT INTO `tbl_inventory_categories`(`category_name`, `category_status`, `category_options`) VALUES 
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
     * get Category Inventory Cargo By Id
     * @param $id
     * @return stdClass
     */
    public static function getCategoryInventoryCargoById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_inventory_categories` WHERE category_id=:category_id";
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
     * edit Category Inventory Cargo By Id
     * @param $id
     * @param $title
     * @param $status
     * @return stdClass
     */
    public static function editCategoryInventoryCargoById($id, $title, $status)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCategoryInventoryCargoById($id);
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

            $sql = 'UPDATE `tbl_inventory_categories` SET `category_name`=:category_name,`category_status`=:category_status,`category_options`=:category_options
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

    ///////////////////// inquiry Inventory from admin


    /**
     * get All Inquiry Inventory By Status
     * @param $status
     * @return stdClass
     */
    public static function getAllInquiryInventoryByStatus($status)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT freight_id,user_mobile,freight_submit_date,user_language,user_firstname,user_lastname,user_avatar FROM `tbl_freight_inventory` INNER JOIN tbl_users ON tbl_users.user_id=tbl_freight_inventory.user_id WHERE freight_status=:freight_status;";
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
     * get Inquiry Inventory Info ById
     * @param $id
     * @return stdClass
     */
    public static function getInquiryInfoById($id)
    {
        $response = sendResponse(200, '');
        $sql = 'SELECT * FROM `tbl_freight_inventory` WHERE `freight_id`=:freight_id';
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
     * get Inventory By Country ID
     * @param $CountryId
     * @return stdClass
     */
    public static function getInventoryByCountryID($CountryId)
    {
        $response = sendResponse(0, "Error Msg");


        $sql = "SELECT tbl_inventory.inventory_id,tbl_inventory.inventory_name,tbl_inventory.city_id FROM `tbl_inventory` 
                INNER JOIN `tbl_cities` ON tbl_inventory.city_id=tbl_cities.city_id
                INNER JOIN `tbl_country` ON tbl_country.country_id=tbl_cities.country_id WHERE tbl_cities.city_status_ship=:city_status_ship AND tbl_country.country_id=:country_id AND tbl_inventory.inventory_status=:inventory_status";
        $params = [
            'country_id' => $CountryId,
            'inventory_status' => 'active',
            'city_status_ship' => 'yes',
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * edit Inquiry Inventory Info By Admin
     * @param $inquiryId
     * @param $type
     * @param $newValue
     * @return stdClass
     */
    public static function editInquiryInventoryInfoByAdmin($inquiryId, $type, $newValue)
    {

        $response = sendResponse(0, "Error Msg");

        $sql = 'UPDATE `tbl_freight_inventory` SET ' . $type . '=:newValue WHERE `freight_id`=:freight_id';
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
     * Add Description From Inquiry Inventory
     * @param $inquiryId
     * @param $type
     * @param $newValue
     * @return stdClass
     */
    public static function addInquiryInventoryInfoByAdmin($inquiryId, $type, $newValue)
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

        $sql = 'UPDATE `tbl_freight_inventory` SET `freight_options`=:freight_options WHERE `freight_id`=:freight_id';
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
     * change Inquiry Inventory Location By Admin
     * @param $inquiryId
     * @param $country
     * @param $city
     * @param $inventory
     * @return stdClass
     */
    public static function changeInquiryInventoryLocationByAdmin($inquiryId, $country, $city, $inventory)
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
            $a['value'] = 'change_location';
            $a['date'] = time();
            array_push($array['update'], $a);

        } else {
            $a = [];
            $array['update'] = [];

            $a['admin'] = $admin_id;
            $a['type'] = 'location';
            $a['value'] = 'change_location';
            $a['date'] = time();
            array_push($array['update'], $a);
        }

        $sql = 'UPDATE `tbl_freight_inventory` SET `city_id`=:city_id ,`inventory_id`=:inventory_id ,
                    `freight_options`=:freight_options WHERE `freight_id`=:freight_id';
        $params = [
            'freight_options' => json_encode($array),
            'city_id' => $city,
            'inventory_id' => $inventory,
            'freight_id' => $inquiryId,
        ];


        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    /**
     * change Inquiry Inventory Status By Admin
     * @param $inquiryId
     * @param $status
     * @return stdClass
     */
    public static function changeInquiryInventoryStatusByAdmin($inquiryId, $status)
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

            $sql = 'UPDATE `tbl_freight_inventory` SET `freight_status`=:freight_status,`freight_options`=:freight_options WHERE `freight_id`=:freight_id';
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
     * get Count Inquiry Inventory By Status
     * @param $status
     * @return stdClass
     */
    public static function getCountInquiryInventoryByStatus($status)
    {
        $response = sendResponse(0, "Error Msg", 0);
        $sql = "SELECT count(*) AS count FROM `tbl_freight_inventory` WHERE freight_status=:freight_status;";
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


    public static function getAllCargoCategory($status = 'active')
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT * FROM `tbl_inventory_categories` ORDER BY category_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_inventory_categories` WHERE `category_status`=:category_status ORDER BY category_id DESC ;";
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
    public static function insertInquiryRailroad($userId, $name, $categoryId, $typeId, $startDate, $weight, $volume, $duration_day,
                                                 $origin, $description, $token)
    {
        if (!Security::verifyCSRF('inquiry-inventory', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('inquiry-inventory');

        $sql = "insert into tbl_freight_inventory(user_id , freight_name , category_id ,type_id, freight_start_date , freight_wieght , freight_volume , freight_duration , city_id,
                freight_description , freight_status , freight_submit_date)values(:user_id , :freight_name , :category_id ,:type_id, :freight_start_date , :freight_wieght , :freight_volume ,
                :freight_duration , :city_id,:freight_description , :freight_status , :freight_submit_date)";
        $params = [
            'user_id' => $userId,
            'freight_name' => $name,
            'category_id' => $categoryId,
            'type_id' => $typeId,
            'freight_start_date' => $startDate,
            'freight_wieght' => $weight,
            'freight_volume' => $volume,
            'freight_duration' => $duration_day,
            'city_id' => $origin,
            'freight_description' => $description,
            'freight_status' => 'pending',
            'freight_submit_date' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Cargo submitted successfully');
            User::createUserLog($userId, 'uLog_submit_cargo_inventory', 'cargo');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
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
        from tbl_freight_inventory
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


    /**
     * @param $user_id
     * @param $status
     * @param $page
     * @param $per_page
     * @return stdClass
     * @author Amir
     */
    public static function getInventoryInquiryList($user_id, $status = 'all' )
    {
        $response = sendResponse(200, '', []);


        if ($status == 'all') {
            $sql = "select *  from tbl_freight_inventory
            where user_id = :user_id 
            order by freight_id desc
       ";
            $params = [
                'user_id' => $user_id
            ];
        } else {
            $sql = "select *  from tbl_freight_inventory
            where tbl_freight_inventory.user_id = :user_id  and freight_status =:status
            order by freight_id desc ";

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
        $sql = "select *  from tbl_freight_inventory 
                inner join tbl_inventory_categories on tbl_inventory_categories.category_id  = tbl_freight_inventory.category_id
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
            $currency = $result->response;
            return $currency[0]->airport_name;
        } else {
            return null;
        }
    }


    //////////////////***************  inventory type ********************////////////////////


    /**
     * get All Category Inventory Type By Status
     * @param null $status
     * @return stdClass
     */
    public static function getAllCategoryInventoryTypeFromTable($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT type_id, type_name, type_status FROM `tbl_inventory_type` ORDER BY `type_id` DESC ;";
            $params = [];
        } else {
            $sql = "SELECT type_id, type_name, type_status FROM `tbl_inventory_type` WHERE `type_status`=:type_status ORDER BY type_id DESC ;";
            $params = [
                'type_status' => $status
            ];
        }

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     *  Set New Category Inventory Type
     * @param $title
     * @param $status
     * @param $priority
     * @return stdClass
     */
    public static function setNewCategoryInventoryType($title, $status, $priority)
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

            $sql = 'INSERT INTO `tbl_inventory_type`(`type_name`,`type_priority`, `type_status`, `type_options`) VALUES 
                    (:category_name,:type_priority,:category_status,:category_options)';
            $params = [
                'category_name' => $title,
                'type_priority' => $priority,
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
     * get Category Inventory Type By Id
     * @param $id
     * @return stdClass
     */
    public static function getCategoryInventoryTypeById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_inventory_type` WHERE type_id=:type_id";
        $params = [
            'type_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * edit Category Inventory Type By Id
     * @param $id
     * @param $title
     * @param $status
     * @param $priority
     * @return stdClass
     */
    public static function editCategoryInventoryTypeById($id, $title, $status, $priority)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCategoryInventoryTypeById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->type_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            $sql = 'UPDATE `tbl_inventory_type` SET `type_name`=:type_name,`type_status`=:type_status,
                    `type_priority`=:type_priority, `type_options`=:type_options WHERE `type_id`=:type_id;';
            $params = [
                'type_name' => $title,
                'type_status' => $status,
                'type_priority' => $priority,
                'type_options' => json_encode($value),
                'type_id' => $id,
            ];
            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    /**
     * @param $status
     * @return stdClass
     * @author Amir
     */
    public static function getAllInvenoryType($status = 'active')
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT * FROM `tbl_inventory_type` ORDER BY type_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_inventory_type` WHERE `type_status`=:type_status ORDER BY type_priority DESC ;";
            $params = [
                'type_status' => $status
            ];
        }

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }

    public static function updateInquiryStatus($freight_id, $status)
    {

        $response = sendResponse(0, "");

        $sql = 'UPDATE tbl_freight_inventory set freight_status =:status where freight_id = :freight_id ';
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


    public static function getCountInventoryFromCensus($max,$min)
    {

        $sql = "SELECT 
                    (SELECT COUNT(*) FROM `tbl_freight_inventory` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:pending) AS pending,
                    (SELECT COUNT(*) FROM `tbl_freight_inventory` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:process) AS process,
                    (SELECT COUNT(*) FROM `tbl_freight_inventory` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:completed) AS completed,
                    (SELECT COUNT(*) FROM `tbl_freight_inventory` WHERE `freight_submit_date` <= :max AND `freight_submit_date` >= :min AND `freight_status`=:read) AS readC
                    FROM tbl_freight_inventory ";
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