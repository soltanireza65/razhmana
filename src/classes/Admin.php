<?php


use Ghasedak\GhasedakApi;
use MJ\Keys\KEYS;
use MJ\Database\DB;
use MJ\Security\Security;
use MJ\SMS\Ghasedak;
use MJ\Utils\Utils;
use function MJ\Keys\sendResponse;

class Admin
{


    /**
     * Login Page Admin
     * @param $mail string
     * @param $password string
     * @return Object
     * @author Amir
     */
    public static function loginAdmin($mail, $password)
    {

        $response = sendResponse(0, "Error Msg");

        $sql = 'SELECT * FROM `tbl_admins` WHERE `admin_email`=:admin_email AND `admin_password`=:admin_password;';
        $params = [
            'admin_email' => $mail,
            'admin_password' => $password,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Set Admin Log
     * @param $title string
     * @param $title int
     * @return Object
     * @author Amir
     */
    public static function SetAdminLog($title, $slug = null, $id = null)
    {
        $response = sendResponse(0, "Error Msg");

        global $antiXSS;

        $admin_id = 0;
        if (empty($id)) {
            if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
                $id = $antiXSS->xss_clean($_COOKIE['UID']);
                $admin_id = Security::decrypt($id);
            }
        } else {
            $admin_id = $id;
        }


//        $detail = [
//            'massage' => $title,
//            'ip' => $_SERVER['REMOTE_ADDR'],
////            'address'=>Utils::getGeoLocation()->response['format'],
//            'browser' => Utils::getClientBrowser(),
//            'browser_version' => Utils::getClientBrowserVersion(),
//            'os' => Utils::getClientOS(),
//            'device' => Utils::getClientDevice(),
//        ];


        $sql = 'INSERT INTO `tbl_admin_logs`(`admin_id`, `log_detail`,`log_ip`,`log_browser`,`log_browser_version`,`log_os`,`log_device`,`log_date`,`log_slug`)
                VALUES (:admin_id,:log_detail,:log_ip,:log_browser,:log_browser_version,:log_os,:log_device,:log_date,:log_slug);SELECT LAST_INSERT_ID();';
        $params = [
            'admin_id' => $admin_id,
            'log_detail' => $title,
            'log_ip' => $_SERVER['REMOTE_ADDR'],
            'log_browser' => Utils::getClientBrowser(),
            'log_browser_version' => Utils::getClientBrowserVersion(),
            'log_os' => Utils::getClientOS(),
            'log_device' => Utils::getClientDevice(),
            'log_date' => time(),
            'log_slug' => $slug,
        ];
        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }

        return $response;
    }


