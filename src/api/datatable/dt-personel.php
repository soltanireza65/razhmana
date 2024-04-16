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
            if ($key != 'personel_action') {
                if ($index == count($searchKeys) - 2) {
                    $searchQuery .= " $key LIKE :$key ) ";
                } else {
                    $searchQuery .= " $key LIKE :$key or ";
                }
                $searchArray[$key] = "%$value%";
            }

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
                     href="/admin/personel/edit/' . $row['personel_ref_code'] . '"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="' . $lang['edit_2'] . '"
                     class="mj-action-btn action-icon"
                     >
                      <div class="mdi mdi-square-edit-outline"></div>
                  </a>
                 <a href="/personnel/' . $row['personel_ref_code'] . '" 
                 target="_blank" data-bs-toggle="tooltip"
                  data-bs-placement="top" title="اطلاعات کاربری"
                   class="action-icon mj-action-btn">
                <div class="fa-eye"></div></a>
                  ';


        $data[] = array(
            "personel_id" => $row['personel_id'],
            "personel_name_fa_IR" => $row['personel_name_fa_IR'],
            "personel_lname_fa_IR" => $row['personel_lname_fa_IR'],
            "personel_create_at" => Utils::getTimeByLang($row['personel_create_at']),
            "personel_action" => $cv_action
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

if (isset($_SESSION['dt-personel']) && $_SESSION['dt-personel'] == "dt-personel-44") {

    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_personls_card   ";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_personls_card   WHERE 1  ";
    $query3 = "SELECT * FROM tbl_personls_card   WHERE 1";
    getData123($query1, $query2, $query3, ["personel_id", "personel_name", "personel_lname", "personel_create_at", "personel_action"], ["personel_id",
        "personel_name",
        "personel_lname",
        "personel_create_at",
        "personel_action"]);

}