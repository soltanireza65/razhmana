<?php

use Couchbase\SearchQuery;
use MJ\Keys\KEYS;
use MJ\Security\Security;
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
//print_r($_POST['columns']);


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
                if (in_array($key[0], ["employ_first_name", "employ_last_name" ])) {

                    if ($index == count($searchArray) - 1) {
                        $searchQuery .= " $key[0] LIKE :$key[0] ) ";
                    } else {
                        $searchQuery .= " $key[0] LIKE :$key[0] or ";
                    }
                    $searchParams[$key[0]] = "%$key[1]%";
                } elseif ($key[0] == 'c_name') {
                    if ($index == count($searchArray) - 1) {
                        $searchQuery .= " c.city_name LIKE :$key[0] ) ";
                    } else {
                        $searchQuery .= " c.city_name LIKE :$key[0] AND ";
                    }
                    $searchParams[$key[0]] = "%$key[1]%";

                } elseif (in_array($key[0], ["employ_status"])) {

                    if ($key[1] == "all") {
                        if ($index == count($searchArray) - 1) {
                            $searchQuery .= " 1=1 ) ";
                        } else {
                            $searchQuery .= " 1=1 or ";
                        }
                    } else {
                        if ($index == count($searchArray) - 1) {
                            $searchQuery .= " $key[0] = :$key[0] ) ";
                        } else {
                            $searchQuery .= " $key[0] = :$key[0] or ";
                        }
                        $searchParams[$key[0]] = "$key[1]";
                    }

                    $searchParams[$key[0]] = "$key[1]";

                } elseif (in_array($key[0], ["employ_title"])) {

                    if ($key[1] == "all") {
                        if ($index == count($searchArray) - 1) {
                            $searchQuery .= " 1=1 ) ";
                        } else {
                            $searchQuery .= " 1=1 ) ";
                        }
                    } else {

                        if ($index == count($searchArray) - 1) {
                            $searchQuery .= " `employ_title` LIKE $key[1] OR `employ_title` LIKE '$key[1],%' OR `employ_title` LIKE '&,$key[1]' OR `employ_title` LIKE '%,$key[1],%' ) ";
                        } else {
                            $searchQuery .= " `employ_title` LIKE $key[1] OR `employ_title` LIKE '$key[1],%' OR `employ_title` LIKE '&,$key[1]' OR `employ_title` LIKE '%,$key[1],%' ) ";
                        }
//                        $searchParams[$key[0]] = "$key[1]";


                    }
                } else {
//                    if ($index == count($searchArray) - 1) {
//                        $searchQuery .= " $key[0] LIKE :$key[0] ) ";
//                    } else {
//                        $searchQuery .= " $key[0] LIKE :$key[0] or ";
//                    }
//                    $searchParams[$key[0]] = "%$key[1]%";
                }

            }
        }
//        print_r($searchQuery);
    }
//print_r($searchQuery);
//print_r($query2 . $searchQuery);

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
//  print_r($query3 .$searchQuery);


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

    foreach ($empRecords as $row) {


        $status = null;
        if ($row['employ_status'] == "pending") {
            $status = "<span class='badge badge-soft-warning font-13'>" . $lang['u_inquiry_air_pending'] . "</span>";
        } elseif ($row['employ_status'] == "process") {
            $status = "<span class='badge badge-soft-info font-13'>" . $lang['u_inquiry_air_process'] . "</span>";
        } elseif ($row['employ_status'] == "completed") {
            $status = "<span class='badge badge-soft-success font-13'>" . $lang['u_inquiry_air_completed'] . "</span>";
        } elseif ($row['employ_status'] == "reject") {
            $status = "<span class='badge badge-soft-pink font-13'>" . $lang['reject'] . "</span>";
        } else {
            $status = "<span class='badge badge-soft-danger font-13'>" . $row['employ_status'] . "</span>";
        }

        $titles = Hire::getEmployTitle($row['employ_title'])->response;
        $title = null;
        foreach ($titles as $loop) {
            $title .= (isset($loop->category_name) && !empty(array_column(json_decode($loop->category_name, true), 'value', 'slug')[$language])) ?
                array_column(json_decode($loop->category_name, true), 'value', 'slug')[$language] . " <br> " : $loop->category_id . " <br> ";
        }

        $tagA = '<a href="/admin/hire/info/' . $row['employ_id'] . '"
                                                   target="_self"
                                                   data-bs-toggle="tooltip"
                                                   data-bs-placement="top"
                                                   title="' . $lang['show_detail'] . '"
                                                   class="action-icon">
                                                    <i class="mdi mdi-eye"></i>
                                                </a>';
        $data[] = array(
            "employ_id" => $row['employ_id'],
            "employ_first_name" => $row['employ_first_name'],
            "employ_last_name" => $row['employ_last_name'],
            "c_name" =>(!empty(array_column(json_decode( $row['c_name'], true), 'value', 'slug')[$language])) ?
                array_column(json_decode( $row['c_name'], true), 'value', 'slug')[$language] : "---",
            "employ_status" => $status,
            "employ_title" => $title,
            "employ_submit_date" => Utils::getTimeCountry($Settings['date_format'], $row['employ_submit_date']),
            "employ_employ" => $tagA,
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


if (isset($_SESSION['dt-hire']) && $_SESSION['dt-hire'] == "dt-hire-44") {
//print_r($_POST);
    $query1 = "SELECT COUNT(*) AS allcount FROM `tbl_employ` INNER join tbl_cities AS c
                            on c.city_id =  tbl_employ.employ_city
                            where 1 ";
    $query2 = "SELECT COUNT(*) AS allcount FROM `tbl_employ` INNER join tbl_cities AS c
                            on c.city_id =  tbl_employ.employ_city
                            where 1 ";
    $query3 = "SELECT tbl_employ.* , c.city_name as c_name FROM tbl_employ INNER join tbl_cities AS c
         on c.city_id =  tbl_employ.employ_city
         where 1 ";
    getData123($query1, $query2, $query3, ["employ_id", "employ_first_name", "employ_last_name", "c_name","employ_status", "employ_title", "employ_submit_date", "employ_employ"], ["railroad_name", "c_name", "railroad_priority", "railroad_id"]);

}