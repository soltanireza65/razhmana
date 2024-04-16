<?php

use MJ\Security\Security;
use MJ\Utils\Utils;

$pageSlug = "personel";
// permission_can_show
global $lang;


$language = (isset($_COOKIE['language'])) ? $_COOKIE['language'] : 'fa_IR';
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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_show == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1
        /**
         * Get All Cargo Count
         */
        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('admin-css', '/dist/css/admin/admin.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
        enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.css');
        enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
        enqueueStylesheet('editable-css', '/dist/libs/x-editable/bootstrap-editable/css/bootstrap-editable.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
        enqueueScript('editable-js', '/dist/libs/x-editable/bootstrap-editable/js/bootstrap-editable.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('FA-css', '/dist/libs/fontawesome/all.min.js');
        enqueueScript('FA-css', '/dist/js/admin/personels/personels-add.js');

//        enqueueScript('cargo-info', '/dist/js/admin/ground/cargo-info.init.js');
        getHeader($lang["driver_services_detail"], [
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
                    <div class="row">

                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <?= $lang['please_enter_information'] ?>
                                    </div>
                                    <div class="mj-cv-avatar-upload">
                                        <div class="avatar-edit">
                                            <input type='file' id="imageUpload" accept=".png, .jpg, .jpeg"/>
                                            <label class="mj-cv-avatar-label" for="imageUpload">
                                                <div><span class="fa-camera"></span></div>
                                                <div class="avatar-preview">
                                                    <div id="imagePreview"
                                                         style="background-image: url('/dist/images/drivers/empty-profile.svg');">
                                                    </div>
                                                </div>

                                            </label>
                                        </div>

                                    </div>

                                    <div class="mj-personels-input-info">
                                        <div class="form-floating mj-personels-inputs-extra ">
                                            <input type="text" class="form-control" id="personel_name_fa_IR"
                                                   placeholder="name@example.com">
                                            <label for="floatingInput"> <?= $lang['pb_name'] ?></label>
                                        </div>
                                        <div class="form-floating mj-personels-inputs-extra ">
                                            <input type="text" class="form-control" id="personel_name_en_US"
                                                   placeholder="name@example.com">
                                            <label for="floatingInput"> <?= $lang['pb_name'] ?> US</label>
                                        </div>
                                        <div class="form-floating mj-personels-inputs-extra ">
                                            <input type="text" class="form-control" id="personel_name_tr_Tr"
                                                   placeholder="name@example.com">
                                            <label for="floatingInput"> <?= $lang['pb_name'] ?> TR</label>
                                        </div>
                                        <div class="form-floating mj-personels-inputs-extra ">
                                            <input type="text" class="form-control" id="personel_name_ru_RU"
                                                   placeholder="name@example.com">
                                            <label for="floatingInput"> <?= $lang['pb_name'] ?> RU</label>
                                        </div>
                                        <div class="form-floating mj-personels-inputs-extra ">
                                            <input type="text" class="form-control" id="personel_lname_fa_IR"
                                                   placeholder="name@example.com">
                                            <label for="floatingInput">  <?= $lang['pb_lname'] ?></label>
                                        </div>
                                        <div class="form-floating mj-personels-inputs-extra ">
                                            <input type="text" class="form-control" id="personel_lname_en_US"
                                                   placeholder="name@example.com">
                                            <label for="floatingInput">  <?= $lang['pb_lname'] ?> US</label>
                                        </div>
                                        <div class="form-floating mj-personels-inputs-extra ">
                                            <input type="text" class="form-control" id="personel_lname_tr_Tr"
                                                   placeholder="name@example.com">
                                            <label for="floatingInput">  <?= $lang['pb_lname'] ?> TR</label>
                                        </div>
                                        <div class="form-floating mj-personels-inputs-extra ">
                                            <input type="text" class="form-control" id="personel_lname_ru_RU"
                                                   placeholder="name@example.com">
                                            <label for="floatingInput">  <?= $lang['pb_lname'] ?> RU</label>
                                        </div>
                                        <div class="form-floating mj-personels-inputs-extra ">
                                            <input type="text" class="form-control" id="personel_job_fa_IR"
                                                   placeholder="name@example.com">
                                            <label for="floatingInput"><?= $lang['personel_job_title'] ?></label>
                                        </div>
                                        <div class="form-floating mj-personels-inputs-extra ">
                                            <input type="text" class="form-control" id="personel_job_en_US"
                                                   placeholder="name@example.com">
                                            <label for="floatingInput"><?= $lang['personel_job_title'] ?> US</label>
                                        </div>
                                        <div class="form-floating mj-personels-inputs-extra ">
                                            <input type="text" class="form-control" id="personel_job_tr_Tr"
                                                   placeholder="name@example.com">
                                            <label for="floatingInput"><?= $lang['personel_job_title'] ?> TR</label>
                                        </div>
                                        <div class="form-floating mj-personels-inputs-extra ">
                                            <input type="text" class="form-control" id="personel_job_ru_RU"
                                                   placeholder="name@example.com">
                                            <label for="floatingInput"><?= $lang['personel_job_title'] ?> RU</label>
                                        </div>
                                        <div class="form-floating mj-personels-inputs-extra ">
                                            <input type="email" class="form-control" id="personel_email"
                                                   placeholder="name@example.com">
                                            <label for="floatingInput"><?= $lang['email'] ?></label>
                                        </div>
                                        <div class="d-flex mj-input-box mj-country-code-box">
                                            <input type="text" inputmode="tel" class="form-control mj-input" id="phone"
                                                   name="phone" maxlength="10" lang="en"
                                                   placeholder="<?= $lang['phone_number'] ?>" dir="ltr">
                                            <select id="phone-country-code" name="country-code" data-width="100px"
                                                    dir="ltr">
                                                <?php

                                                $countriesData = Location::getAllCountriesFromLoginPage();
                                                $countries = $countriesData->response;
                                                $selected = '';
                                                if (substr(($_COOKIE['language']), 0, 2) == 'en') {
                                                    $selected = 86;
                                                } elseif (substr(($_COOKIE['language']), 0, 2) == 'ru') {
                                                    $selected = 2;
                                                } elseif (substr(($_COOKIE['language']), 0, 2) == 'fa') {
                                                    $selected = 1;
                                                } elseif (substr(($_COOKIE['language']), 0, 2) == 'tr') {
                                                    $selected = 3;
                                                }
                                                foreach ($countries as $key => $country) {


                                                    ?>
                                                    <option
                                                        data-image="<?= Utils::fileExist($country->country_flag, '/uploads/flags/empty.webp') ?>"
                                                        <?= $country->country_id == $selected ? 'selected' : '' ?>
                                                        value="<?= $country->country_display_code ?>"><?= $country->country_display_code ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="d-flex mj-input-box mj-country-code-box">
                                            <input type="text" inputmode="tel" class="form-control mj-input"
                                                   id="home-number"
                                                   name="phone" maxlength="10" lang="en"
                                                   placeholder="<?= $lang['auth_phone'] ?>" dir="ltr">
                                            <select id="home-country-code" name="country-code" data-width="100px"
                                                    dir="ltr">
                                                <?php

                                                $countriesData = Location::getAllCountriesFromLoginPage();
                                                $countries = $countriesData->response;
                                                $selected = '';
                                                if (substr(($_COOKIE['language']), 0, 2) == 'en') {
                                                    $selected = 86;
                                                } elseif (substr(($_COOKIE['language']), 0, 2) == 'ru') {
                                                    $selected = 2;
                                                } elseif (substr(($_COOKIE['language']), 0, 2) == 'fa') {
                                                    $selected = 1;
                                                } elseif (substr(($_COOKIE['language']), 0, 2) == 'tr') {
                                                    $selected = 3;
                                                }
                                                foreach ($countries as $key => $country) {


                                                    ?>
                                                    <option
                                                        data-image="<?= Utils::fileExist($country->country_flag, '/uploads/flags/empty.webp') ?>"
                                                        <?= $country->country_id == $selected ? 'selected' : '' ?>
                                                        value="<?= $country->country_display_code ?>"><?= $country->country_display_code ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="d-flex mj-input-box mj-country-code-box">
                                            <input type="text" inputmode="tel" class="form-control mj-input"
                                                   id="whatsapp"
                                                   name="phone" maxlength="10" lang="en"
                                                   placeholder="<?= $lang['support_whatsapp_small'] ?>" dir="ltr">
                                            <select id="whatsapp-country-code" name="country-code" data-width="100px"
                                                    dir="ltr">
                                                <?php

                                                $countriesData = Location::getAllCountriesFromLoginPage();
                                                $countries = $countriesData->response;
                                                $selected = '';
                                                if (substr(($_COOKIE['language']), 0, 2) == 'en') {
                                                    $selected = 86;
                                                } elseif (substr(($_COOKIE['language']), 0, 2) == 'ru') {
                                                    $selected = 2;
                                                } elseif (substr(($_COOKIE['language']), 0, 2) == 'fa') {
                                                    $selected = 1;
                                                } elseif (substr(($_COOKIE['language']), 0, 2) == 'tr') {
                                                    $selected = 3;
                                                }
                                                foreach ($countries as $key => $country) {


                                                    ?>
                                                    <option
                                                        data-image="<?= Utils::fileExist($country->country_flag, '/uploads/flags/empty.webp') ?>"
                                                        <?= $country->country_id == $selected ? 'selected' : '' ?>
                                                        value="<?= $country->country_display_code ?>"><?= $country->country_display_code ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-floating mj-personels-inputs-extra ">
                                            <input type="email" class="form-control" id="personel_ref_code"
                                                   placeholder="code">
                                            <label for="floatingInput"><?= $lang['refferal_code'] ?></label>
                                        </div>
                                    </div>
                                    <div class="form-floating mj-personels-inputs-extra mj-personels-inputs-textarea">
                                        <textarea class="form-control " placeholder="Leave a comment here"
                                                  id="personel_desc_fa_IR" style="height: 100px"></textarea>
                                        <label for="personel_desc_fa_IR"><?= $lang['b_more_desc'] ?></label>
                                    </div>
                                    <div class="form-floating mj-personels-inputs-extra mj-personels-inputs-textarea">
                                        <textarea class="form-control" placeholder="Leave a comment here"
                                                  id="personel_desc_en_US" style="height: 100px"></textarea>
                                        <label for="personel_desc_en_US"><?= $lang['b_more_desc'] ?> US</label>
                                    </div>
                                    <div class="form-floating mj-personels-inputs-extra mj-personels-inputs-textarea">
                                        <textarea class="form-control" placeholder="Leave a comment here"
                                                  id="personel_desc_tr_Tr" style="height: 100px"></textarea>
                                        <label for="personel_desc_tr_Tr"><?= $lang['b_more_desc'] ?> TR</label>
                                    </div>
                                    <div class="form-floating mj-personels-inputs-extra mj-personels-inputs-textarea">
                                        <textarea class="form-control" placeholder="Leave a comment here"
                                                  id="personel_desc_ru_RU" style="height: 100px"></textarea>
                                        <label for="personel_desc_ru_RU"><?= $lang['b_more_desc'] ?> RU</label>
                                    </div>

                                    <div>
                                        <button class="btn btn-primary" id="add-personel">
                                            <?= $lang['submit'] ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


            </div>
            <input type="hidden" id="token2" name="token2" value="<?= Security::initCSRF2() ?>">
            <script>
                var var_lang = <?=json_encode($lang)?>
            </script>
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
            [
                $lang['help_cargo_1'],
                $lang['help_cargo_2'],
                $lang['help_cargo_3'],
                $lang['help_cargo_4'],
                $lang['help_cargo_5'],
                $lang['help_cargo_6'],
                $lang['help_cargo_7'],
            ]
        );

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