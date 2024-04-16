<?php


use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Cargo
{

    /**
     * Get All Cars Type
     *
     * @param null $status
     *
     * @return Object
     * @author  Amir
     * @version 2.0.0
     */
    public static function getUserPhoneNumber($user_id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT user_mobile FROM `tbl_users` WHERE user_id=:user_id LIMIT 1";
        $params = [
            'user_id' => $user_id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }
    /**
     * Get All Cars Type
     *
     * @param null $status
     *
     * @return Object
     * @author  Amir
     * @version 2.0.0
     */
    public static function getAllCargoCategory($status = null)
    {
        $response = sendResponse(0, "Error Msg",[]);
        if (empty($status)) {
            $sql = "SELECT * FROM `tbl_cargo_categories` ORDER BY category_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_cargo_categories` WHERE `category_status`=:category_status ORDER BY category_id DESC ;";
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
     * Set New Car Category
     *
     * @param $title
     * @param $status
     *
     * @return Object
     * @author  Amir
     * @version 2.0.0
     */
    public static function setNewCategoryCargo($title, $color, $icon, $image, $status)
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

            $sql = 'INSERT INTO `tbl_cargo_categories`( `category_name`, `category_color`, `category_icon`, `category_image`, `category_status`, `category_options`)
 VALUES (:category_name, :category_color,:category_icon,:category_image,:category_status,:category_options)';
            $params = [
                'category_name' => $title,
                'category_color' => $color,
                'category_icon' => $icon,
                'category_image' => $image,
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
     * Get Category Car Info By Id
     *
     * @param $id int
     *
     * @return Object
     * @author  Amir
     * @version 1.0.0
     */
    public static function getCategoryCargoById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_cargo_categories` WHERE category_id=:category_id";
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
     * Edit Car Category By ID
     *
     * @param $id
     * @param $title
     * @param $status
     *
     * @return Object
     * @author  Amir
     * @version 1.0.0
     */
    public static function editCategoryCargoById($id, $title, $color, $status, $icon = null, $image = null)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCategoryCargoById($id);
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

            if (!empty($icon) && !empty($image)) {
                $sql = 'UPDATE `tbl_cargo_categories` SET `category_name`=:category_name,`category_color`=:category_color,
                                  `category_icon`=:category_icon,`category_image`=:category_image,`category_status`=:category_status,
                                  `category_options`=:category_options WHERE `category_id`=:category_id';
                $params = [
                    'category_name' => $title,
                    'category_color' => $color,
                    'category_icon' => $icon,
                    'category_image' => $image,
                    'category_status' => $status,
                    'category_options' => json_encode($value),
                    'category_id' => $id,
                ];
            } elseif (!empty($icon) && empty($image)) {
                $sql = 'UPDATE `tbl_cargo_categories` SET `category_name`=:category_name,`category_color`=:category_color,
                                  `category_icon`=:category_icon,`category_status`=:category_status,
                                  `category_options`=:category_options WHERE `category_id`=:category_id';
                $params = [
                    'category_name' => $title,
                    'category_color' => $color,
                    'category_icon' => $icon,
//                    'category_image' => $image,
                    'category_status' => $status,
                    'category_options' => json_encode($value),
                    'category_id' => $id,
                ];
            } elseif (empty($icon) && !empty($image)) {
                $sql = 'UPDATE `tbl_cargo_categories` SET `category_name`=:category_name,`category_color`=:category_color,
                                  `category_image`=:category_image,`category_status`=:category_status,
                                  `category_options`=:category_options WHERE `category_id`=:category_id';
                $params = [
                    'category_name' => $title,
                    'category_color' => $color,
//                    'category_icon' => $icon,
                    'category_image' => $image,
                    'category_status' => $status,
                    'category_options' => json_encode($value),
                    'category_id' => $id,
                ];
            } else {
                $sql = 'UPDATE `tbl_cargo_categories` SET `category_name`=:category_name,`category_color`=:category_color,
                                  `category_status`=:category_status,
                                  `category_options`=:category_options WHERE `category_id`=:category_id';
                $params = [
                    'category_name' => $title,
                    'category_color' => $color,
//                    'category_icon' => $icon,
//                    'category_image' => $image,
                    'category_status' => $status,
                    'category_options' => json_encode($value),
                    'category_id' => $id,
                ];
            }

            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    /**
     * Get All Cars
     *
     * @param null $status
     *
     * @return Object
     * @author  Amir
     * @version 2.0.0
     */
    public static function getAllCargo($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT * FROM `tbl_cargo` ORDER BY cargo_id  DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_cargo` WHERE `cargo_status`=:cargo_status ORDER BY cargo_id  DESC ;";
            $params = [
                'cargo_status' => $status
            ];
        }


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }

    /**
     * Get All cargos  by time
     *
     * @param null $status
     *
     * @return Object
     * @author  Amir
     * @version 2.0.0
     */
    public static function getAllCargoForCronJob($status1 = 'accepted' )
    {

        $response = sendResponse(0, "Error Msg");
        $sql = 'select *  from tbl_cargo where (tbl_cargo.cargo_status = :status1  )';

        $params = [
            "status1" => $status1,

        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }
    /**
     * Get All cargos  by time
     *
     * @param null $status
     *
     * @return Object
     * @author  Amir
     * @version 2.0.0
     */
    public static function getAllCargoInForCronJob($status1 = 'accepted' )
    {

        $response = sendResponse(0, "Error Msg");
        $sql = 'select *  from tbl_cargo_in where (tbl_cargo_in.cargo_status = :status1  )';

        $params = [
            "status1" => $status1,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }

    /**
     * get All Cargos User By User Id
     *
     * @param $userID
     *
     * @return Object
     * @author  Amir
     * @version 2.0.0
     */
    public static function getAllCargosUserByUserId($userID)
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_cargo` WHERE `user_id`=:user_id ORDER BY cargo_id  DESC ;";
        $params = [
            'user_id' => $userID
        ];


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get Get Cargo By ID
     *
     * @param null $status
     *
     * @return Object
     * @author  Amir
     * @version 1.0.0
     */
    public static function getCargoByID($cargoID)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_cargo` WHERE `cargo_id`=:cargo_id ;";
        $params = [
            'cargo_id' => $cargoID
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    /**
     * Edit Cargo Info By Admin
     *
     * @param $cargoID
     * @param $type
     * @param $newValue
     *
     * @return Object
     * @author  Amir
     * @version 1.0.0
     */
    public static function editCargoInfoByAdmin($cargoID, $type, $newValue)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = 'UPDATE `tbl_cargo` SET ' . $type . '=:newValue WHERE `cargo_id`=:cargo_id';
        $params = [
            'newValue' => $newValue,
            'cargo_id' => $cargoID,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }
        return $response;
    }

    /**
     * get All Request Cargo By ID
     * @param $cargoID
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getAllRequestCargoByID($cargoID)
    {
        $response = sendResponse(0, "Error Msg",[]);
        $sql = "SELECT * FROM `tbl_requests` WHERE `cargo_id`=:cargo_id ;";
        $params = [
            'cargo_id' => $cargoID
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    /**
     * get All Extra Expenses By Cargo Id
     * @param $cargoID
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getAllExtraExpensesByCargoId($cargoID)
    {
        $response = sendResponse(0, "Error Msg",[]);
        $sql = "SELECT * FROM `tbl_extra_expenses` WHERE `cargo_id`=:cargo_id ;";
        $params = [
            'cargo_id' => $cargoID
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    /**
     * update Cargo Lat & Lang
     * @param $cargoID
     * @param $column
     * @param $value
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function updateCargoLatLong($cargoID, $column, $value)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "UPDATE `tbl_cargo` SET  {$column}=:newValue WHERE `cargo_id`=:cargo_id";
        $params = [
            'newValue' => $value,
            'cargo_id' => $cargoID
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    /**
     * Get Request Images
     * @param $requestId
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getRequestImage($requestId)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT tbl_requests.request_images AS images FROM `tbl_requests` WHERE `request_id`=:request_id ;";
        $params = [
            'request_id' => $requestId
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Update Request Status By Request ID
     * @param $requestId
     * @param $status
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function updateRequestStatus($requestId, $status)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "UPDATE `tbl_requests` SET `request_status`=:request_status WHERE `request_id`=:request_id";
        $params = [
            'request_status' => $status,
            'request_id' => $requestId
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    /**
     * Update Extra Expenses Status By Expense ID
     * @param $requestId
     * @param $status
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function updateExtraExpensesStatus($idExtra, $status)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "UPDATE `tbl_extra_expenses` SET `expense_status`=:expense_status WHERE `expense_id`=:expense_id";
        $params = [
            'expense_status' => $status,
            'expense_id' => $idExtra
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    /**
     * Get All Requests And Cargoes
     * @param null $status
     * @return Object
     * @author Amir
     * @version 2.0.0
     */
    public static function getAllRequests($status = null)
    {
        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT * FROM `tbl_requests` INNER JOIN `tbl_cargo` ON tbl_requests.cargo_id=tbl_cargo.cargo_id ORDER BY request_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_requests` INNER JOIN `tbl_cargo` ON tbl_requests.cargo_id=tbl_cargo.cargo_id WHERE `request_status`=:request_status ORDER BY request_id DESC ;";
            $params = [
                'request_status' => $status
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    /**
     * Get All Requests And Cargoes
     * @param null $status
     * @return Object
     * @author Amir
     * @version 2.0.0
     */
    public static function getAllRequestsCount($status = null)
    {
        $response = 0;
        if (empty($status)) {
            $sql = "SELECT count(*) as request_count FROM `tbl_requests` INNER JOIN `tbl_cargo` ON tbl_requests.cargo_id=tbl_cargo.cargo_id  ;";
            $params = [];
        } else {
            $sql = "SELECT count(*) as request_count FROM `tbl_requests` INNER JOIN `tbl_cargo` ON tbl_requests.cargo_id=tbl_cargo.cargo_id WHERE `request_status`=:request_status ;";
            $params = [
                'request_status' => $status
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response =  $result->response[0]->request_count;
        }
        return $response;
    }


    /**
     * Get All Requests And Cargoes
     * @param null $status
     * @return Object
     * @author Amir
     * @version 2.0.0
     */
    public static function getAllRequestsInCount($status = null)
    {
        $response =0;
        if (empty($status)) {
            $sql = "SELECT count(*) as request_count  FROM `tbl_requests_in` INNER JOIN `tbl_cargo_in` ON tbl_requests_in.cargo_id=tbl_cargo_in.cargo_id  ;";
            $params = [];
        } else {
            $sql = "SELECT  count(*) as request_count FROM `tbl_requests_in` INNER JOIN `tbl_cargo_in` ON tbl_requests_in.cargo_id=tbl_cargo_in.cargo_id WHERE `request_status`=:request_status  ;";
            $params = [
                'request_status' => $status
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response =$result->response[0]->request_count;
        }
        return $response;
    }


    /**
     * Get Driver All Requests
     * @param $userID
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getDriverRequestsByUserID($userID)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_requests` INNER JOIN `tbl_cargo` ON tbl_requests.cargo_id=tbl_cargo.cargo_id WHERE tbl_requests.user_id=:user_id ORDER BY request_id DESC ;";
        $params = [
            'user_id' => $userID
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get Request By ID
     * @param $requestID int
     * @return Object
     * @author Amir
     * @version 2.0.0
     */
    public static function getRequestByID($requestID)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_requests` WHERE request_id=:request_id ";
        $params = [
            'request_id' => $requestID
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response[0]);
        }
        return $response;

    }


    /**
     * Get Count Cargoes By Status
     * @param $type
     * @param $status
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getCountCargoes($status)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT count(*) AS count FROM `tbl_cargo` WHERE cargo_status=:cargo_status ";
        $params = [
            'cargo_status' => $status,
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
     * Get Driver Cargos By User ID
     * @param $userID
     * @return stdClass
     */
    public static function getDriverCargoByUserID($userID)
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_cargo` INNER JOIN `tbl_requests` ON tbl_requests.cargo_id=tbl_cargo.cargo_id 
                WHERE tbl_requests.user_id=:user_id AND (request_status=:request_status || request_status=:request_status2 || request_status=:request_status3 || request_status=:request_status4) ORDER BY tbl_cargo.cargo_id DESC";
        $params = [
            'user_id' => $userID,
            'request_status' => 'accepted',
            'request_status2' => 'progress',
            'request_status3' => 'canceled',
            'request_status4' => 'completed',
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {

            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;

    }

    /**
     * Get All Cargo For Home
     * @param $status
     *
     * @return \stdClass
     */
    public static function getAllCargoByLimit($status = null, $limitOffest = 0, $limitCount = 10)
    {
        /**
         * SELECT city_id, ( 3959 * acos( cos( radians(37.9041424) ) * cos( radians( tbl_cities.city_lat ) ) * cos( radians( tbl_cities.city_long ) - radians(46.143578) ) + sin( radians(37.9041424) ) * sin( radians(  tbl_cities.city_lat ) ) ) ) AS distance FROM tbl_cities HAVING distance < 100000 ORDER BY distance;
         */
        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT * FROM `tbl_cargo` inner join tbl_car_types ON tbl_car_types.type_id = tbl_cargo.type_id inner join tbl_cargo_categories on tbl_cargo_categories.category_id = tbl_cargo.category_id inner join tbl_currency on tbl_currency.currency_id = tbl_cargo.cargo_monetary_unit ORDER BY tbl_cargo.cargo_id DESC limit $limitOffest , $limitCount ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_cargo` inner join tbl_car_types ON tbl_car_types.type_id = tbl_cargo.type_id inner join tbl_cargo_categories on tbl_cargo_categories.category_id = tbl_cargo.category_id inner join tbl_currency on tbl_currency.currency_id = tbl_cargo.cargo_monetary_unit WHERE `cargo_status`=:cargo_status ORDER BY tbl_cargo.cargo_id  DESC LIMIT $limitOffest , $limitCount;";
            $params = [
                'cargo_status' => $status
            ];
        }


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get All Cargo For Home
     * @param $status
     *
     * @return \stdClass
     */
    public static function getAllCargoByLimitAndStatus($limit = 10)
    {
        /**
         * SELECT city_id, ( 3959 * acos( cos( radians(37.9041424) ) * cos( radians( tbl_cities.city_lat ) ) * cos( radians( tbl_cities.city_long ) - radians(46.143578) ) + sin( radians(37.9041424) ) * sin( radians(  tbl_cities.city_lat ) ) ) ) AS distance FROM tbl_cities HAVING distance < 100000 ORDER BY distance;
         */
        $response = sendResponse(0, "Error Msg");
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;

        $sql = "SELECT * FROM `tbl_cargo` 
                    inner join tbl_car_types ON tbl_car_types.type_id = tbl_cargo.type_id 
                    inner join tbl_cargo_categories on tbl_cargo_categories.category_id = tbl_cargo.category_id 
                    inner join tbl_currency on tbl_currency.currency_id = tbl_cargo.cargo_monetary_unit 
                    WHERE `cargo_status`=:cargo_progress || `cargo_status`=:cargo_accepted and $slugname IS NOT NULL  ORDER BY tbl_cargo.cargo_id  DESC limit {$limit};";
        $params = [
            'cargo_progress' => 'progress',
            'cargo_accepted' => 'accepted',

        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function getAllCargoInByLimitAndStatus($limit = 10)
    {
        /**
         * SELECT city_id, ( 3959 * acos( cos( radians(37.9041424) ) * cos( radians( tbl_cities.city_lat ) ) * cos( radians( tbl_cities.city_long ) - radians(46.143578) ) + sin( radians(37.9041424) ) * sin( radians(  tbl_cities.city_lat ) ) ) ) AS distance FROM tbl_cities HAVING distance < 100000 ORDER BY distance;
         */
        $response = sendResponse(0, "Error Msg");
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        $sql = "SELECT * FROM `tbl_cargo_in` 
                    inner join tbl_car_types ON tbl_car_types.type_id = tbl_cargo_in.type_id 
                    inner join tbl_cargo_categories on tbl_cargo_categories.category_id = tbl_cargo_in.category_id 
                    inner join tbl_currency on tbl_currency.currency_id = tbl_cargo_in.cargo_monetary_unit 
                    WHERE `cargo_status`=:cargo_progress || `cargo_status`=:cargo_accepted and $slugname IS NOT NULL  ORDER BY tbl_cargo_in.cargo_id  DESC limit {$limit};";
        $params = [
            'cargo_progress' => 'progress',
            'cargo_accepted' => 'accepted',

        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    /**
     * get Count Cargo Status From Chart
     * @return array|int[]
     */
    public static function getCountCargoFromChart()
    {

        $sql = "SELECT COUNT(*) AS countAll,
                    (SELECT COUNT(*) FROM `tbl_cargo` WHERE cargo_status=:status_accepted) AS countaccepted,
                    (SELECT COUNT(*) FROM `tbl_cargo` WHERE cargo_status=:status_pending) AS countpending,
                    (SELECT COUNT(*) FROM `tbl_cargo` WHERE cargo_status=:status_rejected) AS countrejected,
                    (SELECT COUNT(*) FROM `tbl_cargo` WHERE cargo_status=:status_completed) AS countcompleted,
                    (SELECT COUNT(*) FROM `tbl_cargo` WHERE cargo_status=:status_canceled) AS countcanceled,
                    (SELECT COUNT(*) FROM `tbl_cargo` WHERE cargo_status=:status_progress) AS countprogress,
                    (SELECT COUNT(*) FROM `tbl_cargo` WHERE cargo_status=:status_expired) AS countexpired
                    FROM tbl_cargo";
        $params = [
            'status_accepted' => "accepted",
            'status_pending' => "pending",
            'status_rejected' => "rejected",
            'status_completed' => "completed",
            'status_canceled' => "canceled",
            'status_progress' => "progress",
            'status_expired' => "expired",
        ];


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            return [
                'all' => $result->response[0]->countAll,
                'accepted' => $result->response[0]->countaccepted,
                'rejected' => $result->response[0]->countrejected,
                'pending' => $result->response[0]->countpending,
                'completed' => $result->response[0]->countcompleted,
                'canceled' => $result->response[0]->countcanceled,
                'progress' => $result->response[0]->countprogress,
                'expired' => $result->response[0]->countexpired,
            ];
        }

        return [
            'all' => 0,
            'accepted' => 0,
            'rejected' => 0,
            'pending' => 0,
            'completed' => 0,
            'canceled' => 0,
            'progress' => 0,
            'expired' => 0,
        ];
    }

    /**
     * update Cargo Lat & Lang
     * @param $cargoID
     * @param $column
     * @param $value
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function updateCargoStatus($cargoID, $status = 'expired')
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "UPDATE `tbl_cargo` SET  cargo_status=:status WHERE `cargo_id`=:cargo_id";
        $params = [
            'status' => $status,
            'cargo_id' => $cargoID
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, "Successful",);
        }
        return $response;
    }
    /**
     * update Cargo Lat & Lang
     * @param $cargoID
     * @param $column
     * @param $value
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function updateCargoInStatus($cargoID, $status = 'expired')
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "UPDATE `tbl_cargo_in` SET  cargo_status=:status WHERE `cargo_id`=:cargo_id";
        $params = [
            'status' => $status,
            'cargo_id' => $cargoID
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, "Successful",);
        }
        return $response;
    }

    /**
     * Get All Cars
     *
     * @param null $status
     *
     * @return Object
     * @author  Amir
     * @version 2.0.0
     */
    public static function getAllCargoCronJob()
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT * FROM `tbl_cargo` WHERE `cargo_status`=:cargo_status or  cargo_status=:cargo_status2 ORDER BY cargo_id  DESC ;";
        $params = [
            'cargo_status' => 'accepted',
            'cargo_status2' => 'progress'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }
    public static function getAllCargoForRing()
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT * FROM `tbl_cargo` WHERE `cargo_status`=:cargo_status  ORDER BY cargo_id  DESC ;";
        $params = [
            'cargo_status' => 'accepted',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    public static function rejectedRequestsAfterCargoExpired($cargo_id)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "UPDATE `tbl_requests` SET `request_status`=:request_status WHERE `cargo_id`=:cargo_id AND `request_status`='pending' ";
        $params = [
            'request_status' => 'rejected',
            'cargo_id' => $cargo_id
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            file_put_contents('public_html/db/log_update_rejected_request_200.txt', $cargo_id . ',', FILE_APPEND);
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }
    public static function rejectedRequestsAfterCargoInExpired($cargo_id)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "UPDATE `tbl_requests_in` SET `request_status`=:request_status WHERE `cargo_id`=:cargo_id AND `request_status`='pending' ";
        $params = [
            'request_status' => 'rejected',
            'cargo_id' => $cargo_id
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            file_put_contents('public_html/db/log_update_rejected_request_in_200.txt', $cargo_id . ',', FILE_APPEND);
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }

    public static function getCargoAllRequest($cargo_id)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_requests` WHERE `cargo_id` =:cargo_id ";
        $params = [
            'cargo_id' => $cargo_id
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    public static function updateCargoDescById($cargoId, $desc)
    {
        $response = sendResponse(0, "Error Msg", []);

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $res = self::getCargoByID($cargoId)->response[0];
        $temp = [];
        if (!empty($res)) {
            $temp = $res->cargo_admin_desc;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];

            $a['admin'] = $admin_id;
            $a['type'] = 'desc';
            $a['desc'] = $desc;
            $a['date'] = time();
            array_push($array, $a);

        } else {
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = 'desc';
            $a['desc'] = $desc;
            $a['date'] = time();
            array_push($array, $a);
        }


        $sql = "update `tbl_cargo` set cargo_admin_desc=:options WHERE `cargo_id`=:id ";
        $params = [
            'options' => json_encode($array),
            'id' => $cargoId,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    public static function updateCargoInDescById($cargoId, $desc)
    {
        $response = sendResponse(0, "Error Msg", []);

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $res = self::getCargoInByID($cargoId)->response[0];
        $temp = [];
        if (!empty($res)) {
            $temp = $res->cargo_admin_desc;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];

            $a['admin'] = $admin_id;
            $a['type'] = 'desc';
            $a['desc'] = $desc;
            $a['date'] = time();
            array_push($array, $a);

        } else {
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = 'desc';
            $a['desc'] = $desc;
            $a['date'] = time();
            array_push($array, $a);
        }


        $sql = "update `tbl_cargo_in` set cargo_admin_desc=:options WHERE `cargo_id`=:id ";
        $params = [
            'options' => json_encode($array),
            'id' => $cargoId,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    public static function updateCargoOptionsById($cargoId, $type, $new)
    {
        $response = sendResponse(0, "Error Msg", []);

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCargoByID($cargoId)->response[0];
        $temp = [];
        if (!empty($res)) {
            $temp = $res->cargo_options;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];

            $a['admin'] = $admin_id;
            $a['type'] = $type;
            $a['old'] = $res->$type;
            $a['new'] = $new;
            $a['data'] = null;
            $a['date'] = time();
            array_push($array, $a);

        } else {
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = $type;
            $a['old'] = $res->$type;
            $a['new'] = $new;
            $a['data'] = null;
            $a['date'] = time();
            array_push($array, $a);
        }


        $sql = "update `tbl_cargo` set `cargo_options`=:options WHERE `cargo_id`=:id ";
        $params = [
            'options' => json_encode($array),
            'id' => $cargoId,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }

    public static function updateCargoOptionsRequestById($requestId, $new)
    {
        $response = sendResponse(0, "Error Msg", []);

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }
        $data = self::getRequestByID($requestId)->response;

        $res = self::getCargoByID($data->cargo_id)->response[0];
        $temp = [];
        if (!empty($res)) {
            $temp = $res->cargo_options;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];

            $a['admin'] = $admin_id;
            $a['type'] = 'request_id';
            $a['old'] = $data->request_status;
            $a['new'] = $new;
            $a['data'] = $requestId;
            $a['date'] = time();
            array_push($array, $a);

        } else {
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = 'request_id';
            $a['old'] = $data->request_status;
            $a['new'] = $new;
            $a['data'] = $requestId;
            $a['date'] = time();
            array_push($array, $a);
        }


        $sql = "update `tbl_cargo` set `cargo_options`=:options WHERE `cargo_id`=:id ";
        $params = [
            'options' => json_encode($array),
            'id' => $data->cargo_id,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }

    public static function updateCargoOptionsExtraExpensesById($idExtra, $new)
    {
        $response = sendResponse(0, "Error Msg", []);

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }
        $data = self::getExtraExpensesByID($idExtra)->response;

        $res = self::getCargoByID($data->cargo_id)->response[0];
        $temp = [];
        if (!empty($res)) {
            $temp = $res->cargo_options;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];

            $a['admin'] = $admin_id;
            $a['type'] = 'expense_id';
            $a['old'] = $data->expense_status;
            $a['new'] = $new;
            $a['data'] = $idExtra;
            $a['date'] = time();
            array_push($array, $a);

        } else {
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = 'expense_id';
            $a['old'] = $data->expense_status;
            $a['new'] = $new;
            $a['data'] = $idExtra;
            $a['date'] = time();
            array_push($array, $a);
        }


        $sql = "update `tbl_cargo` set `cargo_options`=:options WHERE `cargo_id`=:id ";
        $params = [
            'options' => json_encode($array),
            'id' => $data->cargo_id,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    public static function getExtraExpensesByID($idExtra)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_extra_expenses` WHERE expense_id=:expense_id ";
        $params = [
            'expense_id' => $idExtra
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response[0]);
        }
        return $response;

    }


    public static function getCountCargoFromCensus($max, $min)
    {

        $sql = "SELECT
                    (SELECT COUNT(*) FROM `tbl_cargo` WHERE `cargo_date` <= :max AND `cargo_date` >= :min AND cargo_status=:accepted) AS acceptedCount,
                    (SELECT COUNT(*) FROM `tbl_cargo` WHERE `cargo_date` <= :max AND `cargo_date` >= :min AND cargo_status=:pending) AS pendingCount,
                    (SELECT COUNT(*) FROM `tbl_cargo` WHERE `cargo_date` <= :max AND `cargo_date` >= :min AND cargo_status=:rejected) AS rejectedCount,
                    (SELECT COUNT(*) FROM `tbl_cargo` WHERE `cargo_date` <= :max AND `cargo_date` >= :min AND cargo_status=:progress) AS progressCount,
                    (SELECT COUNT(*) FROM `tbl_cargo` WHERE `cargo_date` <= :max AND `cargo_date` >= :min AND cargo_status=:canceled) AS canceledCount,
                    (SELECT COUNT(*) FROM `tbl_cargo` WHERE `cargo_date` <= :max AND `cargo_date` >= :min AND cargo_status=:completed) AS completedCount,
                    (SELECT COUNT(*) FROM `tbl_cargo` WHERE `cargo_date` <= :max AND `cargo_date` >= :min AND cargo_status=:expired) AS expiredCount
                    FROM tbl_cargo ";
        $params = [
            'max' => $max,
            'min' => $min,
            'accepted' => "accepted",
            'pending' => "pending",
            'rejected' => "rejected",
            'progress' => "progress",
            'canceled' => "canceled",
            'completed' => "completed",
            'expired' => "expired",
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            return [
                'accepted' => $result->response[0]->acceptedCount,
                'pending' => $result->response[0]->pendingCount,
                'rejected' => $result->response[0]->rejectedCount,
                'progress' => $result->response[0]->progressCount,
                'canceled' => $result->response[0]->canceledCount,
                'completed' => $result->response[0]->completedCount,
                'expired' => $result->response[0]->expiredCount
            ];
        }

        return [
            'accepted' => 0,
            'pending' => 0,
            'rejected' => 0,
            'progress' => 0,
            'canceled' => 0,
            'completed' => 0,
            'expired' => 0,
        ];
    }


    public static function getCargoCategoryFromCensus($max, $min)
    {
        $response = [];
        $sql = "SELECT * FROM `tbl_cargo` INNER JOIN `tbl_cargo_categories` ON tbl_cargo.category_id=tbl_cargo_categories.category_id WHERE   `cargo_date` <= :max AND `cargo_date` >= :min ";
        $params = [
            'max' => $max,
            'min' => $min,
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = $result->response;
        }
        return $response;

    }

    public static function getCargoTypeFromCensus($max, $min)
    {
        $response = [];
        $sql = "SELECT * FROM `tbl_cargo` INNER JOIN `tbl_car_types` ON tbl_cargo.type_id=tbl_car_types.type_id WHERE   `cargo_date` <= :max AND `cargo_date` >= :min ";
        $params = [
            'max' => $max,
            'min' => $min,
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = $result->response;
        }
        return $response;

    }


    /**
     * Start Cargo In
     */
    public static function getCountCargoInFromChart()
    {
        $sql = "SELECT COUNT(*) AS countAll,
                    (SELECT COUNT(*) FROM `tbl_cargo_in` WHERE cargo_status=:status_accepted) AS countaccepted,
                    (SELECT COUNT(*) FROM `tbl_cargo_in` WHERE cargo_status=:status_pending) AS countpending,
                    (SELECT COUNT(*) FROM `tbl_cargo_in` WHERE cargo_status=:status_rejected) AS countrejected,
                    (SELECT COUNT(*) FROM `tbl_cargo_in` WHERE cargo_status=:status_completed) AS countcompleted,
                    (SELECT COUNT(*) FROM `tbl_cargo_in` WHERE cargo_status=:status_canceled) AS countcanceled,
                    (SELECT COUNT(*) FROM `tbl_cargo_in` WHERE cargo_status=:status_progress) AS countprogress,
                    (SELECT COUNT(*) FROM `tbl_cargo_in` WHERE cargo_status=:status_expired) AS countexpired
                    FROM tbl_cargo_in";
        $params = [
            'status_accepted' => "accepted",
            'status_pending' => "pending",
            'status_rejected' => "rejected",
            'status_completed' => "completed",
            'status_canceled' => "canceled",
            'status_progress' => "progress",
            'status_expired' => "expired",
        ];


        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            return [
                'all' => $result->response[0]->countAll,
                'accepted' => $result->response[0]->countaccepted,
                'rejected' => $result->response[0]->countrejected,
                'pending' => $result->response[0]->countpending,
                'completed' => $result->response[0]->countcompleted,
                'canceled' => $result->response[0]->countcanceled,
                'progress' => $result->response[0]->countprogress,
                'expired' => $result->response[0]->countexpired,
            ];
        }
        return [
            'all' => 0,
            'accepted' => 0,
            'rejected' => 0,
            'pending' => 0,
            'completed' => 0,
            'canceled' => 0,
            'progress' => 0,
            'expired' => 0,
        ];
    }


    public static function getAllCargoIn($status = null)
    {
        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT * FROM `tbl_cargo_in` ORDER BY cargo_id  DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_cargo_in` WHERE `cargo_status`=:cargo_status ORDER BY cargo_id  DESC ;";
            $params = [
                'cargo_status' => $status
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function getCargoInByID($cargoID)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_cargo_in` WHERE `cargo_id`=:cargo_id ;";
        $params = [
            'cargo_id' => $cargoID
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function getAllRequestCargoInByID($cargoID)
    {
        $response = sendResponse(0, "Error Msg",[]);
        $sql = "SELECT * FROM `tbl_requests_in` WHERE `cargo_id`=:cargo_id ;";
        $params = [
            'cargo_id' => $cargoID
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function getAllExtraExpensesByCargoInId($cargoID)
    {
        $response = sendResponse(0, "Error Msg",[]);
        $sql = "SELECT * FROM `tbl_extra_expenses_in` WHERE `cargo_id`=:cargo_id ;";
        $params = [
            'cargo_id' => $cargoID
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function getCargoInAllRequest($cargo_id)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_requests_in` WHERE `cargo_id` =:cargo_id ";
        $params = [
            'cargo_id' => $cargo_id
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function updateCargoInOptionsById($cargoId, $type, $new)
    {
        $response = sendResponse(0, "Error Msg", []);

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCargoInByID($cargoId)->response[0];
        $temp = [];
        if (!empty($res)) {
            $temp = $res->cargo_options;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];

            $a['admin'] = $admin_id;
            $a['type'] = $type;
            $a['old'] = $res->$type;
            $a['new'] = $new;
            $a['data'] = null;
            $a['date'] = time();
            array_push($array, $a);

        } else {
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = $type;
            $a['old'] = $res->$type;
            $a['new'] = $new;
            $a['data'] = null;
            $a['date'] = time();
            array_push($array, $a);
        }


        $sql = "update `tbl_cargo_in` set `cargo_options`=:options WHERE `cargo_id`=:id ";
        $params = [
            'options' => json_encode($array),
            'id' => $cargoId,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    public static function editCargoInInfoByAdmin($cargoID, $type, $newValue)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = 'UPDATE `tbl_cargo_in` SET ' . $type . '=:newValue WHERE `cargo_id`=:cargo_id';
        $params = [
            'newValue' => $newValue,
            'cargo_id' => $cargoID,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }
        return $response;
    }

    public static function getRequestInImage($requestId)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT tbl_requests_in.request_images AS images FROM `tbl_requests_in` WHERE `request_id`=:request_id ;";
        $params = [
            'request_id' => $requestId
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    public static function updateCargoInOptionsRequestById($requestId, $new)
    {
        $response = sendResponse(0, "Error Msg", []);

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }
        $data = self::getRequestInByID($requestId)->response;

        $res = self::getCargoInByID($data->cargo_id)->response[0];
        $temp = [];
        if (!empty($res)) {
            $temp = $res->cargo_options;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];

            $a['admin'] = $admin_id;
            $a['type'] = 'request_id';
            $a['old'] = $data->request_status;
            $a['new'] = $new;
            $a['data'] = $requestId;
            $a['date'] = time();
            array_push($array, $a);

        } else {
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = 'request_id';
            $a['old'] = $data->request_status;
            $a['new'] = $new;
            $a['data'] = $requestId;
            $a['date'] = time();
            array_push($array, $a);
        }


        $sql = "update `tbl_cargo_in` set `cargo_options`=:options WHERE `cargo_id`=:id ";
        $params = [
            'options' => json_encode($array),
            'id' => $data->cargo_id,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }

    public static function getRequestInByID($requestID)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_requests_in` WHERE request_id=:request_id ";
        $params = [
            'request_id' => $requestID
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response[0]);
        }
        return $response;

    }

    public static function updateRequestInStatus($requestId, $status)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "UPDATE `tbl_requests_in` SET `request_status`=:request_status WHERE `request_id`=:request_id";
        $params = [
            'request_status' => $status,
            'request_id' => $requestId
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }

    public static function updateCargoInOptionsExtraExpensesById($idExtra, $new)
    {
        $response = sendResponse(0, "Error Msg", []);

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }
        $data = self::getExtraExpensesInByID($idExtra)->response;

        $res = self::getCargoInByID($data->cargo_id)->response[0];
        $temp = [];
        if (!empty($res)) {
            $temp = $res->cargo_options;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];

            $a['admin'] = $admin_id;
            $a['type'] = 'expense_id';
            $a['old'] = $data->expense_status;
            $a['new'] = $new;
            $a['data'] = $idExtra;
            $a['date'] = time();
            array_push($array, $a);

        } else {
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = 'expense_id';
            $a['old'] = $data->expense_status;
            $a['new'] = $new;
            $a['data'] = $idExtra;
            $a['date'] = time();
            array_push($array, $a);
        }


        $sql = "update `tbl_cargo_in` set `cargo_options`=:options WHERE `cargo_id`=:id ";
        $params = [
            'options' => json_encode($array),
            'id' => $data->cargo_id,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }

    public static function getExtraExpensesInByID($idExtra)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_extra_expenses_in` WHERE expense_id=:expense_id ";
        $params = [
            'expense_id' => $idExtra
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response[0]);
        }
        return $response;
    }


    public static function updateExtraExpensesInStatus($idExtra, $status)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "UPDATE `tbl_extra_expenses_in` SET `expense_status`=:expense_status WHERE `expense_id`=:expense_id";
        $params = [
            'expense_status' => $status,
            'expense_id' => $idExtra
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    public static function updateCargoInLatLong($cargoID, $column, $value)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "UPDATE `tbl_cargo_in` SET  {$column}=:newValue WHERE `cargo_id`=:cargo_id";
        $params = [
            'newValue' => $value,
            'cargo_id' => $cargoID
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    public static function getCountCargoesIn($status)
    {
        $response = sendResponse(0, "Error Msg",0);
        $sql = "SELECT count(*) AS count FROM `tbl_cargo_in` WHERE cargo_status=:cargo_status ";
        $params = [
            'cargo_status' => $status,
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

    public static function getDriverCargoInByUserID($userID)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT * FROM `tbl_cargo_in` INNER JOIN `tbl_requests_in` ON tbl_requests_in.cargo_id=tbl_cargo_in.cargo_id 
                WHERE tbl_requests_in.user_id=:user_id AND (request_status=:request_status || request_status=:request_status2 || request_status=:request_status3 || request_status=:request_status4) ORDER BY tbl_cargo_in.cargo_id DESC";
        $params = [
            'user_id' => $userID,
            'request_status' => 'accepted',
            'request_status2' => 'progress',
            'request_status3' => 'canceled',
            'request_status4' => 'completed',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function getAllCargosInUserByUserId($userID)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT * FROM `tbl_cargo_in` WHERE `user_id`=:user_id ORDER BY cargo_id  DESC ;";
        $params = [
            'user_id' => $userID
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    public static function getDriverRequestsInByUserID($userID)
    {
        $response = sendResponse(0, "Error Msg",[]);
        $sql = "SELECT * FROM `tbl_requests_in`  INNER JOIN `tbl_cargo_in` ON tbl_requests_in.cargo_id=tbl_cargo_in.cargo_id WHERE tbl_requests_in.user_id=:user_id ORDER BY request_id DESC ;";
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