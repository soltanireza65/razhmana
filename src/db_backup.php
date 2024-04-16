<?php
// Database configuration
$host = 'localhost';
$username = 'ntirapp_mjavan';
$password = 'Tj31013570';
$database = 'ntirapp_ntirapp';

// Telegram bot token and chat ID
$telegramBotToken = '5486090300:AAHI1QDLu8oimU7_I-rj6WdpHbRCy4rHrho';
$chatId = '335128627';


//$backupPath = 'C:/Users/Lifetech/Downloads/tbl_personls_card.sql';

$backupPath = '/home/ntirapp/public_html/db/';

// Create backup filename
$backupFilename = $database . '_' . date('Ymd_His') . '.sql';

// Build the mysqldump command
$command = "mysqldump -h{$host} -u{$username} -p{$password} {$database} > {$backupPath}{$backupFilename}";

// Execute the command
exec($command, $output, $returnValue);

if ($returnValue === 0) {
    echo "Database backup completed successfully.";



// Backup file path
    $backupFilePath = $backupPath . $backupFilename;

// Telegram API endpoint


    sendDocument($chatId, $backupPath, $backupFilename);

} else {
    echo "Database backup failed.";
}
 sendDocument($chatId, $backupPath, $backupFilename);
function sendDocument($chatId, $backupPath, $backupFilename)
{


    global $telegramBotToken;
    $ch = curl_init();

    $postFields = array(
        'document' => new CURLFile($backupPath.$backupFilename)
    );
    $proxy = "148.251.162.83:80";
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$telegramBotToken/sendDocument?chat_id={$chatId}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_PROXY, $proxy);
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    }

    curl_close($ch);

    echo $response;



}

?>