    /**
     * Ckeck Admin Login By Admin UID And EID
     * dashboard
     * @param $id string   cookie
     * @param $email string cookie
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function checkAdminLogin()
    {
        global $antiXSS;
        $response = sendResponse(0, "");

        if (isset($_COOKIE['UID']) && isset($_COOKIE['EID']) && !empty($_COOKIE['INF'])) {
            // After Check By Id Admin And Email Admin Is Exsit
            $UID = Security::decrypt($antiXSS->xss_clean($_COOKIE['UID']));
            $EID = $antiXSS->xss_clean($_COOKIE['EID']);
            $sql = "SELECT * FROM `tbl_admins` WHERE `admin_id`=:admin_id AND `admin_email`=:admin_email;";
            $params = [
                'admin_id' => $UID,
                'admin_email' => $EID,
            ];
            $result = DB::rawQuery($sql, $params);

            if ($result->status == 200 && !empty($result->response) &&
                !empty($result->response[0]) && !empty($result->response[0]->admin_email) &&
                $result->response[0]->admin_status == "active") {
                $response = sendResponse(200, "", $result->response[0]);
            }
        }
        return $response;
    }


    /**
     * Get Admin Role Permissons From Cleck
     * dashboard
     * @param $role_id
     * @return object
     */
    private static function getAdminRolePermissonsForCheck($role_id)
    {
        $response = sendResponse(0, "");

        $sql = 'SELECT tbl_admin_slugs.slug_name,tbl_admin_permissions.permission_can_insert,tbl_admin_permissions.permission_can_edit,tbl_admin_permissions.permission_can_delete,tbl_admin_permissions.permission_can_show FROM `tbl_admin_role_permissions` INNER JOIN tbl_admin_permissions ON tbl_admin_role_permissions.permission_id=tbl_admin_permissions.permission_id INNER JOIN tbl_admin_slugs ON tbl_admin_permissions.slug_id=tbl_admin_slugs.slug_id WHERE tbl_admin_role_permissions.role_id=:role_id;';
        $params = [
            'role_id' => $role_id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Get Admin Role ID From Check
     * dashboard
     * @param $role_id
     * @return stdClass
     */
    public static function checkAdminRoleForCheck($role_id)
    {

        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_admin_roles` WHERE `role_id`=:role_id;";
        $params = [
            'role_id' => $role_id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $resultPermissos = self::getAdminRolePermissonsForCheck($role_id);
            if ($resultPermissos->status == 200) {
                $arra = [
                    "role_name" => $result->response[0]->role_name,
                    "role_status" => $result->response[0]->role_status,
                    "permissons" => $resultPermissos->response,

                ];

                $response = sendResponse(200, "", json_encode($arra));
            } else {
                return $response;
            }
        }
        return $response;
    }


    /**
     * Get All Admins
     * Check every page but not check dashboard and login page
     * use admins.php
     * use admins-log.php
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getAllAdmins()
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_admins` ORDER BY tbl_admins.admin_id DESC ;";
        $result = DB::rawQuery($sql, []);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Get All Roles
     * Check every page but not check dashboard and login page
     * use admins.php
     * use admin-role-delete.php
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getAllRoles()
    {
        $response = sendResponse(0, "");

        $result = DB::select("tbl_admin_roles");

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Get All Admin Slugs
     * admin-role-add.php page
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getAllAdminSlugs()
    {

        $response = sendResponse(0, "");

        $result = DB::rawQuery("SELECT * FROM `tbl_admin_slugs`;");

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Add New Permission
     * level 1
     * @param $slug_id int
     * @param $permission_can_insert string
     * @param $permission_can_edit   string
     * @param $permission_can_delete string
     * @param $permission_can_show   string
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    private static function addAdminPermission($slug_id, $permission_can_insert, $permission_can_edit, $permission_can_delete, $permission_can_show)
    {

        $response = sendResponse(0, "");
        $sql = 'INSERT INTO `tbl_admin_permissions`( `slug_id`, `permission_can_insert`, `permission_can_edit`, `permission_can_delete`, `permission_can_show`) VALUES (:slug_id,:permission_can_insert,:permission_can_edit,:permission_can_delete,:permission_can_show);SELECT LAST_INSERT_ID();';
        $params = [
            'slug_id' => $slug_id,
            'permission_can_insert' => $permission_can_insert,
            'permission_can_edit' => $permission_can_edit,
            'permission_can_delete' => $permission_can_delete,
            'permission_can_show' => $permission_can_show,
        ];
        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }
        return $response;

    }


    /**
     * Add New Role
     * level 2
     * @param $title string
     * @param $status string
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    private static function addAdminRole($title, $status)
    {
        $response = sendResponse(0, "Error Msg");
        $sql = 'INSERT INTO `tbl_admin_roles`(`role_name`, `role_status`) VALUES (:role_name,:role_status);SELECT LAST_INSERT_ID();';
        $params = [
            'role_name' => $title,
            'role_status' => $status,
        ];
        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;

    }


    /**
     * Insert ROLE Permission From Connection tbl_admin_roles And tbl_admin_permissions
     * level 3
     * @param $role_id int
     * @param $permission_id int
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    private static function addRolePermission($role_id, $permission_id)
    {

        $response = sendResponse(0, "");
        $sql = 'INSERT INTO `tbl_admin_role_permissions`( `role_id`, `permission_id`) VALUES (:role_id,:permission_id);SELECT LAST_INSERT_ID();';
        $params = [
            'role_id' => $role_id,
            'permission_id' => $permission_id,
        ];
        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }
        return $response;

    }


    /**
     * Add new Role Permission And Connection Together
     * Level 0 And Main Mother
     * @param $title string
     * @param $permission string
     * @param $status string
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function addAdminRolePermission($title, $permission, $status)
    {
        $response = sendResponse(0, "Error Mag");

        $roleID = '';
        $resultAdminRole = self::addAdminRole($title, $status);


        if ($resultAdminRole->status == 200) {
            $roleID = $resultAdminRole->response;


            $permissionStatus = [];
            foreach (json_decode($permission) as $indexPermissionITEM => $permissionITEM) {
                $resultAdminPermission = self::addAdminPermission($indexPermissionITEM, $permissionITEM->insert, $permissionITEM->edit, $permissionITEM->delete, $permissionITEM->show);
                if ($resultAdminPermission->status == 200) {
//                    array_push($permissionID, $resultAdminPermission->response);
                    $resultRolePermission = self::addRolePermission($roleID, $resultAdminPermission->response);

                    array_push($permissionStatus, $resultRolePermission->status);

                }
            }


            $response = sendResponse(200, "", json_encode($permissionStatus));

            return $response;

        } else {
            return $response;
        }

    }


    /**
     * Get List Admin Have This Role
     * use admin-role-delete.php page
     * @param $id int
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getAllAdminHaveThisRole($id)
    {
        $response = sendResponse(0, "Error Mag");

        $sql = "SELECT * FROM `tbl_admins` WHERE role_id=:role_id";
        $params = [
            'role_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get All Row From tbl_admin_role_permissions
     * use adminAjax admin-role-delete.php
     * level 1
     * @param $id int
     * @return Object
     * @author Amir
     */
    public static function getAdminRolePermissionsByRoleId($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_admin_role_permissions` WHERE role_id=:role_id";
        $params = [
            'role_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Get All Rows From tbl_admin_permissions
     * use adminAjax admin-role-delete.php
     * level 2
     * @param $id int
     * @return Object
     */
    public static function getAdminPermissionsByPermissionsId($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_admin_permissions` WHERE permission_id=:permission_id";
        $params = [
            'permission_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * DELETE permission_id From tbl_admin_permissions
     * use adminAjax admin-role-delete.php
     * level 3
     * @param $id int
     * @return Object
     * @author Amir
     */
    public static function deleteAdminPermissions($id)
    {
        $response = sendResponse(-10, "");

        $sql = 'DELETE FROM `tbl_admin_permissions` WHERE permission_id=:permission_id;';
        $params = [
            'permission_id' => $id,
        ];
        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    /**
     * DELETE rp_id From tbl_admin_role_permissions
     * use adminAjax admin-role-delete.php
     * level 4
     * @param $id int
     * @return Object
     * @author Amir
     */
    public static function deleteAdminRolePermissions($id)
    {
        $response = sendResponse(-20, "");

        $sql = 'DELETE FROM `tbl_admin_role_permissions` WHERE rp_id=:rp_id;';
        $params = [
            'rp_id' => $id,
        ];
        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    /**
     * DELETE role_id From tbl_admin_roles
     * use adminAjax admin-role-delete.php
     * level 5
     * @param $id int
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function deleteAdminRole($id)
    {
        $response = sendResponse(-30, "");

        $sql = 'DELETE FROM `tbl_admin_roles` WHERE role_id=:role_id;';
        $params = [
            'role_id' => $id,
        ];
        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }




    /**
     * Get Admin Role Info By id
     * @param $id int
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    /*
        public static function getAdminRoleById($id)
      {

          $response = sendResponse(0, "Error Msg");

          $sql="SELECT * FROM `tbl_admin_roles` WHERE role_id=:role_id";
          $params=[
              'role_id'=>$id,
          ];
          $result = DB::rawQuery($sql,$params);

          if ($result->status == 200) {
              $response = sendResponse(200, "", $result->response);
          }

          return $response;
      }
  */


    /**
     * Update Permission By Id
     * @param $id
     * @param $show
     * @param $insert
     * @param $edit
     * @param $delete
     * @return stdClass
     * @version 3.0
     */
    public static function editPermissionById($id, $show, $insert, $edit, $delete)
    {
        $response = sendResponse(0, "");
        $sql = 'UPDATE `tbl_admin_permissions` SET `permission_can_insert`=:permission_can_insert,`permission_can_edit`=:permission_can_edit,
                                  `permission_can_delete`=:permission_can_delete ,`permission_can_show`=:permission_can_show WHERE `permission_id`=:permission_id';
        $params = [
            'permission_can_insert' => $insert,
            'permission_can_edit' => $edit,
            'permission_can_delete' => $delete,
            'permission_can_show' => $show,
            'permission_id' => $id,
        ];

        $result = DB::update($sql, $params);

        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, "");
        }

        return $response;

    }

    /**
     * Edit Admin Role Info
     * use adminAjax admin-role-edit.php
     * @param $id int
     * @param $title string
     * @param $status string
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function editAdminRole($id, $title, $status, $permission)
    {
        $response = sendResponse(0, "");

        $sql = 'UPDATE `tbl_admin_roles` SET `role_name`=:role_name,`role_status`=:role_status WHERE `role_id`=:role_id';
        $params = [
            'role_name' => $title,
            'role_status' => $status,
            'role_id' => $id,
        ];
        $flag = true;
        $result = DB::update($sql, $params);

        if (!empty($permission)) {
            foreach (json_decode($permission) as $index => $item) {
                $res = self::editPermissionById($index, $item->show, $item->insert, $item->edit, $item->delete);
                if ($res->status == 200 || $res->status == 208) {
                    $flag = true;
                } else {
                    $flag = false;
                }
            }
        }


        if (($result->status == 200 || $result->status == 208) && $flag) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    /**
     * Get Admin Role Info Permission Show
     * use admin-role-edit.php
     * @param $roleId int
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getRoleInfoPermissionFromShow($roleId)
    {

        $response = sendResponse(0, "");

        $sql = "SELECT * FROM tbl_admin_roles 
                    INNER JOIN tbl_admin_role_permissions ON tbl_admin_roles.role_id=tbl_admin_role_permissions.role_id 
                    INNER JOIN tbl_admin_permissions ON tbl_admin_permissions.permission_id=tbl_admin_role_permissions.permission_id
                         WHERE tbl_admin_roles.role_id=:role_id";
        $params = [
            'role_id' => $roleId,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Check Admin Email Exist
     * @param $email string
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function checkAdminEmail($email)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_admins` WHERE admin_email=:admin_email";
        $params = [
            'admin_email' => $email,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Check Admin Mobile Exist
     * @param $email string
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function checkAdminPhone($phone)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_admins` WHERE  admin_mobile=:admin_mobile";
        $params = [
            'admin_mobile' => $phone,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Add New Admin
     * @param $name string
     * @param $nickname string
     * @param $emailEnc string encrypt
     * @param $password string encrypt
     * @param $resultAvatar string
     * @param $role int null
     * @param $status string
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function setNewAdmin($name, $nickname, $mobile, $emailEnc, $password, $resultAvatar, $status, $role)
    {


        $response = sendResponse(0, "Error Msg");
        $sql = "INSERT INTO `tbl_admins`( `role_id`, `admin_email`, `admin_password`, `admin_name`, `admin_nickname`, `admin_mobile`,
        `admin_avatar`, `admin_status`, `admin_register_date`) VALUES (:role_id,:admin_email,:admin_password,:admin_name,:admin_nickname,
        :admin_mobile,:admin_avatar,:admin_status,:admin_register_date);SELECT LAST_INSERT_ID();";
        $params = [
            'role_id' => (int)$role,
            'admin_email' => $emailEnc,
            'admin_password' => $password,
            'admin_name' => $name,
            'admin_nickname' => $nickname,
            'admin_mobile' => $mobile,
            'admin_avatar' => $resultAvatar,
            'admin_status' => $status,
            'admin_register_date' => time(),
        ];
        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }
        return sendResponse(200, "", $params);
    }


    /**
     * Get Admin Info By Id
     * use Admin-edit.php
     * @param $id int
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getAdminById($id)
    {
        $response = sendResponse(0, "");
        $sql = "SELECT * FROM `tbl_admins` WHERE admin_id=:admin_id;";
        $params = [
            'admin_id' => $id,
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Edit Admin Ifo
     * use admin-edit.php adminAjax.php
     * @param $name string
     * @param $nickname string
     * @param $emailEnc string encrypt
     * @param $password string encrypt
     * @param $mobile string encrypt
     * @param $resultAvatar string
     * @param $role int null
     * @param $status string
     * @param $admin_id int
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function editAdmin($name, $nickname, $emailEnc, $password, $mobile, $status, $admin_id, $resultAvatar = "", $role = null)
    {
        $response = sendResponse(0, "");

        if (empty($resultAvatar)) {

            $sql = 'UPDATE `tbl_admins` SET `role_id`=:role_id,`admin_email`=:admin_email,`admin_password`=:admin_password,
`admin_name`=:admin_name,`admin_nickname`=:admin_nickname,`admin_mobile`=:admin_mobile,`admin_status`=:admin_status WHERE `admin_id`=:admin_id';
            $params = [
                'role_id' => $role,
                'admin_email' => $emailEnc,
                'admin_password' => $password,
                'admin_name' => $name,
                'admin_nickname' => $nickname,
                'admin_mobile' => $mobile,
                'admin_status' => $status,
                'admin_id' => $admin_id
            ];

        } else {
            $sql = 'UPDATE `tbl_admins` SET `role_id`=:role_id,`admin_email`=:admin_email,`admin_password`=:admin_password,
`admin_name`=:admin_name,`admin_nickname`=:admin_nickname,`admin_mobile`=:admin_mobile,`admin_avatar`=:admin_avatar,`admin_status`=:admin_status WHERE `admin_id`=:admin_id';
            $params = [
                'role_id' => $role,
                'admin_email' => $emailEnc,
                'admin_password' => $password,
                'admin_name' => $name,
                'admin_nickname' => $nickname,
                'admin_mobile' => $mobile,
                'admin_avatar' => $resultAvatar,
                'admin_status' => $status,
                'admin_id' => $admin_id
            ];
        }

        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }
        return $response;
    }


    /**
     * Get Admin Logs By Id
     * @param $id int
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getAdminLogsById($id)
    {

        $response = sendResponse(0, "");

        $params = [];

        if ($id == 0) {
            $sql = "SELECT * FROM `tbl_admin_logs` ORDER BY log_id DESC ;";
            $result = DB::rawQuery($sql, []);
        } else {
            $sql = "SELECT * FROM `tbl_admin_logs` WHERE admin_id=:admin_id ORDER BY log_id DESC ;";
            $result = DB::rawQuery($sql, ['admin_id' => $id]);
        }

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Get Admin Info By Email (EID)
     * @param $email string
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function getAdminByEmail($email)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_admins` WHERE admin_email=:admin_email;";
        $params = [
            'admin_email' => $email,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Update Admins have Is Old Role
     * @param $roleOld
     * @param $roleNew
     * @return stdClass
     */
    public static function updateAdminsReplaceRole($roleOld, $roleNew)
    {
        $response = sendResponse(0, "");

        $sql = "UPDATE `tbl_admins` SET `role_id`=:role_old WHERE `role_id`=:role_new";
        $params = [
            'role_old' => $roleNew,
            'role_new' => $roleOld,
        ];
        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    /**
     * Edit My Account Admin
     * @param $id int
     * @param $nickname string
     * @param $password string encrypt
     * @param $resultAvatar string
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function myaccountEdit($id, $nickname, $password, $resultAvatar = "")
    {
        $response = sendResponse(0, "");

        if (empty($resultAvatar)) {

            $sql = 'UPDATE `tbl_admins` SET `admin_password`=:admin_password,`admin_nickname`=:admin_nickname WHERE `admin_id`=:admin_id';
            $params = [
                'admin_password' => $password,
                'admin_nickname' => $nickname,
                'admin_id' => $id
            ];

        } else {
            $sql = 'UPDATE `tbl_admins` SET `admin_password`=:admin_password,`admin_nickname`=:admin_nickname,`admin_avatar`=:admin_avatar WHERE `admin_id`=:admin_id';
            $params = [
                'admin_password' => $password,
                'admin_nickname' => $nickname,
                'admin_avatar' => $resultAvatar,
                'admin_id' => $id
            ];
        }

        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }
        return $response;
    }


    /*****************************
     *  Add New Slug For Role
     *
     */
    public static function insertSlug($name)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = 'INSERT INTO `tbl_admin_slugs`(`slug_name`) VALUES (:slug_name);SELECT LAST_INSERT_ID();';
        $params = [
            'slug_name' => $name,
        ];
        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    public static function deleteSlug($slug_id)
    {
        $response = sendResponse(-10, "");

        $sql = 'DELETE FROM `tbl_admin_slugs` WHERE slug_id=:slug_id;';
        $params = [
            'slug_id' => $slug_id,
        ];
        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    public static function insertPermissions($slug_id, $permission_can_insert, $permission_can_edit, $permission_can_delete, $permission_can_show)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = 'INSERT INTO `tbl_admin_permissions`(`slug_id`,`permission_can_insert`,`permission_can_edit`,`permission_can_delete`,`permission_can_show`)
        VALUES (:slug_id,:permission_can_insert,:permission_can_edit,:permission_can_delete,:permission_can_show);SELECT LAST_INSERT_ID();';
        $params = [
            'slug_id' => $slug_id,
            'permission_can_insert' => $permission_can_insert,
            'permission_can_edit' => $permission_can_edit,
            'permission_can_delete' => $permission_can_delete,
            'permission_can_show' => $permission_can_show,
        ];
        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function deletePermissions($permission_id)
    {
        $response = sendResponse(-10, "");

        $sql = 'DELETE FROM `tbl_admin_permissions` WHERE permission_id=:permission_id;';
        $params = [
            'permission_id' => $permission_id,
        ];
        $result = DB::delete($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }

        return $response;
    }


    public static function insertRolePermissions($role_id, $permission_id)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = 'INSERT INTO `tbl_admin_role_permissions`(`role_id`,`permission_id`)
        VALUES (:role_id,:permission_id);SELECT LAST_INSERT_ID();';
        $params = [
            'role_id' => $role_id,
            'permission_id' => $permission_id,
        ];
        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }

    public static function addNewSlugForRole($slug, $insert, $edit, $delete, $show, $role_id)
    {
        $response = sendResponse(0, "Error Msg");

        $slug_id__RE = self::insertSlug($slug);
        if ($slug_id__RE->status == 200) {
            $slug_id = $slug_id__RE->response;

            $permission__RE = self::insertPermissions($slug_id, $insert, $edit, $delete, $show);
            if ($permission__RE->status == 200) {
                $permission = $permission__RE->response;

                $role__R = self::insertRolePermissions($role_id, $permission);

                if ($role__R->status == 200) {
                    return sendResponse(200, "", $role__R->response);

                } else {
                    $res1 = self::deleteSlug($slug_id);
                    $res2 = self::deletePermissions($permission);

                    return sendResponse(200, $res1, $res2);
                }

            } else {
                return self::deleteSlug($slug_id);
            }

        } else {
            return $response;
        }
    }


    /**
     * Get Admin Logs By Time
     * @param $adminID
     * @param $dateStart
     * @param $dateEnd
     * @return stdClass
     */
    public static function getAdminLogsByTime($adminID, $dateStart, $dateEnd)
    {
        $response = sendResponse(-10, "");

        $sql = 'SELECT * FROM `tbl_admin_logs` WHERE log_date>=:log_Start AND log_date<=:log_End AND admin_id=:admin_id;';
        $params = [
            'admin_id' => $adminID,
            'log_Start' => $dateStart,
            'log_End' => $dateEnd,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;

    }


    /**
     * Get Admins Logs
     * @param $dateStart
     * @param $dateEnd
     * @return stdClass
     */
    public static function getAdminsLogsByTime($dateStart, $dateEnd)
    {
        $response = sendResponse(-10, "");

        $sql = 'SELECT * FROM `tbl_admin_logs` WHERE log_date>=:log_Start AND log_date<=:log_End;';
        $params = [
            'log_Start' => $dateStart,
            'log_End' => $dateEnd,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;

    }


    /**
     * Add New Admin Custom
     * @param $name string
     * @param $nickname string
     * @param $emailEnc string encrypt
     * @param $password string encrypt
     * @param $resultAvatar string
     * @param $role int null
     * @param $status string
     * @return Object
     * @author Amir
     * @version 1.0.0
     */
    public static function setCustomNewAdmin($name, $nickname, $mobile, $emailEnc, $password, $resultAvatar, $status, $role)
    {


        $response = sendResponse(0, "Error Msg");
        $sql = "INSERT INTO `tbl_admins`( `role_id`, `admin_email`, `admin_password`, `admin_name`, `admin_nickname`, `admin_mobile`,
        `admin_avatar`, `admin_status`, `admin_register_date`) VALUES (:role_id,:admin_email,:admin_password,:admin_name,:admin_nickname,
        :admin_mobile,:admin_avatar,:admin_status,:admin_register_date);SELECT LAST_INSERT_ID();";
        $params = [
            'role_id' => (int)$role,
            'admin_email' => Security::encrypt($emailEnc),
            'admin_password' => Security::encrypt($password),
            'admin_name' => Security::encrypt($name),
            'admin_nickname' => $nickname,
            'admin_mobile' => Security::encrypt($mobile),
            'admin_avatar' => $resultAvatar,
            'admin_status' => $status,
            'admin_register_date' => time(),
        ];
        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }
        return $response;
    }


    /***********************************************************
     *  Check Sidebar Menu
     *
     */
    public static function getInfoPermissionsForSidebar()
    {
        $INF = Security::decrypt($_COOKIE['INF']);
        if ($INF && @json_decode($INF) && isset(json_decode($INF)->role_id) && json_decode($INF)->role_id) {
            $t = self::getAdminRolePermissonsForCheck(json_decode($INF)->role_id);
            if ($t->status == 200 && $t->response && !empty($t->response)) {
                return $t->response;
            }
        }
        return [];

    }

    public static function getSidebarPermission($permissions, $slug)
    {

        if ($permissions && !empty($permissions)) {
            $key = array_search($slug, array_column($permissions, 'slug_name'));

            if (!is_null($key) && $key !== false) {
                $temp = $permissions[$key];
                if ($temp->permission_can_insert == "yes" || $temp->permission_can_edit == "yes" || $temp->permission_can_delete == "yes" || $temp->permission_can_show == "yes") {
                    return "yes";
                }
            }
        }
        return "no";
    }


    /****************************************************
     **  Get Admin With Role Name
     **/


    /**
     * Get Admin With Role Name
     * @return stdClass
     */
    public static function getAllAdminWithRole()
    {

        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT * FROM `tbl_admins` INNER JOIN `tbl_admin_roles` ON tbl_admin_roles.role_id=tbl_admins.role_id";
        $params = [];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }
        return $response;
    }


    /**
     * @param $api
     * @param $mobile
     * @param $template
     * @param $price
     * @return false|mixed|stdClass|string
     * @author Amir
     */
    public static function sendSmsLowPrice($api, $mobile, $template, $price)
    {
        $response0 = Ghasedak::getCreditCron($api);
        if ($response0->status == 200) {
            if ($response0->response <= $price) {
                $t = explode(",", $mobile);
                $api = new GhasedakApi($api);
                foreach ($t as $loop) {
                    $result = $api->Verify($loop, 1, $template, number_format($response0->response));
                }
                if ($result->result->code == 200) {
                    $response0 = sendResponse(200, 'Sent successfully');
                } else {
                    $response0 = sendResponse(-10, 'Sent Error');
                }
                return $response0;
            }
        }
        return Utils::getFileValue('settings.txt', 'ghasedak_admins_mobile');
    }


    public static function setAdminFromAssign($adminTo, $relationId, $type)
    {
        $response = sendResponse(0, "Error Msg", []);
        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }
        $sql = "INSERT INTO `tbl_assign` ( `admin_from`, `admin_to`, `assign_relation`, `assign_type`, `assign_submit_date`)
                VALUES (:admin_from,:admin_to,:assign_relation,:assign_type,:assign_submit_date)";
        $params = [
            'admin_from' => $admin_id,
            'admin_to' => $adminTo,
            'assign_relation' => $relationId,
            'assign_type' => $type,
            'assign_submit_date' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function getAdminFromAssign($relationId, $type)
    {
        $response = sendResponse(0, "Error Msg", []);
        $sql = "SELECT * FROM `tbl_assign` INNER JOIN `tbl_admins` ON tbl_admins.admin_id=tbl_assign.admin_to WHERE `assign_relation`=:assign_relation AND `assign_type`=:assign_type ";
        $params = [
            'assign_relation' => $relationId,
            'assign_type' => $type,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function addAdminPermissionAfterSlug($slug_id, $role_id, $permission_can_insert, $permission_can_edit, $permission_can_delete, $permission_can_show)
    {

        $response = sendResponse(0, "", 0);
        $sql = 'INSERT INTO `tbl_admin_permissions`( `slug_id`, `permission_can_insert`, `permission_can_edit`, `permission_can_delete`, `permission_can_show`) VALUES (:slug_id,:permission_can_insert,:permission_can_edit,:permission_can_delete,:permission_can_show);SELECT LAST_INSERT_ID();';
        $params = [
            'slug_id' => $slug_id,
            'permission_can_insert' => $permission_can_insert,
            'permission_can_edit' => $permission_can_edit,
            'permission_can_delete' => $permission_can_delete,
            'permission_can_show' => $permission_can_show,
        ];
        $result = DB::insert($sql, $params);

        if ($result->status == 200) {
            $sql = 'INSERT INTO `tbl_admin_role_permissions`( `role_id`, `permission_id`) VALUES (:role_id,:permission_id);';
            $params = [
                'role_id' => $role_id,
                'permission_id' => $result->response,
            ];
            $result = DB::insert($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "", $result->response);
            }
        }
        return $response;

    }


    public static function SetAdminUrl($value)
    {
        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }
        if ($admin_id > 0) {
            $url = SITE_ROOT . "/db/manager-" . $admin_id . ".json";

            $json = file_get_contents($url);
            $data = json_decode($json);
            $array = [];
            $array['date'] = time();
            $array['time'] = Utils::jDate('Y-m-d--H:i', time());
            $array['value'] = $value;

            $data[] = $array;
            file_put_contents($url, json_encode($data));
        }
    }
}