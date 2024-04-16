<?php


use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Driver
{
    /**
     * @param $userId
     *
     * @return int
     */
    public static function getCountOfCompletedRequests($userId)
    {
        $response = 0;

        $sql = "select count(*) as count
        from tbl_requests
        where request_status = :status and user_id = :userId;";
        $params = [
            'userId' => $userId,
            'status' => 'completed'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->count;
        }
        return $response;
    }


    /**
     * @param int $page
     * @param int $perPage
     *
     * @return stdClass
     */
    public static function getCargoList($source_country, $dest_country, $source_city, $dest_city, $car_types, $page = 0, $perPage = 9)
    {

        $response = sendResponse(0, '', []);
        global $lang;
        $from = $page * $perPage;
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';

        $sql = "select cargo_id, cargo_car_count,cargo_name_fa_IR , cargo_name_en_US , cargo_name_ru_RU , cargo_name_tr_Tr, cargo_weight, cargo_recommended_price, cargo_monetary_unit, 
                cargo_origin_id, cargo_destination_id, cargo_start_date,cargo_status , tbl_car_types.type_id ,cargo_green, tbl_cargo_categories.category_name, 
                tbl_cargo_categories.category_icon, tbl_cargo_categories.category_color, currency_name,
                type_name , type_icon
                from tbl_cargo
                inner join tbl_cargo_categories on tbl_cargo.category_id = tbl_cargo_categories.category_id
                inner join tbl_car_types  on tbl_car_types.type_id = tbl_cargo.type_id
                inner join tbl_currency on tbl_cargo.cargo_monetary_unit = tbl_currency.currency_id
                where cargo_status in('progress', 'accepted')  ";
        if ($source_country != 'all-country') {
            if ($source_city != 'all-city') {
                $sql .= " and tbl_cargo.cargo_origin_id = $source_city ";
            } else {
                $source_cities = Location::getAllCitiesByCountryId($source_country, 'ground');
                if ($source_cities->status == 200) {
                    $source_cities = $source_cities->response;
                    $sql .= " and ( ";
                } else {
                    $source_cities = [];
                }

                foreach ($source_cities as $index => $item) {
                    if ($index == count($source_cities) - 1) {
                        $sql .= " tbl_cargo.cargo_origin_id = $item->city_id )";
                    } else {
                        $sql .= " tbl_cargo.cargo_origin_id = $item->city_id or ";
                    }
                }
            }
        }
        if ($dest_country != 'all-country') {
            if ($dest_city != 'all-city') {
                $sql .= " and tbl_cargo.cargo_destination_id = $dest_city ";
            } else {
                $dest_cities = Location::getAllCitiesByCountryId($dest_country, 'ground');
                if ($dest_cities->status == 200) {
                    $dest_cities = $dest_cities->response;
                    $sql .= " and ( ";
                } else {
                    $dest_cities = [];
                }

                foreach ($dest_cities as $index => $item) {
                    if ($index == count($dest_cities) - 1) {
                        $sql .= " tbl_cargo.cargo_destination_id = $item->city_id )";
                    } else {
                        $sql .= " tbl_cargo.cargo_destination_id = $item->city_id or ";
                    }
                }
            }
        }


        if ($car_types != []) {
            $sql .= ' and tbl_cargo.type_id in ( ';
            foreach ($car_types as $index => $type) {
                if ($index == count($car_types) - 1) {
                    $sql .= $type;
                } else {
                    $sql .= $type . ',';
                }
            }
            $sql .= ' ) ';
        }
        $slugname = 'cargo_name_' . $language;
        $sql .= " and $slugname IS NOT NULL " ;
        $sql .= "order by cargo_id desc limit {$from},{$perPage};";
        $params = [];


        $result = DB::rawQuery($sql, $params);
//        print_r($result);
        if ($result->status == 200) {
            $cargoList = [];
            foreach ($result->response as $item) {
                $cargo = new stdClass();
                $cargo->CargoId = $item->cargo_id;
                $cargo->CargoName_fa_IR = $item->cargo_name_fa_IR;
                $cargo->CargoName_en_US = $item->cargo_name_en_US;
                $cargo->CargoName_ru_RU = $item->cargo_name_ru_RU;
                $cargo->CargoName_tr_Tr = $item->cargo_name_tr_Tr;
                $cargo->CargoWeight = $item->cargo_weight;
                $cargo->CargoRecomendedPrice = $item->cargo_recommended_price;
                $cargo->CargoMonetaryUnit = array_column(json_decode($item->currency_name), 'value', 'slug')[$language];
                $cargo->CargoOrigin = @array_column(json_decode(Location::getCityById($item->cargo_origin_id)->response[0]->city_name), 'value', 'slug')[$language];
                $cargo->CargoOriginid = $item->cargo_origin_id;
                $cargo->CargoDestination = @array_column(json_decode(Location::getCityById($item->cargo_destination_id)->response[0]->city_name), 'value', 'slug')[$language];
                $cargo->CargoDestinationid = $item->cargo_destination_id;
                $cargo->CargoStartTransportation = $item->cargo_start_date;
                $cargo->CategoryName = array_column(json_decode($item->category_name), 'value', 'slug')[$language];
                $cargo->TypeName = array_column(json_decode($item->type_name), 'value', 'slug')[$language];
                $cargo->TypeId = $item->type_id;
                $cargo->TypeIcon = $item->type_icon;
                $cargo->CategoryIcon = $item->category_icon;
                $cargo->CategoryColor = $item->category_color;
                $cargo->CargoCarCount = $item->cargo_car_count;
                $cargo->CargoGreen = $item->cargo_green;
                $cargo->CargoStaus = $item->cargo_status;

                array_push($cargoList, $cargo);
            }
            $response = sendResponse(200, $sql, $cargoList);
        }
//        $response = sendResponse(0,$sql, []);
        return $response;
    }
    /**
     * @param int $page
     * @param int $perPage
     *
     * @return stdClass
     */
    public static function getCargoWithId($cargo_id)
    {
        $response = sendResponse(0, '', []);
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $sql = "select cargo_id, cargo_car_count,cargo_name_fa_IR , cargo_name_en_US , cargo_name_ru_RU , cargo_name_tr_Tr, cargo_weight, cargo_recommended_price, cargo_monetary_unit, 
                cargo_origin_id, cargo_destination_id, cargo_start_date,cargo_status , tbl_car_types.type_id ,cargo_green, tbl_cargo_categories.category_name, 
                tbl_cargo_categories.category_icon, tbl_cargo_categories.category_color, currency_name,
                type_name , type_icon
                from tbl_cargo
                inner join tbl_cargo_categories on tbl_cargo.category_id = tbl_cargo_categories.category_id
                inner join tbl_car_types  on tbl_car_types.type_id = tbl_cargo.type_id
                inner join tbl_currency on tbl_cargo.cargo_monetary_unit = tbl_currency.currency_id
                where tbl_cargo.cargo_id =$cargo_id and tbl_cargo.cargo_status in('progress', 'accepted')  ";
        $params = [];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $cargoList = [];
            foreach ($result->response as $item) {
                $cargo = new stdClass();
                $cargo->CargoId = $item->cargo_id;
                $cargo->CargoName_fa_IR = $item->cargo_name_fa_IR;
                $cargo->CargoName_en_US = $item->cargo_name_en_US;
                $cargo->CargoName_ru_RU = $item->cargo_name_ru_RU;
                $cargo->CargoName_tr_Tr = $item->cargo_name_tr_Tr;
                $cargo->CargoWeight = $item->cargo_weight;
                $cargo->CargoRecomendedPrice = $item->cargo_recommended_price;
                $cargo->CargoMonetaryUnit = array_column(json_decode($item->currency_name), 'value', 'slug')[$language];
                $cargo->CargoOrigin = @array_column(json_decode(Location::getCityById($item->cargo_origin_id)->response[0]->city_name), 'value', 'slug')[$language];
                $cargo->CargoOriginid = $item->cargo_origin_id;
                $cargo->CargoDestination = @array_column(json_decode(Location::getCityById($item->cargo_destination_id)->response[0]->city_name), 'value', 'slug')[$language];
                $cargo->CargoDestinationid = $item->cargo_destination_id;
                $cargo->CargoStartTransportation = $item->cargo_start_date;
                $cargo->CategoryName = array_column(json_decode($item->category_name), 'value', 'slug')[$language];
                $cargo->TypeName = array_column(json_decode($item->type_name), 'value', 'slug')[$language];
                $cargo->TypeId = $item->type_id;
                $cargo->TypeIcon = $item->type_icon;
                $cargo->CategoryIcon = $item->category_icon;
                $cargo->CategoryColor = $item->category_color;
                $cargo->CargoCarCount = $item->cargo_car_count;
                $cargo->CargoGreen = $item->cargo_green;
                $cargo->CargoStaus = $item->cargo_status;

                array_push($cargoList, $cargo);
            }
            $response = sendResponse(200, $sql, $cargoList);
        }
        return $response;
    }
    /**
     * @param int $page
     * @param int $perPage
     *
     * @return stdClass
     */
    public static function getCargoInWithId($cargo_id)
    {
        $response = sendResponse(0, '', []);
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $sql = "select cargo_id, cargo_car_count,cargo_name_fa_IR , cargo_name_en_US , cargo_name_ru_RU , cargo_name_tr_Tr, cargo_weight, cargo_recommended_price, cargo_monetary_unit, 
                cargo_origin_id, cargo_destination_id, cargo_start_date,cargo_status , tbl_car_types.type_id , tbl_cargo_categories.category_name, 
                tbl_cargo_categories.category_icon, tbl_cargo_categories.category_color, currency_name,
                type_name , type_icon
                from tbl_cargo_in
                inner join tbl_cargo_categories on tbl_cargo_in.category_id = tbl_cargo_categories.category_id
                left join tbl_car_types  on tbl_car_types.type_id = tbl_cargo_in.type_id
                left join tbl_currency on tbl_cargo_in.cargo_monetary_unit = tbl_currency.currency_id
                where tbl_cargo_in.cargo_id =$cargo_id and tbl_cargo_in.cargo_status in('progress', 'accepted')  ";
        $params = [];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $cargoList = [];
            foreach ($result->response as $item) {
                $cargo = new stdClass();
                $cargo->CargoId = $item->cargo_id;
                $cargo->CargoName_fa_IR = $item->cargo_name_fa_IR;
                $cargo->CargoName_en_US = $item->cargo_name_en_US;
                $cargo->CargoName_ru_RU = $item->cargo_name_ru_RU;
                $cargo->CargoName_tr_Tr = $item->cargo_name_tr_Tr;
                $cargo->CargoWeight = $item->cargo_weight;
                $cargo->CargoRecomendedPrice = $item->cargo_recommended_price;
                $cargo->CargoMonetaryUnit = array_column(json_decode($item->currency_name), 'value', 'slug')[$language];
                $cargo->CargoOrigin = @array_column(json_decode(Location::getCityById($item->cargo_origin_id)->response[0]->city_name), 'value', 'slug')[$language];
                $cargo->CargoOriginid = $item->cargo_origin_id;
                $cargo->CargoDestination = @array_column(json_decode(Location::getCityById($item->cargo_destination_id)->response[0]->city_name), 'value', 'slug')[$language];
                $cargo->CargoDestinationid = $item->cargo_destination_id;
                $cargo->CargoStartTransportation = $item->cargo_start_date;
                $cargo->CategoryName = array_column(json_decode($item->category_name), 'value', 'slug')[$language];
                $cargo->TypeName = array_column(json_decode($item->type_name), 'value', 'slug')[$language];
                $cargo->TypeId = $item->type_id;
                $cargo->TypeIcon = $item->type_icon;
                $cargo->CategoryIcon = $item->category_icon;
                $cargo->CategoryColor = $item->category_color;
                $cargo->CargoCarCount = $item->cargo_car_count;

                $cargo->CargoStaus = $item->cargo_status;

                array_push($cargoList, $cargo);
            }
            $response = sendResponse(200, $sql, $cargoList);
        }
        return $response;
    }
    /**
     * @param int $page
     * @param int $perPage
     *
     * @return stdClass
     */
    public static function getCargoListIn($source_country, $source_city, $dest_city, $car_types, $page = 0, $perPage = 9)
    {

        $response = sendResponse(0, '', []);
        global $lang;
        $from = $page * $perPage;
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        $sql = "select cargo_id, cargo_car_count,cargo_name_fa_IR , cargo_name_en_US , cargo_name_ru_RU , cargo_name_tr_Tr, cargo_weight, cargo_recommended_price, cargo_monetary_unit, 
                cargo_origin_id, cargo_destination_id, cargo_start_date,cargo_status , tbl_cargo_categories.category_name, 
                tbl_cargo_categories.category_icon, tbl_cargo_categories.category_color, currency_name,
       type_name , type_icon
                from tbl_cargo_in
                inner join tbl_cargo_categories on tbl_cargo_in.category_id = tbl_cargo_categories.category_id
                inner join tbl_car_types  on tbl_car_types.type_id = tbl_cargo_in.type_id
                inner join tbl_currency on tbl_cargo_in.cargo_monetary_unit = tbl_currency.currency_id
                where cargo_status in('progress', 'accepted')  ";
        if ($source_country != 'all-country') {
            if ($source_city != 'all-city') {
                $sql .= " and tbl_cargo_in.cargo_origin_id = $source_city ";
            } else {
                $source_cities = Location::getAllCitiesByCountryId($source_country, 'ground');
                if ($source_cities->status == 200) {
                    $source_cities = $source_cities->response;
                    $sql .= " and ( ";
                } else {
                    $source_cities = [];
                }

                foreach ($source_cities as $index => $item) {
                    if ($index == count($source_cities) - 1) {
                        $sql .= " tbl_cargo_in.cargo_origin_id = $item->city_id )";
                    } else {
                        $sql .= " tbl_cargo_in.cargo_origin_id = $item->city_id or ";
                    }
                }
            }
        }
        if ($source_country != 'all-country') {
            if ($dest_city != 'all-city') {
                $sql .= " and tbl_cargo_in.cargo_destination_id = $dest_city ";
            } else {
                $dest_cities = Location::getAllCitiesByCountryId($source_country, 'ground');
                if ($dest_cities->status == 200) {
                    $dest_cities = $dest_cities->response;
                    $sql .= " and ( ";
                } else {
                    $dest_cities = [];
                }

                foreach ($dest_cities as $index => $item) {
                    if ($index == count($dest_cities) - 1) {
                        $sql .= " tbl_cargo_in.cargo_destination_id = $item->city_id )";
                    } else {
                        $sql .= " tbl_cargo_in.cargo_destination_id = $item->city_id or ";
                    }
                }
            }
        }


        if ($car_types != []) {
            $sql .= ' and tbl_cargo_in.type_id in ( ';
            foreach ($car_types as $index => $type) {
                if ($index == count($car_types) - 1) {
                    $sql .= $type;
                } else {
                    $sql .= $type . ',';
                }
            }
            $sql .= ' ) ';
        }
        $sql .= " and $slugname IS NOT NULL " ;
        $sql .= " order by tbl_cargo_in.cargo_id desc limit {$from},{$perPage};";
        $params = [];


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $cargoList = [];
            foreach ($result->response as $item) {
                $cargo = new stdClass();
                $cargo->CargoId = $item->cargo_id;
                $cargo->CargoName_fa_IR = $item->cargo_name_fa_IR;
                $cargo->CargoName_en_US = $item->cargo_name_en_US;
                $cargo->CargoName_ru_RU = $item->cargo_name_ru_RU;
                $cargo->CargoName_tr_Tr = $item->cargo_name_tr_Tr;
                $cargo->CargoWeight = $item->cargo_weight;
                $cargo->CargoRecomendedPrice = $item->cargo_recommended_price;
                $cargo->CargoMonetaryUnit = array_column(json_decode($item->currency_name), 'value', 'slug')[$language];
                $cargo->CargoOrigin = @array_column(json_decode(Location::getCityById($item->cargo_origin_id)->response[0]->city_name), 'value', 'slug')[$language];
                $cargo->CargoOriginid = $item->cargo_origin_id;
                $cargo->CargoDestination = @array_column(json_decode(Location::getCityById($item->cargo_destination_id)->response[0]->city_name), 'value', 'slug')[$language];
                $cargo->CargoDestinationid = $item->cargo_destination_id;
                $cargo->CargoStartTransportation = $item->cargo_start_date;
                $cargo->CategoryName = array_column(json_decode($item->category_name), 'value', 'slug')[$language];
                $cargo->TypeName = array_column(json_decode($item->type_name), 'value', 'slug')[$language];
                $cargo->TypeIcon = $item->type_icon;
                $cargo->CategoryIcon = $item->category_icon;
                $cargo->CategoryColor = $item->category_color;
                $cargo->CargoCarCount = $item->cargo_car_count;
                $cargo->CargoStaus = $item->cargo_status;

                array_push($cargoList, $cargo);
            }
            $response = sendResponse(200, $sql, $cargoList);
        }
