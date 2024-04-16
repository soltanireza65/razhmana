<?php


use MJ\Database\DB;
use MJ\Router\Router;
use MJ\Security\Security;
use MJ\SMS\SMS;
use MJ\Utils\Utils;
use function MJ\Keys\sendResponse;

class Ring
{
    public static function insertOrUpdateRing($user_id, $ring_origin_country_id = [], $origin_id = [], $ring_dest_country_id = [], $dest_id = [], $car_types = [], $status)
    {
        if (isset($_COOKIE['user-login'])) {
            if ($ring_origin_country_id == []) {
                $ring_origin_country_id = null;
            } else {
                $ring_origin_country_id = implode(",", $ring_origin_country_id);
            }
            if ($origin_id == []) {
                $origin_id = null;
            } else {
                $origin_id = implode(",", $origin_id);
            }
            if ($ring_dest_country_id == []) {
                $ring_dest_country_id = null;
            } else {
                $ring_dest_country_id = implode(",", $ring_dest_country_id);
            }

            if ($dest_id == []) {
                $dest_id = null;
            } else {
                $dest_id = implode(",", $dest_id);
            }

            if ($car_types == []) {
                $car_types = null;
            } else {
                $car_types = implode(",", $car_types);
            }

            $ring_count = self::getRingCount();

            if ($ring_count == 0) {
                $sql = 'insert into tbl_ring_cargo
    (user_id ,ring_origin_country_id, ring_origin_id ,ring_dest_country_id, ring_dest_id , ring_car_types_ids , ring_submit_time , ring_status)
                        values 
    (:user_id , :ring_origin_country_id,:ring_origin_id ,:ring_dest_country_id, :ring_dest_id , :ring_car_types_ids , :ring_submit_time , :ring_status)
                        ';
                $params = [
                    "user_id" => $user_id,
                    "ring_origin_country_id" => $ring_origin_country_id,
                    "ring_origin_id" => $origin_id,
                    "ring_dest_country_id" => $ring_dest_country_id,
                    "ring_dest_id" => $dest_id,
                    "ring_car_types_ids" => $car_types,
                    "ring_submit_time" => time(),
                    "ring_status" => $status
                ];
                $result = DB::insert($sql, $params);
                if ($result->status == 200) {
                    return sendResponse(200, 'ring add success');
                } else {
                    return sendResponse(-1, $params);
                }
            } elseif ($ring_count == -1) {
                return sendResponse(0, 'err');
            } else {
                $sql = 'UPDATE `tbl_ring_cargo` SET 
                            `ring_origin_country_id`=:ring_origin_country_id,
                            `ring_origin_id`=:ring_origin_id,
                            `ring_dest_country_id`=:ring_dest_country_id,
                            `ring_dest_id`=:ring_dest_id,
                            `ring_car_types_ids`=:ring_car_types_ids
                            ,`ring_submit_time`=:ring_submit_time,
                            `ring_status`=:ring_status WHERE `user_id`=:user_id
                        ';
                $params = [
                    "user_id" => $user_id,
                    "ring_origin_country_id" => $ring_origin_country_id,
                    "ring_origin_id" => $origin_id,
                    "ring_dest_country_id" => $ring_dest_country_id,
                    "ring_dest_id" => $dest_id,
                    "ring_car_types_ids" => $car_types,
                    "ring_submit_time" => time(),
                    "ring_status" => $status
                ];
                $result = DB::update($sql, $params);
                if ($result->status == 200 || $result->status == 208) {
                    return sendResponse(200, 'ring update success');
                } else {
                    return sendResponse(-1, 'sql err2');
                }
            }
        } else {
            return sendResponse(0, 'err');
        }
        return sendResponse(0, 'err');

    }

    public static function getRingCount()
    {
        if (isset($_COOKIE['user-login'])) {
            $user_id = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
            $sql = 'select count(*) as ring_count  from tbl_ring_cargo where user_id = :user_id';
            $params = [
                'user_id' => $user_id
            ];
            $result = DB::rawQuery($sql, $params);
            if ($result->status == 200) {
                return $result->response[0]->ring_count;
            }

        } else {
            return -1;
        }
    }

