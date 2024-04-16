<?php

// Retrieve the request parameters from DataTables
use MJ\Keys\KEYS;

$draw = $_POST['draw'];
$start = $_POST['start'];
$length = $_POST['length'];
$searchValue = $_POST['search']['value'];
$orderColumnIndex = $_POST['order'][0]['column'];
$orderDir = $_POST['order'][0]['dir'];

// Define the database connection details
global $lang;
$slug = 'title_' . $_COOKIE['language'];

$server = KEYS::$host;
$username = KEYS::$dbUserName;
$password = KEYS::$dbPassword;
$dbname = KEYS::$dbName;
// Connect to the database
$conn = new mysqli($server, $username, $password, $dbname);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$conn->set_charset("utf8");
// Construct the SQL query
$sql = "SELECT * FROM tbl_exchange_request   
inner join tbl_live_price_bonbast on tbl_exchange_request.price_id = id  
 ";
$columnMapping = [
    0 => 'request_id',
    1 => 'request_type',
    2 => 'title_fa_IR',
    3 => 'title_en_US',
    4 => 'title_tr_Tr',
    5 => 'title_ru_RU',
//    6 => 'request_side',
//    7 => 'request_status',
//    5 => 'action'
];

// Apply search filter
if (!empty($searchValue)) {
    $sql .= " WHERE ";
    $searchConditions = [];
    foreach ($columnMapping as $index => $columnName) {
        $searchConditions[] = "$columnName LIKE '%$searchValue%'";
    }
    $sql .= implode(" OR ", $searchConditions);
}

if (!empty($_POST['requestTypeFilter'])) {
    $requestTypeFilter = $_POST['requestTypeFilter'];

    $sql .= " and request_side LIKE '%$requestTypeFilter%'";
}

if (!empty($_POST['requestStatusFilter'])) {
    $requestStatusFilter = $_POST['requestStatusFilter'];

     $sql .= " and request_status LIKE '%$requestStatusFilter%'";
}

// Apply ordering
$orderColumn = [
    'request_id',
    'request_type',
    $slug,
    'request_side',
    'request_status',
//    'action'
][$orderColumnIndex];
$sql .= " ORDER BY $orderColumn $orderDir";


$sql .= " LIMIT $start, $length";
//echo $sql;

$result = $conn->query($sql);


$totalRecords = $conn->query("SELECT COUNT(*) AS total FROM tbl_exchange_request ")->fetch_assoc()['total'];


$filteredRecords = $totalRecords;

$data = [];
while ($row = $result->fetch_assoc()) {
    $status = '';
    if ($row['request_status'] == 'pending') {
        $status = '<span >'.$lang["pending"].' </span>';
    } elseif ($row['request_status'] == 'accepted'){
        $status = '<span >   '.$lang["accepted"].'</span>';
    }elseif ($row['request_status'] == 'rejected'){
        $status = '<span >   '.$lang["rejected"].'</span>';
    }

    $action = ' <a href="/admin/exchange/request/' . $row['request_id'] . '"  >
                                  '.$lang["display"].'
                               </a>';
//    $type = $lang['ir_order_exchange'];
//    if($row['request_type'] == 'ru') {
//        $type = $lang['ru_order_exchange'];
//    }elseif($row['request_type'] == 'du') {
//        $type = $lang['su_order_exchange'];
//    }elseif($row['request_type'] == 'tr') {
//        $type = $lang['tr_order_exchange'];
//    }

    $data[] = [
        'request_id' => $row['request_id'],
        'request_type' => $row['request_type']  ,
        'title' =>  $row[$slug],
        'request_side' =>  $lang[$row['request_side']],
        'request_status' => $status,
        'action' => $action,
    ];
//    'request_id', 'user_id', 'request_type', 'price_id','request_side
}

// Return the response as JSON
$response = [
    'draw' => intval($draw),
    'recordsTotal' => intval($totalRecords),
    'recordsFiltered' => intval($filteredRecords),
    'data' => $data
];

header('Content-Type: application/json');
echo json_encode($response);

// Close the database connection
$conn->close();
?>
