<?php


use MJ\Database\DB;
use MJ\Security\Security;
use MJ\Utils\Utils;
use function MJ\Keys\sendResponse;

class Location
{


    /**
     * Add New Country
     * @param $title
     * @param $iso_country_two_word
     * @param $iso_language
     * @param $country_display_code
     * @param $country_code
     * @param $status_login
     * @param $ImageFlag
     * @return stdClass
     */
    public static function setNewCountry($title, $iso_country_two_word, $iso_language, $country_display_code, $country_code, $status_login, $priority, $ImageFlag, $status_poster)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $array = [];
        $array['admin'] = $admin_id;
        $array['date_create'] = time();
        $array['update'] = [];

        if ($admin_id > 0) {

            $sql = 'INSERT INTO `tbl_country` (`country_name`, `country_iso`, `country_iso_language`, `country_code`, `country_display_code`, 
                           `country_status_login`,`country_status_poster`, `country_flag`, `country_priority`,`country_options`) VALUES 
                    (:country_name,:country_iso,:country_iso_language,:country_code, :country_display_code, :country_status_login,
                     :country_status_poster,:country_flag,:country_priority,:country_options)';
            $params = [
                'country_name' => $title,
                'country_iso' => $iso_country_two_word,
                'country_iso_language' => $iso_language,
                'country_code' => $country_code,
                'country_display_code' => $country_display_code,
                'country_status_login' => $status_login,
                'country_status_poster' => $status_poster,
                'country_flag' => $ImageFlag,
                'country_priority' => $priority,
                'country_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }

        return $response;
    }


