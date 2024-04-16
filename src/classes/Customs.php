<?php

use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Customs
{

    public static function getAllTransportationsByStatus($status = 'active')
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_transportations`  where category_status  = :status ";
        $params = [
            'status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function insertInquiryCustoms($userId, $name, $transportationId, $startDate, $commodity, $token, $description = null)
    {
        if (!Security::verifyCSRF('inquiry-customs', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('inquiry-customs');

        $sql = "INSERT INTO `tbl_freight_customs`(`user_id`, `transportation_id`,`freight_customs`, `freight_description`,
                `freight_start_date`, `freight_submit_date`, `freight_status`)
                VALUES (:user_id,:transportation_id,:freight_customs,:freight_description,:freight_start_date,:freight_submit_date,:freight_status)";
        $params = [
            'user_id' => $userId,
            'transportation_id' => $transportationId,
            'freight_customs' => $name,
            'freight_description' => $description,
            'freight_start_date' => $startDate,
            'freight_submit_date' => time(),
            'freight_status' => 'pending',
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $commodity__ = json_decode($commodity);
            foreach ($commodity__ as $loop) {
                self::setFreightCustomsValues($result->response, $loop->category, $loop->code, $loop->name, $loop->weight, $loop->weightslug, $loop->volume);
            }
            $response = sendResponse(200, 'Cargo submitted successfully');
            User::createUserLog($userId, 'u_submit_inquiry_customs', 'cargo');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }

    private static function setFreightCustomsValues($freightId, $categoryId, $hscode, $name, $weight, $weightSlug, $volume)
    {

        $sql = "INSERT INTO `tbl_freight_customs_values`(`freight_id`,`category_id`, `value_hscode`, `value_name`, `value_weight`, 
                `value_weight_slug`, `value_volume`) 
                VALUES (:freight_id,:category_id,:value_hscode,:value_name,:value_weight,:value_weight_slug,:value_volume)";
        $params = [
            'freight_id' => $freightId,
            'category_id' => $categoryId,
            'value_hscode' => $hscode,
            'value_name' => $name,
            'value_weight' => $weight,
            'value_weight_slug' => $weightSlug,
            'value_volume' => $volume,
        ];
        $result = DB::insert($sql, $params);
        $flag = [];
        if ($result->status == 200) {
            $flag[] = 200;
        } else {
            $flag[] = -1;
        }
        return $flag;
    }


    public static function getAllInquiryCustomsByStatus($status)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT freight_id,user_mobile,freight_submit_date,user_language,user_firstname,user_lastname,user_avatar 
                FROM `tbl_freight_customs` 
                INNER JOIN tbl_users ON tbl_users.user_id=tbl_freight_customs.user_id 
                WHERE freight_status=:freight_status;";
        $params = [
            'freight_status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function getInquiryInfoById($id)
    {
        $response = sendResponse(200, '');
        $sql = 'SELECT * FROM `tbl_freight_customs` WHERE `freight_id`=:freight_id';
        $params = [
            'freight_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    public static function getAllFreightCustomsValuesById($id)
    {
        $response = sendResponse(200, '', []);
        $sql = 'SELECT * FROM `tbl_freight_customs_values` WHERE `freight_id`=:freight_id';
        $params = [
            'freight_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function editInquiryCustomsInfoByAdmin($inquiryId, $type, $newValue)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = 'UPDATE `tbl_freight_customs` SET ' . $type . '=:newValue WHERE `freight_id`=:freight_id';
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

    public static function changeInquiryCustomsStatusByAdmin($inquiryId, $status)
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

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];

            $a['admin'] = $admin_id;
            $a['type'] = 'expense_id';
            $a['old'] = $statusDefault;
            $a['new'] = $status;
            $a['data'] = null;
            $a['date'] = time();
            array_push($array, $a);

        } else {
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = 'expense_id';
            $a['old'] = $statusDefault;
            $a['new'] = $status;
            $a['data'] = null;
            $a['date'] = time();
            array_push($array, $a);
        }

        $sql = 'UPDATE `tbl_freight_customs` SET `freight_status`=:freight_status,`freight_options`=:freight_options WHERE `freight_id`=:freight_id';
        $params = [
            'freight_options' => json_encode($array),
            'freight_status' => $status,
            'freight_id' => $inquiryId,
        ];

        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }
        return $response;
    }


    public static function InquiryCustomsAddAdminDesc($inquiryId, $desc)
    {
        $response = sendResponse(0, "Error Msg", []);

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getInquiryInfoById($inquiryId)->response[0];
        $temp = [];
        if (!empty($res)) {
            $temp = $res->freight_admin_desc;
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


        $sql = "update `tbl_freight_customs` set `freight_admin_desc`=:options WHERE `freight_id`=:id ";
        $params = [
            'options' => json_encode($array),
            'id' => $inquiryId,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }

    /**
     * @param $user_id
     * @param $status
     * @param $page
     * @param $per_page
     *
     * @return stdClass
     */
    public static function inquiryCustomsList($user_id, $status = 'all')
    {
        $response = sendResponse(200, '', []);


        if ($status == 'all') {
            $sql = "select  tbl_freight_customs.freight_id,freight_status      from tbl_freight_customs
            inner join tbl_freight_customs_values  on tbl_freight_customs.freight_id = tbl_freight_customs_values.freight_id
            where user_id = :user_id 
            order by tbl_freight_customs.freight_id desc";

            $params = [
                'user_id' => $user_id
            ];
        } else {
            $sql = "select  tbl_freight_customs.freight_id,freight_status  from tbl_freight_customs
            inner join tbl_freight_customs_values  on tbl_freight_customs.freight_id = tbl_freight_customs_values.freight_id
            where tbl_freight_customs.user_id = :user_id  and freight_status =:status
            order by tbl_freight_customs.freight_id desc ";

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
                $inquiry->freight_status = $item->freight_status;
                array_push($inquiryList, $inquiry);
            }
            $uniqueArray = array_map("unserialize", array_unique(array_map("serialize", $inquiryList)));
            $response = sendResponse(200, '', $uniqueArray);
        }
        return $response;
    }


    /**
     * @param $cargoId
     *
     * @return stdClass
     */
    public static function getInquiryCustomsDetail($freight_id, $user_id)
    {
        $response = sendResponse(404, '', null);
        $sql = "select  *    from tbl_freight_customs
            inner join tbl_freight_customs_values  on tbl_freight_customs.freight_id = tbl_freight_customs_values.freight_id
            inner join tbl_cargo_categories on tbl_cargo_categories.category_id = tbl_freight_customs_values.category_id 
            where user_id = :user_id  and tbl_freight_customs.freight_id = :freight_id";

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

    public static function getTransportationsByStatus($t_id, $status = 'active')
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_transportations`  where tbl_transportations.category_id = :t_id and category_status  = :status ";
        $params = [
            't_id' => $t_id,
            'status' => $status,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    public static function getSourceInfoForCustoms($custom_id)
    {

        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_customs` INNER join tbl_cities ON tbl_customs.city_id =  tbl_cities.city_id
        INNER JOIN tbl_country  on tbl_country.country_id = tbl_cities.country_id
        WHERE tbl_customs.customs_id = :custom_id";
        $params = [
            'custom_id' => $custom_id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    public static function updateInquiryStatus($freight_id, $status)
    {

        $response = sendResponse(0, "");

        $sql = 'UPDATE tbl_freight_customs set freight_status =:status where freight_id = :freight_id ';
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




    public static function getInquiryCountByStatus($userId, $status)
    {
        $response = 0;

        $sql = "select count(*) as count
        from tbl_freight_customs
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

    public static function getCountInquiryCustomsByStatus($status)
    {
        $response = sendResponse(0, "Error Msg", 0);
        $sql = "SELECT count(*) AS count FROM `tbl_freight_customs` WHERE freight_status=:freight_status;";
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
     * get All Wagon
     * @param null $status
     * @return stdClass
     */
    public static function getAllTransportation($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT category_id, category_name, category_status FROM `tbl_transportations` ORDER BY category_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT category_id, category_name, category_status FROM `tbl_transportations` WHERE `category_status`=:category_status ORDER BY category_id DESC ;";
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
     * Set New transport category
     * @param $title
     * @param $status
     * @return stdClass
     */
    public static function setNewtransportcategory($title, $status)
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

            $sql = 'INSERT INTO `tbl_transportations`(`category_name`, `category_status`, `category_options`) VALUES 
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
     * edit transportation category By Id
     * @param $id
     * @param $title
     * @param $status
     * @return stdClass
     */
    public static function editTransportationById($id, $title, $status)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getTransportationById($id);
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

            $sql = 'UPDATE `tbl_transportations` SET `category_name`=:category_name,`category_status`=:category_status,`category_options`=:category_options
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
    /**
     * Get transportation By Id
     * @param $id
     * @return stdClass
     */
    public static function getTransportationById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_transportations` WHERE tbl_transportations.category_id=:id";
        $params = [
            'id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }

}