<?php

use MJ\Security\Security;
use MJ\Utils\Utils;

$pageSlug = "cargo";
// permission_can_show

global $lang;


$cv_id = $_REQUEST['id'];

$my_cv = CV::getCvDetailById($cv_id);
if ($my_cv->status == 200) {
    $my_cv = $my_cv->response[0];
} else {
    $my_cv = [];
    header("Location: /");
}
file_put_contents('temp.json', json_encode($my_cv));
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
        enqueueScript('lightbox', '/dist/js/admin/driver-services/driver-detail.js');
        enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.min.js');

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
            <div class="modal fade mj-accept-modal" id="accept" data-bs-backdrop="static" data-bs-keyboard="false"
                 tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">تایید</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            آیا از تایید رزومه مطمئن هستید؟
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" id="accept-driver-cv"
                                    data-cv-id="<?= $my_cv->cv_id ?>"><?= $lang['yes'] ?></button>
                            <button type="button" class="btn btn-secondary"
                                    data-bs-dismiss="modal"><?= $lang['no'] ?></button>

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade mj-reject-modal" id="reject" data-bs-backdrop="static" data-bs-keyboard="false"
                 tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="staticBackdropLabel">تایید نکردن اطلاعات</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div>لطفا دلیل رد خود را وارد کنید</div>
                            <div class="form-floating">
                                <textarea class="form-control" placeholder="دلیل خود را وارد کنید"
                                          id="cv-reject-detail"></textarea>
                                <label for="cv-reject-detail">دلیل ...</label>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button id="reject-driver-cv" type="button" class="btn btn-primary"
                                    data-cv-id="<?= $my_cv->cv_id ?>">ارسال
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن!</button>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="mj-driver-cv-prsonal">
                    <div class="mj-cv-item">
                        <div class="mj-driver-image">
                            <img src="<?= Utils::fileExist($my_cv->cv_user_avatar, POSTER_DEFAULT); ?>" alt="avatar">
                        </div>
                        <div class="mj-driver-name mb-1"><?= $my_cv->cv_name . ' ' . $my_cv->cv_lname; ?></div>
                        <div class="mj-driver-city mb-1">
                            <span
                                id=""><?= array_column(json_decode($my_cv->city_name, true), 'value', 'slug')[$language] ?></span>
                            /
                            <span><?= Location::getCountryByCityId($my_cv->city_id)->CountryName ?></span>
                        </div>
                        <div class="mj-driver-birthday mb-1">
                            <span><?= $lang['u_driver_cv_birth_day'] ?>:</span>
                            <span><?= Utils::getTimeByLang($my_cv->cv_brith_date) ?></span>
                        </div>
                        <div class="mj-driver-marriage mb-1">
                            <span><?= $lang['cv_drivers_marital'] ?>:</span>
                            <span><?= $my_cv->cv_marital_status == 'married' ? $lang['u_married'] : $lang['u_single'] ?></span>
                        </div>
                        <div class="mj-driver-sex mb-1">
                            <span><?= $lang['u_driver_cv_gender'] ?>:</span>
                            <span><?= $my_cv->cv_gender == 'mr' ? $lang['u_mr'] : $lang['u_ms']; ?></span>
                        </div>

                    </div>
                    <div class="mj-cv-item">
                        <div class="mj-driver-cv-item-title">
                            <span><?= $lang['cv_drivers_address'] ?></span>
                        </div>

                        <div class="mj-driver-item-prsonal-detail">
                            <span><?= $lang['u_driver_cv_phonenumber'] ?>:</span>
                            <span dir="ltr"><?= $my_cv->cv_mobile ?></span>
                        </div>
                        <div class="mj-driver-item-prsonal-detail">
                            <span><?= $lang['u_driver_cv_whatsapp'] ?>:</span>
                            <span dir="ltr">+<?= $my_cv->cv_whatsapp ?></span>
                        </div>
                        <div class="mj-driver-item-prsonal-detail">
                            <span><?= $lang['u_driver_cv_address'] ?>:</span>
                            <span><?= $my_cv->cv_address ?></span>
                        </div>
                        <div id="countries" class="mj-driver-item-prsonal-detail">
                            <span><?= $lang['cv_drivers_fav_country'] ?>:</span>
                            <?php
                            if (isset($my_cv->cv_faviroite_country) && $my_cv->cv_faviroite_country != 'null') {
                                $countries = Location::getCountriesList();
                                foreach ($countries->response as $item) {
                                    if (in_array($item->CountryId, json_decode($my_cv->cv_faviroite_country))) {
                                        ?>
                                        <span>
                                        <?= $item->CountryName ?>
                                    </span>
                                        <?php
                                    }
                                }
                            }
                            ?>
                        </div>


                    </div>
                </div>
                <div class="card-body mj-driver-cv-card-body">

                    <div class="mj-cv-item">
                        <div class="mj-driver-cv-item-title">
                            <span><?= $lang['cv_smartcard_status'] ?></span>
                        </div>

                        <div class="mj-driver-item-detail">
                            <span><?= $lang['cv_smartcard_number'] ?>:</span>
                            <span><?= ($my_cv->cv_smartcard_status == 'yes') ? $my_cv->cv_smartcard_number : '' ?></span>
                        </div>
                        <div class="mj-driver-item-detail">
                            <span><?= $lang['driver_services_expire_date'] ?>:</span>
                            <span><?= ($my_cv->cv_smartcard_status == 'yes') ? Utils::getTimeByLang($my_cv->cv_smartcard_date) : '' ?></span>
                        </div>
                        <div class="mj-driver-item-detail-images">
                            <span><?= $lang['driver_services_images'] ?>:</span>
                            <div><?= $lang['driver_services_images_preview'] ?></div>
                        </div>
                        <div class="mj-driver-item-detail-images-item">
                            <?php foreach (json_decode($my_cv->cv_smartcard_image) as $item) { ?>
                                <img class="me-2" src="<?= Utils::fileExist($item, POSTER_DEFAULT); ?>" alt="">
                            <?php }
                            if (count(json_decode($my_cv->cv_smartcard_image)) == 0) {
                                ?>
                                <img class="me-2" src="<?= POSTER_DEFAULT ?>" alt="">
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                    <div class="mj-cv-item">
                        <div class="mj-driver-cv-item-title">
                            <span><?= $lang['cv_military_status'] ?></span>
                        </div>

                        <div class="mj-driver-item-detail">
                            <span><?= $lang['cv_military_number'] ?>:</span>
                            <span><?= ($my_cv->cv_military_status == 'yes') ? $my_cv->cv_military_number : '' ?></span>
                        </div>
                        <div class="mj-driver-item-detail">
                            <span><?= $lang['driver_services_expire_date'] ?>:</span>
                            <span><?= ($my_cv->cv_military_status == 'yes') ? Utils::getTimeByLang($my_cv->cv_military_date) : '' ?></span>
                        </div>
                        <div class="mj-driver-item-detail-images">
                            <span><?= $lang['driver_services_images'] ?>:</span>
                            <div><?= $lang['driver_services_images_preview'] ?></div>
                        </div>
                        <div class="mj-driver-item-detail-images-item">
                            <?php foreach (json_decode($my_cv->cv_military_image) as $item) { ?>
                                <img class="me-2"
                                     src="<?= Utils::fileExist($my_cv->cv_military_image, POSTER_DEFAULT); ?>" alt="">
                            <?php }
                            if (count(json_decode($my_cv->cv_military_image)) == 0) {
                                ?>
                                <img class="me-2" src="<?= POSTER_DEFAULT ?>" alt="">
                                <?php
                            } ?>
                        </div>
                    </div>

                    <div class="mj-cv-item">
                        <div class="mj-driver-cv-item-title">
                            <span><?= $lang['cv_passport_status'] ?></span>
                        </div>

                        <div class="mj-driver-item-detail">
                            <span><?= $lang['cv_passport_number'] ?>:</span>
                            <span><?= ($my_cv->cv_passport_status == 'yes') ? $my_cv->cv_passport_number : '' ?></span>
                        </div>
                        <div class="mj-driver-item-detail">
                            <span><?= $lang['driver_services_expire_date'] ?>:</span>
                            <span><?= ($my_cv->cv_passport_status == 'yes') ? Utils::getTimeByLang($my_cv->cv_passport_date) : '' ?></span>
                        </div>
                        <div class="mj-driver-item-detail-images">
                            <span><?= $lang['driver_services_images'] ?>:</span>
                            <div><?= $lang['driver_services_images_preview'] ?></div>
                        </div>
                        <div class="mj-driver-item-detail-images-item">
                            <?php foreach (json_decode($my_cv->cv_passport_image) as $item) { ?>
                                <img class="me-2" src="<?= Utils::fileExist($item, POSTER_DEFAULT); ?>" alt="">
                            <?php }
                            if (count(json_decode($my_cv->cv_passport_image)) == 0) {
                                ?>
                                <img class="me-2" src="<?= POSTER_DEFAULT ?>" alt="">
                                <?php
                            } ?>
                        </div>
                    </div>


                    <div class="mj-cv-item">
                        <div class="mj-driver-cv-item-title">
                            <span><?= $lang['cv_visa_status'] ?></span>
                        </div>

                        <div class="mj-driver-item-detail">
                            <span><?= $lang['cv_visa_number'] ?>:</span>
                            <span><?= ($my_cv->cv_visa_status == 'yes') ? $my_cv->cv_visa_number : '' ?></span>
                        </div>
                        <div class="mj-driver-item-detail">
                            <span><?= $lang['driver_services_expire_date'] ?>:</span>
                            <span><?= ($my_cv->cv_visa_status == 'yes') ? Utils::getTimeByLang($my_cv->cv_visa_date) : '' ?></span>
                        </div>
                        <div class="mj-driver-item-detail-images">
                            <span><?= $lang['driver_services_images'] ?>:</span>
                            <div><?= $lang['driver_services_images_preview'] ?></div>
                        </div>
                        <div class="mj-driver-item-detail-images-item">
                            <?php foreach (json_decode($my_cv->cv_visa_image) as $item) { ?>
                                <img class="me-2" src="<?= Utils::fileExist($item, POSTER_DEFAULT); ?>" alt="">
                            <?php }
                            if (count(json_decode($my_cv->cv_visa_image)) == 0) {
                                ?>
                                <img class="me-2" src="<?= POSTER_DEFAULT ?>" alt="">
                                <?php
                            } ?>
                        </div>
                    </div>

                    <div class="mj-cv-item">
                        <div class="mj-driver-cv-item-title">
                            <span><?= $lang['cv_workbook_status'] ?></span>
                        </div>

                        <div class="mj-driver-item-detail">
                            <span><?= $lang['cv_workbook_number'] ?>:</span>
                            <span><?= ($my_cv->cv_workbook_status == 'yes') ? $my_cv->cv_workbook_number : '' ?></span>
                        </div>
                        <div class="mj-driver-item-detail">
                            <span><?= $lang['driver_services_expire_date'] ?>:</span>
                            <span><?= ($my_cv->cv_workbook_status == 'yes') ? Utils::getTimeByLang($my_cv->cv_workbook_date) : '' ?></span>
                        </div>
                        <div class="mj-driver-item-detail-images">
                            <span><?= $lang['driver_services_images'] ?>:</span>
                            <div><?= $lang['driver_services_images_preview'] ?></div>
                        </div>
                        <div class="mj-driver-item-detail-images-item">
                            <?php foreach (json_decode($my_cv->cv_workbook_image) as $item) { ?>
                                <img class="me-2" src="<?= Utils::fileExist($item, POSTER_DEFAULT); ?>" alt="">
                            <?php }
                            if (count(json_decode($my_cv->cv_workbook_image)) == 0) {
                                ?>
                                <img class="me-2" src="<?= POSTER_DEFAULT ?>" alt="">
                                <?php
                            } ?>
                        </div>
                    </div>

                    <div class="mj-cv-item">
                        <div class="mj-driver-cv-item-title">
                            <span><?= $lang['cv_driver_license_status'] ?></span>
                        </div>

                        <div class="mj-driver-item-detail">
                            <span><?= $lang['cv_driver_license_number'] ?>:</span>
                            <span><?= ($my_cv->cv_driver_license_status == 'yes') ? $my_cv->cv_driver_license_number : '' ?></span>
                        </div>
                        <div class="mj-driver-item-detail">
                            <span><?= $lang['driver_services_expire_date'] ?>:</span>
                            <span><?= ($my_cv->cv_driver_license_status == 'yes') ? Utils::getTimeByLang($my_cv->cv_driver_license_date) : '' ?></span>
                        </div>
                        <div class="mj-driver-item-detail-images">
                            <span><?= $lang['driver_services_images'] ?>:</span>
                            <div><?= $lang['driver_services_images_preview'] ?></div>
                        </div>
                        <div class="mj-driver-item-detail-images-item">
                            <?php foreach (json_decode($my_cv->cv_driver_license_image) as $item) { ?>
                                <img class="me-2" src="<?= Utils::fileExist($item, POSTER_DEFAULT); ?>" alt="">
                            <?php }
                            if (count(json_decode($my_cv->cv_driver_license_image)) == 0) {
                                ?>
                                <img class="me-2" src="<?= POSTER_DEFAULT ?>" alt="">
                                <?php
                            } ?>
                        </div>
                    </div>


                </div>
                <div class="mj-cv-driver-permission">
                    <div class="mj-cv-driver-permission-card  <?= ($my_cv->cv_role_status == 'yes') ? 'allow' : '' ?>">
                        <div class="fa-close"></div>
                        <span><?= ($my_cv->cv_role_status == 'yes') ? $lang['cv_role_status_allow'] : $lang['cv_role_status_dont_allow'] ?></span>
                    </div>
                </div>
                <div class="mj-cv-operations">
                    <button id="accept" type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#accept"><?= $lang['driver_services_accept'] ?>
                    </button>
                    <button id="reject" type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#reject"><?= $lang['driver_services_reject'] ?>
                    </button>
                </div>

            </div>
            <div id="lightbox">
                <img id="lightbox-img">
            </div>

            <input type="hidden" id="token" name="token"
                   value="<?= $_SESSION['dt-cargo'] = "dt-cargo-44"; ?>">
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