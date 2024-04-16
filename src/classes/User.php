<?php


use MJ\Database\DB;
use MJ\Router\Router;
use MJ\Security\Security;
use MJ\SMS\SMS;
use MJ\Utils\Utils;
use function MJ\Keys\sendResponse;

class User
{
    private static $userTypeList = [
        'driver' => 'راننده',
        'businessman' => 'تاجر',
        'guest' => 'مهمان'
    ];


    private static $userStatusList = [
        'active' => 'عادی',
        'inactive' => 'غیر فعال',
        'guest' => 'کاربر مهمان',
        'suspend' => 'محدود شده',
    ];


    private static $defaultDriverOptions = [
        'score' => 0,
        'rate' => 0,
        'rate_count' => 0,
        'gift' => 0,
    ];


    private static $defaultBusinessmanOptions = [
        'score' => 0,
        'rate' => 0,
        'rate_count' => 0,
        'gift' => 0,
    ];


    /**
     * @param $mobile
     *
     * @return bool
     */
    private static function checkUserExists($mobile)
    {
        $sql = "select count(*) as count
        from tbl_users 
        where user_mobile = :mobile;";
        $params = [
            'mobile' => Security::encrypt($mobile)
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            if ($result->response[0]->count > 0) {
                return true;
            } else {
                return false;
            }
        }
        return self::checkUserExists($mobile);
    }


    /**
     * @return bool
     */
    public static function userIsLoggedIn()
    {
        if (isset($_COOKIE['user-login'])) {
            $cookie = json_decode(Security::decrypt($_COOKIE['user-login']));
            if (isset($cookie->UserId) && isset($cookie->UserMobile)) {
                if (self::checkUserExists($cookie->UserMobile)) {
                    $user = self::getUserInfo($cookie->UserId);
                    if (in_array($user->UserStatus, ['active', 'suspend', 'guest'])) {
                        self::updateActiveSession($cookie->UserId);
                        $validation = self::loginValidSystem($cookie->UserMobile)->message;

                        return true;
                        /*if (str_contains($validation, $user->UserType) && $user->UserType == 'businessman') {
                            switch (str_contains(Router::getCurrentUri(), 'driver')) {
                                case true:
                                    return false;
                                default:
                                    return true;
                            }
                        } elseif (str_contains($validation, $user->UserType) && $user->UserType == 'driver') {
                            switch (str_contains(Router::getCurrentUri(), 'businessman')) {
                                case true:
                                    return false;
                                default:
                                    return true;
                            }
                        } else {
                            return true;
                        }*/
                    }
                }
            }
            setcookie('user-login', null, -1, '/');
        }
        return false;
    }

    /**
     * @return bool
     */
    public static function checkUserSlugAccess()
    {
        if (isset($_COOKIE['user-login'])) {
            $user = self::getUserInfo();
            if (str_contains(Router::getCurrentUri(), $user->UserType)) {

            } else {
                header('location:/login');
            }
        }
    }


    /**
     * @param $mobile
     * @param $type
     *
     * @return bool
     */
    public static function registerUser($mobile, $type, $user_name, $user_lname, $token, $user_referral = null, $mobileCode = null, $mobileNumber = null)
    {
        if (!Security::verifyCSRF('token_register', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }

        $sql = "insert into tbl_users(user_mobile,user_firstname , user_lastname, user_type, user_score,user_rate,user_rate_count,user_gift, user_language, user_status, user_register_date,user_referral_code,user_unique_code)
        VALUES (:mobile,:user_firstname,:user_lastname, :type, :user_score,:user_rate,:user_rate_count,:user_gift, :lang, :status, :time,:user_referral_code,:user_unique_code);";
        $params = [
            'mobile' => Security::encrypt($mobile),
            'user_firstname' => Security::encrypt($user_name),
            'user_lastname' => Security::encrypt($user_lname),
            'type' => $type,
            'user_score' => 0,
            'user_rate' => 0,
            'user_rate_count' => 0,
            'user_gift' => 0,
            'lang' => 'fa_IR',
            'status' => 'active',
            'time' => time(),
            'user_referral_code' => $user_referral,
            'user_unique_code' => self::generateReferralCode2($mobile, $mobileCode, $mobileNumber),
        ];
        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $user_id = self::getUserId($mobile);
            $cookie = json_encode([
                'UserId' => $user_id,
                'UserMobile' => $mobile,
                'UserType' => 'guest',
            ]);
            self::createUserLog($user_id, 'uLog_login_to_system', 'login');
            self::createLoginSession($user_id);
            unset($_SESSION['userId']);
            unset($_SESSION['mobile']);
            unset($_SESSION['userType']);
            setcookie('user-login', Security::encrypt($cookie), USER_COOKIE_TIMEOUT, '/');
            setcookie('user-type', 'guest', USER_COOKIE_TIMEOUT, '/');
            self::createUserLog($result->response, 'uLog_register', 'register');
            return $result->response;
        }
        return false;
    }


    /**
     * @param $mobile
     * @param $type
     *
     * @return stdClass
     */
    private static function loginValidSystem($mobile)
    {
        if (self::checkUserExists($mobile)) {
            $user_info = self::getUserInfo(self::getUserId($mobile));
            $response = sendResponse(200, $user_info->UserType);
        } else {
            $response = sendResponse(-1, 'register');
        }
        return $response;
    }


