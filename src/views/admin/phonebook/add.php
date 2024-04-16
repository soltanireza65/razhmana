<?php

use MJ\Security\Security;
use MJ\Utils\Utils;

$pageSlug = "phonebook";
// permission_can_show

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_insert == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1
        //custom css
        enqueueStylesheet('phonebook-css', '/dist/css/admin/phonebook-detail.css');
        // Load Stylesheets & Icons
        enqueueStylesheet('s2-css', '/dist/libs/select2/css/select2.min.css');
        // Load Script In Footer
        enqueueScript('data-table', '/dist/js/admin/phonebook/phonebook-add.js');
        enqueueScript('s2-js', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('sweetalert2-js', '/dist/libs/sweetalert/sweetalert.js');
        // header text
        getHeader($lang["driver_cv_list"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_show',
        ]);
        // start roles 2
        if ($flagSlug) {
            // end roles 2
            ?>
            <!--start custom html-->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card p-3">
<!--                        <div class="mt-4">-->
<!--                            <div class="mj-member-type">-->
<!--                                <div class="mj-member-type-radio">-->
<!--                                    <span>  :--><?php //= $lang['pb_user_type'] ?><!--</span>-->
<!--                                    <input type="radio" id="age1" name="user_types" value="driver" checked>-->
<!--                                    <label for="age1">-->
<!--                                        <span class="py-1">--><?php //= $lang['pb_driver'] ?><!--</span>-->
<!--                                    </label>-->
<!--                                    <input type="radio" id="age2" name="user_types" value="businessman">-->
<!--                                    <label for="age2">-->
<!--                                        <span class="py-1">--><?php //= $lang['pb_businessman'] ?><!--</span>-->
<!--                                    </label>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </div>-->


                        <div style="gap: 10px;row-gap: 10px" class="row mt-2 mb-4">
                            <div class=" mj-col-style-pbook col-lg-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="user_name"
                                           placeholder="name@example.com">
                                    <label for="user_name"><?= $lang['pb_name'] ?></label>
                                </div>
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <div class="form-floating ">
                                    <input type="text" class="form-control" id="user_lname"
                                           placeholder="name@example.com">
                                    <label for="user_lname"><?= $lang['pb_lname'] ?></label>
                                </div>
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <div class="form-floating mj-pbooks-phone-add">
                                    <input type="number" class="form-control" id="user_phone"
                                           placeholder="name@example.com">
                                    <label for="user_phone"><?= $lang['pb_phone'] ?></label>
                                    <select id="country-code" name="country-code" data-width="100px" dir="ltr">
                                        <?php
                                        // $countries = json_decode(Utils::getFileValue('countries.json', null, false));
                                        $countriesData = Location::getAllCountriesFromLoginPage();
                                        $countries = $countriesData->response;
                                        $selected = '' ;
                                        if( substr(($_COOKIE['language'])  ,0,2) == 'en' ){
                                            $selected = 86 ;
                                        }elseif (substr(($_COOKIE['language'])  ,0,2) == 'ru'){
                                            $selected = 2 ;
                                        }elseif (substr(($_COOKIE['language'])  ,0,2) == 'fa'){
                                            $selected = 1 ;
                                        }elseif (substr(($_COOKIE['language'])  ,0,2) == 'tr'){
                                            $selected = 3 ;
                                        }
                                        foreach ($countries as $key => $country) {


                                            ?>
                                            <option data-image="<?= Utils::fileExist($country->country_flag, '/uploads/flags/empty.webp') ?>"
                                                <?=  $country->country_id  ==  $selected ? 'selected' : '' ?>
                                                    value="<?= $country->country_display_code ?>"><?= $country->country_display_code ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <div class="form-floating mj-pbooks-phone-add">
                                    <input type="number" class="form-control" id="user_home_number"
                                           placeholder="name@example.com">
                                    <label for="user_home_number"><?= $lang['pb_home_phone'] ?></label>
                                    <select id="country-code-2" name="country-code" data-width="100px" dir="ltr">
                                        <?php
                                        // $countries = json_decode(Utils::getFileValue('countries.json', null, false));
                                        $countriesData = Location::getAllCountriesFromLoginPage();
                                        $countries = $countriesData->response;
                                        $selected = '' ;
                                        if( substr(($_COOKIE['language'])  ,0,2) == 'en' ){
                                            $selected = 86 ;
                                        }elseif (substr(($_COOKIE['language'])  ,0,2) == 'ru'){
                                            $selected = 2 ;
                                        }elseif (substr(($_COOKIE['language'])  ,0,2) == 'fa'){
                                            $selected = 1 ;
                                        }elseif (substr(($_COOKIE['language'])  ,0,2) == 'tr'){
                                            $selected = 3 ;
                                        }
                                        foreach ($countries as $key => $country) {


                                            ?>
                                            <option data-image="<?= Utils::fileExist($country->country_flag, '/uploads/flags/empty.webp') ?>"
                                                <?=  $country->country_id  ==  $selected ? 'selected' : '' ?>
                                                    value="<?= $country->country_display_code ?>"><?= $country->country_display_code ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <div class="form-floating mb-1">
                                    <input type="text" class="form-control" id="company_name"
                                           placeholder="name@example.com"
                                           >
                                    <label for="company_name"><?= $lang['pb_company_name'] ?></label>
                                </div>
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <span class="mj-inputs-no-head"><?= $lang['pb_cargo_type'] ?></span>
                                <div class="mj-detail-member-zone ">
                                    <select id="detail-member-zone" class="form-select"
                                            aria-label="Default select example">
                                        <option value=""></option>
                                        <option value="inout" selected><?= $lang['pb_cargointernal_external'] ?></option>
                                        <option value="out"><?= $lang['pb_cargo_external'] ?></option>
                                        <option value="in " ><?= $lang['pb_cargo_internal'] ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <span class="mj-inputs-no-head"><?= $lang['pb_user_type'] ?></span>
                                <div class="mj-detail-member-zone ">
                                    <select id="detail-member-type" class="form-select"
                                            aria-label="Default select example">
                                        <option value="driver"><?= $lang['pb_driver'] ?></option>
                                        <option value="businessman"><?= $lang['pb_businessman'] ?></option>
                                        <option value="transportation_company"><?= $lang['pb_transportation_company'] ?></option>
                                        <option value="dealer"><?= $lang['pb_dealer'] ?></option>
                                        <option value="shiping"><?= $lang['pb_shiping'] ?></option>
                                        <option value="dischager"><?= $lang['pb_dischager'] ?></option>
                                        <option value="keeper"><?= $lang['pb_keeper'] ?></option>
                                        <option value="other"><?= $lang['pb_other'] ?></option>
                                        <option value="guest" selected><?= $lang['pb_guest'] ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <span class="mj-inputs-no-head"><?= $lang['pb_user_access'] ?></span>
                                <div class="mj-detail-member-status ">
                                    <select id="detail-member-status" class="form-select"
                                            aria-label="Default select example">
                                        <option value=""></option>
                                        <option value="access" selected><?= $lang['pb_access'] ?></option>
                                        <option value="not_access" ><?= $lang['pb_not_access'] ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <span class="mj-inputs-no-head"><?= $lang['pb_car_types'] ?></span>
                                <div class="mj-detail-member-car-type mb-1">
                                    <select id="detail-member-car-type" class="form-select"
                                            aria-label="Default select example" multiple="multiple">
                                        <option value=""></option>
                                        <?php
                                        $carTypes = Driver::getCarTypes();
                                        foreach ($carTypes->response as $item) {
                                            ?>
                                            <option value="<?= $item->TypeId ?>"><?= $item->TypeName ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>

                                </div>
                            </div>

                            <div class=" mj-col-style-pbook-12 col-lg-12">
                                <span class="mj-inputs-no-head"><?= $lang['pb_fav_contries'] ?></span>
                                <div class="mj-detail-member-fav-conts ">
                                    <select id="detail-member-fav-conts" multiple="multiple" class="form-select"
                                            aria-label="Default select example">
                                        <option value="all">
                                            <?= $lang['all']; ?>
                                        </option>
                                        <?php
                                        $countries = Location::getCountriesList();
                                        foreach ($countries->response as $item) {
                                            ?>
                                            <option value="<?= $item->CountryId ?>"><?= $item->CountryName ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class=" mj-col-style-pbook-12 col-lg-12">
                                <div class=" mj-detail-admin-activity form-floating">
                                        <textarea class="form-control" placeholder="Leave a comment here"
                                                  id="activity_summery"></textarea>
                                    <label for="activity_summery"><?= $lang['pb_summray_activity'] ?></label>
                                </div>
                            </div>
                            <div class=" col-lg-4">
                                <button type="button"
                                        class="btn btn-primary mj-detail-submit-btn"><?= $lang['pb_submit'] ?></button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end custom html-->
            <input type="hidden" id="token" name="token"
                   value="<?= Security::initCSRF2() ?>">
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3
        getFooter(
            [
                $lang['help_academy_1'],
                $lang['help_academy_2'],
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