<?php

use MJ\Keys\KEYS;
use MJ\Utils\Utils;

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
    $searchQuery = "";
    $searchArray = [];
    $searchParams = [];
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


    if (isset($_POST['columns'])) {
        for ($i = 0, $ien = count($_POST['columns']); $i < $ien; $i++) {
            $requestColumn = $_POST['columns'][$i];
            if ($requestColumn['search']['value']) {
                $searchArray[] = [$requestColumn['data'], $requestColumn['search']['value']];
            }
        }
        if (!empty($searchArray) && count($searchArray) > 0) {
//            print_r($searchArray);
            $searchQuery = " AND ( ";
            foreach ($searchArray as $index => $key) {
                if ($key[0] == 'request_id') {
                    if ($index == count($searchArray) - 1) {
                        $searchQuery .= " $key[0] = :$key[0] ) ";
                    } else {
                        $searchQuery .= " $key[0] = :$key[0] AND ";
                    }
                    $searchParams[$key[0]] = "$key[1]";
                } elseif ($key[0] == 'cargo_name_'.$language) {
                    if ($index == count($searchArray) - 1) {
                        $searchQuery .= " $key[0] LIKE :$key[0] ) ";
                    } else {
                        $searchQuery .= " $key[0] LIKE :$key[0] AND ";
                    }
                    $searchParams[$key[0]] = "%$key[1]%";

                } elseif ($key[0] == 'cargo_origin_id') {
                    if ($index == count($searchArray) - 1) {
                        $searchQuery .= " c.city_name LIKE :$key[0] ) ";
                    } else {
                        $searchQuery .= " c.city_name LIKE :$key[0] AND ";
                    }
                    $searchParams[$key[0]] = "%$key[1]%";

                } elseif ($key[0] == 'cargo_destination_id') {
                    if ($index == count($searchArray) - 1) {
                        $searchQuery .= " d.city_name LIKE :$key[0] ) ";
                    } else {
                        $searchQuery .= " d.city_name LIKE :$key[0] AND ";
                    }
                    $searchParams[$key[0]] = "%$key[1]%";

                } else {
                    if ($index == count($searchArray) - 1) {
                        $searchQuery .= " $key[0] LIKE :$key[0] ) ";
                    } else {
                        $searchQuery .= " $key[0] LIKE :$key[0] AND ";
                    }
                    $searchParams[$key[0]] = "%$key[1]%";
                }
            }
//            $searchQuery .= " ) ";
        }
    }
//print_r($searchQuery);
## Search
//    $searchQuery = " AND ( ";
//    $searchArray = [];
//    if (!empty($value)) {
//        foreach ($searchKeys as $index => $key) {
//            if ($index == count($searchKeys) - 1) {
//                $searchQuery .= " $key LIKE :$key ) ";
//            } else {
//                $searchQuery .= " $key LIKE :$key or ";
//            }
//            $searchArray[$key] = "%$value%";
//        }
//    } else {
//        $searchQuery = '';
//    }

## Total number of records without filtering
    $stmt = $conn->prepare($query1);
    $stmt->execute();
    $records = $stmt->fetch();
    $totalRecords = $records['allcount'];
//
## Total number of records with filtering
    $stmt = $conn->prepare($query2 . $searchQuery);
    $stmt->execute($searchParams);
    $records = $stmt->fetch();
    $totalRecordwithFilter = $records['allcount'];

## Fetch records
    $stmt = $conn->prepare($query3 . $searchQuery . " ORDER BY " . $columnName . " " . $columnSortOrder . " LIMIT :limit,:offset");

