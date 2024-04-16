<?php
$_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__);
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/Config.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/core/DB.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/core/KEYS.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/core/SMS.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/core/Security.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/core/Upload.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/core/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/core/Builder.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/classes/Cargo.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/core/Ghasedak.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/classes/User.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/classes/Notification.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/classes/Admin.php';
require_once $_SERVER['DOCUMENT_ROOT']. '/classes/Transactions.php';