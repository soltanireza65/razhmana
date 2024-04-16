<?php

use MJ\Keys\KEYS;
use MJ\Security\Security;
use MJ\Utils\Utils;

function getData123($query1, $query2, $query3, $searchKeys, $columns)
{

    $server = KEYS::$host;
    $username = KEYS::$dbUserName;
    $password = KEYS::$dbPassword;
    $dbname = KEYS::$dbName;

    global $lang, $Settings;
// Create connection
    try {
        $conn = new PDO("mysql:host=$server;dbname=$dbname", "$username", "$password");
        $conn->exec("set names utf8");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die('Unable to connect with the database');
    }

## Read value
    $draw = $_POST['draw'];
    $row = $_POST['start'];
    $rowperpage = $_POST['length']; // Rows display per page
    $columnIndex = $_POST['order'][0]['column']; // Column index
    $columnName = $_POST['columns'][$columnIndex]['data']; // Column name
    $columnSortOrder = $_POST['order'][0]['dir']; // asc or desc
    $value = $_POST['search']['value'];

## Search
    $searchQuery = " AND ( ";
    $searchArray = [];
    if (!empty($value)) {
        foreach ($searchKeys as $index => $key) {
            if ($index == count($searchKeys) - 1) {
                $searchQuery .= " $key LIKE :$key ) ";
            } else {
                $searchQuery .= " $key LIKE :$key or ";
            }
            $searchArray[$key] = "%$value%";
        }
    } else {
        $searchQuery = '';
    }

## Total number of records without filtering
    $stmt = $conn->prepare($query1);
    $stmt->execute();
    $records = $stmt->fetch();
    $totalRecords = $records['allcount'];
//
## Total number of records with filtering
    $stmt = $conn->prepare($query2 . $searchQuery);
    $stmt->execute($searchArray);
    $records = $stmt->fetch();
    $totalRecordwithFilter = $records['allcount'];

## Fetch records
    $stmt = $conn->prepare($query3 . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");

// Bind values
    foreach ($searchArray as $key => $search) {
        $stmt->bindValue(':' . $key, $search, PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', (int)$row, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$rowperpage, PDO::PARAM_INT);
    $stmt->execute();
    $empRecords = $stmt->fetchAll();

    $data = array();

    foreach ($empRecords as $row) {
//        $railroad_name = (!empty(array_column(json_decode($row['railroad_name'], true), 'value', 'slug')[$_COOKIE['language']])) ?
//            array_column(json_decode($row['railroad_name'], true), 'value', 'slug')[$_COOKIE['language']] : "";
//
//        $city_name = (!empty(array_column(json_decode($row['city_name'], true), 'value', 'slug')[$_COOKIE['language']])) ?
//            array_column(json_decode($row['city_name'], true), 'value', 'slug')[$_COOKIE['language']] : "";
//
//        $railroad_id = '<a target="_self"
//                     href="/admin/category/railroad/edit/' . $row['railroad_id'] . '"
//                     data-bs-toggle="tooltip"
//                     data-bs-placement="top"
//                     title="' . $lang['edit_2'] . '"
//                     class="action-icon">
//                      <i class="mdi mdi-square-edit-outline"></i>
//                  </a>
//                  <a href="/admin/category/railroad/delete/' . $row['railroad_id'] . '"
//                     target="_self"
//                     data-bs-toggle="tooltip"
//                     data-bs-placement="top"
//                     title="' . $lang['delete'] . '"
//                     class="action-icon">
//                      <i class="mdi mdi-delete"></i>
//                  </a>';
//        $railroad_status = '';
//        if ($row['railroad_status'] == "active") {
//            $railroad_status = "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
//        } elseif ($row['railroad_status'] == "inactive") {
//            $railroad_status = "<span class='badge badge-soft-warning font-12'>" . $lang['inactive'] . "</span>";
//        } else {
//            $railroad_status = "<span class='badge badge-soft-danger font-12'>" . $row['railroad_status'] . "</span>";
//        }

        $name = mb_strimwidth($row['task_title'], 0, 50, '...');

        $priority = '';
        if ($row['task_priority'] == "important") {
            $priority = '<span class="badge badge-soft-danger p-1">' . $lang["important"] . '</span>';
        } elseif ($row['task_priority'] == "critical") {
            $priority = '<span class="badge badge-soft-pink p-1">' . $lang["critical"] . '</span>';
        } elseif ($row['task_priority'] == "high") {
            $priority = '<span class="badge badge-soft-warning p-1">' . $lang["high"] . '</span>';
        } elseif ($row['task_priority'] == "medium") {
            $priority = '<span class="badge badge-soft-success p-1">' . $lang["medium"] . '</span>';
        } elseif ($row['task_priority'] == "low") {
            $priority = '<span class="badge badge-soft-primary p-1">' . $lang["low"] . '</span>';
        } elseif ($row['task_priority'] == "informational") {
            $priority = '<span class="badge badge-soft-info p-1">' . $lang["informational"] . '</span>';
        } else {
            $priority = '<span class="badge badge-soft-secondary p-1">XXX</span>';
        }

        $submit_date = '<bdi>' . Utils::getTimeCountry($Settings['data_time_format'], $row['task_start_date']) . "</bdi>";

        $task_status = "";
        if ($row['task_status'] == "pending") {
            $task_status = $lang['a_task_pending'];
        } elseif ($row['task_status'] == "process") {
            $task_status = $lang['a_task_process'];
        } elseif ($row['task_status'] == "rejected") {
            $task_status = $lang['a_task_rejected'];
        } elseif ($row['task_status'] == "ok") {
            $task_status = $lang['a_task_ok'];
        }

        $railroad_id = '
                  <a href="/admin/tasks/info/' . $row['task_id'] . '"
                     target="_self"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="' . $lang['show_detail'] . '"
                     class="action-icon">
                      <i class="mdi mdi-eye"></i>
                  </a>';

        $data[] = array(
            "task_id" => $row['task_id'],
            "task_title" => $name,
            "task_priority" => $priority,
            "task_start_date" => $submit_date,
            "task_status" => $task_status,
            "admin_id" => $railroad_id,
        );
    }

## Response
    $response = array(
        "draw" => intval($draw),
        "iTotalRecords" => $totalRecords,
        "iTotalDisplayRecords" => $totalRecordwithFilter,
        "aaData" => $data
    );

    echo json_encode($response);
}

if (isset($_SESSION['dt-task-for-me']) && $_SESSION['dt-task-for-me'] == "dt-task-for-me-44") {


    $admin_id = 0;
    if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
        $admin_id = intval(Security::decrypt($_COOKIE['UID']));
    }

    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_task WHERE admin_id={$admin_id}";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_task WHERE admin_id={$admin_id}  ";
    $query3 = "SELECT * FROM tbl_task WHERE admin_id={$admin_id}";
    getData123($query1, $query2, $query3, ["task_id", "task_title", "task_priority", "task_start_date", "task_status", "admin_id"], ["railroad_name", "city_name", "railroad_priority", "railroad_id"]);

}