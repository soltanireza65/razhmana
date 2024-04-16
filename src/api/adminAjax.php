<?php

global $lang;

use MJ\Keys\KEYS;
use MJ\Upload\Upload;
use MJ\Security\Security;
use MJ\Utils\Utils;
use voku\helper\AntiXSS;
use MJ\SMS\SMS;
use function MJ\Keys\sendResponse;


$json = json_decode(file_get_contents('php://input'), true);
$antiXSS = new AntiXSS();
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


if (!empty($json)) {


    $action = $json['action'];


    switch ($action) {

        /**
         * SET COOLIE FOR ADMIN THEME MODE
         * @author tjavan
         */
        case 'theme-mode':
            $mode = $antiXSS->xss_clean($json['mode']);

            if ($mode == 'dark') {
                $mode = 'dark';
            } else {
                $mode = 'light';
            }
            Utils::setTheme($mode);
            echo $mode;
            break;


        /**
         * SET COOLIE FOR ADMIN THEME MODE
         * @author tjavan
         */
        case 'time-admin':
            $lang = $antiXSS->xss_clean($json['lang']);

            $arrayExist = ['ir', 'en'];
            if (in_array($lang, $arrayExist)) {
                setcookie('time', $lang, time() + STABLE_COOKIE_TIMEOUT, '/');
                echo "reload";
            } else {
                echo "bad-request";
            }
            break;


        /**
         * Change Admin Language
         * @author tjavan
         */
        case 'change-language-admin':
            $lang = $antiXSS->xss_clean($json['lang']);

            $resultLanguagesSite = Utils::getFileValue("languages.json", "", false);
            $dataLanguagesSite = [];
            if (!empty($resultLanguagesSite)) {
                $dataLanguagesSite = json_decode($resultLanguagesSite);
                foreach ($dataLanguagesSite as $dataLanguagesSiteL) {
                    $dataLanguagesSite[] = $dataLanguagesSiteL->slug;
                }
            }

            if (in_array($lang, $dataLanguagesSite)) {
                setcookie('language', $lang, time() + STABLE_COOKIE_TIMEOUT, '/');
                User::changeUserLanguageOnChangeLanguage($lang);
                echo "reload";
            } else {
                echo "bad-request";
            }
            break;


        /**
         * logout Admin
         */
        case 'logout-admin':
            Admin::SetAdminLog("logout", "logout");
            setcookie('EID', null, -1, '/');
            setcookie('UID', null, -1, '/');
            setcookie('INF', null, -1, '/');
            unset($_COOKIE['EID']);
            unset($_COOKIE['UID']);
            unset($_COOKIE['INF']);
            break;


        /**
         * SET COOLIE FOR ADMIN SIDEBAR SIZE MODE
         * @author tjavan
         */
        case 'sidebar-size':
            $size = $antiXSS->xss_clean($json['size']);
            if ($size == 'default') {
                $size = 'default';
            } else {
                $size = 'condensed';
            }
            Utils::setSideBar($size);
            echo $size;
            break;


        /**
         * Login Admin
         * @author tjavan
         */
        case'login-admin':
            $mail = strtolower($antiXSS->xss_clean($json['mail']));
            $pass = $antiXSS->xss_clean($json['pass']);
            $check = $antiXSS->xss_clean($json['check']);
            $emailValid = Security::emailValidator($mail);


            if ($emailValid->status == 200) {

                // Set First SESSION
                if (!isset($_SESSION['admin_count'])) {
                    $_SESSION['admin_count'] = 0;
                    $_SESSION['admin_time'] = time();

                    $result = Admin::loginAdmin(Security::encrypt($mail), Security::encrypt($pass));
                    if ($result->status == 200) {
                        if ($result->response[0]->admin_status == "active") {
                            $adminID = Security::encrypt($result->response[0]->admin_id);
                            $adminEmail = $result->response[0]->admin_email;
                            $info = [
                                'nickname' => $result->response[0]->admin_nickname,
                                'role_id' => $result->response[0]->role_id
                            ];
                            $adminInfo = Security::encrypt(json_encode($info));
                            if ($check) {
                                setcookie("UID", $adminID, time() + ADMIN_COOKIE_TIMEOUT, "/");
                                setcookie("EID", $adminEmail, time() + ADMIN_COOKIE_TIMEOUT, "/");
                                setcookie("INF", $adminInfo, time() + ADMIN_COOKIE_TIMEOUT, "/");
                            } else {
                                setcookie("UID", $adminID, 0, "/");
                                setcookie("EID", $adminEmail, 0, "/");
                                setcookie("INF", $adminInfo, 0, "/");
                            }
                            unset($_SESSION['admin_count']);
                            unset($_SESSION['admin_time']);

                            Admin::SetAdminLog("login", "login", $result->response[0]->admin_id);
                            echo "successful";
                        } else {
                            echo "admin_status_inactive";
                        }
                    } else {
                        echo "user_not_find";
                    }


                } else {
                    // Set second unit 5 SESSION
                    if ($_SESSION['admin_count'] < ADMIN_BLOCK_COUNT_LOGIN) {
                        $_SESSION['admin_count'] += 1;


                        $result = Admin::loginAdmin(Security::encrypt($mail), Security::encrypt($pass));
                        if ($result->status == 200) {
                            if ($result->response[0]->admin_status == "active") {
                                $adminID = Security::encrypt($result->response[0]->admin_id);
                                $adminEmail = $result->response[0]->admin_email;
                                $info = [
                                    'nickname' => $result->response[0]->admin_nickname,
                                    'role_id' => $result->response[0]->role_id
                                ];
                                $adminInfo = Security::encrypt(json_encode($info));
                                if ($check) {
                                    setcookie("UID", $adminID, time() + ADMIN_COOKIE_TIMEOUT, "/");
                                    setcookie("EID", $adminEmail, time() + ADMIN_COOKIE_TIMEOUT, "/");
                                    setcookie("INF", $adminInfo, time() + ADMIN_COOKIE_TIMEOUT, "/");
                                } else {
                                    setcookie("UID", $adminID, 0, "/");
                                    setcookie("EID", $adminEmail, 0, "/");
                                    setcookie("INF", $adminInfo, 0, "/");
                                }
                                unset($_SESSION['admin_count']);
                                unset($_SESSION['admin_time']);
                                Admin::SetAdminLog("login", "login", $result->response[0]->admin_id);
                                echo "successful";
                            } else {
                                echo "admin_status_inactive";
                            }
                        } else {
                            echo "user_not_find";
                        }


                    } else {

                        // end count start time
                        if (time() - $_SESSION['admin_time'] > ADMIN_BLOCK_TIME_LOGIN) {
                            $_SESSION['admin_count'] = 0;
                            $_SESSION['admin_time'] = time();

                            $result = Admin::loginAdmin(Security::encrypt($mail), Security::encrypt($pass));
                            if ($result->status == 200) {
                                if ($result->response[0]->admin_status == "active") {
                                    $adminID = Security::encrypt($result->response[0]->admin_id);
                                    $adminEmail = $result->response[0]->admin_email;
                                    $info = [
                                        'nickname' => $result->response[0]->admin_nickname,
                                        'role_id' => $result->response[0]->role_id
                                    ];
                                    $adminInfo = Security::encrypt(json_encode($info));
                                    if ($check) {
                                        setcookie("UID", $adminID, time() + ADMIN_COOKIE_TIMEOUT, "/");
                                        setcookie("EID", $adminEmail, time() + ADMIN_COOKIE_TIMEOUT, "/");
                                        setcookie("INF", $adminInfo, time() + ADMIN_COOKIE_TIMEOUT, "/");
                                    } else {
                                        setcookie("UID", $adminID, 0, "/");
                                        setcookie("EID", $adminEmail, 0, "/");
                                        setcookie("INF", $adminInfo, 0, "/");
                                    }
                                    unset($_SESSION['admin_count']);
                                    unset($_SESSION['admin_time']);

                                    Admin::SetAdminLog("login", "login", $result->response[0]->admin_id);
                                    echo "successful";
                                } else {
                                    echo "admin_status_inactive";
                                }

                            } else {
                                echo "user_not_find";
                            }


                        } else {
                            echo "admin_block_time";
                        }
                    }
                }


            } else {
                echo "email_invalid";
            }

            break;


        /**
         * My Account Edit
         */
        case 'myaccount-edit':
            $nickname = $antiXSS->xss_clean($json['nickname']);
            $password = $antiXSS->xss_clean($json['password']);
            $token = $antiXSS->xss_clean($json['token']);
            $avatar = $antiXSS->xss_clean($json['avatar']);

            if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID']) && strlen($nickname) > 2 && strlen($password) > 7) {
                $id = Security::decrypt($antiXSS->xss_clean($_COOKIE['UID']));

                $passValid = Security::passwordValidator($password);
                if ($passValid->status == 200) {

                    if (!empty($avatar)) {


                        $result_token = Security::verifyCSRF2($token, false);
                        if ($result_token) {

                            $resultAvatar = Upload::uploadBase64($avatar, ADMIN_ADDRESS);

                            if ($resultAvatar->status == 200 && !empty($resultAvatar->response)) {
                                $avatarURL = $resultAvatar->response;

                                $result = Admin::myaccountEdit($id, $nickname, Security::encrypt($password), $avatarURL);
                                if ($result->status == 200) {
                                    echo "successful";
                                    Admin::SetAdminLog("myaccount_edit");
                                } else {
                                    echo "error";
                                }

                            } else {
                                echo "avatar_error";
                            }

                        } else {
                            echo "token_error";
                        }


                    } else {

                        $result_token = Security::verifyCSRF2($token, false);
                        if ($result_token) {
                            $result = Admin::myaccountEdit($id, $nickname, Security::encrypt($password), "");
                            if ($result->status == 200) {
                                echo "successful";
                                Admin::SetAdminLog("myaccount_edit");
                            } else {
                                echo "error";
                            }
                        } else {
                            echo "token_error";
                        }

                    }

                } else {
                    echo "pass_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Lock Screen Admin
         */
        case 'lock-screen-admin':
            $password = $antiXSS->xss_clean($json['pass']);

            if (isset($_COOKIE['EID']) && !empty($_COOKIE['EID'])) {

                $mail = $antiXSS->xss_clean($_COOKIE['EID']);
                $pass = $antiXSS->xss_clean($password);

                // Set First SESSION
                if (!isset($_SESSION['admin_count'])) {
                    $_SESSION['admin_count'] = 0;
                    $_SESSION['admin_time'] = time();


                    $result = Admin::loginAdmin($mail, Security::encrypt($pass));
                    if ($result->status == 200) {
                        if ($result->response[0]->admin_status == "active") {
                            $adminID = Security::encrypt($result->response[0]->admin_id);
                            $adminEmail = $result->response[0]->admin_email;
                            $info = [
                                'nickname' => $result->response[0]->admin_nickname,
                                'role_id' => $result->response[0]->role_id
                            ];
                            $adminInfo = Security::encrypt(json_encode($info));
                            setcookie("UID", $adminID, 0, "/");
                            setcookie("EID", $adminEmail, 0, "/");
                            setcookie("INF", $adminInfo, 0, "/");
                            Admin::SetAdminLog("lock_screen_login", "lock_screen_login", $result->response[0]->admin_id);

                            unset($_SESSION['admin_count']);
                            unset($_SESSION['admin_time']);
                            echo "successful";

                        } else {
                            echo "admin_status_inactive";
                        }
                    } else {
                        echo "user_not_find";
                    }


                } else {
                    // Set second unit 5 SESSION
                    if ($_SESSION['admin_count'] < 6) {
                        $_SESSION['admin_count'] += 1;


                        $result = Admin::loginAdmin($mail, Security::encrypt($pass));
                        if ($result->status == 200) {
                            if ($result->response[0]->admin_status == "active") {
                                $adminID = Security::encrypt($result->response[0]->admin_id);
                                $adminEmail = $result->response[0]->admin_email;
                                $info = [
                                    'nickname' => $result->response[0]->admin_nickname,
                                    'role_id' => $result->response[0]->role_id
                                ];
                                $adminInfo = Security::encrypt(json_encode($info));

                                setcookie("UID", $adminID, 0, "/");
                                setcookie("EID", $adminEmail, 0, "/");
                                setcookie("INF", $adminInfo, 0, "/");
                                Admin::SetAdminLog("lock_screen_login", "lock_screen_login", $result->response[0]->admin_id);
                                unset($_SESSION['admin_count']);
                                unset($_SESSION['admin_time']);
                                echo "successful";
                            } else {
                                echo "admin_status_inactive";
                            }
                        } else {
                            echo "user_not_find";
                        }


                    } else {

                        // end count start time
                        if (time() - $_SESSION['admin_time'] > ADMIN_BLOCK_TIME_LOGIN) {
                            $_SESSION['admin_count'] = 0;
                            $_SESSION['admin_time'] = time();

                            $result = Admin::loginAdmin($mail, Security::encrypt($pass));
                            if ($result->status == 200) {
                                if ($result->response[0]->admin_status == "active") {
                                    $adminID = Security::encrypt($result->response[0]->admin_id);
                                    $adminEmail = $result->response[0]->admin_email;
                                    $info = [
                                        'nickname' => $result->response[0]->admin_nickname,
                                        'role_id' => $result->response[0]->role_id
                                    ];
                                    $adminInfo = Security::encrypt(json_encode($info));

                                    setcookie("UID", $adminID, 0, "/");
                                    setcookie("EID", $adminEmail, 0, "/");
                                    setcookie("INF", $adminInfo, 0, "/");
                                    Admin::SetAdminLog("lock_screen_login", "lock_screen_login", $result->response[0]->admin_id);
                                    unset($_SESSION['admin_count']);
                                    unset($_SESSION['admin_time']);
                                    echo "successful";
                                } else {
                                    echo "admin_status_inactive";
                                }

                            } else {
                                echo "user_not_find";
                            }


                        } else {
                            echo "admin_block_time";
                        }
                    }
                }


            } else {
                echo "email_invalid";
            }
            break;


        /**
         * Admin Role And Permission Add
         * @author tjavan
         */
        case 'admin-role-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean($json['title']);
            $permission = $antiXSS->xss_clean($json['permission']);
            $token = $antiXSS->xss_clean($json['token']);
            $name = json_decode($title)[0]->value;
            if ($status != 'active') {
                $status = "inactive";
            }

            $result_token = Security::verifyCSRF2($token);
            if ($result_token) {

                if (strlen($title) > 2) {

                    $result = Admin::addAdminRolePermission($title, $permission, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("add_new_role_admin_title_" . $name, 'admins');
                    } else {
                        echo "error";
                    }
                } else {
                    echo "empty";
                }
            } else {
                echo "token_error";
            }

            break;


        /**
         * Admin Role Delete If Not Admin Use This Role
         * @author tjavan
         */
        case 'admin-role-delete':
            $id = intval($json['id']);
            $token = $antiXSS->xss_clean($json['token']);
            $replacementRole = $antiXSS->xss_clean($json['replacementRole']);


            $result_token = Security::verifyCSRF2($token);
            if ($result_token) {

                $flag = true;
                $resultAllAdminHaveThisRole = Admin::getAllAdminHaveThisRole($id);
                $dataAllAdminHaveThisRole = [];
                if ($resultAllAdminHaveThisRole->status == 200 && !empty($resultAllAdminHaveThisRole->response)) {
                    $dataAllAdminHaveThisRole = $resultAllAdminHaveThisRole->response;
                }

                if (count($dataAllAdminHaveThisRole) <= 0) {

                    $r1 = Admin::getAdminRolePermissionsByRoleId($id);
                    if ($r1->status == 200 && !empty($r1->response)) {
                        foreach ($r1->response as $r1ITEM) {

                            $r2 = Admin::getAdminPermissionsByPermissionsId($r1ITEM->permission_id);
                            if ($r2->status == 200 && !empty($r2->response)) {
                                foreach ($r2->response as $r2ITEM) {
//                                print_r($r2ITEM->permission_id);
                                    $r22 = Admin::deleteAdminPermissions($r2ITEM->permission_id);
//                                print_r($r22);
                                    if ($r22->status != 200) {
                                        $flag = false;
                                    }
                                }

                            }
//                        print_r($r1ITEM->rp_id);
//                        $r11 = Admin::deleteAdminRolePermissions($r1ITEM->rp_id);
//                        print_r($r11);
//                        if ($r11->status != 200) {
//                            $flag = false;
//                        }
                        }

                        $r3 = Admin::deleteAdminRole($id);
//                    print_r($r3);
                        if ($r3->status != 200) {
                            $flag = false;
                        }


                        if ($flag == true) {
                            echo "successful";
                            Admin::SetAdminLog("admin_role_delete", "admins");
                        } else {
                            echo "end";
                        }
                    } else {
                        echo "error";
                    }
//                print_r($r1);


                } else {
//                    echo "id_used";
                    $result_REP = Admin::updateAdminsReplaceRole($id, $replacementRole);
                    if ($result_REP->status == 200) {


                        $r1 = Admin::getAdminRolePermissionsByRoleId($id);
                        if ($r1->status == 200 && !empty($r1->response)) {
                            foreach ($r1->response as $r1ITEM) {

                                $r2 = Admin::getAdminPermissionsByPermissionsId($r1ITEM->permission_id);
                                if ($r2->status == 200 && !empty($r2->response)) {
                                    foreach ($r2->response as $r2ITEM) {
                                        $r22 = Admin::deleteAdminPermissions($r2ITEM->permission_id);
                                        if ($r22->status != 200) {
                                            $flag = false;
                                        }
                                    }

                                }
                            }
                            $r3 = Admin::deleteAdminRole($id);
                            if ($r3->status != 200) {
                                $flag = false;
                            }
                            if ($flag == true) {
                                echo "successful";
                                Admin::SetAdminLog("admin_role_delete", "admins");
                            } else {
                                echo "end";
                            }
                        } else {
                            echo "error";
                        }


                    }

                }
            } else {
                echo "token_error";
            }


            break;


        /**
         * Admin Role Edit
         * @author tjavan
         */
        case 'admin-role-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean($json['title']);
            $id = (int)$antiXSS->xss_clean($json['id']);
            $permission = $antiXSS->xss_clean($json['permission']);
            $token = $antiXSS->xss_clean($json['token']);
            $name = json_decode($title)[0]->value;

            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {

                if ($status != 'active') {
                    $status = "inactive";
                }

                if (strlen($title) > 2 && $id > 0) {

                    $result = Admin::editAdminRole($id, $title, $status, $permission);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("edit_role_admin_title_" . $name, 'admins');
                    } else {
                        echo "error";
                    }

                } else {
                    echo "empty";
                }
            } else {
                echo "token_error";
            }

            break;


        /**
         * Admin ADD
         * @author tjavan
         */
        case 'admin-add':
            $status = $antiXSS->xss_clean($json['status']);
            $name = $antiXSS->xss_clean($json['name']);
            $nickname = $antiXSS->xss_clean($json['nickname']);
            $email = strtolower($antiXSS->xss_clean($json['email']));
            $password = $antiXSS->xss_clean($json['password']);
            $avatar = $antiXSS->xss_clean($json['avatar']);
            $role = (int)$antiXSS->xss_clean($json['role']);
            $mobile = $antiXSS->xss_clean($json['mobile']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF2($token);
            if ($result_token) {

                if ($status != 'active') {
                    $status = "inactive";
                }

                if (empty($role) || $role == 0) {
                    $role = null;
                }

                $passValid = Security::passwordValidator($password);
                $emailValid = Security::emailValidator($email);


                $emailEnc = Security::encrypt($email);
                $checkAdminEmail = Admin::checkAdminEmail($emailEnc);
                $mobileEnc = Security::encrypt($mobile);
                $checkAdminPhone = Admin::checkAdminPhone($mobileEnc);
                $checkAdminMobile = Security::phoneNumberValidator($mobile);

                if ($checkAdminEmail->status == 200) {
                    echo "email_exist";
                } elseif ($checkAdminPhone->status == 200) {
                    echo "phone_exist";
                } else {
                    if ($checkAdminMobile->status == 200) {

                        if (strlen($name) > 2 && strlen($nickname) > 2 && strlen($email) > 2 && strlen($mobile) == 11 && strlen($password) > 2 && strlen($avatar) > 50) {
                            if ($passValid->status == 200 && $emailValid->status == 200) {


                                $resultAvatar = Upload::uploadBase64($avatar, ADMIN_ADDRESS);

                                if ($resultAvatar->status == 200 && !empty($resultAvatar->response)) {
                                    $avatarURL = $resultAvatar->response;

                                    $result = Admin::setNewAdmin(Security::encrypt($name), $nickname, Security::encrypt($mobile), $emailEnc, Security::encrypt($password), $avatarURL, $status, intval($role));
//                          print_r($result);
                                    if ($result->status == 200) {
                                        //$result->response
                                        echo "successful";
                                        Admin::SetAdminLog("add_new_admin_title_" . $name, 'admins');
                                    } else {
                                        echo "error";
                                    }
                                } else {
                                    echo "avatar_error";
                                }


                            } else {
                                if ($passValid->status != 200 && $emailValid->status != 200) {
                                    echo "email_pass_error";
                                } elseif ($passValid->status != 200) {
                                    echo "pass_error";
                                } else {
                                    echo "email_error";
                                }
                            }

                        } else {
                            echo "empty";
                        }

                    } else {
                        echo "mobile_invalid";
                    }
                }
            } else {
                echo "token_error";
            }

            break;


        /**
         * Admin Edit
         * @author tjavan
         */
        case 'admin-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $name = $antiXSS->xss_clean($json['name']);
            $nickname = $antiXSS->xss_clean($json['nickname']);
            $email = strtolower($antiXSS->xss_clean($json['email']));
            $password = $antiXSS->xss_clean($json['password']);
            $avatar = $antiXSS->xss_clean($json['avatar']);
            $mobile = $antiXSS->xss_clean($json['mobile']);
            $role = (int)$antiXSS->xss_clean($json['role']);
            $id = (int)$antiXSS->xss_clean($json['id']);
            $token = $antiXSS->xss_clean($json['token']);


            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {

                if ($status != 'active') {
                    $status = "inactive";
                }

                if (empty($role) || $role == 0) {
                    $role = null;
                }

                $passValid = Security::passwordValidator($password);
                $emailValid = Security::emailValidator($email);


                $emailEnc = Security::encrypt($email);
                $checkAdminEmail = Admin::checkAdminEmail($emailEnc);
                $checkAdminMobile = Security::phoneNumberValidator($mobile);

                $mobileEnc = Security::encrypt($mobile);
                $checkAdminPhone = Admin::checkAdminPhone($mobileEnc);

                $flag = true;
                if ($checkAdminEmail->status == 200) {
                    if ($checkAdminEmail->response[0]->admin_id == $id) {
                        $flag = false;
                    } else {
//                    echo "email_exist";
                        $flag = true;
                    }
                } else {
                    $flag = false;
                }


                $flagPhone = true;
                if ($checkAdminPhone->status == 200) {
                    if ($checkAdminPhone->response[0]->admin_id == $id) {
                        $flagPhone = false;
                    } else {
                        $flagPhone = true;
                    }
                } else {
                    $flagPhone = false;
                }

                if ($flag) {
                    echo "email_exist";
                } elseif ($flagPhone) {
                    echo "phone_exist";
                } else {

                    if ($checkAdminMobile->status == 200) {
                        if (strlen($name) > 2 && strlen($nickname) > 2 && strlen($email) > 2 && strlen($password) > 2) {
                            if ($passValid->status == 200 && $emailValid->status == 200) {


                                if (!empty($avatar)) {
                                    $resultAvatar = Upload::uploadBase64($avatar, ADMIN_ADDRESS);

                                    if ($resultAvatar->status == 200 && !empty($resultAvatar->response)) {
                                        $avatarURL = $resultAvatar->response;

                                        $result = Admin::editAdmin(Security::encrypt($name), $nickname, $emailEnc, Security::encrypt($password), Security::encrypt($mobile), $status, $id, $avatarURL, $role);
                                        if ($result->status == 200) {
                                            //$result->response
                                            echo "successful";
                                            Admin::SetAdminLog("edit_admin_title_" . $name, 'admins');
                                        } else {
                                            echo "error";
                                        }

                                    } else {
                                        echo "avatar_error";
                                    }

                                } else {

                                    $result = Admin::editAdmin(Security::encrypt($name), $nickname, $emailEnc, Security::encrypt($password), Security::encrypt($mobile), $status, $id, "", $role);
                                    if ($result->status == 200) {
                                        //$result->response
                                        echo "successful";
                                        Admin::SetAdminLog("edit_admin_title_" . $name, 'admins');
                                    } else {
                                        echo "error";
                                    }
                                }


                            } else {
                                if ($passValid->status != 200 && $emailValid->status != 200) {
                                    echo "email_pass_error";
                                } elseif ($passValid->status != 200) {
                                    echo "pass_error";
                                } else {
                                    echo "email_error";
                                }
                            }

                        } else {
                            echo "empty";
                        }
                    } else {
                        echo "mobile_invalid";
                    }
                }
            } else {
                echo "token_error";
            }


            break;


        case 'department-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);
            $type = $antiXSS->xss_clean($json['type']);

            if ($status != 'active') {
                $status = "inactive";
            }

            if ($type != 'driver') {
                $type = "businessman";
            }

            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag == true) {


                $result_token = Security::verifyCSRF('admin-department-add', $token);
                if ($result_token) {

                    $result = ATicket::setNewDepartment($title, $type, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("department_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit Department
         * @author tjavan
         */
        case 'department-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);
            $type = $antiXSS->xss_clean($json['type']);

            if ($status != 'active') {
                $status = "inactive";
            }


            if ($type != 'driver') {
                $type = "businessman";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag == true && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF('admin-department-edit', $token);
                if ($result_token) {

                    $result = ATicket::editDepartment($id, $title, $type, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("department_edit_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Close Room
         * @author tjavan
         */
        case 'close-room':
            $roomID = (int)$json['roomID'];
            $token = $antiXSS->xss_clean($json['token']);

            if (strlen($roomID) > 0) {

                $result_token = Security::verifyCSRF('admin-set-ticket-exist-room', $token);
                if ($result_token) {

                    $result = ATicket::closeRoom($roomID);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("close_room_id_" . $roomID);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }
            break;


        /**
         * Edit Currency
         * @author tjavan
         */
        case 'currency-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag == true && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF('admin-currency-edit', $token);
                if ($result_token) {

                    $result = Currency::editCurrency($id, $title, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("edit_currency_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Update Settings Main
         * @author tjavan
         */
//        case 'settings-main':
//            $name = $antiXSS->xss_clean($json['name']);
//            $name_farsi = $antiXSS->xss_clean($json['name_farsi']);
//            $keywords = $antiXSS->xss_clean($json['keywords']);
//            $description = $antiXSS->xss_clean($json['description']);
//            $token = $antiXSS->xss_clean($json['token']);
//
//            $result_token = Security::verifyCSRF('admin-settings-general', $token);
//            if ($result_token) {
//
//                $result1 = Utils::setFileText("settings.txt", 'name', $name);
//                $result2 = Utils::setFileText("settings.txt", 'name_farsi', $name_farsi);
//                $result3 = Utils::setFileText("settings.txt", 'keywords', $keywords);
//                $result4 = Utils::setFileText("settings.txt", 'description', $description);
//
//
//                if ($result1 == 200 && $result2 == 200 && $result3 == 200 && $result4 == 200) {
//                    echo "successful";
//                    Admin::SetAdminLog("تنظیمات عمومی سایت");
//                } else {
//                    echo "error";
//                }
//            } else {
//                echo "token_error";
//            }
//
//            break;


        /**
         * Update Settings Main
         * @author tjavan
         */
        case 'settings-theme':
            $faviconSite = $antiXSS->xss_clean($json['faviconSite']);
            $logoSm = $antiXSS->xss_clean($json['logoSm']);
            $logoLight = $antiXSS->xss_clean($json['logoLight']);
            $logoDark = $antiXSS->xss_clean($json['logoDark']);
            $token = $antiXSS->xss_clean($json['token']);

            if (!file_exists(getcwd() . SITE_ADDRESS) && !is_dir(getcwd() . SITE_ADDRESS)) {
                mkdir(getcwd() . SITE_ADDRESS);
            }

            if (empty($faviconSite) && empty($logoSm) && empty($logoLight) && empty($logoDark)) {
                echo 'empty';
            } else {
                $result_token = Security::verifyCSRF('admin-settings-general', $token);
                if ($result_token) {

                    $flag1 = true;
                    if (!empty($faviconSite)) {
                        $flag1 = false;
                        $result_faviconSite = Upload::uploadBase64($faviconSite, SITE_ADDRESS, 'favicon');
                        if ($result_faviconSite->status == 200) {
                            $flag1 = true;
                        } else {
                            $flag1 = false;
                        }
                    }


                    $flag2 = true;
                    if (!empty($logoSm)) {
                        $flag2 = false;
                        $result_logoSm = Upload::uploadBase64($logoSm, SITE_ADDRESS, "logo-sm");
                        if ($result_logoSm->status == 200) {
                            $flag2 = true;
                        } else {
                            $flag2 = false;
                        }
                    }


                    $flag3 = true;
                    if (!empty($logoLight)) {
                        $flag3 = false;
                        $result_logoLight = Upload::uploadBase64($logoLight, SITE_ADDRESS, "logo-light");
                        if ($result_logoLight->status == 200) {
                            $flag3 = true;
                        } else {
                            $flag3 = false;
                        }
                    }


                    $flag4 = true;
                    if (!empty($logoDark)) {
                        $flag4 = false;
                        $result_logoDark = Upload::uploadBase64($logoDark, SITE_ADDRESS, "logo-dark");
                        if ($result_logoDark->status == 200) {
                            $flag4 = true;
                        } else {
                            $flag4 = false;
                        }
                    }


                    if ($flag1 == true && $flag2 == true && $flag3 == true && $flag4 == true) {
                        echo 'successful';
                        Admin::SetAdminLog("settings_theme");
                    } else {
                        echo 'error';
                    }
                } else {
                    echo "token_error";
                }


            }


            break;


        /**
         * Update Settings Social
         * @author tjavan
         */
        case 'settings-social':
            $support_call = $antiXSS->xss_clean($json['support_call']);
            $support_call_2 = $antiXSS->xss_clean($json['support_call_2']);
            $whatsapp = $antiXSS->xss_clean($json['whatsapp']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF('admin-settings-general', $token);
            if ($result_token) {

                $result1 = Utils::setFileText("settings.txt", 'whatsapp', $whatsapp);
                $result2 = Utils::setFileText("settings.txt", 'support_call', $support_call);
                $result3 = Utils::setFileText("settings.txt", 'support_call_2', $support_call_2);


                if ($result1 == 200 && $result2 == 200 && $result3 == 200) {
                    echo "successful " . Security::initCSRF('admin-settings-general');;
                    Admin::SetAdminLog("settings_social");
                } else {
                    echo "error";
                }
            } else {
                echo "token_error";
            }

            break;


        /**
         * Settings payment
         */
        case 'settings-payment':

            $jibit_merchantid = $antiXSS->xss_clean($json['jibit_merchantid']);
            $jibit_status = (bool)$antiXSS->xss_clean($json['jibit_status']);
            $jibit_icon = $antiXSS->xss_clean($json['jibit_icon']);
            $token = $antiXSS->xss_clean($json['token']);


            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {


                $flag3 = true;
                if (!empty($jibit_icon)) {
                    $flag3 = false;
                    $result_jibit_icon = Upload::uploadBase64($jibit_icon, PAYMENT_ADDRESS, 'jibit');
                    if ($result_jibit_icon->status == 200) {
                        $flag3 = true;
                    } else {
                        $flag3 = false;
                    }
                }


                $result5 = Utils::setFileText("settings.txt", 'jibit_merchantid', $jibit_merchantid);
                $result6 = Utils::setFileText("settings.txt", 'jibit_status', $jibit_status);

                if ($result5 == 200 && $result6 == 200 && $flag3 == true) {
                    echo "successful";
                    Admin::SetAdminLog("settings_payment");
                } else {
                    echo "error";
                }
            } else {
                echo "token_error";
            }
            break;


        /**
         * settings sms
         */
        case 'settings-sms':
            $sms_panel = $antiXSS->xss_clean($json['sms_panel']);
            $ghasedak_api = $antiXSS->xss_clean($json['ghasedak_api']);
            $ghasedak_sender_number = $antiXSS->xss_clean($json['ghasedak_sender_number']);
            $ghasedak_price_low = $antiXSS->xss_clean($json['ghasedak_price_low']);
            $ghasedak_admins_mobile = $antiXSS->xss_clean($json['ghasedak_admins_mobile']);
            $ghasedak_template_low_price = $antiXSS->xss_clean($json['ghasedak_template_low_price']);
            $token = $antiXSS->xss_clean($json['token']);


            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {

                $result1 = Utils::setFileText("settings.txt", 'sms_panel', $sms_panel);
                $result2 = Utils::setFileText("settings.txt", 'ghasedak_api', $ghasedak_api);
                $result3 = Utils::setFileText("settings.txt", 'ghasedak_sender_number', $ghasedak_sender_number);
                $result4 = Utils::setFileText("settings.txt", 'ghasedak_price_low', $ghasedak_price_low);
                $result5 = Utils::setFileText("settings.txt", 'ghasedak_admins_mobile', $ghasedak_admins_mobile);
                $result6 = Utils::setFileText("settings.txt", 'ghasedak_template_low_price', $ghasedak_template_low_price);

                if (!empty($myArray)) {
                    foreach (json_decode($myArray) as $loop) {
                        $result = Utils::setFileText("settings.txt", $loop->id, $loop->value);
                    }
                }

                if ($result1 == 200 && $result2 == 200 && $result3 == 200 && $result4 == 200 && $result5 == 200 && $result6 == 200) {
                    echo "successful";
                    Admin::SetAdminLog("settings_sms");
                } else {
                    echo "error";
                }
            } else {
                echo "token_error";
            }


            break;


        /**
         * Delete Media
         */
        case 'media-delete':
            $src = $antiXSS->xss_clean($json['src']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {
                $str = str_replace('https://ntirapp.com/', '/home/ntirapp/public_html/', $src);

                $path = parse_url($src, PHP_URL_PATH);
                $fileName = basename($path);

                // Delete the file.
                $temp = unlink('/public_html/uploads/medias/'.$fileName);
                if ($temp == 1) {
                    Admin::SetAdminLog("medias_delete", "medias_delete");
                    echo "successful";
                } else {
                    echo "error";
                }
            } else {
                echo "token_error";
            }

            break;


        /**
         * Insert New Group Notification
         */
        case 'ngroup-add':
            $type = $antiXSS->xss_clean($json['type']);
            $title = strip_tags($antiXSS->xss_clean($json['title']));
            $sender = strip_tags($antiXSS->xss_clean($json['sender']));
            $text = $antiXSS->xss_clean($json['text']);
            $status = $antiXSS->xss_clean($json['status']);
            $language = $antiXSS->xss_clean($json['language']);
            $notics_type = $antiXSS->xss_clean($json['notics_type']);
            $token = $antiXSS->xss_clean($json['token']);


            if ($status != "active") {
                $status = "inactive";
            }
//            $arrayExist = ['driver', 'businessman'];

            if (count($type) > 0 && strlen($title) > 2 && strlen($sender) > 2 && strlen($text) > 2 && strlen($language) > 3) {

                $result_token = Security::verifyCSRF('admin-ngroup-add', $token);
                if ($result_token) {

                    $flag = true;
                    foreach ($type as $loop) {
                        $result = GNotification::addGroupNotification($title, $sender, $text, $loop['type'], $loop['id'], $status, $language, $notics_type);
                        if ($result->status != 200) {
                            $flag = false;
                        }
                    }

                    if ($flag == true) {
                        echo "successful";
                        Admin::SetAdminLog("add_ngroup_add_title_" . $title, "add_ngroup");
                    } else {
                        echo "error";
                    }

                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Show Group Notification
         */
        case 'group-notification-show':
            $id = (int)$antiXSS->xss_clean($json['id']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($id > 0 && $token) {

                $result_token = Security::verifyCSRF('admin-group-notification', $token);
                if ($result_token) {

                    $result = GNotification::getGroupNotificationById($id);
                    if ($result->status == 200 && !empty($result->response) && !empty($result->response[0])) {
                        $myObj = new stdClass();
                        $myObj->title = $result->response[0]->ngroup_title;
                        $myObj->message = $result->response[0]->ngroup_message;
                        $myObj->time = Utils::getTimeCountry('Y/m/d  H:i', json_decode($result->response[0]->ngroup_options)->date_create);
                        $myObj->sender = $result->response[0]->ngroup_sender;
                        $myObj->token = Security::initCSRF('admin-group-notification');
                        $myJSON = json_encode($myObj);
                        echo $myJSON;
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "error";
            }

            break;


        /**
         * Edit Group Notification
         */
        case 'ngroup-edit':
            $id = (int)$antiXSS->xss_clean($json['id']);
            $relation_type = $antiXSS->xss_clean($json['relation_type']);
            $relation = $antiXSS->xss_clean($json['relation']);
            $title = strip_tags($antiXSS->xss_clean($json['title']));
            $sender = strip_tags($antiXSS->xss_clean($json['sender']));
            $text = $antiXSS->xss_clean($json['text']);
            $status = $antiXSS->xss_clean($json['status']);
            $language = $antiXSS->xss_clean($json['language']);
            $notics_type = $antiXSS->xss_clean($json['notics_type']);

            $token = $antiXSS->xss_clean($json['token']);

            if ($status != "active") {
                $status = "inactive";
            }
            $arrayExist = ['driver', 'businessman'];

            if (strlen($title) > 2 && strlen($sender) > 2 && strlen($text) > 2 && strlen($relation_type) > 0 && strlen($relation) > 0
                && $id > 0 && in_array($relation_type, $arrayExist)) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = GNotification::editGroupNotification($id, $sender, $title, $text, $relation_type, $relation, $language, $status, $notics_type);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("edit_ngroup_title_" . $title, "edit_ngroup");
                    } else {
                        echo "error";
                    }

                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Delete Group Notification
         */
        case 'ngroup-delete':
            $id = (int)$antiXSS->xss_clean($json['id']);
            $token = $antiXSS->xss_clean($json['token']);


            if ($id > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $name = "";
                    $result0 = GNotification::getGroupNotificationById($id);
                    if ($result0->status == 200 && !empty($result0->response) && !empty($result0->response[0])) {
                        $name = $result0->response[0]->ngroup_title;
                    }

                    $result = GNotification::deleteGroupNotification($id);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("delete_ngroup_title_" . $name, "delete_ngroup");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }
            break;


        /**
         * Add Category Post
         * @author tjavan
         */
        case 'category-post-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $language = $antiXSS->xss_clean($json['lang']);
            $metaTitle = $antiXSS->xss_clean($json['metaTitle']);
            $metaDesc = $antiXSS->xss_clean($json['metaDesc']);
            $schema = $antiXSS->xss_clean($json['schema']);
            $token = $antiXSS->xss_clean($json['token']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);

            if ($status != 'active') {
                $status = "inactive";
            }


            if (strlen($title) > 2 && strlen($language) > 3) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Post::SetNewPostCategory($title, $language, $status, $priority, $metaTitle, $metaDesc, $schema);

                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("add_category_post_title_" . $title, "post");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit Category Post
         * @author tjavan
         */
        case 'category-post-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean($json['title']);
            $language = $antiXSS->xss_clean($json['language']);
            $cat_id = $antiXSS->xss_clean($json['cat_id']);
            $metaTitle = $antiXSS->xss_clean($json['metaTitle']);
            $metaDesc = $antiXSS->xss_clean($json['metaDesc']);
            $schema = $antiXSS->xss_clean($json['schema']);
            $token = $antiXSS->xss_clean($json['token']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);

            if ($status == 'active') {
                $status = "active";
            } else {
                $status = "inactive";
            }


            if (strlen($title) > 2 && strlen($language) > 3) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Post::editPostCategory($cat_id, $title, $language, $status, $priority, $metaTitle, $metaDesc, $schema);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("edit_category_post_id_" . $cat_id, 'post');
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit Product
         * @author tjavan
         */
        case 'category-post-delete':
            $categoryReplace = (int)$antiXSS->xss_clean($json['categoryReplace']);
            $categoryID = (int)$antiXSS->xss_clean($json['categoryID']);
            $token = $antiXSS->xss_clean($json['token']);


            $result_token = Security::verifyCSRF2($token);
            if ($result_token) {
                if ($categoryID && $categoryID > 0 && $categoryReplace && $categoryReplace > 0) {

                    $result = Post::deleteCategoryPost($categoryID, $categoryReplace);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("delete_category_post_id_replate_" . $categoryID . "==>" . $categoryReplace, "post");
                    } else {
                        echo "error";
                    }


                } else {
                    echo "empty";
                }
            } else {
                echo "token_error";
            }

            break;


        /**
         * Add Post
         * @author tjavan
         */
        case 'post-add':
            $token = $antiXSS->xss_clean($json['token']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $myContent = $json['myContent'];
            $excerpt = $antiXSS->xss_clean(strip_tags($json['excerpt']));
            $metaTitle = $antiXSS->xss_clean($json['metaTitle']);
            $schema = $antiXSS->xss_clean($json['schema']);
            $status = $antiXSS->xss_clean($json['status']);
            $language = $antiXSS->xss_clean($json['language']);

            $category = (int)$antiXSS->xss_clean($json['category']);
            $thumbnail = $antiXSS->xss_clean($json['thumbnail']);
            $slug = str_replace(" ", "-", $antiXSS->xss_clean(strip_tags($json['slug'])));


            if ($status != 'published') {
                $status = "draft";
            }


            if (strlen($title) > 2 && $category > 0 && strlen($slug) > 4
                && strlen($thumbnail) > 50) {


                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {


                    if (!file_exists(getcwd() . BLOG_ADDRESS) && !is_dir(getcwd() . BLOG_ADDRESS)) {
                        mkdir(getcwd() . BLOG_ADDRESS);
                    }

                    $resultThumbnail = Upload::uploadBase64($thumbnail, BLOG_ADDRESS);


                    if ($resultThumbnail->status == 200 && !empty($resultThumbnail->response)) {
                        $thumbnailURL = $resultThumbnail->response;

                        $result = Post::SetNewPost($title, $myContent, $category, $slug, $status, $thumbnailURL, $language, $excerpt, $metaTitle, $schema, 0, 0);

                        if ($result->status == 200) {
                            echo "successful " . $result->response;
                            Admin::SetAdminLog("post_add_title_" . $title, "post");
                        } else {
                            echo "error";
                        }
                    } else {
                        echo "error_img";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Edit Post
         * @author tjavan
         */
        case 'post-edit':
            $token = $antiXSS->xss_clean($json['token']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $myContent = $json['myContent'];
            $excerpt = $antiXSS->xss_clean(strip_tags($json['excerpt']));
            $metaTitle = $antiXSS->xss_clean($json['metaTitle']);
            $schema = $antiXSS->xss_clean($json['schema']);
            $status = $antiXSS->xss_clean($json['status']);
            $language = $antiXSS->xss_clean($json['language']);
            $category = (int)$antiXSS->xss_clean($json['category']);
            $thumbnail = $antiXSS->xss_clean($json['thumbnail']);
            $slug = str_replace(" ", "-", $antiXSS->xss_clean(strip_tags($json['slug'])));
            $id = (int)$antiXSS->xss_clean($json['id']);

            if ($status != 'published') {
                $status = "draft";
            }


            if (strlen($title) > 2 && $category > 0 && strlen($slug) > 4 && $id > 0 && strlen($language) > 3) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $image = "";
                    if (!empty($thumbnail)) {
                        $resultThumbnail = Upload::uploadBase64($thumbnail, BLOG_ADDRESS);
                        if ($resultThumbnail->status == 200 && !empty($resultThumbnail->response)) {
                            $image = $resultThumbnail->response;
                        }
                    }

                    $result = Post::editPost($id, $title, $myContent, $category, $slug, $status, $image, $language, $excerpt, $metaTitle, $schema, null, null);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("post_edit_id_" . $id, "post");
                    } else {
                        echo "error";
                    }


                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * update Post Submit Date
         */
        case 'post-edit-update-date':
            $token = $antiXSS->xss_clean($json['token']);
            $id = (int)$antiXSS->xss_clean($json['id']);

            if ($id > 0) {
                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Post::updatePostSubmitDate($id);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("post_edit_id_" . $id, "post");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;


        /**
         * Edit Post
         * @author tjavan
         */
        case 'post-delete':
            $id = (int)$antiXSS->xss_clean($json['id']);
            $token = $antiXSS->xss_clean($json['token']);

            if (strlen($id) > 0 && $id > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {


                    $res = Post::getPostByID($id);
                    $name = "";
                    if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
                        $name = $res->response[0]->post_title;
                    }


                    $result = Post::deletePost($id);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("delete_post_id_" . $name, "post");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }
            break;


        /**
         * Add City And Customs
         * @author tjavan
         */
        case 'city-add':

            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $country = (int)$antiXSS->xss_clean($json['country']);
            $status_ground = (bool)$antiXSS->xss_clean($json['status_ground']);
            $status_ship = (bool)$antiXSS->xss_clean($json['status_ship']);
            $status_air = (bool)$antiXSS->xss_clean($json['status_air']);
            $status_railroad = (bool)$antiXSS->xss_clean($json['status_railroad']);
            $status_inventory = (bool)$antiXSS->xss_clean($json['status_inventory']);
            $status_poster = (bool)$antiXSS->xss_clean($json['status_poster']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            $token = $antiXSS->xss_clean($json['token']);
            $InternationalName = $antiXSS->xss_clean($json['InternationalName']);


            $status_ground = ($status_ground) ? "yes" : 'no';
            $status_ship = ($status_ship) ? "yes" : 'no';
            $status_air = ($status_air) ? "yes" : 'no';
            $status_railroad = ($status_railroad) ? "yes" : 'no';
            $status_inventory = ($status_inventory) ? "yes" : 'no';
            $status_poster = ($status_poster) ? "yes" : 'no';

            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 2) {
                    $flag = false;
                }
            }

            if ($flag == true && $country > 0) {


                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Location::setNewCity($title, $country, $status_ground, $status_air, $status_ship, $status_railroad, $status_inventory, $priority, $InternationalName, $status_poster);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("city_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit City And Customs
         * @author tjavan
         */
        case 'city-edit':
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $country = (int)$antiXSS->xss_clean($json['country']);
            $status_ground = (bool)$antiXSS->xss_clean($json['status_ground']);
            $status_ship = (bool)$antiXSS->xss_clean($json['status_ship']);
            $status_air = (bool)$antiXSS->xss_clean($json['status_air']);
            $status_railroad = (bool)$antiXSS->xss_clean($json['status_railroad']);
            $status_inventory = (bool)$antiXSS->xss_clean($json['status_inventory']);
            $status_poster = (bool)$antiXSS->xss_clean($json['status_poster']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            $token = $antiXSS->xss_clean($json['token']);
            $InternationalName = $antiXSS->xss_clean($json['InternationalName']);
            $long = $antiXSS->xss_clean($json['long']);
            $lat = $antiXSS->xss_clean($json['lat']);

            $status_ground = ($status_ground) ? "yes" : 'no';
            $status_ship = ($status_ship) ? "yes" : 'no';
            $status_air = ($status_air) ? "yes" : 'no';
            $status_railroad = ($status_railroad) ? "yes" : 'no';
            $status_inventory = ($status_inventory) ? "yes" : 'no';
            $status_poster = ($status_poster) ? "yes" : 'no';


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 2) {
                    $flag = false;
                }
            }


            if ($flag == true && $id != 0 && strlen($id) > 0 && $country > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Location::editCityById($id, $title, $country, $status_ground, $status_air,
                        $status_ship, $status_railroad, $status_inventory, $priority,
                        $InternationalName, $lat, $long, $status_poster);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("city_edit_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * delete city
         * @author tjavan
         */
        case 'city-delete':
            $cityID = (int)$antiXSS->xss_clean($json['cityID']);
            $token = $antiXSS->xss_clean($json['token']);
            $name = $antiXSS->xss_clean($json['name']);

            if ($cityID > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {


                    $result = Location::deleteCity($cityID);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("admin_city_delete_" . $name . "---" . $cityID);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }
            break;


        /**
         * Edit Car Status
         */
        case 'car-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "accepted",
                "pending",
                "rejected",
                "deleted",
            ];

            if ($id != 0 && strlen($id) > 0 && in_array($status, $lists)) {
                $result_token = Security::verifyCSRF('admin-car-edit', $token);
                if ($result_token) {

                    $result = Car::editCarById($id, $status);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("car_edit_title_" . $status . "_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }


            break;


        /**
         * Show Notification In Modal
         */
        case 'notification-show':
            $id = (int)$antiXSS->xss_clean($json['id']);
            $token = $antiXSS->xss_clean($json['token']);


            $myObj = new stdClass();
            $myObj->status = 0;
            $myJSON = json_encode($myObj);

            if ($id > 0 && $token) {
                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Notification::getNotificationById($id);
                    if ($result->status == 200 && !empty($result->response) && !empty($result->response[0])) {

                        global $lang;
                        $title = explode('--', $result->response[0]->notification_title);
                        $text = explode('--', $result->response[0]->notification_message);
                        if (array_key_exists($title[0], $lang) && array_key_exists($text[0], $lang)) {
                            $translateTitle = $lang[$title[0]];
                            $translateText = $lang[$text[0]];
                            for ($index = 1; $index < count($text); $index++) {
                                $translateText = str_replace("#PARAM{$index}#", $text[$index], $translateText);
                            }
                            for ($index = 1; $index < count($title); $index++) {
                                $translateTitle = str_replace("#PARAM{$index}#", $title[$index], $translateTitle);
                            }
                            $text = $translateText;
                            $title = $translateTitle;
                        } else {
                            $title = $result->response[0]->notification_title;
                            $text = $result->response[0]->notification_message;
                        }


                        $myObj = new stdClass();
                        $myObj->status = 200;
                        $myObj->title = $title;
                        $myObj->message = $text;
                        $myObj->time = Utils::getTimeCountry('Y/m/d  H:i', $result->response[0]->notification_time);
                        $myObj->sender = $result->response[0]->notification_sender;
                        $myJSON = json_encode($myObj);
                        echo $myJSON;
                    } else {
                        echo $myJSON;
                    }
                } else {
                    $myObj = new stdClass();
                    $myObj->status = -2;
                    $myJSON = json_encode($myObj);
                    echo $myJSON;
                }

            } else {
                echo $myJSON;
            }

            break;


        /**
         * Insert Notification
         */
        case 'notification-add':
            $id = (int)$antiXSS->xss_clean($json['id']);
            $title = strip_tags($antiXSS->xss_clean($json['title']));
            $sender = strip_tags($antiXSS->xss_clean($json['sender']));
            $text = $antiXSS->xss_clean($json['text']);
            $token = $antiXSS->xss_clean($json['token']);

            if (strlen($title) > 2 && strlen($sender) && strlen($text) && $id > 0) {
                $token = $antiXSS->xss_clean($json['token']);

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Notification::sendNotification($id, $title, $sender, $text);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("send_notification_to_user_" . $id, "notification_add");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Change User Status
         */
        case 'change-businessman-status':
            $status = $antiXSS->xss_clean($json['status']);
            $userID = $antiXSS->xss_clean($json['userID']);
            $token = $antiXSS->xss_clean($json['token']);

            $list_status = [
                "inactive",
                "active",
                "suspend",
                "guest",
            ];
            if (in_array($status, $list_status) && strlen($userID) > 0) {


                $result_token = Security::verifyCSRF('admin-businessman-info', $token);
                if ($result_token) {

                    $result = AUser::updateUserStatus($userID, $status);

                    if ($result->status == 200) {
                        echo "successful";

                        Admin::SetAdminLog("change_businessman_status_to_" . $status . "_" . $userID, "change_businessman_status");
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


        /**
         * Change User Status
         */
        case 'change-status':
            $status = $antiXSS->xss_clean($json['status']);
            $userID = $antiXSS->xss_clean($json['userID']);
            $token = $antiXSS->xss_clean($json['token']);

            $list_status = [
                "inactive",
                "active",
                "suspend",
                "guest",
            ];
            if (in_array($status, $list_status) && strlen($userID) > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {
                    $result = AUser::updateUserStatus($userID, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_user_status_to_" . $status . "_" . $userID);
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


        case 'change-user-class':
            $status = $antiXSS->xss_clean($json['status']);
            $userID = $antiXSS->xss_clean($json['userID']);
            $token = $antiXSS->xss_clean($json['token']);

            if (strlen($userID) > 0) {
                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {
                    $result = AUser::updateUserClass($userID, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        if ($status == "own") {
                            Admin::SetAdminLog("a_change_user_type_own_" . $userID);
                        } else {
                            Admin::SetAdminLog("a_change_user_type_null_" . $userID);
                        }
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


        /**
         * delete user session
         */
        case 'delete-user-session':
            $userId = $antiXSS->xss_clean($json['userId']);
            $number = $antiXSS->xss_clean($json['number']);
            $expire = $antiXSS->xss_clean($json['expire']);
            $token = $antiXSS->xss_clean($json['token']);

            if (strlen($userId) > 0 && strlen($number) > 0 && strlen($expire) > 0) {
                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = AUser::deleteUserSession($userId, $number, $expire);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog('delete_user_session_id_' . $userId, "user_info");
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


        /**
         * Change Cargo Info By X-table
         */
        case 'cargo-info' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $cargoID = (int)$antiXSS->xss_clean($json['cargoID']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "cargo_name_fa_IR",
                "cargo_name_en_US",
                "cargo_name_tr_Tr",
                "cargo_name_ru_RU",
                "category_id",
                "type_id",
                "cargo_weight",
                "cargo_volume",
                "cargo_description_fa_IR",
                "cargo_description_en_US",
                "cargo_description_tr_Tr",
                "cargo_description_ru_RU",
                "cargo_status",
                "cargo_recommended_price",
                "cargo_monetary_unit",
                "cargo_start_date",
                "cargo_green",
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $cargoID > 0) {

                $result_token = Security::verifyCSRF('admin-cargo-info', $token);
                if ($result_token) {
                    @Cargo::updateCargoOptionsById($cargoID, $type, $newValue);
                    $result = Cargo::editCargoInfoByAdmin($cargoID, $type, $newValue);
                    if ($result->status == 200) {
                        echo "successful " . Security::initCSRF('admin-cargo-info');
                        Admin::SetAdminLog("change_cargo_out_info_" . $cargoID, "change_cargo_info");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }


            break;


        case 'cargo-in-info' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $cargoID = (int)$antiXSS->xss_clean($json['cargoID']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "cargo_name_fa_IR",
                "cargo_name_en_US",
                "cargo_name_tr_Tr",
                "cargo_name_ru_RU",
                "category_id",
                "type_id",
                "cargo_weight",
                "cargo_volume",
                "cargo_description_fa_IR",
                "cargo_description_en_US",
                "cargo_description_tr_Tr",
                "cargo_description_ru_RU",
                "cargo_status",
                "cargo_recommended_price",
                "cargo_monetary_unit",
                "cargo_start_date",
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $cargoID > 0) {

                $result_token = Security::verifyCSRF('admin-cargo-in-info', $token);
                if ($result_token) {
                    @Cargo::updateCargoInOptionsById($cargoID, $type, $newValue);
                    $result = Cargo::editCargoInInfoByAdmin($cargoID, $type, $newValue);
                    if ($result->status == 200) {
                        echo "successful " . Security::initCSRF('admin-cargo-in-info');
                        Admin::SetAdminLog("change_cargo_in_info_" . $cargoID, "change_cargo_in_info");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }
            break;

        /**
         * Change Cargo Status
         */
        case 'change-cargo-status' :
            $status = $antiXSS->xss_clean($json['status']);
            $cargoID = (int)$antiXSS->xss_clean($json['CargoID']);
            $token = $antiXSS->xss_clean($json['token']);
            $lists = [
                "accepted",
                "rejected",
            ];

            if (in_array($status, $lists) && strlen($status) > 0 && $cargoID > 0) {
                $result_token = Security::verifyCSRF('admin-cargo-info', $token);
                if ($result_token) {
                    @Cargo::updateCargoOptionsById($cargoID, 'cargo_status', $status);
                    $result = Cargo::editCargoInfoByAdmin($cargoID, 'cargo_status', $status);
                    if ($result->status == 200) {
                        echo "successful " . Security::initCSRF('admin-cargo-info');
                        Admin::SetAdminLog("change_cargo_status_to_" . $status . "_" . $cargoID, "change_cargo_info");
                        Ring::sendRingNotic($cargoID);
                    } else {
                        echo "error " . Security::initCSRF('admin-cargo-info');
                    }
                } else {
                    echo "token_error";
                }
            }


            break;

        case 'change-cargo-in-status' :
            $status = $antiXSS->xss_clean($json['status']);
            $cargoID = (int)$antiXSS->xss_clean($json['CargoID']);
            $token = $antiXSS->xss_clean($json['token']);
            $lists = [
                "accepted",
                "rejected",
            ];

            if (in_array($status, $lists) && strlen($status) > 0 && $cargoID > 0) {

                $result_token = Security::verifyCSRF('admin-cargo-in-info', $token);
                if ($result_token) {
                    @Cargo::updateCargoInOptionsById($cargoID, 'cargo_status', $status);
                    $result = Cargo::editCargoInInfoByAdmin($cargoID, 'cargo_status', $status);
                    if ($result->status == 200) {
                        echo "successful " . Security::initCSRF('admin-cargo-in-info');
                        Admin::SetAdminLog("change_cargo_in_status_to_" . $status . "_" . $cargoID, "change_cargo_info");
                    } else {
                        echo "error " . Security::initCSRF('admin-cargo-in-info');
                    }
                } else {
                    echo "token_error";
                }
            }


            break;

        /**
         * Get Request Images
         */
        case 'get-request-image':
            $token = $antiXSS->xss_clean($json['token']);
            $id = (int)$antiXSS->xss_clean($json['id']);

            if ($id > 0 && strlen($token) > 5) {

                $result_token = Security::verifyCSRF('admin-cargo-info', $token);
                if ($result_token) {
                    $result = Cargo::getRequestImage($id);

                    if ($result->status == 200) {
                        $temp = [];
                        if (isset($result->response[0]) && !empty($result->response[0]) && isset($result->response[0]->images) && !empty($result->response[0]->images)) {
                            json_decode($result->response[0]->images);
                            foreach (json_decode($result->response[0]->images) as $image) {
                                array_push($temp, Utils::fileExist($image, BOX_EMPTY));
                            }
                        }
//                        echo "successful " . Security::initCSRF('admin-cargo-info');
                        print_r(json_encode(["status" => 200, "images" => $temp, "token" => Security::initCSRF('admin-cargo-info')]));
//
                    } else {
                        print_r(json_encode(["status" => 0, "token" => Security::initCSRF('admin-cargo-info')]));
                    }
                } else {
                    echo "token_error";
                }
            }


            break;

        case 'get-request-in-image':
            $token = $antiXSS->xss_clean($json['token']);
            $id = (int)$antiXSS->xss_clean($json['id']);

            if ($id > 0 && strlen($token) > 5) {

                $result_token = Security::verifyCSRF('admin-cargo-in-info', $token);
                if ($result_token) {
                    $result = Cargo::getRequestInImage($id);

                    if ($result->status == 200) {
                        $temp = [];
                        if (isset($result->response[0]) && !empty($result->response[0]) && isset($result->response[0]->images) && !empty($result->response[0]->images)) {
                            json_decode($result->response[0]->images);
                            foreach (json_decode($result->response[0]->images) as $image) {
                                array_push($temp, Utils::fileExist($image, BOX_EMPTY));
                            }
                        }
//                        echo "successful " . Security::initCSRF('admin-cargo-info');
                        print_r(json_encode(["status" => 200, "images" => $temp, "token" => Security::initCSRF('admin-cargo-in-info')]));
//
                    } else {
                        print_r(json_encode(["status" => 0, "token" => Security::initCSRF('admin-cargo-in-info')]));
                    }
                } else {
                    echo "token_error";
                }
            }
            break;

        /**
         * Change Request Status
         */
        case 'change-request-status':
            $token = $antiXSS->xss_clean($json['token']);
            $status = $antiXSS->xss_clean($json['status']);
            $requestId = (int)$antiXSS->xss_clean($json['requestId']);

            $lists = [
                "pending",
                "rejected",
                "accepted",
            ];


            if ($requestId > 0 && in_array($status, $lists) && strlen($token) > 5) {

                $result_token = Security::verifyCSRF('admin-cargo-info', $token);
                if ($result_token) {
                    @Cargo::updateCargoOptionsRequestById($requestId, $status);
                    $result = Cargo::updateRequestStatus($requestId, $status);
                    if ($result->status == 200) {
                        echo "successful " . Security::initCSRF('admin-cargo-info');

                        Admin::SetAdminLog("change_request_status_to_" . $status . "_" . $requestId, "change_request_status");
                    } else {
                        echo "error";
                    }

                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;

        case 'change-request-in-status':
            $token = $antiXSS->xss_clean($json['token']);
            $status = $antiXSS->xss_clean($json['status']);
            $requestId = (int)$antiXSS->xss_clean($json['requestId']);
            $lists = [
                "pending",
                "rejected",
                "accepted",
            ];

            if ($requestId > 0 && in_array($status, $lists) && strlen($token) > 5) {

                $result_token = Security::verifyCSRF('admin-cargo-in-info', $token);
                if ($result_token) {
                    @Cargo::updateCargoInOptionsRequestById($requestId, $status);
                    $result = Cargo::updateRequestInStatus($requestId, $status);
                    if ($result->status == 200) {
                        echo "successful " . Security::initCSRF('admin-cargo-in-info');

                        Admin::SetAdminLog("change_request_in_status_to_" . $status . "_" . $requestId, "change_request_status");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;

        /**
         * Change Extra Expenses
         */
        case 'change-extra-expenses-status':
            $token = $antiXSS->xss_clean($json['token']);
            $status = $antiXSS->xss_clean($json['status']);
            $idExtra = (int)$antiXSS->xss_clean($json['idExtra']);

            $lists = [
                "pending",
                "rejected",
                "accepted",
                "canceled",
            ];


            if ($idExtra > 0 && in_array($status, $lists) && strlen($token) > 5) {

                $result_token = Security::verifyCSRF('admin-cargo-info', $token);
                if ($result_token) {

                    @Cargo::updateCargoOptionsExtraExpensesById($idExtra, $status);
                    $result = Cargo::updateExtraExpensesStatus($idExtra, $status);
                    if ($result->status == 200) {
                        echo "successful " . Security::initCSRF('admin-cargo-info');

                        Admin::SetAdminLog("change_extra_status_to_" . $status . "_" . $idExtra, "change_extra_status");
                    } else {
                        echo "error";
                    }

                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;

        case 'change-extra-expenses-in-status':
            $token = $antiXSS->xss_clean($json['token']);
            $status = $antiXSS->xss_clean($json['status']);
            $idExtra = (int)$antiXSS->xss_clean($json['idExtra']);
            $lists = [
                "pending",
                "rejected",
                "accepted",
                "canceled",
            ];

            if ($idExtra > 0 && in_array($status, $lists) && strlen($token) > 5) {
                $result_token = Security::verifyCSRF('admin-cargo-in-info', $token);
                if ($result_token) {
                    @Cargo::updateCargoInOptionsExtraExpensesById($idExtra, $status);
                    $result = Cargo::updateExtraExpensesInStatus($idExtra, $status);
                    if ($result->status == 200) {
                        echo "successful " . Security::initCSRF('admin-cargo-in-info');

                        Admin::SetAdminLog("change_extra_in_status_to_" . $status . "_" . $idExtra, "change_extra_status");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;

        /**
         *  Add Or Low Score USer
         */
        case 'change-user-score':
            $userId = (int)$antiXSS->xss_clean($json['userId']);
            $count = (int)$antiXSS->xss_clean($json['count']);
            $action = $antiXSS->xss_clean($json['actions']);
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);
            $lists = [
                "add",
                "low",
            ];

            if ($userId > 0 && is_numeric($count) && in_array($action, $lists) && strlen($token) > 10) {

                $temp = "add_score_count_" . $count;
                if ($action != "add") {
                    $count = -$count;
                    $temp = "low_score_count_" . $count;
                }

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = AUser::updateUserScoreValue($userId, $count);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_user_score_" . $count . "---" . $userId);
                        User::createUserLog($userId, $temp, "score");
                    } else {
                        echo "error";
                    }

                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;


        /**
         *  Add Or Low Gift USer
         */
        case 'change-user-gift':
            $userId = (int)$antiXSS->xss_clean($json['userId']);
            $count = (int)$antiXSS->xss_clean($json['count']);
            $action = $antiXSS->xss_clean($json['actions']);
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "add",
                "low",
            ];

            if ($userId > 0 && is_numeric($count) && in_array($action, $lists) && strlen($token) > 10) {

                $temp = "add_gift_count_" . $count;
                if ($action != "add") {
                    $count = -$count;
                    $temp = "low_gift_count_" . $count;
                }

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = AUser::updateUserGiftValue($userId, $count);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_user_gift_" . $count . "---" . $userId);
                        User::createUserLog($userId, $temp, "gift");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;


        /**
         * Change User Card Bank Status
         */
        case 'change-card-bank-status':
            $creditID = (int)$antiXSS->xss_clean($json['creditID']);
            $status = $antiXSS->xss_clean($json['status']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = ["accepted", "rejected", "pending", "deleted"];

            if (is_numeric($creditID) && $creditID > 0 && in_array($status, $lists) && strlen($token) > 10) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = AUser::updateCreditByID($creditID, $status);

                    if ($result->status == 200) {
                        echo "successful";

                        Admin::SetAdminLog("change_card_bank_status_title_" . $status . "_" . $creditID, "card_banks");

                    } else {
                        echo "error";
                    }

                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Delete User Card Bank
         */
        case 'delete-card-bank':
            $creditID = (int)$antiXSS->xss_clean($json['creditID']);
            $token = $antiXSS->xss_clean($json['token']);

            if (is_numeric($creditID) && $creditID > 0 && strlen($token) > 10) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = AUser::deleteCreditByID($creditID);

                    if ($result->status == 200) {
                        echo "successful";

                        Admin::SetAdminLog("delete_card_bank", "card_banks");

                    } else {
                        echo "error";
                    }

                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Change Transaction Info By X-table
         */
        case 'transaction-info' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $transactionID = (int)$antiXSS->xss_clean($json['transactionID']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "transaction_authority",
                "transaction_tracking_code",
                "transaction_gateway",
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $transactionID > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {
                    $result = Transactions::editTransactionInfoByAdmin($transactionID, $type, $newValue);
//                    print_r($result);
                    if ($result->status == 200) {
                        echo "successful " . Security::initCSRF2();
                        Admin::SetAdminLog("change_transaction_info_id_" . $transactionID, "transaction_info");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }


            break;


        /**
         * Change transaction Status
         */
        case 'change-transaction-status':
            $status = $antiXSS->xss_clean($json['status']);
            $transactionID = (int)$antiXSS->xss_clean($json['transactionID']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = ["completed", "rejected", "paid", "rejected_deposit"];
            if (in_array($status, $lists) && $transactionID > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {
                    $result = Transactions::editTransactionStatusByAdmin($transactionID, 'transaction_status', $status);
                    if ($result->status == 200) {
                        echo "successful " . Security::initCSRF2();
//                        Admin::SetAdminLog("change_transaction_status_".$status."_".$transactionID);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }


            break;


        /**
         * Check Complaint New
         */
        case 'complaint-new':
            $result = Complaint::getAllComplaintsWhitCargo('pending');
            $Data = 0;
            if ($result->status == 200 && !empty($result->response)) {
                $Data = count($result->response);
                if ($Data > 0) {
                    echo "show " . $Data;
                } else {
                    echo "hide";
                }
            } else {
                echo "hide";
            }

            break;


        /**
         * Change Complaint Status To accepted
         */
        case 'complaint-set-admin':
            $token = $antiXSS->xss_clean($json['token']);
            $complaint = (int)$antiXSS->xss_clean($json['complaint']);

            if ($complaint > 0 && $token) {
                $result_token = Security::verifyCSRF('admin-complaint-info', $token);
                if ($result_token) {
                    $result = Complaint::setAdminFromNewComplaint($complaint);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_answer_complaint_out_now_answer_id_", "answer_complaint");
                    } elseif ($result->status == 225) {
                        echo "before_set";
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;


        case 'complaint-in-set-admin':
            $token = $antiXSS->xss_clean($json['token']);
            $complaint = (int)$antiXSS->xss_clean($json['complaint']);

            if ($complaint > 0 && $token) {
                $result_token = Security::verifyCSRF('admin-complaint-in-info', $token);
                if ($result_token) {
                    $result = Complaint::setAdminFromNewComplaintIn($complaint);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_answer_complaint_in_now_answer_id_" . $complaint, "answer_complaint");
                    } elseif ($result->status == 225) {
                        echo "before_set";
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;


        /**
         * Change Complaint Status To closed
         */
        case 'complaint-set-closed':
            $token = $antiXSS->xss_clean($json['token']);
            $desc = $antiXSS->xss_clean($json['desc']);
            $complaint = (int)$antiXSS->xss_clean($json['complaint']);

            if ($complaint > 0 && $token && strlen($desc) > 2) {
                $result_token = Security::verifyCSRF('admin-complaint-info', $token);
                if ($result_token) {
                    $result = Complaint::closedComplaint($complaint, $desc);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_answer_complaint_out_closed_id_" . $complaint, "answer_complaint_closed");
                    } elseif ($result->status == 225) {
                        echo "before_set";
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;


        case 'complaint-in-set-closed':
            $token = $antiXSS->xss_clean($json['token']);
            $desc = $antiXSS->xss_clean($json['desc']);
            $complaint = (int)$antiXSS->xss_clean($json['complaint']);

            if ($complaint > 0 && $token && strlen($desc) > 2) {
                $result_token = Security::verifyCSRF('admin-complaint-in-info', $token);
                if ($result_token) {
                    $result = Complaint::closedComplaintIn($complaint, $desc);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_answer_complaint_in_closed_id_" . $complaint, "answer_complaint_closed");
                    } elseif ($result->status == 225) {
                        echo "before_set";
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;

        /**
         * Edit User Balance
         */
        case 'user-balance-edit':
            $balance = (int)$antiXSS->xss_clean($json['balance']);
            $balanceValue = (int)$antiXSS->xss_clean($json['balanceValue']);
            $balanceFrozen = (int)$antiXSS->xss_clean($json['balanceFrozen']);
            $token = $antiXSS->xss_clean($json['token']);

            if (strlen($balance) >= 0 && $balance >= 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = AUser::editUserBalanceByAdmin($balance, $balanceValue, $balanceFrozen);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_user_balance_id_" . $balance, "change_balance");
                    } else {
                        echo "error";
                    }

                } else {
                    echo "token_error";
                }
            }

            break;


        /**
         * Add New Currency From USer
         */
        case 'user-balance-add':

            $userID = (int)$antiXSS->xss_clean($json['userID']);
            $userType = $antiXSS->xss_clean($json['userType']);
            $balanceValue = (int)$antiXSS->xss_clean($json['balanceValue']);
            $balanceFrozen = (int)$antiXSS->xss_clean($json['balanceFrozen']);
            $currencyType = (int)$antiXSS->xss_clean($json['currencyType']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = ["businessman", "drivers"];


            if (in_array($userType, $lists) && strlen($userID) > 0 && $userID > 0 && $currencyType > 0) {

                $result_token = Security::verifyCSRF('admin-user-currency-add', $token);
                if ($result_token) {

                    $result = AUser::addUserBalanceByAdmin($userID, $currencyType, $balanceValue, $balanceFrozen);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("add_new_balance", "change_balance");
                    } elseif ($result->status == 220) {
                        echo "exist-currency " . Security::initCSRF('admin-user-currency-add');
                    } else {
                        echo "error";
                    }

                } else {
                    echo "token_error";
                }
            }

            break;


        /**
         * Change settings_fa_IR.php values
         */
        case 'site-fa-ir':
            $values = ($json['values']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF('admin-settings-site', $token);
            if ($result_token) {

                $file_fa_IR = SITE_ROOT . "/settings/settings_fa_IR.php";

                $temp = file_put_contents($file_fa_IR, $values);
                if ($temp) {
                    echo "successful " . Security::initCSRF('admin-settings-site');
                    Admin::SetAdminLog("change_settings_site_fa_IR");
                } else {
                    echo "error " . Security::initCSRF('admin-settings-site');;
                }
            } else {
                echo "token_error";
            }


            break;


        /**
         * Change settings_en_US.php values
         */
        case 'site-en-US':
            $values = ($json['values']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF('admin-settings-site', $token);
            if ($result_token) {

                $file_en_US = SITE_ROOT . "/settings/settings_en_US.php";

                $temp = file_put_contents($file_en_US, $values);
                if ($temp) {
                    echo "successful " . Security::initCSRF('admin-settings-site');
                    Admin::SetAdminLog("change_settings_site_en_US");
                } else {
                    echo "error " . Security::initCSRF('admin-settings-site');;
                }
            } else {
                echo "token_error";
            }


            break;


        /**
         * Change settings_tr_Tr.php values
         */
        case 'site-tr-Tr':
            $values = ($json['values']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF('admin-settings-site', $token);
            if ($result_token) {

                $file_tr_Tr = SITE_ROOT . "/settings/settings_tr_Tr.php";

                $temp = file_put_contents($file_tr_Tr, $values);
                if ($temp) {
                    echo "successful " . Security::initCSRF('admin-settings-site');
                    Admin::SetAdminLog("change_settings_site_tr_Tr");
                } else {
                    echo "error " . Security::initCSRF('admin-settings-site');;
                }
            } else {
                echo "token_error";
            }


            break;


        /**
         * Change settings_ru_RU.php values
         */
        case 'site-ru-RU':
            $values = ($json['values']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF('admin-settings-site', $token);
            if ($result_token) {

                $file_ru_RU = SITE_ROOT . "/settings/settings_ru_RU.php";

                $temp = file_put_contents($file_ru_RU, $values);
                if ($temp) {
                    echo "successful " . Security::initCSRF('admin-settings-site');
                    Admin::SetAdminLog("change_settings_site_ru_RU");
                } else {
                    echo "error " . Security::initCSRF('admin-settings-site');;
                }
            } else {
                echo "token_error";
            }


            break;

        /**
         * Change fa_IR.php values
         */
        case 'language-fa-ir':
            $values = ($json['values']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF('admin-settings-languages', $token);
            if ($result_token) {

                $file_fa_IR = SITE_ROOT . "/languages/fa_IR.php";

                $temp = file_put_contents($file_fa_IR, $values);
                if ($temp) {
                    echo "successful " . Security::initCSRF('admin-settings-languages');
                    Admin::SetAdminLog("change_settings_languages_fa_IR");
                } else {
                    echo "error " . Security::initCSRF('admin-settings-languages');;
                }
            } else {
                echo "token_error";
            }


            break;

        /**
         * Change en-US.php values
         */
        case 'language-en-US':
            $values = ($json['values']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF('admin-settings-languages', $token);
            if ($result_token) {

                $file_en_US = SITE_ROOT . "/languages/en_US.php";

                $temp = file_put_contents($file_en_US, $values);
                if ($temp) {
                    echo "successful " . Security::initCSRF('admin-settings-languages');
                    Admin::SetAdminLog("change_settings_languages_en_US");
                } else {
                    echo "error " . Security::initCSRF('admin-settings-languages');;
                }
            } else {
                echo "token_error";
            }


            break;


        /**
         * Change tr_Tr.php values
         */
        case 'language-tr-Tr':
            $values = ($json['values']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF('admin-settings-languages', $token);
            if ($result_token) {

                $file_tr_Tr = SITE_ROOT . "/languages/tr_Tr.php";

                $temp = file_put_contents($file_tr_Tr, $values);
                if ($temp) {
                    echo "successful " . Security::initCSRF('admin-settings-languages');
                    Admin::SetAdminLog("change_settings_languages_tr_Tr");
                } else {
                    echo "error " . Security::initCSRF('admin-settings-languages');;
                }
            } else {
                echo "token_error";
            }


            break;


        /**
         * Change ru_RU.php values
         */
        case 'language-ru-RU':
            $values = ($json['values']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF('admin-settings-languages', $token);
            if ($result_token) {

                $file_ru_RU = SITE_ROOT . "/languages/ru_RU.php";

                $temp = file_put_contents($file_ru_RU, $values);
                if ($temp) {
                    echo "successful " . Security::initCSRF('admin-settings-languages');
                    Admin::SetAdminLog("change_settings_languages_ru_RU");
                } else {
                    echo "error " . Security::initCSRF('admin-settings-languages');;
                }
            } else {
                echo "token_error";
            }


            break;


        case 'settings-field-rate':
            $rate = (int)$antiXSS->xss_clean($json['rate']);
            $slug = $antiXSS->xss_clean($json['slug']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {

                $result1 = Utils::setFileText("settings.txt", $slug, $rate);

                if ($result1 == 200) {
                    echo "successful";
                    Admin::SetAdminLog("settings_change_field_rate_" . $slug . "==>" . $rate);
                } else {
                    echo "error";
                }


            } else {
                echo "token_error";
            }


            break;

        case 'settings-overall':
            $cargo_expire = (int)$antiXSS->xss_clean($json['cargo_expire']);
            $cargo_distance = (int)$antiXSS->xss_clean($json['cargo_distance']);
            $r_card_account = $antiXSS->xss_clean($json['r_card_account']);
            $r_card_iban = $antiXSS->xss_clean($json['r_card_iban']);
            $r_card_number = $antiXSS->xss_clean($json['r_card_number']);
            $r_card_number_name = $antiXSS->xss_clean($json['r_card_number_name']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {
                $result1 = Utils::setFileText("settings.txt", 'cargo_expire', $cargo_expire);
                $result2 = Utils::setFileText("settings.txt", 'cargo_distance', $cargo_distance);
                $result3 = Utils::setFileText("settings.txt", 'r_card_account', $r_card_account);
                $result4 = Utils::setFileText("settings.txt", 'r_card_iban', $r_card_iban);
                $result5 = Utils::setFileText("settings.txt", 'r_card_number', $r_card_number);
                $result6 = Utils::setFileText("settings.txt", 'r_card_number_name', $r_card_number_name);
                if ($result1 == 200 && $result2 == 200 && $result3 == 200 && $result4 == 200 && $result5 == 200 && $result6 == 200) {
                    echo "successful";
                    Admin::SetAdminLog("settings_change_cargo_expire");
                } else {
                    echo "error";
                }
            } else {
                echo "token_error";
            }
            break;


        case 'settings-seo':
            $seo_home = $antiXSS->xss_clean($json['seo_home']);
            $seo_user_laws = $antiXSS->xss_clean($json['seo_user_laws']);
            $seo_404 = $antiXSS->xss_clean($json['seo_404']);
            $seo_about_us = $antiXSS->xss_clean($json['seo_about_us']);
            $seo_contact_us = $antiXSS->xss_clean($json['seo_contact_us']);
            $seo_developer = $antiXSS->xss_clean($json['seo_developer']);
            $seo_user_faq = $antiXSS->xss_clean($json['seo_user_faq']);
            $seo_blog = $antiXSS->xss_clean($json['seo_blog']);
            $seo_robots = $json['seo_robots'];
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {
                $result1 = Utils::setFileText("settings.txt", 'seo_home', $seo_home);
                $result2 = Utils::setFileText("settings.txt", 'seo_user_laws', $seo_user_laws);
                $result3 = Utils::setFileText("settings.txt", 'seo_404', $seo_404);
                $result6 = Utils::setFileText("settings.txt", 'seo_about_us', $seo_about_us);
                $result7 = Utils::setFileText("settings.txt", 'seo_contact_us', $seo_contact_us);
                $result8 = Utils::setFileText("settings.txt", 'seo_developer', $seo_developer);
                $result9 = Utils::setFileText("settings.txt", 'seo_user_faq', $seo_user_faq);
                $result10 = Utils::setFileText("settings.txt", 'seo_blog', $seo_blog);
                $result11 = file_put_contents(SITE_ROOT . '/robots.txt', $seo_robots);

                if ($result1 == 200 && $result2 == 200 && $result3 == 200
                    && $result6 == 200 && $result7 == 200 && $result8 == 200 && $result9 == 200 && $result10 == 200) {
                    echo "successful";
                    Admin::SetAdminLog("settings_seo");
                } else {
                    echo "error";
                }
            } else {
                echo "token_error";
            }
            break;


        case 'settings-sitemap':
            $sitemap_all = $json['sitemap_all'];
            $sitemap_blog = $json['sitemap_blog'];
            $sitemap_academy = $json['sitemap_academy'];
            $sitemap_cargo_out = $json['sitemap_cargo_out'];
            $sitemap_cargo_in = $json['sitemap_cargo_in'];
            $sitemap_poster = $json['sitemap_poster'];
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {

                $result1 = file_put_contents(SITE_ROOT . '/sitemap.xml', $sitemap_all);
                $result2 = file_put_contents(SITE_ROOT . '/sitemap-blog.xml', $sitemap_blog);
                $result3 = file_put_contents(SITE_ROOT . '/sitemap-academy.xml', $sitemap_academy);
                $result4 = file_put_contents(SITE_ROOT . '/sitemap-cargo-out.xml', $sitemap_cargo_out);
                $result5 = file_put_contents(SITE_ROOT . '/sitemap-cargo-in.xml', $sitemap_cargo_in);
                $result6 = file_put_contents(SITE_ROOT . '/sitemap-poster.xml', $sitemap_poster);

                if (isset($result1) && isset($result2) && isset($result3) && isset($result4) && isset($result5) && isset($result6)) {
                    echo "successful";
                    Admin::SetAdminLog("a_settings_sitemap_update");
                } else {
                    echo "error";
                }
            } else {
                echo "token_error";
            }
            break;

        case 'settings-price-time':
            $poster_expire_time = (int)$antiXSS->xss_clean($json['poster_expire_time']);
            $poster_immediate_time = (int)$antiXSS->xss_clean($json['poster_immediate_time']);

            $poster_immediate_price_toman = (int)$antiXSS->xss_clean($json['poster_immediate_price_toman']);
            $poster_immediate_price_dollar = (int)$antiXSS->xss_clean($json['poster_immediate_price_dollar']);
            $poster_immediate_price_euro = (int)$antiXSS->xss_clean($json['poster_immediate_price_euro']);

            $poster_ladder_price_toman = (int)$antiXSS->xss_clean($json['poster_ladder_price_toman']);
            $poster_ladder_price_dollar = (int)$antiXSS->xss_clean($json['poster_ladder_price_dollar']);
            $poster_ladder_price_euro = (int)$antiXSS->xss_clean($json['poster_ladder_price_euro']);


            $poster_expert_time = (int)$antiXSS->xss_clean($json['poster_expert_time']);
            $poster_expert_price_toman = (int)$antiXSS->xss_clean($json['poster_expert_price_toman']);
            $poster_expert_price_dollar = (int)$antiXSS->xss_clean($json['poster_expert_price_dollar']);
            $poster_expert_price_euro = (int)$antiXSS->xss_clean($json['poster_expert_price_euro']);

            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF('admin-settings-poster', $token, false);
            if ($result_token) {
                $result1 = Utils::setFileText("settings.txt", 'poster_expire_time', $poster_expire_time);
                $result2 = Utils::setFileText("settings.txt", 'poster_immediate_time', $poster_immediate_time);
                $result3 = Utils::setFileText("settings.txt", 'poster_immediate_price_toman', $poster_immediate_price_toman);
                $result4 = Utils::setFileText("settings.txt", 'poster_immediate_price_dollar', $poster_immediate_price_dollar);
                $result5 = Utils::setFileText("settings.txt", 'poster_immediate_price_euro', $poster_immediate_price_euro);
                $result6 = Utils::setFileText("settings.txt", 'poster_ladder_price_toman', $poster_ladder_price_toman);
                $result7 = Utils::setFileText("settings.txt", 'poster_ladder_price_dollar', $poster_ladder_price_dollar);
                $result8 = Utils::setFileText("settings.txt", 'poster_ladder_price_euro', $poster_ladder_price_euro);

                $result9 = Utils::setFileText("settings.txt", 'poster_expert_time', $poster_expert_time);
                $result10 = Utils::setFileText("settings.txt", 'poster_expert_price_toman', $poster_expert_price_toman);
                $result11 = Utils::setFileText("settings.txt", 'poster_expert_price_dollar', $poster_expert_price_dollar);
                $result12 = Utils::setFileText("settings.txt", 'poster_expert_price_euro', $poster_expert_price_euro);

                if ($result1 == 200 && $result2 == 200 && $result3 == 200 && $result4 == 200 && $result5 == 200 && $result6 == 200
                    && $result7 == 200 && $result8 == 200 && $result9 == 200 && $result10 == 200 && $result11 == 200 && $result12 == 200) {
                    echo "successful";
                    Admin::SetAdminLog("a_update_settings_poster_price_time");
                } else {
                    echo "error";
                }
            } else {
                echo "token_error";
            }
            break;


        case 'settings-security-all':

            $set_admin_cargo_out = $antiXSS->xss_clean($json['set_admin_cargo_out']);
            $set_admin_cargo_in = $antiXSS->xss_clean($json['set_admin_cargo_in']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF('admin-settings-security', $token, false);
            if ($result_token) {
                $result1 = Utils::setFileText("settings.txt", 'set_admin_cargo_out', implode(',', $set_admin_cargo_out));
                $result2 = Utils::setFileText("settings.txt", 'set_admin_cargo_in', implode(',', $set_admin_cargo_in));
                if ($result1 == 200 && $result2 == 200) {
                    echo "successful";
                    Admin::SetAdminLog("settings_change_cargo_expire");
                } else {
                    echo "error";
                }
            } else {
                echo "token_error";
            }
            break;


        /**
         * Get Admin Census
         */
        case 'get-admin-census':
            $start = $antiXSS->xss_clean($json['start']);
            $end = $antiXSS->xss_clean($json['end']);
            $adminID = $antiXSS->xss_clean($json['adminID']);
            $token = $antiXSS->xss_clean($json['token']);

            $result_token = Security::verifyCSRF('admin-census-admin', $token);
            if ($result_token) {

                global $lang;
                $result = Admin::getAdminLogsByTime($adminID, $start, $end);
                if ($result->status == 200) {
                    $temp = $result->response;

                    $array = [];

                    if (!empty($temp)) {
                        foreach ($temp as $loop) {
                            if (isset($array[$loop->log_slug])) {
                                $array[$loop->log_slug] += 1;
                            } else {
                                $array[$loop->log_slug] = 1;
                            }
                        }
                    }

                    $temp = [];
                    if (!empty($array)) {
                        foreach ($array as $index => $loop) {
                            if (empty($index)) {
                                $temp[] = ['name' => $lang['other'], 'count' => $loop];
                            } else {
                                if (array_key_exists($index, $lang)) {
                                    $temp[] = ['name' => $lang[$index], 'count' => $loop];
                                } else {
                                    $temp[] = ['name' => $index, 'count' => $loop];
                                }

                            }

                        }
                    }


                    echo json_encode(['status' => 200, 'data' => $temp, 'token' => Security::initCSRF('admin-census-admin')]);
                } else {
                    echo json_encode(['status' => -1, 'token' => Security::initCSRF('admin-census-admin')]);
                }
            } else {
                echo json_encode(['status' => -100]);
            }
            break;


        /**
         * Add Country
         * @author tjavan
         */
        case 'country-add':
            $status_login = (bool)$antiXSS->xss_clean($json['status_login']);
            $status_poster = (bool)$antiXSS->xss_clean($json['status_poster']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $iso_language = $antiXSS->xss_clean($json['iso_language']);
            $iso_country_two_word = $antiXSS->xss_clean($json['iso_country_two_word']);
            $country_display_code = $antiXSS->xss_clean($json['country_display_code']);
            $country_code = $antiXSS->xss_clean($json['country_code']);
            $img = $antiXSS->xss_clean($json['img']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            $token = $antiXSS->xss_clean($json['token']);

            $status_login = ($status_login) ? "yes" : 'no';
            $status_poster = ($status_poster) ? "yes" : 'no';

            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag && strlen($iso_country_two_word) == 2 && strlen($iso_language) > 4 && $img && strlen($img) > 5 && strlen($country_display_code) > 1 && strlen($country_code) > 2) {


                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $resultImage = Upload::uploadBase64($img, Flag_IMAGE);

                    $ImageURL = BOX_EMPTY;
                    if ($resultImage->status == 200 && !empty($resultImage->response)) {
                        $ImageURL = $resultImage->response;
                    }


                    $result = Location::setNewCountry($title, $iso_country_two_word, $iso_language, $country_display_code, $country_code, $status_login, $priority, $ImageURL, $status_poster);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("country_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit Country
         * @author tjavan
         */
        case 'country-edit':
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);
            $iso_language = $antiXSS->xss_clean($json['iso_language']);
            $iso_country_two_word = $antiXSS->xss_clean($json['iso_country_two_word']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            $status_login = (bool)$antiXSS->xss_clean($json['status_login']);
            $status_poster = (bool)$antiXSS->xss_clean($json['status_poster']);
            $country_display_code = $antiXSS->xss_clean($json['country_display_code']);
            $country_code = $antiXSS->xss_clean($json['country_code']);
            $img = $antiXSS->xss_clean($json['img']);


            $status_login = ($status_login) ? "yes" : 'no';
            $status_poster = ($status_poster) ? "yes" : 'no';


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag && $id != 0 && strlen($id) > 0 && strlen($iso_country_two_word) == 2 && strlen($iso_language) > 4 && strlen($country_display_code) > 1 && strlen($country_code) > 2) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $ImageURL = null;

                    if (!empty($img)) {
                        $resultImage = Upload::uploadBase64($img, Flag_IMAGE);
                        if ($resultImage->status == 200 && !empty($resultImage->response)) {
                            $ImageURL = $resultImage->response;
                        }
                    }

                    $result = Location::editCountryById($id, $title, $iso_country_two_word, $iso_language, $country_display_code, $country_code, $status_login, $priority, $ImageURL, $status_poster);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("country_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * get city by country id From ground
         */
        case 'get-city-by-country-id':
            $id = (int)$json['id'];
            $token = $antiXSS->xss_clean($json['token']);

            $language = ($_COOKIE['language']) ? $_COOKIE['language'] : "fa_ir";

            $arrayTemp = [];
            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {
                $result = Location::getAllCitiesByCountryId($id, 'ground');
                if ($result->status == 200) {

                    if (!empty($result->response)) {
                        foreach ($result->response as $loop) {
                            $array = [];
                            $array['text'] = (!empty(array_column(json_decode($loop->city_name, true), 'value', 'slug')[$language])) ?
                                array_column(json_decode($loop->city_name, true), 'value', 'slug')[$language] : "";
                            $array['id'] = $loop->city_id;
                            $arrayTemp[] = $array;
                        }
                    }
                    print_r(json_encode($arrayTemp));
                } else {
                    echo json_encode($arrayTemp);
                }

            } else {
                echo json_encode($arrayTemp);
            }
            break;


        /**
         * Add New Customs
         */
        case 'customs-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag && $city > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Ground::setNewCustoms($title, $city, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("customs_add_title_" . $name, "category_xxx");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit customs
         * @author tjavan
         */
        case 'customs-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag == true && $id != 0 && strlen($id) > 0 && $city > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Ground::editCustomsById($id, $title, $city, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("customs_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;

        /**
         * delete customs
         * @author tjavan
         */
        case 'customs-delete':
            $customsID = (int)$antiXSS->xss_clean($json['customsID']);
            $token = $antiXSS->xss_clean($json['token']);
            $name = $antiXSS->xss_clean($json['name']);

            if ($customsID > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {


                    $result = Ground::deleteCustoms($customsID);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("admin_customsID_delete_" . $name . "---" . $customsID);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }
            break;

        /**
         * get city by country id From ship
         */
        case 'get-city-port-by-country-id':
            $id = (int)$json['id'];
            $token = $antiXSS->xss_clean($json['token']);

            $language = ($_COOKIE['language']) ? $_COOKIE['language'] : "fa_ir";

            $arrayTemp = [];
            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {
                $result = Location::getAllCitiesByCountryId($id, 'ship');
                if ($result->status == 200) {

                    if (!empty($result->response)) {
                        foreach ($result->response as $loop) {
                            $array = [];
                            $array['text'] = (!empty(array_column(json_decode($loop->city_name, true), 'value', 'slug')[$language])) ?
                                array_column(json_decode($loop->city_name, true), 'value', 'slug')[$language] : "";
                            $array['id'] = $loop->city_id;
                            $arrayTemp[] = $array;
                        }
                    }
                    print_r(json_encode($arrayTemp));
                } else {
                    echo json_encode($arrayTemp);
                }

            } else {
                echo json_encode($arrayTemp);
            }
            break;


        /**
         * Add New port
         * @author tjavan
         */
        case 'port-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            if (@json_decode($title)) {
                $rrr = json_decode($title);
                foreach ($rrr as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                        $flag = false;
                    }
                }
            }


            if ($flag && $city > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Ship::setNewPort($title, $city, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("port_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit port
         * @author tjavan
         */
        case 'port-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag && $id != 0 && strlen($id) > 0 && $city > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Ship::editPortById($id, $title, $city, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("port_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;

        /**
         * delete port
         * @author tjavan
         */
        case 'port-delete':
            $portID = (int)$antiXSS->xss_clean($json['portID']);
            $token = $antiXSS->xss_clean($json['token']);
            $name = $antiXSS->xss_clean($json['name']);

            if ($portID > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {


                    $result = Ship::deletePort($portID);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("admin_port_delete_" . $name . "---" . $portID);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }
            break;

        /**
         * Add New container
         * @author tjavan
         */
        case 'container-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag == true) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Ship::setNewContainer($title, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("container_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit container
         * @author tjavan
         */
        case 'container-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag == true && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Ship::editContainerById($id, $title, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("container_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Add New ship cargo
         * @author tjavan
         */
        case 'ship-cargo-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag == true) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Ship::setNewCategoryShipCargo($title, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("ship_cargo_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit Ship cargo
         * @author tjavan
         */
        case 'ship-cargo-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag == true && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Ship::editCategoryShipCargoById($id, $title, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("ship_cargo_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         *  add Ship Packing
         */
        case 'ship-packing-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Ship::setNewShipPacking($title, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("ship_packing_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit Ship packing
         * @author tjavan
         */
        case 'ship-packing-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Ship::editCategoryShipPackingById($id, $title, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("ship_packing_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * get city by country id From air
         */
        case 'get-city-airport-by-country-id':
            $id = (int)$json['id'];
            $token = $antiXSS->xss_clean($json['token']);

            $language = ($_COOKIE['language']) ? $_COOKIE['language'] : "fa_ir";

            $arrayTemp = [];
            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {
                $result = Location::getAllCitiesByCountryId($id, 'air');
                if ($result->status == 200) {

                    if (!empty($result->response)) {
                        foreach ($result->response as $loop) {
                            $array = [];
                            $array['text'] = (!empty(array_column(json_decode($loop->city_name, true), 'value', 'slug')[$language])) ?
                                array_column(json_decode($loop->city_name, true), 'value', 'slug')[$language] : "";
                            $array['id'] = $loop->city_id;
                            $arrayTemp[] = $array;
                        }
                    }
                    print_r(json_encode($arrayTemp));
                } else {
                    echo json_encode($arrayTemp);
                }

            } else {
                echo json_encode($arrayTemp);
            }
            break;

        /**
         * Add New Airport
         * @author tjavan
         */
        case 'airport-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            if (@json_decode($title)) {
                $rrr = json_decode($title);
                foreach ($rrr as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                        $flag = false;
                    }
                }
            }

            if ($flag && $city > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Air::setNewAirPort($title, $city, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("airport_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit Airport
         * @author tjavan
         */
        case 'airport-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag && $id != 0 && strlen($id) > 0 && $city > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Air::editAirPortById($id, $title, $city, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("airport_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * delete airport
         * @author tjavan
         */
        case 'airport-delete':
            $airportID = (int)$antiXSS->xss_clean($json['airportID']);
            $token = $antiXSS->xss_clean($json['token']);
            $name = $antiXSS->xss_clean($json['name']);

            if ($airportID > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {


                    $result = Air::deleteAirport($airportID);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("admin_airport_delete_" . $name . "---" . $airportID);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }
            break;


        /**
         * Add New Air cargo
         * @author tjavan
         */
        case 'air-cargo-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag == true) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Air::setNewCategoryAirCargo($title, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("air_cargo_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit Air cargo
         * @author tjavan
         */
        case 'air-cargo-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag == true && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Air::editCategoryAirCargoById($id, $title, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("air_cargo_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         *  add Air Packing
         */
        case 'air-packing-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Air::setNewAirPacking($title, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("air_packing_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit Air packing
         * @author tjavan
         */
        case 'air-packing-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Air::editCategoryAirPackingById($id, $title, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("air_packing_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * get city by country id From railroad
         */
        case 'get-city-railroad-by-country-id':
            $id = (int)$json['id'];
            $token = $antiXSS->xss_clean($json['token']);

            $language = ($_COOKIE['language']) ? $_COOKIE['language'] : "fa_ir";

            $arrayTemp = [];
            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {
                $result = Location::getAllCitiesByCountryId($id, 'railroad');
                if ($result->status == 200) {

                    if (!empty($result->response)) {
                        foreach ($result->response as $loop) {
                            $array = [];
                            $array['text'] = (!empty(array_column(json_decode($loop->city_name, true), 'value', 'slug')[$language])) ?
                                array_column(json_decode($loop->city_name, true), 'value', 'slug')[$language] : "";
                            $array['id'] = $loop->city_id;
                            $arrayTemp[] = $array;
                        }
                    }
                    print_r(json_encode($arrayTemp));
                } else {
                    echo json_encode($arrayTemp);
                }

            } else {
                echo json_encode($arrayTemp);
            }
            break;


        /**
         * Add New Railroad
         * @author tjavan
         */
        case 'railroad-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            if (@json_decode($title)) {
                $rrr = json_decode($title);
                foreach ($rrr as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                        $flag = false;
                    }
                }
            }

            if ($flag && $city > 0) {
                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {
                    $result = Railroad::setNewRailroad($title, $city, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("railroad_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit Railroad
         * @author tjavan
         */
        case 'railroad-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag && $id != 0 && strlen($id) > 0 && $city > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Railroad::editRailroadById($id, $title, $city, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("railroad_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;

        /**
         * delete railroad
         * @author tjavan
         */
        case 'railroad-delete':
            $railroadID = (int)$antiXSS->xss_clean($json['railroadID']);
            $token = $antiXSS->xss_clean($json['token']);
            $name = $antiXSS->xss_clean($json['name']);

            if ($railroadID > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {


                    $result = Railroad::deleteRailroad($railroadID);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("admin_railroad_delete_" . $name . "---" . $railroadID);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }
            break;

        /**
         * Add New wagon
         * @author tjavan
         */
        case 'wagon-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag == true) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Railroad::setNewWagon($title, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("wagon_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;
        case 'visa-location-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag == true) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = VisaLocation::setNewVisaLocation($title, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("visa_location_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit wagon
         * @author tjavan
         */
        case 'wagon-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag == true && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Railroad::editWagonById($id, $title, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("wagon_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;

        /**
         * Edit wagon
         * @author tjavan
         */
        case 'visa-location-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag == true && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = VisaLocation::editVisaLocationById($id, $title, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("visa_location_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Add New railroad cargo
         * @author tjavan
         */
        case 'railroad-cargo-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag == true) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Railroad::setNewCategoryRailroadCargo($title, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("railroad_cargo_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit Air cargo
         * @author tjavan
         */
        case 'railroad-cargo-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag == true && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Railroad::editCategoryRailroadCargoById($id, $title, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("railroad_cargo_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         *  add railroad Packing
         */
        case 'railroad-packing-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Railroad::setNewRailroadPacking($title, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("railroad_packing_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit railroad packing
         * @author tjavan
         */
        case 'railroad-packing-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Railroad::editCategoryRailroadPackingById($id, $title, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("railroad_packing_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Add New container railroad
         * @author tjavan
         */
        case 'container-railroad-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag == true) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Railroad::setNewContainer($title, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("container_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit container
         * @author tjavan
         */
        case 'container-railroad-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag == true && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Railroad::editContainerById($id, $title, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("container_railroad_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;



        ///////////////////// inquiry Ground

        /**
         * Change inquiry ground Info By X-table
         */
        case 'inquiry-ground-info' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "category_id",
                "car_type_id",
                "freight_wieght",
                "freight_volume",
                "freight_price",
                "currency_id",
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = Ground::editInquiryGroundInfoByAdmin($inquiryId, $type, $newValue);
//                    print_r($result);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_ground_info_" . $inquiryId, "inquiry");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }


            break;


        /**
         * Add Description Admin From Inquiry Ground Info
         */
        case 'inquiry-ground-info-desc-admin' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "freight_admin_description",
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = Ground::addInquiryGroundInfoByAdmin($inquiryId, $type, $newValue);
//                    print_r($result);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_ground_info_" . $inquiryId, "inquiry");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }


            break;


        /**
         * Change Status And Set Log From Inquiry Ground Info
         */
        case 'inquiry-ground-info-statue':
            $status = $antiXSS->xss_clean($json['status']);
            $inquiryId = (int)$json['inquiryId'];
            $token = $antiXSS->xss_clean($json['token']);


            if ($inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Ground::changeInquiryGroundStatusByAdmin($inquiryId, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_ground_status_id_" . $inquiryId);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;


        /**
         * get All country From inquiry info location
         */
        case 'inquiry-info-location':


            $resultAllCities = Location::getAllCountriesSomeValues();
            $dataAllCities = [];
            if ($resultAllCities->status == 200 && !empty($resultAllCities->response)) {
                $dataAllCities = $resultAllCities->response;
            }

            $array = [];
            if (!empty($dataAllCities)) {
                foreach ($dataAllCities as $lOOP) {
                    $name = (!empty(array_column(json_decode($lOOP->country_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                        array_column(json_decode($lOOP->country_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                    array_push($array, ['name' => $name, 'value' => $lOOP->country_id]);
                }
            }

            if (!empty($array)) {
                print_r(json_encode(["status" => 200, "data" => $array]));
            } else {
                print_r(json_encode(["status" => 0, "data" => $array]));
            }


            break;


        /**
         * get all customs by Id country From inquiry Ground info
         */
        case 'inquiry-info-location-customs':
            $country = (int)$json['country'];


            $resultAllCities = Ground::getCustomsByCountryID($country);
            $dataAllCities = [];
            if ($resultAllCities->status == 200 && !empty($resultAllCities->response)) {
                $dataAllCities = $resultAllCities->response;
            }

            $array = [];
            if (!empty($dataAllCities)) {
                foreach ($dataAllCities as $lOOP) {
                    $name = (!empty(array_column(json_decode($lOOP->customs_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                        array_column(json_decode($lOOP->customs_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                    array_push($array, ['name' => $name, 'city' => $lOOP->city_id, 'value' => $lOOP->customs_id]);
                }
            }

            if (!empty($array)) {
                print_r(json_encode(["status" => 200, "data" => $array]));
            } else {
                print_r(json_encode(["status" => 0, "data" => $array]));
            }


            break;


        /**
         * change inquiry ground location
         */
        case 'inquiry-ground-set-new-location' :

            $country = (int)$antiXSS->xss_clean($json['country']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $customs = (int)$antiXSS->xss_clean($json['customs']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);
            $type = $antiXSS->xss_clean($json['type']);


            if ($type != 'source') {
                $type = "dest";
            }

            if (strlen($inquiryId) > 0 && strlen($customs) >= 1) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Ground::changeInquiryGroundLocationByAdmin($inquiryId, $country, $city, $customs, $type);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_ground_location_" . $inquiryId);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;


        ///////////////////// inquiry ship


        /**
         * Change inquiry Ship Info By X-table
         */
        case 'inquiry-ship-info' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "category_id",
                "packing_id",
                "container_id",
                "freight_count_container",
                "freight_wieght",
                "freight_volume",
                "freight_price",
                "currency_id",
                "freight_free_time",
                "freight_term_id",
                "freight_waybill",
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = Ship::editInquiryShipInfoByAdmin($inquiryId, $type, $newValue);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_ship_info_" . $inquiryId, "inquiry");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }


            break;


        /**
         * Change Status And Set Log From Inquiry Ship Info
         */
        case 'inquiry-ship-info-statue':
            $status = $antiXSS->xss_clean($json['status']);
            $inquiryId = (int)$json['inquiryId'];
            $token = $antiXSS->xss_clean($json['token']);


            if ($inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Ship::changeInquiryShipStatusByAdmin($inquiryId, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_ship_status_id_" . $inquiryId);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;


        /**
         * get all ports by Id country From inquiry Ship info
         */
        case 'inquiry-info-location-port':
            $country = (int)$json['country'];


            $resultAllCities = Ship::getPortsByCountryID($country);
            $dataAllCities = [];
            if ($resultAllCities->status == 200 && !empty($resultAllCities->response)) {
                $dataAllCities = $resultAllCities->response;
            }

            $array = [];
            if (!empty($dataAllCities)) {
                foreach ($dataAllCities as $lOOP) {
                    $name = (!empty(array_column(json_decode($lOOP->port_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                        array_column(json_decode($lOOP->port_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                    array_push($array, ['name' => $name, 'city' => $lOOP->city_id, 'value' => $lOOP->port_id]);
                }
            }

            if (!empty($array)) {
                print_r(json_encode(["status" => 200, "data" => $array]));
            } else {
                print_r(json_encode(["status" => 0, "data" => $array]));
            }


            break;


        /**
         * change inquiry Ship location
         */
        case 'inquiry-ship-set-new-location' :

            $country = (int)$antiXSS->xss_clean($json['country']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $customs = (int)$antiXSS->xss_clean($json['customs']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);
            $type = $antiXSS->xss_clean($json['type']);


            if ($type != 'source') {
                $type = "dest";
            }

            if (strlen($inquiryId) > 0 && strlen($customs) >= 1) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Ship::changeInquiryShipLocationByAdmin($inquiryId, $country, $city, $customs, $type);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_ship_location_" . $inquiryId);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;


        /**
         * Add Description Admin From Inquiry ship Info
         */
        case 'inquiry-ship-info-desc-admin' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "freight_admin_description",
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = Ship::addInquiryShipInfoByAdmin($inquiryId, $type, $newValue);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_ship_info_" . $inquiryId, "inquiry");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }


            break;




        ///////////////////// inquiry Air


        /**
         * Change inquiry Air Info By X-table
         */
        case 'inquiry-air-info' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "category_id",
                "packing_id",
                "freight_wieght",
                "freight_volume",
                "freight_price",
                "freight_price_value",
                "currency_id",
                "currency_id_value",
                "freight_discharge",
                "freight_waybill",
                "freight_contract",
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = Air::editInquiryAirInfoByAdmin($inquiryId, $type, $newValue);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_ship_info_" . $inquiryId, "inquiry");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }


            break;


        /**
         * Change Status And Set Log From Inquiry Air Info
         */
        case 'inquiry-air-info-statue':
            $status = $antiXSS->xss_clean($json['status']);
            $inquiryId = (int)$json['inquiryId'];
            $token = $antiXSS->xss_clean($json['token']);


            if ($inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Air::changeInquiryAirStatusByAdmin($inquiryId, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_air_status_id_" . $inquiryId);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;


        /**
         * Add Description Admin From Inquiry Air Info
         */
        case 'inquiry-air-info-desc-admin' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "freight_admin_description",
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = Air::addInquiryAirInfoByAdmin($inquiryId, $type, $newValue);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_air_info_" . $inquiryId, "inquiry");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }


            break;


        /**
         * get all airports by Id country From inquiry Ship info
         */
        case 'inquiry-info-location-airport':
            $country = (int)$json['country'];


            $resultAllCities = Air::getAirportsByCountryID($country);
            $dataAllCities = [];
            if ($resultAllCities->status == 200 && !empty($resultAllCities->response)) {
                $dataAllCities = $resultAllCities->response;
            }

            $array = [];
            if (!empty($dataAllCities)) {
                foreach ($dataAllCities as $lOOP) {
                    $name = (!empty(array_column(json_decode($lOOP->airport_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                        array_column(json_decode($lOOP->airport_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                    array_push($array, ['name' => $name, 'city' => $lOOP->city_id, 'value' => $lOOP->airport_id]);
                }
            }

            if (!empty($array)) {
                print_r(json_encode(["status" => 200, "data" => $array]));
            } else {
                print_r(json_encode(["status" => 0, "data" => $array]));
            }


            break;


        /**
         * change inquiry Air location
         */
        case 'inquiry-air-set-new-location' :

            $country = (int)$antiXSS->xss_clean($json['country']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $customs = (int)$antiXSS->xss_clean($json['customs']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);
            $type = $antiXSS->xss_clean($json['type']);


            if ($type != 'source') {
                $type = "dest";
            }

            if (strlen($inquiryId) > 0 && strlen($customs) >= 1) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Air::changeInquiryAirLocationByAdmin($inquiryId, $country, $city, $customs, $type);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_ship_location_" . $inquiryId);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;



        ///////////////////// inquiry Railroad


        /**
         * Change inquiry Railroad Info By X-table
         */
        case 'inquiry-railroad-info' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "category_id",
                "packing_id",
                "wagon_id",
                "freight_wieght",
                "freight_volume",
                "freight_price",
                "currency_id",
                "freight_count_container",
                "freight_discharge",
                "freight_international_code",
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = Railroad::editInquiryRailroadInfoByAdmin($inquiryId, $type, $newValue);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_railroad_info_" . $inquiryId, "inquiry");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }


            break;


        /**
         * Change Status And Set Log From Inquiry Air Info
         */
        case 'inquiry-railroad-info-statue':
            $status = $antiXSS->xss_clean($json['status']);
            $inquiryId = (int)$json['inquiryId'];
            $token = $antiXSS->xss_clean($json['token']);


            if ($inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Railroad::changeInquiryRailroadStatusByAdmin($inquiryId, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_railroad_status_id_" . $inquiryId);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;


        /**
         * Add Description Admin From Inquiry Air Info
         */
        case 'inquiry-railroad-info-desc-admin' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "freight_admin_description",
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = Railroad::addInquiryRailroadInfoByAdmin($inquiryId, $type, $newValue);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_railroad_info_" . $inquiryId, "inquiry");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }


            break;


        /**
         * get all airports by Id country From inquiry Ship info
         */
        case 'inquiry-info-location-railroad':
            $country = (int)$json['country'];


            $resultAllCities = Railroad::getRailroadByCountryID($country);
            $dataAllCities = [];
            if ($resultAllCities->status == 200 && !empty($resultAllCities->response)) {
                $dataAllCities = $resultAllCities->response;
            }

            $array = [];
            if (!empty($dataAllCities)) {
                foreach ($dataAllCities as $lOOP) {
                    $name = (!empty(array_column(json_decode($lOOP->railroad_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                        array_column(json_decode($lOOP->railroad_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                    array_push($array, ['name' => $name, 'city' => $lOOP->city_id, 'value' => $lOOP->railroad_id]);
                }
            }

            if (!empty($array)) {
                print_r(json_encode(["status" => 200, "data" => $array]));
            } else {
                print_r(json_encode(["status" => 0, "data" => $array]));
            }


            break;


        /**
         * change inquiry Air location
         */
        case 'inquiry-railroad-set-new-location' :

            $country = (int)$antiXSS->xss_clean($json['country']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $customs = (int)$antiXSS->xss_clean($json['customs']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);
            $type = $antiXSS->xss_clean($json['type']);


            if ($type != 'source') {
                $type = "dest";
            }

            if (strlen($inquiryId) > 0 && strlen($customs) >= 1) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Railroad::changeInquiryRailroadLocationByAdmin($inquiryId, $country, $city, $customs, $type);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_railroad_location_" . $inquiryId);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;




        //// Start Academy

        /**
         * Edit Academy Category
         * @author tjavan
         */
        case 'category-academy-delete':
            $categoryReplace = (int)$antiXSS->xss_clean($json['categoryReplace']);
            $categoryID = (int)$antiXSS->xss_clean($json['categoryID']);
            $token = $antiXSS->xss_clean($json['token']);


            $result_token = Security::verifyCSRF2($token);
            if ($result_token) {
                if ($categoryID && $categoryID > 0 && $categoryReplace && $categoryReplace > 0) {

                    $result = Academy::deleteCategory($categoryID, $categoryReplace);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("delete_category_academy_id_replate_" . $categoryID . "==>" . $categoryReplace, 'academy');
                    } else {
                        echo "error";
                    }


                } else {
                    echo "empty";
                }
            } else {
                echo "token_error";
            }
            break;


        /**
         * Add Academy
         * @author tjavan
         */
        case 'academy-add':
            $token = $antiXSS->xss_clean($json['token']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $myContent = $json['myContent'];
            $excerpt = $antiXSS->xss_clean(strip_tags($json['excerpt']));
            $metaTitle = $antiXSS->xss_clean($json['metaTitle']);
            $schema = $antiXSS->xss_clean($json['schema']);
            $status = $antiXSS->xss_clean($json['status']);
            $language = $antiXSS->xss_clean($json['language']);

            $category = (int)$antiXSS->xss_clean($json['category']);
            $thumbnail = $antiXSS->xss_clean($json['thumbnail']);
            $slug = str_replace(" ", "-", $antiXSS->xss_clean(strip_tags($json['slug'])));


            if ($status != 'published') {
                $status = "draft";
            }


            if (strlen($title) > 2 && $category > 0 && strlen($slug) > 4
                && strlen($thumbnail) > 50) {


                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {


                    if (!file_exists(getcwd() . ACADEMY_ADDRESS) && !is_dir(getcwd() . ACADEMY_ADDRESS)) {
                        mkdir(getcwd() . ACADEMY_ADDRESS);
                    }

                    $resultThumbnail = Upload::uploadBase64($thumbnail, ACADEMY_ADDRESS);


                    if ($resultThumbnail->status == 200 && !empty($resultThumbnail->response)) {
                        $thumbnailURL = $resultThumbnail->response;

                        $result = Academy::SetNewAcademy($title, $myContent, $category, $slug, $status, $thumbnailURL, $language, $excerpt, $metaTitle, $schema, 0, 0);

                        if ($result->status == 200) {
                            echo "successful " . $result->response;
                            Admin::SetAdminLog("academy_add_title_" . $title, "academy");
                        } else {
                            echo "error";
                        }
                    } else {
                        echo "error_img";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Edit Academy
         * @author tjavan
         */
        case 'academy-edit':
            $token = $antiXSS->xss_clean($json['token']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $myContent = $json['myContent'];
            $excerpt = $antiXSS->xss_clean(strip_tags($json['excerpt']));
            $metaTitle = $antiXSS->xss_clean($json['metaTitle']);
            $schema = $antiXSS->xss_clean($json['schema']);
            $status = $antiXSS->xss_clean($json['status']);
            $language = $antiXSS->xss_clean($json['language']);
            $category = (int)$antiXSS->xss_clean($json['category']);
            $thumbnail = $antiXSS->xss_clean($json['thumbnail']);
            $slug = str_replace(" ", "-", $antiXSS->xss_clean(strip_tags($json['slug'])));
            $id = (int)$antiXSS->xss_clean($json['id']);

            if ($status != 'published') {
                $status = "draft";
            }


            if (strlen($title) > 2 && $category > 0 && strlen($slug) > 4 && $id > 0 && strlen($language) > 3) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $image = "";
                    if (!empty($thumbnail)) {
                        $resultThumbnail = Upload::uploadBase64($thumbnail, ACADEMY_ADDRESS);
                        if ($resultThumbnail->status == 200 && !empty($resultThumbnail->response)) {
                            $image = $resultThumbnail->response;
                        }
                    }

                    $result = Academy::editAcademy($id, $title, $myContent, $category, $slug, $status, $image, $language, $excerpt, $metaTitle, $schema, null, null);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("academy_edit_id_" . $id, "academy");
                    } else {
                        echo "error";
                    }


                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }
            break;


        /**
         * Edit Academy
         * @author tjavan
         */
        case 'academy-delete':
            $id = (int)$antiXSS->xss_clean($json['id']);
            $token = $antiXSS->xss_clean($json['token']);

            if (strlen($id) > 0 && $id > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {


                    $res = Academy::getAcademyByID($id);
                    $name = "";
                    if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
                        $name = $res->response[0]->academy_title;
                    }


                    $result = Academy::deleteAcademy($id);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("delete_academy_id_" . $name, "academy");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }
            break;


        case 'academy-edit-update-date':
            $token = $antiXSS->xss_clean($json['token']);
            $id = (int)$antiXSS->xss_clean($json['id']);

            if ($id > 0) {
                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Academy::updateAcademySubmitDate($id);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("academy_edit_id_" . $id, "academy");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;


        /**
         * get All country From cargo info location
         */
        case 'cargo-info-location':

            $resultAllCities = Location::getAllCountriesSomeValues();
            $dataAllCities = [];
            if ($resultAllCities->status == 200 && !empty($resultAllCities->response)) {
                $dataAllCities = $resultAllCities->response;
            }
            $langCookie = $_COOKIE['language'];
            $array = [];
            if (!empty($dataAllCities)) {
                $dataAllCities = array_reverse($dataAllCities);
                foreach ($dataAllCities as $lOOP) {
                    $name = (!empty(array_column(json_decode($lOOP->country_name, true), 'value', 'slug')[$langCookie])) ?
                        array_column(json_decode($lOOP->country_name, true), 'value', 'slug')[$langCookie] : "";
                    array_push($array, ['name' => $name, 'value' => $lOOP->country_id]);
                }
            }

            if (!empty($array)) {
                print_r(json_encode(["status" => 200, "data" => $array]));
            } else {
                print_r(json_encode(["status" => 0, "data" => $array]));
            }


            break;

        /**
         * get all city and customs by Id country From cargo info
         */
        case 'cargo-location-city-and-customs':
            $country = (int)$json['country'];


            $langCookie = $_COOKIE['language'];
            $resultAllCustoms = Ground::getCustomsByCountryID($country);
            $dataAllCustoms = [];
            if ($resultAllCustoms->status == 200 && !empty($resultAllCustoms->response)) {
                $dataAllCustoms = $resultAllCustoms->response;
            }

            $resultAllCities = Location::getAllCitiesByCountryId($country, 'ground');
            $dataAllCities = [];
            if ($resultAllCities->status == 200 && !empty($resultAllCities->response)) {
                $dataAllCities = $resultAllCities->response;
            }


            $arrayCustoms = [];
            if (!empty($dataAllCustoms)) {
                foreach ($dataAllCustoms as $lOOP) {
                    $name = (!empty(array_column(json_decode($lOOP->customs_name, true), 'value', 'slug')[$langCookie])) ?
                        array_column(json_decode($lOOP->customs_name, true), 'value', 'slug')[$langCookie] : "";
                    array_push($arrayCustoms, ['name' => $name, 'value' => $lOOP->customs_id]);
                }
            }

            $arrayCity = [];
            if (!empty($dataAllCities)) {
                foreach ($dataAllCities as $lOOP) {
                    $name = (!empty(array_column(json_decode($lOOP->city_name, true), 'value', 'slug')[$langCookie])) ?
                        array_column(json_decode($lOOP->city_name, true), 'value', 'slug')[$langCookie] : "";
                    array_push($arrayCity, ['name' => $name, 'value' => $lOOP->city_id]);
                }
            }

            if (!empty($arrayCity) || !empty($arrayCustoms)) {
                print_r(json_encode(["status" => 200, "datacity" => $arrayCity, "datacustoms" => $arrayCustoms]));
            } else {
                print_r(json_encode(["status" => 0, "datacity" => $arrayCity, "datacustoms" => $arrayCustoms]));
            }


            break;

        case 'cargo-in-location-city':
            $country = (int)$json['country'];


            $langCookie = $_COOKIE['language'];

            $resultAllCities = Location::getAllCitiesByCountryId($country, 'ground');
            $dataAllCities = [];
            if ($resultAllCities->status == 200 && !empty($resultAllCities->response)) {
                $dataAllCities = $resultAllCities->response;
            }

            $arrayCity = [];
            if (!empty($dataAllCities)) {
                foreach ($dataAllCities as $lOOP) {
                    $name = (!empty(array_column(json_decode($lOOP->city_name, true), 'value', 'slug')[$langCookie])) ?
                        array_column(json_decode($lOOP->city_name, true), 'value', 'slug')[$langCookie] : "";
                    array_push($arrayCity, ['name' => $name, 'value' => $lOOP->city_id]);
                }
            }

            if (!empty($arrayCity)) {
                print_r(json_encode(["status" => 200, "datacity" => $arrayCity]));
            } else {
                print_r(json_encode(["status" => 0, "datacity" => $arrayCity]));
            }


            break;

        /**
         * change cargo info location
         */
        case 'cargo-set-new-location' :

            $country = (int)$antiXSS->xss_clean($json['country']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $customs = (int)$antiXSS->xss_clean($json['customs']);
            $CargoID = (int)$antiXSS->xss_clean($json['CargoID']);
            $token = $antiXSS->xss_clean($json['token']);
            $type = $antiXSS->xss_clean($json['type']);


//            $lists = [
//                "cargo_origin",
//                "cargo_customs_of_origin",
//                "cargo_destination",
//                "cargo_destination_customs",
//            ];

            $cityColumn = 'cargo_origin_id';
            $customsColumn = 'cargo_origin_customs_id';
            if ($type != 'source') {
                $type = "dest";
                $cityColumn = 'cargo_destination_id';
                $customsColumn = 'cargo_destination_customs_id';
            }

            if (strlen($CargoID) > 0 && strlen($city) >= 1 && strlen($customs) >= 1) {

                $result_token = Security::verifyCSRF('admin-cargo-info', $token);
                if ($result_token) {

                    @Cargo::updateCargoOptionsById($CargoID, $cityColumn, $city);
                    @Cargo::updateCargoOptionsById($CargoID, $customsColumn, $customs);
                    $result = Cargo::updateCargoLatLong($CargoID, $cityColumn, $city);
                    $result2 = Cargo::updateCargoLatLong($CargoID, $customsColumn, $customs);

                    if ($result->status == 200 && $result2->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("edit_cargo_" . $type . "_" . $CargoID);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;


        case 'cargo-in-set-new-location' :

            $country = (int)$antiXSS->xss_clean($json['country']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $customs = (int)$antiXSS->xss_clean($json['customs']);
            $CargoID = (int)$antiXSS->xss_clean($json['CargoID']);
            $token = $antiXSS->xss_clean($json['token']);


            $cityColumn = 'cargo_origin_id';
            $customsColumn = 'cargo_destination_id';

            if (strlen($CargoID) > 0 && strlen($city) >= 1 && strlen($customs) >= 1) {
                $result_token = Security::verifyCSRF('admin-cargo-in-info', $token);
                if ($result_token) {
                    @Cargo::updateCargoInOptionsById($CargoID, $cityColumn, $city);
                    @Cargo::updateCargoInOptionsById($CargoID, $customsColumn, $customs);
                    $result = Cargo::updateCargoInLatLong($CargoID, $cityColumn, $city);
                    $result2 = Cargo::updateCargoInLatLong($CargoID, $customsColumn, $customs);
                    if ($result->status == 200 && $result2->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("edit_cargo_in_location_" . $CargoID);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;






        // Start inventory


        /**
         * Add New Inventory cargo
         * @author tjavan
         */
        case 'inventory-cargo-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag == true) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Inventory::setNewCategoryInventoryCargo($title, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("inventory_cargo_add_id_" . $result->response);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit inventory cargo
         * @author tjavan
         */
        case 'inventory-cargo-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag == true && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Inventory::editCategoryInventoryCargoById($id, $title, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("inventory_cargo_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Add New Inventory Type
         * @author tjavan
         */
        case 'inventory-type-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }
            $priority = 1;

            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Inventory::setNewCategoryInventoryType($title, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("inventory_type_add_id_" . $result->response);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit inventory type
         * @author tjavan
         */
        case 'inventory-type-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }
            $priority = 1;

            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Inventory::editCategoryInventoryTypeById($id, $title, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("inventory_type_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * get city by country id From inventory
         */
        case 'get-city-inventory-by-country-id':
            $id = (int)$json['id'];
            $token = $antiXSS->xss_clean($json['token']);

            $language = ($_COOKIE['language']) ? $_COOKIE['language'] : "fa_ir";

            $arrayTemp = [];
            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {
                $result = Location::getAllCitiesByCountryId($id, 'inventory');
                if ($result->status == 200) {

                    if (!empty($result->response)) {
                        foreach ($result->response as $loop) {
                            $array = [];
                            $array['text'] = (!empty(array_column(json_decode($loop->city_name, true), 'value', 'slug')[$language])) ?
                                array_column(json_decode($loop->city_name, true), 'value', 'slug')[$language] : "";
                            $array['id'] = $loop->city_id;
                            $arrayTemp[] = $array;
                        }
                    }
                    print_r(json_encode($arrayTemp));
                } else {
                    echo json_encode($arrayTemp);
                }

            } else {
                echo json_encode($arrayTemp);
            }
            break;


        /**
         * Add New Inventory
         * @author tjavan
         */
        case 'inventory-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            if (@json_decode($title)) {
                $rrr = json_decode($title);
                foreach ($rrr as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                        $flag = false;
                    }
                }
            }

            if ($flag && $city > 0) {
                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {
                    $result = inventory::setNewInventory($title, $city, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("inventory_add_id_" . $result->response);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit Inventory
         * @author tjavan
         */
        case 'inventory-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag && $id != 0 && strlen($id) > 0 && $city > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Inventory::editInventoryById($id, $title, $city, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("inventory_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;

        /**
         * delete Inventory
         * @author tjavan
         */
        case 'inventory-delete':
            $inventoryID = (int)$antiXSS->xss_clean($json['inventoryID']);
            $token = $antiXSS->xss_clean($json['token']);
            $name = $antiXSS->xss_clean($json['name']);

            if ($inventoryID > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {


                    $result = Inventory::deleteInventory($inventoryID);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("admin_inventory_delete_" . $name . "---" . $inventoryID);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }
            break;




        ///////////////////// inquiry Railroad


        /**
         * Change inquiry Inventory Info By X-table
         */
        case 'inquiry-inventory-info' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "category_id",
                "type_id",
                "freight_wieght",
                "freight_volume",
                "freight_price",
                "freight_duration",
                "currency_id"
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = Inventory::editInquiryInventoryInfoByAdmin($inquiryId, $type, $newValue);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_inventory_info_" . $inquiryId, "inquiry");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }


            break;


        /**
         * Add Description Admin From Inquiry Inventory Info
         */
        case 'inquiry-inventory-info-desc-admin' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = [
                "freight_admin_description",
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = Inventory::addInquiryInventoryInfoByAdmin($inquiryId, $type, $newValue);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_inventory_info_" . $inquiryId, "inquiry");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }


            break;


        /**
         * Change Status And Set Log From Inquiry inventory Info
         */
        case 'inquiry-inventory-info-statue':
            $status = $antiXSS->xss_clean($json['status']);
            $inquiryId = (int)$json['inquiryId'];
            $token = $antiXSS->xss_clean($json['token']);


            if ($inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Inventory::changeInquiryInventoryStatusByAdmin($inquiryId, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_inventory_status_id_" . $inquiryId);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;


        /**
         * get all inventory by Id country From inquiry inventory info
         */
        case 'inquiry-info-location-inventory':
            $country = (int)$json['country'];
            $langCookie = $_COOKIE['language'];

            $resultAllCities = Location::getAllCitiesByCountryId($country, 'inventory');
            $dataAllCities = [];
            if ($resultAllCities->status == 200 && !empty($resultAllCities->response)) {
                $dataAllCities = $resultAllCities->response;
            }


            $resultInventory = Inventory::getInventoryByCountryID($country);
            $dataInventory = [];
            if ($resultInventory->status == 200 && !empty($resultInventory->response)) {
                $dataInventory = $resultInventory->response;
            }

            $array = [];
            if (!empty($dataAllCities)) {
                foreach ($dataAllCities as $lOOP) {
                    $name = (!empty(array_column(json_decode($lOOP->city_name, true), 'value', 'slug')[$langCookie])) ?
                        array_column(json_decode($lOOP->city_name, true), 'value', 'slug')[$langCookie] : "";
                    array_push($array, ['name' => $name, 'value' => $lOOP->city_id]);
                }
            }

            $arrayIN = [];
            if (!empty($dataInventory)) {
                foreach ($dataInventory as $lOOP) {
                    $name = (!empty(array_column(json_decode($lOOP->inventory_name, true), 'value', 'slug')[$langCookie])) ?
                        array_column(json_decode($lOOP->inventory_name, true), 'value', 'slug')[$langCookie] : "";
                    array_push($arrayIN, ['name' => $name, 'value' => $lOOP->inventory_id]);
                }
            }

            if (!empty($array) || !empty($arrayIN)) {
                print_r(json_encode(["status" => 200, "dataInventory" => $arrayIN, "dataCity" => $array]));
            } else {
                print_r(json_encode(["status" => 0, "dataInventory" => $arrayIN, "dataCity" => $array]));
            }


            break;


        /**
         * change inquiry Air location
         */
        case 'inquiry-inventory-set-new-location' :

            $country = (int)$antiXSS->xss_clean($json['country']);
            $city = (int)$antiXSS->xss_clean($json['city']);
            $inventory = (int)$antiXSS->xss_clean($json['inventory']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);


            if (strlen($inquiryId) > 0 && strlen($inventory) >= 1) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Inventory::changeInquiryInventoryLocationByAdmin($inquiryId, $country, $city, $inventory);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_inventory_location_" . $inquiryId);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }


            break;


        /**
         * Task Status Update
         */
        case 'task-info-status':

            $taskId = (int)$antiXSS->xss_clean($json['taskId']);
            $status = $antiXSS->xss_clean($json['status']);
            $token = $antiXSS->xss_clean($json['token']);

            $list_status = [
                "ok",
                "rejected",
            ];

            if (strlen($taskId) > 0 && in_array($status, $list_status)) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Tasks::changeTaskStatus($taskId, $status);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_task_status_to_" . $status . "_" . $taskId, 'a_tasks');
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * inquiry card bank
         */
        case 'inquiry-card-bank':

            $creditNumber = $antiXSS->xss_clean($json['creditNumber']);
            $creditID = (int)$antiXSS->xss_clean($json['creditID']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($creditID > 0 && isset($creditNumber)) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = AUser::inquiryCardBank($creditID, $creditNumber);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_inquiry_card_bank_id_" . $creditID, 'card_banks');
                    } elseif ($result->status == 300) {
                        echo "inquiry_error";
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        ///////////////////////////////////////   Start Poster


        /**
         * Model Add
         */
        case 'model-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean($json['title']);
            $parent = (int)$antiXSS->xss_clean($json['parent']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }

            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 1) {
                    $flag = false;
                }
            }


            if ($flag && strlen($parent) > 0) {


                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = PosterC::setNewModel($title, $parent, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("add_new_model_id_" . $result->response);
                    } else {
                        echo "error";
                    }

                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;

        /**
         * Edit And Update Model
         */
        case 'model-edit':
            $id = (int)$json['id'];
            $parent = ((int)$antiXSS->xss_clean($json['parent'])) ? (int)$antiXSS->xss_clean($json['parent']) : null;
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);


            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 1) {
                    $flag = false;
                }
            }


            if ($flag == true && $id > 0 && $parent > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = PosterC::editModelById($id, $title, $parent, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("edit_model_id_" . $id, 'category');
                    } else {
                        echo "error";
                    }

                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        /**
         * Add NEW Property
         */
        case 'report-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean($json['title']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }

            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag) {


                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = PosterC::setNewReport($title, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("add_new_report_id_" . $result->response);
                    } else {
                        echo "error";
                    }


                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;


        /**
         * Edit And Update Report
         */
        case 'report-edit':
            $id = (int)$json['id'];
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean($json['title']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            $token = $antiXSS->xss_clean($json['token']);


            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag && $id > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = PosterC::editReportById($id, $title, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("edit_report_id_" . $id, 'category');
                    } else {
                        echo "error";
                    }


                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }
            break;


        /**
         * census inquiry credit Jibit
         */
        case 'census-inquiry-credit':
            $time = $antiXSS->xss_clean($json['time']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($time && strlen($time) == 6) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = InquiryJibit::getDailyUsage('010808');
                    if (isset(json_decode($result)->report[0]->services)) {
                        // echo "successful";
                        print_r(json_encode(["status" => 200, "response" => json_decode($result)->report[0]->services]));
                        //Admin::SetAdminLog("settings_inquiry" . $id, 'category');
                    } else {
                        //echo "error";
                        print_r(json_encode(["status" => 2, "response" => []]));
                    }


                } else {
                    //echo "token_error";
                    print_r(json_encode(["status" => 3, "response" => []]));
                }

            } else {
                // echo "empty";
                print_r(json_encode(["status" => 4, "response" => []]));
            }
            break;


        case 'change-user-auth-status':
            $status = $antiXSS->xss_clean($json['status']);
            $userID = $antiXSS->xss_clean($json['userID']);
            $token = $antiXSS->xss_clean($json['token']);

            $list_status = [
                "accepted",
                "rejected"
            ];
            if (in_array($status, $list_status) && strlen($userID) > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = AUser::updateUserAuthStatus($userID, $status);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_auth_status_to_" . $status . "_" . $userID, "user_info");
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


        /**
         * request inquiry user
         */
        case 'request-inquiry-user':
            $userID = $antiXSS->xss_clean($json['userID']);
            $token = $antiXSS->xss_clean($json['token']);


            if (strlen($userID) > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = AUser::getUserInfoById($userID);
                    if ($result->status == 200) {
                        $mobile = Security::decrypt($result->response[0]->user_mobile);
                        $id_card = Security::decrypt($result->response[0]->user_number_card);

                        $s = InquiryJibit::matchPhoneAndNationalCode("0" . substr($mobile, 3), $id_card);

                        if (@json_decode($s) && isset(json_decode($s)->matched)) {
                            if (json_decode($s)->matched == true) {
                                $temp = "true";
                            } else {
                                $temp = "false";
                            }
                        } else {
                            $temp = "false";
                        }
                        $x = AUser::updateUserInquiryStatus($userID, $id_card, $temp);

                        if ($x->status == 200) {
                            echo "successful";
                        } else {
                            echo "error";
                        }

                        Admin::SetAdminLog("request_inquiry_user_" . $userID . "==>" . $id_card . "==>" . $mobile, "user_info");
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

        case "share-whatsapp-info-status":
            $waID = (int)$antiXSS->xss_clean($json['waID']);
            $status = $antiXSS->xss_clean($json['status']);
            $token = $antiXSS->xss_clean($json['token']);


            $list_status = [
                "sended",
                "rejected"
            ];
            if (in_array($status, $list_status) && $waID > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Share::changeShareWhatsappStatus($waID, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_share_whatsapp_status_to_" . $status . "_" . $waID, "user_info");
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


        /**
         * Start Poster
         */

        case 'change-poster-status' :

            $status = $antiXSS->xss_clean($json['status']);
            $posterId = (int)$antiXSS->xss_clean($json['posterId']);
            $reason = $antiXSS->xss_clean($json['reason']);
            $token = $antiXSS->xss_clean($json['token']);
            $lists = [
                "accepted",
                "rejected",
                "needed",
            ];
            if (in_array($status, $lists) && $posterId > 0) {

                $result_token = Security::verifyCSRF('admin-poster-info', $token);
                if ($result_token) {
                    $result = Poster::changeStatusPoster($posterId, $status, $reason);
                    $poster_detail = Poster::getPosterDetail($posterId);

//                    print_r($result);
                    if ($result->status == 200) {
                        Notification::sendNotification(
                            $poster_detail->response->user_id,
                            'nLog_poster_'.$status, 'system', 'nLog_poster_message_'.$status,
                            'https://ntirapp.com/poster/detail/'.$posterId , 'unread' , true
                        );
                        echo "successful " . Security::initCSRF('admin-poster-info');
                        Admin::SetAdminLog("change_poster_status_to_" . $status . "_" . $posterId, "a_poster");
                    } else {
                        echo "error " . Security::initCSRF('admin-poster-info');
                    }
                } else {
                    echo "token_error";
                }
            }


            break;

        case 'poster-delete-add':
            $status = $antiXSS->xss_clean($json['status']);
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = PosterC::setNewCategoryReasonDeletedPoster($title, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("a_poster_reason_delete_add_id" . $result->response);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;

        case 'poster-delete-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $priority = (int)$antiXSS->xss_clean($json['priority']);
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = PosterC::editCategoryReasonDeletedPosterById($id, $title, $status, $priority);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_poster_reason_delete_edit_id" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;

        case 'expert-add':
            $token = $antiXSS->xss_clean($json['token']);
            $firstname = $antiXSS->xss_clean($json['firstname']);
            $lastname = $antiXSS->xss_clean($json['lastname']);
            $mobile = $antiXSS->xss_clean($json['mobile']);
            $address = $antiXSS->xss_clean(strip_tags($json['address']));
            $description = $antiXSS->xss_clean(strip_tags($json['description']));
            $status = $antiXSS->xss_clean($json['status']);

            if ($status != 'active') {
                $status = "inactive";
            }
            if (strlen($firstname) > 2 && strlen($lastname) > 2 && strlen($mobile) > 11) {
                $result_token = Security::verifyCSRF('admin-expert-add', $token);
                if ($result_token) {
                    $result = Expert::SetNewExpert($firstname, $lastname, $mobile, $address, $description, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("a_add_new_expert_id_" . $result->response);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;

        case 'expert-edit':
            $token = $antiXSS->xss_clean($json['token']);
            $firstname = $antiXSS->xss_clean($json['firstname']);
            $lastname = $antiXSS->xss_clean($json['lastname']);
            $mobile = $antiXSS->xss_clean($json['mobile']);
            $address = $antiXSS->xss_clean(strip_tags($json['address']));
            $description = $antiXSS->xss_clean(strip_tags($json['description']));
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$antiXSS->xss_clean($json['id']);

            if ($status != 'active') {
                $status = "inactive";
            }

            if (strlen($firstname) > 2 && strlen($lastname) > 2 && strlen($mobile) > 11 && $id > 0) {
                $result_token = Security::verifyCSRF('admin-expert-edit', $token, false);
                if ($result_token) {
                    $result = Expert::editExpertById($id, $firstname, $lastname, $mobile, $address, $description, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_add_new_expert_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;

        case 'poster-expert-info' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $peId = (int)$antiXSS->xss_clean($json['peId']);
            $token = $antiXSS->xss_clean($json['token']);
            $lists = [
                "pe_address",
                "expert_id",
                "pe_reason",
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $peId > 0) {
                $result_token = Security::verifyCSRF('admin-pe-info', $token);
                if ($result_token) {
                    $result = Poster::editPosterExpertInfoByAdmin($peId, $type, $newValue);
                    if ($result->status == 200) {
                        echo "successful " . Security::initCSRF('admin-pe-info');
                        Admin::SetAdminLog("a_change_request_poster_expert_" . $peId);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }
            break;

        case 'poster-expert-select-expert' :
            $peId = (int)$antiXSS->xss_clean($json['peId']);
            $status = $antiXSS->xss_clean($json['status']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($peId > 0 && in_array($status, ['accepted', 'rejected', 'completed', 'canceled'])) {
                $result_token = Security::verifyCSRF('admin-pe-info', $token);
                if ($result_token) {
                    $result = Poster::changePosterExpertStatus($peId, $status);
                    if ($result->status == 200) {
                        echo "successful";
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;

        case 'poster-expert-sent-sms' :
            $peId = (int)$antiXSS->xss_clean($json['peId']);
            $token = $antiXSS->xss_clean($json['token']);
            if ($peId > 0) {
                $result_token = Security::verifyCSRF('admin-pe-info', $token, false);
                if ($result_token) {
                    $result = Poster::sendAddressToExpert($peId);
                    if ($result->status == 200) {
                        echo "successful";
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;

        /**
         * End Poster
         */


        case 'export-settings':
            $token = $antiXSS->xss_clean($json['token']);
            $files = $antiXSS->xss_clean($json['files']);

            if (count($files) > 0) {
                $result_token = Security::verifyCSRF('admin-settings-security', $token);
                if ($result_token) {

                    $admin_id = 0;
                    if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
                        $admin_id = intval(Security::decrypt($_COOKIE['UID']));
                    }

                    $res = Admin::getAdminById($admin_id);
                    if ($res->status == 200 && !empty($res->response) && !empty($res->response[0]) && $res->response[0]->admin_status == 'active') {
                        if ($files[0] == 'all') {
                            $result = Utils::backupFiles($admin_id, 'db/', 'db/');
                            Admin::SetAdminLog("export_settings");
                        } else {

                        }
                    } else {
                        print_r(json_encode(['status' => -1, 'response' => '']));
                    }
                } else {
                    print_r(json_encode(['status' => -2, 'response' => 'token_error']));
                }

            } else {
                print_r(json_encode(['status' => -3, 'response' => '']));
            }
            break;

        case 'change-poster-report-status':
            $posterId = (int)$antiXSS->xss_clean($json['posterId']);
            $reportId = (int)$antiXSS->xss_clean($json['reportId']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($posterId > 0 && $reportId > 0) {
                $result_token = Security::verifyCSRF('admin-poster-info', $token, false);
                if ($result_token) {
                    $result = Poster::changePosterReports($reportId, $posterId);
                    if ($result->status == 200) {
                        echo "successful";
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;

        case 'change-employ-info-status':
            $employID = (int)$antiXSS->xss_clean($json['employID']);
            $status = $antiXSS->xss_clean($json['status']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = ["completed", "process", "reject"];

            if (is_numeric($employID) && $employID > 0 && in_array($status, $lists) && strlen($token) > 10) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Hire::updateEmployStatusByID($employID, $status);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_change_employ_status_" . $status . "_" . $employID, "a_employ");

                    } else {
                        echo "error";
                    }

                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;


        case 'delete-employ-info':
            $employID = (int)$antiXSS->xss_clean($json['employID']);
            $token = $antiXSS->xss_clean($json['token']);

            if (is_numeric($employID) && $employID > 0 && strlen($token) > 10) {
                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {
                    $result = Hire::deleteEmployByID($employID);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_delete_employ_id_" . $employID, "a_employ");

                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;

        case 'add-desc-employ-info':
            $employID = (int)$antiXSS->xss_clean($json['employID']);
            $desc = $antiXSS->xss_clean($json['desc']);
            $token = $antiXSS->xss_clean($json['token']);

            $lists = ["completed", "process"];

            if (is_numeric($employID) && $employID > 0 && strlen($desc) > 0 && strlen($token) > 10) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Hire::updateEmployDescByID($employID, $desc);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_add_employ_desc_" . $employID, "a_employ");

                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;

        case 'hire-category-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }

            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag) {
                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {
                    $result = Hire::setNewCategoryHire($title, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("a_hire_category_add_id_" . $result->response);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;

        case 'hire-category-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }

            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Hire::editCategoryHireById($id, $title, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_hire_category_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;

        case 'change-hire-location-to-new-state':

            $country = (int)$json['country'];
            $city = $antiXSS->xss_clean($json['city']);
            $hire_id = $antiXSS->xss_clean($json['hire_id']);
            $token = $antiXSS->xss_clean($json['token']);
            $result_token = Security::verifyCSRF2($token, false);
            if ($result_token) {
                $result = Hire::editHireCountryCity($hire_id, $country, $city);
                if ($result->status == 200) {
                    echo "successful";
                    Admin::SetAdminLog("edit_hire_city" . $hire_id);
                } else {
                    echo "error";
                }
            } else {
                echo "token_error";
            }
            break;
        case 'cargo-set-admin':
            $CargoID = (int)$antiXSS->xss_clean($json['CargoID']);
            $referTd = $antiXSS->xss_clean($json['referTd']);
            $token = $antiXSS->xss_clean($json['token']);

            if (is_numeric($CargoID) && $CargoID > 0 && count($referTd) > 0 && strlen($token) > 10) {
                $result_token = Security::verifyCSRF('admin-cargo-info', $token);
                if ($result_token) {
                    $flag = true;
                    foreach ($referTd as $admin) {
                        $result = Admin::setAdminFromAssign($admin, $CargoID, 'cargo-out');
                        if ($result->status != 200) {
                            $flag = false;
                        }
                    }
                    if ($flag) {
                        echo "successful";
                        Admin::SetAdminLog("a_set_admin_from_cargo_out_id_" . $CargoID, "cargo");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;

        case 'cargo-in-set-admin':
            $CargoID = (int)$antiXSS->xss_clean($json['CargoID']);
            $referTd = $antiXSS->xss_clean($json['referTd']);
            $token = $antiXSS->xss_clean($json['token']);

            if (is_numeric($CargoID) && $CargoID > 0 && count($referTd) > 0 && strlen($token) > 10) {
                $result_token = Security::verifyCSRF('admin-cargo-in-info', $token);
                if ($result_token) {
                    $flag = true;
                    foreach ($referTd as $admin) {
                        $result = Admin::setAdminFromAssign($admin, $CargoID, 'cargo-in');
                        if ($result->status != 200) {
                            $flag = false;
                        }
                    }
                    if ($flag) {
                        echo "successful";
                        Admin::SetAdminLog("a_set_admin_from_cargo_in_id_" . $CargoID, "cargo");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;

        case 'cargo-admin-add-desc':
            $CargoID = (int)$antiXSS->xss_clean($json['CargoID']);
            $desc = $antiXSS->xss_clean($json['desc']);
            $token = $antiXSS->xss_clean($json['token']);

            if (is_numeric($CargoID) && $CargoID > 0 && strlen($desc) > 0 && strlen($token) > 10) {
                $result_token = Security::verifyCSRF('admin-cargo-info', $token);
                if ($result_token) {
                    $result = Cargo::updateCargoDescById($CargoID, $desc);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_add_desc_admin_cargo_out_" . $CargoID, "cargo");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;

        case 'cargo-in-admin-add-desc':
            $CargoID = (int)$antiXSS->xss_clean($json['CargoID']);
            $desc = $antiXSS->xss_clean($json['desc']);
            $token = $antiXSS->xss_clean($json['token']);

            if (is_numeric($CargoID) && $CargoID > 0 && strlen($desc) > 0 && strlen($token) > 10) {
                $result_token = Security::verifyCSRF('admin-cargo-in-info', $token);
                if ($result_token) {
                    $result = Cargo::updateCargoInDescById($CargoID, $desc);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_add_desc_admin_cargo_in_" . $CargoID, "cargo");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;


        case 'inquiry-customs-info' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);
            $lists = [
                "freight_price",
                "currency_id"
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = Customs::editInquiryCustomsInfoByAdmin($inquiryId, $type, $newValue);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_customs_info_" . $inquiryId, "inquiry");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }
            break;

        case 'inquiry-customs-info-statue':
            $status = $antiXSS->xss_clean($json['status']);
            $inquiryId = (int)$json['inquiryId'];
            $token = $antiXSS->xss_clean($json['token']);

            if ($inquiryId > 0) {
                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = Customs::changeInquiryCustomsStatusByAdmin($inquiryId, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_customs_status_id_" . $inquiryId);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;


        case 'inquiry-customs-info-desc-admin' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);
            $lists = [
                "freight_admin_description",
            ];

            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = Customs::InquiryCustomsAddAdminDesc($inquiryId, $newValue);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_add_desc_admin_from_inquiry_customs_id_" . $inquiryId, "inquiry");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }
            break;

        /**
         * Add New transportation
         * @author morteza
         */
        case 'transportation-add':
            $status = $antiXSS->xss_clean($json['status']);
            $title = $antiXSS->xss_clean(strip_tags($json['title']));
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $name = "";
            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }

            if ($flag == true) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {

                    $result = Customs::setNewtransportcategory($title, $status);
                    if ($result->status == 200) {
                        echo "successful " . $result->response;
                        Admin::SetAdminLog("transportation_add_title_" . $name);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }

            break;


        /**
         * Edit transportation
         * @author morteza
         */
        case 'transportation-edit':
            $status = $antiXSS->xss_clean($json['status']);
            $id = (int)$json['id'];
            $title = $antiXSS->xss_clean($json['title']);
            $token = $antiXSS->xss_clean($json['token']);

            if ($status != 'active') {
                $status = "inactive";
            }


            $flag = true;
            foreach (json_decode($title) as $titleITEM) {
                $name = $titleITEM->value;
                if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                    $flag = false;
                }
            }


            if ($flag == true && $id != 0 && strlen($id) > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {

                    $result = Customs::editTransportationById($id, $title, $status);

                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("transportation_edit_id_" . $id);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo "empty";
            }

            break;
        case 'admin-url' :
            $value = isset($json['value']) ? $antiXSS->xss_clean($json['value']) : null;
            if (!is_null($value)) {
                Admin::SetAdminUrl($value);
            }
            break;
        /***
         * start driver services
         */
        case 'change-driver-cv-status':
            $cv_id = $antiXSS->xss_clean($json['cv_id']);
            $status = $antiXSS->xss_clean($json['status']);
            $token = $antiXSS->xss_clean($json['token']);

            $list_status = [
                "accepted",
                "pending",
                "rejected",
            ];
            if (in_array($status, $list_status) && strlen($cv_id) > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {
                    $result = CV::updateCvStatus($cv_id, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_cv_status_to_" . $status . "_" . $cv_id);
                    } else {
                        print_r($result);
                    }
                } else {
                    echo "token_error";
                }

            } else {
                echo 'empty';
            }

            break;
        case 'reject-driver-cv':
            $cv_id = $antiXSS->xss_clean($json['cv_id']);
            $status = $antiXSS->xss_clean($json['status']);
            $reject_desc = $antiXSS->xss_clean($json['reject_desc']);
            $token = $antiXSS->xss_clean($json['token']);

            $list_status = [
                "accepted",
                "pending",
                "rejected",
            ];
            if (in_array($status, $list_status) && strlen($cv_id) > 0) {

                $result_token = Security::verifyCSRF2($token);
                if ($result_token) {
                    $result = CV::rejectCv($cv_id, $status, $reject_desc);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_cv_status_to_" . $status . "_" . $cv_id);
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
        /***
         * inuqery minicargo
         */
        case 'inquiry-minicargo-info' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);
            $lists = [
                "freight_price",
                "currency_id"
            ];
            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = MiniCargo::editInquiryMiniCargoInfoByAdmin($inquiryId, $type, $newValue);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_minicargo_info_" . $inquiryId, "inquiry");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }
            break;

        case 'inquiry-minicargo-info-statue':
            $status = $antiXSS->xss_clean($json['status']);
            $inquiryId = (int)$json['inquiryId'];
            $token = $antiXSS->xss_clean($json['token']);

            if ($inquiryId > 0) {
                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = MiniCargo::changeInquiryMiniCargoStatusByAdmin($inquiryId, $status);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("change_inquiry_minicargo_status_id_" . $inquiryId);
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            } else {
                echo "empty";
            }
            break;


        case 'inquiry-minicargo-info-desc-admin' :
            $type = $antiXSS->xss_clean($json['type']);
            $newValue = $antiXSS->xss_clean($json['value']);
            $inquiryId = (int)$antiXSS->xss_clean($json['inquiryId']);
            $token = $antiXSS->xss_clean($json['token']);
            $lists = [
                "freight_admin_description",
            ];

            $newValue = trim($newValue);
            if (in_array($type, $lists) && strlen($newValue) > 0 && $inquiryId > 0) {

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $result = MiniCargo::InquiryMiniCargoAddAdminDesc($inquiryId, $newValue);
                    if ($result->status == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_add_desc_admin_from_inquiry_MiniCargo_id_" . $inquiryId, "inquiry");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
            }
            break;
        /**
         * start phone book
         */
        case 'add-phone-book':

            $user_types = isset($json['user_types']) ? $antiXSS->xss_clean($json['user_types']) : null;
            $user_name = isset($json['user_name']) ? $antiXSS->xss_clean($json['user_name']) : null;
            $user_lname = isset($json['user_lname']) ? $antiXSS->xss_clean($json['user_lname']) : null;
            $user_phone = isset($json['user_phone']) ? $antiXSS->xss_clean($json['user_phone']) : null;
            $user_home_number = isset($json['user_home_number']) ? $antiXSS->xss_clean($json['user_home_number']) : null;
            $company_name = isset($json['company_name']) ? $antiXSS->xss_clean($json['company_name']) : null;
            $member_zone = isset($json['member_zone']) ? $antiXSS->xss_clean($json['member_zone']) : null;
            $member_status = isset($json['member_status']) ? $antiXSS->xss_clean($json['member_status']) : null;
            $car_types = isset($json['car_types']) ? $antiXSS->xss_clean($json['car_types']) : null;
            $fav_countries = isset($json['fav_countries']) ? $antiXSS->xss_clean($json['fav_countries']) : [];
            $activity_summery = isset($json['activity_summery']) ? $antiXSS->xss_clean($json['activity_summery']) : null;
            $token = isset($json['token']) ? $antiXSS->xss_clean($json['token']) : null;

            if (isset($user_phone) && Security::verifyCSRF2($token, false)) {
                $check_user = PhoneBook::checkPhoneBookExists($user_phone);
                if ($check_user->response == 0) {
                    $result = PhoneBook::addPhoneBook($user_types, $user_name, $user_lname, $user_phone, $user_home_number, $company_name,
                        $member_zone, $member_status, $car_types, $fav_countries, $activity_summery);
//                    Admin::SetAdminLog("add_phone_book_" . $id);
                    echo json_encode(sendResponse(200, 'pb_id', $result->response));
                } else {
                    echo json_encode(sendResponse(201, 'user-exits'));
                }
            } else {
                echo json_encode(sendResponse(411, '', ''));
            }

            break;
        case 'edit-phone-book':

            $p_id = isset($json['p_id']) ? $antiXSS->xss_clean($json['p_id']) : null;
            $user_types = isset($json['user_types']) ? $antiXSS->xss_clean($json['user_types']) : null;
            $user_name = isset($json['user_name']) ? $antiXSS->xss_clean($json['user_name']) : null;
            $user_lname = isset($json['user_lname']) ? $antiXSS->xss_clean($json['user_lname']) : null;
            $user_phone = isset($json['user_phone']) ? $antiXSS->xss_clean($json['user_phone']) : null;
            $user_home_number = isset($json['user_home_number']) ? $antiXSS->xss_clean($json['user_home_number']) : null;
            $company_name = isset($json['company_name']) ? $antiXSS->xss_clean($json['company_name']) : null;
            $member_zone = isset($json['member_zone']) ? $antiXSS->xss_clean($json['member_zone']) : null;
            $member_status = isset($json['member_status']) ? $antiXSS->xss_clean($json['member_status']) : null;
            $car_types = isset($json['car_types']) ? $antiXSS->xss_clean($json['car_types']) : null;
            $fav_countries = isset($json['fav_countries']) ? $antiXSS->xss_clean($json['fav_countries']) : [];
            $activity_summery = isset($json['activity_summery']) ? $antiXSS->xss_clean($json['activity_summery']) : null;
            $token = isset($json['token']) ? $antiXSS->xss_clean($json['token']) : null;

            if (isset($p_id) && Security::verifyCSRF2($token, false)) {

                $result = PhoneBook::editPhoneBook($p_id, $user_types, $user_name, $user_lname, $user_phone, $user_home_number, $company_name,
                    $member_zone, $member_status, $car_types, $fav_countries, $activity_summery);
//                    Admin::SetAdminLog("add_phone_book_" . $id);

                echo json_encode(sendResponse(200, 'pb_id', $result->response));

            } else {
                echo json_encode(sendResponse(411, '', ''));
            }

            break;
        case 'get-phone-books':

            $search_keys = isset($json['search_keys']) ? $antiXSS->xss_clean($json['search_keys']) : 'all-item';
            $status = isset($json['status']) ? $antiXSS->xss_clean($json['status']) : 'all';
            $user_type = isset($json['user_type']) ? $antiXSS->xss_clean($json['user_type']) : 'all';
            $country = isset($json['country']) ? $antiXSS->xss_clean($json['country']) : 'all';
            $car_type = isset($json['car_type']) ? $antiXSS->xss_clean($json['car_type']) : 'all';
            $cargo_type = isset($json['cargo_type']) ? $antiXSS->xss_clean($json['cargo_type']) : 'all';
            $page = isset($json['page']) ? $antiXSS->xss_clean($json['page']) : 'all';
            $token = isset($json['token']) ? $antiXSS->xss_clean($json['token']) : null;

            if (isset($search_keys) && Security::verifyCSRF2($token, false)) {
                $result = PhoneBook::getPhoneBooks($search_keys, $status, $user_type, $country, $car_type, $cargo_type , $page);
                if ($result->status == 200) {
                    $output = '';
                    foreach ($result->response as $item) {
                        $isRegister = (User::checkUserRegister($item->pb_phone) == 1) ? '' : 'd-none';
                        $isRegister2 = (User::checkUserRegister($item->pb_home_number) == 1) ? '' : 'd-none';

                        /*  $is_user_type = ($item->pb_user_type == 'driver') ? 'driver' : 'businessman';
                          $is_user_type2 = ($item->pb_user_type == 'driver') ? '' : 'businessman';*/


                        if ($item->pb_user_type == 'driver') {
                            $user_type_name = $lang['driver'];
                            $is_user_type = 'driver';
                        } elseif ($item->pb_user_type == 'businessman') {
                            $user_type_name = $lang['businessman'];
                            $is_user_type = 'businessman';
                        } elseif ($item->pb_user_type == 'transportation_company') {
                            $user_type_name = $lang['transportation_company'];
                            $is_user_type = 'transportation_company';
                        } elseif ($item->pb_user_type == 'dealer') {
                            $user_type_name = $lang['dealer'];
                            $is_user_type = 'dealer';
                        } elseif ($item->pb_user_type == 'shiping') {
                            $user_type_name = $lang['shiping'];
                            $is_user_type = 'shiping';
                        } elseif ($item->pb_user_type == 'dischager') {
                            $user_type_name = $lang['dischager'];
                            $is_user_type = 'dischager';
                        } elseif ($item->pb_user_type == 'keeper') {
                            $user_type_name = $lang['keeper'];
                            $is_user_type = 'keeper';
                        } elseif ($item->pb_user_type == 'other') {
                            $user_type_name = $lang['other'];
                            $is_user_type = 'other';
                        } elseif ($item->pb_user_type == 'guest') {
                            $user_type_name = $lang['guest'];
                            $is_user_type = 'guest';
                        } else {
                            $user_type_name = $lang['guest'];
                            $is_user_type = 'guest';
                        }
                        $display_home_number = empty($item->pb_home_number) ? '<span id="second-number">' . $lang['home_number_not_found'] . '</span>' : $item->pb_home_number;

                        $pb_desc = PhoneBook::getPhoneBookDesc($item->pb_id);
                        $pb_desc = $pb_desc->status == 200 ? $pb_desc->response[0] : [];


                        if (isset($pb_desc->desc_text)) {
                            $last_call = Utils::getTimeByLangWithHour($pb_desc->desc_create_at);
                            $last_activity = ($pb_desc->desc_text);
                        } else {
                            $last_call = 'test';
                            $last_activity = 'test';
                        }


                        $access_type_output = '';

                        if ($item->pb_access_type == 'access') {
                            $access_type_output = '
                           <div class="mj-member-card-access-icon ">
                                            <img src="/dist/images/admin/pbook-access.svg" alt="nti">
                                        </div>
                        ';
                        } else {
                            $access_type_output = '
                          <div class="mj-member-card-access-icon notaccess ">
                                            <img src="/dist/images/admin/pbook-noaccess.svg" alt="nti">
                                        </div>
                        ';
                        }

                        $complete_doc_output = '';
                        if (isset($item->pb_username) && isset($item->pb_user_lname) && isset($item->pb_phone) && isset($item->pb_fav_country) && isset($item->pb_car_type) && isset($item->pb_access_type)) {
                            $complete_doc_output = '
                            <div class="mj-member-card-docs-icon ">
                                            <img src="/dist/images/admin/pbook-comdoc.svg" alt="nti">
                                        </div>
                            ';
                        } else {
                            $complete_doc_output = '
                            <div class="mj-member-card-docs-icon notcompleted">
                                            <img src="/dist/images/admin/pbook-notcomdoc.svg" alt="nti">
                                        </div>
                                        ';
                        }

                        $new_member_badge = '';
                        if (isset($pb_desc->desc_create_at)) {
                            $new_member_badge = '   <div class="mj-pbook-new-badge">
                                        <span>' . $lang["new_member_badge"] . '</span>
                                        </div>';
                        }
                        $output .= '  <a href="pbookedit/' . $item->pb_id . '">
                                <div class="mj-pbook-member-card">
                                    <div class="mj-pbook-member-first-row">
                                        <div class="mj-member-mobile-num"><bdi>' . $item->pb_phone . '</bdi>
                                    
                                          <div class="mj-member-card-signed-icon ' . $isRegister . '" >
                                            <img src="/dist/images/admin/pbook-nti.svg" alt="nti">
                                        </div>
                                        </div>
                                        ' . $access_type_output . '
                                        ' . $complete_doc_output . '
                                        
                                    </div>
                                     <div class="mj-pbook-member-second-row ">
                                        <div class="mj-member-mobile-num"><bdi>' . $display_home_number . '</bdi>
                                        
                                        <div class="mj-member-card-signed-icon ' . $isRegister2 . '" >  <img src="/dist/images/admin/pbook-nti.svg" alt="nti">
                                        </div>
                                        
                                        </div>
                                        
                                        
                                       
                <div class="mj-member-user-type">
                                            <div class="mj-member-driver-icon ' . $is_user_type . '">
                                          
                                        <img src="/dist/images/admin/pbook-' . $is_user_type . '.svg" alt="nti">
                                        <div>' . $user_type_name . '</div>
                                            </div>
                                        </div>
                                      
                                     ' . $new_member_badge . '
                                    </div>
                                 
                                      <div class="mj-pbook-member-second-row ">
                                        <div class="mj-member-f-l-name">' . $item->pb_username . ' ' . $item->pb_user_lname . '</div>
                                       </div>
                                    <div class="mj-pbook-member-third-row">
                                        <div class="mj-member-last-seen">
                                            <span>' . $lang["pb_last_call"] . '</span>
                                            <span>
                                            <span><bdi>' . $last_call . '</bdi></span>
                                        </span>
                                        </div>
                                    </div>
                                    
                                    <div id="mj-last-change" class=" mj-member-last-change-card mj-pbook-member-third-row">
                                        <div class="mj-member-last-change">
                                            <span class="d-block mb-2">' . $lang["pb_last_activity"] . '</span>
                                            <span>
                                            <span>' . $last_activity . '</span>
                                        </span>
                                        </div>
                                    </div>
                                </div>
                            </a>';
                    }

                    echo json_encode(sendResponse(200, $result->message, $output));
                } else {
                    echo json_encode(sendResponse(403, ' ', $result));

                }
            } else {
                echo json_encode(sendResponse(411, '', ''));
            }

            break;
        case 'send-sms-phone-books':

            $search_keys = isset($json['search_keys']) ? $antiXSS->xss_clean($json['search_keys']) : 'all-item';
            $sms_text = isset($json['sms_text']) ? $antiXSS->xss_clean($json['sms_text']) : 'all-item';
            $status = isset($json['status']) ? $antiXSS->xss_clean($json['status']) : 'all';
            $user_type = isset($json['user_type']) ? $antiXSS->xss_clean($json['user_type']) : 'all';
            $country = isset($json['country']) ? $antiXSS->xss_clean($json['country']) : 'all';
            $car_type = isset($json['car_type']) ? $antiXSS->xss_clean($json['car_type']) : 'all';
            $cargo_type = isset($json['cargo_type']) ? $antiXSS->xss_clean($json['cargo_type']) : 'all';
            $token = isset($json['token']) ? $antiXSS->xss_clean($json['token']) : null;

            if (isset($search_keys) && Security::verifyCSRF2($token, false)) {
                $result = PhoneBook::sendSmsPhoneBooks($search_keys, $status, $user_type, $country, $car_type, $cargo_type);
                if ($result->status == 200) {
                    $phone_numbers =[];
                    foreach ($result->response as $item)  {
                        $phone_numners[]= $item->pb_phone ;
                        SMS::sendSMS([$item->pb_phone] ,$sms_text );
                    }

                    echo json_encode(sendResponse(200, ' ', $phone_numners));
                } else {
                    echo json_encode(sendResponse(403, ' ', $result));

                }
            } else {
                echo json_encode(sendResponse(411, '', ''));
            }

            break;
        case 'delete-phone-book':

            $p_id = isset($json['p_id']) ? $antiXSS->xss_clean($json['p_id']) : null;
            $token = isset($json['token']) ? $antiXSS->xss_clean($json['token']) : null;

            if (isset($p_id) && Security::verifyCSRF2($token, false)) {

                $result = PhoneBook::deletePhoneBook($p_id);
                Admin::SetAdminLog("delete_phone_book_" . $p_id);
                echo json_encode(sendResponse(200, 'pb_id', $result));
            } else {
                echo json_encode(sendResponse(411, '', ''));
            }

            break;
        case 'change-price-status':


            $price_id = isset($json['price_id']) ? $antiXSS->xss_clean($json['price_id']) : null;
            $ir_price_status = isset($json['ir_price_status']) ? $antiXSS->xss_clean($json['ir_price_status']) : null;
            $ru_price_status = isset($json['ru_price_status']) ? $antiXSS->xss_clean($json['ru_price_status']) : null;
            $du_price_status = isset($json['du_price_status']) ? $antiXSS->xss_clean($json['du_price_status']) : null;
            $tr_price_status = isset($json['tr_price_status']) ? $antiXSS->xss_clean($json['tr_price_status']) : null;
            $ir_plus_value = isset($json['ir_plus_value']) ? $antiXSS->xss_clean($json['ir_plus_value']) : null;
            $ru_plus_value = isset($json['ru_plus_value']) ? $antiXSS->xss_clean($json['ru_plus_value']) : null;
            $du_plus_value = isset($json['du_plus_value']) ? $antiXSS->xss_clean($json['du_plus_value']) : null;
            $tr_plus_value = isset($json['tr_plus_value']) ? $antiXSS->xss_clean($json['tr_plus_value']) : null;
            $ir_minus_value = isset($json['ir_minus_value']) ? $antiXSS->xss_clean($json['ir_minus_value']) : null;
            $ru_minus_value = isset($json['ru_minus_value']) ? $antiXSS->xss_clean($json['ru_minus_value']) : null;
            $du_minus_value = isset($json['du_minus_value']) ? $antiXSS->xss_clean($json['du_minus_value']) : null;
            $tr_minus_value = isset($json['tr_minus_value']) ? $antiXSS->xss_clean($json['tr_minus_value']) : null;


            $token = isset($json['token']) ? $antiXSS->xss_clean($json['token']) : null;

            if (isset($price_id) && Security::verifyCSRF2($token, false)) {

                $result = Exchange::updatePriceSetting($price_id, $ir_price_status, $ru_price_status, $du_price_status, $tr_price_status, $ir_plus_value, $ru_plus_value, $du_plus_value, $tr_plus_value, $ir_minus_value, $ru_minus_value, $du_minus_value, $tr_minus_value);
                Admin::SetAdminLog("change_price_setting" . $price_id);
                echo json_encode(sendResponse(200, '', $result));
            } else {
                echo json_encode(sendResponse(411, '', ''));
            }

            break;
        /**
         * start personel
         */
        case 'add-personel':
            $personel_name_fa_IR = isset($json['personel_name_fa_IR']) ? $antiXSS->xss_clean($json['personel_name_fa_IR']) : null;
            $personel_name_en_US = isset($json['personel_name_en_US']) ? $antiXSS->xss_clean($json['personel_name_en_US']) : null;
            $personel_name_tr_Tr = isset($json['personel_name_tr_Tr']) ? $antiXSS->xss_clean($json['personel_name_tr_Tr']) : null;
            $personel_name_ru_RU = isset($json['personel_name_ru_RU']) ? $antiXSS->xss_clean($json['personel_name_ru_RU']) : null;
            $personel_lname_fa_IR = isset($json['personel_lname_fa_IR']) ? $antiXSS->xss_clean($json['personel_lname_fa_IR']) : null;
            $personel_lname_en_US = isset($json['personel_lname_en_US']) ? $antiXSS->xss_clean($json['personel_lname_en_US']) : null;
            $personel_lname_tr_Tr = isset($json['personel_lname_tr_Tr']) ? $antiXSS->xss_clean($json['personel_lname_tr_Tr']) : null;
            $personel_lname_ru_RU = isset($json['personel_lname_ru_RU']) ? $antiXSS->xss_clean($json['personel_lname_ru_RU']) : null;
            $personel_job_fa_IR = isset($json['personel_job_fa_IR']) ? $antiXSS->xss_clean($json['personel_job_fa_IR']) : null;
            $personel_job_en_US = isset($json['personel_job_en_US']) ? $antiXSS->xss_clean($json['personel_job_en_US']) : null;
            $personel_job_tr_Tr = isset($json['personel_job_tr_Tr']) ? $antiXSS->xss_clean($json['personel_job_tr_Tr']) : null;
            $personel_job_ru_RU = isset($json['personel_job_ru_RU']) ? $antiXSS->xss_clean($json['personel_job_ru_RU']) : null;
            $personel_email = isset($json['personel_email']) ? $antiXSS->xss_clean($json['personel_email']) : null;
            $phone = isset($json['phone']) ? $antiXSS->xss_clean($json['phone']) : null;
            $home_numer = isset($json['home_numer']) ? $antiXSS->xss_clean($json['home_numer']) : null;
            $whatsapp = isset($json['whatsapp']) ? $antiXSS->xss_clean($json['whatsapp']) : null;
            $phone_country_code = isset($json['phone_country_code']) ? $antiXSS->xss_clean($json['phone_country_code']) : null;
            $home_country_code = isset($json['home_country_code']) ? $antiXSS->xss_clean($json['home_country_code']) : null;
            $whatsapp_country_code = isset($json['whatsapp_country_code']) ? $antiXSS->xss_clean($json['whatsapp_country_code']) : null;
            $personel_ref_code = isset($json['personel_ref_code']) ? $antiXSS->xss_clean($json['personel_ref_code']) : null;
            $personel_desc_fa_IR = isset($json['personel_desc_fa_IR']) ? $antiXSS->xss_clean($json['personel_desc_fa_IR']) : null;
            $personel_desc_en_US = isset($json['personel_desc_en_US']) ? $antiXSS->xss_clean($json['personel_desc_en_US']) : null;
            $personel_desc_tr_Tr = isset($json['personel_desc_tr_Tr']) ? $antiXSS->xss_clean($json['personel_desc_tr_Tr']) : null;
            $personel_desc_ru_RU = isset($json['personel_desc_ru_RU']) ? $antiXSS->xss_clean($json['personel_desc_ru_RU']) : null;
            $personel_avatar = isset($json['personel_avatar']) ? $antiXSS->xss_clean($json['personel_avatar']) : null;
            $token = isset($json['token']) ? $antiXSS->xss_clean($json['token']) : null;
            if (isset($phone) && isset($personel_ref_code) && Security::verifyCSRF2($token, false)) {
                $personel_avatar_url = '';
                if (!empty($personel_avatar)) {
                    $upload = Upload::uploadBase64($personel_avatar, PERSONEL_DOCS_ADDRESS);
                    if ($upload->status == 200) {
                        $personel_avatar_url = $upload->response;
                    }
                }
                if (CV::checkPersonelRefCodeExists($personel_ref_code) == 0) {
                    $result = CV::addPersonels($personel_name_fa_IR, $personel_name_en_US, $personel_name_tr_Tr, $personel_name_ru_RU,
                        $personel_lname_fa_IR, $personel_lname_en_US, $personel_lname_tr_Tr, $personel_lname_ru_RU,
                        $personel_job_fa_IR, $personel_job_en_US, $personel_job_tr_Tr, $personel_job_ru_RU, $personel_email, $phone,
                        $home_numer, $whatsapp, $phone_country_code, $home_country_code, $whatsapp_country_code, $personel_ref_code,
                        $personel_desc_fa_IR, $personel_desc_en_US, $personel_desc_tr_Tr, $personel_desc_ru_RU, $personel_avatar_url);
                    echo json_encode(sendResponse(200, 'pb_id', $result));
                } else {
                    echo json_encode(sendResponse(402, 'ref code is exists', ''));
                }

            } else {
                echo json_encode(sendResponse(411, '', ''));
            }
            break;
        case 'edit-personel':

            $personel_id = isset($json['personel_id']) ? $antiXSS->xss_clean($json['personel_id']) : null;
            $personel_name_fa_IR = isset($json['personel_name_fa_IR']) ? $antiXSS->xss_clean($json['personel_name_fa_IR']) : null;
            $personel_name_en_US = isset($json['personel_name_en_US']) ? $antiXSS->xss_clean($json['personel_name_en_US']) : null;
            $personel_name_tr_Tr = isset($json['personel_name_tr_Tr']) ? $antiXSS->xss_clean($json['personel_name_tr_Tr']) : null;
            $personel_name_ru_RU = isset($json['personel_name_ru_RU']) ? $antiXSS->xss_clean($json['personel_name_ru_RU']) : null;
            $personel_lname_fa_IR = isset($json['personel_lname_fa_IR']) ? $antiXSS->xss_clean($json['personel_lname_fa_IR']) : null;
            $personel_lname_en_US = isset($json['personel_lname_en_US']) ? $antiXSS->xss_clean($json['personel_lname_en_US']) : null;
            $personel_lname_tr_Tr = isset($json['personel_lname_tr_Tr']) ? $antiXSS->xss_clean($json['personel_lname_tr_Tr']) : null;
            $personel_lname_ru_RU = isset($json['personel_lname_ru_RU']) ? $antiXSS->xss_clean($json['personel_lname_ru_RU']) : null;
            $personel_job_fa_IR = isset($json['personel_job_fa_IR']) ? $antiXSS->xss_clean($json['personel_job_fa_IR']) : null;
            $personel_job_en_US = isset($json['personel_job_en_US']) ? $antiXSS->xss_clean($json['personel_job_en_US']) : null;
            $personel_job_tr_Tr = isset($json['personel_job_tr_Tr']) ? $antiXSS->xss_clean($json['personel_job_tr_Tr']) : null;
            $personel_job_ru_RU = isset($json['personel_job_ru_RU']) ? $antiXSS->xss_clean($json['personel_job_ru_RU']) : null;
            $personel_email = isset($json['personel_email']) ? $antiXSS->xss_clean($json['personel_email']) : null;
            $phone = isset($json['phone']) ? $antiXSS->xss_clean($json['phone']) : null;
            $home_numer = isset($json['home_numer']) ? $antiXSS->xss_clean($json['home_numer']) : null;
            $whatsapp = isset($json['whatsapp']) ? $antiXSS->xss_clean($json['whatsapp']) : null;
            $phone_country_code = isset($json['phone_country_code']) ? $antiXSS->xss_clean($json['phone_country_code']) : null;
            $home_country_code = isset($json['home_country_code']) ? $antiXSS->xss_clean($json['home_country_code']) : null;
            $whatsapp_country_code = isset($json['whatsapp_country_code']) ? $antiXSS->xss_clean($json['whatsapp_country_code']) : null;
            $personel_ref_code = isset($json['personel_ref_code']) ? $antiXSS->xss_clean($json['personel_ref_code']) : null;
            $personel_desc_fa_IR = isset($json['personel_desc_fa_IR']) ? $antiXSS->xss_clean($json['personel_desc_fa_IR']) : null;
            $personel_desc_en_US = isset($json['personel_desc_en_US']) ? $antiXSS->xss_clean($json['personel_desc_en_US']) : null;
            $personel_desc_tr_Tr = isset($json['personel_desc_tr_Tr']) ? $antiXSS->xss_clean($json['personel_desc_tr_Tr']) : null;
            $personel_desc_ru_RU = isset($json['personel_desc_ru_RU']) ? $antiXSS->xss_clean($json['personel_desc_ru_RU']) : null;
            $personel_avatar = isset($json['personel_avatar']) ? $antiXSS->xss_clean($json['personel_avatar']) : null;
            $token = isset($json['token']) ? $antiXSS->xss_clean($json['token']) : null;
            if (isset($personel_id) && isset($personel_ref_code) && Security::verifyCSRF2($token, false)) {
                $personel_avatar_url = '';
                if (!empty($personel_avatar)) {

                    if (str_contains($personel_avatar, 'uploads/personel')) {
                        $personel_avatar_url = $personel_avatar;
                    } else {
                        $upload = Upload::uploadBase64($personel_avatar, PERSONEL_DOCS_ADDRESS);
                        if ($upload->status == 200) {
                            $personel_avatar_url = $upload->response;
                        }
                    }

                }
//                if(CV::checkPersonelRefCodeExists($personel_ref_code) ==0){
                $result = CV::editPersonels($personel_id, $personel_name_fa_IR, $personel_name_en_US, $personel_name_tr_Tr, $personel_name_ru_RU,
                    $personel_lname_fa_IR, $personel_lname_en_US, $personel_lname_tr_Tr, $personel_lname_ru_RU, $personel_job_fa_IR, $personel_job_en_US,
                    $personel_job_tr_Tr, $personel_job_ru_RU, $personel_email, $phone, $home_numer, $whatsapp, $phone_country_code, $home_country_code,
                    $whatsapp_country_code, $personel_ref_code, $personel_desc_fa_IR, $personel_desc_en_US, $personel_desc_tr_Tr, $personel_desc_ru_RU, $personel_avatar_url);
                echo json_encode(sendResponse(200, 'pb_id', $result));
//                }else{
//                    echo json_encode(sendResponse(402, 'ref code is exists', ''));
//                }

            } else {
                echo json_encode(sendResponse(411, '', ''));
            }
            break;
        case 'delete-personel':

            $personel_id = isset($json['personel_id']) ? $antiXSS->xss_clean($json['personel_id']) : null;

            $token = isset($json['token']) ? $antiXSS->xss_clean($json['token']) : null;
            if (isset($personel_id) && Security::verifyCSRF2($token, false)) {


                $result = CV::deletePersonels($personel_id );
                echo json_encode(sendResponse(200, 'success', $result));


            } else {
                echo json_encode(sendResponse(411, '', ''));
            }
            break;
        case 'submit-exchange-request-description':


            $request_id = ($json['request_id']) ? $antiXSS->xss_clean($json['request_id']) : null;
            $admin_description = ($json['admin_description']) ? $antiXSS->xss_clean($json['admin_description']) : null;
            $token = ($json['token']) ? $antiXSS->xss_clean($json['token']) : null;
            if ($request_id && $admin_description && Security::verifyCSRF2($token, false)) {

               $result =  Exchange::insertRequestDescription($request_id , $admin_description) ;

                echo json_encode($result);


            } else {
                echo json_encode(sendResponse(411, '', ''));
            }
            break;
        case 'change-exchange-request-status':


            $request_id = ($json['request_id']) ? $antiXSS->xss_clean($json['request_id']) : null;
            $request_status = ($json['request_status']) ? $antiXSS->xss_clean($json['request_status']) : null;
            $token = ($json['token']) ? $antiXSS->xss_clean($json['token']) : null;
            if ($request_id && $request_status && Security::verifyCSRF2($token, false)) {

                $result =  Exchange::updateRequestStatus($request_id , $request_status) ;

                echo json_encode($result);


            } else {
                echo json_encode(sendResponse(411, '', ''));
            }
            break;
        case 'change-poster-title':


            $language = ($json['language']) ? $antiXSS->xss_clean($json['language']) : null;
            $poster_id = ($json['poster_id']) ? $antiXSS->xss_clean($json['poster_id']) : null;
            $title = ($json['title']) ? $antiXSS->xss_clean($json['title']) : null;


            if ($language && $poster_id && $title) {

                $result =  Poster::updatePosterTitle($language , $poster_id , $title) ;

                echo json_encode($result);


            } else {
                echo json_encode(sendResponse(411, '', ''));
            }
            break;
        case 'get-country-list':


            echo json_encode(Location::getCountriesListM());

            break;


        case 'get-cities-list':
            $country_id = $json['country_id'] ? $antiXSS->xss_clean($json['country_id'] ) : null;


            echo json_encode(Location::getCitiesM($country_id));

            break;
        case 'insert-office':
            $mobile = $json['mobile'] ? $antiXSS->xss_clean($json['mobile'] ) : null;
            $email = $json['email'] ? $antiXSS->xss_clean($json['email'] ) : null;
            $city = $json['city'] ? $antiXSS->xss_clean($json['city'] ) : null;


            echo json_encode(Office::insertOffice( $mobile, $email, $city));

            break;

        case 'update-office':
            $office_id = $json['office_id'] ? $antiXSS->xss_clean($json['office_id'] ) : null;
            $mobile = $json['mobile'] ? $antiXSS->xss_clean($json['mobile'] ) : null;
            $email = $json['email'] ? $antiXSS->xss_clean($json['email'] ) : null;
            $city = $json['city'] ? $antiXSS->xss_clean($json['city'] ) : null;


            echo json_encode(Office::updateOffice( $office_id ,$mobile, $email, $city));

            break;

        case 'update-user-refferal-code':
            $user_refferal = $json['user_refferal'] ? $antiXSS->xss_clean($json['user_refferal'] ) : null;
            $user_id = $json['user_id'] ? $antiXSS->xss_clean($json['user_id'] ) : null;



            echo json_encode( User::updateUserRefferal($user_id   , $user_refferal));

            break;
        case 'get-international-cargo-detail':
            $cargo_id = $json['cargo_id'] ? $antiXSS->xss_clean($json['cargo_id'] ) : null;
            echo json_encode(   Cargo::getInternationalCargoDetail($cargo_id));
            break;
        case 'get-cargo-categories':

            echo json_encode(   Cargo::getAllCargoCategory());
            break;
        case 'get-all-car-type':

            echo json_encode(   Car::getAllCarsTypes());
            break;
        case 'get-all-counties':

            echo json_encode(  Location::getCountriesListM());
            break;
        case 'get-cities-by-country':
            $country_id = $json['country_id'] ? $antiXSS->xss_clean($json['country_id'] ) : null;
            echo json_encode(  Location::getCitiesByCountry($country_id,'ground'));
            break;
        default :

    }


} else {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {


            /**
             * Add New Ticket To Room
             * @author tjavan
             */
            case 'set-ticket-exist-room':
                $userID = $antiXSS->xss_clean($_POST['userID']);
                $roomID = $antiXSS->xss_clean($_POST['roomID']);
                $massage = $antiXSS->xss_clean($_POST['massage']);
                $token = $antiXSS->xss_clean($_POST['token']);
                $files = $antiXSS->xss_clean($_FILES);


                if (strlen($userID) > 0 && strlen($roomID) > 0 && strlen($massage) > 9) {

                    $result_token = Security::verifyCSRF('admin-set-ticket-exist-room', $token);
                    if ($result_token) {

                        $array = [];
                        $flag = true;
                        if (!empty($files)) {

                            if (!file_exists(getcwd() . TICKET_ADDRESS) && !is_dir(getcwd() . TICKET_ADDRESS)) {
                                mkdir(getcwd() . TICKET_ADDRESS);
                            }

                            foreach ($files as $filesLoop) {
                                $result_upload = Upload::upload($filesLoop, TICKET_ADDRESS);
//                            print_r($result_upload);
                                if ($result_upload->status == 200) {
                                    $myObj = new stdClass();
                                    $myObj->attachment = $result_upload->response;
                                    $myJSON = ($myObj);
                                    array_push($array, $myJSON);
                                } else {
                                    $flag = false;
                                }
                            }

                        }


                        $result = ATicket::setNewTicketExistRoom($roomID, $userID, $massage, json_encode($array));
                        if ($result->status == 200) {
                            echo "successful";
                            Admin::SetAdminLog("send_ticket", "send_ticket");
                        } else {
                            echo "error";
                        }

                    } else {
                        echo "token_error";
                    }


                } else {
                    echo "empty";
                }


                break;


            case 'set-new-ticket-and-room':
                $userID = $antiXSS->xss_clean($_POST['userID']);
                $massage = $antiXSS->xss_clean($_POST['massage']);
                $title = $antiXSS->xss_clean($_POST['title']);
                $departments = $antiXSS->xss_clean($_POST['departments']);
                $token = $antiXSS->xss_clean($_POST['token']);
                $files = $antiXSS->xss_clean($_FILES);


                if (strlen($title) > 2 && strlen($userID) > 0 && strlen($massage) > 9) {

                    $result_token = Security::verifyCSRF('admin-set-new-ticket-and-room', $token);
                    if ($result_token) {

                        $array = [];
                        $flag = true;
                        if (!empty($files)) {

                            if (!file_exists(getcwd() . TICKET_ADDRESS) && !is_dir(getcwd() . TICKET_ADDRESS)) {
                                mkdir(getcwd() . TICKET_ADDRESS);
                            }

                            foreach ($files as $filesLoop) {
                                $result_upload = Upload::upload($filesLoop, TICKET_ADDRESS);
                                if ($result_upload->status == 200) {
                                    $myObj = new stdClass();
                                    $myObj->attachment = $result_upload->response;
                                    $myJSON = ($myObj);
                                    array_push($array, $myJSON);
                                } else {
                                    $flag = false;
                                }
                            }

                        }

                        $result = ATicket::setNewTicketAndRoom($userID, $title, $massage, $departments, json_encode($array));

                        if ($result->status == 200) {
                            echo "successful " . $result->response;
                            Admin::SetAdminLog('add_new_ticket_title_' . $title, "send_ticket");
                        } else {
                            echo "error";
                        }
                    } else {
                        echo "token_error";
                    }

                } else {
                    echo "empty";
                }


                break;


            /**
             * Set Or Update whatsapp default text
             */
            case 'whatsapp-default-text':
                $values = $_POST;
                array_splice($values, 0, 1);


                $result_token = Security::verifyCSRF2($values['token'], false);
                if ($result_token) {
                    array_splice($values, 0, 1);
                    $resultSettings = Utils::getFileValue("settings.txt", 'text_share_whatsapp');

                    $dataSettings = [];
                    if (!empty($resultSettings)) {
                        $dataSettings = json_decode($resultSettings, true);
                    }


                    $temp = [];
                    foreach ($values as $key => $item) {

                        $item = json_decode($item);
                        if (isset($item->title) && !empty($item->desc)) {

                            $temp0 = [
                                'title' => $item->title,
                                'desc' => $item->desc
                            ];
                            array_push($temp, $temp0);
                        }
                    }


                    $result1 = Utils::setFileText("settings.txt", 'whatsapp_default_text', json_encode($temp));

                    if ($result1 == 200) {
                        echo "successful";
                        Admin::SetAdminLog("a_whatsapp_default_text");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

                break;


            /**
             * Uploads Media
             */
            case 'media-upload':
                $files = $antiXSS->xss_clean($_FILES);
                $token = $antiXSS->xss_clean($_POST['token']);
                $name = $antiXSS->xss_clean($_POST['name']) ??  null;

                $result_token = Security::verifyCSRF2($token, false);
                if ($result_token) {
                    $flag = true;
                    if (!empty($files)) {
                        foreach ($files as $filesLoop) {
                            $result_upload = Upload::upload($filesLoop, SITE_MEDIAS ,$name);
                            if ($result_upload->status == 200) {

                            } else {
                                $flag = false;
                            }
                        }
                    }
                    if ($flag == true) {
                        echo "successful";
                        Admin::SetAdminLog("add_media", "media_add");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

                break;


            /**
             * Update User Panel Logoes
             */
            case 'settings-theme-user':
                $files = $antiXSS->xss_clean($_FILES);
                $token = $antiXSS->xss_clean($_POST['token']);

                $result_token = Security::verifyCSRF('admin-settings-general', $token);
                if ($result_token) {

                    $flag = true;
                    if (!empty($files['logoLightUser'])) {
                        $flag = false;
                        $result_upload = Upload::upload($files['logoLightUser'], SITE_ADDRESS, 'user-logo-light');
                        if ($result_upload->status == 200) {
                            $flag = true;
                        } else {
                            $flag = false;
                        }
                    }


                    $flagDark = true;
                    if (!empty($files['logoDarkUser'])) {
                        $flagDark = false;
                        $result_upload = Upload::upload($files['logoDarkUser'], SITE_ADDRESS, 'user-logo-dark');
                        if ($result_upload->status == 200) {
                            $flagDark = true;
                        } else {
                            $flagDark = false;
                        }
                    }


                    if ($flag && $flagDark) {
                        echo "successful";
                        Admin::SetAdminLog("update_site_logo");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

                break;


            /**
             * Update User Panel Logoes
             */
            case 'settings-manifest':
                $files = $antiXSS->xss_clean($_FILES);
                $token = $antiXSS->xss_clean($_POST['token']);

                $result_token = Security::verifyCSRF('admin-settings-general', $token);
                if ($result_token) {

                    $flag = true;
                    if (!empty($files['manifest144'])) {
                        $flag = false;
                        $result_upload = Upload::upload($files['manifest144'], SITE_ADDRESS, '144');
                        if ($result_upload->status == 200) {
                            $flag = true;
                        } else {
                            $flag = false;
                        }
                    }


                    $flag180 = true;
                    if (!empty($files['manifest180'])) {
                        $flag180 = false;
                        $result_upload = Upload::upload($files['manifest180'], SITE_ADDRESS, '180');
                        if ($result_upload->status == 200) {
                            $flag180 = true;
                        } else {
                            $flag180 = false;
                        }
                    }


                    $flag192 = true;
                    if (!empty($files['manifest192'])) {
                        $flag192 = false;
                        $result_upload = Upload::upload($files['manifest192'], SITE_ADDRESS, '192');
                        if ($result_upload->status == 200) {
                            $flag192 = true;
                        } else {
                            $flag192 = false;
                        }
                    }


                    $flag384 = true;
                    if (!empty($files['manifest384'])) {
                        $flag384 = false;
                        $result_upload = Upload::upload($files['manifest384'], SITE_ADDRESS, '384');
                        if ($result_upload->status == 200) {
                            $flag384 = true;
                        } else {
                            $flag384 = false;
                        }
                    }


                    $flag512 = true;
                    if (!empty($files['manifest512'])) {
                        $flag512 = false;
                        $result_upload = Upload::upload($files['manifest512'], SITE_ADDRESS, '512');
                        if ($result_upload->status == 200) {
                            $flag512 = true;
                        } else {
                            $flag512 = false;
                        }
                    }

                    if ($flag && $flag180 && $flag192 && $flag384 && $flag512) {
                        echo "successful";
                        Admin::SetAdminLog("update_manifest_logo");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }

                break;


            /**
             * Change businessman authentication By ADmin
             */
            case 'authentication-businessman-info' :
                $slug = $antiXSS->xss_clean($_POST['slug']);
                $status = $antiXSS->xss_clean($_POST['status']);
                $userID = (int)$antiXSS->xss_clean($_POST['userID']);
                $type = $antiXSS->xss_clean($_POST['type']);
                $btnType = $antiXSS->xss_clean($_POST['btnType']);
                $token = $antiXSS->xss_clean($_POST['token']);

                $flag = false;

                if ($type == "text" && $btnType == "yes" && !empty($antiXSS->xss_clean($_POST['newValue']))) {
                    $flag = true;
                } elseif ($type == "text" && $btnType == "no") {
                    $flag = true;
                }

                if ($type == "file" && $btnType == "yes" && !empty($antiXSS->xss_clean($_FILES)['newValue'])) {
                    $flag = true;
                } elseif ($type == "file" && $btnType == "no") {
                    $flag = true;
                }

                $lists = [
                    "accepted",
                    "rejected"
                ];
                if ($flag && in_array($status, $lists) && strlen($slug) > 0 && $userID > 0 && strlen($token) > 10) {

                    $result_token = Security::verifyCSRF2($token, false);
                    if ($result_token) {

                        if ($type == "text") {
                            $newValueTemp = null;
                            if ($btnType == "yes") {
                                $newValueTemp = $antiXSS->xss_clean($_POST['newValue']);
                            }

                            $result = AUser::editOrInsertUserAuthenticationByAdmin($userID, $slug, $status, $btnType, $newValueTemp);

                            if ($result->status == 200) {
                                echo "successful";
                                Admin::SetAdminLog("change_businessman_authentication_id_" . $userID, "user_info");
                            } else {
                                echo "error";
                            }


                        } else {
                            $imgTemp = null;
                            if ($btnType == "yes") {
                                $newValue = $antiXSS->xss_clean($_FILES);

                                if (!file_exists(getcwd() . AUTH_ADDRESS) && !is_dir(getcwd() . AUTH_ADDRESS)) {
                                    mkdir(getcwd() . AUTH_ADDRESS);
                                }

                                $result_upload = Upload::upload($newValue['newValue'], AUTH_ADDRESS);
                                if ($result_upload->status == 200) {
                                    $imgTemp = $result_upload->response;
                                }
                            }

                            $result = AUser::editOrInsertUserAuthenticationByAdmin($userID, $slug, $status, $btnType, $imgTemp);
                            if ($result->status == 200) {
                                echo "successful";
                                Admin::SetAdminLog("change_businessman_authentication_id_" . $userID, "user_info");
                            } else {
                                echo "error";
                            }

                        }
                    } else {
                        echo "token_error";
                    }
                } else {
                    echo "empty";
                }
                break;


            /**
             * Change driver authentication By ADmin
             */
            case 'authentication-driver-info' :
                $slug = $antiXSS->xss_clean($_POST['slug']);
                $status = $antiXSS->xss_clean($_POST['status']);
                $userID = (int)$antiXSS->xss_clean($_POST['userID']);
                $type = $antiXSS->xss_clean($_POST['type']);
                $btnType = $antiXSS->xss_clean($_POST['btnType']);
                $token = $antiXSS->xss_clean($_POST['token']);

                $flag = false;

                if ($type == "text" && $btnType == "yes" && !empty($antiXSS->xss_clean($_POST['newValue']))) {
                    $flag = true;
                } elseif ($type == "text" && $btnType == "no") {
                    $flag = true;
                }

                if ($type == "file" && $btnType == "yes" && !empty($antiXSS->xss_clean($_FILES)['newValue'])) {
                    $flag = true;
                } elseif ($type == "file" && $btnType == "no") {
                    $flag = true;
                }

                $lists = [
                    "accepted",
                    "rejected"
                ];
                if ($flag && in_array($status, $lists) && strlen($slug) > 0 && $userID > 0 && strlen($token) > 10) {

                    $result_token = Security::verifyCSRF2($token, false);
                    if ($result_token) {

                        if ($type == "text") {
                            $newValueTemp = null;
                            if ($btnType == "yes") {
                                $newValueTemp = $antiXSS->xss_clean($_POST['newValue']);
                            }

                            $result = AUser::editOrInsertUserAuthenticationByAdmin($userID, $slug, $status, $btnType, $newValueTemp);

                            if ($result->status == 200) {
                                echo "successful";
                                Admin::SetAdminLog("change_driver_authentication_id_" . $userID, "user_info");
                            } else {
                                echo "error";
                            }


                        } else {
                            $imgTemp = null;
                            if ($btnType == "yes") {
                                $newValue = $antiXSS->xss_clean($_FILES);

                                if (!file_exists(getcwd() . AUTH_ADDRESS) && !is_dir(getcwd() . AUTH_ADDRESS)) {
                                    mkdir(getcwd() . AUTH_ADDRESS);
                                }

                                $result_upload = Upload::upload($newValue['newValue'], AUTH_ADDRESS);
                                if ($result_upload->status == 200) {
                                    $imgTemp = $result_upload->response;
                                }
                            }

                            $result = AUser::editOrInsertUserAuthenticationByAdmin($userID, $slug, $status, $btnType, $imgTemp);
                            if ($result->status == 200) {
                                echo "successful";
                                Admin::SetAdminLog("change_driver_authentication_id_" . $userID, "user_info");
                            } else {
                                echo "error";
                            }

                        }
                    } else {
                        echo "token_error";
                    }
                } else {
                    echo "empty";
                }

                break;


            case 'task-add':
                $title = $antiXSS->xss_clean($_POST['title']);
                $desc = $antiXSS->xss_clean($_POST['desc']);
                $priority = $antiXSS->xss_clean($_POST['priority']);
                $refer = $antiXSS->xss_clean($_POST['referTd']);
                $start = $antiXSS->xss_clean($_POST['start']);
                $end = $antiXSS->xss_clean($_POST['end']);
                $StartTime = $antiXSS->xss_clean($_POST['StartTime']);;
                $EndTime = $antiXSS->xss_clean($_POST['EndTime']);;
                $token = $antiXSS->xss_clean($_POST['token']);;


                //print_r(strtotime(date('Y-m-d', $start).$StartTime));
                $files = $antiXSS->xss_clean($_FILES);


                $StartTime = ($StartTime && strtotime(date('Y-m-d', time()) . $StartTime)) ? $StartTime : date('H:i');
                $EndTime = ($EndTime && strtotime(date('Y-m-d', time()) . $EndTime)) ? $EndTime : date('H:i');
                $start = ($start) ? $start : date('Y-m-d');
                $end = ($end) ? $end : date('Y-m-d') + 86400;

                $timeS = strtotime(date('Y-m-d', $start) . $StartTime);
                $timeE = strtotime(date('Y-m-d', $end) . $EndTime);


                $list_priority = [
                    "important",
                    "critical",
                    "high",
                    "medium",
                    "low",
                    "informational",
                ];


                if (strlen($title) > 2 && strlen($desc) > 2 && strlen($refer) > 0 && in_array($priority, $list_priority)) {

                    $result_token = Security::verifyCSRF2($token);
                    if ($result_token) {
                        $array = null;
                        $flag = true;

                        if (!empty($files)) {


                            if (!file_exists(getcwd() . TASK_ADDRESS) && !is_dir(getcwd() . TASK_ADDRESS)) {
                                mkdir(getcwd() . TASK_ADDRESS);
                            }

                            $array = [];
                            foreach ($files as $filesLoop) {
                                $result_upload = Upload::upload($filesLoop, TASK_ADDRESS);
                                if ($result_upload->status == 200) {
                                    $array[] = $result_upload->response;
                                } else {
                                    $flag = false;
                                }
                            }

                        }
                        $temp = explode(",", $refer);
                        $flagR = true;
                        foreach ($temp as $loop) {
                            $result = Tasks::setNewTask($title, $desc, $loop, $priority, $timeS, $timeE, json_encode($array));

                            if ($result->status == 200) {
                                Admin::SetAdminLog("set_new_task_id_" . $result->response, 'a_tasks');
                            } else {
                                $flagR = false;
                            }
                        }

                        if ($flagR) {
                            echo "successful";
                        } else {
                            echo "error";
                        }


                    } else {
                        echo "token_error";
                    }
                } else {
                    echo "empty";
                }

                break;


            case 'task-info':
                $title = $antiXSS->xss_clean($_POST['title']);
                $desc = $antiXSS->xss_clean($_POST['desc']);
                $taskId = (int)$antiXSS->xss_clean($_POST['taskId']);
                $token = $antiXSS->xss_clean($_POST['token']);;

                $files = $antiXSS->xss_clean($_FILES);


                if (strlen($title) > 2 && intval($taskId) > 0) {

                    $result_token = Security::verifyCSRF2($token);
                    if ($result_token) {
                        $array = null;
                        $flag = true;

                        if (!empty($files)) {

                            if (!file_exists(getcwd() . TASK_ADDRESS) && !is_dir(getcwd() . TASK_ADDRESS)) {
                                mkdir(getcwd() . TASK_ADDRESS);
                            }

                            $array = [];
                            foreach ($files as $filesLoop) {
                                $result_upload = Upload::upload($filesLoop, TASK_ADDRESS);
                                if ($result_upload->status == 200) {
                                    $array[] = $result_upload->response;
                                } else {
                                    $flag = false;
                                }
                            }

                        }


                        $result = Tasks::addDetailTask($taskId, $title, $desc, json_encode($array));

                        if ($result->status == 200) {
                            Admin::SetAdminLog("set_task_detail_id_" . $taskId, 'a_tasks');
                            echo "successful";

                        } else {
                            echo "error";
                        }
                    } else {
                        echo "token_error";
                    }


                } else {
                    echo "empty";
                }

                break;

            /**
             * Add NEW Brand
             */
            case 'brand-add':
                $status = $antiXSS->xss_clean($_POST['status']);
                $title = $antiXSS->xss_clean($_POST['title']);
                $files = $antiXSS->xss_clean($_FILES);
                $priority = (int)$antiXSS->xss_clean($_POST['priority']);
                $type = $antiXSS->xss_clean($_POST['type']);
                $token = $antiXSS->xss_clean($_POST['token']);

                if ($status != 'active') {
                    $status = "inactive";
                }

                $name = "";
                $flag = true;
                foreach (json_decode($title) as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 2) {
                        $flag = false;
                    }
                }

                if ($flag && isset($files['image']) && in_array($type, ['truck', 'trailer'])) {

                    $result_token = Security::verifyCSRF2($token);
                    if ($result_token) {

                        if (!file_exists(getcwd() . BRANDS_ADDRESS) && !is_dir(getcwd() . BRANDS_ADDRESS)) {
                            mkdir(getcwd() . BRANDS_ADDRESS);
                        }

                        $flag2 = BOX_EMPTY;
                        $result_upload = Upload::upload($files['image'], BRANDS_ADDRESS);
                        if ($result_upload->status == 200) {
                            $flag2 = $result_upload->response;

                            $result = PosterC::setNewBrand($title, $status, $priority, $type, $flag2);
                            if ($result->status == 200) {
                                echo "successful " . $result->response;
                                Admin::SetAdminLog("add_new_brand_id_" . $result->response);
                            } else {
                                echo "error";
                            }

                        } else {
                            echo "error_upload_image";
                        }
                    } else {
                        echo "token_error";
                    }
                } else {
                    echo "empty";
                }

                break;


            /**
             * Edit Brand
             */
            case 'brand-edit':
                $id = (int)$_POST['id'];
                $priority = (int)$antiXSS->xss_clean($_POST['priority']);
                $status = $antiXSS->xss_clean($_POST['status']);
                $title = $antiXSS->xss_clean($_POST['title']);
                $files = $antiXSS->xss_clean($_FILES);
                $token = $antiXSS->xss_clean($_POST['token']);
                $type = $antiXSS->xss_clean($_POST['type']);


                if ($status != 'active') {
                    $status = "inactive";
                }


                $flag = true;
                foreach (json_decode($title) as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 2) {
                        $flag = false;
                    }
                }


                if ($flag && $id != 0 && strlen($id) > 0 && in_array($type, ['truck', 'trailer'])) {

                    $result_token = Security::verifyCSRF2($token, false);
                    if ($result_token) {


                        if (isset($files['image'])) {

                            if (!file_exists(getcwd() . BRANDS_ADDRESS) && !is_dir(getcwd() . BRANDS_ADDRESS)) {
                                mkdir(getcwd() . BRANDS_ADDRESS);
                            }

                            $flag2 = BOX_EMPTY;
                            $result_upload = Upload::upload($files['image'], BRANDS_ADDRESS);
                            if ($result_upload->status == 200) {
                                $flag2 = $result_upload->response;

                                $result = PosterC::editBrandById($id, $title, $status, $priority, $type, $flag2);
                                if ($result->status == 200) {
                                    echo "successful";
                                    Admin::SetAdminLog("edit_brand_id_" . $id, 'category');
                                } else {
                                    echo "error";
                                }
                            } else {
                                echo "error_upload_image";
                            }

                        } else {
                            $result = PosterC::editBrandById($id, $title, $status, $priority, $type, null);
                            if ($result->status == 200) {
                                echo "successful";
                                Admin::SetAdminLog("edit_brand_id_" . $id, 'category');
                            } else {
                                echo "error";
                            }
                        }


                    } else {
                        echo "token_error";
                    }
                } else {
                    echo "empty";
                }
                break;


            /**
             * Add NEW gearbox
             */
            case 'gearbox-add':
                $status = $antiXSS->xss_clean($_POST['status']);
                $title = $antiXSS->xss_clean($_POST['title']);
                $files = $antiXSS->xss_clean($_FILES);
                $priority = (int)$antiXSS->xss_clean($_POST['priority']);
                $token = $antiXSS->xss_clean($_POST['token']);

                if ($status != 'active') {
                    $status = "inactive";
                }

                $name = "";
                $flag = true;
                foreach (json_decode($title) as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                        $flag = false;
                    }
                }


                if ($flag && isset($files['image'])) {


                    $result_token = Security::verifyCSRF2($token);
                    if ($result_token) {

                        if (!file_exists(getcwd() . GEARBOX_ADDRESS) && !is_dir(getcwd() . GEARBOX_ADDRESS)) {
                            mkdir(getcwd() . GEARBOX_ADDRESS);
                        }
                        $flag2 = BOX_EMPTY;
                        $result_upload = Upload::upload($files['image'], GEARBOX_ADDRESS);
                        if ($result_upload->status == 200) {
                            $flag2 = $result_upload->response;

                            $result = PosterC::setNewGearbox($title, $status, $priority, $flag2);
                            if ($result->status == 200) {
                                echo "successful " . $result->response;
                                Admin::SetAdminLog("add_new_gearbox_id_" . $result->response);
                            } else {
                                echo "error";
                            }

                        } else {
                            echo "error_upload_image";
                        }
                    } else {
                        echo "token_error";
                    }
                } else {
                    echo "empty";
                }

                break;


            /**
             * Edit gearbox
             */
            case 'gearbox-edit':
                $id = (int)$_POST['id'];
                $priority = (int)$antiXSS->xss_clean($_POST['priority']);
                $status = $antiXSS->xss_clean($_POST['status']);
                $title = $antiXSS->xss_clean($_POST['title']);
                $files = $antiXSS->xss_clean($_FILES);
                $token = $antiXSS->xss_clean($_POST['token']);


                if ($status != 'active') {
                    $status = "inactive";
                }


                $flag = true;
                foreach (json_decode($title) as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                        $flag = false;
                    }
                }


                if ($flag && $id != 0 && strlen($id) > 0) {

                    $result_token = Security::verifyCSRF2($token, false);
                    if ($result_token) {

                        if (isset($files['image'])) {
                            if (!file_exists(getcwd() . GEARBOX_ADDRESS) && !is_dir(getcwd() . GEARBOX_ADDRESS)) {
                                mkdir(getcwd() . GEARBOX_ADDRESS);
                            }
                            $flag2 = BOX_EMPTY;
                            $result_upload = Upload::upload($files['image'], GEARBOX_ADDRESS);
                            if ($result_upload->status == 200) {
                                $flag2 = $result_upload->response;

                                $result = PosterC::editGearboxById($id, $title, $status, $priority, $flag2);
                                if ($result->status == 200) {
                                    echo "successful";
                                    Admin::SetAdminLog("edit_gearbox_id_" . $id, 'category');
                                } else {
                                    echo "error";
                                }
                            } else {
                                echo "error_upload_image";
                            }
                        } else {
                            $result = PosterC::editGearboxById($id, $title, $status, $priority, null);
                            if ($result->status == 200) {
                                echo "successful";
                                Admin::SetAdminLog("edit_gearbox_id_" . $id, 'category');
                            } else {
                                echo "error";
                            }
                        }
                    } else {
                        echo "token_error";
                    }
                } else {
                    echo "empty";
                }

                break;


            /**
             * Add NEW Fuel
             */
            case 'fuel-add':
                $status = $antiXSS->xss_clean($_POST['status']);
                $title = $antiXSS->xss_clean($_POST['title']);
                $files = $antiXSS->xss_clean($_FILES);
                $priority = (int)$antiXSS->xss_clean($_POST['priority']);
                $token = $antiXSS->xss_clean($_POST['token']);

                if ($status != 'active') {
                    $status = "inactive";
                }

                $name = "";
                $flag = true;
                foreach (json_decode($title) as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                        $flag = false;
                    }
                }

                if ($flag && isset($files['image'])) {
                    $result_token = Security::verifyCSRF2($token);
                    if ($result_token) {
                        if (!file_exists(getcwd() . FUEL_ADDRESS) && !is_dir(getcwd() . FUEL_ADDRESS)) {
                            mkdir(getcwd() . FUEL_ADDRESS);
                        }
                        $flag2 = BOX_EMPTY;
                        $result_upload = Upload::upload($files['image'], FUEL_ADDRESS);
                        if ($result_upload->status == 200) {
                            $flag2 = $result_upload->response;
                            $result = PosterC::setNewFuel($title, $status, $priority, $flag2);
                            if ($result->status == 200) {
                                echo "successful " . $result->response;
                                Admin::SetAdminLog("add_new_fuel_id_" . $result->response);
                            } else {
                                echo "error";
                            }
                        } else {
                            echo "error_upload_image";
                        }
                    } else {
                        echo "token_error";
                    }
                } else {
                    echo "empty";
                }
                break;


            /**
             * Edit Fuel
             */
            case 'fuel-edit':
                $id = (int)$_POST['id'];
                $priority = (int)$antiXSS->xss_clean($_POST['priority']);
                $status = $antiXSS->xss_clean($_POST['status']);
                $title = $antiXSS->xss_clean($_POST['title']);
                $files = $antiXSS->xss_clean($_FILES);
                $token = $antiXSS->xss_clean($_POST['token']);


                if ($status != 'active') {
                    $status = "inactive";
                }


                $flag = true;
                foreach (json_decode($title) as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                        $flag = false;
                    }
                }


                if ($flag && $id != 0 && strlen($id) > 0) {

                    $result_token = Security::verifyCSRF2($token, false);
                    if ($result_token) {


                        if (isset($files['image'])) {

                            if (!file_exists(getcwd() . FUEL_ADDRESS) && !is_dir(getcwd() . FUEL_ADDRESS)) {
                                mkdir(getcwd() . FUEL_ADDRESS);
                            }

                            $flag2 = BOX_EMPTY;
                            $result_upload = Upload::upload($files['image'], FUEL_ADDRESS);
                            if ($result_upload->status == 200) {
                                $flag2 = $result_upload->response;

                                $result = PosterC::editFuelById($id, $title, $status, $priority, $flag2);
                                if ($result->status == 200) {
                                    echo "successful";
                                    Admin::SetAdminLog("edit_fuel_id_" . $id, 'category');
                                } else {
                                    echo "error";
                                }
                            } else {
                                echo "error_upload_image";
                            }

                        } else {
                            $result = PosterC::editFuelById($id, $title, $status, $priority, null);
                            if ($result->status == 200) {
                                echo "successful";
                                Admin::SetAdminLog("edit_fuel_id_" . $id, 'category');
                            } else {
                                echo "error";
                            }
                        }


                    } else {
                        echo "token_error";
                    }
                } else {
                    echo "empty";
                }
                break;


            /**
             * Add NEW Property
             */
            case 'property-add':
                $status = $antiXSS->xss_clean($_POST['status']);
                $title = $antiXSS->xss_clean($_POST['title']);
                $files = $antiXSS->xss_clean($_FILES);
                $priority = (int)$antiXSS->xss_clean($_POST['priority']);
                $type = $antiXSS->xss_clean($_POST['type']);
                $token = $antiXSS->xss_clean($_POST['token']);

                if ($status != 'active') {
                    $status = "inactive";
                }

                $name = "";
                $flag = true;
                foreach (json_decode($title) as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                        $flag = false;
                    }
                }
                $lists = [
                    "truck",
                    "trailer",
                ];
                if ($flag && isset($files['image']) && in_array($type, $lists)) {

                    $result_token = Security::verifyCSRF2($token);
                    if ($result_token) {

                        if (!file_exists(getcwd() . PROPERTY_ADDRESS) && !is_dir(getcwd() . PROPERTY_ADDRESS)) {
                            mkdir(getcwd() . PROPERTY_ADDRESS);
                        }
                        $flag2 = BOX_EMPTY;
                        $result_upload = Upload::upload($files['image'], PROPERTY_ADDRESS);
                        if ($result_upload->status == 200) {
                            $flag2 = $result_upload->response;

                            $result = PosterC::setNewProperty($title, $status, $type, $priority, $flag2);
                            if ($result->status == 200) {
                                echo "successful " . $result->response;
                                Admin::SetAdminLog("add_new_property_id_" . $result->response);
                            } else {
                                echo "error";
                            }

                        } else {
                            echo "error_upload_image";
                        }
                    } else {
                        echo "token_error";
                    }
                } else {
                    echo "empty";
                }
                break;


            /**
             * Edit And Update property
             */
            case 'property-edit':
                $id = (int)$_POST['id'];
                $priority = (int)$antiXSS->xss_clean($_POST['priority']);
                $status = $antiXSS->xss_clean($_POST['status']);
                $title = $antiXSS->xss_clean($_POST['title']);
                $files = $antiXSS->xss_clean($_FILES);
                $type = $antiXSS->xss_clean($_POST['type']);
                $token = $antiXSS->xss_clean($_POST['token']);


                if ($status != 'active') {
                    $status = "inactive";
                }


                $flag = true;
                foreach (json_decode($title) as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                        $flag = false;
                    }
                }


                if ($flag && $id != 0 && strlen($id) > 0) {

                    $result_token = Security::verifyCSRF2($token, false);
                    if ($result_token) {


                        if (isset($files['image'])) {

                            if (!file_exists(getcwd() . PROPERTY_ADDRESS) && !is_dir(getcwd() . PROPERTY_ADDRESS)) {
                                mkdir(getcwd() . PROPERTY_ADDRESS);
                            }

                            $flag2 = BOX_EMPTY;
                            $result_upload = Upload::upload($files['image'], PROPERTY_ADDRESS);
                            if ($result_upload->status == 200) {
                                $flag2 = $result_upload->response;

                                $result = PosterC::editPropertyById($id, $title, $type, $status, $priority, $flag2);
                                if ($result->status == 200) {
                                    echo "successful";
                                    Admin::SetAdminLog("edit_property_id_" . $id, 'category');
                                } else {
                                    echo "error";
                                }
                            } else {
                                echo "error_upload_image";
                            }

                        } else {
                            $result = PosterC::editPropertyById($id, $title, $type, $status, $priority, null);
                            if ($result->status == 200) {
                                echo "successful";
                                Admin::SetAdminLog("edit_property_id_" . $id, 'category');
                            } else {
                                echo "error";
                            }
                        }


                    } else {
                        echo "token_error";
                    }

                } else {
                    echo "empty";
                }
                break;


            /**
             * Add New Car Category
             */
            case 'category-cargo-add':
                $status = $antiXSS->xss_clean($_POST['status']);
                $title = $antiXSS->xss_clean(strip_tags($_POST['title']));
                $color = $antiXSS->xss_clean($_POST['color']);
                $token = $antiXSS->xss_clean($_POST['token']);
                $files = $antiXSS->xss_clean($_FILES);

                if ($status != 'active') {
                    $status = "inactive";
                }


                if (empty($color)) {
                    $color = "#01c99c";
                }


                $name = "";
                $flag = true;
                foreach (json_decode($title) as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                        $flag = false;
                    }
                }


                if ($flag && isset($files['image']) && isset($files['icon'])) {


                    $result_token = Security::verifyCSRF('admin-category-cargo-add', $token);
                    if ($result_token) {


                        if (!file_exists(getcwd() . CARGO_ADDRESS) && !is_dir(getcwd() . CARGO_ADDRESS)) {
                            mkdir(getcwd() . CARGO_ADDRESS);
                        }

                        $flag1 = BOX_EMPTY;
                        $flag2 = BOX_EMPTY;
                        $result_upload1 = Upload::upload($files['image'], CARGO_ADDRESS);
                        $result_upload2 = Upload::upload($files['icon'], CARGO_ADDRESS);
                        if ($result_upload1->status == 200 && $result_upload2->status == 200) {
                            $flag1 = $result_upload1->response;
                            $flag2 = $result_upload2->response;

                            $result = Cargo::setNewCategoryCargo($title, $color, $flag2, $flag1, $status);
                            if ($result->status == 200) {
                                echo "successful " . $result->response;
                                Admin::SetAdminLog("add_cargo_category_title_" . $name);
                            } else {
                                echo "error";
                            }

                        } else {
                            if ($result_upload2->status != 200) {
                                echo "error_upload_icon";
                            } else {
                                echo "error_upload_image";
                            }
                        }
                    } else {
                        echo "token_error";
                    }
                } else {
                    echo "empty";
                }

                break;

            /**
             * Edit Department
             * @author tjavan
             */
            case 'category-cargo-edit':
                $id = (int)$_POST['id'];
                $status = $antiXSS->xss_clean($_POST['status']);
                $title = $antiXSS->xss_clean(strip_tags($_POST['title']));
                $color = $antiXSS->xss_clean($_POST['color']);
                $token = $antiXSS->xss_clean($_POST['token']);
                $files = $antiXSS->xss_clean($_FILES);

                if ($status != 'active') {
                    $status = "inactive";
                }

                if (empty($color)) {
                    $color = "#01c99c";
                }


                if (!file_exists(getcwd() . CARGO_ADDRESS) && !is_dir(getcwd() . CARGO_ADDRESS)) {
                    mkdir(getcwd() . CARGO_ADDRESS);
                }

                $flag = true;
                foreach (json_decode($title) as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                        $flag = false;
                    }
                }


                if ($flag && $id != 0 && strlen($id) > 0) {

                    $result_token = Security::verifyCSRF('admin-category-cargo-edit', $token);
                    if ($result_token) {

                        if (isset($files['image']) && isset($files['icon'])) {

                            $result_image = Upload::upload($files['image'], CARGO_ADDRESS);
                            $result_icon = Upload::upload($files['icon'], CARGO_ADDRESS);
                            if ($result_image->status == 200 && $result_icon->status == 200) {
                                $flag2 = $result_image->response;
                                $flag1 = $result_icon->response;
                                $result = Cargo::editCategoryCargoById($id, $title, $color, $status, $flag1, $flag2);
                                if ($result->status == 200) {
                                    echo "successful";
                                    Admin::SetAdminLog("edit_cargo_category_title_" . $name);
                                } else {
                                    echo "error";
                                }


                            } else {
                                if ($result_icon->status != 200) {
                                    echo "error_upload_icon";
                                } else {
                                    echo "error_upload_image";
                                }
                            }

                        } elseif (isset($files['image'])) {

                            $result_image = Upload::upload($files['image'], CARGO_ADDRESS);
                            if ($result_image->status == 200) {
                                $flag2 = $result_image->response;

                                $result = Cargo::editCategoryCargoById($id, $title, $color, $status, null, $flag2);
                                if ($result->status == 200) {
                                    echo "successful";
                                    Admin::SetAdminLog("edit_cargo_category_title_" . $name);
                                } else {
                                    echo "error";
                                }
                            } else {
                                echo "error_upload_image";
                            }

                        } elseif (isset($files['icon'])) {

                            $result_icon = Upload::upload($files['icon'], CARGO_ADDRESS);
                            if ($result_icon->status == 200) {
                                $flag1 = $result_icon->response;

                                $result = Cargo::editCategoryCargoById($id, $title, $color, $status, $flag1, null);
                                if ($result->status == 200) {
                                    echo "successful";
                                    Admin::SetAdminLog("edit_cargo_category_title" . $name);
                                } else {
                                    echo "error";
                                }
                            } else {
                                echo "error_upload_icon";
                            }
                        } else {
                            $result = Cargo::editCategoryCargoById($id, $title, $color, $status, null, null);
                            if ($result->status == 200) {
                                echo "successful";
                                Admin::SetAdminLog("edit_cargo_category_title_" . $name);
                            } else {
                                echo "error";
                            }
                        }


                    } else {
                        echo "token_error";
                    }

                } else {
                    echo "empty";
                }

                break;

            /**
             * Add New Car Category
             */
            case 'category-car-add':
                $status = $antiXSS->xss_clean($_POST['status']);
                $title = $antiXSS->xss_clean(strip_tags($_POST['title']));
                $token = $antiXSS->xss_clean($_POST['token']);
                $files = $antiXSS->xss_clean($_FILES);

                if ($status != 'active') {
                    $status = "inactive";
                }

                $name = "";
                $flag = true;
                foreach (json_decode($title) as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                        $flag = false;
                    }
                }

                if ($flag && isset($files['icon'])) {
                    $result_token = Security::verifyCSRF2($token, false);
                    if ($result_token) {
                        if (!file_exists(getcwd() . CAR_ADDRESS) && !is_dir(getcwd() . CAR_ADDRESS)) {
                            mkdir(getcwd() . CAR_ADDRESS);
                        }

                        $flag1 = BOX_EMPTY;
                        $result_icon = Upload::upload($files['icon'], CAR_ADDRESS);
                        if ($result_icon->status == 200) {
                            $flag1 = $result_icon->response;

                            $result = Car::setNewCategoryCar($title, $status, $flag1);
                            if ($result->status == 200) {
                                echo "successful " . $result->response;
                                Admin::SetAdminLog("add_car_type_id_" . $result->response);
                            } else {
                                echo "error";
                            }
                        } else {
                            echo "error_upload_icon";
                        }
                    } else {
                        echo "token_error";
                    }
                } else {
                    echo "empty";
                }

                break;


            /**
             * Edit Car Category
             * @author tjavan
             */
            case 'category-car-edit':
                $status = $antiXSS->xss_clean($_POST['status']);
                $id = (int)$_POST['id'];
                $title = $antiXSS->xss_clean($_POST['title']);
                $token = $antiXSS->xss_clean($_POST['token']);
                $files = $antiXSS->xss_clean($_FILES);
                if ($status != 'active') {
                    $status = "inactive";
                }

                $flag = true;
                foreach (json_decode($title) as $titleITEM) {
                    $name = $titleITEM->value;
                    if (strlen($titleITEM->slug) < 2 || strlen($titleITEM->value) < 3) {
                        $flag = false;
                    }
                }


                if ($flag && $id != 0 && strlen($id) > 0) {

                    $result_token = Security::verifyCSRF2($token, false);
                    if ($result_token) {
                        if (isset($files['icon'])) {
//                            $result_icon = Upload::uploadBase64($icon, CAR_ADDRESS);
//                            if ($result_icon->status == 200) {
//                                $flag1 = $result_icon->response;
                            $result_icon = Upload::upload($files['icon'], CAR_ADDRESS);
                            if ($result_icon->status == 200) {
                                $flag1 = $result_icon->response;

                                $result = Car::editCategoryCarById($id, $title, $status, $flag1);
                                if ($result->status == 200) {
                                    echo "successful";
                                    Admin::SetAdminLog("edit_car_type_title_" . $name);
                                } else {
                                    echo "error";
                                }

                            } else {
                                echo "error_upload_icon";
                            }
                        } else {
                            $result = Car::editCategoryCarById($id, $title, $status);
                            if ($result->status == 200) {
                                echo "successful";
                                Admin::SetAdminLog("edit_car_type_title_" . $name);
                            } else {
                                echo "error";
                            }
                        }

                    } else {
                        echo "token_error";
                    }

                } else {
                    echo "empty";
                }

                break;


            /**
             * Add Category Academy
             * @author tjavan
             */
            case 'category-academy-add':
                $status = $antiXSS->xss_clean($_POST['status']);
                $title = $antiXSS->xss_clean($_POST['title']);
                $language = $antiXSS->xss_clean($_POST['lang']);
                $metaTitle = $antiXSS->xss_clean($_POST['metaTitle']);
                $metaDesc = $antiXSS->xss_clean($_POST['metaDesc']);
                $schema = $antiXSS->xss_clean($_POST['schema']);
                $files = $antiXSS->xss_clean($_FILES);
                $token = $antiXSS->xss_clean($_POST['token']);
                $priority = (int)$antiXSS->xss_clean($_POST['priority']);
                $parent = ((int)$_POST['parent'] == 0) ? null : (int)$_POST['parent'];


                if ($status != 'active') {
                    $status = "inactive";
                }


                if (strlen($title) > 2 && strlen($language) > 3 && isset($files['img'])) {

                    if (!file_exists(getcwd() . ACADEMY_CATEGORY_ADDRESS) && !is_dir(getcwd() . ACADEMY_CATEGORY_ADDRESS)) {
                        mkdir(getcwd() . ACADEMY_CATEGORY_ADDRESS);
                    }


                    $result_token = Security::verifyCSRF2($token);
                    if ($result_token) {
//                        $resultThumbnail = Upload::uploadBase64($thumbnail, ACADEMY_CATEGORY_ADDRESS);
//                        if ($resultThumbnail->status == 200 && !empty($resultThumbnail->response)) {
//                            $thumbnailURL = $resultThumbnail->response;
                        $thumbnailURL = BOX_EMPTY;
                        $result_upload = Upload::upload($files['img'], ACADEMY_CATEGORY_ADDRESS);
                        if ($result_upload->status == 200) {
                            $thumbnailURL = $result_upload->response;

                            $result = Academy::SetNewCategory($title, $language, $status, $priority, $thumbnailURL, $parent, $metaTitle, $metaDesc, $schema);

                            if ($result->status == 200) {
                                echo "successful " . $result->response;
                                Admin::SetAdminLog("add_category_academy_title_" . $title, 'academy');
                            } else {
                                echo "error";
                            }
                        } else {
                            echo "error_img";
                        }
                    } else {
                        echo "token_error";
                    }
                } else {
                    echo "empty";
                }
                break;


            /**
             * Edit Category Academy
             * @author tjavan
             */
            case 'category-academy-edit':
                $status = $antiXSS->xss_clean($_POST['status']);
                $title = $antiXSS->xss_clean($_POST['title']);
                $language = $antiXSS->xss_clean($_POST['language']);
                $cat_id = $antiXSS->xss_clean($_POST['cat_id']);
                $metaTitle = $antiXSS->xss_clean($_POST['metaTitle']);
                $metaDesc = $antiXSS->xss_clean($_POST['metaDesc']);
                $schema = $antiXSS->xss_clean($_POST['schema']);
                $token = $antiXSS->xss_clean($_POST['token']);
                $priority = (int)$antiXSS->xss_clean($_POST['priority']);
                $parent = ((int)$_POST['parent'] == 0) ? null : (int)$_POST['parent'];
                $files = $antiXSS->xss_clean($_FILES);
                if ($status == 'active') {
                    $status = "active";
                } else {
                    $status = "inactive";
                }
                if (strlen($title) > 2 && strlen($language) > 3) {
                    $result_token = Security::verifyCSRF2($token, false);
                    if ($result_token) {
                        $image = "";
                        if (isset($files['img'])) {
                            $resultThumbnail = Upload::upload($files['img'], ACADEMY_CATEGORY_ADDRESS);
                            if ($resultThumbnail->status == 200) {
                                $image = $resultThumbnail->response;
                            }
                        }
                        $result = Academy::editCategoryById($cat_id, $title, $language, $status, $priority, $parent, $image, $metaTitle, $metaDesc, $schema);
                        if ($result->status == 200) {
                            echo "successful";
                            Admin::SetAdminLog("edit_category_academy_id_" . $cat_id, "academy");
                        } else {
                            echo "error";
                        }
                    } else {
                        echo "token_error";
                    }
                } else {
                    echo "empty";
                }
                break;
            case 'settings-poster-all':
                $files = $antiXSS->xss_clean($_FILES);
                $token = $antiXSS->xss_clean($_POST['token']);
                $result_token = Security::verifyCSRF('admin-settings-poster', $token);
                if ($result_token) {
                    $flag = true;
                    if (!empty($files['logoLightUser'])) {
                        $flag = false;
                        $result_upload = Upload::upload($files['logoLightUser'], SITE_ADDRESS, 'poster-default');
                        if ($result_upload->status == 200) {
                            $flag = true;
                        } else {
                            $flag = false;
                        }
                    }
                    if ($flag) {
                        echo "successful";
                        Admin::SetAdminLog("a_settings_all_update");
                    } else {
                        echo "error";
                    }
                } else {
                    echo "token_error";
                }
                break;
        }
    } else {
        echo json_encode([
            'status' => 403,
            'message' => "Server"
        ]);
    }
}
