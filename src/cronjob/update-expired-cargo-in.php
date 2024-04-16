<?php
require_once 'autoload.php';
$records = Cargo::getAllCargoInForCronJob();
if ($records->status == 200) {
    $records = $records->response;
    foreach ($records as $index => $record) {
        if ((intval($record->cargo_start_date) + 259200 < time())) {
            $result = Cargo::updateCargoInStatus($record->cargo_id);
            if ($result->status == 200) {
                Cargo::rejectedRequestsAfterCargoInExpired($record->cargo_id);
                file_put_contents('public_html/db/log_update_expired_cargo_in_200.txt', $record->cargo_id .',' , FILE_APPEND);
            }else{
                file_put_contents('public_html/db/log_update_expired_cargo_in_error.txt', $record->cargo_id .',' , FILE_APPEND);
            }
        }
    }
}