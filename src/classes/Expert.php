<?php

use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Expert
{

    public static function getAllExperts()
    {
        $response = [];
        $sql = "SELECT * FROM tbl_experts";
        $params = [];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response;
        }
        return $response;
    }

    public static function SetNewExpert($firstname, $lastname, $mobile, $address, $description, $status)
    {
        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $array = [];
        $array['admin'] = $admin_id;
        $array['date_create'] = time();
        $array['update'] = [];

        if ($admin_id > 0) {
            $sql = "INSERT INTO `tbl_experts`(`expert_firstname`, `expert_lastname`, `expert_mobile`, `expert_address`, `expert_desc`,`expert_status`, `expert_register_date`, `expert_options`)
                    VALUES (:expert_firstname,:expert_lastname,:expert_mobile,:expert_address,:expert_desc,:expert_status,:expert_register_date,:expert_options)";
            $params = [
                'expert_firstname' => $firstname,
                'expert_lastname' => $lastname,
                'expert_mobile' => $mobile,
                'expert_address' => $address,
                'expert_desc' => $description,
                'expert_status' => $status,
                'expert_register_date' => time(),
                'expert_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);
            if ($result->status == 200) {
                $response = sendResponse(200, "Successful", $result->response);
            }
        }
        return $response;
    }

    public static function getExpertById($id)
    {
        $response = sendResponse(0, "");
        $sql = "SELECT * FROM `tbl_experts` WHERE `expert_id`=:expert_id";
        $params = [
            'expert_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }
        return $response;
    }

    public static function editExpertById($id, $firstname, $lastname, $mobile, $address, $description, $status)
    {
        $response = sendResponse(0, "");
        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }
        $res = self::getExpertById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->expert_options;
        }

        if (!empty($temp)) {
            $value = json_decode($temp, true);
            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));
        } else {
            $value['update'] = [];
            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));
        }

        if ($admin_id > 0) {
            $sql = 'UPDATE `tbl_experts` SET `expert_firstname`=:expert_firstname,`expert_lastname`=:expert_lastname,
                         `expert_mobile`=:expert_mobile,`expert_address`=:expert_address,`expert_desc`=:expert_desc,
                         `expert_status`=:expert_status,`expert_options`=:expert_options WHERE `expert_id`=:expert_id';
            $params = [
                'expert_firstname' => $firstname,
                'expert_lastname' => $lastname,
                'expert_mobile' => $mobile,
                'expert_address' => $address,
                'expert_desc' => $description,
                'expert_status' => $status,
                'expert_options' => json_encode($value),
                'expert_id' => $id,
            ];
            $result = DB::update($sql, $params);
            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }

    public static function getAllActiveExperts()
    {
        $response = [];
        $sql = "SELECT * FROM tbl_experts WHERE expert_status=:expert_status ";
        $params = [
            'expert_status'=>'active',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response;
        }
        return $response;
    }
}