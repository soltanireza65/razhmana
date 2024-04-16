<?php

use MJ\Keys\KEYS;
use MJ\Utils\Utils;
use MJ\Security\Security;
global $lang,$Settings;
function getData123($query1, $query2, $query3, $searchKeys, $columns)
{
    $language = 'fa_IR';
    if (isset($_COOKIE['language'])) {
        $language = $_COOKIE['language'];
    }

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

        if ($row['cargo_status'] == "accepted") {
            $status= "<span class='badge badge-soft-success font-12'>" . $lang['accepted'] . "</span>";
        } elseif ($row['cargo_status'] == "pending") {
            $status= "<span class='badge badge-soft-warning font-12'>" . $lang['pending'] . "</span>";
        } elseif ($row['cargo_status'] == "rejected") {
            $status= "<span class='badge badge-soft-danger font-12'>" . $lang['rejected'] . "</span>";
        } elseif ($row['cargo_status'] == "progress") {
            $status= "<span class='badge badge-soft-info font-12'>" . $lang['progress'] . "</span>";
        } elseif ($row['cargo_status'] == "canceled") {
            $status= "<span class='badge badge-soft-secondary font-12'>" . $lang['canceled'] . "</span>";
        } elseif ($row['cargo_status'] == "completed") {
            $status= "<span class='badge badge-soft-primary font-12'>" . $lang['completed'] . "</span>";
        }elseif ($row['cargo_status'] == "expired") {
            $status= "<span class='badge badge-soft-secondary font-12'>" . $lang['expired'] . "</span>";
        } else {
            $status= "<span class='badge badge-soft-pink font-12'>" . $row['cargo_status'] . "</span>";
        }

        $name=($row['user_firstname'])?Security::decrypt($row['user_firstname'])." ".Security::decrypt($row['user_lastname']):$lang['guest_user'];
        $username='<div class="table-user text-start">
                       <img src="'.Utils::fileExist($row['user_avatar'], USER_AVATAR).'"
                            alt="'.$name.'"
                            class="me-2 rounded-circle">
                       <a target="_self"
                          href="/admin/users/info/'. $row['user_id'].'"
                          class="text-body fw-normal">
                           '.$name.'
                       </a>
                   </div>';


        $idA='<a target="_self"
                 data-bs-toggle="tooltip"
                 data-bs-placement="top"
                 title="'.$lang['cargo_detail'].'"
                 href="/admin/cargo-in/'.$row['cargo_id'].'"
                 class="action-icon mj-action-btn">
                  <div class="fa-boxes"></div>
              </a>
              <a href="/admin/users/info/'.$row['user_id'].'"
                 target="_self"
                 data-bs-toggle="tooltip"
                 data-bs-placement="top"
                 title="'.$lang['user_info'].'"
                 class="action-icon mj-action-btn">
                  <div class="fa-user-tie"></div>
              </a>';

        $category=(!empty(array_column(json_decode($row['category_name'], true), 'value', 'slug')[$language])) ?
            array_column(json_decode($row['category_name'], true), 'value', 'slug')[$language] : $row['category_id'];
        $data[] = array(
            "cargo_id" => $row['cargo_id'],
            "user_firstname" => $username,
            "cargo_name_fa_IR" => mb_strimwidth($row['cargo_name_'.$language],0,30,"..."),
            "category_name" => $category,
            "cargo_date" => '<bdi>'. Utils::getTimeCountry($Settings['date_format'], $row['cargo_date']).'</bdi>',
            "cargo_status" => $status,
            "user_lastname" => $idA
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

if (isset($_SESSION['dt-cargo-in']) && $_SESSION['dt-cargo-in'] == "dt-cargo-in-44") {

    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_cargo_in INNER JOIN tbl_cargo_categories ON tbl_cargo_categories.category_id = tbl_cargo_in.category_id INNER JOIN tbl_users ON tbl_users.user_id = tbl_cargo_in.user_id WHERE tbl_users.user_type='businessman'  ";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_cargo_in INNER JOIN tbl_cargo_categories ON tbl_cargo_categories.category_id = tbl_cargo_in.category_id INNER JOIN tbl_users ON tbl_users.user_id = tbl_cargo_in.user_id WHERE tbl_users.user_type='businessman'  ";
    $query3 = "SELECT * FROM tbl_cargo_in INNER JOIN tbl_cargo_categories ON tbl_cargo_categories.category_id = tbl_cargo_in.category_id INNER JOIN tbl_users ON tbl_users.user_id = tbl_cargo_in.user_id WHERE tbl_users.user_type='businessman' ";
    getData123($query1, $query2, $query3, ["cargo_id", "user_firstname", "cargo_name_fa_IR","cargo_date","category_name","cargo_status","user_lastname"], ["airport_name", "city_name", "airport_priority", "airport_id"]);

}