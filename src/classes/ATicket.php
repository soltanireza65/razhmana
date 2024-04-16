<?php


use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class ATicket
{


    /**
     * Get All Departments
     * @param string=draft
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getAllDepartments($status = "")
    {
        $response = sendResponse(0, "");

        if ($status != "") {
            $sql = "SELECT * FROM `tbl_departments` WHERE department_status=:department_status ORDER BY department_id DESC ;";
            $result = DB::rawQuery($sql, ['department_status' => $status,]);
        } else {
            $sql = "SELECT * FROM `tbl_departments` ORDER BY department_id DESC ;";
            $result = DB::rawQuery($sql, []);
        }

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Set New Department
     * @param $title object
     * @param $type string
     * @param $status string
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function setNewDepartment($title, $type, $status)
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

            $sql = 'INSERT INTO `tbl_departments`( `department_name`,`department_type`, `department_status`,`department_options`) VALUES (:department_name,:department_type,:department_status,:department_options)';
            $params = [
                'department_name' => $title,
                'department_type' => $type,
                'department_status' => $status,
                'department_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }

        return $response;
    }


    /**
     * Get Department Info By Id
     * @param $id int
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getDepartmentById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_departments` WHERE department_id=:department_id";
        $params = [
            'department_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Update Department By Id
     * @param $id int
     * @param $title object
     * @param $type string
     * @param $status string
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function editDepartment($id, $title, $type, $status)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getDepartmentById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->department_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {
            $sql = 'UPDATE `tbl_departments` SET `department_name`=:department_name,`department_type`=:department_type,`department_status`=:department_status,`department_options`=:department_options WHERE `department_id`=:department_id;';
            $params = [
                'department_id' => $id,
                'department_name' => $title,
                'department_type' => $type,
                'department_status' => $status,
                'department_options' => json_encode($value),
            ];
            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    /**
     * Get All Tickets
     * @param $id int
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getAllTickets($status = "")
    {
        $response = sendResponse(0, "");
        if (empty($status)) {
            $sql = "SELECT * FROM `tbl_tickets` ORDER BY `tbl_tickets`.`ticket_id` DESC";

            $result = DB::rawQuery($sql, []);
        } else {
            $sql = "SELECT * FROM `tbl_tickets` WHERE ticket_status=:ticket_status ORDER BY `tbl_tickets`.`ticket_id` DESC";
            $params = [
                'ticket_status' => $status,
            ];
            $result = DB::rawQuery($sql, $params);
        }

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Get All Open Tickets
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getAllOpenTickets()
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_tickets` INNER JOIN `tbl_users` ON tbl_users.user_id=tbl_tickets.user_id WHERE ticket_status=:ticket_status ORDER BY `tbl_tickets`.`ticket_id` DESC";
        $params = [
            'ticket_status' => 'open',
        ];
        $result = DB::rawQuery($sql, $params);


        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }

    /**
     * Open Room And Start Tickets
     * @param $userID int
     * @param $departments int
     * @param $title string
     * @param $massage string
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function setNewTicketAndRoom($userID, $title, $massage, $departments, $attachment = "")
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $flag = false;
        $check = Admin::getAllAdmins();
        if ($check->status == 200 && !empty($check->response)) {
            $dataCheck = $check->response;
            foreach ($dataCheck as $dataCheckITEM) {
                if ($dataCheckITEM->admin_id == $admin_id) {
                    $flag = true;
                }
            }
        }


        if ($flag == false) {
            return $response;
        }


        $sql = 'INSERT INTO `tbl_tickets`(`user_id`, `ticket_title`, `department_id`, `ticket_status`, `ticket_submit_date`) VALUES (:user_id,:ticket_title,:department_id,:ticket_status,:ticket_submit_date);SELECT LAST_INSERT_ID();';
        $params = [
            'user_id' => $userID,
            'ticket_title' => $title,
            'department_id' => $departments,
            'ticket_status' => "open",
            'ticket_submit_date' => time(),
        ];
        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $sql1 = 'INSERT INTO `tbl_ticket_messages`(`ticket_id`, `admin_id`, `message_body`,`message_attachment`,`message_status`, `message_submit_date`) VALUES (:ticket_id,:admin_id,:message_body,:message_attachment,:message_status,:message_submit_date);SELECT LAST_INSERT_ID();';
            $params1 = [
                'ticket_id' => $result->response,
                'admin_id' => $admin_id,
                'message_body' => $massage,
                'message_attachment' => $attachment,
                'message_status' => "unread",
                'message_submit_date' => time(),
            ];
            $result1 = DB::insert($sql1, $params1);
            if ($result1->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }

        }

        return $response;
    }


    /**
     * Get Ticket Mas Room Info By Id
     * @param $id int
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getTicketById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_tickets` WHERE ticket_id=:ticket_id ";
        $params = [
            'ticket_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Get All Tickets Mas
     * @param $id int
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getAllTicketMessages($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_ticket_messages` WHERE ticket_id=:ticket_id ORDER BY ticket_id ASC ";
        $params = [
            'ticket_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Add New Tickets Exist Room
     * @param $roomID int
     * @param $userID int
     * @param $massage string
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function setNewTicketExistRoom($roomID, $userID, $massage, $attachment = "")
    {
        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $flag = false;
        $check = Admin::getAllAdmins();
        if ($check->status == 200 && !empty($check->response)) {
            $dataCheck = $check->response;
            foreach ($dataCheck as $dataCheckITEM) {
                if ($dataCheckITEM->admin_id == $admin_id) {
                    $flag = true;
                }
            }
        }


        if ($flag == false) {
            return $response;
        }

        $attah = '';
        if (!empty($attachment)) {
            $attah = $attachment;
        }

        $sql = 'INSERT INTO `tbl_ticket_messages`(`ticket_id`, `admin_id`, `message_body`,`message_attachment`,`message_status`, `message_submit_date`) VALUES (:ticket_id,:admin_id,:message_body,:message_attachment,:message_status,:message_submit_date);SELECT LAST_INSERT_ID();';
        $params = [
            'ticket_id' => $roomID,
            'admin_id' => $admin_id,
            'message_body' => $massage,
            'message_attachment' => $attah,
            'message_status' => "unread",
            'message_submit_date' => time(),
        ];
        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Close Room
     * @param $id int
     * @param $status string
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function closeRoom($idRoom)
    {
        $response = sendResponse(0, "");

        $sql = "UPDATE `tbl_tickets` SET ticket_status=:ticket_status WHERE ticket_id=:ticket_id  ;";
        $param = [
            "ticket_status" => "close",
            "ticket_id" => $idRoom,
        ];

        $result = DB::update($sql, $param);
        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }


        return $response;
    }


    /**
     * Get All Ticket By Limit
     * @param $limitStart int
     * @param $limitEnd int
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getAllTicketsByIdLimit($limitStart, $limitEnd)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_tickets` INNER JOIN `tbl_departments` ON tbl_tickets.department_id=tbl_departments.department_id ORDER BY ticket_id  DESC LIMIT {$limitStart},{$limitEnd};";
        $params = [];


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * SELECT ALL USER Open Ticket Room
     * @param $userID int
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getUserOpenTicketsById($userID, $status = "")
    {

        $response = sendResponse(0, "");
        if (empty($status)) {
            $sql = 'SELECT * FROM `tbl_tickets` WHERE  user_id=:user_id;';
            $params = [
                'user_id' => $userID,
            ];
        } else {
            $sql = 'SELECT * FROM `tbl_tickets` WHERE ticket_status=:ticket_status AND user_id=:user_id;';
            $params = [
                'ticket_status' => $status,
                'user_id' => $userID,
            ];
        }

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Get USer All Ticket
     * @param $userID int
     * @param $limit int
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getUserTicketsByIdLimit($userID, $limitStart, $limitEnd)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_tickets` INNER JOIN `tbl_departments` ON tbl_tickets.department_id=tbl_departments.department_id WHERE user_id=:user_id ORDER BY ticket_id  DESC LIMIT {$limitStart},{$limitEnd};";
        $params = [
            'user_id' => $userID,
        ];


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }





    /**
     * Get Tickets By Status
     * @param $status string
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getTicketsByStatus($status)
    {
        $response = sendResponse(0, "");

            $sql = "SELECT * FROM `tbl_tickets` WHERE tbl_tickets.ticket_status=:ticket_status ";
            $params = [
                'ticket_status' => $status,
            ];
            $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }




    /******************************************* After Change ********************/

    /**
     * Get All Tickets Department
     * @param $id int
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getAllTicketD($departmentId,$status = null)
    {
        $response = sendResponse(0, "");
        if (is_null($status)) {
            $sql = "SELECT * FROM `tbl_tickets` INNER JOIN `tbl_departments` ON tbl_departments.department_id=tbl_tickets.department_id WHERE tbl_departments.department_id=:department_id ORDER BY `tbl_tickets`.`ticket_id` DESC";
            $params = [
                'department_id' => $departmentId,
            ];
        } else {
            $sql = "SELECT * FROM `tbl_tickets` INNER JOIN `tbl_departments` ON tbl_departments.department_id=tbl_tickets.department_id WHERE tbl_departments.department_id=:department_id AND ticket_status=:ticket_status ORDER BY `tbl_tickets`.`ticket_id` DESC";
            $params = [
                'department_id' => $departmentId,
                'ticket_status' => $status,
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * get Count Airports From Chart
     *
     * @return stdClass
     */
    public static function getCountTicketsOpen()
    {

        $sql = "SELECT 
                    (SELECT COUNT(*) FROM tbl_tickets WHERE ticket_status='open' AND department_id=1) AS status_1,
                    (SELECT COUNT(*) FROM tbl_tickets WHERE ticket_status='open' AND department_id=2) AS status_2,
                    (SELECT COUNT(*) FROM tbl_tickets WHERE ticket_status='open' AND department_id=3) AS status_3,
                    (SELECT COUNT(*) FROM tbl_tickets WHERE ticket_status='open' AND department_id=4) AS status_4,
                    (SELECT COUNT(*) FROM tbl_tickets WHERE ticket_status='open' AND department_id=5) AS status_5,
                    (SELECT COUNT(*) FROM tbl_tickets WHERE ticket_status='open' AND department_id=6) AS status_6,
                    (SELECT COUNT(*) FROM tbl_tickets WHERE ticket_status='open' AND department_id=7) AS status_7,
                    (SELECT COUNT(*) FROM tbl_tickets WHERE ticket_status='open' AND department_id=8) AS status_8
                    FROM tbl_customs";
        $params = [];


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            return [
                'status_1' => $result->response[0]->status_1,
                'status_2' => $result->response[0]->status_2,
                'status_3' => $result->response[0]->status_3,
                'status_4' => $result->response[0]->status_4,
                'status_5' => $result->response[0]->status_5,
                'status_6' => $result->response[0]->status_6,
                'status_7' => $result->response[0]->status_7,
                'status_8' => $result->response[0]->status_8,
            ];
        }

        return [
            'status_1' => 0,
            'status_2' => 0,
            'status_3' => 0,
            'status_4' => 0,
            'status_5' => 0,
            'status_6' => 0,
            'status_7' => 0,
            'status_8' => 0,
        ];
    }
}