    public static function expireRing()
    {
        $sql = "UPDATE `tbl_ring_cargo`
SET `ring_status` = 'inactive'
WHERE  (  :now_time - ring_submit_time ) > 259200";

        $params = [
            "now_time" => time()
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            return sendResponse(200, 'ring update success');
        } else {
            return sendResponse(-1, 'sql err2');
        }
    }

    public static function getUserRingDetail()
    {
        if (isset($_COOKIE['user-login'])) {
            $user_id = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
            $sql = 'select *  from tbl_ring_cargo where user_id = :user_id';
            $params = [
                'user_id' => $user_id
            ];
            $result = DB::rawQuery($sql, $params);
            if ($result->status == 200) {
                return sendResponse(200, 'sucess', $result->response);
            }
            return sendResponse(0, 'err', 'user-not-login');
        } else {
            return sendResponse(0, 'err', 'user-not-login');
        }
    }

    public static function getAllRingForCronjob()
    {
        $user_id = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
        $sql = "select *  from tbl_ring_cargo  where ring_status = 'active'";
        $params = [

        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            return $result->response;
        }

    }

    public static function sendRingNotic($cargo_id)
    {
        global $lang;
        $rings = Ring::getAllRingForCronjob();
        foreach ($rings as $ring) {
            //
            // check source city and country  with  null and id
            ///
            $cargo = Cargo::getCargoByID($cargo_id)->response[0];
            $source_country = Location::getCountryByCityId($cargo->cargo_origin_id)->CountryId;
            $desc_country = Location::getCountryByCityId($cargo->cargo_origin_id)->CountryId;
            $flag_source_country_all = false;
            $flag_source_country = false;
            $flag_source_city_all = false;
            $flag_source_city = false;
            if (in_array($source_country, explode(',', $ring->ring_origin_country_id))) {
                $flag_source_country = true;
            }
            if (in_array($cargo->cargo_origin_id, explode(',', $ring->ring_origin_id))) {
                $flag_source_city = true;
            }

            //
            // check dest city and country  with  null and id
            //
            $flag_dest_country_all = false;
            $flag_dest_country = false;
            $flag_dest_city_all = false;
            $flag_dest_city = false;
             if (in_array($desc_country, explode(',', $ring->ring_dest_country_id)) ) {
                $flag_dest_country = true;
            }
            if (in_array($cargo->cargo_destination_id, explode(',', $ring->ring_dest_id))) {
                $flag_dest_city = true;
            }

            //
            // check type_id for car_type
            //
            if (!is_null($ring->ring_car_types_ids)) {
                $car_type_ids = explode(",", $ring->ring_car_types_ids);
            } else {
                $car_type_ids = [];
            }
            $flag_type_car = false;
//            if ($ring->ring_car_types_ids == []) {
//                $flag_type_car = true;
//            }
//            else
            if (in_array($cargo->type_id, $car_type_ids)) {
                $flag_type_car = true;
            }


            $source_final_flag = false;
            if (($flag_source_country_all || $flag_source_country) && ($flag_source_city_all || $flag_source_city)) {
                $source_final_flag = true;
            }


            $dest_final_flag = false;
            if (($flag_dest_country_all || $flag_dest_country) && ($flag_dest_city_all || $flag_dest_city)) {
                $dest_final_flag = true;
            }

            if ($flag_type_car && $source_final_flag && $dest_final_flag) {
                //todo ring alarm send
//                $noticText = str_replace('#PARAM2#', $cargo->cargo_id, $lang["ring_alaran_desc"]);
//                $noticText = str_replace('#PARAM1#', $lang['display_cargo'], $noticText);
//                (Notification::sendNotification($ring->user_id, 'ring_alarm_title', 'system', $noticText));
                Notification::sendNotification(
                    $ring->user_id,
                    'ring_alarm_title', 'system', 'ring_alaran_desc',
                    'https://ntirapp.com/cargo-ads/' . $cargo->cargo_id, 'unread', true
                );

            }

        }

    }
}