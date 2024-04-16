<?php

require_once 'autoload.php';
$records = Cargo::getAllCargoForCronJob();
if ($records->status == 200) {
    $records = $records->response;
    foreach ($records as $index => $record) {
        if ((intval($record->cargo_start_date) + 259200 < time())) {
            $result = Cargo::updateCargoStatus($record->cargo_id);
            if ($result->status == 200) {
                Cargo::rejectedRequestsAfterCargoExpired($record->cargo_id);
                file_put_contents('public_html/db/log_update_expired_cargo_200.txt', $record->cargo_id .',' , FILE_APPEND);
            }else{
                file_put_contents('public_html/db/log_update_expired_cargo_error.txt', $record->cargo_id .',' , FILE_APPEND);
            }
        }
    }
}

