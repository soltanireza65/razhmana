<?php

use Couchbase\SearchQuery;
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
                if (in_array($key[0], ["user_lastname", "user_firstname"])) {
                    if ($index == count($searchArray) - 1) {
                        $searchQuery .= " $key[0] LIKE :$key[0] ) ";
                    } else {
                        $searchQuery .= " $key[0] LIKE :$key[0] or ";
                    }
                    $temp = Security::encrypt($key[1]);
                    $searchParams[$key[0]] = "%$temp%";
                } else {
                    if ($index == count($searchArray) - 1) {
                        $searchQuery .= " $key[0] LIKE :$key[0] ) ";
                    } else {
                        $searchQuery .= " $key[0] LIKE :$key[0] or ";
                    }
                    $searchParams[$key[0]] = "%$key[1]%";
                }

            }
        }
//        print_r($searchArray);
    }


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

//        $nn = (empty($row['user_firstname'])) ? $lang['guest_user'] : Security::decrypt($row['user_firstname']) . " " . Security::decrypt($row['user_lastname']);

        $fn = (empty($row['user_firstname'])) ? '' : Security::decrypt($row['user_firstname']);
        $ln = (empty($row['user_lastname'])) ? '' : Security::decrypt($row['user_lastname']);
//        $name = '<div class="table-user text-start">
//                    <img src="' . Utils::fileExist($row['user_avatar'], USER_AVATAR) . '"
//                         alt="' . $fn . '"
//                         class="me-2 rounded-circle">
//                    <a href="/admin/users/info/' . $row['user_id'] . '"
//                       class="text-body fw-normal">' . $fn . ' </a>
//                </div>';


        if ($row['poster_status'] == "accepted") {
            $status = "<span class='badge badge-soft-success font-12'>" . $lang['a_user_inquiry_accepted'] . "</span>";
        } elseif ($row['poster_status'] == "pending") {
            $status = "<span class='badge badge-soft-warning font-12'>" . $lang['u_inquiry_air_pending'] . "</span>";
        } elseif ($row['poster_status'] == "rejected") {
            $status = "<span class='badge badge-soft-danger font-12'>" . $lang['reject'] . "</span>";
        } elseif ($row['poster_status'] == "deleted") {
            $status = "<span class='badge badge-soft-secondary font-12'>" . $lang['deleted'] . "</span>";
        } elseif ($row['poster_status'] == "expired") {
            $status = "<span class='badge badge-soft-secondary font-12'>" . $lang['expire'] . "</span>";
        } elseif ($row['poster_status'] == "needed") {
            $status = "<span class='badge badge-soft-info font-12'>" . $lang['needed'] . "</span>";
        } else {
            $status = "<span class='badge badge-soft-pink font-12'>" . $row['poster_status'] . "</span>";
        }


        $posterType = '';
        if ($row['poster_type'] == "truck") {
            $posterType = "<span class='badge badge-outline-info font-12'>" . $lang['a_truck'] . "</span>";
        } elseif ($row['poster_type'] == "trailer") {
            $posterType = "<span class='badge badge-outline-primary font-12'>" . $lang['a_trailer'] . "</span>";
        } else {
            $posterType = "<span class='badge badge-outline-pink font-12'>" . $row['poster_type'] . "</span>";
        }

        $tagA = '<a href="/admin/poster/info/' . $row['poster_id'] . '"
               target="_self"
               data-bs-toggle="tooltip"
               data-bs-placement="top"
               title="' . $lang['a_poster_info'] . '"
               class="action-icon">
                <i class="mdi mdi-eye"></i></a>';


        $parent = '';
        if (is_null($row['poster_parent_id'])) {
            $parent = $lang['a_new_2'];
        } else {
            $parent = $lang['a_update'];
        }


        $data[] = array(
            "poster_id" => $row['poster_id'],
            "user_firstname" => $fn,
            "user_lastname" => $ln,
            "poster_type" => $posterType,
            "poster_submit_date" => $parent,
            "poster_status" => $status,
            "poster_expire" => $tagA,
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

if (isset($_SESSION['dt-posters']) && $_SESSION['dt-posters'] == "dt-posters-44") {
//print_r($_POST);
    $query1 = "SELECT COUNT(*) AS allcount FROM `tbl_poster` INNER JOIN `tbl_users` ON tbl_poster.user_id=tbl_users.user_id where 1 ";
    $query2 = "SELECT COUNT(*) AS allcount FROM `tbl_poster` INNER JOIN `tbl_users` ON tbl_poster.user_id=tbl_users.user_id where 1 ";
    $query3 = "SELECT * FROM `tbl_poster` INNER JOIN `tbl_users` ON tbl_poster.user_id=tbl_users.user_id where 1 ";
    getData123($query1, $query2, $query3, ["poster_id", "user_firstname", "user_lastname", "poster_type", "poster_submit_date", "poster_status", "poster_expire"], ["rai"]);

}