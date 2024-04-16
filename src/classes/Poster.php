<?php

use MJ\Database\DB;
use MJ\Security\Security;
use MJ\SMS\SMS;
use MJ\Utils\Utils;
use function MJ\Keys\sendResponse;

class Poster
{

    public static function insertPoster(
        $token,
        $ads_title,
        $user_id,
        $city_id,
        $currency_id,
        $mobile,
        $clockFrom,
        $clockTo,
        $poster_type,
        $cash,
        $leasing,
        $installment,
        $price = null,
        $status = null,
        $brandId = null,
        $brandName = null,
        $modelId = null,
        $modelName = null,
        $gearbox_id = null,
        $fuel_id = null,
        $color = null,
        $builtTruck = null,
        $runTruck = null,
        $axis = null,
        $modelTrailer = null,
        $imageFiles = null,
        $desc = null,
        $whatsapp = null,
        $properties = null,
        $parent_id = null
    ) {

        if (!Security::verifyCSRF('add-edit-poster', $token)) {
            return sendResponse(-1, 0, '');
        }
        $csrf = Security::initCSRF('add-edit-poster');

        $response = sendResponse(-1, 0, $csrf);

        if (!isset($price) || empty($price) || $price == 0) {
            $price = null;
        }

        if ($brandId == 0 && $modelId == 0) {
            $result_Brand = self::setBrandByUser($user_id, $brandName, $poster_type);
            $model = self::setModelByUser($user_id, $modelName, $result_Brand->response)->response;
        } elseif ($brandId != 0 && $modelId == 0) {
            $model = self::setModelByUser($user_id, $modelName, $brandId)->response;
        } elseif ($brandId != 0 && $modelId != 0) {
            $model = $modelId;
        } else {
            $model = null;
        }


        if ($poster_type == "truck" && $status == 'new') {
            $runTruck = 0;
        }

        $language = $_COOKIE['language'] ? $_COOKIE['language'] : 'fa_IR';
        $title_column_name = 'poster_title_' . $language;

        $sql = "INSERT INTO `tbl_poster`(`poster_parent_id`,$title_column_name,`user_id`, `city_id`, `model_id`, `fuel_id`, `gearbox_id`, `currency_id`,
                         `trailer_id`, `poster_type`, `poster_price`, `poster_cash`, `poster_leasing`, `poster_installments`,`poster_type_status`,
                         `poster_color_out`,`poster_used`, `poster_built`, `poster_desc`, `poster_images`, `poster_axis`, `poster_phone`,`poster_whatsapp`, 
                         `poster_time_from`, `poster_time_to`,`poster_submit_date`,`poster_update_date`, `poster_immediate_time`,
                         `poster_status`,`poster_expire`, `poster_options`)
                          VALUES (:poster_parent_id,:poster_title,:user_id,:city_id,:model_id,:fuel_id,:gearbox_id,:currency_id,:trailer_id,:poster_type,:poster_price,:poster_cash,
                                  :poster_leasing,:poster_installments,:poster_type_status,:poster_color_out,:poster_used,:poster_built,:poster_desc,
                                  :poster_images,:poster_axis,:poster_phone,:poster_whatsapp,:poster_time_from,:poster_time_to,:poster_submit_date,
                                  :poster_update_date,:poster_immediate_time,
                                  :poster_status,:poster_expire,:poster_options)";
        $params = [
            'poster_parent_id' => $parent_id,
            'poster_title' => $ads_title,
            'user_id' => $user_id,
            'city_id' => $city_id,
            'model_id' => $model,
            'fuel_id' => $fuel_id,
            'gearbox_id' => $gearbox_id,
            'currency_id' => $currency_id,
            'trailer_id' => $modelTrailer,
            'poster_type' => $poster_type,
            'poster_price' => $price,
            'poster_cash' => $cash,
            'poster_leasing' => $leasing,
            'poster_installments' => $installment,
            'poster_type_status' => $status,
            'poster_color_out' => $color,
            'poster_used' => $runTruck,
            'poster_built' => $builtTruck,
            'poster_desc' => $desc,
            'poster_images' => json_encode($imageFiles),
            'poster_axis' => $axis,
            'poster_phone' => $mobile,
            'poster_whatsapp' => $whatsapp,
            'poster_time_from' => $clockFrom,
            'poster_time_to' => $clockTo,
            'poster_submit_date' => time(),
            'poster_update_date' => time(),
            'poster_immediate_time' => time() - 200,
            'poster_status' => 'pending',
            'poster_expire' => intval(Utils::getFileValue('settings.txt', 'poster_expire_time')),
            'poster_options' => null,
        ];

        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            if (!empty($properties)) {
                foreach ($properties as $property) {
                    self::setProperties($result->response, $property);
                }
            }
            $response = sendResponse(200, $result->response, $csrf);
        }

        return $response;
    }

    public static function setBrandByUser($userId, $barndName, $type)
    {
        $response = sendResponse(0, "Error Msg", 0);

        $array = [];
        $array['user'] = $userId;
        $array['date_create'] = time();
        $array['update'] = [];
        $userLang = User::getUserInfo($userId)->UserLanguage;

        $resultLanguages = Utils::getFileValue("languages.json", "", false);
        $dataLanguages = [];
        if (!empty($resultLanguages)) {
            $dataLanguages = json_decode($resultLanguages);
        }
        $adddd = [];
        if (!empty($dataLanguages)) {
            foreach ($dataLanguages as $dataLanguagesTEMP) {
                if ($dataLanguagesTEMP->slug == $userLang) {
                    array_push($adddd, ['slug' => $dataLanguagesTEMP->slug, 'value' => $barndName]);
                } else {
                    array_push($adddd, ['slug' => $dataLanguagesTEMP->slug, 'value' => '']);
                }
            }
        }
        $sql = "INSERT INTO `tbl_brands`(`brand_name`, `brand_image`, `brand_creator`,`brand_type`, `brand_status`, `brand_priority`, `brand_options`)
                VALUES (:brand_name,:brand_image,:brand_creator,:brand_type,:brand_status,:brand_priority,:brand_options)";
        $params = [
            'brand_name' => json_encode($adddd),
            'brand_image' => null,
            'brand_creator' => 'user',
            'brand_type' => $type,
            'brand_status' => 'user',
            'brand_priority' => 0,
            'brand_options' => json_encode($array),
        ];

        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    public static function setModelByUser($userId, $modelName, $brandId)
    {
        $response = sendResponse(0, "Error Msg", 0);

        $array = [];
        $array['user'] = $userId;
        $array['date_create'] = time();
        $array['update'] = [];


        $userLang = User::getUserInfo($userId)->UserLanguage;

        $resultLanguages = Utils::getFileValue("languages.json", "", false);
        $dataLanguages = [];
        if (!empty($resultLanguages)) {
            $dataLanguages = json_decode($resultLanguages);
        }
        $adddd = [];
        if (!empty($dataLanguages)) {
            foreach ($dataLanguages as $dataLanguagesTEMP) {
                if ($dataLanguagesTEMP->slug == $userLang) {
                    array_push($adddd, ['slug' => $dataLanguagesTEMP->slug, 'value' => $modelName]);
                } else {
                    array_push($adddd, ['slug' => $dataLanguagesTEMP->slug, 'value' => '']);
                }
            }
        }


        $sql = "INSERT INTO `tbl_model`(`model_name`, `brand_id`, `model_creator`, `model_status`, `model_priority`, `model_options`)
                VALUES (:model_name,:brand_id,:model_creator,:model_status,:model_priority,:model_options)";
        $params = [
            'model_name' => json_encode($adddd),
            'brand_id' => $brandId,
            'model_creator' => 'user',
            'model_status' => 'user',
            'model_priority' => 0,
            'model_options' => json_encode($array),
        ];

        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    private static function setProperties($posterId, $propertyId)
    {
        $response = sendResponse(0, "Error Msg", 0);
        $sql = "INSERT INTO `tbl_select_properties`(`poster_id`, `property_id`) VALUES (:poster_id,:property_id)";
        $params = [
            'poster_id' => $posterId,
            'property_id' => $propertyId,
        ];

        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    private static function deletedProperties($posterId)
    {
        $response = sendResponse(0, "Error Msg", 0);
        $sql = "DELETE FROM `tbl_select_properties` WHERE poster_id=:poster_id";
        $params = [
            'poster_id' => $posterId,
        ];

        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    public static function getPosterInfoById($id)
    {
        $response = sendResponse(-1, 'Error', []);

        $sql = "SELECT * FROM `tbl_poster` WHERE poster_id=:posterID";
        $params = [
            'posterID' => $id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200 && isset($result->response[0]) && !empty($result->response[0])) {
            $response = sendResponse(200, 'Successful', $result->response[0]);
        }
        return $response;
    }


    public static function getPosterParentInfoById($id)
    {
        $response = sendResponse(-1, 'Error', []);

        $sql = "SELECT * FROM `tbl_poster` WHERE poster_parent_id=:posterID ";
        $params = [
            'posterID' => $id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200 && isset($result->response[0]) && !empty($result->response[0])) {
            $response = sendResponse(200, 'Successful', $result->response[0]);
        }
        return $response;
    }


    public static function getPosterById($id)
    {
        $user_id = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;

        $response = sendResponse(-1, 'Error', []);

        $sqlP = "SELECT * FROM `tbl_poster` WHERE poster_parent_id=:postreID AND user_id=:userId AND `poster_status` IN ('pending','accepted','needed') ";
        $paramsP = [
            'postreID' => $id,
            'userId' => $user_id,
        ];
        $resultP = DB::rawQuery($sqlP, $paramsP);
        if ($resultP->status == 200 && isset($resultP->response[0]) && !empty($resultP->response[0])) {
            $response = sendResponse(200, 'Successful', $resultP->response[0]);
        } else {

            $sql = "SELECT * FROM `tbl_poster` WHERE poster_id=:postreID AND user_id=:userId  AND `poster_status` IN ('pending','accepted','needed')";
            $params = [
                'postreID' => $id,
                'userId' => $user_id,
            ];
            $result = DB::rawQuery($sql, $params);
            if ($result->status == 200 && isset($result->response[0]) && !empty($result->response[0])) {
                $response = sendResponse(200, 'Successful', $result->response[0]);
            }
        }
        return $response;
    }


    public static function getPosterPropertiesByPosterId($PosterId)
    {
        $response = '';
        $sql = "SELECT GROUP_CONCAT(tsp.property_id) AS ids FROM `tbl_select_properties` tsp WHERE poster_id=:posterID ";
        $params = [
            'posterID' => $PosterId,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200 && isset($result->response[0]) && !empty($result->response[0])) {
            $response = $result->response[0]->ids;
        }
        return $response;
    }


    public static function editPoster(
        $token,
        $ads_title,
        $posterId,
        $user_id,
        $city_id,
        $currency_id,
        $mobile,
        $clockFrom,
        $clockTo,
        $poster_type,
        $cash,
        $leasing,
        $installment,
        $price = null,
        $status = null,
        $brandId = null,
        $brandName = null,
        $modelId = null,
        $modelName = null,
        $gearbox_id = null,
        $fuel_id = null,
        $color = null,
        $builtTruck = null,
        $runTruck = null,
        $axis = null,
        $modelTrailer = null,
        $imageFiles = null,
        $desc = null,
        $whatsapp = null,
        $properties = null,
        $posterStatus = null
    ) {

        $response = sendResponse(-1, 0, []);

        $sql = "SELECT * FROM `tbl_poster` WHERE poster_id=:posterID AND user_id=:userId ";
        $params = [
            'posterID' => $posterId,
            'userId' => $user_id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200 && isset($result->response[0]) && !empty($result->response[0])) {

            if ($result->response[0]->poster_status == "accepted") {


                $rrrrrr = self::insertPoster(
                    $token,
                    $ads_title,
                    $user_id,
                    $city_id,
                    $currency_id,
                    $mobile,
                    $clockFrom,
                    $clockTo,
                    $poster_type,
                    $cash,
                    $leasing,
                    $installment,
                    $price,
                    $status,
                    $brandId,
                    $brandName,
                    $modelId,
                    $modelName,
                    $gearbox_id,
                    $fuel_id,
                    $color,
                    $builtTruck,
                    $runTruck,
                    $axis,
                    $modelTrailer,
                    $imageFiles,
                    $desc,
                    $whatsapp,
                    $properties,
                    $posterId
                );

                self::updateOptionsAdmin($posterId, $rrrrrr->message, "accepted", 'pending', null, $user_id);

                User::createUserLog($user_id, 'uLog_edit_insert_poster_' . $poster_type, 'poster');


                return $rrrrrr;


            } elseif (in_array($result->response[0]->poster_status, ['pending', 'needed'])) {

                if (is_null($result->response[0]->poster_parent_id)) {
                    $posterIdParent = $posterId;
                } else {
                    $posterIdParent = $result->response[0]->poster_parent_id;
                }

                self::updateOptionsAdmin($posterIdParent, $posterId, $result->response[0]->poster_status, 'pending', null, $user_id);

                return self::updatePoster(
                    $token,
                    $ads_title,
                    $posterId,
                    $user_id,
                    $city_id,
                    $currency_id,
                    $mobile,
                    $clockFrom,
                    $clockTo,
                    $poster_type,
                    $cash,
                    $leasing,
                    $installment,
                    $price,
                    $status,
                    $brandId,
                    $brandName,
                    $modelId,
                    $modelName,
                    $gearbox_id,
                    $fuel_id,
                    $color,
                    $builtTruck,
                    $runTruck,
                    $axis,
                    $modelTrailer,
                    $imageFiles,
                    $desc,
                    $whatsapp,
                    $properties,
                    'pending'
                );
                User::createUserLog($user_id, 'uLog_edit_update_poster_' . $poster_type, 'poster');

            }
        }
        return $response;
    }


    private static function updatePoster(
        $token,
        $ads_title,
        $posterId,
        $user_id,
        $city_id,
        $currency_id,
        $mobile,
        $clockFrom,
        $clockTo,
        $poster_type,
        $cash,
        $leasing,
        $installment,
        $price = null,
        $status = null,
        $brandId = null,
        $brandName = null,
        $modelId = null,
        $modelName = null,
        $gearbox_id = null,
        $fuel_id = null,
        $color = null,
        $builtTruck = null,
        $runTruck = null,
        $axis = null,
        $modelTrailer = null,
        $imageFiles = null,
        $desc = null,
        $whatsapp = null,
        $properties = null,
        $posterStatus = null
    ) {

        if (!Security::verifyCSRF('add-edit-poster', $token)) {
            return sendResponse(-1, 'CSRF-Token error', '');
        }
        $csrf = Security::initCSRF('add-edit-poster');

        $response = sendResponse(-1, "error", $csrf);

        if (!isset($price) || empty($price) || $price == 0) {
            $price = null;
        }

        if ($brandId == 0 && $modelId == 0) {
            $result_Brand = self::setBrandByUser($user_id, $brandName, $poster_type);
            $model = self::setModelByUser($user_id, $modelName, $result_Brand->response)->response;
        } elseif ($brandId != 0 && $modelId == 0) {
            $model = self::setModelByUser($user_id, $modelName, $brandId)->response;
        } elseif ($brandId != 0 && $modelId != 0) {
            $model = $modelId;
        } else {
            $model = null;
        }


        if ($poster_type == "truck" && $status == 'new') {
            $runTruck = 0;
        }

        $language = $_COOKIE['language'] ? $_COOKIE['language'] : 'fa_IR';
        $title_column_name = 'poster_title_' . $language;
        $sql = "UPDATE `tbl_poster`
                        SET 
                            `city_id`=:city_id,`model_id`=:model_id,`fuel_id`=:fuel_id,`gearbox_id`=:gearbox_id,
                        `currency_id`=:currency_id,
                        `$title_column_name`=:poster_title,
                        `trailer_id`=:trailer_id,`poster_type`=:poster_type,
                        `poster_price`=:poster_price,`poster_cash`=:poster_cash,`poster_leasing`=:poster_leasing,
                        `poster_installments`=:poster_installments,`poster_type_status`=:poster_type_status,
                        `poster_color_out`=:poster_color_out,`poster_used`=:poster_used,`poster_built`=:poster_built,
                        `poster_desc`=:poster_desc,`poster_images`=:poster_images,`poster_axis`=:poster_axis,`poster_phone`=:poster_phone,
                        `poster_whatsapp`=:poster_whatsapp,`poster_time_from`=:poster_time_from,`poster_time_to`=:poster_time_to,
                       `poster_status`=:poster_status
                WHERE `poster_id`=:poster_id AND `user_id`=:user_id";
        $params = [
            'poster_id' => $posterId,
            'user_id' => $user_id,
            'poster_title' => $ads_title,
            'city_id' => $city_id,
            'model_id' => $model,
            'fuel_id' => $fuel_id,
            'gearbox_id' => $gearbox_id,
            'currency_id' => $currency_id,
            'trailer_id' => $modelTrailer,
            'poster_type' => $poster_type,
            'poster_price' => $price,
            'poster_cash' => $cash,
            'poster_leasing' => $leasing,
            'poster_installments' => $installment,
            'poster_type_status' => $status,
            'poster_color_out' => $color,
            'poster_used' => $runTruck,
            'poster_built' => $builtTruck,
            'poster_desc' => $desc,
            'poster_images' => json_encode($imageFiles),
            'poster_axis' => $axis,
            'poster_phone' => $mobile,
            'poster_whatsapp' => $whatsapp,
            'poster_time_from' => $clockFrom,
            'poster_time_to' => $clockTo,
            'poster_status' => $posterStatus,
        ];

        $result = DB::update($sql, $params);

        if ($result->status == 200 || $result->status == 208) {
            self::deletedProperties($posterId);
            if (!empty($properties)) {
                foreach ($properties as $property) {
                    self::setProperties($posterId, $property);
                }
            }
            $response = sendResponse(200, "Successful", $csrf);
        }

        return $response;
    }


    // Start First Change Poster Status
    public static function changeStatusPoster($posterId, $status, $reason = null)
    {
        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $resultDefault = self::getPosterInfoById($posterId);
        $dataDefault = [];
        if ($resultDefault->status == 200 && !empty($resultDefault->response)) {
            $dataDefault = $resultDefault->response;
        } else {
            return $response;
        }


        if (is_null($dataDefault->poster_parent_id)) {

            self::updateOptionsAdmin($posterId, $posterId, $dataDefault->poster_status, $status, $admin_id, null, $reason);
            $result = self::updateOnlyStatusPoster($posterId, $status, $reason);


            if ($result->status == 200) {
                $rrr = self::getPosterParentInfoById($posterId);
                if ($rrr->status == 200) {
                    self::copyPoster($rrr->response->poster_id, 'deleted');
                    self::deletePoster($rrr->response->poster_id);
                    self::copyPosterProperties($rrr->response->poster_id);
                    self::deletedProperties($rrr->response->poster_id);
                }
                $response = sendResponse(200, "Successful");
            }

        } else {
            // has Parent ID
            if ($status == "needed") {
                self::updateOptionsAdmin($dataDefault->poster_parent_id, $posterId, $dataDefault->poster_status, "needed", $admin_id, null, $reason);

                $result = self::updateOnlyStatusPoster($posterId, "needed", $reason);
                if ($result->status == 200) {
                    $response = sendResponse(200, "Successful");
                }

            } elseif ($status == "accepted") {

                self::copyPoster($posterId, "accepted");

                $result = self::copyChildToParent($posterId, 'accepted', $reason);
                if ($result->status == 200) {

                    self::copyPosterProperties($dataDefault->poster_id);
                    self::deletedProperties($dataDefault->poster_parent_id);

                    $properties = explode(",", self::getPosterPropertiesByPosterId($posterId));
                    if (!empty($properties)) {
                        foreach ($properties as $property) {
                            self::setProperties($dataDefault->poster_parent_id, $property);
                        }
                    }

                    self::updateOptionsAdmin($dataDefault->poster_parent_id, $posterId, $dataDefault->poster_status, "accepted", $admin_id, null, $reason);
                    self::deletePoster($posterId);
                    self::deletedProperties($posterId);

                    $response = sendResponse(200, "Successful", $result);
                }
            }
        }
        return $response;
    }


    private static function updateOnlyStatusPoster($posterId, $statusNew, $reason)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = 'UPDATE `tbl_poster` SET `poster_status`=:poster_status,`poster_reason`=:poster_reason WHERE `poster_id`=:poster_id';
        $params = [
            'poster_status' => $statusNew,
            'poster_id' => $posterId,
            'poster_reason' => $reason,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    private static function deletePoster($posterId)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = 'DELETE FROM `tbl_poster` WHERE `poster_id`=:poster_id';
        $params = [
            'poster_id' => $posterId,
        ];
        $result = DB::delete($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }

    private static function deletePosterChild($posterId)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = 'DELETE FROM `tbl_poster` WHERE poster_parent_id=:poster_parent_id;';
        $params = [
            'poster_parent_id' => $posterId,
        ];
        $result = DB::delete($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    private static function copyPoster($posterId, $status, $type = 'admin')
    {
        $file = SITE_ROOT . "/db/poster.json";

        if (!empty(file_get_contents($file))) {
            $array = json_decode(file_get_contents($file), true);
        } else {
            $array = [];
        }
        $a = [];
        $result = Poster::getPosterInfoById($posterId);
        foreach ($result->response as $key => $value) {
            $a[$key] = $value;
        }
        $a['backup'] = time();
        $a['new_status'] = $status;
        $a['client'] = $type;
        array_push($array, $a);

        file_put_contents($file, json_encode($array));
    }


    private static function copyPosterProperties($posterId)
    {
        $file = SITE_ROOT . "/db/poster-properties.json";

        if (!empty(file_get_contents($file))) {
            $array = json_decode(file_get_contents($file), true);
        } else {
            $array = [];
        }
        $a = [];
        $result = Poster::getAllProperties($posterId);
        foreach ($result->response as $key => $value) {
            $a[$key] = $value;
        }
        array_push($array, $a);

        file_put_contents($file, json_encode($array));
    }


    private static function copyChildToParent($posterId, $statusNew, $reason = null)
    {
        $response = sendResponse(0, "Error Msg");

        $Child = self::getPosterInfoById($posterId)->response;
        $Parent = self::getPosterInfoById($Child->poster_parent_id)->response;

        $sql = "UPDATE `tbl_poster` SET `city_id`=:city_id,`model_id`=:model_id,`fuel_id`=:fuel_id,
                        `gearbox_id`=:gearbox_id,`currency_id`=:currency_id,`trailer_id`=:trailer_id,`poster_type`=:poster_type,
                        `poster_price`=:poster_price,`poster_cash`=:poster_cash,`poster_leasing`=:poster_leasing,
                        `poster_installments`=:poster_installments,`poster_type_status`=:poster_type_status,`poster_color_out`=:poster_color_out,
                        `poster_used`=:poster_used,`poster_built`=:poster_built,`poster_desc`=:poster_desc,`poster_images`=:poster_images,
                        `poster_axis`=:poster_axis,`poster_phone`=:poster_phone,`poster_whatsapp`=:poster_whatsapp,
                        `poster_time_from`=:poster_time_from,`poster_time_to`=:poster_time_to,`poster_status`=:poster_status,`poster_reason`=:poster_reason
                WHERE `poster_id`=:poster_id";
        $params = [
            'poster_id' => $Parent->poster_id,
            'city_id' => $Child->city_id,
            'model_id' => $Child->model_id,
            'fuel_id' => $Child->fuel_id,
            'gearbox_id' => $Child->gearbox_id,
            'currency_id' => $Child->currency_id,
            'trailer_id' => $Child->trailer_id,
            'poster_type' => $Child->poster_type,
            'poster_price' => $Child->poster_price,
            'poster_cash' => $Child->poster_cash,
            'poster_leasing' => $Child->poster_leasing,
            'poster_installments' => $Child->poster_installments,
            'poster_type_status' => $Child->poster_type_status,
            'poster_color_out' => $Child->poster_color_out,
            'poster_used' => $Child->poster_used,
            'poster_built' => $Child->poster_built,
            'poster_desc' => $Child->poster_desc,
            'poster_images' => $Child->poster_images,
            'poster_axis' => $Child->poster_axis,
            'poster_phone' => $Child->poster_phone,
            'poster_whatsapp' => $Child->poster_whatsapp,
            'poster_time_from' => $Child->poster_time_from,
            'poster_time_to' => $Child->poster_time_to,
            'poster_status' => $statusNew,
            'poster_reason' => $reason,
        ];

        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    private static function updateOptionsAdmin($id, $posterId, $statusOld, $statusNew, $adminId = null, $userId = null, $reason = null)
    {

        $resultParent = self::getPosterInfoById($id)->response;

        $array = [];
        if (!empty($resultParent->poster_options)) {
            $array = json_decode($resultParent->poster_options, true);
            $a = [];
            if ($adminId) {
                $a['admin'] = $adminId;
            } else {
                $a['user'] = $userId;
            }
            $a['status_old'] = $statusOld;
            $a['status_new'] = $statusNew;
            $a['poster_id'] = $posterId;
            $a['reason'] = $reason;
            $a['date'] = time();
            array_push($array, $a);

        } else {
            $a = [];
            $array = [];

            if ($adminId) {
                $a['admin'] = $adminId;
            } else {
                $a['user'] = $userId;
            }
            $a['status_old'] = $statusOld;
            $a['status_new'] = $statusNew;
            $a['poster_id'] = $posterId;
            $a['reason'] = $reason;
            $a['date'] = time();

            array_push($array, $a);
        }

        $response = sendResponse(0, "error");
        $sql = "UPDATE `tbl_poster` SET `poster_options`=:poster_options
                WHERE `poster_id`=:poster_id";
        $params = [
            'poster_id' => $id,
            'poster_options' => json_encode($array),
        ];

        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    public static function getAllProperties($posterId)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_select_properties` WHERE poster_id=:poster_id";
        $params = [
            'poster_id' => $posterId,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function getAllPostersByStatus($status)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT * FROM `tbl_poster` INNER JOIN `tbl_users` ON tbl_poster.user_id=tbl_users.user_id WHERE `poster_status`=:poster_status";
        $params = [
            'poster_status' => $status
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    public static function getCountPostersByStatus($status)
    {
        $response = 0;
        $sql = "SELECT count(*) AS count FROM `tbl_poster` WHERE `poster_status`=:poster_status";
        $params = [
            'poster_status' => $status
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = $result->response[0]->count;
        }

        return $response;
    }


    public static function getAllPosterTransactions()
    {
        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT * FROM `tbl_poster_transactions`";
        $params = [];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function getPosterFromHomeSlider($limit = 10)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_poster`
                left join tbl_model on tbl_model.model_id=tbl_poster.model_id
                left join tbl_brands on tbl_brands.brand_id=tbl_model.brand_id
                left join tbl_car_types on tbl_car_types.type_id=tbl_poster.trailer_id 
                inner join tbl_currency on tbl_poster.currency_id = tbl_currency.currency_id
                WHERE `poster_status`=:status ORDER BY poster_id DESC LIMIT {$limit}";
        $params = [
            'status' => 'accepted'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function getAllPostersFromUserDashboard($userId)
    {

        $sql = "SELECT COUNT(*) AS countAll,
                    (SELECT COUNT(*) FROM tbl_poster WHERE user_id=:userId AND poster_status=:pending) AS countPending,
                    (SELECT COUNT(*) FROM tbl_poster WHERE user_id=:userId AND poster_status=:rejected) AS countRejected,
                    (SELECT COUNT(*) FROM tbl_poster WHERE user_id=:userId AND poster_status=:accepted) AS countAccepted,
                    (SELECT COUNT(*) FROM tbl_poster WHERE user_id=:userId AND poster_status=:needed) AS countNeeded,
                    (SELECT COUNT(*) FROM tbl_poster WHERE user_id=:userId AND poster_status=:expired) AS countExpired,
                    (SELECT COUNT(*) FROM tbl_poster WHERE user_id=:userId AND poster_status=:deleted) AS countDeleted
                    FROM tbl_poster WHERE user_id=:userId AND poster_status NOT IN ('deleted') AND poster_parent_id IS NULL  ";
        $params = [
            'userId' => $userId,
            'pending' => 'pending',
            'rejected' => 'rejected',
            'accepted' => 'accepted',
            'needed' => 'needed',
            'expired' => 'expired',
            'deleted' => 'deleted',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $item = new stdClass();
            $item->all = $result->response[0]->countAll;
            $item->pending = $result->response[0]->countPending;
            $item->rejected = $result->response[0]->countRejected;
            $item->accepted = $result->response[0]->countAccepted;
            $item->needed = $result->response[0]->countNeeded;
            $item->expired = $result->response[0]->countExpired;
            $item->deleted = $result->response[0]->countDeleted;
            return $item;
        }
        $item = new stdClass();
        $item->all = 0;
        $item->pending = 0;
        $item->rejected = 0;
        $item->accepted = 0;
        $item->needed = 0;
        $item->expired = 0;
        $item->deleted = 0;
        return $item;
    }


    public static function getPosterMyList($userId, $status = 'all')
    {
        $response = sendResponse(200, '', []);
        global $lang;
        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        if ($status == 'all') {
            $sql = "select * from tbl_poster
                    left join tbl_model on tbl_model.model_id=tbl_poster.model_id
                    left join tbl_brands on tbl_brands.brand_id=tbl_model.brand_id
                    left join tbl_car_types on tbl_car_types.type_id=tbl_poster.trailer_id 
                    where user_id = :userId and poster_status not in ('deleted')
                    order by poster_id desc 
                    limit 0,2500";
            $params = [
                'userId' => $userId,
            ];
        } elseif (in_array($status, ['pending'])) {
            $sql = "select * from tbl_poster
                    left join tbl_model on tbl_model.model_id=tbl_poster.model_id
                    left join tbl_brands on tbl_brands.brand_id=tbl_model.brand_id
                    left join tbl_car_types on tbl_car_types.type_id=tbl_poster.trailer_id 
                    where user_id = :userId and poster_status=:status1
                    order by poster_id desc 
                    limit 0,2500";
            $params = [
                'userId' => $userId,
                'status1' => 'pending',
            ];
        } elseif (in_array($status, ['rejected'])) {
            $sql = "select * from tbl_poster
                    left join tbl_model on tbl_model.model_id=tbl_poster.model_id
                    left join tbl_brands on tbl_brands.brand_id=tbl_model.brand_id
                    left join tbl_car_types on tbl_car_types.type_id=tbl_poster.trailer_id 
                    where user_id = :userId and poster_status=:status1
                    order by poster_id desc 
                    limit 0,2500";
            $params = [
                'userId' => $userId,
                'status1' => 'rejected',
            ];
        } elseif (in_array($status, ['accepted'])) {
            $sql = "select * from tbl_poster
                    left join tbl_model on tbl_model.model_id=tbl_poster.model_id
                    left join tbl_brands on tbl_brands.brand_id=tbl_model.brand_id
                    left join tbl_car_types on tbl_car_types.type_id=tbl_poster.trailer_id 
                    where user_id = :userId and poster_status=:status1
                    order by poster_id desc 
                    limit 0,2500";
            $params = [
                'userId' => $userId,
                'status1' => 'accepted',
            ];
        } elseif (in_array($status, ['needed'])) {
            $sql = "select * from tbl_poster
                    left join tbl_model on tbl_model.model_id=tbl_poster.model_id
                    left join tbl_brands on tbl_brands.brand_id=tbl_model.brand_id
                    left join tbl_car_types on tbl_car_types.type_id=tbl_poster.trailer_id 
                    where user_id = :userId and poster_status=:status1
                    order by poster_id desc 
                    limit 0,2500";
            $params = [
                'userId' => $userId,
                'status1' => 'needed',
            ];
        } elseif (in_array($status, ['expired'])) {
            $sql = "select * from tbl_poster
                    left join tbl_model on tbl_model.model_id=tbl_poster.model_id
                    left join tbl_brands on tbl_brands.brand_id=tbl_model.brand_id
                    left join tbl_car_types on tbl_car_types.type_id=tbl_poster.trailer_id 
                    where user_id = :userId and poster_status=:status1
                    order by poster_id desc 
                    limit 0,2500";
            $params = [
                'userId' => $userId,
                'status1' => 'expired',
            ];
        } else {
            $sql = "select * from tbl_poster
                    left join tbl_model on tbl_model.model_id=tbl_poster.model_id
                    left join tbl_brands on tbl_brands.brand_id=tbl_model.brand_id
                    left join tbl_car_types on tbl_car_types.type_id=tbl_poster.trailer_id 
                    where user_id = :userId and poster_status not in ('deleted')
                    order by poster_id desc 
                    limit 0,2500";
            $params = [
                'userId' => $userId,
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $my_response = $result->response;
            $List = [];

            $a_child = [];
            foreach ($my_response as $loop) {
                if (is_null($loop->poster_parent_id)) {

                } else {
                    array_push($a_child, $loop->poster_parent_id);
                }
            }

            $a_out = [];
            foreach ($my_response as $loop) {
                if (in_array($loop->poster_id, $a_child)) {

                } else {
                    array_push($a_out, $loop);
                }
            }


            foreach ($a_out as $item) {
                $poster = new stdClass();
                $poster->PosterId = $item->poster_id;
                $poster->PosterType = $item->poster_type;
                $poster->PosterBrand = (!empty($item->brand_name)) ? array_column(json_decode($item->brand_name, true), 'value', 'slug')[$language] : '';
                $poster->PosterModel = (!empty($item->model_name)) ? array_column(json_decode($item->model_name, true), 'value', 'slug')[$language] : '';
                $poster->PosterTraile = (!empty($item->type_name)) ? array_column(json_decode($item->type_name, true), 'value', 'slug')[$language] : '';
                $poster->PosterAxis = $item->poster_axis;
                $poster->PosterUpdateDate = $item->poster_update_date;
                $poster->PosterCash = $item->poster_cash;
                $poster->PosterLeasing = $item->poster_leasing;
                $poster->PosterInstallments = $item->poster_installments;
                $poster->posterTypeStatus = $item->poster_type_status;
                $poster->posterImages = $item->poster_images;
                $poster->poster_title_fa_IR = $item->poster_title_fa_IR;
                $poster->poster_title_en_US = $item->poster_title_en_US;
                $poster->poster_title_ru_RU = $item->poster_title_ru_RU;
                $poster->poster_title_tr_Tr = $item->poster_title_tr_Tr;
                if ($item->poster_type == "truck") {
                    $poster->PosterTitle = $lang['u_truck'] . " - " . $poster->PosterBrand . " - " . $poster->PosterModel;
                } elseif ($item->poster_type == "trailer") {
                    $poster->PosterTitle = $lang['u_trailer'] . " - " . $poster->PosterTraile . " - " . $poster->PosterAxis . " " . $lang['u_axis'];
                } else {
                    $poster->PosterTitle = '';
                }
                $poster->PosterSubmit = $item->poster_submit_date;
                $poster->PosterSubmitDate = ($language == 'fa_IR') ? Utils::jDate('Y/m/d', $item->poster_submit_date) : date('Y-m-d', $item->poster_submit_date);
                $poster->PosterStatus = $item->poster_status;

                array_push($List, $poster);
            }
            $response = sendResponse(200, '', $List);
        }
        return $response;
    }


    public static function getPosterDetail($posterId)
    {
        $response = sendResponse(404, '', null);
        $sql = "select * from tbl_poster 
                inner join tbl_users on tbl_poster.user_id = tbl_users.user_id
                inner join tbl_cities on tbl_poster.city_id = tbl_cities.city_id
                inner join tbl_country on tbl_country.country_id = tbl_cities.country_id
                left join tbl_model on tbl_model.model_id=tbl_poster.model_id
                left join tbl_brands on tbl_brands.brand_id=tbl_model.brand_id
                left join tbl_car_types on tbl_car_types.type_id=tbl_poster.trailer_id 
                left join tbl_fuel on tbl_fuel.fuel_id=tbl_poster.fuel_id 
                left join tbl_gearboxs on tbl_gearboxs.gearbox_id =tbl_poster.gearbox_id  
                inner join tbl_currency on tbl_poster.currency_id = tbl_currency.currency_id
                where tbl_poster.poster_id = :posterId;";
        $params = [
            'posterId' => $posterId
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Successful', $result->response[0]);
        }
        return $response;
    }


    public static function getPosterChildDetail($posterChildId)
    {
        $response = sendResponse(404, '', null);
        $sql = "select * from tbl_poster 
                inner join tbl_users on tbl_poster.user_id = tbl_users.user_id
                inner join tbl_cities on tbl_poster.city_id = tbl_cities.city_id
                inner join tbl_country on tbl_country.country_id = tbl_cities.country_id
                left join tbl_model on tbl_model.model_id=tbl_poster.model_id
                left join tbl_brands on tbl_brands.brand_id=tbl_model.brand_id
                left join tbl_car_types on tbl_car_types.type_id=tbl_poster.trailer_id 
                left join tbl_fuel on tbl_fuel.fuel_id=tbl_poster.fuel_id 
                left join tbl_gearboxs on tbl_gearboxs.gearbox_id =tbl_poster.gearbox_id  
                inner join tbl_currency on tbl_poster.currency_id = tbl_currency.currency_id
                where tbl_poster.poster_parent_id = :posterId;";
        $params = [
            'posterId' => $posterChildId
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Successful', $result->response[0]);
        }
        return $response;
    }

    public static function getPosterPropertiesByPosterIdFromDetail($PosterId)
    {
        $response = [];
        $sql = "SELECT * FROM `tbl_select_properties` 
                INNER JOIN tbl_properties on tbl_select_properties.property_id = tbl_properties.property_id 
                WHERE poster_id=:posterID ";
        $params = [
            'posterID' => $PosterId,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response;
        }
        return $response;
    }


    public static function getPosterExpertReason($posterId)
    {
        $response = '';
        $sql = "SELECT `pe_reason` AS reason FROM `tbl_poster_expert` 
                WHERE tbl_poster_expert.poster_id=:poster_id AND pe_status=:status ";
        $params = [
            'poster_id' => $posterId,
            'status' => 'completed',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->reason;
        }
        return $response;
    }


    ///**
    /// Start Poster Expert

    public static function getPosterExpertByID($id)
    {
        $response = [];
        $sql = "SELECT * FROM `tbl_poster_expert` 
                INNER JOIN `tbl_poster` ON tbl_poster.poster_id=tbl_poster_expert.poster_id
                INNER JOIN `tbl_users` ON tbl_poster_expert.user_id=tbl_users.user_id
                LEFT JOIN tbl_experts ON tbl_experts.expert_id=tbl_poster_expert.expert_id 
                WHERE tbl_poster_expert.pe_id=:pe_id ";
        $params = [
            'pe_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0];
        }
        return $response;
    }


    public static function editPosterExpertInfoByAdmin($peID, $type, $newValue)
    {
        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $res = self::getPosterExpertByID($peID);
        $temp = [];
        if (!empty($res)) {
            $temp = $res->pe_options;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];

            $a['admin'] = $admin_id;
            $a['type'] = $type;
            $a['value'] = $newValue;
            $a['date'] = time();
            array_push($array, $a);

        } else {
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = $type;
            $a['value'] = $newValue;
            $a['date'] = time();
            array_push($array, $a);
        }


        $sql = 'UPDATE `tbl_poster_expert` SET ' . $type . '=:newValue , pe_options=:pe_options WHERE `pe_id`=:pe_id';
        $params = [
            'newValue' => $newValue,
            'pe_options' => json_encode($array),
            'pe_id' => $peID,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            if ($type == "expert_id") {
                self::sendAddressToExpert($peID);
            }
            $response = sendResponse(200, "");
        }
        return $response;
    }


    public static function changePosterExpertStatus($peID, $status)
    {
        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $res = self::getPosterExpertByID($peID);
        $userID = (isset($res->user_id)) ? $res->user_id : 0;
        $transactionsID = (isset($res->transaction_id)) ? $res->transaction_id : 0;
        $temp = [];
        if (!empty($res)) {
            $temp = $res->pe_options;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];

            $a['admin'] = $admin_id;
            $a['type'] = 'pe_status';
            $a['value'] = $status;
            $a['date'] = time();
            array_push($array, $a);

        } else {
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = 'pe_status';
            $a['value'] = $status;
            $a['date'] = time();
            array_push($array, $a);
        }

        $sql = 'UPDATE `tbl_poster_expert` SET pe_status=:pe_status,`pe_options`=:pe_options WHERE `pe_id`=:pe_id ';
        $params = [
            'pe_status' => $status,
            'pe_options' => json_encode($array),
            'pe_id' => $peID,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            if ($status == "accepted") {
                //                @Notification::sendNotification($userID, 'u_poster_expert_accepted', 'system', 'u_poster_expert_accepted_text--' . $peID);
                Notification::sendNotification(
                    $userID,
                    'u_poster_expert_accepted',
                    'system',
                    'u_poster_expert_accepted_text',
                    'https://ntirapp.com/poster/detail/' . $peID,
                    'unread',
                    true
                );
            } elseif ($status == "rejected") {
                Transactions::backToWalletByAdmin($transactionsID, $peID);
                //                @Notification::sendNotification($userID, 'u_poster_expert_rejected', 'system', 'u_poster_expert_rejected_text--' . $peID);
                Notification::sendNotification(
                    $userID,
                    'u_poster_expert_rejected',
                    'system',
                    'u_poster_expert_rejected_text',
                    'https://ntirapp.com/poster/detail/' . $peID,
                    'unread',
                    true
                );
            } elseif ($status == "completed") {
                //                @Notification::sendNotification($userID, 'u_poster_expert_completed', 'system', 'u_poster_expert_completed_text--' . $peID);
                Notification::sendNotification(
                    $userID,
                    'u_poster_expert_completed',
                    'system',
                    'u_poster_expert_completed_text',
                    'https://ntirapp.com/poster/detail/' . $peID,
                    'unread',
                    true
                );
            } elseif ($status == "canceled") {
                Transactions::backToWalletByAdmin($transactionsID, $peID);
                //                @Notification::sendNotification($userID, 'u_poster_expert_canceled', 'system', 'u_poster_expert_canceled_text--' . $peID);
                Notification::sendNotification(
                    $userID,
                    'u_poster_expert_canceled',
                    'system',
                    'u_poster_expert_canceled_text',
                    'https://ntirapp.com/poster/detail/' . $peID,
                    'unread',
                    true
                );
            }
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    public static function sendAddressToExpert($peId)
    {
        $response = sendResponse(0, "Error Msg");
        $res = self::getPosterExpertByID($peId);
        if (isset($res->pe_address) && isset($res->expert_mobile)) {
            $tt = SMS::sendSMS([$res->expert_mobile], $res->pe_address);
            if ($tt->status == 200) {
                return sendResponse(200, "Expert Selected And Sent SMS");
            }

        }
        return $response;
    }


    public static function getAllPosterExpertByStatus()
    {

        $sql = "SELECT COUNT(*) AS countAll,
                    (SELECT COUNT(*) FROM tbl_poster_expert WHERE pe_status=:status_pending) AS countPending,
                    (SELECT COUNT(*) FROM tbl_poster_expert WHERE pe_status=:status_accepted) AS countAccepted
                    FROM tbl_poster_expert";
        $params = [
            'status_pending' => "pending",
            'status_accepted' => "accepted",
        ];


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            return [
                'all' => $result->response[0]->countAll,
                'accepted' => $result->response[0]->countAccepted,
                'pending' => $result->response[0]->countPending,
            ];
        }

        return [
            'all' => 0,
            'accepted' => 0,
            'pending' => 0,
        ];
    }





    //////////
    ///
    ///
    private static function getPosterReportCount($userId, $posterId)
    {
        $sql = "SELECT count(*) AS count FROM `tbl_report_poster` WHERE `user_id`=:user_id AND `poster_id`=:poster_id";
        $params = [
            'user_id' => $userId,
            'poster_id' => $posterId,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200 && $result->response[0]->count > 0) {
            return false;
        }
        return true;
    }


    public static function submitReport($userId, $posterId, $catId, $description)
    {
        $flag = self::getPosterReportCount($userId, $posterId);
        if ($flag) {
            $sql = "INSERT INTO `tbl_report_poster` (`user_id`, `poster_id`, `report_id`,`rp_desc`, `rp_status`, `rp_submit_date`)
                VALUES (:user_id,:poster_id,:report_id,:rp_desc,:rp_status,:rp_submit_date)";
            $params = [
                'user_id' => $userId,
                'poster_id' => $posterId,
                'report_id' => ($catId == 0 || $catId == null) ? null : $catId,
                'rp_desc' => ($catId == 0 || $catId == null) ? $description : null,
                'rp_status' => 'pending',
                'rp_submit_date' => time(),
            ];
            $result = DB::insert($sql, $params);
            if ($result->status == 200) {
                User::createUserLog($userId, 'uLog_report_poster', 'poster');
                return sendResponse(200, "success", $result->response);
            }

            return sendResponse(-20, $params, $result);
        } else {
            return sendResponse(-10, 'Set Before');
        }
    }


    public static function userDeletePoster($userId, $posterId, $catId)
    {
        @self::updateOptionsAdmin($posterId, $posterId, self::getPosterInfoById($posterId)->response->poster_status, 'deleted', null, $userId, $catId);
        @self::copyPoster(self::getPosterParentInfoById($posterId)->response, 'deleted', 'user');
        $sql = "UPDATE `tbl_poster` SET `poster_status`=:poster_status ,`delete_id`=:delete_id 
                    WHERE `user_id`=:user_id  AND `poster_id`=:poster_id;
                DELETE FROM `tbl_poster` WHERE `poster_parent_id`=:poster_id";
        $params = [
            'poster_status' => 'deleted',
            'delete_id' => $catId,
            'user_id' => $userId,
            'poster_id' => $posterId,
        ];
        $result = DB::transactionQuery($sql, $params);
        if ($result->status == 200) {
            User::createUserLog($userId, 'uLog_delete_poster', 'poster');
            return sendResponse(200, "success");
        }
        return sendResponse(-20, $params, $result);
    }

    private static function getPosterExpertCount($userId, $posterId)
    {

        $sql = "SELECT count(*) AS count FROM `tbl_poster_expert` 
                        WHERE `user_id`=:user_id AND `poster_id`=:poster_id AND `pe_status` IN (:pe_status1,:pe_status2,:pe_status3)";
        $params = [
            'user_id' => $userId,
            'poster_id' => $posterId,
            'pe_status1' => 'pending',
            'pe_status2' => 'accepted',
            'pe_status3' => 'completed',
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200 && $result->response[0]->count > 0) {
            return false;
        }
        return true;
    }

    public static function setExpertPosterFromUser($userId, $posterId, $currencyId, $type, $address, $token)
    {
        if (!Security::verifyCSRF('poster-detail', $token, false)) {
            return sendResponse(-1, 'CSRF-Token error', '');
        }
        //        $csrf = Security::initCSRF('poster-detail');

        $response = sendResponse(-2, 'error', '');
        $countExpert = self::getPosterExpertCount($userId, $posterId);
        if ($countExpert) {
            if ($type == "wallet") {

                $balance = User::getBalance($userId);

                $BalanceAvailable = 0;
                foreach ($balance->response as $item) {
                    if ($item->CurrencyId == $currencyId) {
                        $BalanceAvailable = $item->BalanceAvailable;
                    }
                }

                $tempC = '';
                if ($currencyId == 1) {
                    $tempC = 'poster_expert_price_toman';
                } elseif ($currencyId == 3) {
                    $tempC = 'poster_expert_price_dollar';
                } elseif ($currencyId == 4) {
                    $tempC = 'poster_expert_price_euro';
                }

                $amount = intval(Utils::getFileValue("settings.txt", $tempC));
                if ($BalanceAvailable >= $amount) {
                    $detail = 'درخواست کارشناس در قبال آگهی با ای دی ' . $posterId;
                    $res = Transactions::transactionPoster($userId, $currencyId, $amount, $detail);
                    if ($res->status == 200) {
                        User::createUserLog($userId, 'uLog_submit_wallet_balance_from_poster_expert_error', 'poster');
                        $res_Balance_Expert = self::changeBalanceAndSetNewPosterExpert($userId, $currencyId, $res->response, $posterId, $address, $amount);
                        if ($res_Balance_Expert->status == 200) {
                            User::createUserLog($userId, 'uLog_submit_wallet_balance_from_poster_expert_error', 'poster');
                            $response = sendResponse(200, 'Set Request Expert And Set Transaction', '');
                        } else {
                            User::createUserLog($userId, 'uLog_submit_wallet_balance_from_poster_expert_error', 'poster');
                            $response = sendResponse(-25, 'Error ; Not Set Expert But Set Transaction', '');
                        }
                    } else {
                        $response = sendResponse(-24, 'Error ; Not Set Transaction', '');
                    }
                } else {
                    $response = sendResponse(-23, 'value Low', '');
                }

            } elseif ($type == "online") {
                // todo Set Online Transaction
            }
        } else {
            $response = sendResponse(-22, 'before Set Expert', '');
        }

        return $response;
    }

    private static function changeBalanceAndSetNewPosterExpert($userID, $currencyID, $transactionId, $posterId, $address, $amount)
    {
        $response = sendResponse(-1, 'error', '');
        $sql = "UPDATE `tbl_balance` SET  balance_value = balance_value - :amount
                WHERE `user_id`=:user_id AND `currency_id`=:currency_id;
                INSERT INTO `tbl_poster_transactions`(`transaction_id`, `poster_id`) VALUES (:transaction_id,:poster_id);
                INSERT INTO `tbl_poster_expert`(`user_id`, `poster_id`, `transaction_id`, `pe_address`,`pe_status`, `pe_submit_date`) 
                VALUES (:user_id,:poster_id,:transaction_id,:pe_address,:pe_status,:pe_submit_date)";
        $params = [
            'amount' => $amount,
            'user_id' => $userID,
            'currency_id' => $currencyID,
            'transaction_id' => $transactionId,
            'poster_id' => $posterId,
            'pe_address' => $address,
            'pe_status' => 'pending',
            'pe_submit_date' => time(),

        ];
        $result = DB::transactionQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }

    public static function setUpgradePosterFromUser($userId, $posterId, $kind, $type, $currencyId, $token)
    {
        if (!Security::verifyCSRF('poster-detail', $token, false)) {
            return sendResponse(-1, 'CSRF-Token error', '');
        }
        //        $csrf = Security::initCSRF('poster-detail');

        $response = sendResponse(-2, 'error', '');

        if ($kind == "ladder") {
            if ($type == "wallet") {
                $balance = User::getBalance($userId);
                $BalanceAvailable = 0;
                foreach ($balance->response as $item) {
                    if ($item->CurrencyId == $currencyId) {
                        $BalanceAvailable = $item->BalanceAvailable;
                    }
                }
                $tempC = '';
                if ($currencyId == 1) {
                    $tempC = 'poster_ladder_price_toman';
                } elseif ($currencyId == 3) {
                    $tempC = 'poster_ladder_price_dollar';
                } elseif ($currencyId == 4) {
                    $tempC = 'poster_ladder_price_euro';
                }

                $amount = intval(Utils::getFileValue("settings.txt", $tempC));
                if ($BalanceAvailable >= $amount) {
                    $detail = 'نردبان کردن آگهی با ای دی ' . $posterId;
                    $res = Transactions::transactionPoster($userId, $currencyId, $amount, $detail);
                    if ($res->status == 200) {
                        User::createUserLog($userId, 'uLog_submit_transaction_wallet_from_poster_ladder', 'poster');
                        $res_Balance_Ladder = self::changeBalanceAndLadderPoster($userId, $currencyId, $res->response, $posterId, $amount);
                        if ($res_Balance_Ladder->status == 200) {
                            User::createUserLog($userId, 'uLog_submit_wallet_balance_from_poster_ladder', 'poster');
                            $response = sendResponse(200, 'Successful ; low balance and ladder and Set Transaction', '');
                        } else {
                            User::createUserLog($userId, 'uLog_submit_wallet_balance_from_poster_ladder_error', 'poster');
                            $response = sendResponse(-13, 'Error ; Not low balance and ladder but Set Transaction', '');
                        }
                    } else {
                        $response = sendResponse(-12, 'Error ; Not Set Transaction', '');
                    }
                } else {
                    $response = sendResponse(-11, 'value Low', '');
                }

            } elseif ($type == "online") {
                // todo Set Online Transaction
            }


        } elseif ($kind == "quick") {

            //            $quick_time = intval(Utils::getFileValue("settings.txt", 'poster_immediate_time')) * 86400;
            $posterDetailResult = self::getPosterDetail($posterId);
            if ($posterDetailResult->status == 200) {
                $posterDetailData = $posterDetailResult->response;
                if ($posterDetailData->poster_immediate_time <= time()) {

                    if ($type == "wallet") {
                        $balance = User::getBalance($userId);
                        $BalanceAvailable = 0;
                        foreach ($balance->response as $item) {
                            if ($item->CurrencyId == $currencyId) {
                                $BalanceAvailable = $item->BalanceAvailable;
                            }
                        }
                        $tempC = '';
                        if ($currencyId == 1) {
                            $tempC = 'poster_immediate_price_toman';
                        } elseif ($currencyId == 3) {
                            $tempC = 'poster_immediate_price_dollar';
                        } elseif ($currencyId == 4) {
                            $tempC = 'poster_immediate_price_euro';
                        }

                        $amount = intval(Utils::getFileValue("settings.txt", $tempC));


                        if ($BalanceAvailable >= $amount) {
                            $detail = 'فوری کردن آگهی با ای دی ' . $posterId;
                            $res = Transactions::transactionPoster($userId, $currencyId, $amount, $detail);
                            if ($res->status == 200) {
                                User::createUserLog($userId, 'uLog_submit_wallet_balance_from_poster_quick_error', 'poster');
                                $res_Balance_Quick = self::changeBalanceAndQuickPoster($userId, $currencyId, $res->response, $posterId, $amount);
                                if ($res_Balance_Quick->status == 200) {
                                    User::createUserLog($userId, 'uLog_submit_wallet_balance_from_poster_quick_error', 'poster');
                                    $response = sendResponse(201, 'Successful ; low balance and Quick and Set Transaction', '');
                                } else {
                                    User::createUserLog($userId, 'uLog_submit_wallet_balance_from_poster_quick_error', 'poster');
                                    $response = sendResponse(-25, 'Error ; Not low balance and Quick but Set Transaction', '');
                                }
                            } else {
                                $response = sendResponse(-24, 'Error ; Not Set Transaction', '');
                            }
                        } else {
                            $response = sendResponse(-23, 'value Low', '');
                        }

                    } elseif ($type == "online") {
                        // todo Set Online Transaction
                    }


                } else {
                    $response = sendResponse(-22, 'Warning ; time quick not finish', '');
                }
            } else {
                $response = sendResponse(-21, 'Error ; not find Poster', '');
            }

        }

        return $response;
    }

    private static function changeBalanceAndLadderPoster($userID, $currencyID, $transactionId, $posterId, $amount)
    {
        $response = sendResponse(-1, 'error', '');
        $sql = "UPDATE `tbl_balance` SET  balance_value = balance_value - :amount
                WHERE `user_id`=:user_id AND `currency_id`=:currency_id;
                INSERT INTO `tbl_poster_transactions`(`transaction_id`, `poster_id`) VALUES (:transaction_id,:poster_id);
                UPDATE `tbl_poster` SET `poster_update_date`=:poster_update_date WHERE `poster_id`=:poster_id";
        $params = [
            'amount' => $amount,
            'user_id' => $userID,
            'currency_id' => $currencyID,
            'transaction_id' => $transactionId,
            'poster_id' => $posterId,
            'poster_update_date' => time(),
        ];
        $result = DB::transactionQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }

    private static function changeBalanceAndQuickPoster($userID, $currencyID, $transactionId, $posterId, $amount)
    {
        $response = sendResponse(-1, 'error', '');
        $sql = "UPDATE `tbl_balance` SET  balance_value = balance_value - :amount
                WHERE `user_id`=:user_id AND `currency_id`=:currency_id;
                INSERT INTO `tbl_poster_transactions`(`transaction_id`, `poster_id`) VALUES (:transaction_id,:poster_id);
                UPDATE `tbl_poster` SET `poster_immediate_time`=:poster_immediate_time WHERE `poster_id`=:poster_id";
        $params = [
            'amount' => $amount,
            'user_id' => $userID,
            'currency_id' => $currencyID,
            'transaction_id' => $transactionId,
            'poster_id' => $posterId,
            'poster_immediate_time' => time() + (intval(Utils::getFileValue('settings.txt', 'poster_immediate_time')) * 86400),
        ];
        $result = DB::transactionQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }

    public static function getPosterReports($posterId)
    {
        $response = [];
        $sql = "SELECT * FROM `tbl_report_poster` LEFT JOIN `tbl_reports` ON tbl_report_poster.report_id=tbl_reports.report_id WHERE `poster_id`=:poster_id";
        $params = [
            'poster_id' => $posterId,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response;
        }
        return $response;
    }

    public static function changePosterReports($reportsId, $posterId)
    {
        $response = sendResponse(-1, 'error', '');

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        if ($admin_id > 0) {

            $array = [];
            $a = [];
            $a['admin'] = $admin_id;
            $a['value'] = null;
            $a['date'] = time();
            array_push($array, $a);


            $sql = "UPDATE `tbl_report_poster` SET  `rp_status`=:rp_status,`rp_options`=:rp_options 
                    WHERE `rp_id`=:rp_id AND `poster_id`=:poster_id ";
            $params = [
                'rp_status' => 'reviewed',
                'rp_options' => json_encode($array),
                'rp_id' => $reportsId,
                'poster_id' => $posterId,
            ];
            $result = DB::update($sql, $params);
            if ($result->status == 200) {
                $response = sendResponse(200, 'Successful', '');
            }
        }
        return $response;
    }

    public static function countPosterReportsPending()
    {
        $response = 0;
        $sql = "SELECT count(*) AS count FROM `tbl_report_poster` WHERE `rp_status`=:rp_status";
        $params = [
            'rp_status' => 'pending',
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->count;
        }
        return $response;
    }


    /**
     * Start Cron Job
     */
    public static function updateToExpiredFromCronJob()
    {
        $response = -1;
        $sql = "UPDATE `tbl_poster` SET `poster_expire`=poster_expire - 1";
        $params = [];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = 200;
        }
        return $response;
    }

    public static function updateToExpiredStatusFromCronJob()
    {
        $response = -1;
        $sql = "UPDATE `tbl_poster` SET `poster_status`='expired'  WHERE `poster_expire` <= -1 AND `poster_status` IN ('pending','accepted','needed') AND poster_parent_id IS NUll";
        $params = [];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = 200;
        }
        return $response;
    }

    public static function deleteToExpiredStatusFromCronJob()
    {
        $response = -1;
        $sql = "DELETE FROM `tbl_poster` WHERE  `poster_expire` <= -1 AND `poster_status` IN ('pending','accepted','needed') AND poster_parent_id IS NOT NUll";
        $params = [];
        $result = DB::delete($sql, $params);
        if ($result->status == 200) {
            $response = 200;
        }
        return $response;
    }

    /**
     * End Cron Job
     */

    public static function getFilter(
        $brands,
        $city,
        $country,
        $from_year,
        $to_year,
        $fuels,
        $gear_boxes,
        $installments,
        $leasing,
        $max_price,
        $min_price,
        $poster_category,
        $properties,
        $trailer_types,
        $worked_km_from,
        $worked_km_to,
        $cash,
        $currencyId,
        $page
    ) {
        global $lang;

        $sql = 'select DISTINCT tbl_poster.poster_id,poster_parent_id,user_id ,city_id,tbl_poster.model_id,fuel_id,gearbox_id,tbl_poster.currency_id,
                trailer_id,poster_type,poster_price,poster_cash,poster_leasing,poster_installments,poster_type_status,poster_color_out,poster_used,
                poster_built,poster_desc,poster_images,poster_axis,poster_phone,poster_whatsapp,poster_time_from,poster_time_to,poster_submit_date,
                poster_update_date,poster_immediate_time,poster_reason,poster_status,poster_expire,model_name,model_creator,model_status,model_priority,
                tbl_brands.brand_id,brand_name,brand_image,brand_creator,brand_status,brand_priority,currency_name,currency_status,type_name,
                poster_title_fa_IR , poster_title_en_US , poster_title_ru_RU , poster_title_tr_Tr, count(*) OVER() AS total_count
                from tbl_poster 
                inner join tbl_currency  on tbl_poster.currency_id = tbl_currency.currency_id
                left join tbl_car_types  on  tbl_car_types.type_id =tbl_poster.trailer_id 
                left join tbl_model  on tbl_poster.model_id = tbl_model.model_id
                left join tbl_brands  on  tbl_brands.brand_id =tbl_model.brand_id 
                left join tbl_select_properties  on tbl_poster.poster_id = tbl_select_properties.poster_id
                where 
                ';
        if ($poster_category == 'truck') {


            if ($fuels != []) {
                $sql .= " tbl_poster.fuel_id in (";
                foreach ($fuels as $index => $item) {
                    if ($index == count($fuels) - 1) {
                        $sql .= $item;
                    } else {
                        $sql .= $item . ',';
                    }
                }
                $sql .= ') and ';
            }
            if ($gear_boxes != []) {
                $sql .= " tbl_poster.gearbox_id in (";
                foreach ($gear_boxes as $index => $item) {
                    if ($index == count($gear_boxes) - 1) {
                        $sql .= $item;
                    } else {
                        $sql .= $item . ',';
                    }
                }
                $sql .= ') and ';
            }
            $sql .= " poster_type ='truck' and ";
        }


        if ($country != 'all-country') {
            if ($city != 'all-city') {
                $sql .= " tbl_poster.city_id = $city and ";
            } else {
                $poster_cities = Location::getAllCitiesByCountryId($country, 'poster');
                if ($poster_cities->status == 200) {
                    $poster_cities = $poster_cities->response;
                    $sql .= "  ( ";
                } else {
                    $poster_cities = [];
                }

                foreach ($poster_cities as $index => $item) {
                    if ($index == count($poster_cities) - 1) {
                        $sql .= " tbl_poster.city_id = $item->city_id ) and ";
                    } else {
                        $sql .= " tbl_poster.city_id = $item->city_id or ";
                    }
                }
            }
        }
        if ($from_year < 1500) {
            $sql .= " ((tbl_poster.poster_built BETWEEN $from_year and  $to_year or tbl_poster.poster_built is null) or ";
            $tmp_from = $from_year + 621;
            $tmp_to = $to_year + 621;
            $sql .= " (tbl_poster.poster_built BETWEEN $tmp_from and  $tmp_to or tbl_poster.poster_built is null) )and ";
        } else {
            $sql .= " ((tbl_poster.poster_built BETWEEN $from_year and  $to_year or tbl_poster.poster_built is null) or ";
            $tmp_from = $from_year - 621;
            $tmp_to = $to_year - 621;
            $sql .= " (tbl_poster.poster_built BETWEEN $tmp_from and  $tmp_to or tbl_poster.poster_built is null)) and ";
        }


        if ($installments == 'yes') {
            $sql .= " ( tbl_poster.poster_installments = 'yes' or ";
        } else {
            $sql .= " (tbl_poster.poster_installments = 'no' or ";
        }

        if ($leasing == 'yes') {
            $sql .= " tbl_poster.poster_leasing = 'yes'  or ";
        } else {
            $sql .= " tbl_poster.poster_leasing = 'no'  or ";
        }

        if ($cash == 'yes') {
            $sql .= " tbl_poster.poster_cash = 'yes' ) and ";
        } else {
            $sql .= " tbl_poster.poster_cash = 'no' ) and ";
        }


        $sql .= " ( tbl_poster.poster_price BETWEEN $min_price  and $max_price or tbl_poster.poster_price is null ) and ";


        if ($properties != []) {
            $sql .= " tbl_select_properties.property_id  in (";
            foreach ($properties as $index => $item) {
                if ($index == count($properties) - 1) {
                    $sql .= $item;
                } else {
                    $sql .= $item . ',';
                }
            }
            $sql .= ') and ';
        }


        if ($poster_category == 'trailer') {
            if ($trailer_types != "all-type") {
                $sql .= " tbl_poster.trailer_id  = $trailer_types and tbl_poster.poster_type ='trailer' and ";
            } else {
                $sql .= "  tbl_poster.poster_type ='trailer' and ";
            }
        }

        if ($brands != []) {
            $sql .= " tbl_brands.brand_id  in (";
            foreach ($brands as $index => $item) {
                if ($index == count($brands) - 1) {
                    $sql .= $item;
                } else {
                    $sql .= $item . ',';
                }
            }
            $sql .= ') and ';
        }
        $sql .= " (tbl_poster.poster_used BETWEEN $worked_km_from  and $worked_km_to or tbl_poster.poster_used is null) and ";


        $sql .= " tbl_poster.poster_status ='accepted' and tbl_poster.poster_parent_id is null  order by tbl_poster.poster_id desc ";

        if (isset($page)) {
            $toOffset = ($page - 1) * 3;

            $sql .= " LIMIT 3 ";
            $sql .= " OFFSET $toOffset ";
        }
        $params = [];
        $response = sendResponse(0, '', $sql);
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $output = '';
            $language = 'fa_IR';
            $totalCount = $result->response[0]->total_count;
            if (isset($_COOKIE['language'])) {
                $language = $_COOKIE['language'];
            }
            if (isset($page)) {
                $output .= self::renderPagination($page, $totalCount);
            }
            foreach ($result->response as $item) {
                $quick = '';
                if ($item->poster_immediate_time - time() > 0) {
                    $quick = '<div class="mj-h-poster-fast d-flex justify-content-space-beetwen">
                                                <div class="fa-bolt fa-bounce me-1"></div>
                                                <span>' . $lang['u_immediate'] . '</span>
                                            </div>';
                }

                $word_output = '';
                if ($item->poster_type == "truck") {
                    if ($item->poster_type_status == "new") {
                        $word_output = '<span id="car-badge">' . $lang['u_zero'] . '</span>';
                    } elseif ($item->poster_type_status == "stock") {
                        $word_output = '<span id="car-badge">' . $lang['u_worked'] . '</span>';
                    } elseif ($item->poster_type_status == "order") {
                        $word_output = '<span id="car-badge">' . $lang['u_remittance'] . '</span>';
                    }
                }
                $payment_output = '';
                if ($item->poster_cash == "yes") {
                    $payment_output = '<span id="pay-badge">' . $lang['u_cash_2'] . '</span>';
                } elseif ($item->poster_leasing == "yes") {
                    $payment_output = '<span id="pay-badge">' . $lang['u_leasing'] . '</span>';
                } elseif ($item->poster_installments == "yes") {
                    $payment_output = '<span id="pay-badge">' . $lang['u_installment'] . '</span>';
                }


                if ($item->poster_type == "truck") {
                    $brandTitle = (!empty(array_column(json_decode($item->brand_name, true), 'value', 'slug')[$language])) ?
                        array_column(json_decode($item->brand_name, true), 'value', 'slug')[$language] : '';
                    if (empty($brandTitle)) {
                        foreach (json_decode($item->brand_name) as $loop) {
                            if (!empty($loop->brand_name)) {
                                $brandTitle = $loop->brand_name;
                            }
                        }
                    }

                } elseif ($item->poster_type == 'trailer') {
                    $modelTitle = (!empty(array_column(json_decode($item->type_name, true), 'value', 'slug')[$language])) ?
                        array_column(json_decode($item->type_name, true), 'value', 'slug')[$language] : '';

                }

                $price_output = '';
                if (isset($item->poster_price) && !empty($item->poster_price)) {
                    $currency = (!empty(array_column(json_decode($item->currency_name, true), 'value', 'slug')[$language])) ?
                        array_column(json_decode($item->currency_name, true), 'value', 'slug')[$language] : '';
                    $price_output = '<span class="mj-h-poster-price-num">' . number_format($item->poster_price) . '</span>' . '<span  class="mj-h-poster-price-unit"> ' . $currency . '</span>';
                } else {
                    $price_output = '<span class="mj-h-poster-price-num">' . $lang['u_agreement'] . '<span>';
                }


                $language = $_COOKIE['language'] ? $_COOKIE['language'] : 'fa_IR';
                $title = 'poster_title_' . $language;
                $images = json_decode($item->poster_images);
                $title = $item->$title;
                $output .= '<div class="mj-p-poster-item-card">
                                   <a href="javascript:void(0)" class="mj-p-poster-item-content" data-id="' . $item->poster_id . '">
                                       <div class="mj-p-poster-top-section">
                                           <div class="mj-p-poster-image">
                                               <img src="' . POSTER_DEFAULT . '" data-src="' . Utils::fileExist((isset($images[0]) ? $images[0] : POSTER_DEFAULT), POSTER_DEFAULT) . '" alt="">
                                               ' . $quick . '
                                           </div>
                                           
                                           <div class="mj-p-poster-details ps-1">
                                               <div class="mj-poster-type-badges">
                                                   ' . $payment_output . $word_output . '
                                               </div>
                                               <div class="mj-p-poster-name ">
                                                   <div class="mj-p-poster-title text-zip">
                                                    ' . $title . '
                                                   </div>
                                                   <span class="d-block">' . Utils::timeElapsedString('@' . $item->poster_update_date) . '</span>
                                               </div>
                    
                                               <div class="mj-p-poster-price">
                                                      ' . $price_output . '
                                               </div>
                                           </div>
                                       </div>
                                   </a>
                               </div>';
            }
            
            if (isset($page)) {
                $output .= self::renderPagination($page, $totalCount);
            }
            $response = sendResponse(200, $sql, $output);

        } elseif ($result->status == 204) {
            $response = sendResponse(204, $sql, []);
        }
        return $response;
    }

    private static function renderPagination($page, $totalCount){
        $prevPage = $page - 1 > 0 ? $page - 1 : 1;
        $nextPage = $page + 1;

        $totalPages = $totalCount / 10;
        if ($totalPages > 6) {
            
        }
        $links = "";

        if(($page - 3) > 0){
            $links .= '<li class="page-item"><a class="page-link" href="javascript:void(0)" id="mj-p-get-poster-filters_prev" data-page="' . $page - 3 . '">' . $page - 3 . '</a></li>';
        }
        if(($page - 2) > 0){
            $links .= '<li class="page-item"><a class="page-link" href="javascript:void(0)" id="mj-p-get-poster-filters_prev" data-page="' . $page - 2 . '">' . $page - 2 . '</a></li>';
        }
        if(($page - 1) > 0){
            $links .= '<li class="page-item"><a class="page-link" href="javascript:void(0)" id="mj-p-get-poster-filters_prev" data-page="' . $page - 1 . '">' . $page - 1 . '</a></li>';
        }

        $links .= '<li class="page-item active"><a class="page-link" href="javascript:void(0)" id="mj-p-get-poster-filters_prev" data-page="' . $page. '">' . $page. '</a></li>';

        if(($page + 1) < $totalPages){
            $links .= '<li class="page-item"><a class="page-link" href="javascript:void(0)" id="mj-p-get-poster-filters_prev" data-page="' . $page + 1 . '">' . $page + 1 . '</a></li>';
        }
        if(($page + 2) < $totalPages){
            $links .= '<li class="page-item"><a class="page-link" href="javascript:void(0)" id="mj-p-get-poster-filters_prev" data-page="' . $page + 2 . '">' . $page + 2 . '</a></li>';
        }
        if(($page + 3) < $totalPages){
            $links .= '<li class="page-item"><a class="page-link" href="javascript:void(0)" id="mj-p-get-poster-filters_prev" data-page="' . $page + 3 . '">' . $page + 3 . '</a></li>';
        }

        
        // '.$totalCount.'
        return '<nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm justify-content-center mb-0">

                            <li class="page-item">
                                <a class="page-link" href="javascript:void(0)" id="mj-p-get-poster-filters_prev" data-page="' . $prevPage . '">
                                    <span aria-hidden="true">&laquo;</span>
                                    <span class="sr-only">Previous</span>
                                </a>
                            </li>

                           '.$links.'
                           
                            <li class="page-item">
                                <a class="page-link" href="javascript:void(0)" id="mj-p-get-poster-filters_next" data-page="' . $nextPage . '">
                                    <span aria-hidden="true">&raquo;</span>
                                    <span class="sr-only">Next</span>
                                </a>
                            </li>
                   
                        </ul>
                    </nav>';
    }


    public static function getCountPosterFromCensus($max, $min)
    {

        $sql = "SELECT 
                    (SELECT COUNT(*) FROM `tbl_poster` WHERE `poster_submit_date` <= :max AND `poster_submit_date` >= :min AND `poster_status`=:pending AND `poster_type`='truck') AS pendingT,
                    (SELECT COUNT(*) FROM `tbl_poster` WHERE `poster_submit_date` <= :max AND `poster_submit_date` >= :min AND `poster_status`=:accepted AND `poster_type`='truck') AS acceptedT,
                    (SELECT COUNT(*) FROM `tbl_poster` WHERE `poster_submit_date` <= :max AND `poster_submit_date` >= :min AND `poster_status`=:reject AND `poster_type`='truck') AS rejectT,
                    (SELECT COUNT(*) FROM `tbl_poster` WHERE `poster_submit_date` <= :max AND `poster_submit_date` >= :min AND `poster_status`=:deleted AND `poster_type`='truck') AS deletedT,
                    (SELECT COUNT(*) FROM `tbl_poster` WHERE `poster_submit_date` <= :max AND `poster_submit_date` >= :min AND `poster_status`=:expired AND `poster_type`='truck') AS expiredT,
                    (SELECT COUNT(*) FROM `tbl_poster` WHERE `poster_submit_date` <= :max AND `poster_submit_date` >= :min AND `poster_status`=:needed AND `poster_type`='truck') AS neededT,
                    (SELECT COUNT(*) FROM `tbl_poster` WHERE `poster_submit_date` <= :max AND `poster_submit_date` >= :min AND `poster_status`=:pending AND `poster_type`='trailer') AS pending,
                    (SELECT COUNT(*) FROM `tbl_poster` WHERE `poster_submit_date` <= :max AND `poster_submit_date` >= :min AND `poster_status`=:accepted AND `poster_type`='trailer') AS accepted,
                    (SELECT COUNT(*) FROM `tbl_poster` WHERE `poster_submit_date` <= :max AND `poster_submit_date` >= :min AND `poster_status`=:reject AND `poster_type`='trailer') AS reject,
                    (SELECT COUNT(*) FROM `tbl_poster` WHERE `poster_submit_date` <= :max AND `poster_submit_date` >= :min AND `poster_status`=:deleted AND `poster_type`='trailer') AS deleted,
                    (SELECT COUNT(*) FROM `tbl_poster` WHERE `poster_submit_date` <= :max AND `poster_submit_date` >= :min AND `poster_status`=:expired AND `poster_type`='trailer') AS expired,
                    (SELECT COUNT(*) FROM `tbl_poster` WHERE `poster_submit_date` <= :max AND `poster_submit_date` >= :min AND `poster_status`=:needed AND `poster_type`='trailer') AS needed
       
                    FROM tbl_poster ";
        $params = [
            'max' => $max,
            'min' => $min,
            'pending' => "pending",
            'accepted' => "accepted",
            'reject' => "reject",
            'deleted' => "deleted",
            'expired' => "expired",
            'needed' => "needed",
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            return [
                'pendingT' => $result->response[0]->pendingT,
                'acceptedT' => $result->response[0]->acceptedT,
                'rejectT' => $result->response[0]->rejectT,
                'deletedT' => $result->response[0]->deletedT,
                'expiredT' => $result->response[0]->expiredT,
                'neededT' => $result->response[0]->neededT,
                'pending' => $result->response[0]->pending,
                'accepted' => $result->response[0]->accepted,
                'reject' => $result->response[0]->reject,
                'deleted' => $result->response[0]->deleted,
                'expired' => $result->response[0]->expired,
                'needed' => $result->response[0]->needed,
            ];
        }

        return [
            'pendingT' => 0,
            'acceptedT' => 0,
            'rejectT' => 0,
            'deletedT' => 0,
            'expiredT' => 0,
            'neededT' => 0,
            'pending' => 0,
            'accepted' => 0,
            'reject' => 0,
            'deleted' => 0,
            'expired' => 0,
            'needed' => 0
        ];
    }


    public static function getReasonDeletedPosterFromCensus($max, $min)
    {
        $response = [];
        $sql = "SELECT * FROM `tbl_poster` INNER JOIN `tbl_poster_reason_delete` ON tbl_poster.delete_id=tbl_poster_reason_delete.category_id WHERE  `poster_status`='deleted' AND `poster_submit_date` <= :max AND `poster_submit_date` >= :min AND tbl_poster.delete_id IS NOT NULL";
        $params = [
            'max' => $max,
            'min' => $min,
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = $result->response;
        }
        return $response;

    }


    public static function updatePosterTitle($language, $poster_id, $title)
    {
        $response = sendResponse(0, 'ERR');

        $sql = " update tbl_poster set poster_title_$language = '$title' where poster_id = $poster_id";
        $params = [];

        $result = DB::update($sql, $params);

        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'success');
        }
        return $response;
    }
}