    /**
     * Get Country Info By Id
     * @param $id int
     * @return Object
     * @author Tjavan
     * @version 2.2.0
     */
    public static function getCountryById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_country` WHERE `country_id`=:country_id";
        $params = [
            'country_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Edit Country By Id
     * @param $id
     * @param $title
     * @param $iso_country_two_word
     * @param $iso_language
     * @param $country_display_code
     * @param $country_code
     * @param $status_login
     * @param null $flag
     * @return stdClass
     */
    public static function editCountryById($id, $title, $iso_country_two_word, $iso_language, $country_display_code, $country_code, $status_login, $priority, $flag = null, $status_poster = 'yes')
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCountryById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->country_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            if (is_null($flag)) {
                $sql = 'UPDATE `tbl_country` SET `country_name`=:country_name,`country_iso`=:country_iso,`country_iso_language`=:country_iso_language,
                    `country_code`=:country_code,`country_display_code`=:country_display_code,`country_status_login`=:country_status_login,
                    `country_status_poster`=:country_status_poster,`country_priority`=:country_priority,`country_options`=:country_options WHERE `country_id`=:country_id;';
                $params = [
                    'country_name' => $title,
                    'country_iso' => $iso_country_two_word,
                    'country_iso_language' => $iso_language,
                    'country_code' => $country_code,
                    'country_display_code' => $country_display_code,
                    'country_status_login' => $status_login,
                    'country_status_poster' => $status_poster,
                    'country_priority' => $priority,
                    'country_options' => json_encode($value),
                    'country_id' => $id,
                ];
            } else {
                $sql = 'UPDATE `tbl_country` SET `country_name`=:country_name,`country_iso`=:country_iso,`country_iso_language`=:country_iso_language,
                    `country_code`=:country_code,`country_display_code`=:country_display_code,`country_status_login`=:country_status_login,
                    `country_status_poster`=:country_status_poster,`country_flag`=:country_flag,`country_priority`=:country_priority,`country_options`=:country_options WHERE `country_id`=:country_id;';
                $params = [
                    'country_name' => $title,
                    'country_iso' => $iso_country_two_word,
                    'country_iso_language' => $iso_language,
                    'country_code' => $country_code,
                    'country_display_code' => $country_display_code,
                    'country_status_login' => $status_login,
                    'country_status_poster' => $status_poster,
                    'country_flag' => $flag,
                    'country_priority' => $priority,
                    'country_options' => json_encode($value),
                    'country_id' => $id,
                ];
            }
            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    /**
     * get All Countries Some Values
     * @return stdClass
     */
    public static function getAllCountriesSomeValues()
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT country_id, country_name FROM `tbl_country`  ORDER BY country_id DESC ;";
        $params = [];


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * get All Countries Some Values 2
     * @return stdClass
     */
    public static function getAllCountriesSomeValues2()
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT country_id, country_name,country_code,country_display_code,country_iso_language,country_iso,country_flag,country_priority
                FROM `tbl_country`  ORDER BY country_id DESC ;";
        $params = [];


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }




    /////////////////////////////////////////// start City

    /**
     * get All Cities By Country Id
     * @param $countryId
     * @param $status
     * @return stdClass
     */
    public static function getAllCitiesByCountryId($countryId, $status)
    {

        $response = sendResponse(0, "Error Msg");

        $row = "city_status_ground";
        if ($status == "air") {
            $row = "city_status_air";
        } elseif ($status == "railroad") {
            $row = "city_status_railroad";
        } elseif ($status == "inventory") {
            $row = "city_status_inventory";
        } elseif ($status == "ship") {
            $row = "city_status_ship";
        } elseif ($status == "poster") {
            $row = "city_status_poster";
        }

        $sql = "SELECT city_id,city_name FROM `tbl_cities` WHERE `country_id`=:country_id AND {$row}=:statuss ORDER BY city_id DESC ;";
        $params = [
            'country_id' => $countryId,
            'statuss' => 'yes',
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * get All Cities Some Values
     * @param null $status
     * @return stdClass
     */
    public static function getAllCitiesSomeValues($status = null)
    {

        $response = sendResponse(0, "Error Msg");

        if (is_null($status)) {
            $sql = "SELECT city_id,city_name FROM `tbl_cities`  ORDER BY city_id DESC ;";
            $params = [];
        } else {

            $row = "city_status_ground";
            if ($status == "air") {
                $row = "city_status_air";
            } elseif ($status == "railroad") {
                $row = "city_status_railroad";
            } elseif ($status == "inventory") {
                $row = "city_status_inventory";
            } elseif ($status == "ship") {
                $row = "city_status_ship";
            }
            $sql = "SELECT city_id,city_name FROM `tbl_cities` WHERE {$row}=:statuss ORDER BY city_id DESC ;";
            $params = [
                'statuss' => 'yes',
            ];
        }


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get All Cities
     * @return Object
     * @author Tjavan
     * @version 3.0.0
     */
    public static function getAllCitiesFromTable()
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT city_id, country_id, city_name, city_priority FROM `tbl_cities`  ORDER BY city_id DESC ;";
        $params = [];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Set New City
     * @param $title
     * @param $country
     * @param $status_ground
     * @param $status_air
     * @param $status_ship
     * @param $status_railroad
     * @param $status_inventory
     * @param $priority
     * @return stdClass
     */
    public static function setNewCity($title, $country, $status_ground, $status_air, $status_ship, $status_railroad, $status_inventory, $priority, $InternationalName, $status_poster)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $array = [];
        $array['admin'] = $admin_id;
        $array['date_create'] = time();
        $array['update'] = [];


        $countryInfoR = self::getCountryById($country);
        $countryInfo = [];
        $countryName = '';
        if ($countryInfoR->status == 200 && isset($countryInfoR->response[0])) {
            $countryInfo = $countryInfoR->response[0];
        }
        if (!empty($countryInfo)) {
            $countryName = (!empty(array_column(json_decode($countryInfo->country_name, true), 'value', 'slug')['en_US'])) ?
                array_column(json_decode($countryInfo->country_name, true), 'value', 'slug')['en_US'] . "-" : '';
        }
        $titleName = (!empty(array_column(json_decode($title, true), 'value', 'slug')['en_US'])) ?
            array_column(json_decode($title, true), 'value', 'slug')['en_US'] . "-" : '';
        $titleName = str_replace(' ', '%20', $titleName);
        $latLang = @Utils::getCityLocationByName1($countryName . $titleName);

        $lat = null;
        $long = null;
        if (isset($latLang) && !empty($latLang)) {
            $lat = $latLang['lat'];
            $long = $latLang['long'];
        }


        if ($admin_id > 0) {

            $sql = 'INSERT INTO `tbl_cities`(`country_id`, `city_name`, `city_status_ground`, `city_status_air`, `city_status_ship`,
                    `city_status_railroad`, `city_status_inventory`,`city_status_poster`,`city_international_name`, `city_lat`,`city_long`,`city_priority`,
                    `city_options`) VALUES 
                    (:country_id,:city_name,:city_status_ground,:city_status_air,:city_status_ship,
                     :city_status_railroad,:city_status_inventory,:city_status_poster,:city_international_name,
                     :city_lat,:city_long,:city_priority,:city_options)';
            $params = [
                'city_name' => $title,
                'country_id' => $country,
                'city_status_ground' => $status_ground,
                'city_status_air' => $status_air,
                'city_status_ship' => $status_ship,
                'city_status_railroad' => $status_railroad,
                'city_status_inventory' => $status_inventory,
                'city_status_poster' => $status_poster,
                'city_international_name' => $InternationalName,
                'city_priority' => $priority,
                'city_lat' => $lat,
                'city_long' => $long,
                'city_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }

        return $response;
    }

    /**
     * Get City Info By Id
     * @param $id int
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getCityById($id)
    {
        $response = sendResponse(0, "", "");

        $sql = "SELECT * FROM `tbl_cities` WHERE city_id=:city_id";
        $params = [
            'city_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    public static function getCityInfoById($id)
    {
        $city = new stdClass();
        $city->CityId = 0;
        $city->CityName = '';
        $city->CountryId = 0;

        $response = $city;

        $sql = "SELECT * FROM `tbl_cities` WHERE city_id=:city_id";
        $params = [
            'city_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {

            $city->CityId = $result->response[0]->city_id;
            $city->CityName = $result->response[0]->city_name;
            $city->CountryId = $result->response[0]->country_id;
            $response = $city;
        }

        return $response;
    }

    /**
     * Get City Info By Id
     * @param $id int
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getCityNameById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_cities` WHERE city_id=:city_id";
        $params = [
            'city_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        if ($result->status == 200) {
            $response = sendResponse(200, "", array_column(json_decode($result->response[0]->city_name), 'value', 'slug')[$language]);
        }

