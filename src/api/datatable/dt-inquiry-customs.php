<?php

use MJ\Keys\KEYS;
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
        $name = mb_strimwidth($row['freight_customs'], 0, 50, '...');

        $freight_submit_date = '<bdi>' . Utils::getTimeCountry($Settings["date_format"], $row["freight_submit_date"]) . '</bdi>';
        $freight_start_date = '<bdi>' . Utils::getTimeCountry($Settings["date_format"], $row["freight_start_date"]) . '</bdi>';

        $id = '<a target="_self"
                     href="/admin/inquiry/customs/' . $row['freight_id'] . '"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="' . $lang['edit_2'] . '"
                     class="action-icon">
                      <i class="mdi mdi-eye"></i>
                  </a>';
        $status = '';
        if ($row['freight_status'] == "pending") {
            $status = "<span class='badge badge-soft-warning font-12'>" . $lang['a_freight_pending'] . "</span>";
        } elseif ($row['freight_status'] == "process") {
            $status = "<span class='badge badge-soft-info font-12'>" . $lang['a_freight_process'] . "</span>";
        } elseif (in_array($row['freight_status'], ["completed", "read"])) {
            $status = "<span class='badge badge-soft-primary font-12'>" . $lang['a_freight_completed'] . "</span>";
        } else {
            $status = "<span class='badge badge-soft-danger font-12'>" . $row['freight_status'] . "</span>";
        }

        $data[] = array(
            "freight_id" => $row['freight_id'],
            "freight_customs" => $name,
            "freight_submit_date" => $freight_submit_date,
            "freight_start_date" => $freight_start_date,
            "freight_status" => $status,
            "currency_id" => $id,
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

if (isset($_SESSION['dt-inquiry-customs']) && $_SESSION['dt-inquiry-customs'] == "dt-inquiry-customs-44") {

    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_freight_customs WHERE 1 ";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_freight_customs WHERE 1 ";
    $query3 = "SELECT * FROM tbl_freight_customs WHERE 1 ";
    getData123($query1, $query2, $query3, ["freight_id", "freight_customs", "freight_submit_date", "freight_start_date", "freight_status", "currency_id"], ["freight_name", "freight_submit_date", "freight_start_date", "freight_id", "freight_status"]);

}