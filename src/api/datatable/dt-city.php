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


//        foreach ($empRecords as $i=>$row) {
//            $dataTemp=[];
//            foreach ($columns as $index=>$key){
//                $dataTemp[$key] =  $row[$key];
//            }
//            array_push($data, $dataTemp);
//        }
    foreach ($empRecords as $row) {
        $city_name = (!empty(array_column(json_decode($row['city_name'], true), 'value', 'slug')[$_COOKIE['language']])) ?
            array_column(json_decode($row['city_name'], true), 'value', 'slug')[$_COOKIE['language']] : "";
        $country_name = (!empty(array_column(json_decode($row['country_name'], true), 'value', 'slug')[$_COOKIE['language']])) ?
            array_column(json_decode($row['country_name'], true), 'value', 'slug')[$_COOKIE['language']] : "";

        $city_id = '<a target="_self"
                     href="/admin/city/edit/' . $row['city_id'] . '"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="' . $lang['edit_2'] . '"
                     class="action-icon">
                      <i class="mdi mdi-square-edit-outline"></i>
                  </a>';
        /*
                <a href="/admin/city/delete/'.$row['city_id'].'"
                             target="_self"
                             data-bs-toggle="tooltip"
                             data-bs-placement="top"
                             title="'.$lang['delete'].'"
                             class="action-icon">
                              <i class="mdi mdi-delete"></i>
                          </a>
                */
        $data[] = array(
            "city_id" => $row['city_id'],
            "city_name" => $city_name,
            "country_name" => $country_name,
            "city_lat" => $row['city_lat'],
            "city_long" => $row['city_long'],
            "city_priority" => $row['city_priority'],
            "city_options" => $city_id,
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

if (isset($_SESSION['dt-city']) && $_SESSION['dt-city'] == "dt-city-44") {

    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_cities INNER JOIN tbl_country ON tbl_country.country_id = tbl_cities.country_id  ";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_cities INNER JOIN tbl_country ON tbl_country.country_id = tbl_cities.country_id WHERE 1  ";
    $query3 = "SELECT * FROM tbl_cities INNER JOIN tbl_country ON tbl_country.country_id = tbl_cities.country_id WHERE 1";
    getData123($query1, $query2, $query3, ["city_name", "country_name", "city_lat", "city_long", "city_options"], ["city_name", "country_name", "city_priority", "city_id"]);

}