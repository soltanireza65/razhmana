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
        $port_name =  (!empty(array_column(json_decode($row['port_name'], true), 'value', 'slug')[$_COOKIE['language']])) ?
            array_column(json_decode($row['port_name'], true), 'value', 'slug')[$_COOKIE['language']] : "";

        $city_name =  (!empty(array_column(json_decode($row['city_name'], true), 'value', 'slug')[$_COOKIE['language']])) ?
            array_column(json_decode($row['city_name'], true), 'value', 'slug')[$_COOKIE['language']] : "";

        $port_id= '<a target="_self"
                     href="/admin/category/port/edit/'.$row['port_id'].'"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="'.$lang['edit_2'].'"
                     class="action-icon">
                      <i class="mdi mdi-square-edit-outline"></i>
                  </a>
                  <a href="/admin/category/port/delete/'.$row['port_id'].'"
                     target="_self"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="'.$lang['delete'].'"
                     class="action-icon">
                      <i class="mdi mdi-delete"></i>
                  </a>';
        $port_status='';
        if ($row['port_status'] == "active") {
            $port_status= "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
        } elseif ($row['port_status'] == "inactive") {
            $port_status= "<span class='badge badge-soft-warning font-12'>" . $lang['inactive'] . "</span>";
        } else {
            $port_status= "<span class='badge badge-soft-danger font-12'>" . $row['port_status'] . "</span>";
        }

        $data[] = array(
            "port_id" => $row['port_id'],
            "port_name" => $port_name,
            "city_name" => $city_name,
            "port_priority" => $row['port_priority'],
            "port_status" => $port_status,
            "port_options" => $port_id,
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

if (isset($_SESSION['dt-ports']) && $_SESSION['dt-ports'] == "dt-ports-44") {

    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_ports INNER JOIN tbl_cities ON tbl_cities.city_id = tbl_ports.city_id  ";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_ports INNER JOIN tbl_cities ON tbl_cities.city_id = tbl_ports.city_id WHERE 1  ";
    $query3 = "SELECT * FROM tbl_ports INNER JOIN tbl_cities ON tbl_ports.city_id = tbl_cities.city_id WHERE 1";
    getData123($query1, $query2, $query3, ["port_name", "city_name", "port_priority","port_status"], ["port_name", "city_name", "port_priority", "port_id"]);

}