<?php

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

    foreach ($empRecords as $row) {

        $cv_action = '<a target="_blank"
                     href="/admin/driver-services/' . $row['cv_id'] . '"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="' . $lang['edit_2'] . '"
                     class="action-icon"
                     >
                      <i class="mdi mdi-square-edit-outline"></i>
                  </a>
                  ';

        if ($row['cv_status'] == "rejected") {
            $status = "<span class='badge badge-soft-success font-12'>" . $lang['rejected'] . "</span>";
        } elseif ($row['cv_status'] == "pending") {
            $status = "<span class='badge badge-soft-warning font-12'>" . $lang['pending'] . "</span>";
        } elseif ($row['cv_status'] == "accepted") {
            $status = "<span class='badge badge-soft-warning font-12'>" . $lang['accepted'] . "</span>";
        } else {
            $status = "<span class='badge badge-soft-danger font-12'>" . $row['cv_status'] . "</span>";
        }


        $data[] = array(
            "cv_id" => $row['cv_id'],
            "cv_name" => $row['cv_name'],
            "cv_lname" => $row['cv_lname'],
            "cv_submit_date" => \MJ\Utils\Utils::getTimeByLang($row['cv_submit_date']),
            "cv_status" => $status,
            "cv_action"=>$cv_action
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

if (isset($_SESSION['dt-cv']) && $_SESSION['dt-cv'] == "dt-cv-44") {

    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_driver_cv   ";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_driver_cv   WHERE 1  ";
    $query3 = "SELECT * FROM tbl_driver_cv   WHERE 1";
    getData123($query1, $query2, $query3, ["cv_id", "cv_name", "cv_lname", "cv_submit_date", "cv_status" , "cv_action"], ["cv_id",
        "cv_name",
        "cv_lname",
        "cv_submit_date",
        "cv_status", "cv_action"]);

}