        return $response;
    }

    public static function editCityById($id, $title, $country, $status_ground, $status_air, $status_ship, $status_railroad, $status_inventory, $priority, $InternationalName, $lat, $long, $status_poster)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCityById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->city_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

//            $countryInfoR = self::getCountryById($country);
//            $countryInfo = [];
//            $countryName = '';
//            if ($countryInfoR->status == 200 && isset($countryInfoR->response[0])) {
//                $countryInfo = $countryInfoR->response[0];
//            }
//            if (!empty($countryInfo)) {
//                $countryName = (!empty(array_column(json_decode($countryInfo->country_name, true), 'value', 'slug')['en_US'])) ?
//                    array_column(json_decode($countryInfo->country_name, true), 'value', 'slug')['en_US'] . "-" : '';
//            }
//            $titleName = (!empty(array_column(json_decode($title, true), 'value', 'slug')['en_US'])) ?
//                array_column(json_decode($title, true), 'value', 'slug')['en_US'] . "-" : '';
//            $titleName = str_replace(' ', '%20', $titleName);
//            $latLang = @Utils::getCityLocationByName1($countryName . $titleName);

//            $lat = null;
//            $long = null;
//            if (isset($latLang) && !empty($latLang)) {
//                $lat = $latLang['lat'];
//                $long = $latLang['long'];
//            }

            $sql = 'UPDATE `tbl_cities` SET `country_id`=:country_id,`city_name`=:city_name,`city_status_ground`=:city_status_ground,
                        `city_status_air`=:city_status_air,`city_status_ship`=:city_status_ship,`city_status_railroad`=:city_status_railroad,
                        `city_status_inventory`=:city_status_inventory,`city_status_poster`=:city_status_poster,`city_international_name`=:city_international_name,`city_priority`=:city_priority,
                        `city_lat`=:city_lat,`city_long`=:city_long,`city_options`=:city_options WHERE `city_id`=:city_id;';
            $params = [
                'city_name' => $title,
                'country_id' => $country,
                'city_status_ground' => $status_ground,
                'city_status_air' => $status_air,
                'city_status_ship' => $status_ship,
                'city_status_railroad' => $status_railroad,
                'city_status_inventory' => $status_inventory,
                'city_status_poster' => $status_poster,
                'city_priority' => $priority,
                'city_international_name' => $InternationalName,
                'city_lat' => $lat,
                'city_long' => $long,
                'city_options' => json_encode($value),
                'city_id' => $id,
            ];
            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }

    /**
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getCountriesList()
    {
        $response = sendResponse(200, '', []);

        $sql = "select *
        from tbl_country
        order by country_priority;";
        $result = DB::rawQuery($sql, []);
        if ($result->status == 200) {
            $countriesList = [];
            foreach ($result->response as $item) {
                $country = new stdClass();
                $country->CountryId = $item->country_id;
                $country->CountryName = array_column(json_decode($item->country_name), 'value', 'slug')[$_COOKIE['language']];

                array_push($countriesList, $country);
            }
            $response = sendResponse(200, '', $countriesList);
        }
        return $response;
    }


    /**
     * @param $cityId
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getCityDetail($cityId)
    {
        $city = self::getCityById($cityId);
        if ($city->status == 200) {
            $detail = new stdClass();
            foreach ($city->response as $item) {
                $detail->CityId = $item->city_id;
                $detail->CountryId = $item->country_id;
                $detail->CityName = array_column(json_decode($item->city_name), 'value', 'slug')[$_COOKIE['language']];
                $detail->CityGroundIsActive = $item->city_status_ground;
                $detail->CityAirIsActive = $item->city_status_air;
                $detail->CityShipIsActive = $item->city_status_ship;
                $detail->CityRailroadIsActive = $item->city_status_railroad;
                $detail->CityInventoryIsActive = $item->city_status_inventory;
                $detail->CityLat = $item->city_lat;
                $detail->CityLong = $item->city_long;
                $detail->CityPriority = $item->city_priority;
            }
            return $detail;
        }
        return new stdClass();
    }


    /**
     * @param $cityId
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getCountryByCityId($cityId)
    {
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $sql = "select *
        from tbl_country
        inner join tbl_cities on tbl_country.country_id = tbl_cities.country_id
        where city_id = :cityId;";
        $params = [
            'cityId' => $cityId
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $country = new stdClass();
            foreach ($result->response as $item) {
                $country->CountryId = $item->country_id;
                $country->CountryName = array_column(json_decode($item->country_name), 'value', 'slug')[$language];
                $country->CountryISO = $item->country_iso;
                $country->CountryLang = $item->country_iso_language;
                $country->CountryCode = $item->country_code;
                $country->CountryDisplayCode = $item->country_display_code;
            }
            return $country;
        }
        return new stdClass();
    }


    /**
     * @param $countryId
     * @param $type
     * @return stdClass
     */
    public static function getCitiesByCountry($countryId, $type)
    {
        $response = sendResponse(0, 'Not found');
        $condition = "city_status_ground";
        if ($type == "air") {
            $condition = "city_status_air";
        } elseif ($type == "railroad") {
            $condition = "city_status_railroad";
        } elseif ($type == "inventory") {
            $condition = "city_status_inventory";
        } elseif ($type == "ship") {
            $condition = "city_status_ship";
        } elseif ($type == "poster") {
            $condition = "city_status_poster";
        }

        $sql = "select *
        from tbl_cities
        where country_id = :countryId and {$condition} = :status
        order by city_priority asc,city_name asc;";
        $params = [
            'countryId' => $countryId,
            'status' => 'yes',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * delete City By Id
     * @param $cityId
     * @return stdClass
     */
    public static function deleteCity($cityId)
    {
        $response = sendResponse(200, '');
        $sql = 'DELETE FROM `tbl_cities` WHERE city_id=:city_id';
        $params = [
            'city_id' => $cityId,
        ];
        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    /**
     * get Multi City And Country By City Id
     * @param $cityId
     * @return stdClass
     */
    public static function getMultiCityAndCountryByCityId($cityId)
    {
        $response = sendResponse(200, '', "");
        $sql = "SELECT tbl_cities.city_id,tbl_cities.city_name,tbl_cities.city_lat,tbl_cities.city_long,tbl_country.country_id,tbl_country.country_name FROM `tbl_cities` INNER JOIN `tbl_country` ON tbl_cities.country_id=tbl_country.country_id WHERE `city_id` IN  ({$cityId})";
        $params = [];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }

    /**
     * @param $status
     * @return \stdClass
     * @author morteza
     */
    public static function getAllCityForShip($country_id, $status = 'yes'): stdClass
    {
        //SELECT * FROM `tbl_cities` where
        $response = sendResponse(200, '');
        $sql = "SELECT * FROM `tbl_cities` where tbl_cities.country_id = :country_id and tbl_cities.city_status_ship = :status order by tbl_cities.city_priority asc , city_name asc ";
        $params = [
            'status' => $status,
            'country_id' => $country_id
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $citiesList = [];
            foreach ($result->response as $item) {
                $city = new stdClass();
                $city->city_id = $item->city_id;
                $city->city_name = array_column(json_decode($item->city_name), 'value', 'slug')[$_COOKIE['language']];
                if ($_COOKIE['language'] == 'fa_IR') {
                    $city->CityNameEN = array_column(json_decode($item->city_name), 'value', 'slug')['en_US'];
                } else {
                    $city->CityNameEN = '';
                }

                array_push($citiesList, $city);
            }
            $response = sendResponse(200, '', $citiesList);
        }
        return $response;
    }

    /**
     * @param $status
     * @return \stdClass
     * @author morteza
     */
    public static function getAllPostForShip($countryId, $status = 'active'): stdClass
    {
        //SELECT * FROM `tbl_cities` where
        $response = sendResponse(200, '');
        $sql = "SELECT * FROM `tbl_ports` inner join tbl_cities on tbl_ports.city_id = tbl_cities.city_id
                where port_status = :status AND tbl_cities.country_id = :countryId ";
        $params = [
            'status' => $status,
            'countryId' => $countryId
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $portLists = [];
            foreach ($result->response as $item) {
                $port = new stdClass();
                $port->port_id = $item->port_id;
                $port->port_name = array_column(json_decode($item->port_name), 'value', 'slug')[$_COOKIE['language']];
                if ($_COOKIE['language'] == 'fa_IR') {
                    $port->port_nameEN = array_column(json_decode($item->port_name), 'value', 'slug')['en_US'];
                } else {
                    $port->port_nameEN = '';
                }


                array_push($portLists, $port);
            }
            $response = sendResponse(200, '', $portLists);
        }
        return $response;
    }

    /**
     * @param $status
     * @return \stdClass
     * @author morteza
     */
    public static function getAllCityForAir($country_id, $status = 'yes'): stdClass
    {
        //SELECT * FROM `tbl_cities` where
        $response = sendResponse(200, '');
        $sql = "SELECT * FROM `tbl_cities` where tbl_cities.country_id =:country_id and  tbl_cities.city_status_air = :status order by tbl_cities.city_priority asc  , tbl_cities.city_name asc";
        $params = [
            'status' => $status,
            'country_id' => $country_id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $citiesList = [];
            foreach ($result->response as $item) {
                $city = new stdClass();
                $city->city_id = $item->city_id;
                $city->city_name = array_column(json_decode($item->city_name), 'value', 'slug')[$_COOKIE['language']];
                if ($_COOKIE['language'] == 'fa_IR') {
                    $city->CityNameEN = array_column(json_decode($item->city_name), 'value', 'slug')['en_US'];
                } else {
                    $city->CityNameEN = '';
                }
                array_push($citiesList, $city);
            }
            $response = sendResponse(200, '', $citiesList);
        }
        return $response;
    }

    /**
     * @param $status
     * @return \stdClass
     * @author morteza
     */
    public static function getAllAirPortsForAir($countryId, $status = 'active'): stdClass
    {
        //SELECT * FROM `tbl_cities` where
        $response = sendResponse(200, '');
        $sql = "SELECT * FROM `tbl_airports` inner join tbl_cities on tbl_airports.city_id = tbl_cities.city_id
                where airport_status = :status AND tbl_cities.country_id = :countryId";

        $params = [
            'status' => $status,
            'countryId' => $countryId,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $airPortLists = [];
            foreach ($result->response as $item) {
                $port = new stdClass();
                $port->airport_id = $item->airport_id;
                $port->airport_name = array_column(json_decode($item->airport_name), 'value', 'slug')[$_COOKIE['language']];
                if ($_COOKIE['language'] == 'fa_IR') {
                    $port->airport_nameEN = array_column(json_decode($item->airport_name), 'value', 'slug')['en_US'];
                } else {
                    $port->airport_nameEN = '';
                }

                array_push($airPortLists, $port);
            }
            $response = sendResponse(200, '', $airPortLists);
        }
        return $response;
    }


    /**
     * @param $status
     * @return \stdClass
     * @author morteza
     */
    public static function getAllCityForInventory($country_id, $status = 'yes'): stdClass
    {
        //SELECT * FROM `tbl_cities` where
        $response = sendResponse(200, '');
        $sql = "SELECT * FROM `tbl_cities` where tbl_cities.city_status_inventory = :status and tbl_cities.country_id = :country_id order by tbl_cities.city_options asc , tbl_cities.city_name asc";
        $params = [
            'status' => $status,
            'country_id' => $country_id
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $citiesList = [];
            foreach ($result->response as $item) {
                $city = new stdClass();
                $city->city_id = $item->city_id;
                $city->city_name = array_column(json_decode($item->city_name), 'value', 'slug')[$_COOKIE['language']];
                if ($_COOKIE['language'] == 'fa_IR') {
                    $city->CityNameEN = array_column(json_decode($item->city_name), 'value', 'slug')['en_US'];
                } else {
                    $city->CityNameEN = '';
                }

                array_push($citiesList, $city);
            }
            $response = sendResponse(200, '', $citiesList);
        }
        return $response;
    }


    /**
     * @param $country_id
     * @param $status
     * @return stdClass
     */
    public static function getAllCityForRailroad($country_id, $status = 'yes'): stdClass
    {
        //SELECT * FROM `tbl_cities` where
        $response = sendResponse(200, '', '');
        $sql = "SELECT * FROM `tbl_cities` where tbl_cities.country_id =:country_id and  tbl_cities.city_status_railroad = :status order by tbl_cities.city_priority asc, tbl_cities.city_name asc";
        $params = [
            'status' => $status,
            'country_id' => $country_id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $citiesList = [];
            foreach ($result->response as $item) {
                $city = new stdClass();
                $city->city_id = $item->city_id;
                $city->city_name = array_column(json_decode($item->city_name), 'value', 'slug')[$_COOKIE['language']];

                if ($_COOKIE['language'] == 'fa_IR') {
                    $city->CityNameEN = array_column(json_decode($item->city_name), 'value', 'slug')['en_US'];
                } else {
                    $city->CityNameEN = '';
                }
                array_push($citiesList, $city);
            }
            $response = sendResponse(200, '', $citiesList);
        }
        return $response;
    }


    /**
     * @param $countryId
     * @param $status
     * @return stdClass
     */
    public static function getAllStationForRailroad($countryId, $status = 'active'): stdClass
    {
        //SELECT * FROM `tbl_cities` where
        $response = sendResponse(200, '');
        $sql = "SELECT * FROM `tbl_railroad` inner join tbl_cities on tbl_railroad.city_id = tbl_cities.city_id
                where railroad_status = :status AND tbl_cities.country_id = :countryId";

        $params = [
            'status' => $status,
            'countryId' => $countryId,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $airPortLists = [];
            foreach ($result->response as $item) {
                $railroad = new stdClass();
                $railroad->railroad_id = $item->railroad_id;
                $railroad->railroad_name = array_column(json_decode($item->railroad_name), 'value', 'slug')[$_COOKIE['language']];
                if ($_COOKIE['language'] == 'fa_IR') {
                    $railroad->railroad_nameEN = array_column(json_decode($item->railroad_name), 'value', 'slug')['en_US'];
                } else {
                    $railroad->railroad_nameEN = '';
                }

                array_push($airPortLists, $railroad);
            }
            $response = sendResponse(200, '', $airPortLists);
        }
        return $response;
    }


    public static function getCityByNmaeLike($like)
    {
        $response = [];

        $sql = "SELECT * FROM `tbl_cities` WHERE tbl_cities.city_name LIKE concat('%', :search, '%');";
        $params = [
            'search' => $like,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = [];
            foreach ($result->response as $loop) {
                $response[] = $loop->city_id;
            }

        }

        return $response;
    }


    /**
     * get City By Country Name Like
     * @param $like
     * @return array
     */
    public static function getCountryByNameLike($like)
    {
        $response = [];

        $sql = "SELECT * FROM `tbl_country` INNER JOIN tbl_cities ON tbl_cities.country_id=tbl_country.country_id WHERE tbl_country.country_name LIKE concat('%', :search, '%');";
        $params = [
            'search' => $like,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = [];
            foreach ($result->response as $loop) {
                $response[] = $loop->city_id;
            }

        }

        return $response;
    }


    public static function getAllCountriesFromLoginPage()
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT country_id, country_name,country_code,country_display_code,country_flag ,country_iso FROM `tbl_country` WHERE country_status_login=:status ORDER BY country_priority ASC ;";
        $params = [
            'status' => 'yes'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    public static function getCountriesListByStatus($status)
    {
        if ($status == "poster") {
            $status = "country_status_poster";
        } else {
            $status = "country_status_login";
        }


        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        $response = sendResponse(200, '', []);

        $sql = "SELECT * FROM `tbl_country` WHERE " . $status . " =:status ORDER BY country_priority;";
        $result = DB::rawQuery($sql, ['status' => 'yes']);
        if ($result->status == 200) {
            $countriesList = [];
            foreach ($result->response as $item) {
                $country = new stdClass();
                $country->CountryId = $item->country_id;
                $country->CountryNameEn = array_column(json_decode($item->country_name), 'value', 'slug')['en_US'];
                $country->CountryName = array_column(json_decode($item->country_name), 'value', 'slug')[$language];
                array_push($countriesList, $country);
            }
            $response = sendResponse(200, '', $countriesList);
        }
        return $response;
    }


    public static function getCitiesListByStatus($countryId, $type)
    {
        $response = ['status' => -1, 'response' => []];;
        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        $cities = self::getCitiesByCountry($countryId, $type);

        if ($cities->status == 200) {
            $citiesList = [];
            foreach ($cities->response as $item) {
                $city = new stdClass();
                $city->CityId = $item->city_id;
                $city->CityName = array_column(json_decode($item->city_name), 'value', 'slug')[$language];
                $city->CityNameEN = array_column(json_decode($item->city_name), 'value', 'slug')['en_US'];

                array_push($citiesList, $city);
            }
            $response = ['status' => 200, 'response' => $citiesList];
        }
        return $response;
    }


    public static function getCitiesForMultiCountries($country_ids)
    {
        $response = sendResponse(0, '$sql', '$cities');

        $sql = "select *
        from tbl_cities";


        $sql .= " WHERE ";
        $searchConditions = [];
        foreach ($country_ids as   $column ) {
            $searchConditions[] = "country_id = $column";
        }
        $sql .= implode(" or ", $searchConditions);
        $sql .= "
         order by city_priority asc,city_name asc;";
        $cities = DB::rawQuery($sql , []);

        if ($cities->status == 200) {
            $citiesList = [];
            foreach ($cities->response as $item) {
                $city = new stdClass();
                $city->CityId = $item->city_id;
                $city->CityName = array_column(json_decode($item->city_name), 'value', 'slug')[$_COOKIE['language']];
                if ($_COOKIE['language'] == 'fa_IR') {
                    $city->CityNameEN = array_column(json_decode($item->city_name), 'value', 'slug')['en_US'];
                } else {
                    $city->CityNameEN = '';
                }
                array_push($citiesList, $city);
            }
            $response = sendResponse(200, $sql, $citiesList);
        }

        return $response;
    }
    public static function getCountriesListM()
    {
        $response = sendResponse(200, '', []);

        $sql = "select *
        from tbl_country
        order by country_priority;";
        $result = DB::rawQuery($sql, []);
        if ($result->status == 200) {
            foreach ($result->response as $item) {
                $item->country_name = array_column(json_decode($item->country_name), 'value', 'slug')[$_COOKIE['language']];
            }
            $response = sendResponse(200, '', $result->response);
        }
        return $response;
    }

    public static function getCitiesM($country_id)
    {
        $response = sendResponse(200, '', []);

        $sql = "select *
        from tbl_cities 
        inner join tbl_country on tbl_cities.country_id = tbl_country.country_id
        where tbl_cities.country_id = :country_id";
        $params = [
            'country_id'=>$country_id
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            foreach ($result->response as $item) {
                $item->country_name = array_column(json_decode($item->country_name), 'value', 'slug')[$_COOKIE['language']];
                $item->city_name = array_column(json_decode($item->city_name), 'value', 'slug')[$_COOKIE['language']];
            }
            $response =   sendResponse(200,'success',$result->response);
        }
        return $response;
    }
}