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
                if (in_array($key[0], ['user_mobile', "user_lastname", "user_firstname"])) {
                    if ($index == count($searchArray) - 1) {
                        $searchQuery .= " $key[0] LIKE :$key[0] ) ";
                    } else {
                        $searchQuery .= " $key[0] LIKE :$key[0] or ";
                    }
                    $temp = Security::encrypt($key[1]);
                    $searchParams[$key[0]] = "%$temp%";
                } elseif (in_array($key[0], ["user_type"])) {

                    if ($index == count($searchArray) - 1) {
                        $searchQuery .= " $key[0] = :$key[0] ) ";
                    } else {
                        $searchQuery .= " $key[0] = :$key[0] or ";
                    }
                    $searchParams[$key[0]] = "$key[1]";

                } elseif (in_array($key[0], ["user_auth_status"])) {
//                    print_r($key[1]);
                    if ($key[1] == "all") {
                        if ($index == count($searchArray) - 1) {
                            $searchQuery .= " 1=1 ) ";
                        } else {
                            $searchQuery .= " 1=1 or ";
                        }
                    } elseif ($key[1] == 'null') {
                        if ($index == count($searchArray) - 1) {
                            $searchQuery .= " $key[0] is null ) ";
                        } else {
                            $searchQuery .= " $key[0] is null or ";
                        }

                    } else {
                        if ($index == count($searchArray) - 1) {
                            $searchQuery .= " $key[0] = :$key[0] ) ";
                        } else {
                            $searchQuery .= " $key[0] = :$key[0] or ";
                        }
                        $searchParams[$key[0]] = "$key[1]";


                    }
                } elseif (in_array($key[0], ["user_status"])) {

//                    print_r($key[1]);
                    if ($key[1] == "all") {
                        if ($index == count($searchArray) - 1) {
                            $searchQuery .= " 1=1 ) ";
                        } else {
                            $searchQuery .= " 1=1 or ";
                        }
                    }  else {
                        if ($index == count($searchArray) - 1) {
                            $searchQuery .= " $key[0] = :$key[0] ) ";
                        } else {
                            $searchQuery .= " $key[0] = :$key[0] or ";
                        }
                        $searchParams[$key[0]] = "$key[1]";


                    }
                }   else {
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
//print_r($searchQuery);
//print_r($searchParams);

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
        $name = '<div class="table-user text-start">
                    <img src="' . Utils::fileExist($row['user_avatar'], USER_AVATAR) . '"
                         alt="' . $fn . '"
                         class="me-2 rounded-circle">
                    <a href="/admin/users/info/' . $row['user_id'] . '"
                       class="text-body fw-normal">' . $fn . ' </a>
                </div>';

        $statusUser = '';
        if ($row['user_status'] == "active") {
            $statusUser = "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
        } elseif ($row['user_status'] == "guest") {
            $statusUser = "<span class='badge badge-soft-warning font-12'>" . $lang['guest'] . "</span>";
        } elseif ($row['user_status'] == "inactive") {
            $statusUser = "<span class='badge badge-soft-secondary font-12'>" . $lang['inactive'] . "</span>";
        } elseif ($row['user_status'] == "suspend") {
            $statusUser = "<span class='badge badge-soft-danger font-12'>" . $lang['suspend'] . "</span>";
        } else {
            $statusUser = "<span class='badge badge-soft-danger font-12'>" . $row['user_status'] . "</span>";
        }

        $statusAuth = '';
        if (is_null($row['user_auth_status'])) {
            $statusAuth = '<span class="badge badge-outline-info font-12 ms-2">' . $lang['a_authorization_no'] . '</span>';
        } elseif ($row['user_auth_status'] == "pending") {
            $statusAuth = '<span class="badge badge-outline-warning font-12 ms-2">' . $lang['a_authorization_pending'] . '</span>';
        } elseif ($row['user_auth_status'] == "accepted") {
            $statusAuth = '<span class="badge badge-outline-success font-12 ms-2">' . $lang['a_authorization_accepted'] . '</span>';
        } elseif ($row['user_auth_status'] == "rejected") {
            $statusAuth = '<span class="badge badge-outline-danger font-12 ms-2">' . $lang['a_authorization_rejected'] . '</span>';
        } else {
            $statusAuth = '<span class="badge badge-outline-secondary font-12 ms-2">' . $row['user_auth_status'] . '</span>';
        }


        $statusInquiry = '';
        if (is_null($row['user_inquiry_status'])) {
            $statusInquiry = '<span class="badge badge-outline-info font-12 ms-2">' . $lang['a_user_inquiry_no'] . '</span>';
        } elseif ($row['user_inquiry_status'] == "true") {
            $statusInquiry = '<span class="badge badge-outline-success font-12 ms-2">' . $lang['a_user_inquiry_accepted'] . '</span>';
        } elseif ($row['user_inquiry_status'] == "false") {
            $statusInquiry = '<span class="badge badge-outline-danger font-12 ms-2">' . $lang['a_user_inquiry_rejected'] . '</span>';
        } else {
            $statusInquiry = '<span class="badge badge-outline-secondary font-12 ms-2">' . $row['user_inquiry_status'] . '</span>';
        }


        $userNational = '';
        if (is_null($row['user_type_card'])) {
            $userNational = $lang['a_unknown'];
        } elseif ($row['user_type_card'] == "id-card") {
            $userNational = $lang['a_iran'];
        } elseif ($row['user_type_card'] == "passport") {
            $userNational = $lang['a_foreign'];
        } else {
            $userNational = $row['user_type_card'];
        }

        $own = '';
        if ($row['user_class'] == "own") {
            $own = '<i class="mdi mdi-star font-20 text-warning"></i>';
        }

        $tagA = '<a href="/admin/users/info/' . $row['user_id'] . '"
               target="_self"
               data-bs-toggle="tooltip"
               data-bs-placement="top"
               title="' . $lang['user_info'] . '"
               class="action-icon mj-action-btn">
                <div class="fa-eye"></div></a>' ;

        $userType = '';
        if ($row['user_type'] == 'driver') {
            $userType = $lang['driver'];
        } elseif ($row['user_type'] == 'guest') {
            $userType = $lang['guest_user'];
        } elseif ($row['user_type'] == 'businessman') {
            $userType = $lang['businessman'];
        } else {
            $userType = $row['user_type'];
        }


        $data[] = array(
            "user_id" => $row['user_id']. $own,
            "user_firstname" => $name,
            "user_lastname" => $ln,
            "user_mobile" => '<bdi>' . Security::decrypt($row['user_mobile']) . '</bdi>',
            "user_type" => $userType,
            "user_auth_status" => $statusAuth,
            "user_type_card" => $userNational,
            "user_inquiry_status" => $statusInquiry,
            "user_status" => $statusUser,
            "user_register_date" => $tagA,
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


if (isset($_SESSION['dt-users-list']) && $_SESSION['dt-users-list'] == "dt-users-list-44") {
//print_r($_POST);
    $query1 = "SELECT COUNT(*) AS allcount FROM `tbl_users` where 1 ";
    $query2 = "SELECT COUNT(*) AS allcount FROM `tbl_users` where 1 ";
    $query3 = "SELECT * FROM tbl_users  where 1 ";
    getData123($query1, $query2, $query3, ["user_id", "user_firstname", "user_lastname", "user_mobile", "user_type", "user_auth_status", "user_type_card", "user_inquiry_status", "user_status", "user_register_date"], ["railroad_name", "city_name", "railroad_priority", "railroad_id"]);

}