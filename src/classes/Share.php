<?php

use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Share
{


    /**
     * get Share Whats App Info By Id
     * @param $id
     * @return stdClass
     */
    public static function getShareWhatsAppInfoById($id)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_whatsapp_massage` WHERE wa_id=:wa_id";
        $params = [
            'wa_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * change Share WhatsApp Status
     * @param $userID
     * @param $status
     * @return stdClass
     */
    public static function changeShareWhatsappStatus($userID, $status)
    {
        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $sql = "UPDATE `tbl_whatsapp_massage` SET `admin_id`=:admin_id,`wa_status`=:wa_status,`wa_send_time`=:wa_send_time 
                WHERE `wa_id`=:wa_id";
        $params = [
            'admin_id' => $admin_id,
            'wa_status' => $status,
            'wa_send_time' => time(),
            'wa_id' => $userID,
        ];
        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }

}