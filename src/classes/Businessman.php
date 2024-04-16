<?php


use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Businessman
{
    /**
     * @return stdClass
     */
    public static function getCargoTypes()
    {
        $response = sendResponse(200, '', []);

        $categories = Cargo::getAllCargoCategory('active');
        if ($categories->status == 200) {
            $categoriesList = [];
            foreach ($categories->response as $item) {
                $category = new stdClass();
                $category->CategoryId = $item->category_id;
                $category->CategoryName = array_column(json_decode($item->category_name), 'value', 'slug')[$_COOKIE['language']];
                $category->CategoryColor = $item->category_color;
                $category->CategoryIcon = $item->category_icon;
                $category->CategoryImage = $item->category_image;
                $category->CategoryStatus = $item->category_status;

                array_push($categoriesList, $category);
            }
            $response = sendResponse(200, '', $categoriesList);
        }
        return $response;
    }


    /**
     * @param      $countryId
     * @param      $type
     * @param null $status
     *
     * @return stdClass
     */
    public static function getCities($countryId, $type, $status = null)
    {
        $response = sendResponse(200, '', []);

        if ($type == 'customs') {
            $cities = Ground::getCustomsByCountry($countryId);
        } else {
            $cities = Location::getCitiesByCountry($countryId, $status);
        }
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
            $response = sendResponse(200, '', $citiesList);
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $status
     *
     * @return int
     */
    public static function getCargoCountByStatus($userId, $status)
    {
        $response = 0;

        $sql = "select count(*) as count
        from tbl_cargo
        where user_id = :userId and cargo_status = :status;";
        $params = [
            'userId' => $userId,
            'status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->count;
        }
        return $response;
    }


    public static function getCargoOutCount($userId)
    {
        $response = 0;
        $sql = "select count(*) as count
        from tbl_cargo
        where user_id = :userId";
        $params = [
            'userId' => $userId,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->count;
        }
        return $response;
    }

    public static function getCargoInCount($userId)
    {
        $response = 0;
        $sql = "select count(*) as count
        from tbl_cargo_in
        where user_id = :userId";
        $params = [
            'userId' => $userId,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->count;
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $originId
     * @param $destinationId
     * @param $categoryId
     * @param $currencyId
     * @param $names
     *
     * @return stdClass
     */
    public static function freightPriceInquiry($userId, $originId, $destinationId, $categoryId, $currencyId, $names)
    {
        $response = sendResponse(404, '', null);

        $sql = "select request_price, cargo_origin_id, cargo_destination_id
        from tbl_requests 
        inner join tbl_cargo on tbl_requests.cargo_id = tbl_cargo.cargo_id
        where cargo_monetary_unit = :currencyId and category_id = :categoryId and request_status = :status
        order by request_id desc;";
        $params = [
            'currencyId' => $currencyId,
            'categoryId' => $categoryId,
            'status' => 'completed',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $avg = 0;
            $total = 0;
            $count = 0;
            foreach ($result->response as $item) {
                $origin = $item->cargo_origin_id;
                $destination = $item->cargo_destination_id;

                if ($origin == $originId && $destination == $destinationId) {
                    $total += $item->request_price;
                    $count++;
                }
            }

            if ($count > 0) {
                $avg = ceil($total / $count);
                $response = sendResponse(200, '', $avg);
            } else {
                global $lang, $Settings;
                $message = str_replace('#FROM#', $names[0], $lang['ticket_freight_price_message']);
                $message = str_replace('#TO#', $names[1], $message);
                $message = str_replace('#TYPE#', $names[2], $message);
                $message = str_replace('#CURRENCY#', $names[3], $message);
                // Ticket::createTicket($userId, $Settings['department_id_inquiry_price'], $Settings['subject_inquiry_price'], $message, null, [], true);
            }
            User::createUserLog($userId, 'uLog_freight_price', 'freight_price');
        }
        return $response;
    }


    /**
     * @param        $userId
     * @param string $status
     * @param int $page
     * @param int $perPage
     *
     * @return stdClass
     */
    public static function getCargoList($userId, $cargo_status, $search_value, $page)
    {
        $sql = 'select *  from ';
        $cargo_types = [];

        if (in_array('in', $cargo_status)) {
            unset($cargo_status[array_search('in', $cargo_status)]);
            $cargo_types[] = 'in';
        }
        if (in_array('out', $cargo_status)) {
            unset($cargo_status[array_search('out', $cargo_status)]);
            $cargo_types[] = 'out';
        }
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        if (in_array('all', $cargo_status)) {
            $sql .= " ((select cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, cargo_start_date,cargo_date, cargo_status , 'out' as cargo_type from tbl_cargo
                   where user_id = $userId)
                    ";
            if ($search_value != 'all-cargoes') {
                $sql .= " and $slugname like '%$search_value%'    ";
            }
        } else {
            if (in_array('out', $cargo_types)) {
                $sql .= " ((select cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, cargo_start_date,cargo_date, cargo_status , 'out' as cargo_type from tbl_cargo
                   where user_id = $userId 
                    ";
                if ($cargo_status) {
                    $sql .= ' and ( ';
                    foreach ($cargo_status as $item) {
                        $sql .= " tbl_cargo.cargo_status = '$item' or ";
                    }
                    $sql = substr($sql, 0, -3);
                    $sql .= ' ) ';
                }

                if ($search_value != 'all-cargoes') {
                    $sql .= " and $slugname like '%$search_value%'    ";

                }
                $sql .= " ) ";
            } else {

                $sql .= " ((select cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, cargo_start_date,cargo_date, cargo_status , 'out' as cargo_type from tbl_cargo
                   where user_id = $userId  
                    ";
                if ($cargo_status) {
                    $sql .= ' and ( ';
                    foreach ($cargo_status as $index => $item) {
                        $sql .= " tbl_cargo.cargo_status = '$item' or ";
                    }
                    $sql = substr($sql, 0, -3);
                    $sql .= ' ) ';
                }

                if ($search_value != 'all-cargoes') {
                    $sql .= " and $slugname like '%$search_value%'    ";

                }
                $sql .= " ) ";
            }
        }

        if (in_array('all', $cargo_status)) {
            $sql .= "  UNION ALL (select cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, cargo_start_date,cargo_date, cargo_status , 'in' as cargo_type from tbl_cargo_in
                   where user_id = $userId )
                    ";
            if ($search_value != 'all-cargoes') {
                $sql .= " and $slugname like '%$search_value%'    ";
            }
            $sql .= " ) ";
        } else {
            if (in_array('in', $cargo_types)) {

                $sql .= " UNION ALL  ( select cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, cargo_start_date,cargo_date, cargo_status , 'in' as cargo_type from tbl_cargo_in
                   where user_id = $userId   
                    ";
                if ($cargo_status) {
                    $sql .= ' and ( ';
                    foreach ($cargo_status as $index => $item) {
                        $sql .= " tbl_cargo_in.cargo_status = '$item' or ";
                    }
                    $sql = substr($sql, 0, -3);
                    $sql .= ' ) ';
                }

                if ($search_value != 'all-cargoes') {
                    $sql .= " and $slugname like '%$search_value%'   ";
                }

                $sql .= ' ) ) ';
            } else {
                $sql .= " UNION ALL  ( select cargo_id, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, cargo_start_date,cargo_date, cargo_status , 'in' as cargo_type from tbl_cargo_in
                   where user_id = $userId 
                    ";
                if ($cargo_status) {
                    $sql .= ' and ( ';
                    foreach ($cargo_status as $index => $item) {
                        $sql .= " tbl_cargo_in.cargo_status = '$item' or ";
                    }
                    $sql = substr($sql, 0, -3);
                    $sql .= ' ) ';
                }

                if ($search_value != 'all-cargoes') {
                    $sql .= " and $slugname like '%$search_value%'   ";
                }
                $sql .= ' ) ) ';
            }

        }

        $sql .= " as temp_table ";
        if (in_array('in', $cargo_types) && in_array('out', $cargo_types)) {
            $sql .= " HAVING cargo_type ='in' or cargo_type ='out'  ";
        } else {
            if (in_array('in', $cargo_types)) {
                $sql .= " HAVING cargo_type ='in'  ";
            }
            if (in_array('out', $cargo_types)) {
                $sql .= " HAVING cargo_type ='out'  ";
            }
        }

        $sql .= " order by cargo_date  desc  limit 1000 ";
        $result = DB::rawQuery($sql, []);
        $cargoList = [];
        if ($result->status == 200) {
            foreach ($result->response as $item) {
                $cargo = new stdClass();
                $cargo->CargoId = $item->cargo_id;
                $cargo->CargoName = $item->$slugname;
                $cargo->CargoStartDate = $item->cargo_start_date;
                $cargo->CargoStatus = $item->cargo_status;
                $cargo->CargoType = $item->cargo_type;

                array_push($cargoList, $cargo);
            }
            $response = sendResponse(200, $sql, $cargoList);
        } else {
            $response = sendResponse(204, $sql);
        }
        file_put_contents('query.txt', $sql);
        return $response;
    }


    /**
     * @param $userId
     * @param $cargoId
     *
     * @return stdClass
     */
    public static function getCargoDetail($userId, $cargoId)
    {
        $response = sendResponse(404, '', null);
        global $lang;
        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }
        $sql = "select cargo_id, category_name, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, type_name, cargo_car_count, cargo_weight,
        cargo_volume, cargo_recommended_price, currency_name, cargo_origin_id, cargo_origin_customs_id,
        cargo_destination_id, cargo_destination_customs_id, cargo_start_date, cargo_description_fa_IR,cargo_description_tr_Tr,cargo_description_en_US,cargo_description_ru_RU, cargo_images,
        cargo_cancel_desc, cargo_status, category_icon, category_image, category_color,cargo_green
        from tbl_cargo
        inner join tbl_cargo_categories on tbl_cargo.category_id = tbl_cargo_categories.category_id
        inner join tbl_car_types on tbl_cargo.type_id = tbl_car_types.type_id
        inner join tbl_currency on tbl_cargo.cargo_monetary_unit = tbl_currency.currency_id
        where user_id = :userId and cargo_id = :cargoId ;";
        $params = [
            'userId' => $userId,
            'cargoId' => $cargoId
        ];
        $result = DB::rawQuery($sql, $params);
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        $slugname_desc = 'cargo_description_' . $language;
        if ($result->status == 200) {
            $cargo = new stdClass();
            foreach ($result->response as $item) {
                $cargo->CargoId = $item->cargo_id;
                $cargo->CargoName = $item->$slugname;
                $cargo->CargoCarCount = $item->cargo_car_count;
                $cargo->CargoCarType = array_column(json_decode($item->type_name), 'value', 'slug')[$language];
                $cargo->CargoWeight = $item->cargo_weight;
                $cargo->CargoVolume = $item->cargo_volume;
                $cargo->CargoRecommendedPrice = $item->cargo_recommended_price;
                $cargo->CargoMonetaryUnit = array_column(json_decode($item->currency_name), 'value', 'slug')[$language];
                $cargo->CargoOrigin = $item->cargo_origin_id;
                $cargo->CargoOriginCity = (!empty($item->cargo_origin_id)) ? array_column(json_decode(Location::getCityById($item->cargo_origin_id)->response[0]->city_name), 'value', 'slug')[$language] : $lang['b_cargo_other_city'];
                $cargo->CargoOriginCityInfo = Location::getCityById($item->cargo_origin_id)->response[0];
                $cargo->CargoCustomsOfOrigin = $item->cargo_origin_customs_id;
                $cargo->CargoCustomsOfOriginCity = (!empty($item->cargo_origin_customs_id)) ? array_column(json_decode(Ground::getCustomsInfoById($item->cargo_origin_customs_id)->response[0]->customs_name), 'value', 'slug')[$language] : $lang['b_cargo_other_city'];
                $cargo->CargoDestination = $item->cargo_destination_id;
                $cargo->CargoDestinationCity = (!empty($item->cargo_destination_id)) ? array_column(json_decode(Location::getCityById($item->cargo_destination_id)->response[0]->city_name), 'value', 'slug')[$language] : $lang['b_cargo_other_city'];
                $cargo->CargoDestinationCityInfo = Location::getCityById($item->cargo_destination_id)->response[0];
                $cargo->CargoDestinationCustoms = json_decode($item->cargo_destination_customs_id);
                $cargo->CargoDestinationCustomsCity = (!empty($item->cargo_destination_customs_id)) ? array_column(json_decode(Ground::getCustomsInfoById($item->cargo_destination_customs_id)->response[0]->customs_name), 'value', 'slug')[$language] : $lang['b_cargo_other_city'];
                $cargo->CargoOriginCountry = Location::getCountryByCityId($cargo->CargoOrigin);
                $cargo->CargoDestinationCountry = Location::getCountryByCityId($cargo->CargoDestination);
                $cargo->CargoGreen = $item->cargo_green;
                $cargo->CargoStartTransportation = $item->cargo_start_date;
                $cargo->CargoDescription = $item->$slugname_desc;
                $cargo->CargoImages = json_decode($item->cargo_images);
                $cargo->CargoStatus = $item->cargo_status;
                $cargo->CategoryName = array_column(json_decode($item->category_name), 'value', 'slug')[$language];
                $cargo->CategoryIcon = $item->category_icon;
                $cargo->CategoryColor = $item->category_color;
                $cargo->CategoryImage = $item->category_image;
            }
            $response = sendResponse(200, '', $cargo);
        }
        return $response;
    }


    /**
     * @param        $userId
     * @param        $cargoId
     * @param string $sort
     *
     * @return stdClass
     */
    public static function getCargoRequests($userId, $cargoId, $sort = 'asc')
    {
        $response = sendResponse(404, '', null);

        $sql = "select request_id, tbl_requests.user_id, type_name, request_price
        from tbl_requests
        inner join tbl_cargo on tbl_requests.cargo_id = tbl_cargo.cargo_id
        left join tbl_cars on tbl_requests.car_id = tbl_cars.car_id
        left join tbl_car_types on tbl_cars.type_id = tbl_car_types.type_id
        where tbl_cargo.user_id = :userId and tbl_requests.cargo_id = :cargoId and request_status = :status
        order by request_price {$sort}";
        $params = [
            'userId' => $userId,
            'cargoId' => $cargoId,
            'status' => 'pending'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $bidsList = [];
            foreach ($result->response as $item) {
                $bid = new stdClass();
                $bid->RequestId = $item->request_id;
                $bid->DriverId = $item->user_id;
                $bid->RequestPrice = $item->request_price;
                $bid->CarType = isset($item->type_name) ? array_column(json_decode($item->type_name), 'value', 'slug')[$_COOKIE['language']] : "ـــــ";

                array_push($bidsList, $bid);
            }
            $response = sendResponse(200, '', $bidsList);
        } elseif ($result->status == 204) {
            $response = sendResponse(200, '', []);
        }
        return $response;
    }

    public static function getCargoInRequests($userId, $cargoId, $sort = 'asc')
    {
        $response = sendResponse(404, '', null);

        $sql = "select request_id, tbl_requests_in.user_id, type_name, request_price
        from tbl_requests_in
        inner join tbl_cargo_in on tbl_requests_in.cargo_id = tbl_cargo_in.cargo_id
        left join tbl_cars on tbl_requests_in.car_id = tbl_cars.car_id
        left join tbl_car_types on tbl_cars.type_id = tbl_car_types.type_id
        where tbl_cargo_in.user_id = :userId and tbl_requests_in.cargo_id = :cargoId and request_status = :status
        order by request_price {$sort}";
        $params = [
            'userId' => $userId,
            'cargoId' => $cargoId,
            'status' => 'pending'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $bidsList = [];
            foreach ($result->response as $item) {
                $bid = new stdClass();
                $bid->RequestId = $item->request_id;
                $bid->DriverId = $item->user_id;
                $bid->RequestPrice = $item->request_price;
                $bid->CarType = isset($item->type_name) ? array_column(json_decode($item->type_name), 'value', 'slug')[$_COOKIE['language']] : "ـــــ";

                array_push($bidsList, $bid);
            }
            $response = sendResponse(200, '', $bidsList);
        } elseif ($result->status == 204) {
            $response = sendResponse(200, '', []);
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $cargoId
     *
     * @return stdClass
     */
    public static function getCargoDrivers($userId, $cargoId)
    {
        $response = sendResponse(404, '', null);
        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        global $lang;
        $sql = "select tbl_users.user_id, user_firstname, user_lastname, user_mobile, user_lat, user_long,
        user_language, request_id, request_receipt, request_images, request_status, request_rate, request_lat, request_long,tbl_requests.request_price , tbl_cargo.cargo_monetary_unit,currency_name
        from tbl_requests
        inner join tbl_users on tbl_requests.user_id = tbl_users.user_id
        inner join tbl_cargo on tbl_requests.cargo_id = tbl_cargo.cargo_id
        inner join tbl_currency on tbl_currency.currency_id = tbl_cargo.cargo_monetary_unit
        where tbl_cargo.user_id = :userId and tbl_requests.cargo_id = :cargoId and request_status not in (:status1, :status2, :status3)";
        $params = [
            'userId' => $userId,
            'cargoId' => $cargoId,
            'status1' => 'pending',
            'status2' => 'canceled',
            'status3' => 'rejected',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $driversList = [];
            foreach ($result->response as $item) {
                $driver = new stdClass();
                $driver->UserId = $item->user_id;
                $driver->UserFirstName = Security::decrypt($item->user_firstname);
                $driver->UserLastName = Security::decrypt($item->user_lastname);
                $driver->UserDisplayName = (!empty($driver->UserFirstName) && !empty($driver->UserLastName)) ? "{$driver->UserFirstName} {$driver->UserLastName}" : $lang['guest_user'];
                $driver->UserMobile = Security::decrypt($item->user_mobile);
                $driver->UserLatitude = $item->user_lat;
                $driver->UserLongitude = $item->user_long;
                $driver->UserLanguage = $item->user_language;
                $driver->RequestId = $item->request_id;
                $driver->RequestReceipt = $item->request_receipt;
                $driver->RequestImages = json_decode($item->request_images);
                $driver->RequestRate = $item->request_rate;
                $driver->RequestPrice = $item->request_price;
                $driver->PriceUnit = $item->cargo_monetary_unit;
                $driver->CurrencyName = array_column(json_decode($item->currency_name), 'value', 'slug')[$language];
                $driver->RequestLatitude = $item->request_lat;
                $driver->RequestLongitude = $item->request_long;
                $driver->RequestStatus = $item->request_status;

                array_push($driversList, $driver);
            }
            $response = sendResponse(200, '', $driversList);
        } elseif ($result->status == 204) {
            $response = sendResponse(200, '', []);
        }
        return $response;
    }

    public static function getCargoInDetail($userId, $cargoId)
    {
        $response = sendResponse(404, '', null);
        global $lang;
        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }
        $sql = "select cargo_id, category_name, cargo_name_fa_IR,cargo_name_en_US,cargo_name_tr_Tr,cargo_name_ru_RU, type_name, cargo_car_count, cargo_weight,
        cargo_volume, cargo_recommended_price, currency_name, cargo_origin_id,
        cargo_destination_id, cargo_start_date, cargo_description_fa_IR , cargo_name_en_US , cargo_description_ru_RU , cargo_name_tr_Tr , cargo_images,
        cargo_cancel_desc, cargo_status, category_icon, category_image, category_color
        from tbl_cargo_in
        inner join tbl_cargo_categories on tbl_cargo_in.category_id = tbl_cargo_categories.category_id
        inner join tbl_car_types on tbl_cargo_in.type_id = tbl_car_types.type_id
        inner join tbl_currency on tbl_cargo_in.cargo_monetary_unit = tbl_currency.currency_id
        where user_id = :userId and cargo_id = :cargoId;";
        $params = [
            'userId' => $userId,
            'cargoId' => $cargoId
        ];
        $result = DB::rawQuery($sql, $params);
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        $slugname_desc = 'cargo_description_' . $language;
        if ($result->status == 200) {
            $cargo = new stdClass();
            foreach ($result->response as $item) {
                $cargo->CargoId = $item->cargo_id;
                $cargo->CargoName = $item->$slugname;
                $cargo->CargoCarCount = $item->cargo_car_count;
                $cargo->CargoCarType = array_column(json_decode($item->type_name), 'value', 'slug')[$language];
                $cargo->CargoWeight = $item->cargo_weight;
                $cargo->CargoVolume = $item->cargo_volume;
                $cargo->CargoRecommendedPrice = $item->cargo_recommended_price;
                $cargo->CargoMonetaryUnit = array_column(json_decode($item->currency_name), 'value', 'slug')[$language];
                $cargo->CargoOrigin = $item->cargo_origin_id;
                $cargo->CargoOriginCity = (!empty($item->cargo_origin_id)) ? array_column(json_decode(Location::getCityById($item->cargo_origin_id)->response[0]->city_name), 'value', 'slug')[$language] : $lang['b_cargo_other_city'];
                $cargo->CargoOriginCityInfo = Location::getCityById($item->cargo_origin_id)->response[0];
                $cargo->CargoDestination = $item->cargo_destination_id;
                $cargo->CargoDestinationCity = (!empty($item->cargo_destination_id)) ? array_column(json_decode(Location::getCityById($item->cargo_destination_id)->response[0]->city_name), 'value', 'slug')[$language] : $lang['b_cargo_other_city'];
                $cargo->CargoDestinationCityInfo = Location::getCityById($item->cargo_destination_id)->response[0];
                $cargo->CargoOriginCountry = Location::getCountryByCityId($cargo->CargoOrigin);
                $cargo->CargoStartTransportation = $item->cargo_start_date;
                $cargo->CargoDescription = $item->$slugname_desc;
                $cargo->CargoImages = json_decode($item->cargo_images);
                $cargo->CargoStatus = $item->cargo_status;
                $cargo->CategoryName = array_column(json_decode($item->category_name), 'value', 'slug')[$language];
                $cargo->CategoryIcon = $item->category_icon;
                $cargo->CategoryColor = $item->category_color;
                $cargo->CategoryImage = $item->category_image;
            }
            $response = sendResponse(200, '', $cargo);
        }
        return $response;
    }


    public static function getCargoInDrivers($userId, $cargoId)
    {
        $response = sendResponse(404, '', null);
        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }
        global $lang;
        $sql = "select tbl_users.user_id, user_firstname, user_lastname, user_mobile, user_lat, user_long,
        user_language, request_id, request_receipt, request_images, request_status, request_rate, request_lat, request_long,tbl_requests_in.request_price , tbl_cargo_in.cargo_monetary_unit,currency_name
        from tbl_requests_in
        inner join tbl_users on tbl_requests_in.user_id = tbl_users.user_id
        inner join tbl_cargo_in on tbl_requests_in.cargo_id = tbl_cargo_in.cargo_id
        inner join tbl_currency on tbl_currency.currency_id = tbl_cargo_in.cargo_monetary_unit
        where tbl_cargo_in.user_id = :userId and tbl_requests_in.cargo_id = :cargoId and request_status not in (:status1, :status2, :status3)";
        $params = [
            'userId' => $userId,
            'cargoId' => $cargoId,
            'status1' => 'pending',
            'status2' => 'canceled',
            'status3' => 'rejected',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $driversList = [];
            foreach ($result->response as $item) {
                $driver = new stdClass();
                $driver->UserId = $item->user_id;
                $driver->UserFirstName = Security::decrypt($item->user_firstname);
                $driver->UserLastName = Security::decrypt($item->user_lastname);
                $driver->UserDisplayName = (!empty($driver->UserFirstName) && !empty($driver->UserLastName)) ? "{$driver->UserFirstName} {$driver->UserLastName}" : $lang['guest_user'];
                $driver->UserMobile = Security::decrypt($item->user_mobile);
                $driver->UserLatitude = $item->user_lat;
                $driver->UserLongitude = $item->user_long;
                $driver->UserLanguage = $item->user_language;
                $driver->RequestId = $item->request_id;
                $driver->RequestReceipt = $item->request_receipt;
                $driver->RequestImages = json_decode($item->request_images);
                $driver->RequestRate = $item->request_rate;
                $driver->RequestPrice = $item->request_price;
                $driver->PriceUnit = $item->cargo_monetary_unit;
                $driver->CurrencyName = array_column(json_decode($item->currency_name), 'value', 'slug')[$language];
                $driver->RequestLatitude = $item->request_lat;
                $driver->RequestLongitude = $item->request_long;
                $driver->RequestStatus = $item->request_status;

                array_push($driversList, $driver);
            }
            $response = sendResponse(200, '', $driversList);
        } elseif ($result->status == 204) {
            $response = sendResponse(200, '', []);
        }
        return $response;
    }


    /**
     * @param $requestId
     *
     * @return stdClass
     */
    public static function getRequestExtraExpenses($requestId)
    {
        $response = sendResponse(200, '', []);

        $sql = "select expense_id, expense_name, expense_price, expense_status, currency_name 
        from tbl_extra_expenses 
        inner join tbl_currency on tbl_extra_expenses.expense_monetary_unit = tbl_currency.currency_id
        where request_id = :requestId;";
        $params = [
            'requestId' => $requestId
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $expenseList = [];
            foreach ($result->response as $item) {
                $expense = new stdClass();
                $expense->ExpenseId = $item->expense_id;
                $expense->ExpenseName = $item->expense_name;
                $expense->ExpensePrice = $item->expense_price;
                $expense->CurrencyName = array_column(json_decode($item->currency_name), 'value', 'slug')[$_COOKIE['language']];
                $expense->ExpenseStatus = $item->expense_status;

                array_push($expenseList, $expense);
            }
            $response = sendResponse(200, '', $expenseList);
        }
        return $response;
    }

    public static function getRequestExtraExpensesIn($requestId)
    {
        $response = sendResponse(200, '', []);

        $sql = "select expense_id, expense_name, expense_price, expense_status, currency_name 
        from tbl_extra_expenses_in 
        inner join tbl_currency on tbl_extra_expenses_in.expense_monetary_unit = tbl_currency.currency_id
        where request_id = :requestId;";
        $params = [
            'requestId' => $requestId
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $expenseList = [];
            foreach ($result->response as $item) {
                $expense = new stdClass();
                $expense->ExpenseId = $item->expense_id;
                $expense->ExpenseName = $item->expense_name;
                $expense->ExpensePrice = $item->expense_price;
                $expense->CurrencyName = array_column(json_decode($item->currency_name), 'value', 'slug')[$_COOKIE['language']];
                $expense->ExpenseStatus = $item->expense_status;

                array_push($expenseList, $expense);
            }
            $response = sendResponse(200, '', $expenseList);
        }
        return $response;
    }

    /**
     * @param $userId
     * @param $cargoId
     * @param $requestId
     * @param $driverId
     * @param $status
     * @param $token
     *
     * @return stdClass
     */
    public static function changeRequestStatus($userId, $cargoId, $requestId, $driverId, $status, $token)
    {
        $response = sendResponse(200, '', []);
        if (!Security::verifyCSRF('request-change', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('request-change');
        $cargo = self::getCargoDetail($userId, $cargoId)->response;

        $acceptPermission = DB::rawQuery("SELECT tbl_cargo.cargo_car_count , count(*) as request_accepted_count FROM `tbl_cargo` 
                            inner join tbl_requests ON tbl_cargo.cargo_id = tbl_requests.cargo_id where tbl_cargo.cargo_id = :cargo_id 
                            AND tbl_requests.request_status = :request_status;", [
            'cargo_id' => $cargoId,
            'request_status' => 'accepted'
        ]);

        if ($acceptPermission->status == 200) {
            $acceptPermission = $acceptPermission->response[0];
//            print_r($acceptPermission);
            if(!$acceptPermission->cargo_car_count) {
                $acceptPermission->cargo_car_count =1;
            }
            if ($acceptPermission->cargo_car_count >= $acceptPermission->request_accepted_count) {
                if ($status == 'accepted') {

                    $tx_query = "UPDATE `tbl_requests` SET request_status = :new_status WHERE user_id = :user_id AND request_status = :request_status ;
                UPDATE tbl_requests SET request_status = 'accepted'  where request_id = :request_id;";
                    $tx_params = [
                        "new_status" => 'canceled',
                        "user_id" => $driverId,
                        "request_status" => 'pending',
                        "request_id" => $requestId,
                    ];
                    $tx_result = DB::transactionQuery($tx_query, $tx_params);
                    if ($tx_result->status == 200) {
//                        $message = str_replace('#CARGO#', $cargo->CargoName, 'nLog_accept_request_text--#CARGO#--#ID#');
//                        $message = str_replace('#ID#', $cargoId, $message);

//                        Notification::sendNotification($driverId, 'nLog_accept_request_title', 'system', $message);
                        Notification::sendNotification(
                            $driverId,
                            'nLog_accept_request_title', 'system', 'nLog_accept_request_text',
                            'https://ntirapp.com/driver/cargo/' . $cargoId, 'unread', true
                        );


                        User::createUserLog($userId, 'uLog_accept_request', 'request');
                        $response = sendResponse(200, 'Request status changed successfully');
                    } else {
                        $response = sendResponse(-10, 'Error', $csrf);
                    }
                } else {
                    $sql = "update tbl_requests set
        request_status = :status
        where request_id = :requestId and cargo_id = :cargoId;";
                    $params = [
                        'requestId' => $requestId,
                        'cargoId' => $cargoId,
                        'status' => $status
                    ];
                    $result = DB::update($sql, $params);

                    if ($result->status == 200 || $result->status == 208) {
                        $response = sendResponse(200, 'Request status changed successfully');
                        if ($status == 'accepted') {
                            Notification::sendNotification(
                                $driverId,
                                'nLog_accept_request_title', 'system', 'nLog_accept_request_text',
                                'https://ntirapp.com/driver/cargo/' . $cargoId, 'unread', true
                            );
                            User::createUserLog($userId, 'uLog_accept_request', 'request');
                        } elseif ($status == 'rejected') {
                            Notification::sendNotification(
                                $driverId,
                                'nLog_reject_request_title', 'system', 'nLog_reject_request_text',
                                'https://ntirapp.com/driver/cargo/' . $cargoId, 'unread', true
                            );
                            User::createUserLog($userId, 'uLog_reject_request', 'request');
                        }
                    } else {
                        $response = sendResponse(-10, 'Error', $csrf);
                    }
                }

            } else {
                $response = sendResponse(-30, 'count', $csrf);
            }
        }
        return $response;
    }


    public static function changeRequestInStatus($userId, $cargoId, $requestId, $driverId, $status, $token)
    {
        $response = sendResponse(200, '', []);
        if (!Security::verifyCSRF('request-in-change', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('request-in-change');
        $cargo = self::getCargoInDetail($userId, $cargoId)->response;

        $acceptPermission = DB::rawQuery("SELECT tbl_cargo_in.cargo_car_count , count(*) as request_accepted_count FROM `tbl_cargo_in` 
                            inner join tbl_requests_in ON tbl_cargo_in.cargo_id = tbl_requests_in.cargo_id where tbl_cargo_in.cargo_id = :cargo_id 
                            AND tbl_requests_in.request_status = :request_status;", [
            'cargo_id' => $cargoId,
            'request_status' => 'accepted'
        ]);

        if ($acceptPermission->status == 200) {
            $acceptPermission = $acceptPermission->response[0];
//            print_r($acceptPermission);
            if(!$acceptPermission->cargo_car_count) {
                $acceptPermission->cargo_car_count =1;
            }

            if ($acceptPermission->cargo_car_count > $acceptPermission->request_accepted_count) {
                if ($status == 'accepted') {

                    $tx_query = "UPDATE `tbl_requests_in` SET request_status = :new_status WHERE user_id = :user_id AND request_status = :request_status ;
                UPDATE tbl_requests_in SET request_status = 'accepted'  where request_id = :request_id;";
                    $tx_params = [
                        "new_status" => 'canceled',
                        "user_id" => $driverId,
                        "request_status" => 'pending',
                        "request_id" => $requestId,
                    ];
                    $tx_result = DB::transactionQuery($tx_query, $tx_params);
                    if ($tx_result->status == 200) {
//                        $message = str_replace('#CARGO#', $cargo->CargoName, 'nLog_accept_request_in_text--#CARGO#--#ID#');
//                        $message = str_replace('#ID#', $cargoId, $message);
//
//                        Notification::sendNotification($driverId, 'nLog_accept_request_in_title', 'system', $message);
                        Notification::sendNotification(
                            $driverId,
                            'nLog_accept_request_in_title', 'system', 'nLog_accept_request_in_text',
                            'https://ntirapp.com/driver/cargo-in/' . $cargoId, 'unread', true
                        );
                        User::createUserLog($userId, 'uLog_accept_request_in', 'request');
                        $response = sendResponse(200, 'Request status changed successfully');
                    } else {
                        $response = sendResponse(-10, 'Error', $csrf);
                    }
                } else {
                    $sql = "update tbl_requests_in set  request_status = :status
                            where request_id = :requestId and cargo_id = :cargoId;";
                    $params = [
                        'requestId' => $requestId,
                        'cargoId' => $cargoId,
                        'status' => $status
                    ];
                    $result = DB::update($sql, $params);

                    if ($result->status == 200 || $result->status == 208) {
                        $response = sendResponse(200, 'Request status changed successfully');
                        if ($status == 'accepted') {
//                            $message = str_replace('#CARGO#', $cargo->CargoName, 'nLog_accept_request_in_text--#CARGO#--#ID#');
//                            $message = str_replace('#ID#', $cargoId, $message);
//
//                            Notification::sendNotification($driverId, 'nLog_accept_request_in_text', 'system', $message);

                            Notification::sendNotification(
                                $driverId,
                                'nLog_accept_request_in_title', 'system', 'nLog_accept_request_in_text',
                                'https://ntirapp.com/driver/cargo-in/' . $cargoId, 'unread', true
                            );
                            User::createUserLog($userId, 'uLog_accept_request_in', 'request');

                        } elseif ($status == 'rejected') {
//                            $message = str_replace('#CARGO#', $cargo->CargoName, 'nLog_reject_request_in_text--#CARGO#--#ID#');
//                            $message = str_replace('#ID#', $cargoId, $message);
//                            Notification::sendNotification($driverId, 'nLog_reject_request_in_title', 'system', $message);

                            Notification::sendNotification(
                                $driverId,
                                'nLog_reject_request_in_title', 'system', 'nLog_reject_request_in_text',
                                'https://ntirapp.com/driver/cargo-in/' . $cargoId, 'unread', true
                            );

                            User::createUserLog($userId, 'uLog_reject_request_in', 'request');
                        }
                    } else {
                        $response = sendResponse(-10, 'Error', $csrf);
                    }
                }

            } else {
                $response = sendResponse(-30, 'count', $csrf);
            }
        }
        return $response;
    }

    /**
     * @param $userId
     * @param $expenseId
     * @param $requestId
     * @param $cargoId
     * @param $driverId
     * @param $status
     * @param $token
     *
     * @return stdClass
     */
    public static function changeExpenseStatus($userId, $expenseId, $requestId, $cargoId, $driverId, $status, $token)
    {
        if (!Security::verifyCSRF('expenses', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('expenses');
        $cargo = self::getCargoDetail($userId, $cargoId)->response;

        $sql = "update tbl_extra_expenses set
        expense_status = :status 
        where expense_id = :expenseId and request_id = :requestId and cargo_id = :cargoId;";
        $params = [
            'expenseId' => $expenseId,
            'requestId' => $requestId,
            'cargoId' => $cargoId,
            'status' => $status,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'Expense status changed successfully');
            if ($status == 'accepted') {
//                $message = str_replace('#CARGO#', $cargo->CargoName, 'nLog_accept_expense_text--#CARGO#--#ID#');
//                $message = str_replace('#ID#', $cargoId, $message);
//                Notification::sendNotification($driverId, 'nLog_accept_expense_title', 'system', $message);
                Notification::sendNotification(
                    $driverId,
                    'nLog_accept_expense_title', 'system', 'nLog_accept_expense_text',
                    'https://ntirapp.com/driver/cargo/' . $cargoId, 'unread', true
                );

                User::createUserLog($userId, 'nLog_accept_expense_title', 'expense');
            } elseif ($status == 'rejected') {
//                $message = str_replace('#CARGO#', $cargo->CargoName, 'nLog_reject_expense_text--#CARGO#--#ID#');
//                $message = str_replace('#ID#', $cargoId, $message);
//                Notification::sendNotification($driverId, 'nLog_reject_expense_title', 'system', $message);
                Notification::sendNotification(
                    $driverId,
                    'nLog_reject_expense_title', 'system', 'nLog_reject_expense_text',
                    'https://ntirapp.com/driver/cargo/' . $cargoId, 'unread', true
                );
                User::createUserLog($userId, 'uLog_reject_expense_text', 'expense');
            }
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }

    public static function changeExpenseStatusIn($userId, $expenseId, $requestId, $cargoId, $driverId, $status, $token)
    {
        if (!Security::verifyCSRF('expenses-in', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('expenses-in');
        $cargo = self::getCargoInDetail($userId, $cargoId)->response;

        $sql = "update tbl_extra_expenses_in set
        expense_status = :status 
        where expense_id = :expenseId and request_id = :requestId and cargo_id = :cargoId;";
        $params = [
            'expenseId' => $expenseId,
            'requestId' => $requestId,
            'cargoId' => $cargoId,
            'status' => $status,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'Expense status changed successfully');
            if ($status == 'accepted') {
//                $message = str_replace('#CARGO#', $cargo->CargoName, 'nLog_accept_expense_in_text--#CARGO#--#ID#');
//                $message = str_replace('#ID#', $cargoId, $message);
//                Notification::sendNotification($driverId, 'nLog_accept_expense_in_title', 'system', $message);

                Notification::sendNotification(
                    $driverId,
                    'nLog_accept_expense_in_title', 'system', 'nLog_accept_expense_in_text',
                    'https://ntirapp.com/driver/cargo-in/' . $cargoId, 'unread', true
                );
                User::createUserLog($userId, 'nLog_accept_expense_in_title', 'expense');
            } elseif ($status == 'rejected') {
//                $message = str_replace('#CARGO#', $cargo->CargoName, 'nLog_reject_expense_text--#CARGO#--#ID#');
//                $message = str_replace('#ID#', $cargoId, $message);
//                Notification::sendNotification($driverId, 'nLog_reject_expense_in_title', 'system', $message);
                Notification::sendNotification(
                    $driverId,
                    'nLog_reject_expense_in_title', 'system', 'nLog_reject_expense_text',
                    'https://ntirapp.com/driver/cargo-in/' . $cargoId, 'unread', true
                );
                User::createUserLog($userId, 'nLog_reject_expense_in_title', 'expense');
            }
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }

    /**
     * @param $cargoId
     * @param $requestId
     * @param $rate
     * @param $token
     *
     * @return stdClass
     */
    public static function submitRequestRate($cargoId, $requestId, $rate, $token)
    {
        if (!Security::verifyCSRF('rate', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('rate');

        $sql = "update tbl_requests set
        request_rate = :rate
        where request_id = :requestId and cargo_id = :cargoId;";
        $params = [
            'requestId' => $requestId,
            'cargoId' => $cargoId,
            'rate' => $rate,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'Rate submitted successfully');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }

    public static function submitRequestInRate($cargoId, $requestId, $rate, $token)
    {
        if (!Security::verifyCSRF('rate-in', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('rate-in');

        $sql = "update tbl_requests_in set
        request_rate = :rate
        where request_id = :requestId and cargo_id = :cargoId;";
        $params = [
            'requestId' => $requestId,
            'cargoId' => $cargoId,
            'rate' => $rate,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'Rate submitted successfully');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }

    /**
     * @param $userId
     * @param $cargoId
     * @param $reason
     * @param $token
     *
     * @return stdClass
     */
    public static function cancelCargo($userId, $cargoId, $reason, $token)
    {
        if (!Security::verifyCSRF('cancel-cargo', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('cancel-cargo');

        $sql = "update tbl_cargo set
        cargo_cancel_desc = :reason, cargo_status = :status
        where user_id = :userId and cargo_id = :cargoId;";
        $params = [
            'userId' => $userId,
            'cargoId' => $cargoId,
            'reason' => $reason,
            'status' => 'canceled'
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            self::cancelCargoRequests($cargoId);
            $response = sendResponse(200, 'Cargo canceled successfully');
            User::createUserLog($userId, 'uLog_cancel_cargo', 'cargo');
        } else {
            $response = sendResponse(-10, 'Cancel error', $csrf);
        }
        return $response;
    }


    public static function cancelCargoIn($userId, $cargoId, $reason, $token)
    {
        if (!Security::verifyCSRF('cancel-cargo-in', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('cancel-cargo-in');

        $sql = "update tbl_cargo_in set
        cargo_cancel_desc = :reason, cargo_status = :status
        where user_id = :userId and cargo_id = :cargoId;";
        $params = [
            'userId' => $userId,
            'cargoId' => $cargoId,
            'reason' => $reason,
            'status' => 'canceled'
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            self::cancelCargoRequestsIn($cargoId);
            $response = sendResponse(200, 'Cargo canceled successfully');
            User::createUserLog($userId, 'uLog_cancel_cargo_in', 'cargo');
        } else {
            $response = sendResponse(-10, 'Cancel error', $csrf);
        }
        return $response;
    }

    /**
     * @param $cargoId
     *
     * @return bool
     */
    private static function cancelCargoRequests($cargoId)
    {
        $sql = "update tbl_requests set
        request_status = :status
        where cargo_id = :cargoId;";
        $params = [
            'cargoId' => $cargoId,
            'status' => 'rejected'
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            return true;
        }
        return false;
    }

    private static function cancelCargoRequestsIn($cargoId)
    {
        $sql = "update tbl_requests_in set
        request_status = :status
        where cargo_id = :cargoId;";
        $params = [
            'cargoId' => $cargoId,
            'status' => 'rejected'
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            return true;
        }
        return false;
    }

    /**
     * @param $userId
     * @param $categoryId
     * @param $name
     * @param $carTypeId
     * @param $carCount
     * @param $weight
     * @param $volume
     * @param $price
     * @param $currencyId
     * @param $origin
     * @param $customsOfOrigin
     * @param $destination
     * @param $destinationCustoms
     * @param $latitude
     * @param $longitude
     * @param $startDate
     * @param $description
     * @param $images
     * @param $token
     *
     * @return stdClass
     */
    public static function submitCargo($userId, $categoryId, $name, $carTypeId, $carCount, $weight, $volume, $price,
                                       $currencyId, $origin, $customsOfOrigin, $destination, $destinationCustoms,
                                       $latitude, $longitude, $startDate, $description, $images, $token, $greenStreet)
    {
        if (!Security::verifyCSRF('add-cargo', $token, false)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('add-cargo');


        $countyId = Location::getCityInfoById(intval($origin))->CountryId;
        if ($greenStreet == "yes" && $countyId == 1) {

        } else {
            $greenStreet = "no";
        }
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        $slugname_cargo_description = 'cargo_description_' . $language;

        $sql = "insert into tbl_cargo(user_id, category_id, $slugname, type_id, cargo_car_count, cargo_weight, cargo_volume, cargo_recommended_price, 
                      cargo_monetary_unit, cargo_origin_id, cargo_origin_customs_id, cargo_destination_id, cargo_destination_customs_id,cargo_green,
                      cargo_latitude, cargo_longitude, cargo_start_date, $slugname_cargo_description, cargo_images, cargo_rate, cargo_status, cargo_date) 
        values (:userId, :categoryId, :name, :typeId, :cars, :weight, :volume, :price, :currencyId, :origin, :customsOfOrigin, :destination, 
                :destinationCustoms,:cargo_green, :lat, :long, :startDate, :description, :images, :rate, :status, :time);";
        $params = [
            'userId' => $userId,
            'categoryId' => $categoryId,
            'name' => $name,
            'typeId' => $carTypeId,
            'cars' => $carCount,
            'weight' => $weight,
            'volume' => $volume,
            'price' => $price,
            'currencyId' => $currencyId,
            'origin' => $origin,
            'customsOfOrigin' => $customsOfOrigin,
            'destination' => $destination,
            'destinationCustoms' => $destinationCustoms,
            'cargo_green' => $greenStreet,
            'lat' => $latitude,
            'long' => $longitude,
            'startDate' => $startDate,
            'description' => $description,
            'images' => json_encode($images),
            'rate' => json_encode([]),
            'status' => 'pending',
            'time' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Cargo submitted successfully');
            User::createUserLog($userId, 'uLog_submit_cargo', 'cargo');

        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }


    public static function submitCargoIn($userId, $categoryId, $name, $carTypeId, $carCount, $weight, $volume, $price,
                                         $currencyId, $origin, $destination,
                                         $latitude, $longitude, $startDate, $description, $images, $token)
    {
        if (!Security::verifyCSRF('add-cargo-in', $token, false)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('add-cargo-in');
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        $slugname_cargo_description = 'cargo_description_' . $language;
        $sql = "insert into tbl_cargo_in(user_id, category_id, $slugname, type_id, cargo_car_count, cargo_weight, cargo_volume, cargo_recommended_price, cargo_monetary_unit, cargo_origin_id,  cargo_destination_id,  cargo_latitude, cargo_longitude, cargo_start_date, $slugname_cargo_description, cargo_images, cargo_rate, cargo_status, cargo_date) 
        values (:userId, :categoryId, :name, :typeId, :cars, :weight, :volume, :price, :currencyId, :origin, :destination,:lat, :long, :startDate, :description, :images, :rate, :status, :time);";
        $params = [
            'userId' => $userId,
            'categoryId' => $categoryId,
            'name' => $name,
            'typeId' => $carTypeId,
            'cars' => $carCount,
            'weight' => $weight,
            'volume' => $volume,
            'price' => $price,
            'currencyId' => $currencyId,
            'origin' => $origin,
            'destination' => $destination,
            'lat' => $latitude,
            'long' => $longitude,
            'startDate' => $startDate,
            'description' => $description,
            'images' => json_encode($images),
            'rate' => json_encode([]),
            'status' => 'pending',
            'time' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Cargo submitted successfully');
            User::createUserLog($userId, 'uLog_submit_cargo_in', 'cargo_in');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $cargoId
     * @param $categoryId
     * @param $name
     * @param $carTypeId
     * @param $carCount
     * @param $weight
     * @param $volume
     * @param $price
     * @param $currencyId
     * @param $origin
     * @param $customsOfOrigin
     * @param $destination
     * @param $destinationCustoms
     * @param $latitude
     * @param $longitude
     * @param $startDate
     * @param $description
     * @param $images
     * @param $token
     *
     * @return stdClass
     */
    public static function editCargo($userId, $cargoId, $categoryId, $name, $carTypeId, $carCount, $weight, $volume,
                                     $price, $currencyId, $origin, $customsOfOrigin, $destination, $destinationCustoms,
                                     $latitude, $longitude, $startDate, $description, $images, $token, $greenStreet)
    {
        if (!Security::verifyCSRF('edit-cargo', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('edit-cargo');

        $countyId = Location::getCityInfoById(intval($origin))->CountryId;
        if ($greenStreet == "yes" && $countyId == 1) {

        } else {
            $greenStreet = "no";
        }
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        $slugname_cargo_description = 'cargo_description_' . $language;
        $sql = "update tbl_cargo set
        category_id = :categoryId, $slugname = :name, type_id = :typeId, cargo_car_count = :cars, cargo_weight = :weight,
        cargo_volume = :volume, cargo_recommended_price = :price, cargo_monetary_unit = :currencyId, cargo_origin_id = :origin,
        cargo_origin_customs_id = :customsOfOrigin, cargo_destination_id = :destination, cargo_destination_customs_id = :destinationCustoms,
        `cargo_green`=:cargo_green,cargo_latitude = :lat, cargo_longitude = :long, cargo_start_date = :startDate, $slugname_cargo_description = :description,
        cargo_images = :images, cargo_status = :status
        where user_id = :userId and cargo_id = :cargoId;";
        $params = [
            'userId' => $userId,
            'cargoId' => $cargoId,
            'categoryId' => $categoryId,
            'name' => $name,
            'typeId' => $carTypeId,
            'cars' => $carCount,
            'weight' => $weight,
            'volume' => $volume,
            'price' => $price,
            'currencyId' => $currencyId,
            'origin' => $origin,
            'customsOfOrigin' => $customsOfOrigin,
            'destination' => $destination,
            'destinationCustoms' => $destinationCustoms,
            'cargo_green' => $greenStreet,
            'lat' => $latitude,
            'long' => $longitude,
            'startDate' => $startDate,
            'description' => $description,
            'images' => json_encode($images),
            'status' => 'pending'
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'Cargo updated successfully');
            User::createUserLog($userId, 'uLog_edit_cargo', 'cargo');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }


    public static function editCargoIn($userId, $cargoId, $categoryId, $name, $carTypeId, $carCount, $weight, $volume,
                                       $price, $currencyId, $origin, $destination,
                                       $latitude, $longitude, $startDate, $description, $images, $token)
    {
        if (!Security::verifyCSRF('edit-cargo-in', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('edit-cargo-in');
        $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
        $slugname = 'cargo_name_' . $language;
        $slugname_cargo_description = 'cargo_description_' . $language;
        $sql = "update tbl_cargo_in set
        category_id = :categoryId, $slugname = :name, type_id = :typeId, cargo_car_count = :cars, cargo_weight = :weight,
        cargo_volume = :volume, cargo_recommended_price = :price, cargo_monetary_unit = :currencyId, cargo_origin_id = :origin,
        cargo_destination_id = :destination,
        cargo_latitude = :lat, cargo_longitude = :long, cargo_start_date = :startDate, $slugname_cargo_description = :description,
        cargo_images = :images, cargo_status = :status
        where user_id = :userId and cargo_id = :cargoId;";
        $params = [
            'userId' => $userId,
            'cargoId' => $cargoId,
            'categoryId' => $categoryId,
            'name' => $name,
            'typeId' => $carTypeId,
            'cars' => $carCount,
            'weight' => $weight,
            'volume' => $volume,
            'price' => $price,
            'currencyId' => $currencyId,
            'origin' => $origin,
            'destination' => $destination,
            'lat' => $latitude,
            'long' => $longitude,
            'startDate' => $startDate,
            'description' => $description,
            'images' => json_encode($images),
            'status' => 'pending'
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'Cargo updated successfully');
            User::createUserLog($userId, 'uLog_edit_cargo_in', 'cargo');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }


}