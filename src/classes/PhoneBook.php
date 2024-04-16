<?php

use MJ\Database\DB;
use MJ\Security\Security;
use MJ\SMS\SMS;
use function MJ\Keys\sendResponse;

class PhoneBook
{

    public static function checkPhoneBookExists($phone)
    {

        $response = sendResponse(0, 'error');

        $sql = "select count(*) as user_count from tbl_phonebook where tbl_phonebook.pb_phone = :user_mobile;";
        $params = [
            'user_mobile' => $phone
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, '', $result->response[0]->user_count);
        }
        return $response;
    }

    public static function addPhoneBook($user_types, $user_name, $user_lname, $user_phone, $user_home_number, $company_name,
                                        $member_zone, $member_status, $car_types, $fav_countries, $activity_summery)
    {
        $response = sendResponse(0, "");
        global $lang;
        $sql = " 
 INSERT INTO `tbl_phonebook`(   `pb_username`, `pb_user_lname`, `pb_user_type`, `pb_phone`, `pb_home_number`,
                             `pb_company_name`, `pb_cargo_type`, `pb_access_type`, `pb_car_type`, `pb_fav_country`
                             , `pb_create_at`, `pb_update_at`) VALUES(
                   :pb_username, :pb_user_lname, :pb_user_type, :pb_phone, :pb_home_number,
                             :pb_company_name, :pb_cargo_type, :pb_access_type, :pb_car_type, :pb_fav_country
                             , :pb_create_at, :pb_update_at
            )
 
        ";
        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }
        $params = [
            "pb_username" => $user_name,
            "pb_user_lname" => $user_lname,
            "pb_user_type" => $user_types,
            "pb_phone" => $user_phone,
            "pb_home_number" => $user_home_number,
            "pb_company_name" => $company_name,
            "pb_cargo_type" => $member_zone,
            "pb_access_type" => $member_status,
            "pb_car_type" => implode(',', $car_types),
            "pb_fav_country" => implode(',', $fav_countries),

            "pb_create_at" => time(),
            "pb_update_at" => time(),
        ];
        $result = DB::insert($sql, $params);
        $response = sendResponse(200,''  , $result->response);
        if ($result->status == 200) {
            $desc_sql = "INSERT INTO `tbl_phonebook_desc`( `pb_id`, `desc_text`, `desc_create_at`, `desc_admin_id`) 
                    VALUES (:pb_id,:desc_text,:desc_create_at,:desc_admin_id)";
            $desc_params = [
                'pb_id' => $result->response,
                'desc_text' => $activity_summery,
                'desc_create_at' => time(),
                'desc_admin_id' => $admin_id,
            ];
            $result_desc = DB::insert($desc_sql, $desc_params);

        }

        return $response;
    }


    public static function editPhoneBook($p_id, $user_types, $user_name, $user_lname, $user_phone, $user_home_number, $company_name,
                                         $member_zone, $member_status, $car_types, $fav_countries, $activity_summery)
    {
        $response = sendResponse(0, "");
        global $lang;
        $sql = " 
 UPDATE `tbl_phonebook` SET  `pb_username`=:pb_username,`pb_user_lname`=:pb_user_lname,`pb_user_type`=:pb_user_type,
                            `pb_phone`=:pb_phone,`pb_home_number`=:pb_home_number,`pb_company_name`=:pb_company_name,`pb_cargo_type`=:pb_cargo_type,
                            `pb_access_type`=:pb_access_type,`pb_car_type`=:pb_car_type,`pb_fav_country`=:pb_fav_country,
                           `pb_update_at`=:pb_update_at WHERE tbl_phonebook.pb_id = :p_id
 
        ";
        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

//        $detail = self::getphonebook($p_id)->response;
//
//        $array2 = json_decode(json_encode([["title" => $activity_summery, "update_at" => time(), "admin_id" => $admin_id]],JSON_UNESCAPED_UNICODE), true);

            $desc_sql = "INSERT INTO `tbl_phonebook_desc`( `pb_id`, `desc_text`, `desc_create_at`, `desc_admin_id`) 
                    VALUES (:pb_id,:desc_text,:desc_create_at,:desc_admin_id)";
        $desc_params = [
            'pb_id' => $p_id,
            'desc_text' => $activity_summery,
            'desc_create_at' => time(),
            'desc_admin_id' => $admin_id,
        ];
        DB::insert($desc_sql, $desc_params);

        $params = [
            "p_id" => $p_id,
            "pb_username" => $user_name,
            "pb_user_lname" => $user_lname,
            "pb_user_type" => $user_types,
            "pb_phone" => $user_phone,
            "pb_home_number" => $user_home_number,
            "pb_company_name" => $company_name,
            "pb_cargo_type" => $member_zone,
            "pb_access_type" => $member_status,
            "pb_car_type" => implode(',', $car_types),
            "pb_fav_country" => implode(',', $fav_countries),
            "pb_update_at" => time(),
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {


            $response = sendResponse(200, "", $p_id);
        }

        return $response;
    }

    public static function getPhoneBooks($searchKeys, $status, $user_type, $country, $car_type, $cargo_type , $page)
    {

        $sql = "
                    SELECT
                        p.pb_id,
                        p.pb_username,
                        p.pb_user_lname,
                        p.pb_user_type,
                        p.pb_phone,
                        p.pb_company_name,
                        p.pb_access_type,
                        p.pb_home_number,
                        p.pb_desc,
                        p.pb_fav_country,
                        p.pb_car_type,
                        CONCAT(c.country_name , ', ') as c_name,
                        CONCAT(c.country_id , ', ') as c_id,
                        CONCAT(ct.type_id , ', ') as ct_id,
                        CONCAT(ct.type_name , ', ') as ct_name
                        
                    FROM
                        tbl_phonebook p
                            left JOIN tbl_country c ON FIND_IN_SET(c.country_id, p.pb_fav_country)
                            left JOIN tbl_car_types ct ON FIND_IN_SET(ct.type_id, p.pb_car_type)
                            left join tbl_phonebook_desc tpd on p.pb_id = tpd.pb_id
                    WHERE
                        (p.pb_username like '%$searchKeys%' or
                        p.pb_user_lname like '%$searchKeys%' or
                        p.pb_phone like '%$searchKeys%' or
                        p.pb_company_name like '%$searchKeys%' or
                        p.pb_home_number like '%$searchKeys%' or 
                        tpd.desc_text like  '%$searchKeys%')
                   
";
        $response = sendResponse(0, 'error' , $sql);
        if ($searchKeys != '') {

        }

        if ($status != 'all') {
            $sql .= " and p.pb_access_type = '$status' ";
        }
        if ($user_type != 'all') {
            $sql .= " and  p.pb_user_type = '$user_type' ";
        }
        if ($country != 'all') {
            $sql .= " and c.country_id = '$country' ";
        }
        if ($car_type != 'all') {
            $sql .= " and ct.type_id = '$car_type' ";
        }
        if ($cargo_type != 'all') {
            $sql .= " and p.pb_cargo_type = '$cargo_type' ";
        }

        $page=$page ==1  ? 0  : $page *100;
        $sql .= " GROUP BY     p.pb_id,
                                 p.pb_username,
                                 p.pb_phone
                        order by p.pb_id desc
                        LIMIT 100 OFFSET $page;";
        $params = [

        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, '$sql', $result->response);
        } elseif ($result->status == 204) {
            $response = sendResponse(200, '$sql', []);
        }
        return $response;

    }
    public static function sendSmsPhoneBooks($searchKeys, $status, $user_type, $country, $car_type, $cargo_type)
    {

        $sql = "
                    SELECT
                        p.pb_id,
                        p.pb_username,
                        p.pb_user_lname,
                        p.pb_user_type,
                        p.pb_phone,
                        p.pb_company_name,
                        p.pb_access_type,
                        p.pb_home_number,
                        p.pb_desc,
                        p.pb_fav_country,
                        p.pb_car_type,
                        GROUP_CONCAT(c.country_name , ',') as c_name,
                        GROUP_CONCAT(c.country_id , ',') as c_id,
                        GROUP_CONCAT(ct.type_id , ',') as ct_id,
                        GROUP_CONCAT(ct.type_name , ',') as ct_name
                        
                    FROM
                        tbl_phonebook p
                            left JOIN tbl_country c ON FIND_IN_SET(c.country_id, p.pb_fav_country)
                            left JOIN tbl_car_types ct ON FIND_IN_SET(ct.type_id, p.pb_car_type)
                            left join tbl_phonebook_desc tpd on p.pb_id = tpd.pb_id
                    WHERE
                        (p.pb_username like '%$searchKeys%' or
                        p.pb_user_lname like '%$searchKeys%' or
                        p.pb_phone like '%$searchKeys%' or
                        p.pb_company_name like '%$searchKeys%' or
                        p.pb_home_number like '%$searchKeys%' or 
                        tpd.desc_text like  '%$searchKeys%')
                   
";
        $response = sendResponse(0, 'error' , $sql);
        if ($searchKeys != '') {

        }

        if ($status != 'all') {
            $sql .= " and p.pb_access_type = '$status' ";
        }
        if ($user_type != 'all') {
            $sql .= " and  p.pb_user_type = '$user_type' ";
        }
        if ($country != 'all') {
            $sql .= " and c.country_id = '$country' ";
        }
        if ($car_type != 'all') {
            $sql .= " and ct.type_id = '$car_type' ";
        }
        if ($cargo_type != 'all') {
            $sql .= " and p.pb_cargo_type = '$cargo_type' ";
        }
        $sql .= " GROUP BY     p.pb_id,
                                 p.pb_username,
                                 p.pb_phone
                        order by p.pb_id desc
                          ";
        $params = [

        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, $sql, $result->response);
        } elseif ($result->status == 204) {
            $response = sendResponse(200, $sql, []);
        }
        return $response;

    }

    public static function getphonebook($pb_id)
    {
        $response = sendResponse(0, 'error');

        $sql = "select * from tbl_phonebook  where tbl_phonebook.pb_id = :pb_id;";
        $params = [
            'pb_id' => $pb_id
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, '', $result->response[0]);
        }
        return $response;

    }

    public static function getPhoneBookDesc($pb_id)
    {
        $response = sendResponse(0, 'error');

        $sql = "select * from tbl_phonebook_desc tpd  where pb_id = :pb_id order by desc_id  desc";
        $params = [
            'pb_id' => $pb_id
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, '', $result->response);
        }
        return $response;
    }

    public static function addUserToPhoneBookWhenRegister($user_name, $last_name, $type, $phone)
    {
        $response = sendResponse(0, "");

        $params = [
            'name' => $user_name,
            'lname' => $last_name,
            'type' =>  $type,
            'phone' => $phone,
            'home_numner' => null,
            'company_name' => null,
            'cargo_type' => null,
            'access_type' => 'access',
            'car_type' => null,
            'fav_country' => null,
            'create_at' => time(),
            'update_at' => time()
        ];
        $sql = 'insert into tbl_phonebook(tbl_phonebook.pb_username , tbl_phonebook.pb_user_lname ,tbl_phonebook.pb_user_type 
        , tbl_phonebook.pb_phone ,tbl_phonebook.pb_home_number ,tbl_phonebook.pb_company_name ,tbl_phonebook.pb_cargo_type ,tbl_phonebook.pb_access_type ,
        tbl_phonebook.pb_car_type , tbl_phonebook.pb_fav_country  , tbl_phonebook.pb_create_at ,tbl_phonebook.pb_update_at) 
values (:name  , :lname ,  :type  ,:phone  ,:home_numner  ,  :company_name , :cargo_type , :access_type  , :car_type , :fav_country  , :create_at , :update_at)';


        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }

    public static function deletePhoneBook($p_id)
    {
        $response  =  sendResponse(0  , '');

        $sql = 'delete from tbl_phonebook where tbl_phonebook.pb_id = :pb_id';
        $params  = [
            'pb_id'=>$p_id
        ];
        $result = DB::delete($sql , $params);
        if($result->status ==200){
            $response = sendResponse(200 , 'deleted'  );
        }
        return $response;
    }
}