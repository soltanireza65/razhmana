<?php

use MJ\Keys\KEYS;
use MJ\Security\Security;
use MJ\Utils\Utils;

function getData123($query1, $query2, $query3, $searchKeys, $columns)
{

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
            $searchQuery = " AND ( ";
            foreach ($searchArray as $index => $key) {
                if ($key[0] == 'source_city_id') {
                    $cities = Location::getCityByNmaeLike($key[1]);
                    foreach ($cities as $cindex => $city) {
//                        if ($index == count($cities) - 1) {
//                            $searchQuery .= "$key[0]  =:source_city_id_$cindex or ";
//                        } else {
                        $searchQuery .= " $key[0]  =:source_city_id_$cindex or ";
//                        }

                        $searchParams["$key[0]_$cindex"] = "$city";
                    }
//                    $searchParams[$key[0]] = (isset($res[0]) && !empty($res[0]))?$res[0]:0;
//                    $searchParams[$key[0]] = (isset($res[0]) && !empty($res[0]))?$res[0]:0;
//                    print_r($key[0]);
                } elseif ($key[0] == 'dest_city_id') {
                    $cities = Location::getCityByNmaeLike($key[1]);
                    foreach ($cities as $cindex => $city) {
//                            if ($index == count($cities) - 1) {
//                                $searchQuery .= "$key[0]  =:dest_city_id_$cindex ) ";
//                            } else {
                        $searchQuery .= " $key[0]  =:dest_city_id_$cindex or ";
//                            }

                        $searchParams["$key[0]_$cindex"] = "$city";
                    }
                } elseif ($key[0] == 'source_customs_id') {

                    $cities = Location::getCountryByNameLike($key[1]);
                    foreach ($cities as $cindex => $city) {
                        $searchQuery .= " source_city_id  =:source_city_id__$cindex or ";
                        $searchParams["source_city_id__$cindex"] = "$city";
                    }

                } elseif ($key[0] == 'dest_customs_id') {
                    $cities = Location::getCountryByNameLike($key[1]);
                    foreach ($cities as $cindex => $city) {
                        $searchQuery .= " dest_city_id  =:dest_city_id__$cindex or ";
                        $searchParams["dest_city_id__$cindex"] = "$city";
                    }
                } else {
                    $searchQuery .= " $key[0] LIKE :$key[0] or ";
                    $searchParams[$key[0]] = "%$key[1]%";
                }
            }
            $searchQuery .= "   1!=1 ) ";
        }
//        print_r($searchParams);
    }
//    print_r($query3 . $searchQuery);
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
//    print_r($query3.$searchQuery);
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
    $language = $_COOKIE['language'];
    foreach ($empRecords as $row) {
        $name = mb_strimwidth($row['freight_name'], 0, 50, '...');

//        $freight_submit_date = '<bdi>' . Utils::getTimeCountry($Settings["date_format"], $row["freight_submit_date"]) . '</bdi>';
//        $freight_start_date = '<bdi>' . Utils::getTimeCountry($Settings["date_format"], $row["freight_start_date"]) . '</bdi>';

        $id = '<a target="_self"
                     href="/admin/inquiry/ground/' . $row['freight_id'] . '"
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
        } elseif ($row['freight_status'] == "completed") {
            $status = "<span class='badge badge-soft-primary font-12'>" . $lang['a_freight_completed'] . "</span>";
        } else {
            $status = "<span class='badge badge-soft-danger font-12'>" . $row['freight_status'] . "</span>";
        }

        $source_city_id = Location::getMultiCityAndCountryByCityId($row['source_city_id'])->response[0];
        $source_city = (!empty(array_column(json_decode($source_city_id->city_name, true), 'value', 'slug')[$language])) ?
            array_column(json_decode($source_city_id->city_name, true), 'value', 'slug')[$language] : $row['source_city_id'];
        $source_country = (!empty(array_column(json_decode($source_city_id->country_name, true), 'value', 'slug')[$language])) ?
            array_column(json_decode($source_city_id->country_name, true), 'value', 'slug')[$language] : $row['source_city_id'];

        $dest_city_id = Location::getMultiCityAndCountryByCityId($row['dest_city_id'])->response[0];
        $dest_city = (!empty(array_column(json_decode($dest_city_id->city_name, true), 'value', 'slug')[$language])) ?
            array_column(json_decode($dest_city_id->city_name, true), 'value', 'slug')[$language] : $row['dest_city_id'];
        $dest_country = (!empty(array_column(json_decode($dest_city_id->country_name, true), 'value', 'slug')[$language])) ?
            array_column(json_decode($dest_city_id->country_name, true), 'value', 'slug')[$language] : $row['dest_city_id'];

        $data[] = array(
            "freight_id" => $row['freight_id'],
            "freight_name" => $name,
            "source_city_id" => $source_city,
            "dest_city_id" => $dest_city,
            "source_customs_id" => $source_country,
            "dest_customs_id" => $dest_country,
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

if (isset($_SESSION['dt-inquiry-ground']) && $_SESSION['dt-inquiry-ground'] == "dt-inquiry-ground-44") {

    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_freight_ground WHERE 1 ";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_freight_ground WHERE 1 ";
    $query3 = "SELECT * FROM tbl_freight_ground WHERE 1 ";
    getData123($query1, $query2, $query3, ["freight_id", "freight_name", "source_city_id", "dest_city_id", "source_customs_id", "dest_customs_id", "freight_status", "currency_id"], ["freight_name", "freight_submit_date", "freight_start_date", "freight_id", "freight_status"]);

}