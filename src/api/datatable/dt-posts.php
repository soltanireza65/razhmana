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

        $port_id = '<a target="_self"
                     href="/admin/post/edit/' . $row['post_id'] . '"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="' . $lang['edit_2'] . '"
                     class="action-icon">
                      <i class="mdi mdi-square-edit-outline"></i>
                  </a>
                  <a href="/admin/post/delete/' . $row['post_id'] . '"
                     target="_self"
                     data-bs-toggle="tooltip"
                     data-bs-placement="top"
                     title="' . $lang['delete'] . '"
                     class="action-icon">
                      <i class="mdi mdi-delete"></i>
                  </a>';

        $status = '';
        if ($row['post_status'] == "published") {
            $status = "<span class='badge badge-soft-success font-12'>" . $lang['published'] . "</span>";
        } elseif ($row['post_status'] == "draft") {
            $status = "<span class='badge badge-soft-warning font-12'>" . $lang['draft'] . "</span>";
        } else {
            $status = "<span class='badge badge-soft-danger font-12'>" . $row['post_status'] . "</span>";
        }

        $langua = "";
        if ($row['post_language'] == "fa_IR") {
            $langua = $lang['fa_IR'];
        } elseif ($row['post_language'] == "en_US") {
            $langua = $lang['en_US'];
        } elseif ($row['post_language'] == "tr_Tr") {
            $langua = $lang['tr_Tr'];
        } elseif ($row['post_language'] == "ru_RU") {
            $langua = $lang['ru_RU'];
        } else {
            $langua = $row['post_language'];
        }

        $data[] = array(
            "post_id" => $row['post_id'],
            "post_title" => mb_strimwidth($row['post_title'], 0, 50, "..."),
            "category_name" => $row['category_name'],
            "post_language" => $langua,
            "post_status" => $status,
            "post_submit_time" => $port_id,
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

if (isset($_SESSION['dt-posts']) && $_SESSION['dt-posts'] == "dt-posts-44") {

    $query1 = "SELECT COUNT(*) AS allcount FROM tbl_posts INNER JOIN tbl_post_categories ON tbl_posts.category_id = tbl_post_categories.category_id  ";
    $query2 = "SELECT COUNT(*) AS allcount FROM tbl_posts INNER JOIN tbl_post_categories ON tbl_posts.category_id = tbl_post_categories.category_id WHERE 1  ";
    $query3 = "SELECT * FROM tbl_posts INNER JOIN tbl_post_categories ON tbl_posts.category_id = tbl_post_categories.category_id WHERE 1";
    getData123($query1, $query2, $query3, ["post_id", "post_title", "category_name", "post_language", "post_status", "post_submit_time"], ["port_name", "city_name", "port_priority", "port_id"]);

}