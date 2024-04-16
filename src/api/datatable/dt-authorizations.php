<?php

use MJ\Keys\KEYS;
use MJ\Security\Security;

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


        $aTag = '<a href="/admin/users/info/' . $row['user_id'] . '"
               target="_self"
               data-bs-toggle="tooltip"
               data-bs-placement="top"
               title="' . $lang['user_info'] . '"
               class="action-icon mj-action-btn">
                <div class="fa-eye"></div></a>';

        $data[] = array(
            "option_id" => $row['user_id'],
            "user_id" => "<span class='badge badge-soft-warning font-12'>" . $lang['a_pending_check'] . "</span>",
            "user_firstname" => Security::decrypt($row["user_firstname"]),
            "user_lastname" => Security::decrypt($row["user_lastname"]),
            "user_mobile" => Security::decrypt($row["user_mobile"]),
            "option_status" => $aTag,
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

if (isset($_SESSION['dt-authorizations']) && $_SESSION['dt-authorizations'] == "dt-authorizations-44") {

    $query1 = "SELECT DISTINCT COUNT(tbl_user_options.user_id) AS allcount ,tbl_users.user_firstname , tbl_users.user_lastname , tbl_users.user_mobile FROM `tbl_user_options` inner join tbl_users on tbl_user_options.user_id =tbl_users.user_id WHERE option_status='pending' ";
    $query2 = "SELECT DISTINCT COUNT(tbl_user_options.user_id) AS allcount ,tbl_users.user_firstname , tbl_users.user_lastname , tbl_users.user_mobile FROM `tbl_user_options` inner join tbl_users on tbl_user_options.user_id =tbl_users.user_id WHERE option_status='pending' ";
    $query3 = "SELECT DISTINCT tbl_user_options.user_id ,tbl_users.user_firstname , tbl_users.user_lastname , tbl_users.user_mobile FROM tbl_user_options inner join tbl_users on tbl_user_options.user_id =tbl_users.user_id WHERE option_status='pending' ";
    getData123($query1, $query2, $query3, ["option_id", "user_id", "user_firstname", "user_lastname", "user_mobile", "option_status"], ["railroad_name", "city_name", "railroad_priority", "railroad_id"]);

}