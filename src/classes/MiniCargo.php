<?php

use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class MiniCargo
{
    public static function getCountInquiryMiniCargoByStatus($status)
    {
        $response = sendResponse(0, "Error Msg", 0);
        $sql = "SELECT count(*) AS count FROM `tbl_freight_minicargo`  WHERE freight_status=:freight_status;";
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

    public static function insertInquiryMinicargos($userId, $source_city_id, $dest_city_id, $cargo_arrangement, $startDate, $commodity, $token, $description = null)
    {
        if (!Security::verifyCSRF2($token , false)) {
            return sendResponse(-1, 'CSRF-Token error');
        }


        $sql = "INSERT INTO `tbl_freight_minicargo`(`user_id`, `freight_description`,`source_city_id`,`dest_city_id`,`freight_arrangement`,
                `freight_start_date`, `freight_submit_date`, `freight_status`)
                VALUES (:user_id,:freight_description,:source_city_id,:dest_city_id,:freight_arrangement,:freight_start_date,:freight_submit_date,:freight_status)";
        $params = [
            'user_id' => $userId,
            'freight_description' => $description,
            "source_city_id" => $source_city_id,
            "dest_city_id" => $dest_city_id,
            "freight_arrangement" => $cargo_arrangement,
            'freight_start_date' => $startDate,
            'freight_submit_date' => time(),
            'freight_status' => 'pending',
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $commodity__ = json_decode($commodity);
            foreach ($commodity__ as $loop) {
                self::setFreightMiniCargoValues($result->response, $loop->category, $loop->name, $loop->weight, $loop->weightslug, $loop->volume);
            }
            $response = sendResponse(200, 'Cargo submitted successfully');
            User::createUserLog($userId, 'u_submit_inquiry_minicargo', 'cargo');
        } else {
            $response = sendResponse(-10, 'Error', $token);
        }
        return $response;
    }

    private static function setFreightMiniCargoValues($freightId, $categoryId, $name, $weight, $weightSlug, $volume)
    {

        $sql = "INSERT INTO `tbl_freight_minicargo_values`(`freight_id`,`category_id`, `value_name`, `value_weight`, 
                `value_weight_slug`, `value_volume`) 
                VALUES (:freight_id,:category_id,:value_name,:value_weight,:value_weight_slug,:value_volume)";
        $params = [
            'freight_id' => $freightId,
            'category_id' => $categoryId,
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


    public static function getAllInquiryMiniCargoByStatus($status)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT freight_id,user_mobile,freight_submit_date,user_language,user_firstname,user_lastname,user_avatar 
                FROM `tbl_freight_minicargo`  
                INNER JOIN tbl_users ON tbl_users.user_id=tbl_freight_minicargo.user_id 
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
        $sql = 'SELECT * FROM `tbl_freight_minicargo`  WHERE `freight_id`=:freight_id';
        $params = [
            'freight_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    public static function getAllFreightMiniCargoValuesById($id)
    {
        $response = sendResponse(200, '', []);
        $sql = 'SELECT * FROM `tbl_freight_minicargo_values`  WHERE `freight_id`=:freight_id';
        $params = [
            'freight_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function editInquiryMiniCargoInfoByAdmin($inquiryId, $type, $newValue)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = 'UPDATE `tbl_freight_minicargo` SET ' . $type . '=:newValue WHERE `freight_id`=:freight_id';
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

    public static function changeInquiryMiniCargoStatusByAdmin($inquiryId, $status)
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

        $sql = 'UPDATE `tbl_freight_minicargo` SET `freight_status`=:freight_status,`freight_options`=:freight_options WHERE `freight_id`=:freight_id';
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


    public static function InquiryMiniCargoAddAdminDesc($inquiryId, $desc)
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


        $sql = "update `tbl_freight_minicargo` set `freight_admin_desc`=:options WHERE `freight_id`=:id ";
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
    public static function inquiryMiniCargoList($user_id, $status = 'all')
    {
        $response = sendResponse(200, '', []);


        if ($status == 'all') {
            $sql = "select  tbl_freight_minicargo.freight_id,freight_status      from tbl_freight_minicargo
            inner join tbl_freight_minicargo_values  on tbl_freight_minicargo.freight_id = tbl_freight_minicargo_values.freight_id
            where user_id = :user_id 
            order by tbl_freight_minicargo.freight_id desc";

            $params = [
                'user_id' => $user_id
            ];
        } else {
            $sql = "select  tbl_freight_minicargo.freight_id,freight_status  from tbl_freight_minicargo
            inner join tbl_freight_minicargo_values  on tbl_freight_minicargo.freight_id = tbl_freight_minicargo_values.freight_id
            where tbl_freight_minicargo.user_id = :user_id  and freight_status =:status
            order by tbl_freight_minicargo.freight_id desc ";

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
    public static function getInquiryMiniCargoDetail($freight_id, $user_id)
    {
        $response = sendResponse(404, '', null);
        $sql = "select  *    from tbl_freight_minicargo
            inner join tbl_freight_minicargo_values  on tbl_freight_minicargo.freight_id = tbl_freight_minicargo_values.freight_id
            inner join tbl_cargo_categories on tbl_cargo_categories.category_id = tbl_freight_minicargo_values.category_id 
            where user_id = :user_id  and tbl_freight_minicargo.freight_id = :freight_id";

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




    public static function updateInquiryStatus($freight_id, $status)
    {

        $response = sendResponse(0, "");

        $sql = 'UPDATE tbl_freight_minicargo set freight_status =:status where freight_id = :freight_id ';
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
        from tbl_freight_minicargo
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
  
}