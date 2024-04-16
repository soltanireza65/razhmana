<?php


use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Complaint
{


    /**
     * Get All Complaints WhitCargo By Status
     * @param $type $status
     * @return Object
     * @author Tjavan
     * @version 2.0.0
     */
    public static function getAllComplaintsWhitCargo($status = null)
    {

        $response = sendResponse(0, "Error Msg");
//        if (empty($status)) {
//            $sql = "SELECT * FROM `tbl_complaints` INNER JOIN `tbl_cargo` ON tbl_cargo.cargo_id=tbl_complaints.cargo_id ORDER BY complaint_id DESC ;";
//            $params = [];
//        } else {
//            $sql = "SELECT * FROM `tbl_complaints` INNER JOIN `tbl_cargo` ON tbl_cargo.cargo_id=tbl_complaints.cargo_id WHERE `complaint_status`=:complaint_status ORDER BY complaint_id DESC ;";
//            $params = [
//                'complaint_status' => $status
//            ];
//        }

        if (empty($status)) {
            $sql = "SELECT * from (
                    (SELECT `complaint_id`, tbl_complaints.`cargo_id`, `request_id`, `complaint_from`, `complaint_to`, `admin_id`, `complaint_status`, `complaint_date`,`cargo_name_fa_IR`, `cargo_name_en_US`, `cargo_name_tr_Tr`, `cargo_name_ru_RU`,'out' as xtype 
                    FROM tbl_complaints INNER JOIN `tbl_cargo` ON tbl_cargo.cargo_id=tbl_complaints.cargo_id )
                    UNION ALL
                    (SELECT `complaint_id`, tbl_complaints_in.`cargo_id`, `request_id`, `complaint_from`, `complaint_to`, `admin_id`, `complaint_status`, `complaint_date`,`cargo_name_fa_IR`, `cargo_name_en_US`, `cargo_name_tr_Tr`, `cargo_name_ru_RU`,'in' as xtype
                    FROM tbl_complaints_in INNER JOIN `tbl_cargo_in` ON tbl_cargo_in.cargo_id=tbl_complaints_in.cargo_id )
                    )  AS i
                    order by  complaint_date desc;";
            $params = [];
        } else {
            $sql = "SELECT * from (
                    (SELECT `complaint_id`, tbl_complaints.`cargo_id`, `request_id`, `complaint_from`, `complaint_to`, `admin_id`, `complaint_status`, `complaint_date`,`cargo_name_fa_IR`, `cargo_name_en_US`, `cargo_name_tr_Tr`, `cargo_name_ru_RU`,'out' as xtype
                    FROM tbl_complaints INNER JOIN `tbl_cargo` ON tbl_cargo.cargo_id=tbl_complaints.cargo_id WHERE complaint_status = :complaint_status )
                    UNION ALL
                    (SELECT `complaint_id`, tbl_complaints_in.`cargo_id`, `request_id`, `complaint_from`, `complaint_to`, `admin_id`, `complaint_status`, `complaint_date`,`cargo_name_fa_IR`, `cargo_name_en_US`, `cargo_name_tr_Tr`, `cargo_name_ru_RU`,'in' as xtype
                    FROM tbl_complaints_in INNER JOIN `tbl_cargo_in` ON tbl_cargo_in.cargo_id=tbl_complaints_in.cargo_id WHERE complaint_status =:complaint_status )
                    )  AS i
                    order by  complaint_date desc;";
            $params = [
                'complaint_status' => $status
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    /**
     * Get Complaint By ID
     * @param $ID
     * @return Object
     * @author Tjavan
     * @version 2.0.0
     */
    public static function getComplaintByID($ID)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT * FROM `tbl_complaints`  WHERE `complaint_id`=:complaint_id ;";
        $params = [
            'complaint_id' => $ID
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    public static function getComplaintInByID($ID)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT * FROM `tbl_complaints_in`  WHERE `complaint_id`=:complaint_id ;";
        $params = [
            'complaint_id' => $ID
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    /**
     * Set Admin From New Complaint
     * @param $complaintID
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function setAdminFromNewComplaint($complaintID)
    {
        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $res = self::getComplaintByID($complaintID);
        $temp = [];
        $tempAdminSet = null;
        $tempStatus = '';
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->complaint_options;
            $tempAdminSet = $res->response[0]->admin_id;
            $tempStatus = $res->response[0]->complaint_status;
        }

        if (is_null($tempAdminSet) && $tempStatus == "pending") {


            $array = [];
            $array = json_decode($temp, true);
            $array['accepted_admin_id'] = $admin_id;
            $array['accepted_date'] = time();


            if ($admin_id > 0) {
                $sql = "UPDATE `tbl_complaints` SET `admin_id`=:admin_id,`complaint_status`=:complaint_status,`complaint_options`=:complaint_options  WHERE `complaint_id`=:complaint_id ;";
                $params = [
                    'admin_id' => $admin_id,
                    'complaint_status' => 'accepted',
                    'complaint_options' => json_encode($array),
                    'complaint_id' => $complaintID,
                ];

                $result = DB::update($sql, $params);

                if ($result->status == 200) {
                    $response = sendResponse(200, "Successful");
                }
            }

        } else {
            $response = sendResponse(225, "!");
        }


        return $response;
    }

    public static function setAdminFromNewComplaintIn($complaintID)
    {
        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $res = self::getComplaintInByID($complaintID);
        $temp = [];
        $tempAdminSet = null;
        $tempStatus = '';
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->complaint_options;
            $tempAdminSet = $res->response[0]->admin_id;
            $tempStatus = $res->response[0]->complaint_status;
        }

        if (is_null($tempAdminSet) && $tempStatus == "pending") {
            $array = [];
            $array = json_decode($temp, true);
            $array['accepted_admin_id'] = $admin_id;
            $array['accepted_date'] = time();

            if ($admin_id > 0) {
                $sql = "UPDATE `tbl_complaints_in` SET `admin_id`=:admin_id,`complaint_status`=:complaint_status,`complaint_options`=:complaint_options  WHERE `complaint_id`=:complaint_id ;";
                $params = [
                    'admin_id' => $admin_id,
                    'complaint_status' => 'accepted',
                    'complaint_options' => json_encode($array),
                    'complaint_id' => $complaintID,
                ];
                $result = DB::update($sql, $params);
                if ($result->status == 200) {
                    $response = sendResponse(200, "Successful");
                }
            }
        } else {
            $response = sendResponse(225, "!");
        }
        return $response;
    }

    /**
     * Closed Complaint
     * @param $complaintID
     * @param $desc
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function closedComplaint($complaintID, $desc)
    {
        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $res = self::getComplaintByID($complaintID);
        $temp = [];
        $tempAdminSet = null;
        $tempStatus = '';
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->complaint_options;
            $tempStatus = $res->response[0]->complaint_status;
        }

        if ($tempStatus == "accepted") {

            $array = [];
            $array = json_decode($temp, true);
            $array['closed_admin_id'] = $admin_id;
            $array['closed_date'] = time();
            $array['description'] = $desc;

            if ($admin_id > 0) {
                $sql = "UPDATE `tbl_complaints` SET `complaint_status`=:complaint_status,`complaint_options`=:complaint_options  WHERE `complaint_id`=:complaint_id ;";
                $params = [
                    'complaint_status' => 'closed',
                    'complaint_options' => json_encode($array),
                    'complaint_id' => $complaintID,
                ];

                $result = DB::update($sql, $params);

                if ($result->status == 200) {
                    $response = sendResponse(200, "Successful");
                }
            }

        } else {
            $response = sendResponse(225, "!");
        }


        return $response;
    }


    public static function closedComplaintIn($complaintID, $desc)
    {
        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getComplaintInByID($complaintID);
        $temp = [];
        $tempAdminSet = null;
        $tempStatus = '';
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->complaint_options;
            $tempStatus = $res->response[0]->complaint_status;
        }

        if ($tempStatus == "accepted") {
            $array = [];
            $array = json_decode($temp, true);
            $array['closed_admin_id'] = $admin_id;
            $array['closed_date'] = time();
            $array['description'] = $desc;

            if ($admin_id > 0) {
                $sql = "UPDATE `tbl_complaints_in` SET `complaint_status`=:complaint_status,`complaint_options`=:complaint_options  WHERE `complaint_id`=:complaint_id ;";
                $params = [
                    'complaint_status' => 'closed',
                    'complaint_options' => json_encode($array),
                    'complaint_id' => $complaintID,
                ];
                $result = DB::update($sql, $params);
                if ($result->status == 200) {
                    $response = sendResponse(200, "Successful");
                }
            }
        } else {
            $response = sendResponse(225, "!");
        }
        return $response;
    }


    /**
     * Get Count Complaint By Status
     * @param $type
     * @param $status
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getCountComplaint($status)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT count(*) AS count FROM `tbl_complaints` WHERE complaint_status=:complaint_status ";
        $params = [
            'complaint_status' => $status,
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
     * Get All Complaints Whit Cargo By User Id
     * @param $userID
     * @param $fromTo
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getAllComplaintsWhitCargoByUserId($userID, $fromTo)
    {

        $temp = "complaint_from";
        if ($fromTo != "from") {
            $temp = 'complaint_to';
        }

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_complaints` INNER JOIN `tbl_cargo` ON tbl_cargo.cargo_id=tbl_complaints.cargo_id 
             WHERE tbl_complaints." . $temp . "=:complaint_from_to ORDER BY complaint_id DESC ;";
        $params = [
            'complaint_from_to' => $userID,
        ];


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * @param $cargoId
     * @param $requestId
     * @param $from
     * @param $to
     * @param $token
     * @param bool $isSystem
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function submitComplaint($cargoId, $requestId, $from, $to, $token, $isSystem = false)
    {
        if (!Security::verifyCSRF('submit-complaint', $token) && !$isSystem) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('submit-complaint');

        $sql = "insert into tbl_complaints(cargo_id, request_id, complaint_from, complaint_to, complaint_status, complaint_date) 
        values (:cargoId, :requestId, :from, :to, :status, :time);";
        $params = [
            'cargoId' => $cargoId,
            'requestId' => $requestId,
            'from' => $from,
            'to' => $to,
            'status' => 'pending',
            'time' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            User::createUserLog($from, 'uLog_submit_complaint', 'complaint');
            return sendResponse(200, 'Complaint submitted successfully');
        } elseif ($result->status == 202) {
            return sendResponse(-10, 'Error', $csrf);
        }
        return self::submitComplaint($cargoId, $requestId, $from, $to, null, true);
    }

    public static function submitComplaintIn($cargoId, $requestId, $from, $to, $token, $isSystem = false)
    {
        if (!Security::verifyCSRF('submit-complaint-in', $token) && !$isSystem) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('submit-complaint-in');

        $sql = "insert into tbl_complaints_in(cargo_id, request_id, complaint_from, complaint_to, complaint_status, complaint_date) 
        values (:cargoId, :requestId, :from, :to, :status, :time);";
        $params = [
            'cargoId' => $cargoId,
            'requestId' => $requestId,
            'from' => $from,
            'to' => $to,
            'status' => 'pending',
            'time' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            User::createUserLog($from, 'uLog_submit_complaint_in', 'complaint');
            return sendResponse(200, 'Complaint submitted successfully');
        } elseif ($result->status == 202) {
            return sendResponse(-10, 'Error', $csrf);
        }
        return self::submitComplaintIn($cargoId, $requestId, $from, $to, null, true);
    }

    /**
     * @param $cargoId
     * @param $requestId
     * @param $from
     * @param $to
     * @return bool
     * @author Tjavan
     * @version 1.0.0
     */
    public static function checkCanSendComplaint($cargoId, $requestId, $from, $to)
    {
        $response = true;

        $sql = "select count(*) as count
        from tbl_complaints
        where cargo_id = :cargoId and request_id = :requestId and complaint_from = :from and complaint_to = :to and complaint_status = :status;";
        $params = [
            'cargoId' => $cargoId,
            'requestId' => $requestId,
            'from' => $from,
            'to' => $to,
            'status' => 'pending',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            if ($result->response[0]->count > 0) {
                return false;
            }
        }
        return $response;
    }


    public static function checkCanSendComplaintIn($cargoId, $requestId, $from, $to)
    {
        $response = true;

        $sql = "select count(*) as count
        from tbl_complaints_in
        where cargo_id = :cargoId and request_id = :requestId and complaint_from = :from and complaint_to = :to and complaint_status = :status;";
        $params = [
            'cargoId' => $cargoId,
            'requestId' => $requestId,
            'from' => $from,
            'to' => $to,
            'status' => 'pending',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            if ($result->response[0]->count > 0) {
                return false;
            }
        }
        return $response;
    }
}