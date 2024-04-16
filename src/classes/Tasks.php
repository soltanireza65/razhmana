<?php

use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Tasks
{

    /**
     * Create New Task
     * @param $title
     * @param $desc
     * @param $refer
     * @param $priority
     * @param $timeS
     * @param $timeE
     * @param $attachment
     * @return stdClass
     */
    public static function setNewTask($title, $desc, $refer, $priority, $timeS, $timeE, $attachment = null)
    {
        $response = sendResponse(0, "");


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        if ($admin_id > 0) {

            $sql = 'INSERT INTO `tbl_task`(`creator_id`, `admin_id`, `task_title`, `task_desc`, `task_attach`, `task_start_date`,
                       `task_end_date`, `task_submit_date`, `task_priority`, `task_status`) 
                       VALUES (:creator_id,:admin_id,:task_title,:task_desc,:task_attach,:task_start_date,
                        :task_end_date,:task_submit_date,:task_priority,:task_status)';
            $params = [
                'creator_id' => $admin_id,
                'admin_id' => $refer,
                'task_title' => $title,
                'task_desc' => $desc,
                'task_attach' => $attachment,
                'task_start_date' => $timeS,
                'task_end_date' => $timeE,
                'task_submit_date' => time(),
                'task_priority' => $priority,
                'task_status' => 'pending',
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }
        return $response;
    }


    /**
     * get Task For Me Not End From tasks.php
     * @return stdClass
     */
    public static function getTaskForMeNotEnd()
    {
        $response = sendResponse(0, "");


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        if ($admin_id > 0) {

            $sql = 'SELECT * FROM `tbl_task` WHERE `admin_id`=:admin_id AND `task_status` !=:task_status';
            $params = [
                'admin_id' => $admin_id,
                'task_status' => 'ok',
            ];
            $result = DB::rawQuery($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }
        return $response;
    }


    /**
     * get Task Create I Not End
     * @return stdClass
     */
    public static function getTaskCreateIamNotEnd()
    {
        $response = sendResponse(0, "");


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        if ($admin_id > 0) {

            $sql = 'SELECT * FROM `tbl_task` WHERE `creator_id`=:creator_id AND `task_status` !=:task_status';
            $params = [
                'creator_id' => $admin_id,
                'task_status' => 'ok',
            ];
            $result = DB::rawQuery($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }
        return $response;
    }


    /**
     * get Task INfo By Id
     * @param $id
     * @return stdClass
     */
    public static function getTaskById($id)
    {
        $response = sendResponse(0, "");


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        if ($admin_id > 0) {
            $sql = 'SELECT * FROM `tbl_task` WHERE `task_id`=:task_id  AND (`creator_id`=:creator_id OR `admin_id` =:admin_id)';
            $params = [
                'task_id' => $id,
                'creator_id' => $admin_id,
                'admin_id' => $admin_id,
            ];
            $result = DB::rawQuery($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }
        return $response;
    }


    /**
     * get Task By Id From Show
     * @param $id
     * @return stdClass
     */
    public static function getTaskByIdFromShow($id)
    {
        $response = sendResponse(0, "");


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        if ($admin_id > 0) {
            $sql = 'SELECT * FROM `tbl_task` WHERE `task_id`=:task_id ';
            $params = [
                'task_id' => $id,
            ];
            $result = DB::rawQuery($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }
        return $response;
    }


    /**
     * get Task Detail By Tas kId
     * @param $id
     * @return stdClass
     */
    public static function getTaskDetailByTaskId($id)
    {
        $response = sendResponse(0, "");


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        if ($admin_id > 0) {
            $sql = 'SELECT * FROM `tbl_task_detail` WHERE `task_id`=:task_id';
            $params = [
                'task_id' => $id,
            ];
            $result = DB::rawQuery($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }
        return $response;
    }


    /**
     * add Detail Task
     * @param $taskId
     * @param $title
     * @param $desc
     * @param $attachment
     * @return stdClass
     */
    public static function addDetailTask($taskId, $title, $desc, $attachment = null)
    {
        $response = sendResponse(0, "");


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        if ($admin_id > 0) {

            $resultTask = self::getTaskById($taskId);
            $dataTask = [];
            if ($resultTask->status == 200) {
                $dataTask = $resultTask->response[0];
            }
            if (!empty($dataTask)) {
                if ($dataTask->creator_id == $admin_id) {


                    $sql = 'INSERT INTO `tbl_task_detail`(`task_id`, `admin_id`, `detail_title`, `detail_desc`,
                              `detail_attach`, `detail_status`, `detail_status_date`, `detail_submit_date`)
                               VALUES (:task_id,:admin_id,:detail_title,:detail_desc,
                               :detail_attach,:detail_status,:detail_status_date,:detail_submit_date);';
                    $params = [
                        'task_id' => $taskId,
                        'admin_id' => $admin_id,
                        'detail_title' => $title,
                        'detail_desc' => $desc,
                        'detail_attach' => $attachment,
                        'detail_status' => 'add',
                        'detail_status_date' => time(),
                        'detail_submit_date' => time(),
                    ];
                    $result = DB::transactionQuery($sql, $params);
                    if ($result->status == 200) {
                        $response = sendResponse(200, "");
                    }


                } else {

                    $sql = 'INSERT INTO `tbl_task_detail`(`task_id`, `admin_id`, `detail_title`, `detail_desc`,
                              `detail_attach`, `detail_status`, `detail_status_date`, `detail_submit_date`)
                               VALUES (:task_id,:admin_id,:detail_title,:detail_desc,
                               :detail_attach,:detail_status,:detail_status_date,:detail_submit_date);
                    UPDATE `tbl_task` SET `task_status`=:task_status WHERE `task_id`=:task_id;';
                    $params = [
                        'task_id' => $taskId,
                        'admin_id' => $admin_id,
                        'detail_title' => $title,
                        'detail_desc' => $desc,
                        'detail_attach' => $attachment,
                        'detail_status' => null,
                        'detail_status_date' => null,
                        'detail_submit_date' => time(),
                        'task_status' => 'process',
                    ];
                    $result = DB::transactionQuery($sql, $params);
                    if ($result->status == 200) {
                        $response = sendResponse(200, "");
                    }
                }
            }


        }
        return $response;
    }


    /**
     * change Task Status
     * @param $taskId
     * @param $status
     * @return stdClass
     */
    public static function changeTaskStatus($taskId, $status)
    {
        $response = sendResponse(0, "");


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        if ($admin_id > 0) {

//            $resultTask=self::getTaskById($taskId);
//            $dataTask=[];
//            if($resultTask->status==200){
//                $dataTask=$resultTask->response[0];
//            }

            $sql = 'UPDATE `tbl_task` SET `task_status`=:task_status WHERE `task_id`=:task_id;
                    UPDATE `tbl_task_detail` SET `detail_status`=:detail_status,`detail_status_date`=:detail_status_date 
                    WHERE task_id=:task_id AND  detail_status IS NULL';
            $params = [
                'task_id' => $taskId,
                'task_status' => $status,
                'detail_status' => $status,
                'detail_status_date' => time(),
            ];
            $result = DB::transactionQuery($sql, $params);
            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }


        }


        return $response;
    }


    /**
     * get All My Task From Dashboard
     * @return stdClass
     */
    public static function getTaskFromDashboard()
    {
        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        if ($admin_id > 0) {
            $sql = 'SELECT tbl_task.task_id,tbl_task.task_status,tbl_task.task_start_date,tbl_task.task_end_date,tbl_task.task_title,tbl_task.task_desc,tbl_admins.admin_nickname,tbl_admins.admin_avatar FROM `tbl_task` INNER JOIN tbl_admins ON tbl_task.creator_id = tbl_admins.admin_id 
                    WHERE tbl_task.admin_id=:admin_id AND  `task_status` != :task_status ;';
            $params = [
                'admin_id' => $admin_id,
                'task_status' => 'ok',
            ];
            $result = DB::rawQuery($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }
        return $response;
    }
}