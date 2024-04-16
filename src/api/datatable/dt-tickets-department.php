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


        $tagA = '<a href="/admin/ticket/open/' . $row['ticket_id'] . '"
                 target="_self"
                 data-bs-toggle="tooltip"
                 data-bs-placement="top"
                 title="' . $lang['show_detail'] . '"
                 class="action-icon">
                  <i class="mdi mdi-eye"></i></a>';


        $status = '';
        if ($row['ticket_status'] == "open") {
            $status = "<span class='badge badge-soft-danger font-12'>" . $lang['ticket_open'] . "</span>";
        } elseif ($row['ticket_status'] == "close") {
            $status = "<span class='badge badge-soft-success font-12'>" . $lang['ticket_close'] . "</span>";
        } else {
            $status = "<span class='badge badge-soft-warning font-12'>" . $$row['ticket_status'] . "</span>";
        }


        $submit_date = '<bdi>' . Utils::getTimeCountry($Settings["date_format"], $row["ticket_submit_date"]) . '</bdi>';

        $data[] = array(
            "ticket_id" => $row['ticket_id'],
            "user_firstname" => $fn,
            "user_lastname" => $ln,
            "ticket_title" => mb_strimwidth($row['ticket_title'], 0, 20, '...'),
            "ticket_submit_date" => $submit_date,
            "ticket_status" => $status,
            "user_id" => $tagA,
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

if (isset($_SESSION['dt-tickets-department']) && $_SESSION['dt-tickets-department'] == "dt-tickets-department-44") {

    $id = 0;
    if (isset($_REQUEST['id'])) {
        $id = (int)$_REQUEST['id'];
    }
    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_tickets INNER JOIN tbl_departments ON tbl_departments.department_id=tbl_tickets.department_id INNER JOIN tbl_users ON tbl_tickets.user_id=tbl_users.user_id WHERE tbl_tickets.department_id={$id}";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_tickets INNER JOIN tbl_departments ON tbl_departments.department_id=tbl_tickets.department_id INNER JOIN tbl_users ON tbl_tickets.user_id=tbl_users.user_id WHERE tbl_tickets.department_id={$id}  ";
    $query3 = "SELECT * FROM tbl_tickets INNER JOIN tbl_departments ON tbl_departments.department_id=tbl_tickets.department_id INNER JOIN tbl_users ON tbl_tickets.user_id=tbl_users.user_id WHERE tbl_tickets.department_id={$id}";
    getData123($query1, $query2, $query3, ["ticket_id", "user_firstname", "user_lastname", "ticket_title", "ticket_submit_date", "ticket_status", "user_id"], []);

}