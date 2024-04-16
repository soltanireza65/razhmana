<?php


use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Car
{


    /**
     * Get All Cars Type
     * @param null $status
     * @return Object
     * @author Tjavan
     * @version 2.0.0
     */
    public static function getAllCarsTypes($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT * FROM `tbl_car_types` ORDER BY type_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_car_types` WHERE `type_status`=:type_status ORDER BY type_id DESC ;";
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
     * Set New Car Category
     * @param $title
     * @param $status
     * @return Object
     * @author Tjavan
     * @version 2.0.0
     */
    public static function setNewCategoryCar($title, $status,$icon)
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

            $sql = 'INSERT INTO `tbl_car_types`( `type_name`,`type_status`,`type_icon`, `type_options`) VALUES (:type_name,:type_status,:type_icon,:type_options)';
            $params = [
                'type_name' => $title,
                'type_status' => $status,
                'type_icon' => $icon,
                'type_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }

        return $response;
    }


    /**
     * Get Category Car Info By Id
     * @param $id int
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getCategoryCarById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_car_types` WHERE type_id=:type_id";
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
     * Edit Car Category By ID
     * @param $id
     * @param $title
     * @param $status
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function editCategoryCarById($id, $title, $status,$icon=null)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCategoryCarById($id);
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

            if(is_null($icon)){
                $sql = 'UPDATE `tbl_car_types` SET `type_name`=:type_name,`type_status`=:type_status,`type_options`=:type_options WHERE `type_id`=:type_id;';
                $params = [
                    'type_name' => $title,
                    'type_status' => $status,
                    'type_options' => json_encode($value),
                    'type_id' => $id,
                ];
                $result = DB::update($sql, $params);
            }else{
                $sql = 'UPDATE `tbl_car_types` SET `type_name`=:type_name,`type_status`=:type_status,`type_icon`=:type_icon,`type_options`=:type_options WHERE `type_id`=:type_id;';
                $params = [
                    'type_name' => $title,
                    'type_status' => $status,
                    'type_icon' => $icon,
                    'type_options' => json_encode($value),
                    'type_id' => $id,
                ];
                $result = DB::update($sql, $params);
            }


            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    /**
     * Get All Cars
     * @param null $status
     * @return Object
     * @author Tjavan
     * @version 2.0.0
     */
    public static function getAllCars($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT * FROM `tbl_cars` ORDER BY car_id  DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_cars` WHERE `car_status`=:car_status ORDER BY car_id  DESC ;";
            $params = [
                'car_status' => $status
            ];
        }


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get Car Info By ID
     * @param $id
     * @return Object
     * @author Tjavan
     * @version 2.0.0
     */
    public static function getCarById($id)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_cars` WHERE `car_id`=:car_id ";
        $params = [
            'car_id' => $id
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get Car Info By ID
     * @param $id
     * @return Object
     * @author Tjavan
     * @version 2.0.0
     */
    public static function editCarById($id, $status)
    {
        $response = sendResponse(0, "Error Msg");


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCarById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->car_options;
        }


        if (empty($temp)) {

            $array = [];
            $array['update'] = [];
            $value = [];
            $value['admin'] = $admin_id;
            $value['status'] = $status;
            $value['time'] = time();
            array_push($array['update'], ($value));
        } else {
            $array = json_decode($temp, true);

            $value = [];
            $value['admin'] = $admin_id;
            $value['status'] = $status;
            $value['time'] = time();
            array_push($array['update'], ($value));
        }

        if ($admin_id > 0) {

            $sql = "UPDATE `tbl_cars` SET `car_options`=:car_options,`car_status`=:car_status WHERE `car_id`=:car_id";
            $params = [
                'car_options' => json_encode($array),
                'car_status' => $status,
                'car_id' => $id,
            ];


            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "Successful");
            }
        }
        return $response;
    }


    /**
     * Get Driver All Cars Bu Driver ID
     * @param $userID
     * @return Object
     * @author Tjavan
     * @version 2.0.0
     */
    public static function getUSerAllCarsByUserID($userID)
    {

        $response = sendResponse(0, "Error Msg");

            $sql = "SELECT * FROM `tbl_cars` WHERE `user_id`=:user_id ORDER BY car_id  DESC ;";
            $params = [
                'user_id' => $userID
            ];



        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


}