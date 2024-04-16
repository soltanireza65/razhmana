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

        $port_id = '<a target="_self"
                     href="/admin/share/whatsapp/' . $row['wa_id'] . '"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="' . $lang['a_sending'] . '"
                     class="action-icon">
                      <i class="mdi mdi-share-circle"></i>
                  </a>';

        $status = '';
        if ($row['wa_status'] == "pending") {
            $status = "<span class='badge badge-soft-warning font-12'>" . $lang['a_pending_check'] . "</span>";
        } elseif ($row['wa_status'] == "sended") {
            $status = "<span class='badge badge-soft-success font-12'>" . $lang['a_sended'] . "</span>";
        } elseif ($row['wa_status'] == "rejected") {
            $status = "<span class='badge badge-soft-danger font-12'>" . $lang['reject'] . "</span>";
        } else {
            $status = "<span class='badge badge-soft-secondary font-12'>" . $row['wa_status'] . "</span>";
        }


        $data[] = array(
            "wa_id" => $row['wa_id'],
            "user_id" => mb_strimwidth($row['wa_massage'], 0, 50, "..."),
            "wa_massage" => "<bdi>" . Utils::getTimeCountry($Settings['data_time_format'], $row['wa_send_time']) . "</bdi>",
            "wa_status" => $status,
            "wa_submit_time" => $port_id,
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

if (isset($_SESSION['dt-whatsapp-massage']) && $_SESSION['dt-whatsapp-massage'] == "dt-whatsapp-massage-44") {

    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_whatsapp_massage";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_whatsapp_massage WHERE 1  ";
    $query3 = "SELECT * FROM tbl_whatsapp_massage WHERE 1";
    getData123($query1, $query2, $query3, ["wa_id", "user_id", "wa_massage", "wa_status", "wa_submit_time"], ["port_name", "city_name", "port_priority", "port_id"]);

}