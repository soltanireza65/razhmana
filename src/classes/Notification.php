<?php


use MJ\Database\DB;
use MJ\SMS\SMS;
use function MJ\Keys\sendResponse;


class Notification
{


    /**
     * Send Notification Admin To User
     * @param $id
     * @param $title
     * @param $sender
     * @param $text
     * @param string $status
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function sendNotification($id,
                                            $notification_title,
                                            $sender,
                                            $notification_message,
                                            $notification_link,
                                            $status = "unread", $send_sms = true)
    {
        $response = sendResponse(0, "");
        global $lang;
        $sql = "INSERT INTO `tbl_notifications`(`user_id`, `notification_sender`,
                                `notification_title` ,
                                `notification_message` ,
                                `notification_link` ,
                                `notification_status`, `notification_time`) VALUES (:user_id,:notification_sender,
                :notification_title, 
                :notification_message, 
                :notification_link, 
                :notification_status,:notification_time)";
        $params = [
            'user_id' => $id,
            'notification_sender' => $sender,
            'notification_title' => $notification_title,
            'notification_message' => $notification_message,
            'notification_link' => $notification_link,
            'notification_status' => $status,
            'notification_time' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            if ($send_sms) {
                // send sms
                $user = User::getUserInfo($id);
                include_once "./languages/{$user->UserLanguage}.php";
                $smstext = $lang[$notification_message] . ' ' . $notification_link;
                @SMS::sendSMS([$user->UserMobile], $smstext);
            }

            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Get All NotiFications User
     * @param $id
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getUserNotifications($id)
    {
        $response = sendResponse(0, "" ,[]);

        $sql = "SELECT * FROM `tbl_notifications` WHERE `user_id`=:user_id ORDER BY `notification_id` DESC ;";
        $params = [
            'user_id' => $id
        ];

        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }

    public static function getUserNotificationsCount($id , $notification_status = "unread")
    {


        $sql = "SELECT count(*) as notification_count FROM `tbl_notifications` WHERE `user_id`=:user_id and notification_status=:notification_status ORDER BY `notification_id` DESC ;";
        $params = [
            'user_id' => $id,
            'notification_status' => $notification_status
        ];

        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            return $result->response[0]->notification_count;
        }

        return 0;
    }


    /**
     * @param $id
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getNotificationById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_notifications` WHERE `notification_id`=:notification_id";
        $params = [
            'notification_id' => $id
        ];

        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }

    public static function changeNotificationStatus($notification_id , $notification_status = 'read'){
        $response = sendResponse(0, "");

        $sql = "update tbl_notifications set tbl_notifications.notification_status = :notification_status WHERE `notification_id`=:notification_id";
        $params = [
            'notification_id' => $notification_id,
            'notification_status' => $notification_status
        ];

        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, "");
        }

        return $response;
    }
}