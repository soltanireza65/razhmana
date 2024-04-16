<?php


use MJ\Database\DB;
use MJ\Security\Security;
use MJ\Utils\Utils;
use function MJ\Keys\sendResponse;

class AUser
{

    /**
     * Get All Users
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getAllUsers()
    {

        $response = sendResponse(0, "Error Msg");

        $params = [];
        $sql = "SELECT * FROM `tbl_users` ORDER BY user_id ASC ;";

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get User Info By Id
     * @param $id int
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getUserInfoById($id)
    {

        $response = sendResponse(0, "");
        $sql = "SELECT * FROM `tbl_users` WHERE user_id=:user_id ";
        $params = [
            'user_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Get User Logs By Id
     * @param $id int
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getUserLogsById($id)
    {

        $response = sendResponse(0, "");


        if ($id == 0) {
            $sql = "SELECT * FROM `tbl_user_log` ORDER BY log_id DESC ;";
            $result = DB::rawQuery($sql, []);
        } else {
            $sql = "SELECT * FROM `tbl_user_log` WHERE user_id=:user_id  ORDER BY log_id DESC ;";
            $result = DB::rawQuery($sql, ['user_id' => $id]);
        }

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Update User Ststus
     * @param $status
     * @param $userID
     * @return stdClass
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function updateUserStatus($userID, $status)
    {
        $response = sendResponse(0, "");

        $sql = "UPDATE `tbl_users` SET `user_status`=:user_status WHERE user_id=:user_id";
        $params = [
            'user_status' => $status,
            'user_id' => $userID,
        ];
        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }

    /**
     * Update User Ststus
     * @param $status
     * @param $userID
     * @return stdClass
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function updateUserClass($userID, $status)
    {
        $response = sendResponse(0, "");
        $sql = "UPDATE `tbl_users` SET `user_class`=:user_class WHERE user_id=:user_id";
        $params = [
            'user_class' => $status,
            'user_id' => $userID,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }
        return $response;
    }


    /**
     * Delete User Active Session
     * @param $userId
     * @param $number
     * @param $expire
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function deleteUserSession($userId, $number, $expire)
    {
        $response = sendResponse(0, "");


        $resultUserInfoById = AUser::getUserInfoById($userId);
        $dataUserInfoById = [];
        $data = [];
        $temp = [];
        if ($resultUserInfoById->status == 200 && !empty($resultUserInfoById->response) && isset($resultUserInfoById->response[0]) && !empty($resultUserInfoById->response[0])) {
            $dataUserInfoById = $resultUserInfoById->response[0]->user_active_session;

            if (!empty($dataUserInfoById)) {
                $temp = json_decode($dataUserInfoById, true);
                foreach ($temp as $index => $tempITEM) {
                    if ($index == $number - 1 && $tempITEM['expire'] == $expire) {
                        unset($temp[$index]);
                    }
                }

                foreach ($temp as $tempLoop) {
                    $data[] = $tempLoop;
                }


                $sql = "UPDATE `tbl_users` SET `user_active_session`=:user_active_session WHERE user_id=:user_id";
                $params = [
                    'user_active_session' => json_encode($data),
                    'user_id' => $userId,
                ];
                $result = DB::update($sql, $params);

                if ($result->status == 200) {
                    $response = sendResponse(200, "");
                }


            }

        }
        return $response;
    }


    /**
     * Update User Gift
     * @param $userId
     * @param $count
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function updateUserGiftValue($userId, $count)
    {
        $response = sendResponse(0, "Error");
        $sql = "UPDATE `tbl_users` SET `user_gift`= user_gift + :user_gift WHERE user_id=:user_id";
        $params = [
            'user_gift' => $count,
            'user_id' => $userId,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    /**
     * Update User Score
     * @param $userId
     * @param $count
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function updateUserScoreValue($userId, $count)
    {
        $response = sendResponse(0, "");
        $sql = "UPDATE `tbl_users` SET `user_score`=user_score + :user_score WHERE user_id=:user_id";
        $params = [
            'user_score' => $count,
            'user_id' => $userId,
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }
        return $response;
    }


    /**
     * Get Card Bank
     * @param null $status
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getBankCard($status = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($status)) {
            $sql = "SELECT * FROM `tbl_bank_card` ORDER BY card_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_bank_card` WHERE `card_status`=:card_status ORDER BY card_id DESC ;";
            $params = [
                'card_status' => $status
            ];
        }


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get Bank Card By ID
     * @param $id
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getBankCardByID($id)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_bank_card` WHERE `card_id`=:card_id  ;";
        $params = [
            'card_id' => $id
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Update Bank Card By Value Key
     * @param $cardID
     * @param $status
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function updateCreditByID($cardID, $status)
    {
        $response = sendResponse(0, "Error Msg");


        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $res = self::getBankCardByID($cardID);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->card_options;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];
            $a['admin'] = $admin_id;
            $a['status'] = $status;
            $a['date'] = time();
            array_push($array['update'], $a);

        } else {
            $a = [];
            $array['update'] = [];

            $a['admin'] = $admin_id;
            $a['status'] = $status;
            $a['date'] = time();
            array_push($array['update'], $a);
        }


        if ($admin_id > 0) {

            $sql = "UPDATE `tbl_bank_card` SET `card_status`=:card_status,`card_options`=:card_options WHERE card_id=:card_id";
            $params = [
                'card_status' => $status,
                'card_options' => json_encode($array),
                'card_id' => $cardID,
            ];
            $result = DB::update($sql, $params);

            if ($result->status == 200 || $result->status == 208) {
                $response = sendResponse(200, "Successful");
            }
        }
        return $response;
    }


    /**
     * Delete Card Bank By Id
     * @param $cardID
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function deleteCreditByID($cardID)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "DELETE FROM `tbl_bank_card` WHERE card_id=:card_id";
        $params = [
            'card_id' => $cardID,
        ];
        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }


    /**
     * Get Multiple Users By IDs
     * @param $ids
     * @return stdClass
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getMultipleUserByID($ids)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_users` WHERE user_id IN ({$ids})";
        $params = [
//            'admin_id' => $ids
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get Count Users By Status
     * @param $status
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getCountUsers($status)
    {
        $response = sendResponse(0, "Error Msg", 0);

        $sql = "SELECT count(*) AS count FROM `tbl_users` WHERE user_status=:user_status";
        $params = [
            'user_status' => $status,
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $count = 0;
            if (isset($result->response[0]->count)) {
                $count = (int)$result->response[0]->count;
            }
            $response = sendResponse(200, "Successful", $count);
        }

        return $response;
    }


    /**
     * Get Balances
     * @param null $userID
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getAllBalances($userID = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($userID)) {
            $sql = "SELECT * FROM `tbl_balance` ORDER BY balance_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_balance` WHERE `user_id`=:user_id ;";
            $params = [
                'user_id' => $userID
            ];
        }


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get USer All Card Bank
     * @param $UserID
     * @return Object
     * @author  Amir
     * @version 1.0.0
     */
    public static function getUserBankCard($UserID)
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_bank_card` WHERE `user_id`=:user_id ORDER BY card_id DESC ;";
        $params = [
            'user_id' => $UserID
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }


        return $response;
    }


    /**
     * Get Balance InFo By Balance ID
     * @param $balanceID
     * @return Object
     * @author  Amir
     * @version 1.0.0
     */
    public static function getBalanceInfoById($balanceID)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_balance` WHERE `balance_id`=:balance_id ;";
        $params = [
            'balance_id' => $balanceID
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }


