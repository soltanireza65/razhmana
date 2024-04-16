<?php
$pageSlug = "field_c";

// permission_can_edit

use MJ\Security\Security;
use MJ\Utils\Utils;

global $lang;

include_once getcwd() . '/views/admin/header-footer.php';

// start roles 1
$resultCheckAdminLogin = Admin::checkAdminLogin();
$dataCheckAdminLogin = [];
if ($resultCheckAdminLogin->status == 200 && !empty($resultCheckAdminLogin->response)) {
    $dataCheckAdminLogin = $resultCheckAdminLogin->response;

    if ($dataCheckAdminLogin->admin_status == "active") {


        $dataCheckAdminRoleForCheck = [];
        if (!empty($dataCheckAdminLogin->role_id)) {
            $resultCheckAdminRoleForCheck = Admin::checkAdminRoleForCheck($dataCheckAdminLogin->role_id);
            if ($resultCheckAdminRoleForCheck->status == 200) {
                $dataCheckAdminRoleForCheck = $resultCheckAdminRoleForCheck->response;
            }
        }


        $flagSlug = false;
        if (!empty($dataCheckAdminRoleForCheck) && json_decode($dataCheckAdminRoleForCheck)->role_status == "active") {
            foreach (json_decode($dataCheckAdminRoleForCheck)->permissons as $item000) {
                if ($item000->slug_name == $pageSlug && $item000->permission_can_edit == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1

        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('editable-js', '/dist/libs/x-editable/bootstrap-editable/js/bootstrap-editable.min.js');
        enqueueScript('settings-field', '/dist/js/admin/settings/settings-field.init.js');

        getHeader($lang["auth_list"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_edit',
        ]);

        // start roles 2
        if ($flagSlug) {
            // end roles 2
            ?>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row justify-content-between mb-3">
                                <div class="col-auto">
                                    <h4 class="page-title"><?= $lang['auth_list']; ?></h4>
                                </div>
                                <div class="col-auto">
                                    <div class="text-lg-end my-1 my-lg-0">
                                    </div>
                                </div><!-- end col-->
                            </div>

                            <div class="table-responsive">
                                <table id="orders-table" data-page-length='10' data-order='[[ 0, "desc" ]]'
                                       class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?= $lang['title']; ?></th>
                                        <th><?= $lang['users_type']; ?></th>
                                        <th><?= $lang['type']; ?></th>
                                        <th><?= $lang['rate']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <?php $j = 0; ?>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_title_company']; ?></td>
                                        <td><?= $lang['businessman']; ?></td>
                                        <td><?= $lang['a_text']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_company"
                                               data-tj-type="auth_company"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_company"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_manager_company']; ?></td>
                                        <td><?= $lang['businessman']; ?></td>
                                        <td><?= $lang['a_text']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_manager"
                                               data-tj-type="auth_manager"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_manager"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_address']; ?></td>
                                        <td><?= $lang['businessman'] . " , ", $lang['driver']; ?></td>
                                        <td><?= $lang['a_text']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_address"
                                               data-tj-type="auth_address"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_address"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_phone']; ?></td>
                                        <td><?= $lang['businessman'] . " , ", $lang['driver']; ?></td>
                                        <td><?= $lang['a_text']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_phone"
                                               data-tj-type="auth_phone"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_phone"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_fox']; ?></td>
                                        <td><?= $lang['businessman']; ?></td>
                                        <td><?= $lang['a_text']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_fox"
                                               data-tj-type="auth_fox"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_fox"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_mail']; ?></td>
                                        <td><?= $lang['businessman']; ?></td>
                                        <td><?= $lang['a_text']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_mail"
                                               data-tj-type="auth_mail"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_mail"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_site']; ?></td>
                                        <td><?= $lang['businessman']; ?></td>
                                        <td><?= $lang['a_text']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_site"
                                               data-tj-type="auth_site"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_site"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_id_card']; ?></td>
                                        <td><?= $lang['businessman'] . " , ", $lang['driver']; ?></td>
                                        <td><?= $lang['a_img']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_id-card-image"
                                               data-tj-type="auth_id-card-image"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_id-card-image"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_passport_image']; ?></td>
                                        <td><?= $lang['businessman'] . " , ", $lang['driver']; ?></td>
                                        <td><?= $lang['a_img']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_passport-image"
                                               data-tj-type="auth_passport-image"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_passport-image"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_birthday_city']; ?></td>
                                        <td><?= $lang['driver']; ?></td>
                                        <td><?= $lang['a_text']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_birthday-city"
                                               data-tj-type="auth_birthday-city"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_birthday-city"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_birthday_date']; ?></td>
                                        <td><?= $lang['driver']; ?></td>
                                        <td><?= $lang['a_text']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_birthday-date"
                                               data-tj-type="auth_birthday-date"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_birthday-date"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_phone_national']; ?></td>
                                        <td><?= $lang['driver']; ?></td>
                                        <td><?= $lang['a_text']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_phone-national"
                                               data-tj-type="auth_phone-national"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_phone-national"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_insurance_type']; ?></td>
                                        <td><?= $lang['driver']; ?></td>
                                        <td><?= $lang['a_text']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_insurance-type"
                                               data-tj-type="auth_insurance-type"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_insurance-type"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_insurance_number']; ?></td>
                                        <td><?= $lang['driver']; ?></td>
                                        <td><?= $lang['a_text']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_insurance-number"
                                               data-tj-type="auth_insurance-number"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_insurance-number"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><?= $j += 1; ?></td>
                                        <td><?= $lang['auth_car_card']; ?></td>
                                        <td><?= $lang['driver']; ?></td>
                                        <td><?= $lang['a_text']; ?></td>
                                        <td></td>
                                        <td>
                                         <span id="auth_car-card-image"
                                               data-tj-type="auth_car-card-image"
                                               data-type="number"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['click_for_edit']; ?>">
                                               <?= Utils::getFileValue("settings.txt", "auth_car-card-image"); ?>
                                           </span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF2() ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'error' => $lang['error'],
                        'successful' => $lang['successful'],
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'empty_input' => $lang['empty_input'],
                        'token_error' => $lang['token_error'],
                        'error_mag' => $lang['error_mag'],
                        'a_empty' => $lang['a_empty'],
                    ];
                    print_r(json_encode($var_lang));  ?>';
            </script>
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter();

        // start roles 4
    } else {
        setcookie('EID', null, -1, '/');
        setcookie('UID', null, -1, '/');
        setcookie('INF', null, -1, '/');
        unset($_COOKIE['EID']);
        unset($_COOKIE['UID']);
        unset($_COOKIE['INF']);

        header('Location: ' . ADMIN_HEADER_LOCATION);
    }
} else {

    setcookie('EID', null, -1, '/');
    setcookie('UID', null, -1, '/');
    setcookie('INF', null, -1, '/');
    unset($_COOKIE['EID']);
    unset($_COOKIE['UID']);
    unset($_COOKIE['INF']);

    header('Location: ' . ADMIN_HEADER_LOCATION);
}
// end roles 4