<?php
// Admin::addNewSlugForRole('tester','yes','yes','yes','yes',3);
// Admin::addAdminPermissionAfterSlug(105,2,'no','no','no','no');
// Admin::addAdminPermissionAfterSlug(105,4,'no','no','no','no');
// Admin::addAdminPermissionAfterSlug(105,5,'no','no','no','no');
// Admin::addAdminPermissionAfterSlug(105,6,'no','no','no','no');
// Admin::addAdminPermissionAfterSlug(105,7,'no','no','no','no');
// Admin::addAdminPermissionAfterSlug(105,8,'no','no','no','no');
// Admin::addAdminPermissionAfterSlug(105,9,'no','no','no','no');
// Admin::addAdminPermissionAfterSlug(105,10,'no','no','no','no');
// Admin::addAdminPermissionAfterSlug(105,14,'no','no','no','no');
// Admin::addAdminPermissionAfterSlug(105,15,'no','no','no','no');
// Admin::addAdminPermissionAfterSlug(105,16,'no','no','no','no');


require_once './vendor/autoload.php';


$bot_api_key = '6383174258:AAHJgo2S5jLSqbHBZip6xeRHClN0thasbYI';
 use SergiX44\Nutgram\Nutgram;

$bot = new Nutgram($bot_api_key);
file_put_contents('test.txt'  , 'run');

$bot->onCommand('start', function(Nutgram $bot) {
    $bot->sendMessage('Hello, welcome to our bot!');
});

$bot->onText('My name is {name}', function(Nutgram $bot, string $name) {
    $bot->sendMessage("Nice to meet you, $name!");
});

$bot->onCommand('help', function(Nutgram $bot) {
    $bot->sendMessage('You can interact with me by sending "My name is {your_name}".');
});

$bot->run();