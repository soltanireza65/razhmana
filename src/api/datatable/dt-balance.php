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

$aTag=' <a href="/admin/users/info/'.$row['user_id'].'"
           target="_self"
           data-bs-toggle="tooltip"
           data-bs-placement="top"
           title="'.$lang['user_info'].'"
           class="action-icon mj-action-btn">
            <div class="fa-eye"></div>
        </a>';

        $fn = (empty($row['user_firstname'])) ? '' : Security::decrypt($row['user_firstname']);
        $ln = (empty($row['user_lastname'])) ? '' : Security::decrypt($row['user_lastname']);
        $cName=(!empty(array_column(json_decode($row['currency_name'], true), 'value', 'slug')[$_COOKIE['language']])) ?
            array_column(json_decode($row['currency_name'], true), 'value', 'slug')[$_COOKIE['language']] : $row['currency_id'];
        $data[] = array(
            "balance_id" => $row['balance_id'],
            "user_lastname" => $fn,
            "currency_options" => $ln,
            "user_firstname" => $row['user_id'],
            "balance_value" => $cName,
            "balance_frozen" => number_format($row['balance_value']),
            "balance_in_withdraw" => number_format($row['balance_frozen']),
            "currency_name" => number_format($row['balance_in_withdraw']),
            "currency_status" => $aTag,
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

if (isset($_SESSION['dt-balance']) && $_SESSION['dt-balance'] == "dt-balance-44") {

    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_balance INNER JOIN tbl_currency ON tbl_balance.currency_id  = tbl_currency.currency_id INNER JOIN tbl_users ON tbl_users.user_id=tbl_balance.user_id WHERE 1 ";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_balance INNER JOIN tbl_currency ON tbl_balance.currency_id  = tbl_currency.currency_id INNER JOIN tbl_users ON tbl_users.user_id=tbl_balance.user_id  WHERE 1  ";
    $query3 = "SELECT * FROM tbl_balance INNER JOIN tbl_currency ON tbl_balance.currency_id  = tbl_currency.currency_id INNER JOIN tbl_users ON tbl_users.user_id=tbl_balance.user_id  WHERE 1";
    getData123($query1, $query2, $query3, ["balance_id", "user_lastname","currency_options", "user_firstname", "balance_value", "balance_frozen", "balance_in_withdraw","currency_name","currency_status"], ["port_name", "city_name", "port_priority", "port_id"]);

}