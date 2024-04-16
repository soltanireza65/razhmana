<?php

use MJ\Database\DB;
use MJ\Security\Security;
use MJ\Utils\Utils;
use function MJ\Keys\sendResponse;

class PosterC
{
    /////////////////// Start Brands

    /**
     * get All Brands From Tabel
     *
     * @return stdClass
     */
    public static function getAllBrandsFromTabel($status = null)
    {

        $response = sendResponse(0, "Error Msg");

        if (is_null($status)) {
            $sql = "SELECT `brand_id`, `brand_name`, `brand_image`, `brand_creator`, `brand_status`,`brand_priority`,`brand_type` FROM `tbl_brands` ORDER BY brand_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT `brand_id`, `brand_name`, `brand_image`, `brand_creator`, `brand_status`,`brand_priority`,`brand_type` FROM `tbl_brands` WHERE `brand_status`=:brand_status ORDER BY brand_id DESC ;";
            $params = [
                'brand_status' => $status
            ];
        }


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * set New Brand
     *
     * @param $title
     * @param $image
     * @param $status
     * @param $priority
     *
     * @return stdClass
     */
    public static function setNewBrand($title, $status, $priority, $type, $image = null, $brand_creator = 'admin')
    {

        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $array = [];
        $array['admin'] = $admin_id;
        $array['date_create'] = time();
        $array['update'] = [];

        if ($admin_id > 0) {
            $sql = "INSERT INTO `tbl_brands`(`brand_name`, `brand_image`, `brand_creator`,`brand_type` ,`brand_status`,`brand_priority`, `brand_options`)
                VALUES (:brand_name,:brand_image,:brand_creator,:brand_type,:brand_status,:brand_priority,:brand_options)";
            $params = [
                'brand_name' => $title,
                'brand_image' => $image,
                'brand_creator' => $brand_creator,
                'brand_type' => $type,
                'brand_status' => $status,
                'brand_priority' => $priority,
                'brand_options' => json_encode($array),
            ];

            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "Successful", $result->response);
            }
        }