        return $response;
    }


    /**
     * Update User Balance By Balance ID
     * @param $balanceID
     * @param $balanceValue
     * @return Object
     * @author  Amir
     * @version 1.0.0
     */
    public static function editUserBalanceByAdmin($balanceID, $balanceValue, $balanceFrozen)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "UPDATE `tbl_balance` SET `balance_value`=:balance_value,`balance_frozen`=:balance_frozen WHERE `balance_id`=:balance_id";
        $params = [
            'balance_value' => $balanceValue,
            'balance_frozen' => $balanceFrozen,
            'balance_id' => $balanceID,
        ];

        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }

        return $response;
    }


    /**
     * Add New Balance From User
     * @param $userID
     * @param $currencyType
     * @param $balanceValue
     * @param $balanceFrozen
     * @return Object
     * @author Amir
     * @version 2.0.0
     */
    public static function addUserBalanceByAdmin($userID, $currencyType, $balanceValue, $balanceFrozen)
    {
        $response = sendResponse(0, "Error Msg");


        // Get User Balances By Id

        $resultAllBalances = self::getAllBalances($userID);
        $dataAllBalances = [];
        if ($resultAllBalances->status == 200 && !empty($resultAllBalances->response)) {
            $dataAllBalances = $resultAllBalances->response;
        }

        if (!empty($dataAllBalances)) {
            foreach ($dataAllBalances as $dataAllBalancesLOOP) {
                if ($dataAllBalancesLOOP->currency_id == $currencyType) {
                    return sendResponse(220, "Error Msg - Currency Exist");
                }
            }
        }

        $sql = "INSERT INTO `tbl_balance`(`user_id`, `currency_id`, `balance_value`, `balance_frozen`) 
                VALUES (:user_id,:currency_id,:balance_value,:balance_frozen)";
        $params = [
            'user_id' => $userID,
            'currency_id' => $currencyType,
            'balance_value' => $balanceValue,
            'balance_frozen' => $balanceFrozen,
        ];

        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Edit OR Insert Auth Field From User
     * @param $userType
     * @param $UserID
     * @param $field
     * @param $newValue
     * @param $fileId
     * @param $option_id
     * @return Object
     * @author Amir
     * @version 2.0.0
     */
    public static function editOrInsertUserAuthenticationByAdmin($userId, $slug, $status, $btnType, $newValue = null)
    {
        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $userINFO = self::getUserInfoById($userId);
        if ($userINFO->status == 200 && !empty($userINFO->response)) {

            $settingScore = (Utils::getFileValue("settings.txt", "auth_" . $slug)) ? Utils::getFileValue("settings.txt", "auth_" . $slug) : 5;

            $sql = "SELECT option_options , count(*) AS count FROM `tbl_user_options` WHERE `user_id`=:user_id AND `option_slug`=:option_slug";
            $params = [
                'user_id' => $userId,
                'option_slug' => $slug,
            ];

            $result = DB::rawQuery($sql, $params);
            $temp = '';
            $resIdCount = 0;
            if ($result->status == 200 && !empty($result->response) && isset($result->response[0]->count)) {
                $resIdCount = $result->response[0]->count;
                $temp = $result->response[0]->option_options;
            }


            if ($resIdCount > 0) {
                // update

                $array = [];
                if (!empty($temp)) {
                    $array = json_decode($temp, true);
                    $a = [];
                    $a['admin'] = $admin_id;
                    $a['value'] = $newValue;
                    $a['status'] = $status;
                    $a['date'] = time();
                    array_push($array['update'], $a);

                } else {
                    $a = [];
                    $array['update'] = [];
                    $a['admin'] = $admin_id;
                    $a['value'] = $newValue;
                    $a['status'] = $status;
                    $a['date'] = time();
                    array_push($array['update'], $a);
                }

                if ($btnType == "yes") {

                    $sqlUPDATE = "UPDATE `tbl_user_options` SET `option_value`=:option_value,`option_status`=:option_status,
                        `option_submit_date`=:option_submit_date,`option_options`=:option_options
                        WHERE `user_id`=:user_id AND `option_slug`=:option_slug";
                    $paramsUPDATE = [
                        'option_value' => $newValue,
                        'option_status' => $status,
                        'option_submit_date' => time(),
                        'option_options' => json_encode($array),
                        'user_id' => $userId,
                        'option_slug' => $slug,
                    ];
                } else {
                    $sqlUPDATE = "UPDATE `tbl_user_options` SET `option_status`=:option_status,
                        `option_submit_date`=:option_submit_date,`option_options`=:option_options
                        WHERE `user_id`=:user_id AND `option_slug`=:option_slug";
                    $paramsUPDATE = [
                        'option_status' => $status,
                        'option_submit_date' => time(),
                        'option_options' => json_encode($array),
                        'user_id' => $userId,
                        'option_slug' => $slug,
                    ];
                }


                $resultUPDATE = DB::update($sqlUPDATE, $paramsUPDATE);
                if ($resultUPDATE->status == 200 || $resultUPDATE->status == 208) {
                    $response = sendResponse(200, "Successful",$resultUPDATE);
                    if ($status == "accepted") {
                        $x = self::UpdateUSerOption($userId, $settingScore);
                    }
                }


            } else {

                $array = [];

                $a = [];
                $array['update'] = [];
                $a['admin'] = $admin_id;
                $a['value'] = $newValue;
                $a['status'] = $status;
                $a['date'] = time();
                array_push($array['update'], $a);


                // insert
                $sqlINSERT = "INSERT INTO `tbl_user_options`(`user_id`, `option_slug`, `option_value`, `option_status`, `option_submit_date`,
                               `option_options`) VALUES (:user_id,:option_slug,:option_value,:option_status,:option_submit_date,
                             :option_options)";
                $paramsINSERT = [
                    'user_id' => $userId,
                    'option_slug' => $slug,
                    'option_value' => $newValue,
                    'option_status' => $status,
                    'option_submit_date' => time(),
                    'option_options' => json_encode($array),
                ];


                $resultINSERT = DB::insert($sqlINSERT, $paramsINSERT);
                if ($resultINSERT->status == 200) {
                    $response = sendResponse(200, "Successful",'$resultINSERT');
                    if ($status == "accepted") {
                        $x = self::UpdateUSerOption($userId, $settingScore);
                    }
                }

            }
        }
        return $response;
    }


    /**
     * Update USer Option
     * @param $userID
     * @param $option
     * @return bool
     */
    private static function UpdateUSerOption($userID, $score)
    {
        $sql = 'UPDATE `tbl_users` SET `user_score`=user_score + :user_score WHERE `user_id`=:user_id';
        $params = [
            'user_score' => $score,
            'user_id' => $userID,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            return true;
        }
        return false;
    }


    /**
     * Insert inquiry Card Bank
     * @param $creditId
     * @param $creditNumber
     * @return stdClass
     */
    public static function inquiryCardBank($creditId, $creditNumber)
    {
        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        if ($admin_id > 0) {
            $result = InquiryJibit::convertCardToIBAN($creditNumber);
            if (@json_decode($result) && isset(json_decode($result)->ibanInfo)) {

                $temp = json_decode($result);
                $sql = "INSERT INTO `tbl_inquiry_card`(`card_id`, `admin_id`, `inquiry_owner_first_name`, `inquiry_owner_last_name`,
                               `inquiry_number`, `inquiry_deposit_number`, `inquiry_iban`, `inquiry_bank`, `inquiry_status`,
                               `inquiry_submit_date`) 
                               VALUES (:card_id,:admin_id,:inquiry_owner_first_name,:inquiry_owner_last_name,:inquiry_number,
                                       :inquiry_deposit_number,:inquiry_iban,:inquiry_bank,:inquiry_status,:inquiry_submit_date)";
                $params = [
                    'card_id' => $creditId,
                    'admin_id' => $admin_id,
                    'inquiry_owner_first_name' => $temp->ibanInfo->owners[0]->firstName,
                    'inquiry_owner_last_name' => $temp->ibanInfo->owners[0]->lastName,
                    'inquiry_number' => $creditNumber,
                    'inquiry_deposit_number' => $temp->ibanInfo->depositNumber,
                    'inquiry_iban' => $temp->ibanInfo->iban,
                    'inquiry_bank' => $temp->ibanInfo->bank,
                    'inquiry_status' => $temp->ibanInfo->status,
                    'inquiry_submit_date' => time(),
                ];

                $result = DB::insert($sql, $params);

                if ($result->status == 200) {
                    $response = sendResponse(200, "Successful");
                }

            } else {
                $response = sendResponse(300, "error in inquiry");
            }
        }
        return $response;
    }


    /**
     * get Inquiry Card Bank By Card Id
     * @param $creditId
     * @return stdClass
     */
    public static function getInquiryCardBankByCardId($creditId)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_inquiry_card` WHERE `card_id`=:card_id";
        $params = [
            'card_id' => $creditId
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * get User Auth Options
     * @param $userId
     * @return stdClass
     */
    public static function getUserAuthOptions($userId)
    {
        $response = sendResponse(200, '', []);

        $sql = "select * from tbl_user_options where user_id = :userId;";
        $params = [
            'userId' => $userId
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $fields = [];
            foreach ($result->response as $item) {
                $option = new stdClass();
                $option->option_id = $item->option_id;
                $option->option_slug = $item->option_slug;
                $option->option_value = $item->option_value;
                $option->option_status = $item->option_status;
                $option->option_submit_date = $item->option_submit_date;

                $fields[$item->option_slug] = $option;
            }
            $response = sendResponse(200, '', $fields);
        }
        return $response;
    }


    /**
     * Update User Auth Status
     * @param $status
     * @param $userID
     * @return stdClass
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function updateUserAuthStatus($userID, $status)
    {
        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $sql = "UPDATE `tbl_users` SET `user_auth_status`=:user_auth_status,`user_auth_admin`=:user_auth_admin,
                    `user_auth_admin_time`=:user_auth_admin_time WHERE user_id=:user_id";
        $params = [
            'user_auth_status' => $status,
            'user_id' => $userID,
            'user_auth_admin' => $admin_id,
            'user_auth_admin_time' => time(),
        ];
        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    /**
     * update User Inquiry Status
     * @param $userID
     * @param $idCard
     * @param $status
     * @return stdClass
     */
    public static function updateUserInquiryStatus($userID, $idCard, $status)
    {
        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $sql = "UPDATE `tbl_users` SET `user_inquiry_status`=:user_inquiry_status,`user_inquiry_admin`=:user_inquiry_admin,
                `user_inquiry_admin_time`=:user_inquiry_admin_time,`user_inquiry_id_card`=:user_inquiry_id_card 
                WHERE `user_id`=:user_id";
        $params = [
            'user_inquiry_status' => $status,
            'user_inquiry_admin' => $admin_id,
            'user_inquiry_admin_time' => time(),
            'user_inquiry_id_card' => Security::encrypt($idCard),
            'user_id' => $userID,
        ];
        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    /**
     * get Count Users Auth Status
     * @param $status
     * @return stdClass
     */
    public static function getCountUsersAuthStatus($status)
    {
        $response = sendResponse(0, "Error Msg", 0);

        $sql = "SELECT count(*) AS count FROM `tbl_users` WHERE user_auth_status=:user_auth_status";
        $params = [
            'user_auth_status' => $status,
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $count = 0;
            if (isset($result->response[0]->count)) {
                $count = (int)$result->response[0]->count;
            }
            $response = sendResponse(200, "Successful", $count);
        }

        return $response;
    }


    /**
     * get Count Users Optional AuthStatus
     * @param $status
     * @return stdClass
     */
    public static function getCountUsersOptionalAuthStatus($status)
    {
        $count = 0;
        $sql = "SELECT DISTINCT `user_id`  FROM `tbl_user_options` WHERE option_status=:option_status ";
        $params = [
            'option_status' => $status,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $count = count($result->response);
        }
        return $count;
    }


    public static function getCountUserFromCensus($max,$min)
    {

        $sql = "SELECT 
                    (SELECT COUNT(*) FROM `tbl_users` WHERE `user_register_date` <= :max AND `user_register_date` >= :min AND user_type=:type_businessman) AS businessmanCount,
                    (SELECT COUNT(*) FROM `tbl_users` WHERE `user_register_date` <= :max AND `user_register_date` >= :min AND user_type=:type_guest) AS guestCount,
                    (SELECT COUNT(*) FROM `tbl_users` WHERE `user_register_date` <= :max AND `user_register_date` >= :min AND user_type=:type_driver) AS driverCount
                    FROM tbl_users WHERE `user_register_date` <= :max AND `user_register_date` >= :min";
        $params = [
            'max' => $max,
            'min' => $min,
            'type_businessman' => "businessman",
            'type_guest' => "guest",
            'type_driver' => "driver",
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            return [
                'businessman' => $result->response[0]->businessmanCount,
                'guest' => $result->response[0]->guestCount,
                'driver' => $result->response[0]->driverCount,
            ];
        }

        return [
            'businessman' => 0,
            'guest' => 0,
            'driver' => 0,
        ];
    }




    public static function getUserReferrals($code){

        $response = sendResponse(404, '', null);
        $sql = "SELECT * FROM `tbl_users` WHERE user_referral_code=:code;";
        $params = [
            'code' => $code,
        ];
        $result = DB::rawQuery($sql, $params);
        if($result->status==200){
            $response = sendResponse(200, '', $result->response);
        }
        return $response;
    }
}

