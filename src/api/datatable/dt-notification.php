<?php

use MJ\Utils\Utils;
use MJ\Keys\KEYS;

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


//        foreach ($empRecords as $i=>$row) {
//            $dataTemp=[];
//            foreach ($columns as $index=>$key){
//                $dataTemp[$key] =  $row[$key];
//            }
//            array_push($data, $dataTemp);
//        }
    foreach ($empRecords as $row) {

        $title = explode('--', $row['notification_title']);
        if (array_key_exists($title[0], $lang)) {
            $translateTitle = $lang[$title[0]];
            for ($index = 1; $index < count($title); $index++) {
                $translateTitle = str_replace("#PARAM{$index}#", $title[$index], $translateTitle);
            }
            $title = $translateTitle;
        } else {
            $title = $row['notification_title'];
        }


        $status = '';
        if ($row['notification_status'] == "read") {
            $status = "<span class='badge badge-soft-success font-12'>" . $lang['read'] . "</span>";
        } elseif ($row['notification_status'] == "unread") {
            $status = "<span class='badge badge-soft-warning font-12'>" . $lang['unread'] . "</span>";
        } else {
            $status = "<span class='badge badge-soft-danger font-12'>" . $row['notification_status'] . "</span>";
        }

        $aTag = '<a class="showNotification action-icon"
               href="javascript:void(0);"
               data-bs-toggle="tooltip"
               data-bs-placement="top"
               title="' . $lang['show_detail'] . '"
               data-notification-id="' . $row['notification_id'] . '">
               <i class="mdi mdi-eye"></i>
               </a>';

        $data[] = array(
            "notification_id" => $row['notification_id'],
            "user_id" => ($row['notification_sender'] == "system") ? $lang['system'] : $row['notification_sender'],
            "notification_sender" => $title,
            "notification_title" => $status,
            "notification_status" => '<bdi>' . Utils::getTimeCountry($Settings['date_format'], $row['notification_time']) . '</bdi>',
            "notification_time" => $aTag,
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

if (isset($_SESSION['dt-notification']) && $_SESSION['dt-notification'] == "dt-notification-44") {

    $id = 0;
    if (isset($_REQUEST['id'])) {
        $id = (int)$_REQUEST['id'];
    }
    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_notifications WHERE user_id={$id}";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_notifications WHERE user_id={$id}  ";
    $query3 = "SELECT * FROM tbl_notifications WHERE user_id={$id}";
    getData123($query1, $query2, $query3, ["notification_id", "user_id", "notification_sender", "notification_title", "notification_status", "notification_time",], ["log_date", "log_detail", "log_os", "log_browser", "log_browser_version"]);

}