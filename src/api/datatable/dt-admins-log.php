<?php

use MJ\Security\Security;
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
        $detail = "";
        if (array_key_exists($row['log_detail'], $lang)) {
            $detail = $lang[$row['log_detail']];
        } else {
            $temp = explode('_', $row['log_detail']);
            $temp2 = array_slice($temp, 0, -1);
            $temp3 = implode("_", $temp2);
            if (array_key_exists($temp3, $lang)) {
                $detail = $lang[$temp3] . " " . end($temp);
            } else {
                $detail = $row['log_detail'];
            }
        }
        $log_browser = "";
        if (array_key_exists($row['log_browser'], $lang)) {
            $log_browser = $lang[$row['log_browser']] . " " . $row['log_browser_version'];
        } else {
            $log_browser = $lang[$row['log_browser']] . " " . $row['log_browser_version'];
        }

        $data[] = array(
            "log_id" => $row['log_id'],
            "admin_name" => Security::decrypt($row['admin_name']),
            "log_date" => "<bdi>" . Utils::getTimeCountry($Settings['data_time_format'], $row['log_date']) . "</bdi>",
            "log_detail" => $detail,
            "log_os" => $row['log_os'],
            "log_browser" => $log_browser,
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

if (isset($_SESSION['dt-admins-log']) && $_SESSION['dt-admins-log'] == "dt-admins-log-44") {

    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_admin_logs INNER JOIN tbl_admins ON tbl_admin_logs.admin_id = tbl_admins.admin_id  ";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_admin_logs INNER JOIN tbl_admins ON tbl_admin_logs.admin_id = tbl_admins.admin_id WHERE 1  ";
    $query3 = "SELECT * FROM tbl_admin_logs INNER JOIN tbl_admins ON tbl_admin_logs.admin_id = tbl_admins.admin_id WHERE 1";
    getData123($query1, $query2, $query3, ["admin_name", "log_detail", "log_os", "log_browser"], ["admin_name", "log_date", "log_detail", "log_os", "log_browser", "log_browser_version"]);

}