    /**
     * @param $mobile
     * @param $type
     * @param $token
     *
     * @return stdClass
     */
    public static function loginUser($mobile, $type, $token)
    {
        if (!Security::verifyCSRF('login', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('login');

        $loginValidSystem = self::loginValidSystem($mobile);

        $isLogin = ($loginValidSystem->status == 200) ? true : false;
        $sms = SMS::sendOTP($mobile);
        if ($sms->status == 200) {
            $_SESSION['OTP'] = $sms->response;
            if ($isLogin) {
                $_SESSION['mobile'] = $mobile;
                $_SESSION['userId'] = self::getUserId($mobile);
                $_SESSION['userType'] = $type;
                $response = sendResponse(200, 'Logged in as exist user', $csrf);
            } else {
                $_SESSION['mobile'] = $mobile;
                $response = sendResponse(201, 'logged in as new user', $csrf);
            }
        } else {
            $response = sendResponse(-20, $sms, $csrf);
        }
        return $response;
    }


    /**
     * @param $code
     * @param $token
     *
     * @return stdClass
     */
    public static function verifyOTP($code, $status, $token)
    {

        if (!Security::verifyCSRF('otp', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('otp');

        $verify = SMS::verifyOTP($code);
        if ($verify->status == 200) {
            if ($status == 'register') {
                setcookie('can-register', true, USER_COOKIE_TIMEOUT, '/');
                $response = sendResponse(201, 'can register', $csrf);
            } else {
                $user_type = self::getUserType($_SESSION['userId']);
                $cookie = json_encode([
                    'UserId' => $_SESSION['userId'],
                    'UserMobile' => $_SESSION['mobile'],
                    'UserType' => 'guest',
                ]);
                self::createUserLog($_SESSION['userId'], 'uLog_login_to_system', 'login');
                self::createLoginSession($_SESSION['userId']);
                unset($_SESSION['userId']);
                unset($_SESSION['mobile']);
                unset($_SESSION['userType']);
                setcookie('user-login', Security::encrypt($cookie), USER_COOKIE_TIMEOUT, '/');
                setcookie('user-type', $user_type, USER_COOKIE_TIMEOUT, '/');

                $response = sendResponse(200, 'Login successfully', $csrf);
            }


        } else {
            $response = sendResponse(-10, 'OTP wrong', $csrf);
        }
        return $response;
    }

    public static function getUserType($user_id)
    {
        $sql = "select user_type
        from tbl_users 
        where user_id = :user_id;";
        $params = [
            'user_id' => $user_id
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            return $result->response[0]->user_type;
        } else {
            return self::getUserType($user_id);
        }
    }

    /**
     * @param $mobile
     *
     * @return int|null
     */
    public static function getUserId($mobile)
    {
        $sql = "select user_id
        from tbl_users 
        where user_mobile = :mobile;";
        $params = [
            'mobile' => Security::encrypt($mobile)
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            return $result->response[0]->user_id;
        } elseif ($result->status == 204) {
            return null;
        }
        return self::getUserId($mobile);
    }


    /**
     * @param null $userId
     *
     * @return stdClass
     */
    public static function getUserInfo($userId = null)
    {
        $user = new stdClass();

        $userId = (!empty($userId) && !is_null($userId)) ? $userId : json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;

        $sql = "select * from tbl_users where user_id = :userId;";
        $params = [
            'userId' => $userId
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            foreach ($result->response as $item) {
                $user->UserId = $item->user_id;
                $user->UserMobile = Security::decrypt($item->user_mobile);
                $user->UserFirstName = Security::decrypt($item->user_firstname);
                $user->UserLastName = Security::decrypt($item->user_lastname);
                $user->UserDisplayName = (!empty($user->UserFirstName) && !empty($user->UserLastName)) ? "{$user->UserFirstName} {$user->UserLastName}" : 'کاربر مهمان';
                $user->UserType = $item->user_type;
                $user->UserDisplayType = self::$userTypeList[$item->user_type];
                $user->UserScore = $item->user_score;
                $user->UserRate = $item->user_rate;
                $user->UserRateCount = $item->user_rate_count;
                $user->UserGift = $item->user_gift;
                $user->UserLat = $item->user_lat;
                $user->UserLong = $item->user_long;
                $user->UserTypeCard = $item->user_type_card;
                $user->UserNumberCard = Security::decrypt($item->user_number_card);
                $user->UserAuthStatus = $item->user_auth_status;
                $user->UserLanguage = $item->user_language;
                $user->UserAvatar = $item->user_avatar;
                $user->UserActiveSessions = json_decode($item->user_active_session);
                $user->UserStatus = $item->user_status;

                $user->UserDisplayStatus = self::$userStatusList[$item->user_status];
                $user->UserRegisterDate = $item->user_register_date;
                $user->UserClass = $item->user_class;
            }
        }
        return $user;
    }


    /**
     * @param $userId
     * @param $key
     * @param $value
     *
     * @return stdClass
     */
    public static function updateUserOptions($userId, $key, $value)
    {
        $response = sendResponse(-10, 'Update Err');

        if ($key == "user_score") {
            $sql = "update tbl_users set user_score =user_score +  :options where user_id = :userId;";
        } elseif ($key == "user_rate") {
            $sql = "update tbl_users set user_rate =user_rate +  :options where user_id = :userId;";
        } elseif ($key == "user_rate_count") {
            $sql = "update tbl_users set user_rate_count =user_rate_count +  :options where user_id = :userId;";
        } elseif ($key == "user_gift") {
            $sql = "update tbl_users set user_gift =user_gift +  :options where user_id = :userId;";
        } else {
            $sql = "";
        }

        $params = [
            'userId' => $userId,
            'options' => $value
        ];

        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Updated successfully');
            self::createUserLog($userId, "uLog_edit_{$key}", 'edit_options');
        } elseif ($result->status == 208) {
            $response = sendResponse(208, 'It has already been updated');
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $avatar
     * @param $token
     *
     * @return stdClass
     */
    public static function changeAvatar($userId, $avatar, $token)
    {
        if (!Security::verifyCSRF('avatar', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('avatar');

        $sql = "update tbl_users set user_avatar = :avatar
                where user_id = :userId";
        $params = [
            'userId' => $userId,
            'avatar' => $avatar
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'Avatar changed successfully');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }


    /**
     * @param $fieldId
     *
     * @return stdClass
     */
    /*   private static function getAuthField($fieldId)
       {
           $response = sendResponse(404, '', null);

           $sql = "select * from
           tbl_user_fields
           where field_id = :fieldId;";
           $params = [
               'fieldId' => $fieldId
           ];
           $result = DB::rawQuery($sql, $params);
           if ($result->status == 200) {
               $field = new stdClass();
               foreach ($result->response as $item) {
                   $field->FieldId = $item->field_id;
                   $field->FieldName = $item->field_name;
                   $field->FieldRequired = $item->field_required;
                   $field->FieldType = $item->field_input_type;
                   $field->FieldMode = $item->field_input_mode;
               }
               $response = sendResponse(200, '', $field);
           }
           return $response;
       }*/


    /**
     * @param $userId
     * @param $firstName
     * @param $token
     *
     * @return stdClass
     */
    public static function updateUserFirstName($userId, $firstName, $token)
    {
        if (!Security::verifyCSRF('edit-name', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('edit-name');

        $sql = "update tbl_users set
        user_firstname = :fName, user_status = :status
        where user_id = :userId;";
        $params = [
            'userId' => $userId,
            'fName' => Security::encrypt($firstName),
            'status' => 'active'
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'First name changed successfully', $csrf);
            self::createUserLog($userId, 'uLog_auth_first_name', 'auth');
        } elseif ($result->status == 208) {
            $response = sendResponse(208, 'Changes already applied', $csrf);
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $lastName
     * @param $token
     *
     * @return stdClass
     */
    public static function updateUserLastName($userId, $lastName, $token)
    {
        if (!Security::verifyCSRF('edit-name', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('edit-name');

        $sql = "update tbl_users set
        user_lastname = :lName, user_status = :status
        where user_id = :userId;";
        $params = [
            'userId' => $userId,
            'lName' => Security::encrypt($lastName),
            'status' => 'active'
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Last name changed successfully', $csrf);
            self::createUserLog($userId, 'uLog_auth_last_name', 'auth');
        } elseif ($result->status == 208) {
            $response = sendResponse(208, 'Changes already applied', $csrf);
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }
    public static function updateUserRefferal($user_id ,$user_reffral)
    {


        $sql = "update tbl_users set
        user_referral_code = :user_referral_code 
        where user_id = :userId;";
        $params = [
            'userId' => $user_id,
            'user_referral_code' => $user_reffral,

        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 ||$result->status == 208) {
            $response = sendResponse(200, 'Last name changed successfully');
            Admin::SetAdminLog("change_user_refferal_" . $user_reffral . "_" . $user_id, "change_user_refferal");
        } else {
            $response = sendResponse(-10, 'Error');
        }
        return $response;
    }


    /**
     * @param $userId
     *
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
                $option->OptionId = $item->option_id;
                $option->OptionSlug = $item->option_slug;
                $option->OptionValue = $item->option_value;
                $option->OptionStatus = $item->option_status;

                $fields[$item->option_slug] = $option;
            }
            $response = sendResponse(200, '', $fields);
        }
        return $response;
    }


    /**
     * @param $userId
     *
     * @return stdClass
     */
    public static function getNotifications($userId)
    {
        $response = sendResponse(200, '', []);

        $notifications = Notification::getUserNotifications($userId);
        if ($notifications->status == 200) {
            $notificationList = [];
            foreach ($notifications->response as $item) {
                $notice = new stdClass();
                $notice->NotificationId = $item->notification_id;
                $notice->NotificationSender = $item->notification_sender;
                $notice->NotificationTitle = $item->notification_title;
                $notice->NotificationMessage = $item->notification_message;
                $notice->NotificationStatus = $item->notification_status;
                $notice->NotificationTime = $item->notification_time;

                array_push($notificationList, $notice);
            }
            $response = sendResponse(200, '', $notificationList);
        }
        return $response;
    }


    /**
     * @param $userType
     * @param $userStatus
     *
     * @return stdClass
     */
    public static function getGroupNotifications($userType, $userStatus, $notics_type)
    {
        $response = sendResponse(200, '', []);

        $sql = "select *
        from tbl_notification_group 
        where user_type = :type and user_status = :uStatus and ngroup_status = :nStatus and ngroup_language = :lang  and  tbl_notification_group.ngroup_notics_type = :notics_type;";
        $params = [
            'type' => $userType,
            'notics_type' => $notics_type,
            'uStatus' => $userStatus,
            'nStatus' => 'active',
            'lang' => $_COOKIE['language']
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $notificationsList = [];
            foreach ($result->response as $item) {
                $notice = new stdClass();
                $notice->NotificationId = $item->ngroup_id;
                $notice->NotificationSender = $item->ngroup_sender;
                $notice->NotificationTitle = $item->ngroup_title;
                $notice->NotificationMessage = $item->ngroup_message;
                $notice->NotificationTime = json_decode($item->ngroup_options)->date_create;

                array_push($notificationsList, $notice);
            }
            $response = sendResponse(200, '', $notificationsList);
        }
        return $response;
    }


    /**
     * @param $notificationId
     *
     * @return stdClass
     */
    public static function getGroupNotificationDetail($notificationId)
    {
        $response = sendResponse(404, '', null);

        $sql = "select *
        from tbl_notification_group 
        where ngroup_id = :nId and ngroup_status = :status and ngroup_language = :lang";
        $params = [
            'nId' => $notificationId,
            'status' => 'active',
            'lang' => $_COOKIE['language'],
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $notice = new stdClass();
            foreach ($result->response as $item) {
                $notice->NotificationId = $item->ngroup_id;
                $notice->NotificationSender = $item->ngroup_sender;
                $notice->NotificationTitle = $item->ngroup_title;
                $notice->NotificationMessage = $item->ngroup_message;
            }
            $response = sendResponse(200, '', $notice);
        }
        return $response;
    }

    /**
     * @param $notificationId
     *
     * @return stdClass
     */
    public static function getNotificationDetail($notificationId)
    {
        $response = sendResponse(404, '', null);

        $notification = Notification::getNotificationById($notificationId);
        if ($notification->status == 200) {
            $notice = new stdClass();
            foreach ($notification->response as $item) {
                $notice->NotificationId = $item->notification_id;
                $notice->NotificationSender = $item->notification_sender;
                $notice->NotificationTitle = $item->notification_title;
                $notice->NotificationMessage = $item->notification_message;
                $notice->NotificationStatus = $item->notification_status;
                $notice->NotificationTime = $item->notification_time;
            }
            $response = sendResponse(200, '', $notice);
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $notificationId
     *
     * @return bool
     */
    public static function readNotification($userId, $notificationId)
    {
        $sql = "update tbl_notifications set
        notification_status = :status
        where notification_id = :noticeId and user_id = :userId;";
        $params = [
            'userId' => $userId,
            'noticeId' => $notificationId,
            'status' => 'read'
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            return true;
        }
        return false;
    }


    /**
     * @param $userId
     *
     * @return int
     */
    public static function getCountOfUnreadNotification($userId)
    {
        $response = 0;

        $sql = "select count(*) as count
        from tbl_notifications 
        where user_id = :userId and notification_status = :status;";
        $params = [
            'userId' => $userId,
            'status' => 'unread'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->count;
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
    public static function getTransactionsList($userId, $status = 'all', $page = 1, $perPage = 10)
    {
        $response = sendResponse(200, '', []);
        $from = ($page == 1) ? 0 : ($page - 1) * $perPage;

        if ($status == 'all') {
            $sql = "select * from
            tbl_transactions
            where user_id = :userId
            order by transaction_id desc
            limit {$from},{$perPage};";
            $params = [
                'userId' => $userId
            ];
        } elseif ($status == 'completed') {
            $sql = "select * from
            tbl_transactions
            where user_id = :userId and transaction_status in (:status1, :status2)
            order by transaction_id desc 
            limit {$from},{$perPage};";
            $params = [
                'userId' => $userId,
                'status1' => 'completed',
                'status2' => 'paid',
            ];
        } elseif ($status == 'pending') {
            $sql = "select * from
            tbl_transactions
            where user_id = :userId and transaction_status in (:status1, :status2)
            order by transaction_id desc 
            limit {$from},{$perPage};";
            $params = [
                'userId' => $userId,
                'status1' => 'pending',
                'status2' => 'pending_deposit',
            ];
        } elseif ($status == 'rejected') {
            $sql = "select * from
            tbl_transactions
            where user_id = :userId and transaction_status in (:status1, :status2)
            order by transaction_id desc 
            limit {$from},{$perPage};";
            $params = [
                'userId' => $userId,
                'status1' => 'rejected',
                'status2' => 'rejected_deposit',
            ];
        } else {
            $sql = "select * from
            tbl_transactions
            where user_id = :userId and transaction_status = :status
            order by transaction_id desc
            limit {$from},{$perPage};";
            $params = [
                'userId' => $userId,
                'status' => $status,
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $transactionList = [];
            foreach ($result->response as $item) {
                $tx = new stdClass();
                $tx->TransactionId = $item->transaction_id;
                $tx->TransactionAuthority = $item->transaction_authority;
                $tx->TransactionTrackingCode = $item->transaction_tracking_code;
                $tx->TransactionAmount = $item->transaction_amount;
                $tx->TransactionType = $item->transaction_type;
                $tx->TransactionGateway = $item->transaction_gateway;
                $tx->TransactionDepositType = $item->transaction_deposit_type;
                $tx->TransactionOptions = json_decode($item->transaction_options);
                $tx->TransactionCurrency = array_column(json_decode(Currency::getCurrencyById($tx->TransactionOptions->currency_id)->response[0]->currency_name), 'value', 'slug')[$_COOKIE['language']];
                $tx->TransactionDestination = (!empty($tx->TransactionOptions->card_id)) ? self::getCreditCardDetail($userId, $tx->TransactionOptions->card_id)->response : '';
                $tx->TransactionStatus = $item->transaction_status;
                $tx->TransactionTime = $item->transaction_date;

                array_push($transactionList, $tx);
            }
            $response = sendResponse(200, '', $transactionList);
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $transactionId
     *
     * @return stdClass
     */
    public static function getTransactionDetail($userId, $transactionId)
    {
        $response = sendResponse(404, '', null);

        $sql = "select * from
        tbl_transactions
        where user_id = :userId and transaction_id = :txId;";
        $params = [
            'userId' => $userId,
            'txId' => $transactionId
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $tx = new stdClass();
            foreach ($result->response as $item) {
                $tx->TransactionId = $item->transaction_id;
                $tx->TransactionAuthority = $item->transaction_authority;
                $tx->TransactionTrackingCode = $item->transaction_tracking_code;
                $tx->TransactionAmount = $item->transaction_amount;
                $tx->TransactionType = $item->transaction_type;
                $tx->TransactionGateway = $item->transaction_gateway;
                $tx->TransactionDepositType = $item->transaction_deposit_type;
                $tx->TransactionOptions = json_decode($item->transaction_options);
                $tx->TransactionCurrency = array_column(json_decode(Currency::getCurrencyById($tx->TransactionOptions->currency_id)->response[0]->currency_name), 'value', 'slug')[$_COOKIE['language']];
                $tx->TransactionDestination = (!empty($tx->TransactionOptions->card_id)) ? self::getCreditCardDetail($userId, $tx->TransactionOptions->card_id)->response : '';
                $tx->TransactionStatus = $item->transaction_status;
                $tx->TransactionTime = $item->transaction_date;
            }
            $response = sendResponse(200, '', $tx);
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
    public static function getCreditCardsList($userId, $status = 'all', $page = 1, $perPage = 10)
    {
        $response = sendResponse(200, '', []);
        $from = ($page == 1) ? 0 : ($page - 1) * $perPage;

        if ($status == 'all') {
            $sql = "select * from
            tbl_bank_card
            where user_id = :userId and card_status != :status
            order by card_id desc 
            limit {$from},{$perPage};";
            $params = [
                'userId' => $userId,
                'status' => 'deleted'
            ];
        } else {
            $sql = "select * from
            tbl_bank_card
            where user_id = :userId and card_status = :status
            order by card_id desc 
            limit {$from},{$perPage};";
            $params = [
                'userId' => $userId,
                'status' => $status
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $cardsList = [];
            foreach ($result->response as $item) {
                $card = new stdClass();
                $card->CardId = $item->card_id;
                $card->CardBankName = $item->card_bank;
                $card->CardNumber = $item->card_number;
                $card->CardAccountNumber = $item->card_account;
                $card->CardIBAN = $item->card_iban;
                $card->CardStatus = $item->card_status;

                array_push($cardsList, $card);
            }
            $response = sendResponse(200, '', $cardsList);
        }
        return $response;
    }


    public static function getCreditCardsList2($userId, $status = 'all')
    {
        $response = sendResponse(200, '', []);

        if ($status == 'all') {
            $sql = "select * from `tbl_bank_card`
            INNER JOIN `tbl_currency` ON tbl_currency.currency_id=tbl_bank_card.currency_id
            where user_id = :userId and card_status != :status
            order by card_id desc ;";
            $params = [
                'userId' => $userId,
                'status' => 'deleted'
            ];
        } else {
            $sql = "select * from `tbl_bank_card`
            INNER JOIN `tbl_currency` ON tbl_currency.currency_id=tbl_bank_card.currency_id
            where user_id = :userId and card_status = :status
            order by card_id desc ;";
            $params = [
                'userId' => $userId,
                'status' => $status
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $cardsList = [];
            foreach ($result->response as $item) {
                $card = new stdClass();
                $card->CardId = $item->card_id;
                $card->CardBankName = $item->card_bank;
                $card->CurrencyId = $item->currency_id;
                $card->CardNumber = $item->card_number;
                $card->CardAccountNumber = $item->card_account;
                $card->CardIBAN = $item->card_iban;
                $card->CardStatus = $item->card_status;

                array_push($cardsList, $card);
            }
            $response = sendResponse(200, '', $cardsList);
        }
        return $response;
    }

    /**
     * @param $userId
     * @param $cardId
     *
     * @return stdClass
     */
    public static function getCreditCardDetail($userId, $cardId)
    {
        $response = sendResponse(404, '', null);

        $sql = "select * from
        tbl_bank_card
        where user_id = :userId and card_id = :cardId and card_status != :status;";
        $params = [
            'userId' => $userId,
            'cardId' => $cardId,
            'status' => 'deleted'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $card = new stdClass();
            foreach ($result->response as $item) {
                $card->CardId = $item->card_id;
                $card->CardBankName = $item->card_bank;
                $card->CardCurrencyId = $item->currency_id;
                $card->CardNumber = $item->card_number;
                $card->CardAccountNumber = $item->card_account;
                $card->CardIBAN = $item->card_iban;
                $card->CardStatus = $item->card_status;
            }
            $response = sendResponse(200, '', $card);
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $bank
     * @param $cart
     * @param $account
     * @param $iban
     * @param $token
     *
     * @return stdClass
     */
    public static function newCreditCard($userId, $bank, $cart, $account, $iban, $currency, $token)
    {
        if (!Security::verifyCSRF('add-credit-card', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('add-credit-card');

        $sql = "insert into tbl_bank_card(user_id,currency_id, card_bank, card_number, card_account, card_iban, card_status, card_time) 
        values (:userId,:currency_id, :bank, :number, :account, :iban, :status, :time);";
        $params = [
            'userId' => $userId,
            'currency_id' => $currency,
            'bank' => $bank,
            'number' => $cart,
            'account' => $account,
            'iban' => $iban,
            'status' => 'pending',
            'time' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Credit card submitted');
            self::createUserLog($userId, 'uLog_new_credit_card', 'credit_card');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $cardId
     * @param $token
     *
     * @return stdClass
     */
    public static function deleteCreditCard($userId, $cardId, $token)
    {
        if (!Security::verifyCSRF('add-credit-card', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('add-credit-card');

        $sql = "update tbl_bank_card set
        card_status = :status
        where user_id = :userId and card_id = :cardId";
        $params = [
            'userId' => $userId,
            'cardId' => $cardId,
            'status' => 'deleted'
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'Credit card deleted successfully');
            self::createUserLog($userId, 'uLog_delete_credit_card', 'credit_card');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }


    /**
     * @param $userId
     */
    public static function checkBalanceIsGenerated($userId)
    {
        $currencies = Currency::getAllCurrencies();
        if ($currencies->status == 200) {
            foreach ($currencies->response as $currency) {
                $sql = "select count(*) as count
                from tbl_balance
                where user_id = :userId and currency_id = :currencyId;";
                $params = [
                    'userId' => $userId,
                    'currencyId' => $currency->currency_id
                ];
                $result = DB::rawQuery($sql, $params);
                if ($result->status == 200) {
                    if ($result->response[0]->count == 0) {
                        self::generateBalance($userId, $currency->currency_id);
                    }
                }
            }
        }
    }


    /**
     * @param $userId
     * @param $currencyId
     *
     * @return bool
     */
    private static function generateBalance($userId, $currencyId)
    {
        $sql = "insert into tbl_balance(user_id, currency_id, balance_value, balance_frozen, balance_in_withdraw) 
        values (:userId, :currencyId, :balance, :frozen, :withdraw);";
        $params = [
            'userId' => $userId,
            'currencyId' => $currencyId,
            'balance' => 0,
            'frozen' => 0,
            'withdraw' => 0,
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            return true;
        }
        return false;
    }


    /**
     * @param $userId
     *
     * @return stdClass
     */
    public static function getBalance($userId)
    {
        $response = sendResponse(200, '', []);

        $sql = "select * from
         tbl_balance 
         inner join tbl_currency on tbl_balance.currency_id = tbl_currency.currency_id
         where user_id = :userId and currency_status = :status;";
        $params = [
            'userId' => $userId,
            'status' => 'active'
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $balanceList = [];
            foreach ($result->response as $item) {
                $balance = new stdClass();
                $balance->BalanceId = $item->balance_id;
                $balance->BalanceAvailable = $item->balance_value;
                $balance->BalanceFrozen = $item->balance_frozen;
                $balance->BalanceInWithdraw = $item->balance_in_withdraw;
                $balance->BalanceTotal = $balance->BalanceAvailable + $balance->BalanceFrozen;
                $balance->CurrencyId = $item->currency_id;
                $balance->BalanceCurrency = array_column(json_decode($item->currency_name), 'value', 'slug')[$_COOKIE['language']];

                array_push($balanceList, $balance);
            }
            $response = sendResponse(200, '', $balanceList);
        }
        return $response;
    }

    /**
     * @param $userId
     * @param $currencyId
     * @param $amount
     *
     * @return bool
     */
    private static function checkWithdrawIsAvailable($userId, $currencyId, $amount)
    {
        $response = false;

        if ($amount > 0) {
            $sql = "select if(balance_value >= {$amount}, 'yes', 'no') as available
            from tbl_balance
            where user_id = {$userId} and currency_id = {$currencyId};";
            $params = [
                // 'amount' => $amount,
                // 'userId' => $userId,
                // 'currencyId' => $currencyId,
            ];
            $result = DB::rawQuery($sql, $params);
            if ($result->status == 200) {
                if ($result->response[0]->available == 'yes') {
                    $response = true;
                }
            }
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $amount
     * @param $currencyId
     * @param $destination
     * @param $token
     *
     * @return stdClass
     */
    public static function withdrawRequest($userId, $amount, $currencyId, $destination, $token)
    {
        if (!Security::verifyCSRF('add-credit-card', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('add-credit-card');


        $res = User::getCreditCardDetail($userId, $destination);
        if ($res->status == 200 && $res->response->CardStatus == "accepted" && $res->response->CardCurrencyId == $currencyId) {
            if (self::checkWithdrawIsAvailable($userId, $currencyId, $amount)) {
                $sql = "update tbl_balance set
                    balance_value = balance_value - :amount, balance_in_withdraw = balance_in_withdraw + :amount
                    where user_id = :userId and currency_id = :currency_id;
                    insert into tbl_transactions(user_id,currency_id,card_id , transaction_amount, transaction_type,  transaction_status, transaction_date) 
                    values (:userId,:currency_id,:card_id, :amount, :type, :status, :time);";
                $params = [
                    'userId' => $userId,
                    'currency_id' => $currencyId,
                    'card_id' => $destination,
                    'amount' => $amount,
                    'type' => 'withdraw',
                    'status' => 'pending',
                    'time' => time(),
                ];
                $result = DB::transactionQuery($sql, $params);
                if ($result->status == 200) {
                    // self::withdrawAmount($userId, $currencyId, $amount);
                    $response = sendResponse(200, 'Request submitted successfully');
                    self::createUserLog($userId, 'uLog_withdraw_request', 'wallet');
                } else {
                    $response = sendResponse(-10, 'Error', $csrf);
                }
            } else {
                $response = sendResponse(-20, 'Your requested amount is not allowed', $csrf);
            }
        } else {
            $response = sendResponse(-30, 'Your Credit is not accepted', $csrf);
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $currencyId
     * @param $amount
     *
     * @return bool
     */
    private static function withdrawAmount($userId, $currencyId, $amount)
    {
        $sql = "update tbl_balance set
        balance_value = balance_value - :amount, balance_in_withdraw = balance_in_withdraw + :amount
        where user_id = :userId and currency_id = :currencyId;";
        $params = [
            'userId' => $userId,
            'currencyId' => $currencyId,
            'amount' => $amount
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200) {
            return true;
        }
        return false;
    }


    /**
     * @param $userId
     *
     * @return bool
     */
    public static function createLoginSession($userId)
    {
        $user = self::getUserInfo($userId);
        $oldSessions = $user->UserActiveSessions;
        $activeSessions = [];
        $now = time();
        $location = Utils::getGeoLocation()->response;

        if (!empty($oldSessions)) {
            $oldSessions = array_filter($oldSessions, function ($item) use ($now) {
                return $item->expire > $now;
            });

            foreach ($oldSessions as $item) {
                $activeSessions[] = $item;
            }
        }

        $activeSessions[] = [
            'ip' => $location['ip'],
            'location' => $location['format'],
            'device' => Utils::getClientDevice(),
            'os' => Utils::getClientOS(),
            'browser' => Utils::getClientBrowser(),
            'time' => time(),
            'expire' => time() + USER_COOKIE_TIMEOUT,
        ];

        $sql = "update tbl_users set
        user_active_session = :sessions
        where user_id = :userId;";
        $params = [
            'userId' => $userId,
            'sessions' => json_encode($activeSessions)
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            return true;
        }
        return false;
    }


    /**
     * @param $userId
     *
     * @return bool
     */
    public static function updateActiveSession($userId)
    {
        $user = self::getUserInfo($userId);
        $oldSessions = $user->UserActiveSessions;
        $activeSessions = [];
        $now = time();

        if (!empty($oldSessions)) {
            $oldSessions = array_filter($oldSessions, function ($item) use ($now) {
                return $item->expire > $now;
            });

            foreach ($oldSessions as $item) {
                $activeSessions[] = $item;
            }
        }

        $sql = "update tbl_users set
        user_active_session = :sessions
        where user_id = :userId;";
        $params = [
            'userId' => $userId,
            'sessions' => json_encode($activeSessions)
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            return true;
        }
        return false;
    }


    /**
     * @param $userId
     * @param $message
     * @param $type
     *
     * @return bool
     */
    public static function createUserLog($userId, $message, $type)
    {
        $location = Utils::getGeoLocation()->response;

        $sql = "insert into tbl_user_log(user_id, log_detail, log_type) 
        VALUES (:userId, :log, :type);";
        $params = [
            'userId' => $userId,
            'log' => json_encode([
                'message' => $message,
                'ip' => $location['ip'],
                'location' => $location['format'],
                'device' => Utils::getClientDevice(),
                'os' => Utils::getClientOS(),
                'browser' => Utils::getClientBrowser(),
                'browser_version' => 1,
                'time' => time(),
            ]),
            'type' => $type
        ];
        $result = DB::insert($sql, $params);
        //todo
//        self::createWhatsAppMessage($userId, $message);
        //todo
        //question for sms

        if ($result->status == 200) {
            return true;
        }
        return false;
    }

    public static function getSupportCount($user_id, $status = "open")
    {
        $response = sendResponse(200, '', []);

        $sql = "SELECT COUNT(*) as support_badge from tbl_tickets WHERE tbl_tickets.ticket_status =:ticket_status  AND tbl_tickets.user_id =:user_id;";
        $params = [
            'user_id' => $user_id,
            'ticket_status' => $status,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, '', $result->response);
        }
        return $response;
    }

    public static function getSupportCountByStatus($user_id, $status = "all")
    {
        $response = sendResponse(200, '', []);
        if ($status == 'all') {
            $sql = "SELECT COUNT(*) as support_badge from tbl_tickets WHERE   tbl_tickets.user_id =:user_id;";
            $params = [
                'user_id' => $user_id,

            ];
        } else {
            $sql = "SELECT COUNT(*) as support_badge from tbl_tickets WHERE tbl_tickets.ticket_status =:ticket_status  AND tbl_tickets.user_id =:user_id;";
            $params = [
                'user_id' => $user_id,
                'ticket_status' => $status,
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, '', $result->response);
        }
        return $response;
    }


    /**
     * @param $userId
     *
     * @return \stdClass
     */
    public static function updateUserType($user_id, $type, $token)
    {
        $response = sendResponse(-10, 'Update Err');
        if (!Security::verifyCSRF('token_change_user_type', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('token_change_user_type');
        $sql = "update tbl_users set user_type =:user_type where user_id = :user_id";
        $params = [
            'user_id' => $user_id,
            'user_type' => $type
        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'update success', $csrf);
            $cookie = json_encode([
                'UserId' => $user_id,
                'UserMobile' => json_decode(Security::decrypt($_COOKIE['user-login']))->UserMobile,
                'UserType' => $type,
            ]);
            self::createUserLog($user_id, 'uLog_login_to_system', 'login');
            self::createLoginSession($user_id);
            setcookie('user-login', Security::encrypt($cookie), USER_COOKIE_TIMEOUT, '/');
            setcookie('user-type', $type, USER_COOKIE_TIMEOUT, '/');
        }
        return $response;
    }


    /**
     * @param $userId
     * @param $message
     * @param $type
     *
     * @return bool
     */
    public static function createWhatsAppMessage($userId, $message)
    {
        $sql = "insert into tbl_whatsapp_massage(user_id, wa_massage, wa_status , wa_submit_time) 
        VALUES (:user_id, :wa_massage, :wa_status ,:wa_submit_time);";
        $params = [
            'user_id' => $userId,
            'wa_massage' => $message,
            'wa_status' => 'unsend',
            'wa_submit_time' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            return true;
        }
        return false;
    }


    /**
     * Update User Required Auth
     *
     * @param $userId
     * @param $fName
     * @param $lName
     * @param $idCard
     * @param $passport
     * @param $type
     * @param $token
     *
     * @return stdClass
     */
    public static function updateUserRequiredAuth($userId, $fName, $lName, $idCard, $passport, $type, $token)
    {
        if (!Security::verifyCSRF2($token, false)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
//        $csrf = Security::initCSRF('edit-name');

        if (strlen($fName) <= 2) {
            return sendResponse(-2, 'Length fName Invalid');
        }

        if (strlen($lName) <= 2) {
            return sendResponse(-3, 'Length lName Invalid');
        }

        $number = null;
        if ($type == "id-card") {
            $number = $idCard;
            if (strlen($idCard) != 10) {
                return sendResponse(-4, 'Length Id Card Invalid');
            }
        } else {
            $number = $passport;
            if (strlen($passport) <= 4) {
                return sendResponse(-5, 'Length Passport Invalid');
            }
        }


        if ($type == "id-card") {
            // شروع احراز هویت با کارت ملی
            $userInfo = User::getUserInfo($userId);
            $test = InquiryJibit::matchPhoneAndNationalCode($userInfo->UserMobile, $number);
            if (isset(json_decode($test)->matched)) {
                if (json_decode($test)->matched) {

                    // جواب مثبت برگشته است
                    $sql = "UPDATE `tbl_users` SET `user_firstname`=:user_firstname,`user_lastname`=:user_lastname
                ,`user_type_card`=:user_type_card,`user_number_card`=:user_number_card,`user_auth_status`=:user_auth_status
                ,`user_auth_submit`=:user_auth_submit,`user_status`=:user_status WHERE `user_id`=:user_id";
                    $params = [
                        'user_firstname' => Security::encrypt($fName),
                        'user_lastname' => Security::encrypt($lName),
                        'user_type_card' => $type,
                        'user_number_card' => Security::encrypt($number),
                        'user_auth_status' => 'accepted',
                        'user_auth_submit' => time(),
                        'user_status' => 'active',
                        'user_id' => $userId,
                    ];
                    $result = DB::update($sql, $params);
                    if ($result->status == 200) {
                        $response = sendResponse(200, 'Last name changed successfully');
                        self::createUserLog($userId, 'uLog_auth_required', 'auth');
//                        self::autoAuthUser($userId, $type);
                    } elseif ($result->status == 208) {
                        $response = sendResponse(208, 'Changes already applied');
                    } else {
                        $response = sendResponse(-10, 'Error');
                    }


                } else {
                    return sendResponse(-6, 'Id Card Invalid');
                }
            } else {
                if (@json_decode($test)->message && json_decode($test)->message == "کدملی نامعتبر است") {
                    return sendResponse(-6, 'Id Card Invalid');
                } else {
                    return sendResponse(-10, 'Error');
                }
            }

            // پایان احراز هویت با کارت ملی


        } else {

            // شروع احراز هویت با پاسپورت
            $sql = "UPDATE `tbl_users` SET `user_firstname`=:user_firstname,`user_lastname`=:user_lastname
                ,`user_type_card`=:user_type_card,`user_number_card`=:user_number_card,`user_auth_status`=:user_auth_status
                ,`user_auth_submit`=:user_auth_submit,`user_status`=:user_status WHERE `user_id`=:user_id";
            $params = [
                'user_firstname' => Security::encrypt($fName),
                'user_lastname' => Security::encrypt($lName),
                'user_type_card' => $type,
                'user_number_card' => Security::encrypt($number),
                'user_auth_status' => 'accepted',
                'user_auth_submit' => time(),
                'user_status' => 'active',
                'user_id' => $userId,
            ];
            $result = DB::update($sql, $params);
            if ($result->status == 200) {
                $response = sendResponse(200, 'Last name changed successfully');
                self::createUserLog($userId, 'uLog_auth_required', 'auth');
//                self::autoAuthUser($userId, $type);
            } elseif ($result->status == 208) {
                $response = sendResponse(208, 'Changes already applied');
            } else {
                $response = sendResponse(-10, 'Error');
            }
            // پایان احراز هویت با پاسپورت

        }


        return $response;
    }

    public static function autoAuthUser($userId, $type)
    {
        if ($type == 'id-card' && !empty($idCard)) {
            $phone = User::getUserInfo()->UserMobile;
            $phone = str_replace('+98', '0', $phone);
            $result = InquiryJibit::matchPhoneAndNationalCode($phone, $idCard);
            $authStatus = 'rejected';
            if (isset(json_decode($result)->matched) && json_decode($result)->matched == 1) {
                $authStatus = 'accepted';
            }

            $queryInquiry = "update tbl_users set user_auth_status = :auth_status  , user_inquiry_status = :inquiry_status 
                                 , user_inquiry_admin_time = :admin_time , user_auth_submit = :time ,user_inquiry_id_card = :inquiry_id_card 
                                 where user_id  = :user_id";
            $paramsInquiry = [
                'auth_status' => $authStatus,
                'inquiry_status' => $authStatus == 'accepted',
                'admin_time' => time(),
                'time' => time(),
                'inquiry_id_card' => $idCard,
                'user_id' => $userId
            ];
            DB::update($queryInquiry, $paramsInquiry);
        }
    }

    /**
     * Insert Or Update Businessman Optional Auth
     *
     * @param $userId
     * @param $company
     * @param $manager
     * @param $address
     * @param $phone
     * @param $fox
     * @param $mail
     * @param $site
     * @param $idCard
     * @param $passport
     * @param $token
     *
     * @return stdClass
     */
    public static function updateUserBusinessmanOptionalAuth($userId, $company, $manager, $address, $phone, $fox, $mail, $site, $idCard, $passport, $token)
    {
        if (!Security::verifyCSRF2($token, false)) {
            return sendResponse(-1, 'CSRF-Token error');
        }

        if (strlen($company) <= 0 && strlen($manager) <= 0 && strlen($address) <= 0 && strlen($phone) <= 0 &&
            strlen($fox) <= 0 && strlen($mail) <= 0 && strlen($site) <= 0 && strlen($idCard) <= 0 &&
            strlen($passport) <= 0) {
            return sendResponse(-2, 'Length Values Invalid');
        } else {
            $flag = false;
            if (strlen($company) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'company', $company);
            }
            if (strlen($manager) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'manager', $manager);
            }
            if (strlen($address) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'address', $address);
            }
            if (strlen($phone) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'phone', $phone);
            }
            if (strlen($fox) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'fox', $fox);
            }
            if (strlen($mail) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'mail', $mail);
            }
            if (strlen($site) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'site', $site);
            }
            if (strlen($idCard) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'id-card-image', $idCard);
            }
            if (strlen($passport) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'passport-image', $passport);
            }

            if ($flag) {
                $response = sendResponse(200, 'Authentication Optional changed successfully');
            } else {
                $response = sendResponse(-10, 'Error');
            }

        }

        return $response;
    }


    public static function updateUserDriverOptionalAuth($userId, $birthday, $birthdayDate, $phoneNational, $address, $phone, $insuranceType, $insuranceNumber, $idCard, $passport, $carCard, $token)
    {
        if (!Security::verifyCSRF2($token, false)) {
            return sendResponse(-1, 'CSRF-Token error');
        }

        if (strlen($birthday) <= 0 && strlen($birthdayDate) <= 0 && strlen($phoneNational) <= 0 &&
            strlen($address) <= 0 && strlen($phone) <= 0 && strlen($insuranceType) <= 0 &&
            strlen($insuranceNumber) <= 0 && strlen($idCard) <= 0 && strlen($passport) <= 0 && strlen($carCard) <= 0) {
            return sendResponse(-2, 'Length Values Invalid');
        } else {
            $flag = false;
            if (strlen($birthday) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'birthday-city', $birthday);
            }
            if (strlen($birthdayDate) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'birthday-date', $birthdayDate);
            }
            if (strlen($phoneNational) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'phone-national', $phoneNational);
            }
            if (strlen($address) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'address', $address);
            }
            if (strlen($phone) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'phone', $phone);
            }
            if (strlen($insuranceType) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'insurance-type', $insuranceType);
            }
            if (strlen($insuranceNumber) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'insurance-number', $insuranceNumber);
            }
            if (strlen($idCard) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'id-card-image', $idCard);
            }
            if (strlen($passport) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'passport-image', $passport);
            }
            if (strlen($carCard) > 0) {
                $flag = self::InsertOrUpdateAuthenticationOptional($userId, 'car-card-image', $passport);
            }
            if ($flag) {
                $response = sendResponse(200, 'Authentication Optional changed successfully');
            } else {
                $response = sendResponse(-10, 'Error');
            }

        }

        return $response;
    }


    private static function InsertOrUpdateAuthenticationOptional($userID, $slug, $value)
    {


        $sql = "INSERT INTO  `tbl_user_options`( `user_id`, `option_slug`,`option_value`, `option_status`, `option_submit_date`) 
SELECT :user_id,:option_slug,:option_value,:option_status,:option_submit_date FROM DUAL WHERE 
NOT EXISTS (SELECT  * FROM `tbl_user_options` WHERE user_id = :user_id AND option_slug = :option_slug);
UPDATE `tbl_user_options` SET  option_value = :option_value,option_status=:option_status,option_submit_date=:option_submit_date WHERE user_id = :user_id AND option_slug = :option_slug;";

        $params = [
            'user_id' => $userID,
            'option_slug' => $slug,
            'option_value' => $value,
            'option_status' => 'accepted',
            'option_submit_date' => time(),
        ];
        $result = DB::transactionQuery($sql, $params);
        if ($result->status == 200) {
            return true;
        }
        return $result;

    }

    public static function updateUserLang($userId, $lang)
    {
        $resultLanguages = Utils::getFileValue("languages.json", "", false);
        $flag = false;
        if (!empty($resultLanguages)) {
            $dataLanguages = json_decode($resultLanguages);
            foreach ($dataLanguages as $loop) {
                if ($loop->slug == $lang) {
                    $flag = true;
                }
            }

            if ($flag) {
                $sql = "update tbl_users set user_language = :user_language where user_id = :userId;";
                $params = [
                    'userId' => $userId,
                    'user_language' => $lang
                ];
                $result = DB::update($sql, $params);
                if ($result->status == 200 || $result->status == 208) {
                    User::createUserLog($userId, 'u_change_language', 'info');
                    return true;
                }
            }
        }
        return false;
    }


    public static function generateReferralCode()
    {
        $code = Utils::generateRandomNumber(8);
        $c = str_split($code);

        while ($c[0] == '0') {
            $code = Utils::generateRandomNumber(8);
            $c = str_split($code);
        }

        if (!self::checkReferralCode($code)) {
            return $code;
        }
        return self::generateReferralCode();
    }


    public static function generateReferralCode2($mobile, $mobileCode, $mobileNumber)
    {
        $mobile2 = substr($mobile, 1);
        $mobileCode2 = substr($mobileCode, 1);
        $code = str_replace($mobileCode2, "", $mobile2);

        if (!self::checkReferralCode($code)) {
            return $code;
        }
        return self::generateReferralCode2(substr($mobile, 0, -1), $mobileCode, $mobileNumber);
    }


    private static function checkReferralCode($code)
    {
        $sql = "select count(*) as count
        from tbl_users 
        where user_unique_code = :user_unique_code;";
        $params = [
            'user_unique_code' => $code
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            if ($result->response[0]->count > 0) {
                return true;
            } else {
                return false;
            }
        }
        return self::checkReferralCode($code);
    }


    /**
     * @param null $userId
     *
     * @return stdClass
     */
    public static function checkUserRegister($phone)
    {

        $response = sendResponse(0, 'error');

        $sql = "select count(*) as register_flag from tbl_users where user_mobile = :user_mobile;";
        $params = [
            'user_mobile' => Security::encrypt($phone)
        ];
        $result = DB::rawQuery($sql, $params);


        return $result->response[0]->register_flag;
    }

    public static function canChangeUserTypeBusinessman()
    {
        $user_id = self::getUserInfo()->UserId;
        $user_type = self::getUserInfo()->UserType;

        if ($user_type == 'driver') {
            if (Driver::getRequestCountByStatus($user_id) == 0) {
                if (Driver::getRequestInCountByStatus($user_id) == 0) {
                    return true;
                }
            }
        }
        return false;
    }


    public static function canChangeUserTypeDriver()
    {
        $user_id = self::getUserInfo()->UserId;
        $user_type = self::getUserInfo()->UserType;

        if ($user_type == 'businessman') {
            if (Businessman::getCargoOutCount($user_id) == 0) {
                if (Businessman::getCargoInCount($user_id) == 0) {
                    return true;
                }
            }
        }
        return false;
    }


    public static function getUserReferrals($code)
    {

        $response = sendResponse(404, '', null);
        $sql = "SELECT * FROM `tbl_users` WHERE user_referral_code=:user_unique_code;";
        $params = [
            'user_unique_code' => $code,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, '', $result->response);
        }
        return $response;
    }


    public static function changeUserLanguageOnChangeLanguage($user_language)
    {

        if (isset($_COOKIE['user-login'])) {
            $user_id = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;

            $sql = "update tbl_users set tbl_users.user_language = :user_language  
                                 where tbl_users.user_id  = :user_id";
            $params = [
                'user_language' => $user_language,
                'user_id' => $user_id
            ];
            $result = DB::update($sql, $params);

            if ($result->status == 200 || $result->status == 208) {
                return sendResponse(200, '');
            }
        }

        return sendResponse(0, '');
    }
}