<?php


namespace MJ\Utils;


use DateTime;
use DOMDocument;
use Exception;
use MJ\Security\Security;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;
use mysqli;
use stdClass;
use Vtiful\Kernel\Format;
use ZipArchive;
use function MJ\Keys\sendResponse;

require_once $_SERVER['DOCUMENT_ROOT'] . '/core/KEYS.php';


class Utils
{
    /**
     * @var
     */
    private static $userAgent;


    /**
     * @var array
     */
    private static $osPlatforms = [
        '/windows nt 11/i' => 'Windows 11',
        '/windows nt 10/i' => 'Windows 10',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000',
        '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    ];


    /**
     * @var array
     */
    private static $browsersList = [
        '/msie/i' => 'Internet Explorer',
        '/firefox/i' => 'Firefox',
        '/safari/i' => 'Safari',
        '/chrome/i' => 'Chrome',
        '/edge/i' => 'Edge',
        '/opera/i' => 'Opera',
        '/netscape/i' => 'Netscape',
        '/maxthon/i' => 'Maxthon',
        '/konqueror/i' => 'Konqueror',
        '/mobile/i' => 'Handheld Browser'
    ];


    /**
     * Configuration User Agent
     *
     * @author  Tjavan
     * @version 1.0.0
     */
    private static function configUserAgent()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            self::$userAgent = $_SERVER['HTTP_USER_AGENT'];
        }
    }


    /**
     * @return mixed|string
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function getClientOS()
    {
        self::configUserAgent();;
        $platformName = 'Unknown OS Platform';

        foreach (self::$osPlatforms as $osPlatform => $platform) {
            if (preg_match($osPlatform, self::$userAgent)) {
                $platformName = $platform;
            }
        }
        return $platformName;
    }


    /**
     * @return mixed|string
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function getClientBrowser()
    {
        self::configUserAgent();;
        $browserName = 'Unknown Browser';

        foreach (self::$browsersList as $browser => $name) {
            if (preg_match($browser, self::$userAgent)) {
                $browserName = $name;
            }
        }
        return $browserName;
    }


    /**
     * @return mixed
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function getClientBrowserVersion()
    {
        $browser = self::getClientBrowser();

        $known = ['Version', $browser, 'other'];
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        preg_match_all($pattern, self::$userAgent, $matches);

        $count = count($matches['browser']);
        if ($count != 1) {
            if (stripos(self::$userAgent, 'Version') < stripos(self::$userAgent, $browser)) {
                $version = $matches['version'][0];
            } else {
                $version = 0;
            }
        } else {
            $version = $matches['version'][0];
        }
        return $version;
    }


    /**
     * @return stdClass
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function getGeoLocation()
    {
//        $ipAddress = $_SERVER['REMOTE_ADDR'];

        try {
//            $json = json_decode(file_get_contents("http://ipinfo.io/{$ipAddress}/geo"), true);
        } catch (Exception $e) {
//            $json = null;
        }

        $country = isset($json['country']) ? $json['country'] : 'Unknown';
        $region = isset($json['region']) ? $json['region'] : 'Unknown';
        $city = isset($json['city']) ? $json['city'] : 'Unknown';

        return sendResponse(200, '', [
//            'ip' => $ipAddress,
//            'country' => $country,
//            'region' => $region,
//            'city' => $city,
//            'format' => "{$country} -- {$region} -- ${city}"
        'ip' => '$ipAddress',
            'country' => '$country',
            'region' => '$region',
            'city' => '$city',
            'format' => "{$country} -- {$region} -- ${city}"
        ]);
    }


    /**
     * @return string
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function getClientDevice()
    {
        self::configUserAgent();;

        if (preg_match("/(android|iphone|ipod|ipad|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", substr(self::$userAgent, 0, 4))) {
            $deviceName = 'Mobile';
        } else {
            $deviceName = 'PC';
        }
        return $deviceName;
    }


    /**
     * @param        $number
     * @param string $currency
     *
     * @return string
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function currencyFormat($number, $currency = '$')
    {
        if ($number >= 1000000000) {
            $fraction = 1000000000;
            $symbol = 'B';
        } elseif ($number >= 1000000) {
            $fraction = 1000000;
            $symbol = 'M';
        } elseif ($number >= 1000) {
            $fraction = 1000;
            $symbol = 'K';
        } else {
            $fraction = 1;
            $symbol = '';
        }
        $result = number_format($number / $fraction, 0, '.', ',');

        return "{$currency} {$result}{$symbol}";
    }


    /**
     * @param int $length
     *
     * @return string
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $generatedString = '';
        for ($i = 0; $i < $length; $i++) {
            $generatedString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $generatedString;
    }


    /**
     * @param int $length
     *
     * @return string
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function generateRandomNumber($length = 5)
    {
        $characters = '0123456789';
        $number = '';
        for ($i = 0; $i < $length; $i++) {
            $number .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $number;
    }


    /**
     * @return string
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function generateGUID()
    {
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }


    /**
     * @param $theme
     *
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function setTheme($theme)
    {
        $theme = ($theme == 'dark') ? 'dark' : 'light';
        setcookie('theme', $theme, time() + STABLE_COOKIE_TIMEOUT, '/');
    }


    /**
     * @param $theme
     *
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function setSideBar($sidebar)
    {
        $sidebar = ($sidebar == 'default') ? 'default' : 'condensed';
        setcookie('sidebar-admin', $sidebar, time() + STABLE_COOKIE_TIMEOUT, '/');
    }


    /**
     * @param      $dateTime
     * @param bool $full
     *
     * @return string|null
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function timeElapsedString($dateTime, $full = false)
    {
        $response = null;
        try {
            $now = new DateTime();
            $ago = new DateTime($dateTime);
            $diff = $now->diff($ago);

            $diff->w = floor($diff->d / 7);
            $diff->d -= $diff->w * 7;
            global $lang;
            $string = [
                'y' => $lang['year'],
                'm' => $lang['month'],
                'w' => $lang['week'],
                'd' => $lang['day'],
                'h' => $lang['hour'],
                'i' => $lang['minute'],
                's' => $lang['second'],
            ];

            foreach ($string as $key => &$value) {
                if ($diff->$key) {
                    $value = $diff->$key . ' ' . $value . ($diff->$key > 1 ? ' ' : ' ');
                } else {
                    unset($string[$key]);
                }
            }

            if (!$full) {
                $string = array_slice($string, 0, 1);
            }
            $response = $string ? implode(', ', $string) . $lang['before']   : $lang['a_few_seconds_before'];
        } catch (Exception $e) {

        }
        return $response;
    }


    public static function getCityLocationByName($cityName)
    {
        $url = "https://nominatim.openstreetmap.org/search.php?q=${cityName}&format=jsonv2";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'chorome');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        $output = [];
        $address = ["address" => json_decode($resp)[0]->display_name];
        $lati = ["lat" => json_decode($resp)[0]->lat];
        $lang = ["long" => json_decode($resp)[0]->lon];
        array_push($output, $address);
        array_push($output, $lati);
        array_push($output, $lang);
        return json_encode($output);
    }

    public static function getCityLocationByName1($cityName)
    {
        $url = "https://nominatim.openstreetmap.org/search.php?q=${cityName}&format=jsonv2";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_USERAGENT, 'chorome');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
//        $output =  [];
//        $address = ["address" => json_decode($resp)[0]->display_name];
//        $lati = ["lat" => json_decode($resp)[0]->lat];
//        $lang = ["long" => json_decode($resp)[0]->lon];
        $output = [
            "address" => json_decode($resp)[0]->display_name,
            "lat" => json_decode($resp)[0]->lat,
            "long" => json_decode($resp)[0]->lon
        ];
        return $output;
    }


    /**
     * @param        $format
     * @param string $timestamp
     * @param null $timezone
     *
     * @return string
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function jDate($format, $timestamp = 'now', $timezone = null)
    {
        return Jalalian::fromDateTime($timestamp, $timezone)->format($format);
    }


    /**
     * @param $jy
     * @param $jm
     * @param $jd
     *
     * @return array
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function jalaliToGregorian($jy, $jm, $jd)
    {
        return CalendarUtils::toGregorian($jy, $jm, $jd);
    }


    /**
     * @param $gy
     * @param $gm
     * @param $gd
     *
     * @return array
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function gregorianToJalali($gy, $gm, $gd)
    {
        return CalendarUtils::toJalali($gy, $gm, $gd);
    }


    /**
     * EXPORT DATABASE
     *
     * @param      $host
     * @param      $user
     * @param      $pass
     * @param      $name
     * @param bool $tables
     * @param bool $backup_name
     *
     * @return  .sql
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function exportDatabase($host, $user, $pass, $name, $tables = false, $backup_name = false)
    {
        set_time_limit(3000);
        $mysqli = new mysqli($host, $user, $pass, $name);
        $mysqli->select_db($name);
        $mysqli->query("SET NAMES 'utf8'");
        $queryTables = $mysqli->query('SHOW TABLES');
        while ($row = $queryTables->fetch_row()) {
            $target_tables[] = $row[0];
        }
        if ($tables !== false) {
            $target_tables = array_intersect($target_tables, $tables);
        }
        $content = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\r\nSET time_zone = \"+00:00\";\r\n\r\n\r\n/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\r\n/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\r\n/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\r\n/*!40101 SET NAMES utf8 */;\r\n--\r\n-- Database: `" . $name . "`\r\n--\r\n\r\n\r\n";
        foreach ($target_tables as $table) {
            if (empty($table)) {
                continue;
            }
            $result = $mysqli->query('SELECT * FROM `' . $table . '`');
            $fields_amount = $result->field_count;
            $rows_num = $mysqli->affected_rows;
            $res = $mysqli->query('SHOW CREATE TABLE ' . $table);
            $TableMLine = $res->fetch_row();
            $content .= "\n\n" . $TableMLine[1] . ";\n\n";
            $TableMLine[1] = str_ireplace('CREATE TABLE `', 'CREATE TABLE IF NOT EXISTS `', $TableMLine[1]);
            for ($i = 0, $st_counter = 0; $i < $fields_amount; $i++, $st_counter = 0) {
                while ($row = $result->fetch_row()) { //when started (and every after 100 command cycle):
                    if ($st_counter % 100 == 0 || $st_counter == 0) {
                        $content .= "\nINSERT INTO " . $table . " VALUES";
                    }
                    $content .= "\n(";
                    for ($j = 0; $j < $fields_amount; $j++) {
                        $row[$j] = str_replace("\n", "\\n", addslashes($row[$j]));
                        if (isset($row[$j])) {
                            $content .= '"' . $row[$j] . '"';
                        } else {
                            $content .= '""';
                        }
                        if ($j < ($fields_amount - 1)) {
                            $content .= ',';
                        }
                    }
                    $content .= ")";
                    //every after 100 command cycle [or at last line] ....p.s. but should be inserted 1 cycle eariler
                    if ((($st_counter + 1) % 100 == 0 && $st_counter != 0) || $st_counter + 1 == $rows_num) {
                        $content .= ";";
                    } else {
                        $content .= ",";
                    }
                    $st_counter = $st_counter + 1;
                }
            }
            $content .= "\n\n\n";
        }
        $content .= "\r\n\r\n/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\r\n/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\r\n/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;";
        $backup_name = $backup_name ? $backup_name : $name . '___(' . date('H-i-s') . '_' . date('d-m-Y') . ').sql';
        ob_get_clean();
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header('Content-Length: ' . (function_exists('mb_strlen') ? mb_strlen($content, '8bit') : strlen($content)));
        header("Content-disposition: attachment; filename=\"" . $backup_name . "\"");
        echo $content;
        exit;
    }


    public static function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }


    /**
     * @param $value
     *
     * @return string
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function convertNumbersToLatin($value)
    {
        return CalendarUtils::convertNumbers($value, true);
    }


    /**
     * @param      $date
     * @param null $time
     *
     * @return false|int
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function jalaliToTimestamp($date, $time = null)
    {
        $date = str_contains($date, '/') ? explode('/', $date) : explode('-', $date);
        $date = self::jalaliToGregorian($date[0], $date[1], $date[2]);
        return strtotime("{$date[0]}-{$date[1]}-{$date[2]} {$time}");
    }


    /**
     * @param $file
     * @param $replace
     *
     * @return mixed
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function fileExist($fileMain, $replace)
    {
        $firstCharacter = substr($fileMain, 0, 1);

        $file = $fileMain;
        if ($firstCharacter != "/") {
            $file = "/" . $fileMain;
        }

        if (!empty($file) && is_file(SITE_ROOT . $file) && file_exists(SITE_ROOT . $file)) {
            return SITE_URL . $file;
        } else {
            return SITE_URL . $replace;
        }
    }


    /**
     * SET OR UPDATE JSON FILE
     *
     * @param $file
     * @param $name
     * @param $value
     *
     * @return int
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function setFileText($file, $name, $value, $encrypt = true)
    {

        $file = SITE_ROOT . "/db/" . $file;

        if (!file_exists($file)) {
            touch($file);
        }

        if ($encrypt) {
            $temp = json_decode(Security::decrypt(file_get_contents($file)), true);
        } else {
            $temp = json_decode(file_get_contents($file), true);
        }
        $flag = false;

        foreach ($temp as $index => $loop) {
            if ($index == $name) {
                $temp[$index] = $value;
                $flag = true;
            }

        }
        if ($flag == false) {
            $temp[$name] = $value;
        }
        if ($encrypt) {
            $result = file_put_contents($file, Security::encrypt(json_encode($temp)));
        } else {
            $result = file_put_contents($file, json_encode($temp));
        }
        if ($result == 0 || $result == false) {
            return 0;
        } else {
            return 200;
        }
    }

    /**
     * SET OR UPDATE JSON FILE
     *
     * @param $file
     * @param $name
     * @param $value
     *
     * @return int
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function setFileTextjSONArray($file, $value)
    {
        $file = SITE_ROOT . "/db/" . $file;
        $temp = [];
        $temp = json_decode(file_get_contents($file), true);

        array_push($temp, $value);


        $result = file_put_contents($file, json_encode($temp));

        if ($result == 0 || $result == false) {
            return 0;
        } else {
            return 200;
        }
    }


    public static function getFileValue($file0, $name = "", $encrypt = true)
    {

        $file = SITE_ROOT . "/db/" . $file0;
        
        if (empty($file0) || !is_file($file) || !file_exists($file)) {
            return "";
        }

        if (empty($name)) {
            if ($encrypt) {
                return Security::decrypt(file_get_contents($file));
            } else {
                return file_get_contents($file);
            }
        } else {
            if ($encrypt) {
                $temp = json_decode(Security::decrypt(file_get_contents($file)), true);
            } else {
                $temp = json_decode(file_get_contents($file), true);
            }
            foreach ($temp as $index => $loop) {
                if ($index == $name) {
                    return $loop;
                }
            }
        }
        return "";
    }


    public static function backupFiles($name, $dir, $dirSave)
    {
        $zip = new ZipArchive;
        $name = time() . "--" . $name;

        $srcDir = SITE_ROOT . "/" . $dir;
        $files = scandir($srcDir);

        $arr = ['.', "..", "..."];
        $format = ['zip'];

        if ($zip->open(SITE_ROOT . '/' . $dirSave . $name . '.zip', ZipArchive::CREATE) === TRUE) {

            foreach ($files as $file) {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                if (!in_array($file, $arr) && !in_array($extension, $format)) {
                    $zip->addFile($srcDir . $file, $file);
                }
            }

            $zip->close();
            return json_encode(['status' => 200, 'response' => SITE_URL . '/' . $dirSave . $name . '.zip']);
        } else {
            return -1;
        }
    }


    /**
     * SEND CURL REQUEST
     *
     * @return object
     */
    public static function sendRequest($network, $action, $params = null)
    {
        $headers = [
            'Content-Type: application/json',
            'mj-api-token: HEADER_TOKEN_MJ',

        ];

//        $curl = curl_init(SITE_URL . "/bank/{$network}/{$action}");
        $curl = curl_init("http://localhost:3000/bank/{$network}/{$action}");
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        if (!empty($params)) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));
        }
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }


    public static function getThemeDirection()
    {
        $rtlArray = [
            'fa_IR'
        ];
        if (in_array($_COOKIE['language'], $rtlArray)) {
            return 'rtl';
        } else {
            return "ltr";
        }
    }


    /**
     * Get Star By Rate
     *
     * @param $num
     *
     * @return string
     */
    public static function getStarsByRate($num)
    {
        $num = round($num * 2) / 2;

        $temp = '';
        for ($n = 0; $n < 5; $n++) {
            $temp .= '<i class="fa  fa-star';
            if ($num == $n + .5) {
                $temp .= '-half-alt text-warning icon-rotates';
            } elseif ($num < $n + .5) {
                $temp .= '';
            } else {
                $temp .= ' text-warning';
            };
            $temp .= '"></i>';
        }
        return $temp;
    }


    public static function getTimeCountry($format, $time)
    {

        $arrayExist = ['ir', 'en'];

        if (isset($_COOKIE['time']) && in_array($_COOKIE['time'], $arrayExist)) {
            $country = $_COOKIE['time'];
        } else {
            $country = 'ir';
        }


        if ($country == 'ir') {
            return self::jDate($format, $time);
        } else {
            date_default_timezone_set('UTC');
            return date($format, $time);
        }
    }

    public static function getTimeByLang($time)
    {
        if ($_COOKIE['language'] == 'fa_IR') {
            $time = self::jDate('Y/m/d', $time);
        } else {
            $time = date('Y-m-d', $time);
        }
        return $time;
    }
    public static function getTimeByLangWithHour($time)
    {
        if ($_COOKIE['language'] == 'fa_IR') {
            $time = self::jDate('Y/m/d-H:i:s', $time);
        } else {
            $time = date('Y-m-d-H:i:s', $time);
        }
        return $time;
    }
    public static function getHourMiniteSecondByLang($time , $format = 'H:i:s')
    {
        if ($_COOKIE['language'] == 'fa_IR') {
            $time = self::jDate($format, $time);
        } else {
            $time = date($format, $time);
        }
        return $time;
    }
    public static function getBankIranInfo($number){

        $MyTemp = new stdClass();
        switch ($number){
            case '603799' :
                $MyTemp->name = 'بانک ملی ایران';
                $MyTemp->icon = '/uploads/banks/melli.svg';
                break;
            case '859210' :
                $MyTemp->name = 'بانک سپه';
                $MyTemp->icon = '/uploads/banks/sepah.svg';
                break;
            case '636214' :
                $MyTemp->name = 'بانک آینده';
                $MyTemp->icon = '/uploads/banks/ayande.svg';
                break;
            case '603770' :
                $MyTemp->name = 'بانک کشاورزی';
                $MyTemp->icon = '/uploads/banks/keshavarzi.svg';
                break;
            case '628023' :
                $MyTemp->name = 'بانک مسکن';
                $MyTemp->icon = '/uploads/banks/maskan.svg';
                break;
            case '627412' :
                $MyTemp->name = 'بانک اقتصاد نوین';
                $MyTemp->icon = '/uploads/banks/eghtesad.svg';
                break;
            case '622106' :
                $MyTemp->name = 'بانک پارسیان';
                $MyTemp->icon = '/uploads/banks/parsian.svg';
                break;
            case '502229' :
                $MyTemp->name = 'بانک پاسارگاد';
                $MyTemp->icon = '/uploads/banks/pasargad.svg';
                break;
            case '621986' :
                $MyTemp->name = 'بانک سامان';
                $MyTemp->icon = '/uploads/banks/saman.svg';
                break;
            case '639346' :
                $MyTemp->name = 'بانک سینا';
                $MyTemp->icon = '/uploads/banks/sina.svg';
                break;
            case '639607' :
                $MyTemp->name = 'بانک سرمایه';
                $MyTemp->icon = '/uploads/banks/sarmaye.svg';
                break;
            case '502806' :
                $MyTemp->name = 'بانک شهر';
                $MyTemp->icon = '/uploads/banks/shahr.svg';
                break;
            case '502938' :
                $MyTemp->name = 'بانک دی';
                $MyTemp->icon = '/uploads/banks/dey.svg';
                break;
            case '603769' :
                $MyTemp->name = 'بانک صادرات';
                $MyTemp->icon = '/uploads/banks/saderat.svg';
                break;
            case '610433' :
                $MyTemp->name = 'بانک ملت';
                $MyTemp->icon = '/uploads/banks/mellat.svg';
                break;
            case '627353' :
                $MyTemp->name = 'بانک تجارت';
                $MyTemp->icon = '/uploads/banks/tejarat.svg';
                break;
            case '859463' :
                $MyTemp->name = 'بانک رفاه';
                $MyTemp->icon = '/uploads/banks/refah.svg';
                break;
            case '627381' :
                $MyTemp->name = 'بانک انصار';
                $MyTemp->icon = '/uploads/banks/ansar.svg';
                break;
            default:
                $MyTemp->name = 'ناشناس';
                $MyTemp->icon = '/uploads/banks/default.svg';
        }

        return $MyTemp;
    }

    public static function getCurrencyImage($currencyId){

        $MyTemp = new stdClass();
        switch ($currencyId){
            case '1' :
                $MyTemp->icon = '/dist/images/wallet/IRT.svg';
                break;
            case '3' :
                $MyTemp->icon = '/dist/images/wallet/dollar.svg';
                break;
            case '4' :
                $MyTemp->icon = '/dist/images/wallet/euro.svg';
                break;
            default:
                $MyTemp->icon = '/dist/images/wallet/IRT.svg';
        }

        return $MyTemp;
    }


    public static function getUsdPrice()
    {
        $url = 'https://studio.tgju.org/index.php/web-service/list/price/free-currency?limit=150';
        $token = 'zsxu2ss272hmokux9qfb';

        $ch = curl_init();

        $headers = array(
            'Authorization: Bearer ' . $token,
            'User-Agent: ntirapp-agent'
        );

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);

        if ($response === false) {
            curl_close($ch);
            return 'cURL Error: ' . curl_error($ch);
        } else {
            // Handle the response here
            curl_close($ch);
            return $response;
        }
    }
    public static function generatePassword($length = 8)
    {
        $characters = '0123456789';
        $password = '';

        $characterCount = strlen($characters);

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = mt_rand(0, $characterCount - 1);
            $password .= $characters[$randomIndex];
        }

        return   $password;
    }

}
