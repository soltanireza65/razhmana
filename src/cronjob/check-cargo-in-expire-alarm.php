<?php
require_once 'autoload.php';

$records = Cargo::getAllCargoInForCronJob();
if ($records->status == 200) {
    $records = $records->response;
    foreach ($records as $index => $record) {
        if ((intval($record->cargo_start_date) < (time() + 43200 + 43200)) && (intval($record->cargo_start_date) > time())) {
            User::createWhatsAppMessage($record->user_id, 'expire_flag');
//            Notification::sendNotification($record->user_id, 'expire_title', "expire_sender", "expire_message");
            Notification::sendNotification(
                $record->user_id,
                'expire_title', 'system', 'expire_message',
                'https://ntirapp.com/businessman/cargo-detail/'.$record->cargo_id , 'unread' , true
            );
            file_put_contents('public_html/db/log_send_notification_successfully.txt', $record->cargo_id . ',', FILE_APPEND);
        }
    }
}
