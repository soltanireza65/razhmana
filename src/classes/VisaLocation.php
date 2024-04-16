<?php


use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class VisaLocation{
    /**
     * get All VisaLocation
     * @param null $status
     * @return stdClass
     */
    public static function getAllVisaLocation($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT visa_id, visa_name, visa_status FROM `tbl_visa_location` ORDER BY visa_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT visa_id, visa_name, visa_status FROM `tbl_visa_location` WHERE `visa_status`=:visa_status ORDER BY visa_id DESC ;";
            $params = [
                'visa_status' => $status
            ];
        }

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            foreach ($result->response as $row) {
                $row->visa_name_fa_IR = array_column(json_decode($row->visa_name), 'value', 'slug')['fa_IR'];
                $row->visa_name_en_US= array_column(json_decode($row->visa_name), 'value', 'slug')['en_US'];
                $row->visa_name_tr_Tr = array_column(json_decode($row->visa_name), 'value', 'slug')['tr_Tr'];
                $row->visa_name_ru_RU = array_column(json_decode($row->visa_name), 'value', 'slug')['ru_RU'];
            }
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }
    /**
     * Set New VisaLocation
     * @param $title
     * @param $status
     * @return stdClass
     */
    public static function setNewVisaLocation($title, $status)
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

            $sql = 'INSERT INTO `tbl_visa_location`(`visa_name`, `visa_status`, `visa_options`) VALUES 
                    (:visa_name,:visa_status,:visa_options)';
            $params = [
                'visa_name' => $title,
                'visa_status' => $status,
                'visa_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }

        return $response;
    }
    /**
     * Get VisaLocation By Id
     * @param $id
     * @return stdClass
     */
    public static function getVisaLocationById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_visa_location` WHERE visa_id=:visa_id";
        $params = [
            'visa_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }
    /**
     * edit VisaLocation By Id
     * @param $id
     * @param $title
     * @param $status
     * @return stdClass
     */
    public static function editVisaLocationById($id, $title, $status)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getVisaLocationById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->visa_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            $sql = 'UPDATE `tbl_visa_location` SET `visa_name`=:visa_name,`visa_status`=:visa_status,`visa_options`=:visa_options
                    WHERE `visa_id`=:visa_id;';
            $params = [
                'visa_name' => $title,
                'visa_status' => $status,
                'visa_options' => json_encode($value),
                'visa_id' => $id,
            ];
            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }
}