<?php

global $lang, $antiXSS, $Settings;

use MJ\Security\Security;
use MJ\Upload\Upload;
use MJ\Utils\Utils;
use function MJ\Keys\sendResponse;

$json = json_decode(file_get_contents("php://input"));

if (!empty($json && isset($json))) {
    $security_data = file_get_contents("php://input");
    if (Security::check_json_for_script_tags($security_data)) {
        echo(permissionAccess());
        die();
    }
} else {
    if (isset($_POST)) {

        $security_data = json_encode($_POST);
        if (Security::check_json_for_script_tags($security_data)) {
            echo(permissionAccess());
            die();
        }
    }
}


if (!empty($json) && isset($json->action)) {

    switch ($json->action) {
        case 'change-theme':
            $theme = $json->theme;
            Utils::setTheme($theme);
            break;

        /**
         * START USER
         */

        case 'login-user':
            $mobile = (isset($json->mobile)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->mobile)) : null;
            $type = (isset($json->type)) ? $antiXSS->xss_clean($json->type) : null;
            $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
            if (!empty($mobile) && !empty($type) && !empty($token)) {
                echo json_encode(User::loginUser($mobile, $type, $token));
            } else {
                echo parameterRequired();
            }
            break;

        case 'verify-login':
            $code = (isset($json->code)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->code)) : null;
            $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
            $status = (isset($json->status)) ? $antiXSS->xss_clean($json->status) : null;
            if (!empty($code) && !empty($token)) {
                echo json_encode(User::verifyOTP($code, $status, $token));
            } else {
                echo parameterRequired();
            }
            break;

        case 'register-new-user':
            $code = (isset($json->code)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->code)) : null;
            $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
            $user_name = (isset($json->user_name)) ? $antiXSS->xss_clean($json->user_name) : null;
            $user_lname = (isset($json->user_lname)) ? $antiXSS->xss_clean($json->user_lname) : null;
            $user_referral = (isset($json->user_referral) && is_numeric($json->user_referral)) ? $antiXSS->xss_clean($json->user_referral) : null;
            $status = (isset($json->status)) ? $antiXSS->xss_clean($json->status) : null;
            $mobileCode = (isset($json->mobileCode)) ? $antiXSS->xss_clean($json->mobileCode) : null;
            $mobileNumber = (isset($json->mobileNumber)) ? $antiXSS->xss_clean($json->mobileNumber) : null;
            if (!empty($code) && !empty($token) && !empty($user_name) && !empty($user_lname)) {
                if (isset($_COOKIE['can-register']) && $_COOKIE['can-register'] == true) {
                    $mobile = $mobileCode . $mobileNumber;

                    User::registerUser($mobile, 'guest', $user_name, $user_lname, $token, $user_referral, $mobileCode, $mobileNumber);
                    PhoneBook::addUserToPhoneBookWhenRegister($user_name, $user_lname, 'guest', $mobile);

                    echo json_encode(['status' => 200, 'message' => 'register success']);
                }
            } else {
                echo parameterRequired();
            }
            break;
        case 'edit-first-name':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $fName = (isset($json->fName)) ? $antiXSS->xss_clean($json->fName) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($fName) && !empty($token)) {
                    echo json_encode(User::updateUserFirstName($userId, $fName, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'edit-last-name':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $lName = (isset($json->lName)) ? $antiXSS->xss_clean($json->lName) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($lName) && !empty($token)) {
                    echo json_encode(User::updateUserLastName($userId, $lName, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        /* case 'save-text-option':
             $isValidRequest = false;
             if (User::userIsLoggedIn()) {
                 $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                 $isValidRequest = true;
             }

             if ($isValidRequest) {
                 $fieldId = (isset($json->field)) ? $antiXSS->xss_clean($json->field) : null;
                 $value = (isset($json->value)) ? $antiXSS->xss_clean($json->value) : null;
                 $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                 if (!empty($userId) && !empty($fieldId) && !empty($value) && !empty($token)) {
                     echo json_encode(User::updateUserAuth($userId, $fieldId, $value, $token));
                 } else {
                     echo parameterRequired();
                 }
             } else {
                 echo permissionAccess();
             }
             break;*/

        case 'submit-complaint':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $cargoId = (isset($json->cargo)) ? $antiXSS->xss_clean($json->cargo) : null;
                $requestId = (isset($json->request)) ? $antiXSS->xss_clean($json->request) : null;
                $to = (isset($json->to)) ? $antiXSS->xss_clean($json->to) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($cargoId) && !empty($requestId) && !empty($to) && !empty($token)) {
                    echo json_encode(Complaint::submitComplaint($cargoId, $requestId, $userId, $to, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'submit-complaint-in':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $cargoId = (isset($json->cargo)) ? $antiXSS->xss_clean($json->cargo) : null;
                $requestId = (isset($json->request)) ? $antiXSS->xss_clean($json->request) : null;
                $to = (isset($json->to)) ? $antiXSS->xss_clean($json->to) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($cargoId) && !empty($requestId) && !empty($to) && !empty($token)) {
                    echo json_encode(Complaint::submitComplaintIn($cargoId, $requestId, $userId, $to, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'load-more-transactions':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $page = (isset($json->page)) ? $antiXSS->xss_clean($json->page) : null;
                $status = (isset($json->status)) ? $antiXSS->xss_clean($json->status) : null;

                if (!empty($userId) && !empty($page) && !empty($status)) {
                    $records = User::getTransactionsList($userId, $status, $page);
                    $key = ($page * 10);
                    $html = '';

                    foreach ($records->response as $item) {
                        $date = ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('Y/m/d', $item->TransactionTime) : date('Y-m-d', $item->TransactionTime);
                        if (in_array($item->TransactionStatus, ['completed', 'paid'])) {
                            $badge = 'mj-badge-success';
                        } elseif ($item->TransactionStatus == 'pending') {
                            $badge = 'mj-badge-warning';
                        } elseif ($item->TransactionStatus == 'rejected') {
                            $badge = 'mj-badge-danger';
                        } elseif ($item->TransactionStatus == 'expired') {
                            $badge = 'mj-badge-dark';
                        } elseif ($item->TransactionStatus == 'canceled') {
                            $badge = 'mj-badge-cancel';
                        } elseif ($item->TransactionStatus == 'unpaid') {
                            $badge = 'mj-badge-purple';
                        } else {
                            $badge = 'mj-badge-primary';
                        }
                        $amount = number_format($item->TransactionAmount);

                        $html .= "
                            <tr class='align-middle'>
                                <td>{$key}</td>
                                <td>{$amount} {$item->TransactionCurrency}</td>
                                <td>{$date}</td>
                                <td>
                                    <div class='text-center'>
                                        <span class='mj-badge {$badge}'></span>
                                    </div>
                                </td>
                                <td>
                                    <a href='/driver/tx/{$item->TransactionId}' class='mj-btn-more'>
                                        {$lang['d_button_detail']}
                                    </a>
                                </td>
                            </tr>";
                        $key++;
                    }

                    echo $html;
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'load-more-credit-cards':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $page = (isset($json->page)) ? $antiXSS->xss_clean($json->page) : null;
                $status = (isset($json->status)) ? $antiXSS->xss_clean($json->status) : null;

                if (!empty($userId) && !empty($page) && !empty($status)) {
                    $records = User::getCreditCardsList($userId, $status, $page);
                    $key = ($page * 10);
                    $html = '';

                    foreach ($records->response as $item) {
                        if ($item->CardStatus == 'pending') {
                            $badge = 'mj-badge-warning';
                        } elseif ($item->CardStatus == 'accepted') {
                            $badge = 'mj-badge-success';
                        } elseif ($item->CardStatus == 'accepted') {
                            $badge = 'mj-badge-danger';
                        } else {
                            $badge = 'mj-badge-dark';
                        }

                        $html .= "
                            <tr class='align-middle'>
                                <td>{$key}</td>
                                <td>
                                    <span dir='ltr'>{$item->CardAccountNumber}</span>
                                </td>
                                <td>
                                    <div class='text-center'>
                                        <span class='mj-badge {$badge}'></span>
                                    </div>
                                </td>
                                <td>
                                    <a href='javascript:void(0);' class='mj-btn-more' onclick='cardDetail($(this))'
                                    data-number='{$item->CardNumber}' data-iban='{$item->CardIBAN}'
                                    data-account='{$item->CardAccountNumber}'
                                    data-card='{$item->CardId}'
                                    data-credit-detail role='button'>
                                        {$lang['d_button_detail']}
                                    </a>
                                </td>
                            </tr>";
                        $key++;
                    }

                    echo $html;
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'delete-credit-card':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $cardId = (isset($json->card)) ? $antiXSS->xss_clean($json->card) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($cardId) && !empty($token)) {
                    echo json_encode(User::deleteCreditCard($userId, $cardId, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'new-credit-card':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $bank = (isset($json->bank)) ? $antiXSS->xss_clean($json->bank) : null;
                $cart = (isset($json->cart)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->cart)) : null;
                $account = (isset($json->account)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->account)) : null;
                $iban = (isset($json->iban)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->iban)) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
                $currency = (isset($json->currency) && $json->currency != 0) ? $antiXSS->xss_clean($json->currency) : null;

                if (!empty($userId) && !empty($bank) && !empty($account) && !empty($iban) && !empty($token) && !empty($currency)) {
                    echo json_encode(User::newCreditCard($userId, $bank, $cart, $account, $iban, $currency, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'withdraw-request':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $amount = (isset($json->amount)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->amount)) : null;
                $currency = (isset($json->currency)) ? $antiXSS->xss_clean($json->currency) : null;
                $destination = (isset($json->destination)) ? $antiXSS->xss_clean($json->destination) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($amount) && $amount != 0 && !empty($currency) && !empty($destination) && !empty($token)) {
                    echo json_encode(User::withdrawRequest($userId, $amount, $currency, $destination, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;


        case 'auth-required':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $lists = [
                    "id-card",
                    "passport",
                ];

                $fName = (isset($json->fName)) ? $antiXSS->xss_clean($json->fName) : null;
                $lName = (isset($json->lName)) ? $antiXSS->xss_clean($json->lName) : null;
                $idCard = (isset($json->idCard)) ? $antiXSS->xss_clean($json->idCard) : null;
                $passport = (isset($json->passport)) ? $antiXSS->xss_clean($json->passport) : null;
                $type = (isset($json->type) && in_array($json->type, $lists)) ? $antiXSS->xss_clean($json->type) : 'card-id';
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($fName) && !empty($lName) && !empty($type) && !empty($token)) {
                    echo json_encode(User::updateUserRequiredAuth($userId, $fName, $lName, $idCard, $passport, $type, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'auth-optional-businessman':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {

                $company = (isset($json->company)) ? $antiXSS->xss_clean($json->company) : null;
                $manager = (isset($json->manager)) ? $antiXSS->xss_clean($json->manager) : null;
                $address = (isset($json->address)) ? $antiXSS->xss_clean($json->address) : null;
                $phone = (isset($json->phone)) ? $antiXSS->xss_clean($json->phone) : null;
                $fox = (isset($json->fox)) ? $antiXSS->xss_clean($json->fox) : null;
                $mail = (isset($json->mail)) ? $antiXSS->xss_clean($json->mail) : null;
                $site = (isset($json->site)) ? $antiXSS->xss_clean($json->site) : null;
                $idCard = (isset($json->idCard)) ? $antiXSS->xss_clean($json->idCard) : null;
                $passport = (isset($json->passport)) ? $antiXSS->xss_clean($json->passport) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                $attachmentIdCard = null;
                if (strlen($idCard) > 20) {
                    $upload = Upload::uploadBase64($idCard, AUTH_ADDRESS);
                    if ($upload->status == 200) {
                        $attachmentIdCard = $upload->response;
                    }
                }

                $attachmentPassport = null;
                if (strlen($passport) > 20) {
                    $upload = Upload::uploadBase64($passport, AUTH_ADDRESS);
                    if ($upload->status == 200) {
                        $attachmentPassport = $upload->response;
                    }
                }


                if (!empty($userId) && !empty($token)) {
                    echo json_encode(User::updateUserBusinessmanOptionalAuth($userId, $company, $manager, $address, $phone, $fox, $mail, $site, $attachmentIdCard, $attachmentPassport, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;


        case 'auth-optional-driver':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {

                $birthday = (isset($json->birthday)) ? $antiXSS->xss_clean($json->birthday) : null;
                $birthdayDate = (isset($json->birthdayDate)) ? $antiXSS->xss_clean($json->birthdayDate) : null;
                $phoneNational = (isset($json->phoneNational)) ? $antiXSS->xss_clean($json->phoneNational) : null;
                $address = (isset($json->address)) ? $antiXSS->xss_clean($json->address) : null;
                $phone = (isset($json->phone)) ? $antiXSS->xss_clean($json->phone) : null;
                $insuranceType = (isset($json->insuranceType)) ? $antiXSS->xss_clean($json->insuranceType) : null;
                $insuranceNumber = (isset($json->insuranceNumber)) ? $antiXSS->xss_clean($json->insuranceNumber) : null;
                $idCard = (isset($json->idCard)) ? $antiXSS->xss_clean($json->idCard) : null;
                $passport = (isset($json->passport)) ? $antiXSS->xss_clean($json->passport) : null;
                $carCard = (isset($json->carCard)) ? $antiXSS->xss_clean($json->carCard) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                $attachmentIdCard = null;
                if (strlen($idCard) > 20) {
                    $upload = Upload::uploadBase64($idCard, AUTH_ADDRESS);
                    if ($upload->status == 200) {
                        $attachmentIdCard = $upload->response;
                    }
                }

                $attachmentPassport = null;
                if (strlen($passport) > 20) {
                    $upload = Upload::uploadBase64($passport, AUTH_ADDRESS);
                    if ($upload->status == 200) {
                        $attachmentPassport = $upload->response;
                    }
                }

                $attachmentCarCard = null;
                if (strlen($carCard) > 20) {
                    $upload = Upload::uploadBase64($carCard, AUTH_ADDRESS);
                    if ($upload->status == 200) {
                        $attachmentCarCard = $upload->response;
                    }
                }


                if (!empty($userId) && !empty($token)) {
                    echo json_encode(User::updateUserDriverOptionalAuth($userId, $birthday, $birthdayDate, $phoneNational, $address, $phone, $insuranceType, $insuranceNumber, $attachmentIdCard, $attachmentPassport, $attachmentCarCard, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'search-in-academy':
            $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
            $search_value = (isset($json->search)) ? $antiXSS->xss_clean($json->search) : null;

            if (!empty($search_value) && !empty($token) && Security::verifyCSRF2($token, false)) {
                echo json_encode(Academy::searchInAcademy($search_value));
            } else {
                echo parameterRequired();
            }
            break;
        case 'search-in-academy-category':
            $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
            $search_value = (isset($json->search)) ? $antiXSS->xss_clean($json->search) : null;
            $category_id = (isset($json->category_id)) ? $antiXSS->xss_clean($json->category_id) : null;

            if (!empty($search_value) && !empty($category_id) && !empty($token) && Security::verifyCSRF2($token, false)) {
                echo json_encode(Academy::searchInAcademy($search_value, $category_id));
            } else {
                echo parameterRequired();
            }
            break;

        case 'submit-driver-cv':
            $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
            $cv_name = (isset($json->cv_name)) ? $antiXSS->xss_clean($json->cv_name) : null;
            $city_id = (isset($json->city_id)) ? $antiXSS->xss_clean($json->city_id) : null;
            $cv_lname = (isset($json->cv_lname)) ? $antiXSS->xss_clean($json->cv_lname) : null;
            $cv_user_avatar = (isset($json->cv_user_avatar)) ? $antiXSS->xss_clean($json->cv_user_avatar) : null;
            $cv_brith_date = (isset($json->cv_brith_date)) ? $antiXSS->xss_clean($json->cv_brith_date) : time();
            $cv_gender = (isset($json->cv_gender)) ? $antiXSS->xss_clean($json->cv_gender) : null;
            $cv_marital_status = (isset($json->cv_marital_status)) ? $antiXSS->xss_clean($json->cv_marital_status) : null;
            $cv_military_status = (isset($json->cv_military_status)) ? $antiXSS->xss_clean($json->cv_military_status) : null;
            $cv_military_image = (isset($json->cv_military_image)) ? $antiXSS->xss_clean($json->cv_military_image) : [];
            $cv_military_number = (isset($json->cv_military_number)) ? $antiXSS->xss_clean($json->cv_military_number) : null;
            $cv_military_date = (isset($json->cv_military_date)) ? $antiXSS->xss_clean($json->cv_military_date) : time();
            $cv_smartcard_status = (isset($json->cv_smartcard_status)) ? $antiXSS->xss_clean($json->cv_smartcard_status) : null;
            $cv_smartcard_image = (isset($json->cv_smartcard_image)) ? $antiXSS->xss_clean($json->cv_smartcard_image) : [];
            $cv_smartcard_number = (isset($json->cv_smartcard_number)) ? $antiXSS->xss_clean($json->cv_smartcard_number) : null;
            $cv_smartcard_date = (isset($json->cv_smartcard_date)) ? $antiXSS->xss_clean($json->cv_smartcard_date) : time();
            $cv_passport_status = (isset($json->cv_passport_status)) ? $antiXSS->xss_clean($json->cv_passport_status) : null;
            $cv_passport_image = (isset($json->cv_passport_image)) ? $antiXSS->xss_clean($json->cv_passport_image) : [];
            $cv_passport_number = (isset($json->cv_passport_number)) ? $antiXSS->xss_clean($json->cv_passport_number) : null;
            $cv_passport_date = (isset($json->cv_passport_date)) ? $antiXSS->xss_clean($json->cv_passport_date) : time();
            $cv_visa_status = (isset($json->cv_visa_status)) ? $antiXSS->xss_clean($json->cv_visa_status) : null;
            $cv_visa_image = (isset($json->cv_visa_image)) ? $antiXSS->xss_clean($json->cv_visa_image) : [];
            $cv_visa_number = (isset($json->cv_visa_number)) ? $antiXSS->xss_clean($json->cv_visa_number) : null;
            $cv_visa_date = (isset($json->cv_visa_date)) ? $antiXSS->xss_clean($json->cv_visa_date) : time();
            $cv_visa_location = (($json->cv_visa_location)) ? $antiXSS->xss_clean($json->cv_visa_location) : [];
            $cv_workbook_status = (isset($json->cv_workbook_status)) ? $antiXSS->xss_clean($json->cv_workbook_status) : null;
            $cv_workbook_image = (isset($json->cv_workbook_image)) ? $antiXSS->xss_clean($json->cv_workbook_image) : [];
            $cv_workbook_number = (isset($json->cv_workbook_number)) ? $antiXSS->xss_clean($json->cv_workbook_number) : null;
            $cv_workbook_date = (isset($json->cv_workbook_date)) ? $antiXSS->xss_clean($json->cv_workbook_date) : time();
            $cv_driver_license_status = (isset($json->cv_driver_license_status)) ? $antiXSS->xss_clean($json->cv_driver_license_status) : null;
            $cv_driver_license_image = (isset($json->cv_driver_license_image)) ? $antiXSS->xss_clean($json->cv_driver_license_image) : [];
            $cv_driver_license_number = (isset($json->cv_driver_license_number)) ? $antiXSS->xss_clean($json->cv_driver_license_number) : null;
            $cv_driver_license_date = (isset($json->cv_driver_license_date)) ? $antiXSS->xss_clean($json->cv_driver_license_date) : time();
            $cv_mobile = (isset($json->cv_mobile)) ? $antiXSS->xss_clean($json->cv_mobile) : null;
            $cv_whatsapp = (isset($json->cv_whatsapp)) ? $antiXSS->xss_clean($json->cv_whatsapp) : null;
            $cv_address = (isset($json->cv_address)) ? $antiXSS->xss_clean($json->cv_address) : null;
            $cv_faviroite_country = (($json->cv_faviroite_country)) ? $antiXSS->xss_clean($json->cv_faviroite_country) : [];
            $cv_role_status = (isset($json->cv_role_status)) ? $antiXSS->xss_clean($json->cv_role_status) : null;

            if (!empty($city_id) && !empty($cv_name) && !empty($cv_lname) && !empty($cv_brith_date) && !empty($cv_gender) && !empty($cv_marital_status)
                && !empty($cv_military_status) && !empty($cv_smartcard_status) && !empty($cv_passport_status) && !empty($cv_visa_status)
                && !empty($cv_workbook_status) && !empty($cv_driver_license_status) && !empty($cv_mobile) && !empty($cv_whatsapp)
                && !empty($cv_address)) {
                $military_image = [];
                if ($cv_military_image != []) {

                    foreach ($cv_military_image as $image) {
                        $upload = Upload::uploadBase64($image, DRIVER_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $military_image[] = $upload->response;
                        }
                    }
                }

                $smartcard_image = [];
                if ($cv_smartcard_image != []) {

                    foreach ($cv_smartcard_image as $image) {
                        $upload = Upload::uploadBase64($image, DRIVER_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $smartcard_image[] = $upload->response;
                        }
                    }
                }

                $passport_image = [];
                if ($cv_passport_image != []) {

                    foreach ($cv_passport_image as $image) {
                        $upload = Upload::uploadBase64($image, DRIVER_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $passport_image[] = $upload->response;
                        }
                    }
                }
                $visa_image = [];
                if ($cv_visa_image != []) {

                    foreach ($cv_visa_image as $image) {
                        $upload = Upload::uploadBase64($image, DRIVER_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $visa_imagee[] = $upload->response;
                        }
                    }
                }
                $workbook_image = [];
                if ($cv_workbook_image != []) {

                    foreach ($cv_workbook_image as $image) {
                        $upload = Upload::uploadBase64($image, DRIVER_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $workbook_image[] = $upload->response;
                        }
                    }
                }
                $driver_license_image = [];
                if ($cv_driver_license_image != []) {

                    foreach ($cv_driver_license_image as $image) {
                        $upload = Upload::uploadBase64($image, DRIVER_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $driver_license_image[] = $upload->response;
                        }
                    }
                }
                $user_avatar = '';
                if (!empty($cv_user_avatar)) {
                    $upload = Upload::uploadBase64($cv_user_avatar, DRIVER_DOCS_ADDRESS);
                    if ($upload->status == 200) {
                        $user_avatar = $upload->response;
                    }
                }
                print_r(json_encode(CV::insertDriverCV($city_id, $cv_name, $cv_lname, $cv_brith_date, $cv_gender,
                    $cv_marital_status, $cv_military_status, $military_image, $cv_military_number,
                    $cv_military_date, $cv_smartcard_status, $smartcard_image, $cv_smartcard_number,
                    $cv_smartcard_date, $cv_passport_status, $passport_image, $cv_passport_number,
                    $cv_passport_date, $cv_visa_status, $visa_image, $cv_visa_number, $cv_visa_date, $cv_visa_location,
                    $cv_workbook_status, $workbook_image, $cv_workbook_number, $cv_workbook_date,
                    $cv_driver_license_status, $driver_license_image, $cv_driver_license_number,
                    $cv_driver_license_date, $cv_mobile, $cv_whatsapp, $cv_address, $cv_faviroite_country,
                    $cv_role_status, $user_avatar)));

            } else {
                echo parameterRequired();
            }


            break;
        case 'update-driver-cv':
            $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
            $cv_name = (isset($json->cv_name)) ? $antiXSS->xss_clean($json->cv_name) : null;
            $cv_id = (isset($json->cv_id)) ? $antiXSS->xss_clean($json->cv_id) : null;
            $city_id = (isset($json->city_id)) ? $antiXSS->xss_clean($json->city_id) : null;
            $cv_lname = (isset($json->cv_lname)) ? $antiXSS->xss_clean($json->cv_lname) : null;
            $cv_user_avatar = (isset($json->cv_user_avatar)) ? $antiXSS->xss_clean($json->cv_user_avatar) : null;
            $cv_brith_date = (isset($json->cv_brith_date)) ? $antiXSS->xss_clean($json->cv_brith_date) : time();
            $cv_gender = (isset($json->cv_gender)) ? $antiXSS->xss_clean($json->cv_gender) : null;
            $cv_marital_status = (isset($json->cv_marital_status)) ? $antiXSS->xss_clean($json->cv_marital_status) : null;
            $cv_military_status = (isset($json->cv_military_status)) ? $antiXSS->xss_clean($json->cv_military_status) : null;
            $cv_military_image = (isset($json->cv_military_image)) ? $antiXSS->xss_clean($json->cv_military_image) : [];
            $cv_military_number = (isset($json->cv_military_number)) ? $antiXSS->xss_clean($json->cv_military_number) : null;
            $cv_military_date = (isset($json->cv_military_date)) ? $antiXSS->xss_clean($json->cv_military_date) : time();
            $cv_smartcard_status = (isset($json->cv_smartcard_status)) ? $antiXSS->xss_clean($json->cv_smartcard_status) : null;
            $cv_smartcard_image = (isset($json->cv_smartcard_image)) ? $antiXSS->xss_clean($json->cv_smartcard_image) : [];
            $cv_smartcard_number = (isset($json->cv_smartcard_number)) ? $antiXSS->xss_clean($json->cv_smartcard_number) : null;
            $cv_smartcard_date = (isset($json->cv_smartcard_date)) ? $antiXSS->xss_clean($json->cv_smartcard_date) : time();
            $cv_passport_status = (isset($json->cv_passport_status)) ? $antiXSS->xss_clean($json->cv_passport_status) : null;
            $cv_passport_image = (isset($json->cv_passport_image)) ? $antiXSS->xss_clean($json->cv_passport_image) : [];
            $cv_passport_number = (isset($json->cv_passport_number)) ? $antiXSS->xss_clean($json->cv_passport_number) : null;
            $cv_passport_date = (isset($json->cv_passport_date)) ? $antiXSS->xss_clean($json->cv_passport_date) : time();
            $cv_visa_status = (isset($json->cv_visa_status)) ? $antiXSS->xss_clean($json->cv_visa_status) : null;
            $cv_visa_image = (isset($json->cv_visa_image)) ? $antiXSS->xss_clean($json->cv_visa_image) : [];
            $cv_visa_number = (isset($json->cv_visa_number)) ? $antiXSS->xss_clean($json->cv_visa_number) : null;
            $cv_visa_date = (isset($json->cv_visa_date)) ? $antiXSS->xss_clean($json->cv_visa_date) : time();
            $cv_visa_location = (($json->cv_visa_location)) ? $antiXSS->xss_clean($json->cv_visa_location) : [];
            $cv_workbook_status = (isset($json->cv_workbook_status)) ? $antiXSS->xss_clean($json->cv_workbook_status) : null;
            $cv_workbook_image = (isset($json->cv_workbook_image)) ? $antiXSS->xss_clean($json->cv_workbook_image) : [];
            $cv_workbook_number = (isset($json->cv_workbook_number)) ? $antiXSS->xss_clean($json->cv_workbook_number) : null;
            $cv_workbook_date = (isset($json->cv_workbook_date)) ? $antiXSS->xss_clean($json->cv_workbook_date) : time();
            $cv_driver_license_status = (isset($json->cv_driver_license_status)) ? $antiXSS->xss_clean($json->cv_driver_license_status) : null;
            $cv_driver_license_image = (isset($json->cv_driver_license_image)) ? $antiXSS->xss_clean($json->cv_driver_license_image) : [];
            $cv_driver_license_number = (isset($json->cv_driver_license_number)) ? $antiXSS->xss_clean($json->cv_driver_license_number) : null;
            $cv_driver_license_date = (isset($json->cv_driver_license_date)) ? $antiXSS->xss_clean($json->cv_driver_license_date) : time();
            $cv_mobile = (isset($json->cv_mobile)) ? $antiXSS->xss_clean($json->cv_mobile) : null;
            $cv_whatsapp = (isset($json->cv_whatsapp)) ? $antiXSS->xss_clean($json->cv_whatsapp) : null;
            $cv_address = (isset($json->cv_address)) ? $antiXSS->xss_clean($json->cv_address) : null;
            $cv_faviroite_country = (($json->cv_faviroite_country)) ? $antiXSS->xss_clean($json->cv_faviroite_country) : [];
            $cv_role_status = (isset($json->cv_role_status)) ? $antiXSS->xss_clean($json->cv_role_status) : null;

            if (!empty($city_id) && !empty($cv_name) && !empty($cv_id) && !empty($cv_lname) && !empty($cv_brith_date) && !empty($cv_gender) && !empty($cv_marital_status)
                && !empty($cv_military_status) && !empty($cv_smartcard_status) && !empty($cv_passport_status) && !empty($cv_visa_status)
                && !empty($cv_workbook_status) && !empty($cv_driver_license_status) && !empty($cv_mobile) && !empty($cv_whatsapp)
                && !empty($cv_address)) {
                $military_image = [];
                if ($cv_military_image != []) {

                    foreach ($cv_military_image as $image) {
                        $upload = Upload::uploadBase64($image, DRIVER_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $military_image[] = $upload->response;
                        }
                    }
                }

                $smartcard_image = [];
                if ($cv_smartcard_image != []) {

                    foreach ($cv_smartcard_image as $image) {
                        $upload = Upload::uploadBase64($image, DRIVER_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $smartcard_image[] = $upload->response;
                        }
                    }
                }

                $passport_image = [];
                if ($cv_passport_image != []) {

                    foreach ($cv_passport_image as $image) {
                        $upload = Upload::uploadBase64($image, DRIVER_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $passport_image[] = $upload->response;
                        }
                    }
                }
                $visa_image = [];
                if ($cv_visa_image != []) {

                    foreach ($cv_visa_image as $image) {
                        $upload = Upload::uploadBase64($image, DRIVER_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $visa_imagee[] = $upload->response;
                        }
                    }
                }
                $workbook_image = [];
                if ($cv_workbook_image != []) {

                    foreach ($cv_workbook_image as $image) {
                        $upload = Upload::uploadBase64($image, DRIVER_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $workbook_image[] = $upload->response;
                        }
                    }
                }
                $driver_license_image = [];
                if ($cv_driver_license_image != []) {

                    foreach ($cv_driver_license_image as $image) {
                        $upload = Upload::uploadBase64($image, DRIVER_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $driver_license_image[] = $upload->response;
                        }
                    }
                }
                $user_avatar = '';
                if (!empty($cv_user_avatar)) {

                    if (str_contains($cv_user_avatar, 'uploads/driver')) {
                        $user_avatar = $cv_user_avatar;
                    } else {
                        $upload = Upload::uploadBase64($cv_user_avatar, DRIVER_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $user_avatar = $upload->response;
                        }
                    }

                }
                /*    print_r(json_encode($city_id,$cv_id, $cv_name, $cv_lname, $cv_brith_date, $cv_gender,
                        $cv_marital_status, $cv_military_status, $military_image, $cv_military_number,
                        $cv_military_date, $cv_smartcard_status, $smartcard_image, $cv_smartcard_number,
                        $cv_smartcard_date, $cv_passport_status, $passport_image, $cv_passport_number,
                        $cv_passport_date, $cv_visa_status, $visa_image, $cv_visa_number, $cv_visa_date,
                        $cv_workbook_status, $workbook_image, $cv_workbook_number, $cv_workbook_date,
                        $cv_driver_license_status, $driver_license_image, $cv_driver_license_number,
                        $cv_driver_license_date, $cv_mobile, $cv_whatsapp, $cv_address, $cv_faviroite_country,
                        $cv_role_status, $user_avatar));*/
                print_r(json_encode(CV::updateDriverCv($city_id, $cv_id, $cv_name, $cv_lname, $cv_brith_date, $cv_gender,
                    $cv_marital_status, $cv_military_status, $military_image, $cv_military_number,
                    $cv_military_date, $cv_smartcard_status, $smartcard_image, $cv_smartcard_number,
                    $cv_smartcard_date, $cv_passport_status, $passport_image, $cv_passport_number,
                    $cv_passport_date, $cv_visa_status, $visa_image, $cv_visa_number, $cv_visa_date, $cv_visa_location,
                    $cv_workbook_status, $workbook_image, $cv_workbook_number, $cv_workbook_date,
                    $cv_driver_license_status, $driver_license_image, $cv_driver_license_number,
                    $cv_driver_license_date, $cv_mobile, $cv_whatsapp, $cv_address, $cv_faviroite_country,
                    $cv_role_status, $user_avatar)));

            } else {
                echo parameterRequired();
            }


            break;
        case 'get-drivers-cv-lists':

            $search_text = (isset($json->search_text)) ? $antiXSS->xss_clean($json->search_text) : null;
            $countries = (isset($json->countries)) ? $antiXSS->xss_clean($json->countries) : [];
            $status = (isset($json->status)) ? $antiXSS->xss_clean($json->status) : null;
            $visa_locations = (isset($json->visa_locations)) ? $antiXSS->xss_clean($json->visa_locations) : [];
            $page = (isset($json->page)) ? $antiXSS->xss_clean($json->page) : 1;
            $records = CV::getCvLists($search_text, $page, $countries, $status, $visa_locations);
            $output = '';
            $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';

            foreach ($records as $record) {
                $role_output = '';
                $role_output_class = $record->cv_role_status == 'yes' ? "busy" : " ";
                if ($record->cv_role_status == 'yes') {
                    $role_output = '  <div class="mj-driver-busy-lottie"><lottie-player src="/dist/lottie/truck2.json" background="transparent" speed="1" loop autoplay></lottie-player></div>';
                }
                $output .= '<div class="mj-account-card mj-driver-card  ' . $role_output_class . ' mb-1  ">
                    <div class="mj-driver-card-title">
                        ' . $role_output . '
                        <div class="mj-driver-item-badge">
                            <img src="/dist/images/drivers/ntirapp-logo.svg" alt="ntirapp">
                        </div>
    
                        <div class="mj-driver-list-profile">
                            <img src="' . Utils::fileExist($record->cv_user_avatar, BOX_EMPTY) . '" alt="dd">
                        </div>
                        <div class="mj-driver-info">
                            <div class="mj-cv-list-driver-name"> ' . $record->cv_name . ' ' . $record->cv_lname . '</div>
                            <div class="mj-cv-list-city">
                                <Span class="mj-cv-city-name">' . array_column(json_decode($record->city_name, true), 'value', 'slug')[$language] . '</Span>
                                |
                                <span>' . Location::getCountryByCityId($record->city_id)->CountryName . '</span>
                            </div>
                        </div>
    
    
                        <svg width="63" height="13" viewBox="0 0 63 13" fill="none"
                             xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_818_507)">
                                <path d="M49.7336 1H0C6.35681 1 11.9766 3.14994 15.2318 6.41296C15.3239 6.55019 15.4314 6.68742 15.5542 6.8094C15.7692 7.08386 16.0149 7.34308 16.2605 7.58704C19.5157 10.8501 25.1355 13 31.4923 13C37.8491 13 43.4689 10.8501 46.7241 7.60229C46.7241 7.58704 46.7241 7.58704 46.7241 7.58704C46.9544 7.37357 47.154 7.14485 47.3536 6.90089C47.5072 6.74841 47.6147 6.59593 47.7375 6.42821C47.7375 6.42821 47.7426 6.42313 47.7529 6.41296C51.008 3.14994 56.6125 1 62.9846 1H49.7183L49.7336 1Z"
                                      fill="white"/>
                            </g>
                            <path d="M36.3906 5.16016L32.5312 8.79102C32.4043 8.91797 32.252 8.96875 32.125 8.96875C31.9727 8.96875 31.8203 8.91797 31.6934 8.81641L27.834 5.16016C27.5801 4.93164 27.5801 4.55078 27.8086 4.29688C28.0371 4.04297 28.418 4.04297 28.6719 4.27148L32.125 7.52148L35.5527 4.27148C35.8066 4.04297 36.1875 4.04297 36.416 4.29688C36.6445 4.55078 36.6445 4.93164 36.3906 5.16016Z"
                                  fill="#9A9A9A"/>
                            <defs>
                                <clipPath id="clip0_818_507">
                                    <rect width="63" height="12" fill="white" transform="translate(0 1)"/>
                                </clipPath>
                            </defs>
                        </svg>
                    </div>
    
                    <div class="mj-driver-subdetail">
                        <div class="mj-account-info-detail mj-driver-info-subdetail">
                            <span>' . $lang['u_driver_cv_birth_day'] . ':</span>
                            <span>' . Utils::getTimeByLang($record->cv_brith_date) . '</span>
                        </div>
                        <div class="mj-account-info-detail my-2 mj-driver-info-subdetail">
                            <span>' . $lang['u_driver_cv_gender'] . ':</span>
                            <span>' . $lang["u_" . $record->cv_gender] . '</span>
                        </div>
                        <div class="mj-account-info-detail my-2 mj-driver-info-subdetail">
                            <span>' . $lang['u_driver_cv_marital_status'] . ':</span>
                            <span>' . $lang["u_" . $record->cv_marital_status] . '</span>
                        </div>
                        
                        <a class="mj-driver-item-list-link" href="/user/drivers/detail/' . $record->cv_id . '">
                            ' . $lang['u_driver_cv_detail'] . '
                        </a>
                    </div>
    
    
                </div>';
            }

            echo $output;


            break;
        case 'get-visa-location':

            echo json_encode(VisaLocation::getAllVisaLocation());

            break;
        /**
         * END USER
         */


        /**
         * START API
         */

        case 'get-cities':
            $isValidRequest = false;

            $countryId = (isset($json->country)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->country)) : null;
            $city = (isset($json->city)) ? $antiXSS->xss_clean($json->city) : null;
            $type = (isset($json->type)) ? $antiXSS->xss_clean($json->type) : null;

            if (!empty($countryId) && !empty($city)) {
                echo json_encode(Businessman::getCities($countryId, $city, $type));
            } else {
                echo parameterRequired();
            }

            break;
        case 'get-cities-for-multi-contries':
            $isValidRequest = false;

            $countryId = (isset($json->country)) ? ($antiXSS->xss_clean($json->country)) : [];


            if (($countryId)) {
                echo json_encode(Location::getCitiesForMultiCountries($countryId));
            } else {
                echo parameterRequired();
            }

            break;
        case 'get-poster-cities':

            $countryId = (isset($json->country)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->country)) : null;
            $city = (isset($json->city)) ? $antiXSS->xss_clean($json->city) : null;
            $type = (isset($json->type)) ? $antiXSS->xss_clean($json->type) : null;

            if (!empty($countryId) && !empty($city)) {
                echo json_encode(Businessman::getCities($countryId, $city, $type));
            } else {
                echo parameterRequired();
            }

            break;
        case 'get-ship-cities':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $countryId = (isset($json->country)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->country)) : null;

                $type = (isset($json->type)) ? $antiXSS->xss_clean($json->type) : null;

                if (!empty($countryId) && !empty($type)) {
                    if ($type == 'port') {
                        echo json_encode(Location::getAllPostForShip($countryId));
                    } else if ($type == 'city') {
                        echo json_encode(Location::getAllCityForShip($countryId));
                    } else {
                        echo parameterRequired();
                    }

                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;
        case 'get-air-cities':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $countryId = (isset($json->country)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->country)) : null;

                $type = (isset($json->type)) ? $antiXSS->xss_clean($json->type) : null;

                if (!empty($countryId) && !empty($type)) {
                    if ($type == 'air') {
                        echo json_encode(Location::getAllAirPortsForAir($countryId));
                    } else if ($type == 'city') {
                        echo json_encode(Location::getAllCityForAir($countryId));
                    } else {
                        echo parameterRequired();
                    }

                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;
        case 'get-railroad-cities':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $countryId = (isset($json->country)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->country)) : null;

                $type = (isset($json->type)) ? $antiXSS->xss_clean($json->type) : null;

                if (!empty($countryId) && !empty($type)) {
                    if ($type == 'railroad') {
                        echo json_encode(Location::getAllCityForRailroad($countryId));
                    } else if ($type == 'city') {
                        echo json_encode(Location::getAllStationForRailroad($countryId));
                    } else {
                        echo parameterRequired();
                    }

                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'get-inventory-cities':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $countryId = (isset($json->country)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->country)) : null;

                $type = (isset($json->type)) ? $antiXSS->xss_clean($json->type) : null;

                if (!empty($countryId) && !empty($type)) {
                    if ($type == 'inventory') {
                        echo json_encode(Location::getAllCityForInventory($countryId));
                    } else {
                        echo parameterRequired();
                    }

                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        /**
         * END API
         */


        /**
         * START BUSINESSMAN
         */

        case 'submit-cargo':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $name = (isset($json->name)) ? $antiXSS->xss_clean($json->name) : null;
                $categoryId = (isset($json->category)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->category)) : null;
                $carTypeId = (isset($json->carType)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->carType)) : null;
                $startDate = (isset($json->startDate)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->startDate)) : null;
                $weight = (isset($json->weight)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->weight)) : null;
                $volume = (isset($json->volume)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->volume)) : null;
                $neededCar = (isset($json->neededCar)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->neededCar)) : null;
                $originCity = (isset($json->originCity)) ? $antiXSS->xss_clean($json->originCity) : null;
                $originId = (isset($json->origin)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->origin)) : null;
                $customsOfOriginId = (isset($json->customsOfOrigin)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->customsOfOrigin)) : null;
                $destinationCity = (isset($json->destinationCity)) ? $antiXSS->xss_clean($json->destinationCity) : null;
                $destinationId = (isset($json->destination)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destination)) : null;
                $destinationCustomsId = (isset($json->destinationCustoms)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destinationCustoms)) : null;
                $recommendedPrice = (int)$antiXSS->xss_clean($json->recommendedPrice);
                $currencyId = (isset($json->currency)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->currency)) : null;
                $description = (isset($json->description)) ? $antiXSS->xss_clean($json->description) : null;
                $images = (isset($json->images)) ? $json->images : [];
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
                $greenStreet = (isset($json->greenStreet) && $json->greenStreet && $json->greenStreet == 1) ? 'yes' : 'no';
                if (!empty($userId) && !empty($name) && !empty($categoryId) && !empty($startDate) && 1 != 0 &&
                    !empty($weight) && !empty($neededCar) && !empty($originId) && !empty($destinationId) &&
                    !empty($currencyId) && !empty($destinationCity) && !empty($originCity) &&
                    !empty($token)) {
                    $imageFiles = [];
                    foreach ($images as $image) {
                        $upload = Upload::uploadBase64($image, CARGOES_IMAGES_ADDRESS);
                        if ($upload->status == 200) {
                            $imageFiles[] = $upload->response;
                        }
                    }
                    $originCity = Location::getCityDetail($originId);
                    $destinationCity = Location::getCityDetail($destinationId);
                    $origin = $originId;
                    $destination = $destinationId;
                    $customsOfOrigin = $customsOfOriginId;
                    $destinationCustoms = $destinationCustomsId;
                    echo json_encode(Businessman::submitCargo($userId, $categoryId, $name, $carTypeId, $neededCar,
                        $weight, $volume, $recommendedPrice, $currencyId, $origin, $customsOfOrigin, $destination,
                        $destinationCustoms, $originCity->CityLat, $originCity->CityLong, $startDate, $description, $imageFiles, $token, $greenStreet));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'submit-cargo-in':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $name = (isset($json->name)) ? $antiXSS->xss_clean($json->name) : null;
                $categoryId = (isset($json->category)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->category)) : null;
                $carTypeId = (isset($json->carType)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->carType)) : null;
                $startDate = (isset($json->startDate)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->startDate)) : null;
                $weight = (isset($json->weight)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->weight)) : null;
                $volume = 0;
                $neededCar = (isset($json->neededCar)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->neededCar)) : null;
                $originId = (isset($json->origin)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->origin)) : null;
                $destinationId = (isset($json->destination)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destination)) : null;
                $recommendedPrice = (int)$antiXSS->xss_clean($json->recommendedPrice);
                $currencyId = (isset($json->currency)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->currency)) : null;
                $description = (isset($json->description)) ? $antiXSS->xss_clean($json->description) : null;
                $images = (isset($json->images)) ? $json->images : [];
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
                if (!empty($userId) && !empty($name) && !empty($categoryId) && !empty($startDate) &&
                    !empty($weight) && !empty($neededCar) && !empty($originId) && !empty($destinationId) &&
                    !empty($currencyId) && !empty($token)) {
                    $imageFiles = [];
                    foreach ($images as $image) {
                        $upload = Upload::uploadBase64($image, CARGOES_IN_IMAGES_ADDRESS);
                        if ($upload->status == 200) {
                            $imageFiles[] = $upload->response;
                        }
                    }
                    $originCity = Location::getCityDetail($originId);
                    $destinationCity = Location::getCityDetail($destinationId);
                    echo json_encode(Businessman::submitCargoIn($userId, $categoryId, $name, $carTypeId, $neededCar,
                        $weight, $volume, $recommendedPrice, $currencyId, $originId, $destinationId,
                        $originCity->CityLat, $originCity->CityLong, $startDate, $description, $imageFiles, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'edit-cargo':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $name = (isset($json->name)) ? $antiXSS->xss_clean($json->name) : null;
                $cargoId = (isset($json->cargo)) ? $antiXSS->xss_clean($json->cargo) : null;
                $categoryId = (isset($json->category)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->category)) : null;
                $carTypeId = (isset($json->carType)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->carType)) : null;
                $startDate = (isset($json->startDate)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->startDate)) : null;
                $weight = (isset($json->weight)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->weight)) : null;
                $volume = (isset($json->volume)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->volume)) : null;
                $neededCar = (isset($json->neededCar)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->neededCar)) : null;
                $originCity = (isset($json->originCity)) ? $antiXSS->xss_clean($json->originCity) : null;
                $originId = (isset($json->origin)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->origin)) : null;
                $customsOfOriginId = (isset($json->customsOfOrigin)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->customsOfOrigin)) : null;
                $destinationCity = (isset($json->destinationCity)) ? $antiXSS->xss_clean($json->destinationCity) : null;
                $destinationId = (isset($json->destination)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destination)) : null;
                $destinationCustomsId = (isset($json->destinationCustoms)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destinationCustoms)) : null;
                $recommendedPrice = (int)$antiXSS->xss_clean($json->recommendedPrice);
                $currencyId = (isset($json->currency)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->currency)) : null;
                $description = (isset($json->description)) ? $antiXSS->xss_clean($json->description) : null;
                $defaultImages = (isset($json->defaultImages)) ? $json->defaultImages : [];
                $newImages = (isset($json->images)) ? $json->images : [];
                $greenStreet = (isset($json->greenStreet) && $json->greenStreet && $json->greenStreet == 1) ? 'yes' : 'no';
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
                if (!empty($userId) && !empty($cargoId) && !empty($name) && !empty($categoryId) && !empty($startDate) &&
                    !empty($weight) && !empty($neededCar) && !empty($originId) && !empty($destinationId) &&
                    !empty($currencyId) && !empty($destinationCity) && !empty($originCity) &&
                    !empty($token)) {
                    $imageFiles = $defaultImages;
                    foreach ($newImages as $image) {
                        $upload = Upload::uploadBase64($image, CARGOES_IMAGES_ADDRESS);
                        if ($upload->status == 200) {
                            $imageFiles[] = $upload->response;
                        }
                    }
                    $originCity = Location::getCityDetail($originId);
                    $destinationCity = Location::getCityDetail($destinationId);
                    $origin = $originId;
                    $destination = $destinationId;
                    $customsOfOrigin = $customsOfOriginId;
                    $destinationCustoms = $destinationCustomsId;

                    echo json_encode(Businessman::editCargo($userId, $cargoId, $categoryId, $name, $carTypeId, $neededCar,
                        $weight, $volume, $recommendedPrice, $currencyId, $origin, $customsOfOrigin, $destination,
                        $destinationCustoms, $originCity->CityLat, $originCity->CityLong, $startDate, $description, $imageFiles, $token, $greenStreet));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'edit-cargo-in':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $name = (isset($json->name)) ? $antiXSS->xss_clean($json->name) : null;
                $cargoId = (isset($json->cargo)) ? $antiXSS->xss_clean($json->cargo) : null;
                $categoryId = (isset($json->category)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->category)) : null;
                $carTypeId = (isset($json->carType)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->carType)) : null;
                $startDate = (isset($json->startDate)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->startDate)) : null;
                $weight = (isset($json->weight)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->weight)) : null;
                $volume = 0;
                $neededCar = (isset($json->neededCar)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->neededCar)) : null;
                $originId = (isset($json->origin)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->origin)) : null;
                $destinationId = (isset($json->destination)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destination)) : null;
                $recommendedPrice = (int)$antiXSS->xss_clean($json->recommendedPrice);
                $currencyId = (isset($json->currency)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->currency)) : null;
                $description = (isset($json->description)) ? $antiXSS->xss_clean($json->description) : null;
                $defaultImages = (isset($json->defaultImages)) ? $json->defaultImages : [];
                $newImages = (isset($json->images)) ? $json->images : [];
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($cargoId) && !empty($name) && !empty($categoryId) && !empty($startDate) &&
                    !empty($weight) && !empty($neededCar) && !empty($originId) && !empty($destinationId) &&
                    !empty($currencyId) && !empty($token)) {

                    $imageFiles = $defaultImages;
                    foreach ($newImages as $image) {
                        $upload = Upload::uploadBase64($image, CARGOES_IN_IMAGES_ADDRESS);
                        if ($upload->status == 200) {
                            $imageFiles[] = $upload->response;
                        }
                    }

                    $originCity = Location::getCityDetail($originId);
                    $destinationCity = Location::getCityDetail($destinationId);


                    echo json_encode(Businessman::editCargoIn($userId, $cargoId, $categoryId, $name, $carTypeId, $neededCar,
                        $weight, $volume, $recommendedPrice, $currencyId, $originId, $destinationId,
                        $originCity->CityLat, $originCity->CityLong, $startDate, $description, $imageFiles, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'get-businessman-cargoes':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $page = (isset($json->page)) ? $antiXSS->xss_clean($json->page) : 0;
                $cargo_status = (isset($json->cargo_status)) ? $antiXSS->xss_clean($json->cargo_status) : null;
                $search_value = (isset($json->search_value)) ? $antiXSS->xss_clean($json->search_value) : null;

                if (!empty($userId) && !empty($cargo_status) && !empty($search_value)) {
                    $records = Businessman::getCargoList($userId, $cargo_status, $search_value, $page);
                    $html = '';
                    if ($records->status == 200) {
                        foreach ($records->response as $item) {
                            $startDate = ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('Y/m/d', $item->CargoStartDate) : date('Y-m-d', $item->CargoStartDate);
                            $type_text = ($item->CargoType == "in") ? $lang["u_cargo_in"] : $lang["u_cargo_out"];
                            $link = ($item->CargoType == "in") ? '/businessman/cargo-in-detail/' . $item->CargoId : '/businessman/cargo-detail/' . $item->CargoId;
                            $logo = ($item->CargoType == "in") ? "inter" : "outer";
                            $logo = Utils::fileExist("/dist/images/" . $logo . ".svg", BOX_EMPTY);
                            $html .= ' <div class="mj-trx-item">
                                <a href="' . $link . '">
                                    <div class="mj-trx-info">
                                        <div class="mj-cargo-status ' . $item->CargoStatus . '">
                                            <img src="' . $logo . '" alt="">
                                        </div>
                                        <div class="mj-trx-detail"><span
                                                    class="mj-trx-account-name">' . $item->CargoName . '</span>
                                            <div class="mj-trx-date d-flex align-items-center">
                                                <span class="trx-date">' . $type_text . '</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mj-my-cargo-value ">
                                        <span> ' . $lang["start_date"] . ' : </span>
                                        <span>' . $startDate . '</span>
                                    </div>
                                </a>
                            </div>';

                        }
                    } else if ($records->status == 204) {
                        $html .= ' <div class="mj-empty-cargoes-list">
                            <lottie-player src="/dist/lottie/emptycargo.json" background="transparent"   speed="1"  style="width: 250px; height: 250px;"  loop  autoplay></lottie-player>
                        </div>';
                    }
                    echo $html;
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'change-extra-expense-status':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $expenseId = (isset($json->expense)) ? intval($antiXSS->xss_clean($json->expense)) : null;
                $requestId = (isset($json->request)) ? intval($antiXSS->xss_clean($json->request)) : null;
                $cargoId = (isset($json->cargo)) ? intval($antiXSS->xss_clean($json->cargo)) : null;
                $driverId = (isset($json->driver)) ? intval($antiXSS->xss_clean($json->driver)) : null;
                $status = (isset($json->status)) ? $antiXSS->xss_clean($json->status) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($expenseId) && !empty($requestId) && !empty($cargoId) && !empty($driverId) && !empty($status) && !empty($token)) {
                    echo json_encode(Businessman::changeExpenseStatus($userId, $expenseId, $requestId, $cargoId, $driverId, $status, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'change-extra-expense-in-status':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $expenseId = (isset($json->expense)) ? intval($antiXSS->xss_clean($json->expense)) : null;
                $requestId = (isset($json->request)) ? intval($antiXSS->xss_clean($json->request)) : null;
                $cargoId = (isset($json->cargo)) ? intval($antiXSS->xss_clean($json->cargo)) : null;
                $driverId = (isset($json->driver)) ? intval($antiXSS->xss_clean($json->driver)) : null;
                $status = (isset($json->status)) ? $antiXSS->xss_clean($json->status) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($expenseId) && !empty($requestId) && !empty($cargoId) && !empty($driverId) && !empty($status) && !empty($token)) {
                    echo json_encode(Businessman::changeExpenseStatusIn($userId, $expenseId, $requestId, $cargoId, $driverId, $status, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;


        case 'submit-rate-to-request':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $requestId = (isset($json->request)) ? intval($antiXSS->xss_clean($json->request)) : null;
                $cargoId = (isset($json->cargo)) ? intval($antiXSS->xss_clean($json->cargo)) : null;
                $rate = (isset($json->rate)) ? intval($antiXSS->xss_clean($json->rate)) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($requestId) && !empty($cargoId) && !empty($rate) && !empty($token)) {
                    echo json_encode(Businessman::submitRequestRate($cargoId, $requestId, $rate, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'submit-rate-to-request-in':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $requestId = (isset($json->request)) ? intval($antiXSS->xss_clean($json->request)) : null;
                $cargoId = (isset($json->cargo)) ? intval($antiXSS->xss_clean($json->cargo)) : null;
                $rate = (isset($json->rate)) ? intval($antiXSS->xss_clean($json->rate)) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($requestId) && !empty($cargoId) && !empty($rate) && !empty($token)) {
                    echo json_encode(Businessman::submitRequestInRate($cargoId, $requestId, $rate, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'cancel-cargo':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $cargoId = (isset($json->cargo)) ? intval($antiXSS->xss_clean($json->cargo)) : null;
                $reason = (isset($json->reason)) ? $antiXSS->xss_clean($json->reason) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($cargoId) && !empty($reason) && !empty($token)) {
                    echo json_encode(Businessman::cancelCargo($userId, $cargoId, $reason, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'cancel-cargo-in':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $cargoId = (isset($json->cargo)) ? intval($antiXSS->xss_clean($json->cargo)) : null;
                $reason = (isset($json->reason)) ? $antiXSS->xss_clean($json->reason) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($cargoId) && !empty($reason) && !empty($token)) {
                    echo json_encode(Businessman::cancelCargoIn($userId, $cargoId, $reason, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'change-request-status':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $cargoId = (isset($json->cargo)) ? intval($antiXSS->xss_clean($json->cargo)) : null;
                $requestId = (isset($json->request)) ? intval($antiXSS->xss_clean($json->request)) : null;
                $driverId = (isset($json->driver)) ? intval($antiXSS->xss_clean($json->driver)) : null;
                $status = (isset($json->status)) ? $antiXSS->xss_clean($json->status) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($cargoId) && !empty($requestId) && !empty($driverId) && !empty($status) && !empty($token)) {
                    echo json_encode(Businessman::changeRequestStatus($userId, $cargoId, $requestId, $driverId, $status, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'change-request-in-status':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $cargoId = (isset($json->cargo)) ? intval($antiXSS->xss_clean($json->cargo)) : null;
                $requestId = (isset($json->request)) ? intval($antiXSS->xss_clean($json->request)) : null;
                $driverId = (isset($json->driver)) ? intval($antiXSS->xss_clean($json->driver)) : null;
                $status = (isset($json->status)) ? $antiXSS->xss_clean($json->status) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($cargoId) && !empty($requestId) && !empty($driverId) && !empty($status) && !empty($token)) {
                    echo json_encode(Businessman::changeRequestInStatus($userId, $cargoId, $requestId, $driverId, $status, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'ground-freight-price':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $originId = (isset($json->origin)) ? intval($antiXSS->xss_clean($json->origin)) : null;
                $destinationId = (isset($json->destination)) ? intval($antiXSS->xss_clean($json->destination)) : null;
                $categoryId = (isset($json->category)) ? intval($antiXSS->xss_clean($json->category)) : null;
                $currencyId = (isset($json->currency)) ? intval($antiXSS->xss_clean($json->currency)) : null;
                $origin = (isset($json->originName)) ? $antiXSS->xss_clean($json->originName) : null;
                $destination = (isset($json->destinationName)) ? $antiXSS->xss_clean($json->destinationName) : null;
                $category = (isset($json->categoryName)) ? $antiXSS->xss_clean($json->categoryName) : null;
                $currency = (isset($json->currencyName)) ? $antiXSS->xss_clean($json->currencyName) : null;

                $names = [
                    $origin,
                    $destination,
                    $category,
                    $currency
                ];

                if (!empty($userId) && !empty($originId) && !empty($destinationId) && !empty($categoryId) && !empty($currencyId)) {
                    echo json_encode(Businessman::freightPriceInquiry($userId, $originId, $destinationId, $categoryId, $currencyId, $names));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'inquiry-ground':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $name = (isset($json->name)) ? $antiXSS->xss_clean($json->name) : null;
                $categoryId = (isset($json->category)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->category)) : null;
                $carTypeId = (isset($json->carType)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->carType)) : null;
                $startDate = (isset($json->startDate)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->startDate)) : null;
                $weight = (isset($json->weight)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->weight)) : null;
                $volume = (isset($json->volume)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->volume)) : null;
                $originCity = (isset($json->originCity)) ? $antiXSS->xss_clean($json->originCity) : null;
                $originId = (isset($json->origin)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->origin)) : null;
                $customsOfOriginId = (isset($json->customsOfOrigin)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->customsOfOrigin)) : null;
                $destinationCity = (isset($json->destinationCity)) ? $antiXSS->xss_clean($json->destinationCity) : null;
                $destinationId = (isset($json->destination)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destination)) : null;
                $destinationCustomsId = (isset($json->destinationCustoms)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destinationCustoms)) : null;
                $description = (isset($json->description)) ? $antiXSS->xss_clean($json->description) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($name) && !empty($categoryId) && !empty($startDate) &&
                    !empty($originId) && !empty($destinationId) && !empty($destinationCity) && !empty($originCity) &&
                    !empty($token)) {
                    echo json_encode(Ground::inquiryGroundInsert($userId, $name, $categoryId,
                        $carTypeId, $startDate, $weight, $volume, $originId,
                        $customsOfOriginId, $destinationId, $destinationCustomsId,
                        $description, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'inquiry-ship':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $name = (isset($json->name)) ? $antiXSS->xss_clean($json->name) : null;
                $categoryId = (isset($json->category)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->category)) : null;
                $container_id = (isset($json->carType)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->carType)) : null;
                $startDate = (isset($json->startDate)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->startDate)) : null;
                $weight = (isset($json->weight)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->weight)) : null;
                $volume = (isset($json->volume)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->volume)) : null;
                $cargoContainerCount = (isset($json->cargoContainerCount)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->cargoContainerCount)) : null;
                $originCity = (isset($json->originCity)) ? $antiXSS->xss_clean($json->originCity) : null;
                $source_city_id = (isset($json->origin)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->origin)) : null;
                $source_port_id = (isset($json->sourcePort)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->sourcePort)) : null;
                $destinationCity = (isset($json->destinationCity)) ? $antiXSS->xss_clean($json->destinationCity) : null;
                $dest_city_id = (isset($json->destination)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destination)) : null;
                $dest_port_id = (isset($json->destPort)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destPort)) : null;
                $description = (isset($json->description)) ? $antiXSS->xss_clean($json->description) : null;
                $packing_id = 1;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($name) && !empty($categoryId) && !empty($startDate) &&
                    !empty($source_city_id) && !empty($dest_city_id) && !empty($dest_port_id) && !empty($source_port_id) &&
                    !empty($token)) {
                    echo json_encode(Ship::inquiryShipInsert(
                        $userId, $source_city_id, $dest_city_id, $source_port_id, $dest_port_id, $categoryId, $container_id
                        , $packing_id, $name, $description, $cargoContainerCount,
                        $weight, $volume, $startDate, $token
                    ));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'inquiry-air':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }
            if ($isValidRequest) {
                $name = (isset($json->name)) ? $antiXSS->xss_clean($json->name) : null;
                $categoryId = (isset($json->category)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->category)) : null;
                $startDate = (isset($json->startDate)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->startDate)) : null;
                $weight = (isset($json->weight)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->weight)) : null;
                $volume = (isset($json->volume)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->volume)) : null;
                $originCity = (isset($json->originCity)) ? $antiXSS->xss_clean($json->originCity) : null;
                $source_city_id = (isset($json->origin)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->origin)) : null;
                $source_port_id = (isset($json->sourcePort)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->sourcePort)) : null;
                $destinationCity = (isset($json->destinationCity)) ? $antiXSS->xss_clean($json->destinationCity) : null;
                $dest_city_id = (isset($json->destination)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destination)) : null;
                $dest_port_id = (isset($json->destPort)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destPort)) : null;
                $description = (isset($json->description)) ? $antiXSS->xss_clean($json->description) : null;
                $price = (isset($json->cargoRecommendedPrice)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->cargoRecommendedPrice)) : null;
                $currencyId = (isset($json->cargoMonetaryUnit)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->cargoMonetaryUnit)) : null;
                $packing_id = (isset($json->packing)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->packing)) : null;
                $cargoDischarge = (isset($json->cargoDischarge)) ? $antiXSS->xss_clean($json->cargoDischarge) : null;

                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
                if ($cargoDischarge) {
                    $cargoDischarge = 'yes';
                } else {
                    $cargoDischarge = 'no';
                }
                if (!empty($userId) && !empty($name) && !empty($categoryId) && !empty($startDate) &&
                    !empty($source_city_id) && !empty($dest_city_id) && !empty($dest_port_id) && !empty($source_port_id) &&
                    !empty($token)) {
                    echo json_encode(Air::insertInquiryAir(
                        $userId, $source_city_id, $dest_city_id, $source_port_id, $dest_port_id, $categoryId,
                        $packing_id
                        , $currencyId, $name, $description, $price,
                        $weight, $volume, $cargoDischarge, $startDate, $token
                    ));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'inquiry-railroad':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $name = (isset($json->name)) ? $antiXSS->xss_clean($json->name) : null;
                $categoryId = (isset($json->category)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->category)) : null;
                $wagonType = (isset($json->wagonType)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->wagonType)) : null;
                $containerType = (isset($json->containerType)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->containerType)) : null;
                $packingType = (isset($json->packingType)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->packingType)) : null;
                $startDate = (isset($json->startDate)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->startDate)) : null;
                $weight = (isset($json->weight)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->weight)) : null;
                $volume = (isset($json->volume)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->volume)) : null;

                $origin = (isset($json->origin)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->origin)) : null;
                $source_railroad_id = (isset($json->customsOfOrigin)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->customsOfOrigin)) : null;
                $destination = (isset($json->destination)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destination)) : null;
                $dest_railroad_id = (isset($json->destinationCustoms)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destinationCustoms)) : null;
                $description = (isset($json->description)) ? $antiXSS->xss_clean($json->description) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($name) && !empty($categoryId) && !empty($startDate) &&
                    !empty($origin) && !empty($destination) && !empty($dest_railroad_id) && !empty($source_railroad_id) &&
                    !empty($token)) {
                    echo json_encode(
                        Railroad::insertInquiryRailroad($userId, $name, $categoryId, $wagonType, $containerType, $packingType, $startDate, $weight, $volume, $origin, $source_railroad_id, $destination, $dest_railroad_id, $description, $token)
                    );
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'inquiry-inventory':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $name = (isset($json->name)) ? $antiXSS->xss_clean($json->name) : null;
                $categoryId = (isset($json->category)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->category)) : null;
                $typeId = (isset($json->type)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->type)) : null;
                $startDate = (isset($json->startDate)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->startDate)) : null;
                $weight = (isset($json->weight)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->weight)) : null;
                $volume = (isset($json->volume)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->volume)) : null;
                $durationDay = (isset($json->durationDay)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->durationDay)) : null;

                $origin = (isset($json->origin)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->origin)) : null;
                $source_railroad_id = (isset($json->customsOfOrigin)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->customsOfOrigin)) : null;
                $destination = (isset($json->destination)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destination)) : null;
                $dest_railroad_id = (isset($json->destinationCustoms)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->destinationCustoms)) : null;
                $description = (isset($json->description)) ? $antiXSS->xss_clean($json->description) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($name) && !empty($categoryId) && !empty($typeId) && !empty($startDate) &&
                    !empty($origin) && !empty($token)) {

                    echo json_encode(
                        Inventory::insertInquiryRailroad($userId, $name, $categoryId, $typeId, $startDate, $weight, $volume, $durationDay, $origin, $description, $token)
                    );
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;
        /**
         * END BUSINESSMAN
         */


        /**
         * Start Driver
         */

        case 'get-plaque-types':
            echo Utils::getFileValue('plaque_types.json', null, false);
            break;

        case 'new-car':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $typeId = (isset($json->car)) ? intval($antiXSS->xss_clean($json->car)) : null;
                $plaqueType = (isset($json->type)) ? $antiXSS->xss_clean($json->type) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
                $name = (isset($json->name)) ? $antiXSS->xss_clean($json->name) : null;
                $plaque = (isset($json->plaque)) ? $antiXSS->xss_clean($json->plaque) : null;
                $images = $json->images;

                $ImagesItem = [];

                foreach ($images as $index => $image) {
                    $upload = Upload::uploadBase64($image, CARS_ADDRESS);
                    if ($upload->status == 200) {
                        $ImagesItem[] = $upload->response;
                    }
                    usleep(5000);
                }
                if (!empty($name) && !empty($typeId) && !empty($token)) {
                    echo json_encode(Driver::newCar($userId, $typeId, $name, $plaqueType, $plaque, $token, $ImagesItem));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'delete-car':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $carId = (isset($json->car)) ? $antiXSS->xss_clean($json->car) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
                if (!empty($carId) && !empty($token)) {
                    echo json_encode(Driver::deleteCar($carId, $userId, $token));
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'send-request':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $cargoId = (isset($json->cargo)) ? $antiXSS->xss_clean($json->cargo) : null;
                $carId = (isset($json->car)) ? $antiXSS->xss_clean($json->car) : null;
                $price = (isset($json->price)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->price)) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
                if (!empty($cargoId) && !empty($carId) && !empty($price) && !empty($token)) {
                    echo json_encode(Driver::sendRequest($userId, $cargoId, $carId, $price, $token));
                }
            } else {
                echo permissionAccess();
            }
            break;


        case 'send-request-in':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $cargoId = (isset($json->cargo)) ? $antiXSS->xss_clean($json->cargo) : null;
                $carId = (isset($json->car)) ? $antiXSS->xss_clean($json->car) : null;
                $price = (isset($json->price)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->price)) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
                if (!empty($cargoId) && !empty($carId) && !empty($price) && !empty($token)) {
                    echo json_encode(Driver::sendRequestIn($userId, $cargoId, $carId, $price, $token));
                }
            } else {
                echo permissionAccess();
            }
            break;


        case 'start-transportation':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $cargoId = (isset($json->cargo)) ? $antiXSS->xss_clean($json->cargo) : null;
                $requestId = (isset($json->request)) ? $antiXSS->xss_clean($json->request) : null;
                $images = (isset($json->images)) ? $json->images : [];
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                $ImagesItem = [];
                foreach ($images as $image) {
                    $upload = Upload::uploadBase64($image, '/uploads/transportation');
                    if ($upload->status == 200) {
                        $ImagesItem[] = $upload->response;
                    }
                }

                if (!empty($userId) && !empty($requestId) && !empty($cargoId) && !empty($token)) {
                    echo json_encode(Driver::startTransportation($requestId, $cargoId, $userId, $token, $ImagesItem));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;


        case 'start-transportation-in':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $cargoId = (isset($json->cargo)) ? $antiXSS->xss_clean($json->cargo) : null;
                $requestId = (isset($json->request)) ? $antiXSS->xss_clean($json->request) : null;
                $images = (isset($json->images)) ? $json->images : [];
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                $ImagesItem = [];
                foreach ($images as $image) {
                    $upload = Upload::uploadBase64($image, '/uploads/transportation-in');
                    if ($upload->status == 200) {
                        $ImagesItem[] = $upload->response;
                    }
                }

                if (!empty($userId) && !empty($requestId) && !empty($cargoId) && !empty($token)) {
                    echo json_encode(Driver::startTransportationIn($requestId, $cargoId, $userId, $token, $ImagesItem));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;


        case 'new-extra-expenses':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $cargoId = (isset($json->cargo)) ? $antiXSS->xss_clean($json->cargo) : null;
                $requestId = (isset($json->request)) ? $antiXSS->xss_clean($json->request) : null;
                $title = (isset($json->title)) ? $antiXSS->xss_clean($json->title) : null;
                $amount = (isset($json->amount)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->amount)) : null;
                $unit = (isset($json->unit)) ? $antiXSS->xss_clean($json->unit) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($requestId) && !empty($cargoId) && !empty($title) && !empty($amount) && !empty($unit) && !empty($token)) {
                    echo json_encode(Driver::requestExtraExpenses($userId, $requestId, $cargoId, $title, $amount, $unit, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'new-extra-expenses-in':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $cargoId = (isset($json->cargo)) ? $antiXSS->xss_clean($json->cargo) : null;
                $requestId = (isset($json->request)) ? $antiXSS->xss_clean($json->request) : null;
                $title = (isset($json->title)) ? $antiXSS->xss_clean($json->title) : null;
                $amount = (isset($json->amount)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->amount)) : null;
                $unit = (isset($json->unit)) ? $antiXSS->xss_clean($json->unit) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($requestId) && !empty($cargoId) && !empty($title) && !empty($amount) && !empty($unit) && !empty($token)) {
                    echo json_encode(Driver::requestExtraExpensesIn($userId, $requestId, $cargoId, $title, $amount, $unit, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'get-driver-requests':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }
            if ($isValidRequest) {
                $page = (isset($json->page)) ? $antiXSS->xss_clean($json->page) : 0;
                $request_status = (isset($json->request_status)) ? $antiXSS->xss_clean($json->request_status) : null;
                $search_value = (isset($json->search_value)) ? $antiXSS->xss_clean($json->search_value) : null;
                if (!empty($userId) && !empty($request_status) && !empty($search_value)) {
                    $records = Driver::getMyRequestsList($userId, $request_status, $search_value, $page);
                    $html = '';
                    if ($records->status == 200) {
                        foreach ($records->response as $item) {
                            $request_date = ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('Y/m/d', $item->RequestDate) : date('Y-m-d', $item->RequestDate);
                            $type_text = ($item->RequestType == "in") ? $lang["u_cargo_in"] : $lang["u_cargo_out"];
                            $link = ($item->RequestType == "in") ? '/cargo-in-ads/' . $item->CargoId : '/cargo-ads/' . $item->CargoId;
                            $logo = ($item->RequestType == "in") ? "inter" : "outer";
                            $logo = Utils::fileExist("/dist/images/" . $logo . ".svg", BOX_EMPTY);
                            $html .= ' <div class="mj-trx-item">
                                <a href="' . $link . '">
                                    <div class="mj-trx-info">
                                        <div class="mj-cargo-status ' . $item->RequestStatus . '">
                                            <img src="' . $logo . '" alt="">
                                        </div>
                                        <div class="mj-trx-detail"><span
                                                    class="mj-trx-account-name">' . $item->CargoName . '</span>
                                            <div class="mj-trx-date d-flex align-items-center">
                                                <span class="trx-date">' . $type_text . '</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mj-my-cargo-value ">
                                        <span> ' . $lang["start_date"] . ' : </span>
                                        <span>' . $request_date . '</span>
                                    </div>
                                </a>
                            </div>';

                        }
                    } else if ($records->status == 204) {
                        $html .= ' <div class="mj-empty-cargoes-list">
                            <lottie-player src="/dist/lottie/emptycargo.json" background="transparent"   speed="1"  style="width: 250px; height: 250px;"  loop  autoplay></lottie-player>
                        </div>';
                    }

                    //                    print_r($records);
                    echo $html;
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'load-more-cargo':


            $source_country = (isset($json->source_country)) ? $antiXSS->xss_clean($json->source_country) : null;
            $dest_country = (isset($json->dest_country)) ? $antiXSS->xss_clean($json->dest_country) : null;
            $source_city = (isset($json->source_city)) ? $antiXSS->xss_clean($json->source_city) : null;
            $dest_city = (isset($json->dest_city)) ? $antiXSS->xss_clean($json->dest_city) : null;
            $car_type = (isset($json->car_type)) ? $antiXSS->xss_clean($json->car_type) : null;
            $page = (isset($json->page)) ? $antiXSS->xss_clean($json->page) : 0;
            if (!empty($source_city) && !empty($dest_city) && !empty($source_country) && !empty($dest_country)) {
                $records = Driver::getCargoList($source_country, $dest_country, $source_city, $dest_city, $car_type, $page, 9);
                //
                //                print_r($records);
                //                exit(0);
                $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
                $html = '';
                foreach ($records->response as $item) {
                    $categoryIcon = Utils::fileExist($item->CategoryIcon, BOX_EMPTY);
                    $price = number_format($item->CargoRecomendedPrice);
                    $startTransportation = '';
                    if (($item->CargoStaus == 'accepted' || $item->CargoStaus == 'progress') && $item->CargoStartTransportation <= time() && ($item->CargoStartTransportation + CARGO_READY_TO_LOAD) >= time()) {
                        $startTransportation = $lang['u_date_ready_to_load'];
                    } else {
                        $startTransportation = ($language == 'fa_IR') ? Utils::jDate('Y/m/d', $item->CargoStartTransportation) : date('Y-m-d', $item->CargoStartTransportation);
                    }

                    $flagGreen = ($item->CargoGreen == "yes") ? '<div class="mj-green-road-blob"></div>' : null;
                    $flagGreenPS = ($item->CargoGreen == "yes") ? "ps-2" : "";

                    $flagPrice = ($item->CargoRecomendedPrice == 0) ? $lang['u_agreement'] : number_format($item->CargoRecomendedPrice) . '<small>' . $item->CargoMonetaryUnit . '</small>';
                    $type_car_output = '';

                    $slugname = 'CargoName_' . $language;
                    if ($item->TypeId == 18) {
                        $type_car_output = '<div class="mj-d-cargo-car-type-badge d-flex">
                            <div class="mj-ambulnace-light me-2">
                                <div id="light-lamp">
                                    <div id="lamp-spinner"></div>
                                </div>
    
                                <div id="light-lamp-bottom"></div>
                            </div>
                            <span> ' . $item->TypeName . '</span>
                        </div>';
                    } else {
                        $type_car_output = '<div class="mj-d-cargo-car-type-badge d-flex">
                             <img src="' . Utils::fileExist($item->TypeIcon, BOX_EMPTY) . '" alt="">
                            <span> ' . $item->TypeName . '</span>
                        </div>';
                    }
                    $html = $html . '<div class="mj-d-cargo-card">
                            <div class="mj-d-cargo-card-badge">
                                ' . $item->CargoId . '
                            </div>
     
                                ' . $type_car_output . ' 
                            <div class="card-body">
                                <div class="d-flex align-items-center mt-2 mb-2">
                                    <div class="mj-d-cargo-item-category me-2"
                                         style="background: ' . $item->CategoryColor . '">
                                        <img src="' . Utils::fileExist($item->CategoryIcon, BOX_EMPTY) . '" alt="' . $item->CategoryName . '">
                                        <span>' . $item->CategoryName . '</span>
                                    </div>
                                    <div class="flex-fill">
                                        <h2 class="mj-d-cargo-item-header mt-0 mb-2">' . $item->$slugname . '</h2>
                                        <div class="mj-d-cargo-item-price-box d-flex align-items-center justify-content-between">
                                            <span>' . $lang['d_cargo_price'] . ':</span>
                                            <span>
                                            ' . $flagPrice . '
                                         </span>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="row">
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="position-relative">
                                                <img src="/dist/images/icons/arrow-up-left-from-circle.svg"
                                                     class="mj-d-cargo-item-icon me-2" alt="origin"/>
                                                     
                                                     ' . $flagGreen . '
                                            </div>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['d_cargo_origin'] . ':</div>
                                                <div class="mj-d-cargo-item-value mj-ellipse ' . $flagGreenPS . '">' . $item->CargoOrigin . " - " . Driver::getCountryByCities($item->CargoOriginid)->response . '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-4">
                                            <img src="/dist/images/icons/calendar-star.svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="loading-time"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['d_cargo_loading_time'] . ':
                                                </div>
                                                <div class="mj-d-cargo-item-value">' . $startTransportation . '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="/dist/images/icons/arrow-down-left-from-circle.svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="destination"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['d_cargo_destination'] . ':
                                                </div>
                                                <div class="mj-d-cargo-item-value mj-ellipse">' . $item->CargoDestination . " - " . Driver::getCountryByCities($item->CargoDestinationid)->response . '</div></div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="/dist/images/icons/truck-container(blue).svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="weight"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['b_cargo_car_needed'] . ':</div>
                                                <div class="mj-d-cargo-item-value">
                                                    ' . $item->CargoCarCount . ' </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex">
                                <a data-id="' . $item->CargoId . '" href="javascript:void(0);"
                                   class="mj-d-cargo-item-link mj-btn mj-btn-primary">
                                    ' . $lang['d_cargo_show'] . '
                                </a> 
                                <a href="javascript:viod(0);"
                                   class="mj-d-cargo-item-link2 mj-btn mj-btn-primary">
                                    ' . $lang['d_cargo_owner_call'] . '
                                </a>
                                </div>
                                
                            </div>
                        </div>
                            ';

                }
                echo $html;
            } else {
                echo parameterRequired();
            }


            break;
        case 'load-more-cargo-in':


            $source_country = (isset($json->source_country)) ? $antiXSS->xss_clean($json->source_country) : null;
            $source_city = (isset($json->source_city)) ? $antiXSS->xss_clean($json->source_city) : null;
            $dest_city = (isset($json->dest_city)) ? $antiXSS->xss_clean($json->dest_city) : null;
            $car_type = (isset($json->car_type)) ? $antiXSS->xss_clean($json->car_type) : null;
            $page = (isset($json->page)) ? $antiXSS->xss_clean($json->page) : 0;
            if (!empty($source_city) && !empty($dest_city) && !empty($source_country)) {
                $records = Driver::getCargoListIn($source_country, $source_city, $dest_city, $car_type, $page, 9);

                $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
                //                print_r($records);
                //                exit(0);
                $html = '';
                foreach ($records->response as $item) {
                    $categoryIcon = Utils::fileExist($item->CategoryIcon, BOX_EMPTY);
                    $price = number_format($item->CargoRecomendedPrice);
                    $startTransportation = ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('Y/m/d', $item->CargoStartTransportation) : date('Y-m-d', $item->CargoStartTransportation);


                    $flagPrice = ($item->CargoRecomendedPrice == 0) ? $lang['u_agreement'] : number_format($item->CargoRecomendedPrice) . '<small>' . $item->CargoMonetaryUnit . '</small>';
                    $slugname = 'CargoName_' . $language;
                    $transportation = '';
                    if (($item->CargoStaus == 'accepted' || $item->CargoStaus == 'progress') && $item->CargoStartTransportation <= time() && ($item->CargoStartTransportation + CARGO_READY_TO_LOAD) >= time()) {
                        $transportation = $lang['u_date_ready_to_load'];
                    } else {
                        $transportation = ($language == 'fa_IR') ? Utils::jDate('Y/m/d', $item->CargoStartTransportation) : date('Y-m-d', $item->CargoStartTransportation);
                    }

                    $html = $html . '<div class="mj-d-cargo-card">
                            <div class="mj-d-cargo-card-badge">
                                ' . $item->CargoId . '
                            </div>
                            <div class="mj-d-cargo-car-type-badge">
                                <img src="' . Utils::fileExist($item->TypeIcon, BOX_EMPTY) . '" alt="">
                                ' . $item->TypeName . '
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center mt-2 mb-2">
                                    <div class="mj-d-cargo-item-category me-2"
                                         style="background: ' . $item->CategoryColor . '">
                                        <img src="' . Utils::fileExist($item->CategoryIcon, BOX_EMPTY) . '" alt="' . $item->CategoryName . '">
                                        <span>' . $item->CategoryName . '</span>
                                    </div>
                                    <div class="flex-fill">
                                        <h2 class="mj-d-cargo-item-header mt-0 mb-2">' . $item->$slugname . '</h2>
                                        <div class="mj-d-cargo-item-price-box d-flex align-items-center justify-content-between">
                                            <span>' . $lang['d_cargo_price'] . ':</span>
                                            <span>
                                            ' . $flagPrice . '
                                         </span>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="row">
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="position-relative">
                                                <img src="/dist/images/icons/arrow-up-left-from-circle.svg"
                                                     class="mj-d-cargo-item-icon me-2" alt="origin"/>
                                            </div>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['d_cargo_origin'] . ':</div>
                                                <div class="mj-d-cargo-item-value mj-ellipse ">' . $item->CargoOrigin . " - " . Driver::getCountryByCities($item->CargoOriginid)->response . '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-4">
                                            <img src="/dist/images/icons/calendar-star.svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="loading-time"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['d_cargo_loading_time'] . ':
                                                </div>
                                                <div class="mj-d-cargo-item-value">' . $transportation . '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="/dist/images/icons/arrow-down-left-from-circle.svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="destination"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['d_cargo_destination'] . ':
                                                </div>
                                                <div class="mj-d-cargo-item-value mj-ellipse">' . $item->CargoDestination . '</div></div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="/dist/images/icons/truck-container(blue).svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="weight"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['b_cargo_car_needed'] . ':</div>
                                                <div class="mj-d-cargo-item-value">
                                                    ' . $item->CargoCarCount . ' </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex">
                                <a data-id="' . $item->CargoId . '" href="javascript:void(0);"
                                   class="mj-d-cargo-item-link mj-btn mj-btn-primary">
                                    ' . $lang['d_cargo_show'] . '
                                </a> 
                                <a href="javascript:void(0);"
                                   class="mj-d-cargo-item-link2 mj-btn mj-btn-primary">
                                    ' . $lang['d_cargo_owner_call'] . '
                                </a>
                                </div>
                                
                            </div>
                        </div>
                            ';

                }
                print_r($html);
            } else {
                echo parameterRequired();
            }


            break;

        case 'load-cargo-with-id':
            $cargo_id = (isset($json->cargo_id)) ? $antiXSS->xss_clean($json->cargo_id) : null;
            if (!empty($cargo_id)) {
                $records = Driver::getCargoWithId($cargo_id);

                $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
                $html = '';
                foreach ($records->response as $item) {
                    $categoryIcon = Utils::fileExist($item->CategoryIcon, BOX_EMPTY);
                    $price = number_format($item->CargoRecomendedPrice);
                    $startTransportation = ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('Y/m/d', $item->CargoStartTransportation) : date('Y-m-d', $item->CargoStartTransportation);


                    $flagGreen = ($item->CargoGreen == "yes") ? '<div class="mj-green-road-blob"></div>' : null;
                    $flagGreenPS = ($item->CargoGreen == "yes") ? "ps-2" : "";

                    $flagPrice = ($item->CargoRecomendedPrice == 0) ? $lang['u_agreement'] : number_format($item->CargoRecomendedPrice) . '<small>' . $item->CargoMonetaryUnit . '</small>';
                    $type_car_output = '';

                    $slugname = 'CargoName_' . $language;
                    if ($item->TypeId == 18) {
                        $type_car_output = '<div class="mj-d-cargo-car-type-badge d-flex">
                            <div class="mj-ambulnace-light me-2">
                                <div id="light-lamp">
                                    <div id="lamp-spinner"></div>
                                </div>
    
                                <div id="light-lamp-bottom"></div>
                            </div>
                            <span> ' . $item->TypeName . '</span>
                        </div>';
                    } else {
                        $type_car_output = '<div class="mj-d-cargo-car-type-badge d-flex">
                             <img src="' . Utils::fileExist($item->TypeIcon, BOX_EMPTY) . '" alt="">
                            <span> ' . $item->TypeName . '</span>
                        </div>';
                    }
                    $html = $html . '<div class="mj-d-cargo-card">
                            <div class="mj-d-cargo-card-badge">
                                ' . $item->CargoId . '
                            </div>
     
                                ' . $type_car_output . ' 
                            <div class="card-body">
                                <div class="d-flex align-items-center mt-2 mb-2">
                                    <div class="mj-d-cargo-item-category me-2"
                                         style="background: ' . $item->CategoryColor . '">
                                        <img src="' . Utils::fileExist($item->CategoryIcon, BOX_EMPTY) . '" alt="' . $item->CategoryName . '">
                                        <span>' . $item->CategoryName . '</span>
                                    </div>
                                    <div class="flex-fill">
                                        <h2 class="mj-d-cargo-item-header mt-0 mb-2">' . $item->$slugname . '</h2>
                                        <div class="mj-d-cargo-item-price-box d-flex align-items-center justify-content-between">
                                            <span>' . $lang['d_cargo_price'] . ':</span>
                                            <span>
                                            ' . $flagPrice . '
                                         </span>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="row">
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="position-relative">
                                                <img src="/dist/images/icons/arrow-up-left-from-circle.svg"
                                                     class="mj-d-cargo-item-icon me-2" alt="origin"/>
                                                     
                                                     ' . $flagGreen . '
                                            </div>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['d_cargo_origin'] . ':</div>
                                                <div class="mj-d-cargo-item-value mj-ellipse ' . $flagGreenPS . '">' . $item->CargoOrigin . " - " . Driver::getCountryByCities($item->CargoOriginid)->response . '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-4">
                                            <img src="/dist/images/icons/calendar-star.svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="loading-time"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['d_cargo_loading_time'] . ':
                                                </div>
                                                <div class="mj-d-cargo-item-value">' . $startTransportation . '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="/dist/images/icons/arrow-down-left-from-circle.svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="destination"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['d_cargo_destination'] . ':
                                                </div>
                                                <div class="mj-d-cargo-item-value mj-ellipse">' . $item->CargoDestination . " - " . Driver::getCountryByCities($item->CargoDestinationid)->response . '</div></div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="/dist/images/icons/truck-container(blue).svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="weight"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['b_cargo_car_needed'] . ':</div>
                                                <div class="mj-d-cargo-item-value">
                                                    ' . $item->CargoCarCount . ' </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex">
                                <a data-id="' . $item->CargoId . '" href="javascript:void(0);"
                                   class="mj-d-cargo-item-link mj-btn mj-btn-primary">
                                    ' . $lang['d_cargo_show'] . '
                                </a> 
                                <a href="javascript:viod(0);"
                                   class="mj-d-cargo-item-link2 mj-btn mj-btn-primary">
                                    ' . $lang['d_cargo_owner_call'] . '
                                </a>
                                </div>
                                
                            </div>
                        </div>
                            ';

                }
                echo $html;
            } else {
                echo parameterRequired();
            }


            break;

        case 'load-cargo-in-with-id':
            $cargo_id = (isset($json->cargo_id)) ? $antiXSS->xss_clean($json->cargo_id) : null;
            if (!empty($cargo_id)) {
                $records = Driver::getCargoInWithId($cargo_id);

                $language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
                $html = '';
                foreach ($records->response as $item) {
                    $categoryIcon = Utils::fileExist($item->CategoryIcon, BOX_EMPTY);
                    $price = number_format($item->CargoRecomendedPrice);
                    $startTransportation = ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('Y/m/d', $item->CargoStartTransportation) : date('Y-m-d', $item->CargoStartTransportation);


                    $flagPrice = ($item->CargoRecomendedPrice == 0) ? $lang['u_agreement'] : number_format($item->CargoRecomendedPrice) . '<small>' . $item->CargoMonetaryUnit . '</small>';
                    $type_car_output = '';

                    $slugname = 'CargoName_' . $language;
                    if ($item->TypeId == 18) {
                        $type_car_output = '<div class="mj-d-cargo-car-type-badge d-flex">
                            <div class="mj-ambulnace-light me-2">
                                <div id="light-lamp">
                                    <div id="lamp-spinner"></div>
                                </div>
    
                                <div id="light-lamp-bottom"></div>
                            </div>
                            <span> ' . $item->TypeName . '</span>
                        </div>';
                    } else {
                        $type_car_output = '<div class="mj-d-cargo-car-type-badge d-flex">
                             <img src="' . Utils::fileExist($item->TypeIcon, BOX_EMPTY) . '" alt="">
                            <span> ' . $item->TypeName . '</span>
                        </div>';
                    }
                    $html = $html . '<div class="mj-d-cargo-card">
                            <div class="mj-d-cargo-card-badge">
                                ' . $item->CargoId . '
                            </div>
     
                                ' . $type_car_output . ' 
                            <div class="card-body">
                                <div class="d-flex align-items-center mt-2 mb-2">
                                    <div class="mj-d-cargo-item-category me-2"
                                         style="background: ' . $item->CategoryColor . '">
                                        <img src="' . Utils::fileExist($item->CategoryIcon, BOX_EMPTY) . '" alt="' . $item->CategoryName . '">
                                        <span>' . $item->CategoryName . '</span>
                                    </div>
                                    <div class="flex-fill">
                                        <h2 class="mj-d-cargo-item-header mt-0 mb-2">' . $item->$slugname . '</h2>
                                        <div class="mj-d-cargo-item-price-box d-flex align-items-center justify-content-between">
                                            <span>' . $lang['d_cargo_price'] . ':</span>
                                            <span>
                                            ' . $flagPrice . '
                                         </span>
                                        </div>
                                    </div>
                                </div>
    
                                <div class="row">
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="position-relative">
                                                <img src="/dist/images/icons/arrow-up-left-from-circle.svg"
                                                     class="mj-d-cargo-item-icon me-2" alt="origin"/>
                                                     
                                                   
                                            </div>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['d_cargo_origin'] . ':</div>
                                                <div class="mj-d-cargo-item-value mj-ellipse  ">' . $item->CargoOrigin . " - " . Driver::getCountryByCities($item->CargoOriginid)->response . '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-4">
                                            <img src="/dist/images/icons/calendar-star.svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="loading-time"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['d_cargo_loading_time'] . ':
                                                </div>
                                                <div class="mj-d-cargo-item-value">' . $startTransportation . '</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="/dist/images/icons/arrow-down-left-from-circle.svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="destination"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['d_cargo_destination'] . ':
                                                </div>
                                                <div class="mj-d-cargo-item-value mj-ellipse">' . $item->CargoDestination . " - " . Driver::getCountryByCities($item->CargoDestinationid)->response . '</div></div>
                                        </div>
                                    </div>
                                    <div class="cargo-detail col-6">
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="/dist/images/icons/truck-container(blue).svg"
                                                 class="mj-d-cargo-item-icon me-2" alt="weight"/>
                                            <div>
                                                <div class="mj-d-cargo-item-title">' . $lang['b_cargo_car_needed'] . ':</div>
                                                <div class="mj-d-cargo-item-value">
                                                    ' . $item->CargoCarCount . ' </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex">
                                <a data-id="' . $item->CargoId . '" href="javascript:void(0);"
                                   class="mj-d-cargo-item-link mj-btn mj-btn-primary">
                                    ' . $lang['d_cargo_show'] . '
                                </a> 
                                <a href="javascript:viod(0);"
                                   class="mj-d-cargo-item-link2 mj-btn mj-btn-primary">
                                    ' . $lang['d_cargo_owner_call'] . '
                                </a>
                                </div>
                                
                            </div>
                        </div>
                            ';

                }
                echo $html;
            } else {
                echo parameterRequired();
            }


            break;
        case 'cancel-request':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $requestId = (isset($json->request)) ? $antiXSS->xss_clean($json->request) : null;
                $reason = (isset($json->reason)) ? $antiXSS->xss_clean($json->reason) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($requestId) && !empty($reason) && !empty($token)) {
                    echo json_encode(Driver::cancelRequest($userId, $requestId, $reason, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'cancel-request-in':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $requestId = (isset($json->request)) ? $antiXSS->xss_clean($json->request) : null;
                $reason = (isset($json->reason)) ? $antiXSS->xss_clean($json->reason) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($requestId) && !empty($reason) && !empty($token)) {
                    echo json_encode(Driver::cancelRequestIn($userId, $requestId, $reason, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'update-location':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $latitude = (isset($json->lat)) ? $antiXSS->xss_clean($json->lat) : null;
                $longitude = (isset($json->long)) ? $antiXSS->xss_clean($json->long) : null;

                if (!empty($userId) && !empty($latitude) && !empty($longitude)) {
                    Driver::updateLocation($userId, $latitude, $longitude);
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        /**
         * End Driver
         */


        /**
         * Start Site
         */

        case 'load_more_blog':
            $count = (int)$antiXSS->xss_clean($json->count);

            $result = Post::getPostByLimit($count, 6, $_COOKIE['language']);
            // print_r($result);
            if ($result->status == 200) {

                $arrayTemp = [];
                $flag = 0;
                if (!empty($result->response)) {
                    $flag = count($result->response);
                    foreach ($result->response as $index => $temp) {
                        if ($index >= 5) {
                            break;
                        }
                        $array = [];
                        $array['slug'] = $temp->post_slug;
                        $array['alt'] = strip_tags(mb_strimwidth($temp->post_title, 0, 18, "..."));
                        $array['image'] = Utils::fileExist($temp->post_thumbnail, BOX_EMPTY);
                        $array['title'] = strip_tags(mb_strimwidth($temp->post_title, 0, 45, "..."));
                        //                        $array['category'] = $temp->category_name;
                        $array['submitTime'] = Utils::getTimeCountry('Y / m / d', $temp->post_submit_time);
                        //                        $array['excerpt'] = (empty($temp->post_excerpt)) ? strip_tags(mb_strimwidth($temp->post_description, 0, 600, "...")) : $temp->post_excerpt;
                        array_push($arrayTemp, $array);
                    }
                }

                echo json_encode(['status' => 200, 'data' => $arrayTemp, 'count' => $flag]);
            } else {
                echo json_encode(['status' => -1, 'count' => 0]);
            }


            break;
        /**
         * End Site
         */

        case 'change-user-type':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }
            if ($isValidRequest) {
                $type = (isset($json->type)) ? $antiXSS->xss_clean($json->type) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($userId) && !empty($type) && !empty($token)) {


                    echo json_encode(User::updateUserType($userId, $type, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'get-all-list-transactions':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }
            if ($isValidRequest) {
                $status = (isset($json->status)) ? $antiXSS->xss_clean($json->status) : 'all';
                $search = (isset($json->search)) ? $antiXSS->xss_clean($json->search) : null;
                $currency = (isset($json->currency)) ? (int)$antiXSS->xss_clean($json->currency) : 1;
                echo json_encode(Transactions::getTransactionsList2($userId, $currency, $status, $search));
            }
            break;

        case 'change-driver-cv-status':
            $cv_id = (isset($json->cv_id)) ? $antiXSS->xss_clean($json->cv_id) : null;
            $role_status = (isset($json->role_status)) ? $antiXSS->xss_clean($json->role_status) : null;
            $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;


            $list_status = [
                "yes",
                "no",
            ];
            if (in_array($role_status, $list_status) && strlen($cv_id) > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    if ($role_status == 'yes') {
                        $role_status = 'no';
                    } else {
                        $role_status = 'yes';
                    }
                    $result = CV::updateCvRoleStatus($cv_id, $role_status);
                    if ($result->status == 200) {
                        echo "successful";
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo 'empty';
            }

            break;

        case
        'logout-user':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }
            if ($isValidRequest) {
                try {
                    setcookie('user-login', null, -1, '/');
                    unset($_COOKIE['user-login']);
                    setcookie('user-type', null, -1, '/');
                    unset($_COOKIE['user-type']);
                    setcookie('install', null, -1, '/');
                    unset($_COOKIE['install']);
                    setcookie('language-flag', null, -1, '/');
                    unset($_COOKIE['language-flag']);

                    setcookie('t-b-dashboard', null, -1, '/');
                    unset($_COOKIE['t-b-dashboard']);
                    setcookie('t-d-dashboard', null, -1, '/');
                    unset($_COOKIE['t-d-dashboard']);
                    setcookie('t-home', null, -1, '/');
                    unset($_COOKIE['t-home']);
                    setcookie('t-poster-dashboard', null, -1, '/');
                    unset($_COOKIE['t-poster-dashboard']);

                    echo json_encode(['status' => 200, 'message' => "success"]);

                } catch (Exception $exception) {
                    echo json_encode(['status' => 0, 'message' => "error"]);
                }

            } else {
                echo permissionAccess();
            }
            break;


        /**
         * Start Poster
         */
        case 'get-brands-poster':
            $type = (isset($json->type) && in_array($json->type, ['truck', 'trailer'])) ? $antiXSS->xss_clean($json->type) : 'truck';
            echo json_encode(PosterC::getBrandsPoster($type));
            break;

        case 'get-models-poster':
            echo json_encode(PosterC::getModelsPoster());
            break;

        case 'get-property-poster':
            $type = (isset($json->type) && in_array($json->type, ['truck', 'trailer'])) ? $antiXSS->xss_clean($json->type) : 'truck';
            echo json_encode(PosterC::getAllPropertyFromUser($type));
            break;
        case 'get-property-poster-search':
            $type = (isset($json->type) && in_array($json->type, ['truck', 'trailer'])) ? $antiXSS->xss_clean($json->type) : 'truck';
            $search_value = (isset($json->search_value)) ? $antiXSS->xss_clean($json->search_value) : null;
            echo json_encode(PosterC::getSearchPropertyItem($type, $search_value));
            break;
        case 'get-brand-poster':
            $type = (isset($json->type) && in_array($json->type, ['truck', 'trailer'])) ? $antiXSS->xss_clean($json->type) : 'truck';
            echo json_encode(PosterC::getAllBrandsbyType($type));
            break;
        case 'get-brand-poster-search':
            $type = (isset($json->type) && in_array($json->type, ['truck', 'trailer'])) ? $antiXSS->xss_clean($json->type) : 'truck';
            $search_value = (isset($json->search_value)) ? $antiXSS->xss_clean($json->search_value) : null;
            echo json_encode(PosterC::getSearchBrandItem($type, $search_value));
            break;

        case 'get-cities-by-country':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $countryId = (isset($json->country)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->country)) : null;
                $type = (isset($json->type)) ? $antiXSS->xss_clean($json->type) : null;
                if (!empty($countryId) && !empty($type)) {
                    echo json_encode(Location::getCitiesListByStatus($countryId, $type));
                } else {
                    echo json_encode(['status' => -2, 'response' => []]);
                }
            } else {
                echo json_encode(['status' => -3, 'response' => []]);
            }
            break;

        case 'submit-poster':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $type = (isset($json->type) && in_array($json->type, ['truck', 'trailer'])) ? $antiXSS->xss_clean($json->type) : null;
                $ads_title = $json->ads_title ? $antiXSS->xss_clean($json->ads_title) : null;

                $properties = (isset($json->properties)) ? $antiXSS->xss_clean($json->properties) : null;
                $country = (isset($json->country)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->country)) : null;
                $city = (isset($json->city)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->city)) : null;
                $mobile = (isset($json->mobile)) ? $antiXSS->xss_clean($json->mobile) : null;
                $phone = (isset($json->phone)) ? $antiXSS->xss_clean($json->phone) : null;
                $clockFrom = (isset($json->clockFrom)) ? $antiXSS->xss_clean($json->clockFrom) : null;
                $clockTo = (isset($json->clockTo)) ? $antiXSS->xss_clean($json->clockTo) : null;
                $description = (isset($json->description)) ? $antiXSS->xss_clean($json->description) : null;
                $images = (isset($json->images)) ? $json->images : [];
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if ($type == "truck") {
                    $price = (isset($json->priceTruck)) ? $antiXSS->xss_clean($json->priceTruck) : null;
                    $currency = (isset($json->currencyTruck)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->currencyTruck)) : null;
                    $cash = (isset($json->cashTruck) && $json->cashTruck && $json->cashTruck == 1) ? 'yes' : 'no';
                    $leasing = (isset($json->leasingTruck) && $json->leasingTruck && $json->leasingTruck == 1) ? 'yes' : 'no';
                    $installment = (isset($json->installmentTruck) && $json->installmentTruck && $json->installmentTruck == 1) ? 'yes' : 'no';

                    $modelTrailer = null;
                    $axisTrailer = null;

                    $status = (isset($json->status) && in_array($json->status, ['new', 'stock', 'order'])) ? $antiXSS->xss_clean($json->status) : null;
                    $brandTruck = (isset($json->brandTruck)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->brandTruck)) : null;
                    $brandTextTruck = (isset($json->brandTextTruck)) ? $antiXSS->xss_clean($json->brandTextTruck) : null;
                    $modelTruck = (isset($json->modelTruck)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->modelTruck)) : null;
                    $modelTextTruck = (isset($json->modelTextTruck)) ? $antiXSS->xss_clean($json->modelTextTruck) : null;
                    $gearboxTruck = (isset($json->gearboxTruck)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->gearboxTruck)) : null;
                    $fuelTruck = (isset($json->fuelTruck)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->fuelTruck)) : null;
                    $colorOutTruck = (isset($json->colorOutTruck)) ? $antiXSS->xss_clean($json->colorOutTruck) : '#ffffff';
                    $builtTruck = (isset($json->builtTruck)) ? $antiXSS->xss_clean($json->builtTruck) : null;
                    $runTruck = (isset($json->runTruck)) ? $antiXSS->xss_clean($json->runTruck) : null;

                } else {
                    $price = (isset($json->priceTrailer)) ? $antiXSS->xss_clean($json->priceTrailer) : null;
                    $currency = (isset($json->currencyTrailer)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->currencyTrailer)) : null;
                    $cash = (isset($json->cashTrailer) && $json->cashTrailer && $json->cashTrailer == 1) ? 'yes' : 'no';
                    $leasing = (isset($json->leasingTrailer) && $json->leasingTrailer && $json->leasingTrailer == 1) ? 'yes' : 'no';
                    $installment = (isset($json->installmentTrailer) && $json->installmentTrailer && $json->installmentTrailer == 1) ? 'yes' : 'no';

                    $modelTrailer = (isset($json->modelTrailer)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->modelTrailer)) : null;
                    $axisTrailer = (isset($json->axisTrailer)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->axisTrailer)) : null;


                    $status = null;
                    //                    $brandTruck = null;
                    //                    $brandTextTruck = null;
                    //                    $modelTruck = null;
                    //                    $modelTextTruck = null;
                    $brandTruck = (isset($json->brandTrailer)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->brandTrailer)) : null;
                    $brandTextTruck = (isset($json->brandTextTrailer)) ? $antiXSS->xss_clean($json->brandTextTrailer) : null;
                    $modelTruck = (isset($json->modelBTrailer)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->modelBTrailer)) : null;
                    $modelTextTruck = (isset($json->modelTextTrailer)) ? $antiXSS->xss_clean($json->modelTextTrailer) : null;


                    $gearboxTruck = null;
                    $fuelTruck = null;
                    $colorOutTruck = null;
                    $builtTruck = null;
                    $runTruck = null;
                }


                //                echo json_encode(Poster::insertPoster($token,$userId, $city, $currency,$mobile, $clockFrom,$clockTo, $type,
                //                    $cash,$leasing,$installment,
                //                    $price,
                //                    $status, $brandTruck, $brandTextTruck, $modelTruck, $modelTextTruck, $gearboxTruck, $fuelTruck,
                //                    $colorOutTruck,$builtTruck, $runTruck,
                //                    $axisTrailer,$modelTrailer,$imageFiles,$desc,$phone));

                // Start truck

                if (!empty($type) && $type == "truck" && !empty($userId)
                    && !empty($status) && strlen($brandTruck) > 0 && !empty($brandTextTruck) && strlen($modelTruck) > 0
                    && !empty($modelTextTruck) && !empty($gearboxTruck) && !empty($fuelTruck) && !empty($builtTruck) &&
                    (($status == "new" && $runTruck == null) || ($status != "new" && strlen($runTruck) > 0))
                    && !empty($city) && !empty($mobile) && !empty($clockFrom) && !empty($clockTo) && !empty($currency) && !empty($token)) {

                    $imageFiles = [];
                    foreach ($images as $index => $image) {
                        if ($index == 10) {
                            break;
                        }
                        $upload = Upload::uploadBase64($image, POSTER_ADDRESS);
                        if ($upload->status == 200) {
                            $imageFiles[] = $upload->response;
                        }
                    }

                    $response_insert = Poster::insertPoster($token, $ads_title, $userId, $city, $currency, $mobile, $clockFrom, $clockTo, $type,
                        $cash, $leasing, $installment,
                        $price,
                        $status, $brandTruck, $brandTextTruck, $modelTruck, $modelTextTruck, $gearboxTruck, $fuelTruck,
                        $colorOutTruck, $builtTruck, $runTruck,
                        null, null, $imageFiles, $description, $phone, $properties, null);

                    echo json_encode($response_insert);
                    if ($response_insert->status == 200) {
                        User::createUserLog($userId, 'uLog_submit_new_poster_' . $type, 'poster');
                    }

                } // Start trailer
                elseif (!empty($type) && $type == "trailer" && !empty($userId) && isset($modelTrailer) && !empty($axisTrailer)
                    && strlen($brandTruck) > 0 && !empty($brandTextTruck) && strlen($modelTruck) > 0
                    && !empty($modelTextTruck)
                    && !empty($city) && !empty($mobile) &&
                    !empty($clockFrom) && !empty($clockTo) && !empty($currency) && !empty($token)) {

                    $imageFiles = [];
                    foreach ($images as $index => $image) {
                        if ($index == 10) {
                            break;
                        }
                        $upload = Upload::uploadBase64($image, POSTER_ADDRESS);
                        if ($upload->status == 200) {
                            $imageFiles[] = $upload->response;
                        }
                    }


                    $response_insert = Poster::insertPoster($token, $ads_title, $userId, $city, $currency, $mobile, $clockFrom, $clockTo, 'trailer',
                        $cash, $leasing, $installment,
                        $price,
                        null, $brandTruck, $brandTextTruck, $modelTruck, $modelTextTruck, null, null,
                        null, null, null,
                        $axisTrailer, $modelTrailer, $imageFiles, $description, $phone, $properties, null);
                    echo json_encode($response_insert);
                    if ($response_insert->status == 200) {
                        User::createUserLog($userId, 'uLog_submit_new_poster_' . $type, 'poster');
                    }

                } else {
                    echo permissionAccess();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'update-poster':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $type = (isset($json->type) && in_array($json->type, ['truck', 'trailer'])) ? $antiXSS->xss_clean($json->type) : null;
                $posterId = (isset($json->id)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->id)) : null;
                $ads_title = $json->ads_title ? $antiXSS->xss_clean($json->ads_title) : null;
                $properties = (isset($json->properties)) ? $antiXSS->xss_clean($json->properties) : null;
                $country = (isset($json->country)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->country)) : null;
                $city = (isset($json->city)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->city)) : null;
                $mobile = (isset($json->mobile)) ? $antiXSS->xss_clean($json->mobile) : null;
                $phone = (isset($json->phone)) ? $antiXSS->xss_clean($json->phone) : null;
                $clockFrom = (isset($json->clockFrom)) ? $antiXSS->xss_clean($json->clockFrom) : null;
                $clockTo = (isset($json->clockTo)) ? $antiXSS->xss_clean($json->clockTo) : null;
                $description = (isset($json->description)) ? $antiXSS->xss_clean($json->description) : null;
                $images = (isset($json->images)) ? $json->images : [];
                $defaultImages = (isset($json->defaultImages)) ? $json->defaultImages : [];
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if ($type == "truck") {
                    $price = (isset($json->priceTruck)) ? $antiXSS->xss_clean($json->priceTruck) : null;
                    $currency = (isset($json->currencyTruck)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->currencyTruck)) : null;
                    $cash = (isset($json->cashTruck) && $json->cashTruck && $json->cashTruck == 1) ? 'yes' : 'no';
                    $leasing = (isset($json->leasingTruck) && $json->leasingTruck && $json->leasingTruck == 1) ? 'yes' : 'no';
                    $installment = (isset($json->installmentTruck) && $json->installmentTruck && $json->installmentTruck == 1) ? 'yes' : 'no';

                    $modelTrailer = null;
                    $axisTrailer = null;

                    $status = (isset($json->status) && in_array($json->status, ['new', 'stock', 'order'])) ? $antiXSS->xss_clean($json->status) : null;
                    $brandTruck = (isset($json->brandTruck)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->brandTruck)) : null;
                    $brandTextTruck = (isset($json->brandTextTruck)) ? $antiXSS->xss_clean($json->brandTextTruck) : null;
                    $modelTruck = (isset($json->modelTruck)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->modelTruck)) : null;
                    $modelTextTruck = (isset($json->modelTextTruck)) ? $antiXSS->xss_clean($json->modelTextTruck) : null;
                    $gearboxTruck = (isset($json->gearboxTruck)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->gearboxTruck)) : null;
                    $fuelTruck = (isset($json->fuelTruck)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->fuelTruck)) : null;
                    $colorOutTruck = (isset($json->colorOutTruck)) ? $antiXSS->xss_clean($json->colorOutTruck) : '#ffffff';
                    $builtTruck = (isset($json->builtTruck)) ? $antiXSS->xss_clean($json->builtTruck) : null;
                    $runTruck = (isset($json->runTruck)) ? $antiXSS->xss_clean($json->runTruck) : null;

                    if ($status == "new") {
                        $runTruck = null;
                    }
                } else {
                    $price = (isset($json->priceTrailer)) ? $antiXSS->xss_clean($json->priceTrailer) : null;
                    $currency = (isset($json->currencyTrailer)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->currencyTrailer)) : null;
                    $cash = (isset($json->cashTrailer) && $json->cashTrailer && $json->cashTrailer == 1) ? 'yes' : 'no';
                    $leasing = (isset($json->leasingTrailer) && $json->leasingTrailer && $json->leasingTrailer == 1) ? 'yes' : 'no';
                    $installment = (isset($json->installmentTrailer) && $json->installmentTrailer && $json->installmentTrailer == 1) ? 'yes' : 'no';

                    $modelTrailer = (isset($json->modelTrailer)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->modelTrailer)) : null;
                    $axisTrailer = (isset($json->axisTrailer)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->axisTrailer)) : null;


                    $status = null;
                    //                    $brandTruck = null;
                    //                    $brandTextTruck = null;
                    //                    $modelTruck = null;
                    //                    $modelTextTruck = null;
                    $brandTruck = (isset($json->brandTrailer)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->brandTrailer)) : null;
                    $brandTextTruck = (isset($json->brandTextTrailer)) ? $antiXSS->xss_clean($json->brandTextTrailer) : null;
                    $modelTruck = (isset($json->modelBTrailer)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->modelBTrailer)) : null;
                    $modelTextTruck = (isset($json->modelTextTrailer)) ? $antiXSS->xss_clean($json->modelTextTrailer) : null;


                    $gearboxTruck = null;
                    $fuelTruck = null;
                    $colorOutTruck = null;
                    $builtTruck = null;
                    $runTruck = null;
                }


                // Start truck

                if (!empty($type) && $type == "truck" && !empty($userId) && !empty($posterId)
                    && !empty($status) && strlen($brandTruck) > 0 && !empty($brandTextTruck) && strlen($modelTruck) > 0
                    && !empty($modelTextTruck) && !empty($gearboxTruck) && !empty($fuelTruck) && !empty($builtTruck) &&
                    (($status == "new" && $runTruck == null) || ($status != "new" && strlen($runTruck) > 0))
                    && !empty($city) && !empty($mobile) && !empty($clockFrom) && !empty($clockTo) && !empty($currency) && !empty($token)) {


                    //                    $imageFiles = $defaultImages;
                    //                    foreach ($images as $image) {
                    //                        $upload = Upload::uploadBase64($image, POSTER_ADDRESS);
                    //                        if ($upload->status == 200) {
                    //                            $imageFiles[] = $upload->response;
                    //                        }
                    //                    }


                    $imageFiles = $defaultImages;
                    if (count($imageFiles) < 10) {
                        if (!empty($images)) {
                            foreach ($images as $image) {
                                $upload = Upload::uploadBase64($image, POSTER_ADDRESS);
                                if ($upload->status == 200) {
                                    $imageFiles[] = $upload->response;
                                    if (count($imageFiles) > 10) {
                                        goto the_end_if_1;
                                    }
                                }
                            }
                        }
                    }
                    the_end_if_1:


                    echo json_encode(Poster::editPoster($token, $ads_title, $posterId, $userId, $city, $currency, $mobile, $clockFrom, $clockTo, $type,
                        $cash, $leasing, $installment,
                        $price,
                        $status, $brandTruck, $brandTextTruck, $modelTruck, $modelTextTruck, $gearboxTruck, $fuelTruck,
                        $colorOutTruck, $builtTruck, $runTruck,
                        null, null, $imageFiles, $description, $phone, $properties));

                } // Start trailer
                elseif (!empty($type) && $type == "trailer" && !empty($userId) && !empty($posterId)
                    && strlen($brandTruck) > 0 && !empty($brandTextTruck) && strlen($modelTruck) > 0
                    && !empty($modelTextTruck)
                    && isset($modelTrailer) && !empty($axisTrailer) && !empty($city) && !empty($mobile) &&
                    !empty($clockFrom) && !empty($clockTo) && !empty($currency) && !empty($token)) {

                    //                    $imageFiles = $defaultImages;
                    //                    foreach ($images as $image) {
                    //                        $upload = Upload::uploadBase64($image, POSTER_ADDRESS);
                    //                        if ($upload->status == 200) {
                    //                            $imageFiles[] = $upload->response;
                    //                        }
                    //                    }

                    $imageFiles = $defaultImages;
                    if (count($imageFiles) < 10) {
                        if (!empty($images)) {
                            foreach ($images as $image) {
                                $upload = Upload::uploadBase64($image, POSTER_ADDRESS);
                                if ($upload->status == 200) {
                                    $imageFiles[] = $upload->response;
                                    if (count($imageFiles) > 10) {
                                        goto the_end_if_2;
                                    }
                                }
                            }
                        }
                    }
                    the_end_if_2:


                    echo json_encode(Poster::editPoster($token, $ads_title, $posterId, $userId, $city, $currency, $mobile, $clockFrom, $clockTo, 'trailer',
                        $cash, $leasing, $installment,
                        $price,
                        null, $brandTruck, $brandTextTruck, $modelTruck, $modelTextTruck, null, null,
                        null, null, null,
                        $axisTrailer, $modelTrailer, $imageFiles, $description, $phone, $properties));

                } else {
                    echo permissionAccess();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'poster-report':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $posterId = (isset($json->posterId)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->posterId)) : null;
                $catId = (isset($json->catId)) ? (int)$json->catId : 0;
                $description = (isset($json->description)) ? $antiXSS->xss_clean($json->description) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($posterId) && (($catId == 0 && !empty($description)) || $catId > 0) && !empty($token) && Security::verifyCSRF('poster-detail', $token, false)) {
                    echo json_encode(Poster::submitReport($userId, $posterId, $catId, $description));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'poster-delete':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $posterId = (isset($json->posterId)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->posterId)) : null;
                $catId = (isset($json->catId)) ? (int)$json->catId : 0;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;

                if (!empty($posterId) && $catId > 0 && !empty($token) && Security::verifyCSRF('poster-detail', $token)) {
                    echo json_encode(Poster::userDeletePoster($userId, $posterId, $catId));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'poster-request-expert':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $posterId = (isset($json->posterId)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->posterId)) : null;
                $type = (isset($json->type)) ? $antiXSS->xss_clean($json->type) : null;
                $address = (isset($json->address)) ? $antiXSS->xss_clean($json->address) : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
                $currencyId = 1;

                if (!empty($userId) && !empty($posterId) && !empty($address) && !empty($token)) {
                    echo json_encode(Poster::setExpertPosterFromUser($userId, $posterId, $currencyId, $type, $address, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'poster-upgrade':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $posterId = (isset($json->posterId)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->posterId)) : null;
                $type = (isset($json->type) && in_array($json->type, ['wallet', 'online'])) ? $json->type : null;
                $kind = (isset($json->kind) && in_array($json->kind, ['ladder', 'quick'])) ? $json->kind : null;
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;
                $currencyId = 1;

                if (!empty($userId) && !empty($posterId) && !empty($type) && !empty($kind) && !empty($token)) {
                    echo json_encode(Poster::setUpgradePosterFromUser($userId, $posterId, $kind, $type, $currencyId, $token));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;

        case 'get-poster-filters':


            //            $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
            $brands = (isset($json->brands)) ? $antiXSS->xss_clean($json->brands) : null;
            $city = (isset($json->city)) ? $antiXSS->xss_clean($json->city) : null;
            $country = (isset($json->country)) ? $antiXSS->xss_clean($json->country) : null;
            $from_year = (isset($json->from_year)) ? $antiXSS->xss_clean($json->from_year) : 1375;
            $to_year = (isset($json->to_year)) ? $antiXSS->xss_clean($json->to_year) : 1402;
            $fuels = (isset($json->fuels)) ? $antiXSS->xss_clean($json->fuels) : null;
            $gear_boxes = (isset($json->gear_boxes)) ? $antiXSS->xss_clean($json->gear_boxes) : null;
            $installments = (isset($json->installments) && $json->installments) ? 'yes' : 'no';
            $leasing = (isset($json->leasing) && $json->leasing) ? 'yes' : 'no';
            $max_price = (isset($json->max_price)) ? $antiXSS->xss_clean($json->max_price) : 0;
            $min_price = (isset($json->min_price)) ? $antiXSS->xss_clean($json->min_price) : 0;
            $poster_category = (isset($json->poster_category)) ? $antiXSS->xss_clean($json->poster_category) : null;
            $properties = (isset($json->properties)) ? $antiXSS->xss_clean($json->properties) : null;
            $trailer_types = (isset($json->trailer_types)) ? $antiXSS->xss_clean($json->trailer_types) : null;
            $worked_km_from = (isset($json->worked_km_from)) ? $antiXSS->xss_clean($json->worked_km_from) : 0;
            $worked_km_to = (isset($json->worked_km_to)) ? $antiXSS->xss_clean($json->worked_km_to) : 0;

            $cash = (isset($json->cash) && $json->cash) ? 'yes' : 'yes';
            $currencyId = 1;
            $page = $json->page;

            if (!empty($to_year) && !empty($installments) && !empty($leasing) &&
                !empty($max_price) && !empty($poster_category) && !empty($worked_km_to)) {

                echo json_encode(Poster::getFilter($brands, $city, $country, $from_year, $to_year, $fuels, $gear_boxes, $installments, $leasing, $max_price, $min_price, $poster_category, $properties, $trailer_types, $worked_km_from, $worked_km_to, $cash, $currencyId, $page));
            } else {
                echo parameterRequired();
            }

            break;

        /**
         * End Poster
         */

        /**
         * Start Ring
         */
        case 'submit-ring':
            if (isset($_COOKIE['user-login'])) {
                $user_id = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $source_country = isset($json->source_country) ? $antiXSS->xss_clean($json->source_country) : [];
                $dest_country = isset($json->dest_country) ? $antiXSS->xss_clean($json->dest_country) : [];
                $source_city = isset($json->source_city) ? $antiXSS->xss_clean($json->source_city) : [];
                $dest_city = isset($json->dest_city) ? $antiXSS->xss_clean($json->dest_city) : [];

                $car_type = isset($json->car_type) ? $antiXSS->xss_clean($json->car_type) : [];
                $status = isset($json->status) ? $antiXSS->xss_clean($json->status) : 'active';
                if (!empty($user_id)) {
                    $result = Ring::insertOrUpdateRing($user_id, $source_country, $source_city, $dest_country, $dest_city, $car_type, $status);
                    print_r(json_encode($result));
                }
            } else {
                print_r(json_encode(sendResponse(201, 'user-not-logged-in', [])));
            }

            break;
        /**
         * End Ring
         */
        case 'update-user-lang':
            $lang = (isset($json->lang)) ? $antiXSS->xss_clean($json->lang) : null;
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                User::updateUserLang($userId, $lang);
            }

            break;


        case 'inquiry-customs':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $nameCustoms = (isset($json->nameCustoms)) ? $antiXSS->xss_clean($json->nameCustoms) : null;
                $description = (isset($json->description)) ? $antiXSS->xss_clean($json->description) : null;
                $transportationId = (isset($json->transportation)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->transportation)) : null;
                $startDate = (isset($json->startDate)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->startDate)) : null;
                $commodity = (isset($json->commodity)) ? $antiXSS->xss_clean($json->commodity) : null;

                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;


                if (!empty($userId) && !empty($nameCustoms) && !empty($transportationId) && !empty($startDate) && !empty($token) &&
                    @json_decode($commodity) && count(json_decode($commodity, true)) > 0) {
                    echo json_encode(
                        Customs::insertInquiryCustoms($userId, $nameCustoms, $transportationId, $startDate, $commodity, $token, $description)
                    );
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;
        case 'inquiry-minicargo':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $description = (isset($json->description)) ? $antiXSS->xss_clean($json->description) : null;
                $startDate = (isset($json->startDate)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->startDate)) : null;
                $source_city_id = (isset($json->source_city_id)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->source_city_id)) : null;
                $dest_city_id = (isset($json->dest_city_id)) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($json->dest_city_id)) : null;
                $cargo_arrangement = (isset($json->cargo_arrangement)) ? $antiXSS->xss_clean($json->cargo_arrangement) : 'no';
                $commodity = (isset($json->commodity)) ? $antiXSS->xss_clean($json->commodity) : null;

                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;


                if (!empty($userId) && !empty($startDate) && !empty($source_city_id) && !empty($source_city_id) && !empty($dest_city_id) && !empty($token) &&
                    @json_decode($commodity) && count(json_decode($commodity, true)) > 0) {
                    echo json_encode(
                        MiniCargo::insertInquiryMinicargos($userId, $source_city_id, $dest_city_id, $cargo_arrangement, $startDate, $commodity, $token, $description)
                    );
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;
        case 'submit-ticket':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $departmentId = (isset($json->department)) ? $antiXSS->xss_clean($json->department) : null;
                $subject = (isset($json->ticket_title)) ? $antiXSS->xss_clean($json->ticket_title) : null;
                $message = (isset($json->ticket_desc)) ? $antiXSS->xss_clean($json->ticket_desc) : null;
                $selected_files = (isset($json->selected_files)) ? $antiXSS->xss_clean($json->selected_files) : [];
                $token = (isset($json->token)) ? $antiXSS->xss_clean($json->token) : null;


                $uploaded_files = [];
                if ($selected_files != []) {

                    foreach ($selected_files as $image) {
                        $upload = Upload::uploadBase64($image, DRIVER_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $uploaded_files[] = $upload->response;
                        }
                    }
                }

                if (!empty($userId) && !empty($departmentId) && !empty($subject) && !empty($message)) {
                    echo json_encode(Ticket::createTicket($userId, $departmentId, $subject, $message, $token, $uploaded_files));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;
        case 'change-notification-status':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $user_id = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $notification_id = $json->notification_id ? $antiXSS->xss_clean($json->notification_id) : null;
                if ($notification_id) {
                    echo json_encode(Notification::changeNotificationStatus($notification_id));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;
        case 'submit-exchange-request':
            $isValidRequest = false;
            if (User::userIsLoggedIn()) {
                $user_id = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                $isValidRequest = true;
            }

            if ($isValidRequest) {
                $request_type = $json->request_type ? $antiXSS->xss_clean($json->request_type) : null;
                $price_id = $json->price_id ? $antiXSS->xss_clean($json->price_id) : null;
                $price_buy = $json->price_buy ? $antiXSS->xss_clean($json->price_buy) : null;
                $price_sell = $json->price_sell ? $antiXSS->xss_clean($json->price_sell) : null;
                $request_side = $json->request_side ? $antiXSS->xss_clean($json->request_side) : null;
                $count = $json->count ? $antiXSS->xss_clean($json->count) : null;

                if ($request_type && $price_id && $request_side) {
                    echo json_encode(Exchange::insertExchangeRequest($user_id, $request_type, $price_id, $price_buy, $price_sell, $request_side, $count));
                } else {
                    echo parameterRequired();
                }
            } else {
                echo permissionAccess();
            }
            break;
        default:
            echo parameterRequired();
            break;
    }
} else {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'change-avatar':
                $isValidRequest = false;
                if (User::userIsLoggedIn()) {
                    $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                    $isValidRequest = true;
                }

                if ($isValidRequest) {
                    $avatar = (isset($_FILES['avatar'])) ? $_FILES['avatar'] : null;
                    $token = (isset($_POST['token'])) ? $antiXSS->xss_clean($_POST['token']) : null;

                    $attachmentFile = '/dist/images/icons/profile.svg';

                    $upload = Upload::upload($avatar, '/uploads/avatars');
                    if ($upload->status == 200) {
                        $attachmentFile = $upload->response;
                    }
                    //test
                    if (!empty($userId) && !empty($attachmentFile) && !empty($token)) {
                        echo json_encode(User::changeAvatar($userId, $attachmentFile, $token));
                    } else {
                        echo parameterRequired();
                    }
                } else {
                    echo permissionAccess();
                }
                break;

            case 'end-transportation':
                $isValidRequest = false;
                if (User::userIsLoggedIn()) {
                    $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                    $isValidRequest = true;
                }

                if ($isValidRequest) {
                    $cargoId = (isset($_POST['cargo'])) ? $antiXSS->xss_clean($_POST['cargo']) : null;
                    $requestId = (isset($_POST['request'])) ? $antiXSS->xss_clean($_POST['request']) : null;
                    $receipt = (isset($_POST['receipt'])) ? $antiXSS->xss_clean($_POST['receipt']) : null;
                    $images = (isset($_POST['images'])) ? json_decode($_POST['images']) : [];
                    $rate = (isset($_POST['rate'])) ? $antiXSS->xss_clean($_POST['rate']) : 0;
                    $token = (isset($_POST['token'])) ? $antiXSS->xss_clean($_POST['token']) : null;

                    $receiptFile = '';
                    $ImagesItem = [];
                    if (Driver::getExtraExpensesListCountByStatus($requestId)->response[0]->count > 0) {
                        echo json_encode([
                            'status' => 420,
                            'message' => "extra-expensive"
                        ]);
                    } else {
                        if (!empty($receipt)) {
                            $receiptUpload = Upload::uploadBase64($receipt, '/uploads/cargo-receipt');
                            if ($receiptUpload->status == 200) {
                                $receiptFile = $receiptUpload->response;
                            }
                        }

                        foreach ($images as $image) {
                            $upload = Upload::uploadBase64($image, '/uploads/transportation');
                            if ($upload->status == 200) {
                                $ImagesItem[] = $upload->response;
                            }
                        }

                        if (!empty($userId) && !empty($cargoId) && !empty($requestId) && !empty($token)) {
                            echo json_encode(Driver::endTransportation($userId, $requestId, $cargoId, $token, $receiptFile, $ImagesItem, $rate));
                        } else {
                            echo parameterRequired();
                        }
                    }


                } else {
                    echo permissionAccess();
                }
                break;

            case 'end-transportation-in':
                $isValidRequest = false;
                if (User::userIsLoggedIn()) {
                    $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                    $isValidRequest = true;
                }

                if ($isValidRequest) {
                    $cargoId = (isset($_POST['cargo'])) ? $antiXSS->xss_clean($_POST['cargo']) : null;
                    $requestId = (isset($_POST['request'])) ? $antiXSS->xss_clean($_POST['request']) : null;
                    $receipt = (isset($_POST['receipt'])) ? $antiXSS->xss_clean($_POST['receipt']) : null;
                    $images = (isset($_POST['images'])) ? json_decode($_POST['images']) : [];
                    $rate = (isset($_POST['rate'])) ? $antiXSS->xss_clean($_POST['rate']) : 0;
                    $token = (isset($_POST['token'])) ? $antiXSS->xss_clean($_POST['token']) : null;

                    $receiptFile = '';
                    $ImagesItem = [];
                    if (Driver::getExtraExpensesInListCountByStatus($requestId)->response[0]->count > 0) {
                        echo json_encode([
                            'status' => 420,
                            'message' => "extra-expensive"
                        ]);
                    } else {
                        if (!empty($receipt)) {
                            $receiptUpload = Upload::uploadBase64($receipt, '/uploads/cargo-in-receipt');
                            if ($receiptUpload->status == 200) {
                                $receiptFile = $receiptUpload->response;
                            }
                        }

                        foreach ($images as $image) {
                            $upload = Upload::uploadBase64($image, '/uploads/transportation-in');
                            if ($upload->status == 200) {
                                $ImagesItem[] = $upload->response;
                            }
                        }

                        if (!empty($userId) && !empty($cargoId) && !empty($requestId) && !empty($token)) {
                            echo json_encode(Driver::endTransportationIn($userId, $requestId, $cargoId, $token, $receiptFile, $ImagesItem, $rate));
                        } else {
                            echo parameterRequired();
                        }
                    }


                } else {
                    echo permissionAccess();
                }
                break;


            case 'send-message':
                $isValidRequest = false;
                if (User::userIsLoggedIn()) {
                    $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                    $isValidRequest = true;
                }

                if ($isValidRequest) {
                    $ticketId = (isset($_POST['ticket'])) ? $antiXSS->xss_clean($_POST['ticket']) : null;
                    $message = (isset($_POST['message'])) ? $_POST['message'] : null;
                    $attachments = (isset($_FILES)) ? $_FILES : [];
                    $token = (isset($_POST['token'])) ? $antiXSS->xss_clean($_POST['token']) : null;

                    $attachmentFiles = [];

                    foreach ($attachments as $file) {
                        $upload = Upload::upload($file, '/uploads/tickets');
                        if ($upload->status == 200) {
                            $attachmentFiles[] = ['attachment' => $upload->response];
                        }
                    }

                    if (!empty($userId) && !empty($ticketId) && !empty($message) && !empty($token)) {
                        echo json_encode(Ticket::sendTicketMessage($userId, $ticketId, $message, $token, $attachmentFiles, false));
                    } else {
                        echo parameterRequired();
                    }
                } else {
                    echo permissionAccess();
                }
                break;

            /*            case 'save-file-option':
                            $isValidRequest = false;
                            if (User::userIsLoggedIn()) {
                                $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                                $isValidRequest = true;
                            }

                            if ($isValidRequest) {
                                $fieldId = (isset($_POST['field'])) ? $antiXSS->xss_clean($_POST['field']) : null;
                                $value = (isset($_FILES['value'])) ? $_FILES['value'] : null;
                                $token = (isset($_POST['token'])) ? $antiXSS->xss_clean($_POST['token']) : null;

                                $attachmentFile = '';

                                $upload = Upload::upload($value, DRIVER_ADDRESS);
                                if ($upload->status == 200) {
                                    $attachmentFile = $upload->response;
                                }

                                if (!empty($userId) && !empty($fieldId) && !empty($attachmentFile) && !empty($token)) {
                                    echo json_encode(User::updateUserAuth($userId, $fieldId, $attachmentFile, $token));
                                } else {
                                    echo parameterRequired();
                                }
                            } else {
                                echo permissionAccess();
                            }
                            break;*/

            case 'deposit-with-receipt':
                $isValidRequest = false;
                if (User::userIsLoggedIn()) {
                    $userId = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
                    $isValidRequest = true;
                }

                if ($isValidRequest) {
                    $authority = (isset($_POST['authority'])) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($_POST['authority'])) : null;
                    $amount = (isset($_POST['amount'])) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($_POST['amount'])) : null;
                    $currency = (isset($_POST['currency'])) ? $antiXSS->xss_clean($_POST['currency']) : null;
                    $receipt = (isset($_FILES['receipt'])) ? $_FILES['receipt'] : null;
                    $token = (isset($_POST['token'])) ? $antiXSS->xss_clean($_POST['token']) : null;

                    $attachmentFile = '';

                    $upload = Upload::upload($receipt, DEPOSIT_ADDRESS);
                    if ($upload->status == 200) {
                        $attachmentFile = $upload->response;
                    }

                    if (!empty($userId) && !empty($amount) && !empty($authority) && !empty($currency) && !empty($receipt) && !empty($token)) {
                        echo json_encode(Transactions::depositWithReceipt($userId, $authority, $amount, $currency, $attachmentFile, $token));
                    } else {
                        echo parameterRequired();
                    }
                } else {
                    echo permissionAccess();
                }
                break;


            case 'submit-employ':
                $authority = (isset($_POST['authority'])) ? Utils::convertNumbersToLatin($antiXSS->xss_clean($_POST['authority'])) : null;

                $name = (isset($_POST['name'])) ? $antiXSS->xss_clean($_POST['name']) : null;
                $lname = (isset($_POST['lname'])) ? $antiXSS->xss_clean($_POST['lname']) : null;
                $father = (isset($_POST['father'])) ? $antiXSS->xss_clean($_POST['father']) : null;
                $birthdayLocation = (isset($_POST['birthdayLocation'])) ? $antiXSS->xss_clean($_POST['birthdayLocation']) : null;
                $birthdayTime = (isset($_POST['birthdayTime'])) ? $antiXSS->xss_clean($_POST['birthdayTime']) : null;
                $codeNational = (isset($_POST['codeNational'])) ? $antiXSS->xss_clean($_POST['codeNational']) : null;
                $gender = (isset($_POST['gender']) && in_array($_POST['gender'], ['man', 'female'])) ? $antiXSS->xss_clean($_POST['gender']) : 'man';
                $military = (isset($_POST['military']) && in_array($_POST['military'], ['end', 'sponsorship', 'exempt'])) ? $antiXSS->xss_clean($_POST['military']) : null;
                $exemptionType = (isset($_POST['exemptionType'])) ? $antiXSS->xss_clean($_POST['exemptionType']) : null;
                $marital = (isset($_POST['marital']) && in_array($_POST['marital'], ['married', 'single'])) ? $antiXSS->xss_clean($_POST['marital']) : 'married';
                $countChild = (isset($_POST['countChild'])) ? $antiXSS->xss_clean($_POST['countChild']) : 0;
                $homeStatus = (isset($_POST['homeStatus']) && in_array($_POST['homeStatus'], ['personal', 'rental', 'father'])) ? $antiXSS->xss_clean($_POST['homeStatus']) : 'personal';
                $insuranceStatus = (isset($_POST['insuranceStatus']) && in_array($_POST['insuranceStatus'], ['tamin', 'darman', 'mosalah', 'no'])) ? $antiXSS->xss_clean($_POST['insuranceStatus']) : 'no';
                $insuranceTime = (isset($_POST['insuranceTime'])) ? $antiXSS->xss_clean($_POST['insuranceTime']) : null;
                $mobile = (isset($_POST['mobile'])) ? $antiXSS->xss_clean($_POST['mobile']) : null;
                $phone = (isset($_POST['phone'])) ? $antiXSS->xss_clean($_POST['phone']) : null;
                $addressLocation = (isset($_POST['addressLocation'])) ? $antiXSS->xss_clean($_POST['addressLocation']) : null;
                $liveLocationCountry = (isset($_POST['liveLocationCountry'])) ? $antiXSS->xss_clean($_POST['liveLocationCountry']) : null;
                //                $liveLocationState = (isset($_POST['liveLocationState'])) ? $antiXSS->xss_clean($_POST['liveLocationState']) : null;
                $liveLocationCity = (isset($_POST['liveLocationCity'])) ? $antiXSS->xss_clean($_POST['liveLocationCity']) : null;
                $company = (isset($_POST['company'])) ? $antiXSS->xss_clean($_POST['company']) : null;
                $eduName1 = (isset($_POST['eduName1'])) ? $antiXSS->xss_clean($_POST['eduName1']) : null;
                $eduName2 = (isset($_POST['eduName2'])) ? $antiXSS->xss_clean($_POST['eduName2']) : null;
                $eduName3 = (isset($_POST['eduName3'])) ? $antiXSS->xss_clean($_POST['eduName3']) : null;
                $eduName4 = (isset($_POST['eduName4'])) ? $antiXSS->xss_clean($_POST['eduName4']) : null;
                $eduName5 = (isset($_POST['eduName5'])) ? $antiXSS->xss_clean($_POST['eduName5']) : null;
                $eduAddress1 = (isset($_POST['eduAddress1'])) ? $antiXSS->xss_clean($_POST['eduAddress1']) : null;
                $eduAddress2 = (isset($_POST['eduAddress2'])) ? $antiXSS->xss_clean($_POST['eduAddress2']) : null;
                $eduAddress3 = (isset($_POST['eduAddress3'])) ? $antiXSS->xss_clean($_POST['eduAddress3']) : null;
                $eduAddress4 = (isset($_POST['eduAddress4'])) ? $antiXSS->xss_clean($_POST['eduAddress4']) : null;
                $eduAddress5 = (isset($_POST['eduAddress5'])) ? $antiXSS->xss_clean($_POST['eduAddress5']) : null;
                $language = (isset($_POST['language'])) ? $antiXSS->xss_clean($_POST['language']) : null;
                $record = (isset($_POST['record'])) ? $antiXSS->xss_clean($_POST['record']) : null;
                $category = (isset($_POST['category'])) ? $antiXSS->xss_clean($_POST['category']) : null;
                $work = (isset($_POST['work']) && in_array($_POST['work'], ['yes', 'no'])) ? $antiXSS->xss_clean($_POST['work']) : 'no';
                $guarantee = (isset($_POST['guarantee']) && in_array($_POST['guarantee'], ['yes', 'no'])) ? $antiXSS->xss_clean($_POST['guarantee']) : 'no';
                $transfer = (isset($_POST['transfer']) && in_array($_POST['transfer'], ['yes', 'no'])) ? $antiXSS->xss_clean($_POST['transfer']) : 'no';
                $price = (isset($_POST['price'])) ? $antiXSS->xss_clean($_POST['price']) : 0;
                $representativeName = (isset($_POST['representativeName'])) ? $antiXSS->xss_clean($_POST['representativeName']) : null;
                $representativePhone = (isset($_POST['representativePhone'])) ? $antiXSS->xss_clean($_POST['representativePhone']) : null;
                $representativeJob = (isset($_POST['representativeJob'])) ? $antiXSS->xss_clean($_POST['representativeJob']) : null;
                $representativeAddress = (isset($_POST['representativeAddress'])) ? $antiXSS->xss_clean($_POST['representativeAddress']) : null;
                $employ = (isset($_POST['employ']) && in_array($_POST['transfer'], ['post', 'relatives', 'jober', 'other'])) ? $antiXSS->xss_clean($_POST['employ']) : 'other';
                $token = (isset($_POST['token'])) ? $antiXSS->xss_clean($_POST['token']) : null;
                $attachments = (isset($_FILES)) ? $_FILES : [];

                $resultDb = Hire::getHireBycodeNational($codeNational);

                if ($resultDb == 200) {
                    $profile = null;
                    if (isset($_FILES['profile'])) {
                        $upload = Upload::upload($_FILES['profile'], '/uploads/hire');
                        if ($upload->status == 200) {
                            $profile = $upload->response;
                        }
                        array_splice($attachments, 0, 1);
                    }


                    $attachmentFiles = [];
                    foreach ($attachments as $file) {
                        $upload = Upload::upload($file, '/uploads/hire');
                        if ($upload->status == 200) {
                            $attachmentFiles[] = $upload->response;
                        }
                    }


                    if (!empty($token)) {
                        echo json_encode(Hire::setNewEmploy($token, $name, $lname, $father, $birthdayLocation, $birthdayTime, $codeNational,
                            $gender, $military, $exemptionType, $marital, $countChild, $homeStatus,
                            $insuranceStatus, $insuranceTime, $mobile, $phone, $addressLocation, $company,
                            $eduName1, $eduName2, $eduName3, $eduName4, $eduName5, $eduAddress1,
                            $eduAddress2, $eduAddress3, $eduAddress4, $eduAddress5, $language, $record, $work,
                            $guarantee, $transfer, $price, $representativeName, $representativePhone, $representativeJob,
                            $representativeAddress, $employ, $category, $liveLocationCountry, $liveLocationCity, $profile, $attachmentFiles));
                    } else {
                        echo parameterRequired();
                    }
                } else {
                    echo json_encode([
                        'status' => -9,
                        'message' => "Before Set",
                        'response' => Security::initCSRF('employ'),
                    ]);
                }


                break;


            default:
                echo parameterRequired();
                break;
        }
    } else {
        echo permissionAccess();
    }
}


function parameterRequired()
{
    return json_encode([
        'status' => 411,
        'message' => "Parameter"
    ]);
}

function permissionAccess()
{
    return json_encode([
        'status' => 403,
        'message' => "Server"
    ]);
}