        return $response;
    }


    /**
     * get Brand Info By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getBrandById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_brands` WHERE `brand_id`=:brand_id";
        $params = [
            'brand_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * edit Brand By Id
     *
     * @param $id
     * @param $title
     * @param $image
     * @param $status
     *
     * @return stdClass
     */
    public static function editBrandById($id, $title, $status, $priority, $type, $image = null)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getBrandById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->brand_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            if (is_null($image)) {
                $sql = 'UPDATE `tbl_brands` SET `brand_name`=:brand_name,`brand_type`=:brand_type,`brand_status`=:brand_status,`brand_priority`=:brand_priority,
                        `brand_options`=:brand_options WHERE `brand_id`=:brand_id';
                $params = [
                    'brand_name' => $title,
                    'brand_type' => $type,
                    'brand_status' => $status,
                    'brand_priority' => $priority,
                    'brand_options' => json_encode($value),
                    'brand_id' => $id,
                ];
            } else {
                $sql = 'UPDATE `tbl_brands` SET `brand_name`=:brand_name,`brand_image`=:brand_image,`brand_type`=:brand_type,`brand_status`=:brand_status,
                        `brand_priority`=:brand_priority,`brand_options`=:brand_options WHERE `brand_id`=:brand_id';
                $params = [
                    'brand_name' => $title,
                    'brand_image' => $image,
                    'brand_type' => $type,
                    'brand_status' => $status,
                    'brand_priority' => $priority,
                    'brand_options' => json_encode($value),
                    'brand_id' => $id,
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
     * get All Brands From Model
     *
     * @return stdClass
     */
    public static function getAllBrandsParentActive()
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT brand_id, brand_name,brand_type FROM `tbl_brands` 
                WHERE brand_status=:brand_status ORDER BY brand_id DESC ;";
        $params = [
            'brand_status' => "active"
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    public static function getBrandsPoster($type)
    {
        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        $temp = [];
        $sql = "SELECT brand_id, brand_name,brand_image FROM `tbl_brands` 
                WHERE brand_status=:brand_status AND `brand_creator`=:brand_creator AND `brand_type`=:brand_type ORDER BY brand_priority ASC ;";
        $params = [
            'brand_status' => "active",
            'brand_creator' => "admin",
            'brand_type' => $type,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            foreach ($result->response as $key => $loop) {
                $array = [];
                $array['id'] = $loop->brand_id;
                $array['name_en'] = array_column(json_decode($loop->brand_name, true), 'value', 'slug')['en_US'];
                $array['name'] = array_column(json_decode($loop->brand_name, true), 'value', 'slug')[$language];
                $array['image'] = Utils::fileExist($loop->brand_image, BOX_EMPTY);
                array_push($temp, $array);
            }
            return ['status' => 200, 'data' => $temp];
        }
        return ['status' => -1, 'data' => $temp];
    }
    //////////////// Start Model


    /**
     * get All Model From Tabel
     *
     * @return stdClass
     */
    public static function getAllModelsFromTabel()
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT `model_id`, `model_name`, `model_creator`, `model_status`,`model_priority`,tbl_model.brand_id,brand_name,brand_type FROM `tbl_model`
                INNER JOIN `tbl_brands` ON tbl_model.brand_id=tbl_brands.brand_id ORDER BY model_id DESC ;";
        $params = [];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * set New Model
     *
     * @param $title
     * @param $image
     * @param $status
     * @param $parent
     *
     * @return stdClass
     */
    public static function setNewModel($title, $parent, $status, $priority, $creator = 'admin')
    {

        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $array = [];
        $array['admin'] = $admin_id;
        $array['date_create'] = time();
        $array['update'] = [];

        if ($admin_id > 0) {
            $sql = "INSERT INTO `tbl_model`(`model_name`,`brand_id`,`model_creator`, `model_status`,`model_priority`, `model_options`)
                VALUES (:model_name,:brand_id,:model_creator,:model_status,:model_priority,:model_options)";
            $params = [
                'model_name' => $title,
                'brand_id' => $parent,
                'model_creator' => $creator,
                'model_status' => $status,
                'model_priority' => $priority,
                'model_options' => json_encode($array),
            ];

            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "Successful", $result->response);
            }
        }

        return $response;
    }


    /**
     * get Model Info By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getModelById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_model` WHERE `model_id`=:model_id";
        $params = [
            'model_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * edit Model By Id
     *
     * @param $id
     * @param $title
     * @param $parent
     * @param $status
     * @param $priority
     *
     * @return stdClass
     */
    public static function editModelById($id, $title, $parent, $status, $priority)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getModelById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->model_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            $sql = 'UPDATE `tbl_model` SET `brand_id`=:brand_id,`model_name`=:model_name,
                       `model_status`=:model_status,`model_priority`=:model_priority,`model_options`=:model_options WHERE `model_id`=:model_id';
            $params = [
                'brand_id' => $parent,
                'model_name' => $title,
                'model_status' => $status,
                'model_priority' => $priority,
                'model_options' => json_encode($value),
                'model_id' => $id,
            ];

            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;

    }


    public static function getModelsPoster()
    {
        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        $temp = [];
        $sql = "SELECT model_name,model_id,brand_id FROM `tbl_model` 
                WHERE model_status=:model_status AND `model_creator`=:model_creator ORDER BY `model_priority` ASC ;";
        $params = [
            'model_status' => "active",
            'model_creator' => "admin",
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            foreach ($result->response as $key => $loop) {
                $array = [];
                $array['id'] = $loop->model_id;
                $array['brandId'] = $loop->brand_id;
                $array['name_en'] = array_column(json_decode($loop->model_name, true), 'value', 'slug')['en_US'];
                $array['name'] = array_column(json_decode($loop->model_name, true), 'value', 'slug')[$language];
                array_push($temp, $array);
            }
            return ['status' => 200, 'data' => $temp];
        }
        return ['status' => -1, 'data' => $temp];
    }


    public static function getModelsPosterFromAdmin()
    {
        $response = sendResponse(0, "", []);
        $sql = "SELECT * FROM `tbl_model` INNER JOIN `tbl_brands` ON tbl_model.brand_id=tbl_brands.brand_id";
        $params = [];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            return sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    //////////////// Start Gearbox


    /**
     * get All Gearbox From Tabel
     *
     * @return stdClass
     */
    public static function getAllGearboxFromTabel($status = null)
    {

        $response = sendResponse(0, "Error Msg");

        if (is_null($status)) {
            $sql = "SELECT `gearbox_id`, `gearbox_name`, `gearbox_image`, `gearbox_status`, `gearbox_priority` FROM `tbl_gearboxs` ORDER BY `gearbox_id` DESC ;";
            $params = [];
        } else {
            $sql = "SELECT `gearbox_id`, `gearbox_name`, `gearbox_image`, `gearbox_status`, `gearbox_priority` FROM `tbl_gearboxs` WHERE `gearbox_status`=:gearbox_status ORDER BY `gearbox_id` DESC ;";
            $params = [
                'gearbox_status' => $status
            ];
        }


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * set New Gearbox
     *
     * @param $title
     * @param $image
     * @param $status
     * @param $priority
     *
     * @return stdClass
     */
    public static function setNewGearbox($title, $status, $priority, $image = null)
    {

        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $array = [];
        $array['admin'] = $admin_id;
        $array['date_create'] = time();
        $array['update'] = [];

        if ($admin_id > 0) {
            $sql = "INSERT INTO `tbl_gearboxs`(`gearbox_name`, `gearbox_image`, `gearbox_status`,`gearbox_priority`, `gearbox_options`)
                VALUES (:gearbox_name,:gearbox_image,:gearbox_status,:gearbox_priority,:gearbox_options)";
            $params = [
                'gearbox_name' => $title,
                'gearbox_image' => $image,
                'gearbox_status' => $status,
                'gearbox_priority' => $priority,
                'gearbox_options' => json_encode($array),
            ];

            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "Successful", $result->response);
            }
        }

        return $response;
    }


    /**
     * get Gearbox Info By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getGearboxById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_gearboxs` WHERE `gearbox_id`=:gearbox_id";
        $params = [
            'gearbox_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * edit Gearbox By Id
     *
     * @param $id
     * @param $title
     * @param $image
     * @param $status
     *
     * @return stdClass
     */
    public static function editGearboxById($id, $title, $status, $priority, $image = null)
    {
        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getGearboxById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->gearbox_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            if (is_null($image)) {
                $sql = 'UPDATE `tbl_gearboxs` SET `gearbox_name`=:gearbox_name,`gearbox_status`=:gearbox_status,`gearbox_priority`=:gearbox_priority,
                        `gearbox_options`=:gearbox_options WHERE `gearbox_id`=:gearbox_id';
                $params = [
                    'gearbox_name' => $title,
                    'gearbox_status' => $status,
                    'gearbox_priority' => $priority,
                    'gearbox_options' => json_encode($value),
                    'gearbox_id' => $id,
                ];
            } else {
                $sql = 'UPDATE `tbl_gearboxs` SET `gearbox_name`=:gearbox_name,`gearbox_image`=:gearbox_image,`gearbox_status`=:gearbox_status,
                        `gearbox_priority`=:gearbox_priority,`gearbox_options`=:gearbox_options WHERE `gearbox_id`=:gearbox_id';
                $params = [
                    'gearbox_name' => $title,
                    'gearbox_image' => $image,
                    'gearbox_status' => $status,
                    'gearbox_priority' => $priority,
                    'gearbox_options' => json_encode($value),
                    'gearbox_id' => $id,
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
     * @return stdClass
     * @author Tjavan
     */
    public static function getAllGearboxsFromUser()
    {
        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        $response = [];
        $sql = "SELECT `gearbox_id`, `gearbox_name`,`gearbox_image` FROM `tbl_gearboxs` 
                WHERE `gearbox_status`=:gearbox_status ORDER BY `gearbox_priority` ASC ;";
        $params = [
            'gearbox_status' => 'active',
        ];
        $result = DB::rawQuery($sql, $params);
        $list = [];
        if ($result->status == 200) {
            foreach ($result->response as $item) {
                $data = new stdClass();
                $data->id = $item->gearbox_id;
                $data->name = array_column(json_decode($item->gearbox_name, true), 'value', 'slug')[$language];
                $data->image = $item->gearbox_image;
                array_push($list, $data);
            }
            $response = $list;
        }

        return $response;
    }


    //////////////// Start Fuel


    /**
     * get All Gearbox From Tabel
     *
     * @return stdClass
     */
    public static function getAllFuelFromTabel($status = null)
    {

        $response = sendResponse(0, "Error Msg");

        if (is_null($status)) {
            $sql = "SELECT `fuel_id`, `fuel_name`, `fuel_image`, `fuel_status`, `fuel_priority` FROM `tbl_fuel` ORDER BY `fuel_id` DESC ;";
            $params = [];
        } else {
            $sql = "SELECT `fuel_id`, `fuel_name`, `fuel_image`,`fuel_status`, `fuel_priority` FROM `tbl_fuel` WHERE `fuel_status`=:fuel_status ORDER BY `fuel_id` DESC ;";
            $params = [
                'fuel_status' => $status
            ];
        }


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * set New Fuel
     *
     * @param $title
     * @param $image
     * @param $status
     * @param $priority
     *
     * @return stdClass
     */
    public static function setNewFuel($title, $status, $priority, $image = null)
    {

        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $array = [];
        $array['admin'] = $admin_id;
        $array['date_create'] = time();
        $array['update'] = [];

        if ($admin_id > 0) {
            $sql = "INSERT INTO `tbl_fuel`(`fuel_name`, `fuel_image`, `fuel_status`,`fuel_priority`, `fuel_options`)
                VALUES (:fuel_name,:fuel_image,:fuel_status,:fuel_priority,:fuel_options)";
            $params = [
                'fuel_name' => $title,
                'fuel_image' => $image,
                'fuel_status' => $status,
                'fuel_priority' => $priority,
                'fuel_options' => json_encode($array),
            ];

            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "Successful", $result->response);
            }
        }

        return $response;
    }


    /**
     * get Fuel Info By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getFuelById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_fuel` WHERE `fuel_id`=:fuel_id";
        $params = [
            'fuel_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * edit Fuel By Id
     *
     * @param $id
     * @param $title
     * @param $image
     * @param $status
     *
     * @return stdClass
     */
    public static function editFuelById($id, $title, $status, $priority, $image = null)
    {
        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getFuelById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->fuel_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            if (is_null($image)) {
                $sql = 'UPDATE `tbl_fuel` SET `fuel_name`=:fuel_name,`fuel_status`=:fuel_status,`fuel_priority`=:fuel_priority,
                        `fuel_options`=:fuel_options WHERE `fuel_id`=:fuel_id';
                $params = [
                    'fuel_name' => $title,
                    'fuel_status' => $status,
                    'fuel_priority' => $priority,
                    'fuel_options' => json_encode($value),
                    'fuel_id' => $id,
                ];
            } else {
                $sql = 'UPDATE `tbl_fuel` SET `fuel_name`=:fuel_name,`fuel_image`=:fuel_image,`fuel_status`=:fuel_status,
                        `fuel_priority`=:fuel_priority,`fuel_options`=:fuel_options WHERE `fuel_id`=:fuel_id';
                $params = [
                    'fuel_name' => $title,
                    'fuel_image' => $image,
                    'fuel_status' => $status,
                    'fuel_priority' => $priority,
                    'fuel_options' => json_encode($value),
                    'fuel_id' => $id,
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
     * @return stdClass
     * @author Tjavan
     */
    public static function getAllFuelsFromUser()
    {
        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        $response = [];
        $sql = "SELECT `fuel_id`, `fuel_name`,`fuel_image` FROM `tbl_fuel` 
                WHERE `fuel_status`=:fuel_status ORDER BY `fuel_priority` ASC ;";
        $params = [
            'fuel_status' => 'active',
        ];
        $result = DB::rawQuery($sql, $params);
        $list = [];
        if ($result->status == 200) {
            foreach ($result->response as $item) {
                $data = new stdClass();
                $data->id = $item->fuel_id;
                $data->name = array_column(json_decode($item->fuel_name, true), 'value', 'slug')[$language];
                $data->image = $item->fuel_image;
                array_push($list, $data);
            }
            $response = $list;
        }

        return $response;
    }

    //////////////// Start Fuel


    /**
     * get All property From Tabel
     *
     * @return stdClass
     */
    public static function getAllPropertyFromTabel($status = null)
    {

        $response = sendResponse(0, "Error Msg");

        if (is_null($status)) {
            $sql = "SELECT `property_id`, `property_name`, `property_image`, `property_status`, `property_priority`,`property_type` FROM `tbl_properties` ORDER BY `property_id` DESC ;";
            $params = [];
        } else {
            $sql = "SELECT `property_id`, `property_name`, `property_image`,`property_status`, `property_priority`,`property_type` FROM `tbl_properties` WHERE `property_status`=:property_status ORDER BY `property_id` DESC ;";
            $params = [
                'property_status' => $status
            ];
        }


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * set New Property
     *
     * @param $title
     * @param $status
     * @param $type
     * @param $priority
     * @param $image
     *
     * @return stdClass
     */
    public static function setNewProperty($title, $status, $type, $priority, $image)
    {

        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $array = [];
        $array['admin'] = $admin_id;
        $array['date_create'] = time();
        $array['update'] = [];

        if ($admin_id > 0) {
            $sql = "INSERT INTO `tbl_properties`(`property_name`, `property_image`, `property_type`, `property_priority`, `property_status`, `property_options`)
                VALUES (:property_name,:property_image,:property_type,:property_priority,:property_status,:property_options)";
            $params = [
                'property_name' => $title,
                'property_image' => $image,
                'property_type' => $type,
                'property_priority' => $priority,
                'property_status' => $status,
                'property_options' => json_encode($array),
            ];

            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "Successful", $result->response);
            }
        }

        return $response;
    }


    /**
     * get Property Info By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getPropertyById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_properties` WHERE `property_id`=:property_id";
        $params = [
            'property_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * edit Property By Id
     *
     * @param $id
     * @param $title
     * @param $type
     * @param $status
     * @param $priority
     * @param $image
     *
     * @return stdClass
     */
    public static function editPropertyById($id, $title, $type, $status, $priority, $image = null)
    {
        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getPropertyById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->property_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            if (is_null($image)) {
                $sql = 'UPDATE `tbl_properties` SET `property_name`=:property_name,`property_status`=:property_status,`property_priority`=:property_priority,
                        `property_type`=:property_type,`property_options`=:property_options WHERE `property_id`=:property_id';
                $params = [
                    'property_name' => $title,
                    'property_status' => $status,
                    'property_priority' => $priority,
                    'property_type' => $type,
                    'property_options' => json_encode($value),
                    'property_id' => $id,
                ];
            } else {
                $sql = 'UPDATE `tbl_properties` SET `property_name`=:property_name,`property_status`=:property_status,`property_priority`=:property_priority,
                        `property_type`=:property_type,`property_image`=:property_image,`property_options`=:property_options WHERE `property_id`=:property_id';
                $params = [
                    'property_name' => $title,
                    'property_status' => $status,
                    'property_priority' => $priority,
                    'property_image' => $image,
                    'property_type' => $type,
                    'property_options' => json_encode($value),
                    'property_id' => $id,
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
     * @return stdClass
     * @author Tjavan
     */
    public static function getAllPropertyFromUser($type)
    {
        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        $response = [];
        $sql = "SELECT `property_id`, `property_name`, `property_image`  FROM `tbl_properties` 
                WHERE `property_status`=:property_status AND `property_type`=:property_type ORDER BY `property_priority` ASC ;";
        $params = [
            'property_status' => 'active',
            'property_type' => $type,
        ];
        $result = DB::rawQuery($sql, $params);
        $list = [];
        if ($result->status == 200) {
            foreach ($result->response as $item) {
                $data = new stdClass();
                $data->id = $item->property_id;
                $data->name = array_column(json_decode($item->property_name, true), 'value', 'slug')[$language];
                $data->image = Utils::fileExist($item->property_image, BOX_EMPTY);
                array_push($list, $data);
            }
            $response = $list;
        }

        return $response;
    }    /**
     * @return stdClass
     * @author Tjavan
     */
    public static function getSearchPropertyItem($type , $search_value)
    {
        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        $response = [];

        $sql = '';
        if($search_value == 'all-properties'){
            $sql = "SELECT `property_id`, `property_name`, `property_image`  FROM `tbl_properties` 
                WHERE `property_status`=:property_status AND `property_type`=:property_type ORDER BY `property_priority` ASC ; ";
        }else{

            $sql = "SELECT `property_id`, `property_name`, `property_image`  FROM `tbl_properties` 
                WHERE `property_status`=:property_status AND `property_type`=:property_type and property_name like '%$search_value%' ORDER BY `property_priority` ASC ; ";
        }

        $params = [
            'property_status' => 'active',
            'property_type' => $type,
        ];
        $result = DB::rawQuery($sql, $params);
        $list = [];
        if ($result->status == 200) {
            foreach ($result->response as $item) {
                $data = new stdClass();
                $data->id = $item->property_id;
                $data->name = array_column(json_decode($item->property_name, true), 'value', 'slug')[$language];
                $data->image = Utils::fileExist($item->property_image, BOX_EMPTY);
                array_push($list, $data);
            }
            $response = $list;
        }

        return $response;
    }
    ////////////////// Start Reports


    /**
     * get All property From Tabel
     *
     * @return stdClass
     */
    public static function getAllReportFromTabel($status = null)
    {

        $response = sendResponse(0, "Error Msg");

        if (is_null($status)) {
            $sql = "SELECT `report_title`, `report_id`, `report_creator`, `report_status`, `report_priority` FROM `tbl_reports` ORDER BY `report_id` DESC ;";
            $params = [];
        } else {
            $sql = "SELECT `report_title`, `report_id`, `report_creator`,`report_status`, `report_priority` FROM `tbl_reports` WHERE `report_status`=:report_status ORDER BY `report_id` DESC ;";
            $params = [
                'report_status' => $status
            ];
        }


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    public static function setNewReport($title, $status, $priority)
    {

        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $array = [];
        $array['admin'] = $admin_id;
        $array['date_create'] = time();
        $array['update'] = [];

        if ($admin_id > 0) {
            $sql = "INSERT INTO `tbl_reports`(`report_title`, `report_creator`, `report_status`, `report_priority`, `report_options`)
                VALUES (:report_title,:report_creator,:report_status,:report_priority,:report_options)";
            $params = [
                'report_title' => $title,
                'report_creator' => 'admin',
                'report_status' => $status,
                'report_priority' => $priority,
                'report_options' => json_encode($array),
            ];

            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "Successful", $result->response);
            }
        }

        return $response;
    }


    /**
     * get Report Info By Id
     *
     * @param $id
     *
     * @return stdClass
     */
    public static function getReportById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_reports` WHERE `report_id`=:report_id";
        $params = [
            'report_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * edit Report By Id
     *
     * @param $id
     * @param $title
     * @param $desc
     * @param $status
     * @param $priority
     *
     * @return stdClass
     */
    public static function editReportById($id, $title, $status, $priority)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getReportById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->report_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            $sql = 'UPDATE `tbl_reports` SET `report_title`=:report_title,`report_status`=:report_status,
                         `report_priority`=:report_priority,`report_options`=:report_options WHERE `report_id`=:report_id';
            $params = [
                'report_title' => $title,
                'report_status' => $status,
                'report_priority' => $priority,
                'report_options' => json_encode($value),
                'report_id' => $id,
            ];

            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;

    }


    public static function getAllReportFromPosterDetail()
    {
        $response = [];
        $sql = "SELECT `report_title`, `report_id` FROM `tbl_reports`
                WHERE `report_status`=:report_status ORDER BY `report_priority` ASC ;";
        $params = [
            'report_status' => 'active',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response;
        }
        return $response;
    }


    public static function getBrandsModalInfoByModelId($modelId)
    {
        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

//        $response = [];
        $sql = "SELECT tbl_model.brand_id, `brand_name`,`brand_image`,`model_id`,`model_name` 
                FROM `tbl_brands` INNER JOIN `tbl_model`
                ON tbl_model.brand_id=tbl_brands.brand_id
                WHERE `model_id`=:model_id";
        $params = [
            'model_id' => $modelId
        ];

        $result = DB::rawQuery($sql, $params);
        $data = [];
        if ($result->status == 200) {
//            foreach ($result->response as $item) {
            $item = $result->response[0];

            $data = new stdClass();
            $data->brandId = $item->brand_id;
            $data->brandName = array_column(json_decode($item->brand_name, true), 'value', 'slug')[$language];
            $data->brandImage = Utils::fileExist($item->brand_image, BOX_EMPTY);
            $data->modelId = $item->model_id;
            $data->modelName = array_column(json_decode($item->model_name, true), 'value', 'slug')[$language];
//            array_push($list, $data);
        }
//        $response = $data;
//        }

        return $data;
    }


    // Start Reason Deleted


    public static function getAllCategoryReasonDeletedPosterFromTable($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT * FROM `tbl_poster_reason_delete` ORDER BY `category_id` DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_poster_reason_delete` WHERE `category_status`=:category_status ORDER BY category_id DESC ;";
            $params = [
                'category_status' => $status
            ];
        }

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    public static function setNewCategoryReasonDeletedPoster($title, $status, $priority)
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

            $sql = 'INSERT INTO `tbl_poster_reason_delete`(`category_name`, `category_priority`,`category_status`, `category_options`) VALUES 
                    (:category_name,:category_priority,:category_status,:category_options)';
            $params = [
                'category_name' => $title,
                'category_priority' => $priority,
                'category_status' => $status,
                'category_options' => json_encode($array),
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }

        return $response;
    }

    public static function getCategoryReasonDeletedPosterById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_poster_reason_delete` WHERE category_id=:category_id";
        $params = [
            'category_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }

    public static function editCategoryReasonDeletedPosterById($id, $title, $status, $priority)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCategoryReasonDeletedPosterById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->category_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            $sql = 'UPDATE `tbl_poster_reason_delete` SET `category_name`=:category_name,`category_priority`=:category_priority,`category_status`=:category_status,`category_options`=:category_options
                    WHERE `category_id`=:category_id;';
            $params = [
                'category_name' => $title,
                'category_priority' => $priority,
                'category_status' => $status,
                'category_options' => json_encode($value),
                'category_id' => $id,
            ];
            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    public static function getAllPosterReasonDeleteFromPosterDetail()
    {
        $response = [];
        $sql = "SELECT `category_name`, `category_id` FROM `tbl_poster_reason_delete`
                WHERE `category_status`=:category_status ORDER BY `category_priority` ASC ;";
        $params = [
            'category_status' => 'active',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response;
        }
        return $response;
    }


    /**
     * get All Brands From Tabel
     *
     * @return stdClass
     */
    public static function getAllBrandsbyType($type, $status = 'active')
    {

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT `brand_id`, `brand_name`, `brand_image`, `brand_creator`, `brand_status`,`brand_priority`,`brand_type` FROM `tbl_brands`
                    WHERE `brand_status`=:brand_status  and  tbl_brands.brand_type = :brand_type
                    ORDER BY brand_priority DESC ;";
        $params = [
            'brand_status' => $status,
            'brand_type' => $type
        ];
        $result = DB::rawQuery($sql, $params);

        $list = [];
        if ($result->status == 200) {
            foreach ($result->response as $item) {
                $data = new stdClass();
                $data->id = $item->brand_id;
                $data->name = array_column(json_decode($item->brand_name, true), 'value', 'slug')[$language];
                $data->image = Utils::fileExist($item->brand_image, BOX_EMPTY);
                array_push($list, $data);
            }
            $response = $list;
        }

        return $response;
    }

    /**
     * get All Brands From Tabel
     *
     * @return stdClass
     */
    public static function getSearchBrandItem($type , $search_value, $status = 'active')
    {

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }
        $response = sendResponse(0, "Error Msg");
        $sql = '';
        if($search_value == 'all-brands'){
            $sql = "SELECT `brand_id`, `brand_name`, `brand_image`, `brand_creator`, `brand_status`,`brand_priority`,`brand_type` FROM `tbl_brands`
                    WHERE `brand_status`=:brand_status  and  tbl_brands.brand_type = :brand_type 
                    ORDER BY brand_priority DESC ;";
        }else{
            $sql = "SELECT `brand_id`, `brand_name`, `brand_image`, `brand_creator`, `brand_status`,`brand_priority`,`brand_type` FROM `tbl_brands`
                    WHERE `brand_status`=:brand_status  and  tbl_brands.brand_type = :brand_type and brand_name like  '%$search_value%' 
                    ORDER BY brand_priority DESC ;";
        }

        $params = [
            'brand_status' => $status,
            'brand_type' => $type
        ];
        $result = DB::rawQuery($sql, $params);

        $list = [];
        if ($result->status == 200) {
            foreach ($result->response as $item) {
                $data = new stdClass();
                $data->id = $item->brand_id;
                $data->name = array_column(json_decode($item->brand_name, true), 'value', 'slug')[$language];
                $data->image = Utils::fileExist($item->brand_image, BOX_EMPTY);
                array_push($list, $data);
            }
            $response = $list;
        }

        return $response;
    }
}