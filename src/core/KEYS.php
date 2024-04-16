<?php

namespace MJ\Keys;

use MJ\Config\Config;
use MJ\Database\DB;
use MJ\Utils\Utils;
use stdClass;

require_once 'Utils.php';
require_once 'Security.php';

date_default_timezone_set('Asia/Tehran');
define('URL_PROTOCOL', ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? 'https://' : 'http://');
define('SITE_HOST', (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : '');
define('SITE_ROOT', getcwd());
define('SITE_URL', URL_PROTOCOL . SITE_HOST);
define('ADMIN_COOKIE_TIMEOUT', 864000); // 10 day
define('USER_COOKIE_TIMEOUT', strtotime('+1 year')); // 1 Year
define('STABLE_COOKIE_TIMEOUT', 31536000); // 1 Year
define('ADMIN_BLOCK_TIME_LOGIN', 600); // 10 min
define('ADMIN_BLOCK_COUNT_LOGIN', 6); // 6
define('AUTHMODALTIMEOUT', strtotime('+3 hour')); // 6
define('ADMIN_HEADER_LOCATION', '/admin/login');
define('ADMIN_ADDRESS', '/uploads/admins');
define('SITE_MEDIAS', '/uploads/medias');
define('PAGE_404', '/404');

define('BLOG_ADDRESS', '/uploads/blogs');
define('ACADEMY_ADDRESS', '/uploads/academy');
define('ACADEMY_CATEGORY_ADDRESS', '/uploads/academy-category');
define('TICKET_ADDRESS', '/uploads/tickets');
define('SITE_ADDRESS', '/uploads/site');
define('SOCIAL_ADDRESS', '/uploads/social');
define('CARGO_ADDRESS', '/uploads/cargo');
define('CAR_ADDRESS', '/uploads/car');
define('CARS_ADDRESS', '/uploads/cars');
define('CARGOES_IMAGES_ADDRESS', '/uploads/cargoes');
define('DRIVER_DOCS_ADDRESS', '/uploads/driver/documents');
define('PERSONEL_DOCS_ADDRESS', '/uploads/personel');
define('CARGOES_IN_IMAGES_ADDRESS', '/uploads/cargoes-in');
define('BUSINESSMAN_ADDRESS', '/uploads/users/businessman');
define('DRIVER_ADDRESS', '/uploads/users/driver');
define('PAYMENT_ADDRESS', '/uploads/payment');
define('TASK_ADDRESS', '/uploads/tasks');
define('BRANDS_ADDRESS', '/uploads/brands');
define('GEARBOX_ADDRESS', '/uploads/gearboxs');
define('FUEL_ADDRESS', '/uploads/fuels');
define('PROPERTY_ADDRESS', '/uploads/property');
define('AUTH_ADDRESS', '/uploads/users/auth');
define('DEPOSIT_ADDRESS', '/uploads/deposit');
define('POSTER_ADDRESS', '/uploads/poster');
define('CARGO_READY_TO_LOAD', 259200);

define('BOX_EMPTY', '/dist/images/null.svg');
define('USER_AVATAR', '/dist/images/user.svg');
define('CAR_IMAGE', '/dist/images/car.svg');
define('POSTER_DEFAULT', '/uploads/site/poster-default.svg');
define('POSTER_WEBP_DEFAULT', '/uploads/site/poster-default.webp');
define('Flag_IMAGE', '/uploads/flags');


/**
 * Status, Message, Response, Errors
 * @param mixed ...$opts
 * @return stdClass
 * @author Tjavan
 * @version 1.0.0
 */
function sendResponse(...$opts)
{
    $response = new stdClass();
    $response->status = $opts[0];
    $response->message = $opts[1];
    if (isset($opts[2])) {
        $response->response = $opts[2];
    }
    if (isset($opts[3])) {
        $response->errors = $opts[3];
    }

    return $response;
}

class KEYS
{
    public static $themes = [
        'dark',
        'light'
    ];


    public static $sidebar = [
        'default',
        'condensed'
    ];

    public static $host = '';
    public static $dbName = '';
    public static $dbUserName = '';
    public static $dbPassword = '';

    public static $encryptionKey = 'NTIRAPP_19_TJAVAN';


    public static function init()
    {
        self::$host = Config::$host;
        self::$dbName = Config::$dbName;
        self::$dbUserName = Config::$dbUserName;
        self::$dbPassword = Config::$dbPassword;

        self::loadFromDB();
    }


    private static function loadFromDB()
    {
//        $sql = "select * from tbl_settings;";
//        $params = [];
//        $result = DB::rawQuery($sql, $params);
//        if ($result->status == 200) {
//            foreach ($result->response as $item) {
//                if ($item->setting_name == 'sms') {
//                    self::$ghasedakAPI = $item->setting_value;
//                }
//
//                if ($item->setting_name == 'sms_ghasedak') {
//                    self::$ghasedakAPI = json_decode($item->setting_value)->api;
//                    self::$ghasedakLineNumber = json_decode($item->setting_value)->phone;
//                }
//
//                if ($item->setting_name == 'sms_parsgreen') {
//                    self::$parsgreenAPI = json_decode($item->setting_value)->api;
//                    self::$parsgreenLineNumber = json_decode($item->setting_value)->phone;
//                }
//
//            }
//        }
    }
}

KEYS::init();