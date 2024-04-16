<?php


use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class GNotification
{


    /**
     * Set New Group Notification
     * @param $title
     * @param $sender
     * @param $text
     * @param $type
     * @param $relation
     * @param $status
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function addGroupNotification($title, $sender, $text, $user_type, $user_status, $status, $lang,$notics_type)
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


        $sql = "INSERT INTO `tbl_notification_group`(`user_type`, `user_status`, `ngroup_sender`, `ngroup_title`, `ngroup_message`,
                                     `ngroup_status`,`ngroup_language`, `ngroup_options`,ngroup_notics_type) 
                 VALUES (:user_type,:user_status,:ngroup_sender,:ngroup_title,:ngroup_message,:ngroup_status,:ngroup_language,:ngroup_options,:ngroup_notics_type)";

        $params = [
            'user_type' => $user_type,
            'user_status' => $user_status,
            'ngroup_sender' => $sender,
            'ngroup_title' => $title,
            'ngroup_message' => $text,
            'ngroup_status' => $status,
            'ngroup_language' => $lang,
            'ngroup_notics_type' => $notics_type,
            'ngroup_options' => json_encode($array),
        ];


        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Get All Group Notifications
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getAllGroupNotifications()
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_notification_group` ORDER BY `ngroup_id` DESC ;";
        $params = [];

        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Get Group Notification By Id
     * @param $id
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getGroupNotificationById($id)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_notification_group` WHERE `ngroup_id`=:ngroup_id";
        $params = [
            'ngroup_id' => $id
        ];

        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Edit Group Notifications By ID
     * @param $id
     * @param $relation
     * @param $sender
     * @param $title
     * @param $text
     * @param $relation_type
     * @param $status
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function editGroupNotification($id, $sender, $title, $text, $user_type, $user_status, $language, $status ,$notics_type)
    {
        $response = sendResponse(0, "Error Msg");


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getGroupNotificationById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->ngroup_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        $sql = "UPDATE `tbl_notification_group` SET `user_type`=:user_type,`user_status`=:user_status,
                `ngroup_sender`=:ngroup_sender,`ngroup_title`=:ngroup_title,`ngroup_message`=:ngroup_message,`ngroup_status`=:ngroup_status,
                `ngroup_language`=:ngroup_language,`ngroup_options`=:ngroup_options  , tbl_notification_group.ngroup_notics_type = :ngroup_notics_type WHERE `ngroup_id`=:ngroup_id";
        $params = [
            'user_type' => $user_type,
            'user_status' => $user_status,
            'ngroup_sender' => $sender,
            'ngroup_title' => $title,
            'ngroup_message' => $text,
            'ngroup_status' => $status,
            'ngroup_language' => $language,
            'ngroup_options' => json_encode($value),
            'ngroup_notics_type' => $notics_type,
            'ngroup_id' => $id,
        ];


        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    /**
     * Delete Group Notifications
     * @param $id
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function deleteGroupNotification($id)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "DELETE FROM `tbl_notification_group` WHERE `ngroup_id`=:ngroup_id";
        $params = [
            'ngroup_id' => $id
        ];

        $result = DB::delete($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Delete Successful");
        }

        return $response;
    }


    /**
     * Get Group Notification By Id
     * @param $id
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getGroupNotificationByStatus($status)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_notification_group` WHERE `ngroup_status`=:ngroup_status";
        $params = [
            'ngroup_status' => $status
        ];

        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    public static function getGroupNoticsCountByType($type = 'notics')
    {


        $sql = "SELECT count(0) as n_count FROM `tbl_notification_group` WHERE tbl_notification_group.`ngroup_notics_type`=:type";
        $params = [
            'type' => $type
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            return $result->response[0]->n_count;
        }

        return 0;
    }


}