//        $response = sendResponse(0,$sql, []);
        return $response;
    }


    /**
     * @param $cargoId
     *
     * @return stdClass
     */
    public static function getCargoDetail($cargoId)
    {
        global $lang;
        $response = sendResponse(404, '', null);


        $sql = "select cargo_id, cargo_name_fa_IR , cargo_name_en_US , cargo_name_ru_RU , cargo_name_tr_Tr, cargo_car_count, cargo_weight, cargo_volume, cargo_recommended_price, 
            currency_name, category_name, category_color, category_icon, category_image, cargo_origin_id, 
            cargo_origin_customs_id, cargo_destination_id, cargo_destination_customs_id, cargo_start_date, cargo_description_fa_IR,cargo_description_en_US , cargo_description_ru_RU , cargo_description_tr_Tr,
            cargo_images, cargo_rate, cargo_status, type_name, tbl_cargo.user_id, user_firstname, user_lastname, 
            user_mobile, user_language,cargo_green
        from tbl_cargo
        inner join tbl_users on tbl_cargo.user_id = tbl_users.user_id
        inner join tbl_cargo_categories on tbl_cargo.category_id = tbl_cargo_categories.category_id
        inner join tbl_car_types on tbl_cargo.type_id = tbl_car_types.type_id
        inner join tbl_currency on tbl_cargo.cargo_monetary_unit = tbl_currency.currency_id
        where tbl_cargo.cargo_id = :cargoId;";
        $params = [
            'cargoId' => $cargoId
        ];
        $result = DB::rawQuery($sql, $params);
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        if ($result->status == 200) {
            $cargo = new stdClass();
            foreach ($result->response as $item) {
                $cargo->CargoId = $item->cargo_id;
                $cargo->CargoName  = $item->$slugname;
                $cargo->CargoCarCount = $item->cargo_car_count;
                $cargo->CargoCarType = array_column(json_decode($item->type_name), 'value', 'slug')[$language];
                $cargo->CargoWeight = $item->cargo_weight;
                $cargo->CargoVolume = $item->cargo_volume;
                $cargo->CargoRecomendedPrice = $item->cargo_recommended_price;
                $cargo->CargoMonetaryUnit = array_column(json_decode($item->currency_name), 'value', 'slug')[$language];
                $cargo->CargoOrigin = $item->cargo_origin_id;
                $cargo->CargoOriginCityInfo = @Location::getCityById($item->cargo_origin_id)->response[0];
                $cargo->CargoOriginCity = array_column(json_decode($cargo->CargoOriginCityInfo->city_name), 'value', 'slug')[$language];
                $cargo->CargoCustomsOfOrigin = $item->cargo_origin_customs_id;
                $cargo->CargoCustomsOfOriginCity = (!empty($item->cargo_origin_customs_id)) ? array_column(json_decode(Ground::getCustomsInfoById($item->cargo_origin_customs_id)->response[0]->customs_name), 'value', 'slug')[$language] : $lang['other'];
                $cargo->CargoDestination = $item->cargo_destination_id;
                $cargo->CargoDestinationCityInfo = @Location::getCityById($item->cargo_destination_id)->response[0];
                $cargo->CargoDestinationCity = array_column(json_decode($cargo->CargoDestinationCityInfo->city_name), 'value', 'slug')[$language];
                $cargo->CargoDestinationCustoms = $item->cargo_destination_customs_id;
                $cargo->CargoDestinationCustomsCity = (!empty($item->cargo_destination_customs_id)) ? array_column(json_decode(Ground::getCustomsInfoById($item->cargo_destination_customs_id)->response[0]->customs_name), 'value', 'slug')[$language] : $lang['other'];
                $cargo->CargoStartTransportation = $item->cargo_start_date;
                $cargo->CargoDescription_fa_IR = $item->cargo_description_fa_IR;
                $cargo->CargoDescription_en_US = $item->cargo_description_en_US;
                $cargo->CargoDescription_ru_RU = $item->cargo_description_ru_RU;
                $cargo->CargoDescription_tr_Tr = $item->cargo_description_tr_Tr;
                $cargo->CargoImages = json_decode($item->cargo_images);
                $cargo->CargoRate = json_decode($item->cargo_rate);
                $cargo->CargoStatus = $item->cargo_status;
                $cargo->CargoGreen = $item->cargo_green;
                $cargo->CargoOriginCountry = Location::getCountryByCityId($cargo->CargoOrigin);
                $cargo->CargoDestinationCountry = Location::getCountryByCityId($cargo->CargoDestination);
                $cargo->CargoMinRequest = self::getCargoMinimumRequest($cargoId);
                $cargo->CategoryName = array_column(json_decode($item->category_name), 'value', 'slug')[$language];
                $cargo->CategoryIcon = $item->category_icon;
                $cargo->CategoryColor = $item->category_color;
                $cargo->CategoryImage = $item->category_image;
                $cargo->BusinessmanId = $item->user_id;
                $cargo->BusinessmanMobile = Security::decrypt($item->user_mobile);
                $cargo->BusinessmanFirstName = Security::decrypt($item->user_firstname);
                $cargo->BusinessmanLastName = Security::decrypt($item->user_lastname);
                $cargo->BusinessmanDisplayName = "{$cargo->BusinessmanFirstName} {$cargo->BusinessmanLastName}";
                $cargo->BusinessmanLanguage = $item->user_language;
            }
            $response = sendResponse(200, '', $cargo);
        }
        return $response;
    }


    public static function getCargoInDetail($cargoId)
    {
        global $lang;
        $response = sendResponse(404, '', null);

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }
        $sql = "select cargo_id, cargo_name_fa_IR , cargo_name_en_US , cargo_name_ru_RU ,cargo_name_tr_Tr, cargo_car_count, cargo_weight, cargo_volume, cargo_recommended_price, 
            currency_name, category_name, category_color, category_icon, category_image, cargo_origin_id, 
             cargo_destination_id, cargo_start_date, cargo_description_fa_IR ,cargo_description_en_US , cargo_description_ru_RU , cargo_description_tr_Tr,
            cargo_images, cargo_rate, cargo_status, type_name, tbl_cargo_in.user_id, user_firstname, user_lastname, 
            user_mobile, user_language
        from tbl_cargo_in
        inner join tbl_users on tbl_cargo_in.user_id = tbl_users.user_id
        inner join tbl_cargo_categories on tbl_cargo_in.category_id = tbl_cargo_categories.category_id
        inner join tbl_car_types on tbl_cargo_in.type_id = tbl_car_types.type_id
        inner join tbl_currency on tbl_cargo_in.cargo_monetary_unit = tbl_currency.currency_id
        where tbl_cargo_in.cargo_id = :cargoId;";
        $params = [
            'cargoId' => $cargoId
        ];
        $result = DB::rawQuery($sql, $params);
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname= 'cargo_name_' . $language;
        if ($result->status == 200) {
            $cargo = new stdClass();
            foreach ($result->response as $item) {
                $cargo->CargoId = $item->cargo_id;
                $cargo->CargoName  = $item->$slugname;
                $cargo->CargoCarCount = $item->cargo_car_count;
                $cargo->CargoCarType = array_column(json_decode($item->type_name), 'value', 'slug')[$language];
                $cargo->CargoWeight = $item->cargo_weight;
                $cargo->CargoVolume = $item->cargo_volume;
                $cargo->CargoRecomendedPrice = $item->cargo_recommended_price;
                $cargo->CargoMonetaryUnit = array_column(json_decode($item->currency_name), 'value', 'slug')[$language];
                $cargo->CargoOrigin = $item->cargo_origin_id;
                $cargo->CargoOriginCityInfo = Location::getCityById($item->cargo_origin_id)->response[0];
                $cargo->CargoOriginCity = array_column(json_decode($cargo->CargoOriginCityInfo->city_name), 'value', 'slug')[$language];
                $cargo->CargoDestination = $item->cargo_destination_id;
                $cargo->CargoDestinationCityInfo = Location::getCityById($item->cargo_destination_id)->response[0];
                $cargo->CargoDestinationCity = array_column(json_decode($cargo->CargoDestinationCityInfo->city_name), 'value', 'slug')[$language];
                $cargo->CargoStartTransportation = $item->cargo_start_date;
                $cargo->CargoDescription_fa_IR = $item->cargo_description_fa_IR;
                $cargo->CargoDescription_en_US = $item->cargo_description_en_US;
                $cargo->CargoDescription_ru_RU = $item->cargo_description_ru_RU;
                $cargo->CargoDescription_tr_Tr = $item->cargo_description_tr_Tr;
                $cargo->CargoImages = json_decode($item->cargo_images);
                $cargo->CargoRate = json_decode($item->cargo_rate);
                $cargo->CargoStatus = $item->cargo_status;
                $cargo->CargoOriginCountry = Location::getCountryByCityId($cargo->CargoOrigin);
                $cargo->CargoDestinationCountry = Location::getCountryByCityId($cargo->CargoDestination);
                $cargo->CargoMinRequest = self::getCargoMinimumRequest($cargoId);
                $cargo->CategoryName = array_column(json_decode($item->category_name), 'value', 'slug')[$language];
                $cargo->CategoryIcon = $item->category_icon;
                $cargo->CategoryColor = $item->category_color;
                $cargo->CategoryImage = $item->category_image;
                $cargo->BusinessmanId = $item->user_id;
                $cargo->BusinessmanMobile = Security::decrypt($item->user_mobile);
                $cargo->BusinessmanFirstName = Security::decrypt($item->user_firstname);
                $cargo->BusinessmanLastName = Security::decrypt($item->user_lastname);
                $cargo->BusinessmanDisplayName = "{$cargo->BusinessmanFirstName} {$cargo->BusinessmanLastName}";
                $cargo->BusinessmanLanguage = $item->user_language;
            }
            $response = sendResponse(200, '', $cargo);
        }
        return $response;
    }

    /**
     * @param $cargoId
     *
     * @return string|null
     */
    private static function getCargoMinimumRequest($cargoId)
    {
        $output = [];
        $sql = "select CAST(request_price AS UNSIGNED) as request_price
                from tbl_requests
                where cargo_id =:cargoId and (request_status = 'progress' or request_status = 'accepted'  or request_status = 'completed' or request_status = 'pending')
                order by request_price asc";
        $params = [
            'cargoId' => $cargoId
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $output[] = (isset($result->response[0]->request_price)) ? $result->response[0]->request_price : 0;
            $output[] = (isset($result->response[1]->request_price)) ? $result->response[1]->request_price : 0;
            $output[] = (isset($result->response[2]->request_price)) ? $result->response[2]->request_price : 0;
        }
        return $output;
    }


    /**
     * @param $cargoId
     * @param $userId
     *
     * @return stdClass|null
     */
    public static function getMyRequestForCargo($cargoId, $userId)
    {
        $response = null;

        $sql = "select * 
        from tbl_requests 
        where cargo_id = :cargoId and user_id = :userId and request_status not in (:status1, :status2);";
        $params = [
            'cargoId' => $cargoId,
            'userId' => $userId,
            'status1' => 'rejected',
            'status2' => 'canceled',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $request = new stdClass();
            foreach ($result->response as $item) {
                $request->RequestId = $item->request_id;
                $request->RequestPrice = $item->request_price;
                $request->RequestStartDate = $item->request_start_date;
                $request->RequestReceipt = $item->request_receipt;
                $request->RequestImages = json_decode($item->request_images);
                $request->RequestRate = $item->request_rate;
                $request->RequestStatus = $item->request_status;
            }
            $response = $request;
        }
        return $response;
    }

    public static function getMyRequestInForCargo($cargoId, $userId)
    {
        $response = null;

        $sql = "select * 
        from tbl_requests_in 
        where cargo_id = :cargoId and user_id = :userId and request_status not in (:status1, :status2);";
        $params = [
            'cargoId' => $cargoId,
            'userId' => $userId,
            'status1' => 'rejected',
            'status2' => 'canceled',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $request = new stdClass();
            foreach ($result->response as $item) {
                $request->RequestId = $item->request_id;
                $request->RequestPrice = $item->request_price;
                $request->RequestStartDate = $item->request_start_date;
                $request->RequestReceipt = $item->request_receipt;
                $request->RequestImages = json_decode($item->request_images);
                $request->RequestRate = $item->request_rate;
                $request->RequestStatus = $item->request_status;
            }
            $response = $request;
        }
        return $response;
    }


    /**
     * @return stdClass
     */
    public static function getCarTypes()
    {
        $response = sendResponse(200, '', []);

        $sql = "select *
        from tbl_car_types 
        where type_status = :status";
        $params = [
            'status' => 'active'
        ];
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';

        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $typeList = [];
            foreach ($result->response as $item) {
                $type = new stdClass();
                $type->TypeId = $item->type_id;
                $type->TypeName = array_column(json_decode($item->type_name), 'value', 'slug')[$language];
                $type->TypeImage = $item->type_icon;

                array_push($typeList, $type);
            }
            $response = sendResponse(200, '', $typeList);
        }
        return $response;
    }


    /**
     * @param $userId
     *
     * @return stdClass
     */
    public static function getMyCarsList($userId)
    {
        $response = sendResponse(200, '', []);
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';

        $sql = "select car_id,car_name,tbl_cars.type_id, type_name, plaque_type, car_plaque, car_images,type_icon,
            car_options, car_status
        from tbl_cars
        inner join tbl_car_types on tbl_cars.type_id = tbl_car_types.type_id
        where user_id = :userId and car_status != :status
        order by car_id desc;";
        $params = [
            'userId' => $userId,
            'status' => 'deleted'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $carsList = [];
            foreach ($result->response as $item) {
                $car = new stdClass();
                $car->CarId = $item->car_id;
                $car->TypeId = $item->type_id;
                $car->car_name = $item->car_name;
                $car->CarType = array_column(json_decode($item->type_name), 'value', 'slug')[$language];
                $car->CarPlaqueType = $item->plaque_type;
                $car->CarPlaque = $item->car_plaque;
                $car->CarImages = json_decode($item->car_images);
                $car->CarOptions = json_decode($item->car_options);
                $car->CarStatus = $item->car_status;
                $car->TypeImage = $item->type_icon;

                array_push($carsList, $car);
            }
            $response = sendResponse(200, '', $carsList);
        }
        return $response;
    }


    /**
     * @param       $userId
     * @param       $typeId
     * @param       $plaqueType
     * @param       $plaque
     * @param       $token
     * @param array $images
     *
     * @return stdClass
     */
    public static function newCar($userId, $typeId, $carName, $plaqueType, $plaque, $token, $images = [])
    {
        if (!Security::verifyCSRF2($token, false)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $sql = "insert into tbl_cars(user_id, type_id,car_name, plaque_type, car_plaque, car_images, car_status, car_submit_date)
            VALUES (:userId, :typeId,:carName, :plaqueType, :plaque, :images, :status, :time);";
        $params = [
            'userId' => $userId,
            'typeId' => $typeId,
            'carName' => $carName,
            'plaqueType' => $plaqueType,
            'plaque' => $plaque,
            'images' => json_encode($images),
            'status' => 'accepted',
            'time' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'New car submitted. Wait until the car is checked and approved');
            User::createUserLog($userId, 'uLog_new_car', 'car');
        } else {
            $response = sendResponse(-10, 'Submit error');
        }
        return $response;
    }


    /**
     * @param $carId
     * @param $userId
     *
     * @return stdClass
     */
    public static function getCarDetail($carId, $userId)
    {
        $response = sendResponse(404, '', null);
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';

        $sql = "select car_id, type_name, car_plaque, car_images, car_status,car_name
        from tbl_cars 
        inner join tbl_car_types on tbl_cars.type_id = tbl_car_types.type_id
        where car_id = :carId and user_id = :userId;";
        $params = [
            'carId' => $carId,
            'userId' => $userId
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $car = new stdClass();
            foreach ($result->response as $item) {
                $car->CarId = $item->car_id;
                $car->CarName = $item->car_name;
                $car->CarType = array_column(json_decode($item->type_name), 'value', 'slug')[$language];
                $car->CarPlaque = $item->car_plaque;
                $car->CarImages = json_decode($item->car_images);
                $car->CarStatus = $item->car_status;
            }
            $response = sendResponse(200, '', $car);
        }
        return $response;
    }


    /**
     * @param $carId
     * @param $userId
     * @param $token
     *
     * @return stdClass
     */
    public static function deleteCar($carId, $userId, $token)
    {
        if (!Security::verifyCSRF('delete-car', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('delete-car');

        $sql = "update tbl_cars set
        car_status = :status
        where car_id = :carId and user_id = :userId;";
        $params = [
            'status' => 'deleted',
            'carId' => $carId,
            'userId' => $userId
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'Car deleted successfully');
            User::createUserLog($userId, 'uLog_delete_car', 'car');
        } else {
            $response = sendResponse(-1, 'Delete error', $csrf);
        }
        return $response;
    }


    /**
     * @param        $userId
     * @param string $status
     * @param int    $page
     * @param int    $perPage
     *
     * @return stdClass
     */
    public static function getMyRequestsList($userId, $request_status, $search_value, $page)
    {

        $sql = 'select *  from ';
        $request_types = [];
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        if (in_array('in', $request_status)) {
            unset($request_status[array_search('in', $request_status)]);
            $request_types[] = 'in';
        }
        if (in_array('out', $request_status)) {
            unset($request_status[array_search('out', $request_status)]);
            $request_types[] = 'out';
        }

        if (in_array('all', $request_status)) {
            $sql .= " ((select tbl_requests.cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, cargo_start_date, request_status, request_price  , request_date , 'out' as request_type
            from tbl_requests
            inner join tbl_cargo on tbl_requests.cargo_id = tbl_cargo.cargo_id 
                   where tbl_requests.user_id = $userId)
                    ";
            if ($search_value != 'all-requests') {
                $sql .= " and $slugname like '%$search_value%'    ";
            }
        }
        else {
            if (in_array('out', $request_types)) {
                $sql .= " ((select tbl_requests.cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, cargo_start_date, request_status, request_price  , request_date , 'out' as request_type
            from tbl_requests
            inner join tbl_cargo on tbl_requests.cargo_id = tbl_cargo.cargo_id 
            where tbl_requests.user_id = $userId
                    ";
                if ($request_status) {
                    $sql .= ' and ( ';
                    foreach ($request_status as $item) {
                        $sql .= " tbl_requests.request_status = '$item' or ";
                    }
                    $sql = substr($sql, 0, -3);
                    $sql .= ' ) ';
                }

                if ($search_value != 'all-requests') {
                    $sql .= " and $slugname like '%$search_value%'    ";

                }
                $sql .= " ) ";
            } else {

                $sql .= " ((select tbl_requests.cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, cargo_start_date, request_status, request_price  , request_date , 'out' as request_type
            from tbl_requests
            inner join tbl_cargo on tbl_requests.cargo_id = tbl_cargo.cargo_id 
                where tbl_requests.user_id = $userId
                    ";
                if ($request_status) {
                    $sql .= ' and ( ';
                    foreach ($request_status as $index => $item) {
                        $sql .= " tbl_requests.request_status = '$item' or ";
                    }
                    $sql = substr($sql, 0, -3);
                    $sql .= ' ) ';
                }

                if ($search_value != 'all-requests') {
                    $sql .= " and $slugname like '%$search_value%'    ";

                }
                $sql .= " ) ";
            }
        }

        if (in_array('all', $request_status)) {
            $sql .= "  UNION ALL (select tbl_requests_in.cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, cargo_start_date, request_status, request_price , request_date, 'in' as request_type
            from tbl_requests_in
            inner join tbl_cargo_in on tbl_requests_in.cargo_id = tbl_cargo_in.cargo_id 
            where tbl_requests_in.user_id = $userId )
                    ";
            if ($search_value != 'all-requests') {
                $sql .= " and $slugname like '%$search_value%'    ";

            }
            $sql .= " ) ";
        }
        else {
            if (in_array('in', $request_types)) {

                $sql .= " UNION ALL  ( select tbl_requests_in.cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, cargo_start_date, request_status, request_price , request_date, 'in' as request_type
            from tbl_requests_in
            inner join tbl_cargo_in on tbl_requests_in.cargo_id = tbl_cargo_in.cargo_id 
            where tbl_requests_in.user_id = $userId
                    ";
                if ($request_status) {
                    $sql .= ' and ( ';
                    foreach ($request_status as $item) {
                        $sql .= " tbl_requests_in.request_status = '$item' or ";
                    }
                    $sql = substr($sql, 0, -3);
                    $sql .= ' ) ';
                }

                if ($search_value != 'all-requests') {
                    $sql .= " and $slugname like '%$search_value%'   ";
                }

                $sql .= ' ) ) ';
            } else {
                $sql .= " UNION ALL  ( select tbl_requests_in.cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, cargo_start_date, request_status, request_price , request_date, 'in' as request_type
            from tbl_requests_in
            inner join tbl_cargo_in on tbl_requests_in.cargo_id = tbl_cargo_in.cargo_id 
            where tbl_requests_in.user_id = $userId
                    ";
                if ($request_status) {
                    $sql .= ' and ( ';
                    foreach ($request_status as $item) {
                        $sql .= " tbl_requests_in.request_status = '$item' or ";
                    }
                    $sql = substr($sql, 0, -3);
                    $sql .= ' ) ';
                }

                if ($search_value != 'all-requests') {
                    $sql .= " and $slugname like '%$search_value%'   ";
                }
                $sql .= ' ) ) ';
            }

        }

        $sql .= " as temp_table ";
        if (in_array('in', $request_types) && in_array('out', $request_types)) {
            $sql .= " HAVING request_type ='in' or request_type ='out'  ";
        } else {
            if (in_array('in', $request_types)) {
                $sql .= " HAVING request_type ='in'  ";
            }
            if (in_array('out', $request_types)) {
                $sql .= " HAVING request_type ='out'  ";
            }
        }

        $sql .= " order by request_date  desc  limit 1000 ";
        file_put_contents('query.txt', $sql);
        $result = DB::rawQuery($sql, []);
        $request_list = [];
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        if ($result->status == 200) {

            foreach ($result->response as $item) {
                $request = new stdClass();
                $request->CargoId = $item->cargo_id;
                $request->CargoName = $item->$slugname;
                $request->CargoStartDate = $item->cargo_start_date;
                $request->RequestStatus = $item->request_status;
                $request->RequestPrice = $item->request_price;
//                $request->CurrencyName = $item->currency_name;
                $request->RequestDate = $item->request_date;
                $request->RequestType = $item->request_type;

                array_push($request_list, $request);
            }
            $response = sendResponse(200, $sql, $request_list);
        } else {
            $response = sendResponse(204, $sql);
        }
        return $response;
    }

    public static function getMyRequestsInList($userId, $status = 'all', $page = 1, $perPage = 10)
    {
        $response = sendResponse(200, '', []);
        $from = ($page == 1) ? 0 : ($page - 1) * $perPage;
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';

        if ($status == 'all') {
            $sql = "select tbl_requests_in.cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, cargo_start_date, request_status, request_price, currency_name
            from tbl_requests_in
            inner join tbl_cargo_in on tbl_requests_in.cargo_id = tbl_cargo_in.cargo_id 
            INNER JOIN tbl_currency  on tbl_cargo_in.cargo_monetary_unit=tbl_currency.currency_id
            where tbl_requests_in.user_id = :userId
            order by request_id desc
            limit {$from},{$perPage};";
            $params = [
                'userId' => $userId
            ];
        } else {
            $sql = "select tbl_requests_in.cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, cargo_start_date, request_status, request_price, currency_name
            from tbl_requests_in
            inner join tbl_cargo_in on tbl_requests_in.cargo_id = tbl_cargo_in.cargo_id
            INNER JOIN tbl_currency  on tbl_cargo_in.cargo_monetary_unit = tbl_currency.currency_id
            where tbl_requests_in.user_id = :userId and request_status = :status
            order by request_id desc
            limit {$from},{$perPage};";
            $params = [
                'userId' => $userId,
                'status' => $status
            ];
        }
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $requestsList = [];
            foreach ($result->response as $item) {
                $request = new stdClass();
                $request->CargoId = $item->cargo_id;
                $request->CargoName = $item->$slugname;
                $request->CargoStartDate = $item->cargo_start_date;
                $request->RequestPrice = $item->request_price;
                $request->CurrencyName = array_column(json_decode($item->currency_name), 'value', 'slug')[$language];
                $request->RequestStatus = $item->request_status;

                array_push($requestsList, $request);
            }
            $response = sendResponse(200, '', $requestsList);
        }
        return $response;
    }

    /**
     * @param $requestId
     * @param $userId
     *
     * @return stdClass
     */
    public static function getRequestDetail($requestId, $userId)
    {
        $response = sendResponse(404, '', null);

        $sql = "select request_id, tbl_requests.cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, tbl_cargo.user_id, tbl_requests.car_id,
        request_price, request_start_date, request_receipt, request_images, request_rate, request_status, request_date
        from tbl_requests
        inner join tbl_cargo on tbl_requests.cargo_id = tbl_cargo.cargo_id
        inner join tbl_users on tbl_cargo.user_id = tbl_users.user_id
        left join tbl_cars on tbl_requests.car_id = tbl_cars.car_id
        left join tbl_car_types on tbl_cars.type_id = tbl_car_types.type_id
        where request_id = :requestId and tbl_requests.user_id = :userId and request_status not in (:status1, :status2)
        order by request_id;";
        $params = [
            'requestId' => $requestId,
            'userId' => $userId,
            'status1' => 'rejected',
            'status2' => 'canceled',
        ];
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $request = new stdClass();
            foreach ($result->response as $item) {
                $request->RequestId = $item->request_id;
                $request->CargoId = $item->cargo_id;
                $request->CargoName = $item->$slugname;
                $request->BusinessmanId = $item->user_id;
                $request->CarId = $item->car_id;
                $request->RequestPrice = $item->request_price;
                $request->RequestStartDate = $item->request_start_date;
                $request->RequestReceipt = $item->request_receipt;
                $request->RequestImages = json_decode($item->request_images);
                $request->RequestRate = $item->request_rate;
                $request->RequestStatus = $item->request_status;
                $request->RequestDate = $item->request_date;
            }
            $response = sendResponse(200, '', $request);
        }
        return $response;
    }

    public static function getRequestInDetail($requestId, $userId)
    {
        $response = sendResponse(404, '', null);

        $sql = "select request_id, tbl_requests_in.cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, tbl_cargo_in.user_id, tbl_requests_in.car_id,
        request_price, request_start_date, request_receipt, request_images, request_rate, request_status, request_date
        from tbl_requests_in
        inner join tbl_cargo_in on tbl_requests_in.cargo_id = tbl_cargo_in.cargo_id
        inner join tbl_users on tbl_cargo_in.user_id = tbl_users.user_id
        left join tbl_cars on tbl_requests_in.car_id = tbl_cars.car_id
        left join tbl_car_types on tbl_cars.type_id = tbl_car_types.type_id
        where request_id = :requestId and tbl_requests_in.user_id = :userId and request_status not in (:status1, :status2)
        order by request_id;";
        $params = [
            'requestId' => $requestId,
            'userId' => $userId,
            'status1' => 'rejected',
            'status2' => 'canceled',
        ];
        $result = DB::rawQuery($sql, $params);$language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        if ($result->status == 200) {
            $request = new stdClass();
            foreach ($result->response as $item) {
                $request->RequestId = $item->request_id;
                $request->CargoId = $item->cargo_id;
                $request->CargoName = $item->$slugname;
                $request->BusinessmanId = $item->user_id;
                $request->CarId = $item->car_id;
                $request->RequestPrice = $item->request_price;
                $request->RequestStartDate = $item->request_start_date;
                $request->RequestReceipt = $item->request_receipt;
                $request->RequestImages = json_decode($item->request_images);
                $request->RequestRate = $item->request_rate;
                $request->RequestStatus = $item->request_status;
                $request->RequestDate = $item->request_date;
            }
            $response = sendResponse(200, '', $request);
        }
        return $response;
    }

    /**
     * @param $cargoId
     * @param $userId
     *
     * @return bool
     */
    public static function checkCanSendRequest($cargoId, $userId)
    {
        $response = false;

        if (!self::checkDriverHasProgressRequest($userId)) {
            $sql = "select count(*) as count
            from tbl_requests 
            where cargo_id = :cargoId and user_id = :userId and request_status not in (:status1, :status2)";
            $params = [
                'cargoId' => $cargoId,
                'userId' => $userId,
                'status1' => 'rejected',
                'status2' => 'canceled'
            ];
            $result = DB::rawQuery($sql, $params);
            if ($result->status == 200) {
                if ($result->response[0]->count == 0) {
                    $response = true;
                }
            }
        }
        return $response;
    }


    public static function checkCanSendRequestIn($cargoId, $userId)
    {
        $response = false;

        if (!self::checkDriverHasProgressRequestIn($userId)) {
            $sql = "select count(*) as count
            from tbl_requests_in 
            where cargo_id = :cargoId and user_id = :userId and request_status not in (:status1, :status2)";
            $params = [
                'cargoId' => $cargoId,
                'userId' => $userId,
                'status1' => 'rejected',
                'status2' => 'canceled'
            ];
            $result = DB::rawQuery($sql, $params);
            if ($result->status == 200) {
                if ($result->response[0]->count == 0) {
                    $response = true;
                }
            }
        }
        return $response;
    }

    /**
     * @param $userId
     *
     * @return bool
     */
    private static function checkDriverHasProgressRequest($userId)
    {
        $sql = "select count(*) as count
        from tbl_requests
        where user_id = :userId and request_status = :status;";
        $params = [
            'userId' => $userId,
            'status' => 'progress'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            if ($result->response[0]->count > 0) {
                return true;
            }
        }
        return false;
    }


    private static function checkDriverHasProgressRequestIn($userId)
    {
        $sql = "select count(*) as count
        from tbl_requests_in
        where user_id = :userId and request_status = :status;";
        $params = [
            'userId' => $userId,
            'status' => 'progress'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            if ($result->response[0]->count > 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $userId
     * @param $cargoId
     * @param $carId
     * @param $price
     * @param $token
     *
     * @return stdClass
     */
    public static function sendRequest($userId, $cargoId, $carId, $price, $token)
    {
        if (!Security::verifyCSRF('request', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        global $lang;
        $csrf = Security::initCSRF('request');
        $cargo = self::getCargoDetail($cargoId)->response;

        $sql = "insert into tbl_requests(user_id, cargo_id, car_id, request_price, request_status, request_date)
            VALUES (:userId, :cargoId, :carId, :price, :status, :time);";
        $params = [
            'userId' => $userId,
            'cargoId' => $cargoId,
            'carId' => $carId,
            'price' => $price,
            'status' => 'pending',
            'time' => time()
        ];
        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, 'Request sent successfully');
            User::createUserLog($userId, 'uLog_send_request', 'request');
//            $message = str_replace('#CARGO#', $cargo->CargoName, 'nLog_send_request_text--#CARGO#--#ID#');
//            $message = str_replace('#ID#', $cargoId, $message);
//            Notification::sendNotification($cargo->BusinessmanId, 'nLog_send_request_title', 'system', $message);
            Notification::sendNotification(
                $cargo->BusinessmanId,
                'nLog_send_request_title', 'system', 'nLog_send_request_text',
                'https://ntirapp.com/businessman/cargo-detail/'.$cargoId , 'unread' , true
            );
        } else {
            $response = sendResponse(-10, '', $csrf);
        }
        return $response;
    }

    public static function sendRequestIn($userId, $cargoId, $carId, $price, $token)
    {
        if (!Security::verifyCSRF('request', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('request');
        $cargo = self::getCargoInDetail($cargoId)->response;

        $sql = "insert into tbl_requests_in (user_id, cargo_id, car_id, request_price, request_status, request_date)
            VALUES (:userId, :cargoId, :carId, :price, :status, :time);";
        $params = [
            'userId' => $userId,
            'cargoId' => $cargoId,
            'carId' => $carId,
            'price' => $price,
            'status' => 'pending',
            'time' => time()
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Request sent successfully');
            User::createUserLog($userId, 'uLog_send_request_in', 'request');

//            $message = str_replace('#CARGO#', $cargo->CargoName, 'nLog_send_request_in_text--#CARGO#--#ID#');
//            $message = str_replace('#ID#', $cargoId, $message);
//            Notification::sendNotification($cargo->BusinessmanId, 'nLog_send_request_in_title', 'system', $message);

            Notification::sendNotification(
                $cargo->BusinessmanId,
                'nLog_send_request_in_title', 'system', 'nLog_send_request_in_text',
                'https://ntirapp.com/businessman/cargo-in-detail/'.$cargoId , 'unread' , true
            );
        } else {
            $response = sendResponse(-10, '', $csrf);
        }
        return $response;
    }


    /**
     * @param       $requestId
     * @param       $cargoId
     * @param       $userId
     * @param       $token
     * @param array $images
     *
     * @return stdClass
     */
    public static function startTransportation($requestId, $cargoId, $userId, $token, $images = [])
    {
        if (!Security::verifyCSRF('start-transportation', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        global $lang;
        $csrf = Security::initCSRF('start-transportation');
        $cargo = self::getCargoDetail($cargoId)->response;
        $user = User::getUserInfo($userId);

        $sql = "update tbl_requests set
        request_start_date = :time, request_status = :status, request_images = :images
        where request_id = :requestId and user_id = :userId;";
        $params = [
            'requestId' => $requestId,
            'userId' => $userId,
            'time' => time(),
            'status' => 'progress',
            'images' => json_encode($images),
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'Cargo transportation is being start');
            self::checkIsFirstTransportation($cargoId);
            User::createUserLog($userId, 'uLog_start_transportation', 'request');
//            $message = str_replace('#DRIVER#', $user->UserDisplayName, 'nLog_start_transportation_text--#DRIVER#--#CARGO#--#ID#');
//            $message = str_replace('#CARGO#', $cargo->CargoName, $message);
//            $message = str_replace('#ID#', $cargoId, $message);
//            Notification::sendNotification($cargo->BusinessmanId, 'nLog_start_transportation_title', 'system', $message);

            Notification::sendNotification(
                $cargo->BusinessmanId,
                'nLog_start_transportation_title', 'system', 'nLog_start_transportation_text',
                'https://ntirapp.com/businessman/cargo-detail/'.$cargoId , 'unread' , true
            );
        } else {
            $response = sendResponse(-10, '', $csrf);
        }
        return $response;
    }

    public static function startTransportationIn($requestId, $cargoId, $userId, $token, $images = [])
    {
        if (!Security::verifyCSRF('start-transportation-in', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        global $lang;
        $csrf = Security::initCSRF('start-transportation-in');
        $cargo = self::getCargoInDetail($cargoId)->response;
        $user = User::getUserInfo($userId);

        $sql = "update tbl_requests_in set
        request_start_date = :time, request_status = :status, request_images = :images
        where request_id = :requestId and user_id = :userId;";
        $params = [
            'requestId' => $requestId,
            'userId' => $userId,
            'time' => time(),
            'status' => 'progress',
            'images' => json_encode($images),
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'Cargo transportation is being start');
            self::checkIsFirstTransportationIn($cargoId);
            User::createUserLog($userId, 'uLog_start_transportation_in', 'request');
//            $message = str_replace('#DRIVER#', $user->UserDisplayName, 'nLog_start_transportation_in_text--#DRIVER#--#CARGO#--#ID#');
//            $message = str_replace('#CARGO#', $cargo->CargoName, $message);
//            $message = str_replace('#ID#', $cargoId, $message);
//            Notification::sendNotification($cargo->BusinessmanId, 'nLog_start_transportation_in_title', 'system', $message);

            Notification::sendNotification(
                $cargo->BusinessmanId,
                'nLog_start_transportation_in_title', 'system', 'nLog_start_transportation_in_text',
                'https://ntirapp.com/businessman/cargo-in-detail/'.$cargoId , 'unread' , true
            );
        } else {
            $response = sendResponse(-10, '', $csrf);
        }
        return $response;
    }

    /**
     * @param $cargoId
     *
     * @return bool
     */
    private static function checkIsFirstTransportation($cargoId)
    {
        $response = false;

        $sql = "select count(*) as count
        from tbl_requests 
        where cargo_id = :cargoId and request_status in (:status1, :status2);";
        $params = [
            'cargoId' => $cargoId,
            'status1' => 'progress',
            'status2' => 'completed'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            if ($result->response[0]->count == 1) {
                $sql = "update tbl_cargo set
                cargo_status = :status
                where cargo_id = :cargoId;";
                $params = [
                    'cargoId' => $cargoId,
                    'status' => 'progress'
                ];
                $result = DB::update($sql, $params);
                if ($result->status == 200 || $result->status == 208) {
                    $response = true;
                }
            }
        }
        return $response;
    }

    private static function checkIsFirstTransportationIn($cargoId)
    {
        $response = false;

        $sql = "select count(*) as count
        from tbl_requests_in 
        where cargo_id = :cargoId and request_status in (:status1, :status2);";
        $params = [
            'cargoId' => $cargoId,
            'status1' => 'progress',
            'status2' => 'completed'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            if ($result->response[0]->count == 1) {
                $sql = "update tbl_cargo set
                cargo_status = :status
                where cargo_id = :cargoId;";
                $params = [
                    'cargoId' => $cargoId,
                    'status' => 'progress'
                ];
                $result = DB::update($sql, $params);
                if ($result->status == 200 || $result->status == 208) {
                    $response = true;
                }
            }
        }
        return $response;
    }

    /**
     * @param $userId
     * @param $requestId
     * @param $cargoId
     * @param $title
     * @param $amount
     * @param $unit
     * @param $token
     *
     * @return stdClass
     */
    public static function requestExtraExpenses($userId, $requestId, $cargoId, $title, $amount, $unit, $token)
    {
        if (!Security::verifyCSRF('extra-expenses', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        global $lang;
        $csrf = Security::initCSRF('extra-expenses');
        $cargo = self::getCargoDetail($cargoId)->response;
        $user = User::getUserInfo($userId);

        $sql = "insert into tbl_extra_expenses(cargo_id, request_id, expense_name, expense_price, expense_monetary_unit, expense_status, expense_date) 
            VALUES (:cargoId, :requestId, :title, :amount, :unit, :status, :time);";
        $params = [
            'cargoId' => $cargoId,
            'requestId' => $requestId,
            'title' => $title,
            'amount' => $amount,
            'unit' => $unit,
            'status' => 'pending',
            'time' => time()
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Extra expenses request submitted', $csrf);
            User::createUserLog($userId, 'uLog_request_extra_expenses', 'request');
//            $message = str_replace('#DRIVER#', $user->UserDisplayName, 'nLog_request_extra_expenses_text--#DRIVER#--#CARGO#--#ID#');
//            $message = str_replace('#CARGO#', $cargo->CargoName, $message);
//            $message = str_replace('#ID#', $cargoId, $message);
//            Notification::sendNotification($cargo->BusinessmanId, 'nLog_request_extra_expenses_title', 'system', $message);

            Notification::sendNotification(
                $cargo->BusinessmanId,
                'nLog_request_extra_expenses_title', 'system', 'nLog_request_extra_expenses_text',
                'https://ntirapp.com/businessman/cargo-detail/'.$cargoId , 'unread' , true
            );
        } else {
            $response = sendResponse(-10, 'Submit error', $csrf);
        }
        return $response;
    }

    public static function requestExtraExpensesIn($userId, $requestId, $cargoId, $title, $amount, $unit, $token)
    {
        if (!Security::verifyCSRF('extra-expenses-in', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        global $lang;
        $csrf = Security::initCSRF('extra-expenses-in');
        $cargo = self::getCargoInDetail($cargoId)->response;
        $user = User::getUserInfo($userId);

        $sql = "insert into tbl_extra_expenses_in(cargo_id, request_id, expense_name, expense_price, expense_monetary_unit, expense_status, expense_date) 
            VALUES (:cargoId, :requestId, :title, :amount, :unit, :status, :time);";
        $params = [
            'cargoId' => $cargoId,
            'requestId' => $requestId,
            'title' => $title,
            'amount' => $amount,
            'unit' => $unit,
            'status' => 'pending',
            'time' => time()
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Extra expenses request submitted', $csrf);
            User::createUserLog($userId, 'uLog_request_extra_expenses_in', 'request');
//            $message = str_replace('#DRIVER#', $user->UserDisplayName, 'nLog_request_extra_expenses_in_text--#DRIVER#--#CARGO#--#ID#');
//            $message = str_replace('#CARGO#', $cargo->CargoName, $message);
//            $message = str_replace('#ID#', $cargoId, $message);
//            Notification::sendNotification($cargo->BusinessmanId, 'nLog_request_extra_expenses_in_title', 'system', $message);
            Notification::sendNotification(
                $cargo->BusinessmanId,
                'nLog_request_extra_expenses_in_title', 'system', 'nLog_request_extra_expenses_in_text',
                'https://ntirapp.com/businessman/cargo-in-detail/'.$cargoId , 'unread' , true
            );
        } else {
            $response = sendResponse(-10, 'Submit error', $csrf);
        }
        return $response;
    }

    /**
     * @return stdClass
     */
    public static function getCurrencyList()
    {
        $response = sendResponse(200, '', []);
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';

        $sql = "select *
        from tbl_currency 
        where currency_status = :status;";
        $params = [
            'status' => 'active'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $currenciesList = [];
            foreach ($result->response as $item) {
                $currency = new stdClass();
                $currency->CurrencyId = $item->currency_id;
                $currency->CurrencyName = array_column(json_decode($item->currency_name), 'value', 'slug')[$language];

                array_push($currenciesList, $currency);
            }
            $response = sendResponse(200, '', $currenciesList);
        }
        return $response;
    }


    /**
     * @param $requestId
     *
     * @return stdClass
     */
    public static function getExtraExpensesList($requestId)
    {
        $response = sendResponse(200, '', []);
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';

        $sql = "select expense_id, expense_name, expense_price, currency_name, expense_status 
        from tbl_extra_expenses
        inner join tbl_currency on tbl_extra_expenses.expense_monetary_unit = tbl_currency.currency_id
        where request_id = :requestId
        order by expense_price desc;";
        $params = [
            'requestId' => $requestId
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $expensesList = [];
            foreach ($result->response as $item) {
                $expense = new stdClass();
                $expense->ExpenseId = $item->expense_id;
                $expense->ExpenseName = $item->expense_name;
                $expense->ExpenseAmount = $item->expense_price;
                $expense->ExpenseCurrency = array_column(json_decode($item->currency_name), 'value', 'slug')[$language];
                $expense->ExpenseStatus = $item->expense_status;
                $expense->ExpenseDisplayStatus = $item->expense_id;

                array_push($expensesList, $expense);
            }
            $response = sendResponse(200, '', $expensesList);
        }
        return $response;
    }


    public static function getExtraExpensesInList($requestId)
    {
        $response = sendResponse(200, '', []);
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';

        $sql = "select expense_id, expense_name, expense_price, currency_name, expense_status 
        from tbl_extra_expenses_in
        inner join tbl_currency on tbl_extra_expenses_in.expense_monetary_unit = tbl_currency.currency_id
        where request_id = :requestId
        order by expense_price desc;";
        $params = [
            'requestId' => $requestId
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $expensesList = [];
            foreach ($result->response as $item) {
                $expense = new stdClass();
                $expense->ExpenseId = $item->expense_id;
                $expense->ExpenseName = $item->expense_name;
                $expense->ExpenseAmount = $item->expense_price;
                $expense->ExpenseCurrency = array_column(json_decode($item->currency_name), 'value', 'slug')[$language];
                $expense->ExpenseStatus = $item->expense_status;
                $expense->ExpenseDisplayStatus = $item->expense_id;

                array_push($expensesList, $expense);
            }
            $response = sendResponse(200, '', $expensesList);
        }
        return $response;
    }


    /**
     * @param       $userId
     * @param       $requestId
     * @param       $cargoId
     * @param       $token
     * @param null  $receipt
     * @param array $images
     * @param int   $rate
     *
     * @return stdClass
     */
    public static function endTransportation($userId, $requestId, $cargoId, $token, $receipt = null, $images = [], $rate = 0)
    {
        if (!Security::verifyCSRF('end-transportation', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        global $lang;
        $csrf = Security::initCSRF('end-transportation');
        $request = self::getRequestDetail($requestId, $userId)->response;
        $cargo = self::getCargoDetail($cargoId)->response;
        $user = User::getUserInfo($userId);

        $images = (!empty($images)) ? array_merge($request->RequestImages, $images) : $request->RequestImages;

        $sql = "update tbl_requests set
        request_receipt = :receipt, request_images = :images, request_status = :status
        where request_id = :requestId and user_id = :userId and cargo_id = :cargoId;";
        $params = [
            'receipt' => $receipt,
            'images' => json_encode($images),
            'status' => 'completed',
            'requestId' => $requestId,
            'userId' => $userId,
            'cargoId' => $cargoId
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $cargo->CargoRate[] = $rate;
            $sql = "update tbl_cargo set
            cargo_rate = :rate 
            where cargo_id = :cargoId;";
            $params = [
                'cargoId' => $cargoId,
                'rate' => json_encode($cargo->CargoRate)
            ];
            $result = DB::update($sql, $params);
            if ($result->status == 200 || $result->status == 208) {
                $response = sendResponse(200, 'Cargo transportation ended');
            } else {
                $response = sendResponse(200, 'Cargo transportation ended. But rating not submitted');
            }
            self::checkIsLastTransportation($cargoId);
            self::rateToBusinessman($cargo->BusinessmanId, $rate);
            User::createUserLog($userId, 'uLog_end_transportation', 'request');
//            $message = str_replace('#DRIVER#', $user->UserDisplayName, 'nLog_end_transportation_text--#DRIVER#--#CARGO#--#ID#');
//            $message = str_replace('#CARGO#', $cargo->CargoName, $message);
//            $message = str_replace('#ID#', $cargoId, $message);
//            Notification::sendNotification($cargo->BusinessmanId, 'nLog_end_transportation_title', 'system', $message);

            Notification::sendNotification(
                $cargo->BusinessmanId,
                'nLog_end_transportation_title', 'system', 'nLog_end_transportation_text',
                'https://ntirapp.com/businessman/cargo-detail/'.$cargoId , 'unread' , true
            );
        } else {
            $response = sendResponse(-10, '', $csrf);
        }
        return $response;
    }


    public static function endTransportationIn($userId, $requestId, $cargoId, $token, $receipt = null, $images = [], $rate = 0)
    {
        if (!Security::verifyCSRF('end-transportation-in', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        global $lang;
        $csrf = Security::initCSRF('end-transportation-in');
        $request = self::getRequestInDetail($requestId, $userId)->response;
        $cargo = self::getCargoInDetail($cargoId)->response;
        $user = User::getUserInfo($userId);

        $images = (!empty($images)) ? array_merge($request->RequestImages, $images) : $request->RequestImages;

        $sql = "update tbl_requests_in set
        request_receipt = :receipt, request_images = :images, request_status = :status
        where request_id = :requestId and user_id = :userId and cargo_id = :cargoId;";
        $params = [
            'receipt' => $receipt,
            'images' => json_encode($images),
            'status' => 'completed',
            'requestId' => $requestId,
            'userId' => $userId,
            'cargoId' => $cargoId
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $cargo->CargoRate[] = $rate;
            $sql = "update tbl_cargo_in set
            cargo_rate = :rate 
            where cargo_id = :cargoId;";
            $params = [
                'cargoId' => $cargoId,
                'rate' => json_encode($cargo->CargoRate)
            ];
            $result = DB::update($sql, $params);
            if ($result->status == 200 || $result->status == 208) {
                $response = sendResponse(200, 'Cargo transportation ended');
            } else {
                $response = sendResponse(200, 'Cargo transportation ended. But rating not submitted');
            }
            self::checkIsLastTransportationIn($cargoId);
            self::rateToBusinessman($cargo->BusinessmanId, $rate);
            User::createUserLog($userId, 'uLog_end_transportation_in', 'request');
//            $message = str_replace('#DRIVER#', $user->UserDisplayName, 'nLog_end_transportation_in_text--#DRIVER#--#CARGO#--#ID#');
//            $message = str_replace('#CARGO#', $cargoId, $message);
//            $message = str_replace('#ID#', $cargoId, $message);
//            Notification::sendNotification($cargo->BusinessmanId, 'nLog_end_transportation_in_title', 'system', $message);
            Notification::sendNotification(
                $cargo->BusinessmanId,
                'nLog_end_transportation_in_title', 'system', 'nLog_end_transportation_in_text',
                'https://ntirapp.com/businessman/cargo-in-detail/'.$cargoId , 'unread' , true
            );
        } else {
            $response = sendResponse(-10, '', $csrf);
        }
        return $response;
    }


    /**
     * @param $cargoId
     *
     * @return bool
     */
    public static function checkIsLastTransportation($cargoId)
    {
        $response = false;

        $sql = "select count(*) as completed, tbl_cargo.cargo_car_count
        from tbl_requests
        inner join tbl_cargo on tbl_requests.cargo_id = tbl_cargo.cargo_id
        where tbl_requests.cargo_id = :cargoId and tbl_requests.request_status = :status";
        $params = [
            'cargoId' => $cargoId,
            'status' => 'completed'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            if ($result->response[0]->completed == $result->response[0]->cargo_car_count) {
                $sql = "update tbl_cargo set
                cargo_status = :status
                where cargo_id = :cargoId;";
                $params = [
                    'cargoId' => $cargoId,
                    'status' => 'completed'
                ];
                $result = DB::update($sql, $params);
                if ($result->status == 200 || $result->status == 208) {
                    $response = true;
                }
            }
        }
        return $response;
    }

    public static function checkIsLastTransportationIn($cargoId)
    {
        $response = false;

        $sql = "select count(*) as completed, tbl_cargo_in.cargo_car_count
        from tbl_requests_in
        inner join tbl_cargo_in on tbl_requests_in.cargo_id = tbl_cargo_in.cargo_id
        where tbl_requests_in.cargo_id = :cargoId and tbl_requests_in.request_status = :status";
        $params = [
            'cargoId' => $cargoId,
            'status' => 'completed'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            if ($result->response[0]->completed == $result->response[0]->cargo_car_count) {
                $sql = "update tbl_cargo_in set
                cargo_status = :status
                where cargo_id = :cargoId;";
                $params = [
                    'cargoId' => $cargoId,
                    'status' => 'completed'
                ];
                $result = DB::update($sql, $params);
                if ($result->status == 200 || $result->status == 208) {
                    $response = true;
                }
            }
        }
        return $response;
    }

    /**
     * @param $businessmanId
     * @param $rate
     */
    private static function rateToBusinessman($businessmanId, $rate)
    {
        User::updateUserOptions($businessmanId, 'user_rate', $rate);
        User::updateUserOptions($businessmanId, 'user_rate_count', 1);
    }


    /**
     * @param $userId
     * @param $requestId
     * @param $reason
     * @param $token
     *
     * @return stdClass
     */
    public static function cancelRequest($userId, $requestId, $reason, $token)
    {
        if (!Security::verifyCSRF('cancel-request', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('cancel-request');

        $sql = "update tbl_requests set
        request_status = :status, request_cancel_text = :reason
        where user_id = :userId and request_id = :requestId";
        $params = [
            'userId' => $userId,
            'requestId' => $requestId,
            'reason' => $reason,
            'status' => 'canceled',
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'Request canceled successfully');
            User::createUserLog($userId, 'uLog_cancel_request', 'request');
            $cargo = self::getCargoInfoFromRequest($requestId);
            if ($cargo->status == 200) {
                $cargo = Driver::getCargoInfoFromRequest($requestId)->response;
            }
//            $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
//            $slugname = 'cargo_name_' . $language;
//            $message = str_replace('#PARAM1#', $cargo->$slugname, 'nLog_cancel_request_text--#PARAM1#--#PARAM2#');
//            $message = str_replace('#PARAM2#', $cargo->cargo_id, $message);
//            Notification::sendNotification($cargo->b_id, 'uLog_cancel_request', 'system', $message);
            Notification::sendNotification(
                $cargo->b_id,
                'uLog_cancel_request', 'system', 'nLog_cancel_request_text',
                'https://ntirapp.com/businessman/cargo-detail/'.$cargo->cargo_id , 'unread' , true
            );
        } else {
            $response = sendResponse(-10, 'Canceling error', $csrf);
        }
        return $response;
    }

    public static function cancelRequestIn($userId, $requestId, $reason, $token)
    {
        if (!Security::verifyCSRF('cancel-request-in', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('cancel-request-in');

        $sql = "update tbl_requests_in set
        request_status = :status, request_cancel_text = :reason
        where user_id = :userId and request_id = :requestId";
        $params = [
            'userId' => $userId,
            'requestId' => $requestId,
            'reason' => $reason,
            'status' => 'canceled',
        ];
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'Request canceled successfully');
            User::createUserLog($userId, 'uLog_cancel_request_in', 'request');
            $cargo = self::getCargoInfoFromRequestIn($requestId);
            if ($cargo->status == 200) {
                $cargo = Driver::getCargoInfoFromRequestIn($requestId)->response;
            }

//            $message = str_replace('#PARAM1#', $cargo->$slugname, 'nLog_cancel_request_in_text--#PARAM1#--#PARAM2#');
//            $message = str_replace('#PARAM2#', $cargo->cargo_id, $message);
//            Notification::sendNotification($cargo->b_id, 'uLog_cancel_request_in', 'system', $message);

            Notification::sendNotification(
                $cargo->b_id,
                'uLog_cancel_request_in', 'system', 'nLog_cancel_request_in_text',
                'https://ntirapp.com/businessman/cargo-in-detail/'.$cargo->cargo_id , 'unread' , true
            );
        } else {
            $response = sendResponse(-10, 'Canceling error', $csrf);
        }
        return $response;
    }

    /**
     * @param $userId
     * @param $latitude
     * @param $longitude
     */
    public static function updateLocation($userId, $latitude, $longitude)
    {
        $sql = "update tbl_users set
        user_lat = :lat, user_long = :long
        where user_id = :userId;";
        $params = [
            'userId' => $userId,
            'lat' => $latitude,
            'long' => $longitude
        ];
        $result = DB::update($sql, $params);

        $sql = "update tbl_requests set
        request_lat = :lat, request_long = :long
        where user_id = :userId and request_status = :status;";
        $params = [
            'userId' => $userId,
            'lat' => $latitude,
            'long' => $longitude,
            'status' => 'progress'
        ];
        $result = DB::update($sql, $params);
    }

    /**
     * @param $requestId
     *
     * @return stdClass
     */
    public static function getExtraExpensesListCountByStatus($requestId, $status = 'pending')
    {
        $response = sendResponse(200, '', []);

        $sql = "select COUNT(tbl_extra_expenses.expense_id)  as c
        from tbl_extra_expenses
        inner join tbl_currency on tbl_extra_expenses.expense_monetary_unit = tbl_currency.currency_id
        where request_id = :requestId and expense_status = :status 
        ";
        $params = [
            'requestId' => $requestId,
            'status' => $status,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $expensesList = [];
            foreach ($result->response as $item) {
                $expense = new stdClass();
                $expense->count = $item->c;


                array_push($expensesList, $expense);
            }
            $response = sendResponse(200, '', $expensesList);
        }
        return $response;
    }

    public static function getExtraExpensesInListCountByStatus($requestId, $status = 'pending')
    {
        $response = sendResponse(200, '', []);

        $sql = "select COUNT(tbl_extra_expenses_in.expense_id)  as c
        from tbl_extra_expenses_in
        inner join tbl_currency on tbl_extra_expenses_in.expense_monetary_unit = tbl_currency.currency_id
        where request_id = :requestId and expense_status = :status 
        ";
        $params = [
            'requestId' => $requestId,
            'status' => $status,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $expensesList = [];
            foreach ($result->response as $item) {
                $expense = new stdClass();
                $expense->count = $item->c;


                array_push($expensesList, $expense);
            }
            $response = sendResponse(200, '', $expensesList);
        }
        return $response;
    }

    public static function getCargoInfoFromRequest($request_id)
    {
        //SELECT tbl_cargo.user_id as b_id FROM `tbl_requests` inner join tbl_cargo on tbl_requests.cargo_id =  tbl_cargo.cargo_id  where tbl_requests.request_id = 5;
        $response = sendResponse(200, '', []);

        $sql = "SELECT tbl_cargo.user_id as b_id ,  tbl_cargo.cargo_id , cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU  FROM `tbl_requests` inner join tbl_cargo on tbl_requests.cargo_id =  tbl_cargo.cargo_id  where tbl_requests.request_id = :request_id;";
        $params = [
            'request_id' => $request_id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {

            $response = sendResponse(200, '', $result->response[0]);
        }
        return $response;
    }

    public static function getCargoInfoFromRequestIn($request_id)
    {
        //SELECT tbl_cargo.user_id as b_id FROM `tbl_requests` inner join tbl_cargo on tbl_requests.cargo_id =  tbl_cargo.cargo_id  where tbl_requests.request_id = 5;
        $response = sendResponse(200, '', []);

        $sql = "SELECT tbl_cargo_in.user_id as b_id ,  tbl_cargo_in.cargo_id , cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU  FROM `tbl_requests_in` inner join tbl_cargo_in on tbl_requests_in.cargo_id =  tbl_cargo_in.cargo_id  where tbl_requests_in.request_id = :request_id;";
        $params = [
            'request_id' => $request_id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {

            $response = sendResponse(200, '', $result->response[0]);
        }
        return $response;
    }

    public static function getCountryByCities($city_id)
    {
        $response = sendResponse(200, '', []);
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';

        $sql = "select country_name from tbl_cities inner join tbl_country on tbl_cities.country_id = tbl_country.country_id WHERE tbl_cities.city_id = :city_id";
        $params = [
            'city_id' => $city_id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {

            $response = sendResponse(200, '', array_column(json_decode($result->response[0]->country_name), 'value', 'slug')[$language]);

        }
        return $response;
    }

    /**
     * @param $userId
     * @param $status
     *
     * @return int
     */
    public static function getRequestCountByStatus($userId, $status = 'all')
    {
        $response = 0;
        if ($status == 'all') {
            $sql = "select count(*) as count
        from tbl_requests
        where user_id = :userId";
            $params = [
                'userId' => $userId,
            ];
        } else {
            $sql = "select count(*) as count
        from tbl_requests
        where user_id = :userId and request_status = :status;";
            $params = [
                'userId' => $userId,
                'status' => $status
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->count;
        }
        return $response;
    }
    /**
     * @param $userId
     * @param $status
     *
     * @return int
     */
    public static function getRequestInCountByStatus($userId, $status = 'all')
    {
        $response = 0;
        if ($status == 'all') {
            $sql = "select count(*) as count
        from tbl_requests_in 
        where user_id = :userId";
            $params = [
                'userId' => $userId,
            ];
        } else {
            $sql = "select count(*) as count
        from tbl_requests_in 
        where user_id = :userId and request_status = :status;";
            $params = [
                'userId' => $userId,
                'status' => $status
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->count;
        }
        return $response;
    }

    /**
     * @param $userId
     * @param $status
     *
     * @return int
     */
    public static function getMyCarsCount($userId, $status = 'accepted')
    {
        $response = 0;

        $sql = "select count(*) as count
        from tbl_cars
        where user_id = :userId and car_status = :status;";
        $params = [
            'userId' => $userId,
            'status' => 'accepted'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->count;
        }
        return $response;
    }


    public static function getMyCargoProgress($userId)
    {
        $response = sendResponse(0, '', []);

        $sql = "SELECT * from (
            (SELECT cargo_id,'out' as Xtype
FROM tbl_requests  WHERE user_id = :userId AND  request_status IN ('progress','accepted') )
UNION ALL
    (SELECT cargo_id, 'in' as Xtype
FROM tbl_requests_in WHERE user_id =:userId  AND  request_status IN ('progress','accepted'))
)  AS i";
        $params = [
            'userId' => $userId,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response =sendResponse(200, '', $result->response) ;
        }
        return $response;
    }


    public static function getDriverRequestsRate($user_id)
    {
        $sql = "select *  from tbl_requests where tbl_requests.user_id = :user_id";
        $params = [
            'user_id' => $user_id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $sum = 0;
            $counter = 1;
            foreach ($result->response as $item) {
                if (isset($item->request_rate)) {
                    $sum += $item->request_rate;
                    $counter++;
                }
            }
            return $sum / $counter;
        } elseif ($result->status == 204) {
            return 0;
        }
        return 0;
    }

    public static function getDriverRequestsInRate($user_id)
    {
        $sql = "select *  from tbl_requests_in  where tbl_requests_in.user_id = :user_id";
        $params = [
            'user_id' => $user_id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $sum = 0;
            $counter = 1;
            foreach ($result->response as $item) {
                if (isset($item->request_rate)) {
                    $sum += $item->request_rate;
                    $counter++;
                }
            }
            return $sum / $counter;
        } elseif ($result->status == 204) {
            return 0;
        }
        return 0;
    }
}