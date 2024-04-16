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

        $status = '';
        if ($row['transaction_status'] == "completed") {
            $status = "<span class='badge badge-soft-success font-12'>" . $lang['completed'] . "</span>";
        } elseif ($row['transaction_status'] == "pending") {
            $status = "<span class='badge badge-soft-warning font-12'>" . $lang['pending'] . "</span>";
        } elseif ($row['transaction_status'] == "rejected") {
            $status = "<span class='badge badge-soft-danger font-12'>" . $lang['rejected'] . "</span>";
        } elseif ($row['transaction_status'] == "expired") {
            $status = "<span class='badge badge-outline-secondary font-12'>" . $lang['expired'] . "</span>";
        } elseif ($row['transaction_status'] == "paid") {
            $status = "<span class='badge badge-outline-info font-12'>" . $lang['paid'] . "</span>";
        } elseif ($row['transaction_status'] == "unpaid") {
            $status = "<span class='badge badge-outline-dark font-12'>" . $lang['unpaid'] . "</span>";
        } elseif ($row['transaction_status'] == "pending_deposit") {
            $status = "<span class='badge badge-outline-warning font-12'>" . $lang['pending_deposit'] . "</span>";
        } elseif ($row['transaction_status'] == "rejected_deposit") {
            $status = "<span class='badge badge-outline-danger font-12'>" . $lang['rejected_deposit'] . "</span>";
        } else {
            $status = "<span class='badge badge-soft-pink font-12'>" . $row['transaction_status'] . "</span>";
        }


        $tagA = '<a target="_self"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="' . $lang['info_transaction'] . '"
                     href="/admin/transaction/info/' . $row['transaction_id'] . '"
                     class="action-icon">
                      <i class="mdi mdi-square-edit-outline"></i>
                  </a>
                  <a data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="' . $lang['user_info'] . '"
                     href="/admin/users/info/' . $row['user_id'] . '"
                     target="_self"
                     class="action-icon">
                      <i class="mdi mdi-account-tie"></i>
                  </a>';

        $type = '';
        if ($row['transaction_type'] == "deposit") {
            $type = "<span class='badge badge-soft-primary font-13'>" . $lang['deposit'] . "</span>";
        } elseif ($row['transaction_type'] == "withdraw") {
            $type = "<span class='badge badge-soft-success font-13'>" . $lang['withdraw'] . "</span>";
        } else {
            $type = "<span class='badge badge-soft-pink font-13'>" . $row['transaction_type'] . "</span>";
        }

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        $currencyName = (!empty(array_column(json_decode($row['currency_name'], true), 'value', 'slug')[$language])) ?
            array_column(json_decode($row['currency_name'], true), 'value', 'slug')[$language] : "";


        $data[] = array(
            "transaction_id" => $row['transaction_id'],
            "transaction_authority" => $row['transaction_authority'],
            "transaction_tracking_code" => $row['transaction_tracking_code'],
            "transaction_amount" => number_format($row['transaction_amount']),
            "currency_name" => $currencyName,
            "transaction_status" => $status,
            "transaction_date" => Utils::getTimeCountry($Settings["date_format"], $row["transaction_date"]),
            "transaction_type" => $type,

            "card_id" => $tagA,
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

if (isset($_SESSION['dt-transactions-user']) && $_SESSION['dt-transactions-user'] == "dt-transactions-user-44") {

    $id = 0;
    if (isset($_REQUEST['id'])) {
        $id = (int)$_REQUEST['id'];
    }
    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_transactions INNER JOIN tbl_currency ON tbl_transactions.currency_id=tbl_currency.currency_id WHERE user_id={$id} ";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_transactions INNER JOIN tbl_currency ON tbl_transactions.currency_id=tbl_currency.currency_id WHERE user_id={$id} ";
    $query3 = "SELECT * FROM tbl_transactions INNER JOIN tbl_currency ON tbl_transactions.currency_id=tbl_currency.currency_id WHERE user_id={$id} ";
    getData123($query1, $query2, $query3, ["transaction_id", "transaction_authority", "transaction_tracking_code", "transaction_amount", "currency_name", "transaction_status", "transaction_date", "transaction_type", "card_id"], ["log_date", "log_detail", "log_os", "log_browser", "log_browser_version"]);

}