// Bind values
    foreach ($searchParams as $key => $search) {
        $stmt->bindValue(':' . $key, $search, PDO::PARAM_STR);
    }

    $stmt->bindValue(':limit', (int)$row, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int)$rowperpage, PDO::PARAM_INT);
    $stmt->execute();
    $empRecords = $stmt->fetchAll();

    $data = array();
    $language = ($_COOKIE['language']) ? $_COOKIE['language'] : "fa_IR";
    foreach ($empRecords as $row) {

        $cityO = (!empty(array_column(json_decode($row['cityO'], true), 'value', 'slug')[$language])) ?
            array_column(json_decode($row['cityO'], true), 'value', 'slug')[$language] : "---";

        $cityDesc = (!empty(array_column(json_decode($row['cityDesc'], true), 'value', 'slug')[$language])) ?
            array_column(json_decode($row['cityDesc'], true), 'value', 'slug')[$language] : "---";


        $status = null;
        if ($row['request_status'] == "accepted") {
            $status = "<span class='badge badge-soft-success font-12'>" . $lang['accepted'] . "</span>";
        } elseif ($row['request_status'] == "pending") {
            $status = "<span class='badge badge-soft-warning font-12'>" . $lang['pending'] . "</span>";
        } elseif ($row['request_status'] == "rejected") {
            $status = "<span class='badge badge-soft-danger font-12'>" . $lang['rejected'] . "</span>";
        } elseif ($row['request_status'] == "progress") {
            $status = "<span class='badge badge-soft-info font-12'>" . $lang['progress'] . "</span>";
        } elseif ($row['request_status'] == "canceled") {
            $status = "<span class='badge badge-soft-secondary font-12'>" . $lang['canceled'] . "</span>";
        } elseif ($row['request_status'] == "completed") {
            $status = "<span class='badge badge-soft-primary font-12'>" . $lang['completed'] . "</span>";
        } else {
            $status = "<span class='badge badge-soft-pink font-12'>" . $row['request_status'] . "</span>";
        }


        if ($row['request_price'] == "0") {
            $price = $lang['u_agreement'];
        } else {
            $p = (!empty(array_column(json_decode($row['currency_name'], true), 'value', 'slug')[$language])) ?
                array_column(json_decode($row['currency_name'], true), 'value', 'slug')[$language] : "---";
            $price = number_format($row['request_price']) . " " . $p;
        }

        $tagA = '<a href="/admin/users/info/' . $row['user_id'] . '"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  title="' . $lang['user_info'] . '"
                  target="_self" class="action-icon">
                   <i class="mdi mdi-account-circle-outline"></i>
               </a>
               <a href="/admin/cargo-in/' . $row['cargo_id'] . '"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  title="' . $lang['cargo_info'] . '"
                  target="_self" class="action-icon">
                   <i class="mdi mdi-truck-trailer"></i>
               </a>';
        $data[] = array(
            "request_id" => $row['request_id'],
            "cargo_name_".$language => $row['cargo_name_'.$language],
            "cargo_origin_id" => $cityO,
            "cargo_destination_id" => $cityDesc,
            "request_price" => $price,
            "request_date" => Utils::getTimeCountry($Settings['date_format'], $row['request_date']),
            "request_status" => $status,
            "cargo_monetary_unit" => $tagA,
        );
//        print_r($row);
//        exit();
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

if (isset($_SESSION['dt-request-in']) && $_SESSION['dt-request-in'] == "dt-request-in-44") {

    $id = 0;
    if (isset($_REQUEST['id'])) {
        $id = $_REQUEST['id'];
    }

    $language = 'fa_IR';
    if (isset($_COOKIE['language'])) {
        $language = $_COOKIE['language'];
    }

//print_r($_REQUEST);
    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_requests_in a 
                INNER JOIN tbl_cargo_in b ON b.cargo_id=a.cargo_id
                INNER JOIN tbl_currency e ON b.cargo_monetary_unit=e.currency_id
                LEFT JOIN tbl_cities c ON c.city_id=b.cargo_origin_id
                LEFT JOIN tbl_cities d ON d.city_id=b.cargo_destination_id WHERE 1 ";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_requests_in a 
                INNER JOIN tbl_cargo_in b ON b.cargo_id=a.cargo_id
                INNER JOIN tbl_currency e ON b.cargo_monetary_unit=e.currency_id
                LEFT JOIN tbl_cities c ON c.city_id=b.cargo_origin_id
                LEFT JOIN tbl_cities d ON d.city_id=b.cargo_destination_id WHERE 1 ";
    $query3 = "SELECT a.request_id,a.user_id,a.request_date,a.request_status,b.cargo_id,b.cargo_name_{$language},a.request_price,b.cargo_monetary_unit,c.city_name as cityO,d.city_name as cityDesc,e.currency_name  FROM tbl_requests_in a 
                INNER JOIN tbl_cargo_in b ON b.cargo_id=a.cargo_id
                INNER JOIN tbl_currency e ON b.cargo_monetary_unit=e.currency_id
                LEFT JOIN tbl_cities c ON c.city_id=b.cargo_origin_id
                LEFT JOIN tbl_cities d ON d.city_id=b.cargo_destination_id WHERE 1 ";
    getData123($query1, $query2, $query3, ["request_id", "cargo_name_".$language, "cargo_origin_id", "cargo_destination_id", "request_price", "request_date", "request_status", "cargo_monetary_unit"], []);

}