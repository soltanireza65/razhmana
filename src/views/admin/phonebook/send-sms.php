<?php

use MJ\Security\Security;

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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_show == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1
        //custom css
        enqueueStylesheet('phonebook-css', '/dist/css/admin/phonebook.css');

        // Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
        enqueueStylesheet('s2-css', '/dist/libs/select2/css/select2.min.css');
        // Load Script In Footer

        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('s2-js', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');

        enqueueScript('data-table', '/dist/js/admin/phonebook/phonebook-sms.js');
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
            <!--            SEND SMS MODAL START-->
            <!-- Modal -->
            <div class="modal fade" id="sendsms-alert" tabindex="-1" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?= $lang['admin_sms_alert'] ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</button>
                        </div>
                    </div>
                </div>
            </div>
            <!--            SEND SMS MODAL END-->






            <!-- Button trigger modal -->
            <!--start custom html-->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card p-3">
                        <div class="mj-new-pbook-member-btn mb-3">
                            <a href="/admin/pbookadd"
                               class="mj-new-pbook-member"
                               target="_blank">
                                <img class="me-1" src="/dist/images/admin/pb-plus.svg" alt="">
                                <span><?= $lang['add_new_phonebook'] ?></span>
                            </a>
                        </div>
                        <div class="mj-pbook-list-head">

                            <div class="mj-new-pbook-member-search">
                                <input type="search" id="search-keys" placeholder="search">
                            </div>
                            <div class="mj-new-pbook-member-status">
                                <div id="member-status" class="form-floating  ">
                                    <select id="member-status-select" class="form-select"
                                            aria-label="Default select example">
                                        <option value="all" selected>
                                            <?= $lang['all']; ?>
                                        </option>
                                        <option value="access"><?= $lang['pb_access'] ?></option>
                                        <option value="not_access"><?= $lang['pb_not_access'] ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="mj-new-pbook-member-road">
                                <div id="member-road" class="form-floating  ">
                                    <select id="member-road-select" class="form-select"
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
                            <div class="mj-new-pbook-member-car">
                                <div id="member-car" class="form-floating  ">
                                    <select id="member-car-select" class="form-select"
                                            aria-label="Default select example">
                                        <option value="all">
                                            <?= $lang['all']; ?>
                                        </option>
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
                            <div class="mj-new-pbook-member-inout">
                                <div id="member-inout" class="form-floating  ">
                                    <select id="member-inout-select" class="form-select"
                                            aria-label="Default select example">
                                        <option value="all" selected>
                                            <?= $lang['all']; ?>
                                        </option>
                                        <option value="inout"><?= $lang['pb_cargointernal_external'] ?></option>
                                        <option value="out"><?= $lang['pb_cargo_external'] ?></option>
                                        <option value="in "><?= $lang['pb_cargo_internal'] ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="mj-new-pbook-member-type">
                                <div id="member-type" class="form-floating  ">
                                    <select id="member-type-select" class="form-select"
                                            aria-label="Default select example">
                                        <option value="all" selected>
                                            <?= $lang['all']; ?>
                                        </option>
                                        <option value="driver"><?= $lang['pb_driver'] ?></option>
                                        <option value="businessman"><?= $lang['pb_businessman'] ?></option>
                                        <option
                                            value="transportation_company"><?= $lang['pb_transportation_company'] ?></option>
                                        <option value="dealer"><?= $lang['pb_dealer'] ?></option>
                                        <option value="shiping"><?= $lang['pb_shiping'] ?></option>
                                        <option value="dischager"><?= $lang['pb_dischager'] ?></option>
                                        <option value="keeper"><?= $lang['pb_keeper'] ?></option>
                                        <option value="other"><?= $lang['pb_other'] ?></option>
                                        <option value="guest"><?= $lang['pb_guest'] ?></option>

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mj-apply-pbook-filter-btn">
                            <a href="javascript:void(0)"
                               class="mj-pbook-apply-filter">
                                <img class="me-1" src="/dist/images/admin/pb-apply.svg" alt="">
                                <span><?= $lang['apply_filter_phonebook'] ?></span>
                            </a>
                        </div>

                        <div class="mj-send-message-part">
                            <div class="mb-2">ارسال پیام</div>
                            <div class="mj-pbook-list-head2">

                                <!--                                <div class="mj-new-pbook-member-search2">-->
                                <!--                                    <input type="search" id="search-keys" placeholder="search">-->
                                <!--                                </div>-->
                                <!--                                <div class="mj-new-pbook-member-status2">-->
                                <!--                                    <div id="member-status" class="form-floating  ">-->
                                <!--                                        <select id="member-status-select2" class="form-select"-->
                                <!--                                                aria-label="Default select example">-->
                                <!--                                            <option value="all" selected>-->
                                <!--                                                --><?php //= $lang['all']; ?>
                                <!--                                            </option>-->
                                <!--                                            <option value="access">-->
                                <?php //= $lang['pb_access'] ?><!--</option>-->
                                <!--                                            <option value="not_access" >-->
                                <?php //= $lang['pb_not_access'] ?><!--</option>-->
                                <!--                                        </select>-->
                                <!--                                    </div>-->
                                <!--                                </div>-->
                                <!--                                <div class="mj-new-pbook-member-road2">-->
                                <!--                                    <div id="member-road" class="form-floating  ">-->
                                <!--                                        <select id="member-road-select2" class="form-select"-->
                                <!--                                                aria-label="Default select example">-->
                                <!--                                            <option value="all">-->
                                <!--                                                --><?php //= $lang['all']; ?>
                                <!--                                            </option>-->
                                <!--                                            --><?php
                                //                                            $countries = Location::getCountriesList();
                                //                                            foreach ($countries->response as $item) {
                                //                                                ?>
                                <!--                                                <option value="-->
                                <?php //= $item->CountryId ?><!--">--><?php //= $item->CountryName ?><!--</option>-->
                                <!--                                                --><?php
                                //                                            }
                                //                                            ?>
                                <!--                                        </select>-->
                                <!--                                    </div>-->
                                <!--                                </div>-->
                                <!--                                <div class="mj-new-pbook-member-car2">-->
                                <!--                                    <div id="member-car" class="form-floating  ">-->
                                <!--                                        <select id="member-car-select2" class="form-select"-->
                                <!--                                                aria-label="Default select example">-->
                                <!--                                            <option value="all">-->
                                <!--                                                --><?php //= $lang['all']; ?>
                                <!--                                            </option>-->
                                <!--                                            --><?php
                                //                                            $carTypes = Driver::getCarTypes();
                                //                                            foreach ($carTypes->response as $item) {
                                //                                                ?>
                                <!--                                                <option value="-->
                                <?php //= $item->TypeId ?><!--">--><?php //= $item->TypeName ?><!--</option>-->
                                <!--                                                --><?php
                                //                                            }
                                //                                            ?>
                                <!--                                        </select>-->
                                <!--                                    </div>-->
                                <!--                                </div>-->
                                <!--                                <div class="mj-new-pbook-member-inout2">-->
                                <!--                                    <div id="member-inout" class="form-floating  ">-->
                                <!--                                        <select id="member-inout-select2" class="form-select"-->
                                <!--                                                aria-label="Default select example">-->
                                <!--                                            <option value="all" selected>-->
                                <!--                                                --><?php //= $lang['all']; ?>
                                <!--                                            </option>-->
                                <!--                                            <option value="inout">-->
                                <?php //= $lang['pb_cargointernal_external'] ?><!--</option>-->
                                <!--                                            <option value="out">-->
                                <?php //= $lang['pb_cargo_external'] ?><!--</option>-->
                                <!--                                            <option value="in " >-->
                                <?php //= $lang['pb_cargo_internal'] ?><!--</option>-->
                                <!--                                        </select>-->
                                <!--                                    </div>-->
                                <!--                                </div>-->
                                <!--                                <div class="mj-new-pbook-member-type2">-->
                                <!--                                    <div id="member-type" class="form-floating  ">-->
                                <!--                                        <select id="member-type-select2" class="form-select"-->
                                <!--                                                aria-label="Default select example">-->
                                <!--                                            <option value="all" selected>-->
                                <!--                                                --><?php //= $lang['all']; ?>
                                <!--                                            </option>-->
                                <!--                                            <option value="driver">-->
                                <?php //= $lang['pb_driver'] ?><!--</option>-->
                                <!--                                            <option value="businessman">-->
                                <?php //= $lang['pb_businessman'] ?><!--</option>-->
                                <!--                                            <option value="transportation_company">-->
                                <?php //= $lang['pb_transportation_company'] ?><!--</option>-->
                                <!--                                            <option value="dealer">-->
                                <?php //= $lang['pb_dealer'] ?><!--</option>-->
                                <!--                                            <option value="shiping">-->
                                <?php //= $lang['pb_shiping'] ?><!--</option>-->
                                <!--                                            <option value="dischager">-->
                                <?php //= $lang['pb_dischager'] ?><!--</option>-->
                                <!--                                            <option value="keeper">-->
                                <?php //= $lang['pb_keeper'] ?><!--</option>-->
                                <!--                                            <option value="other">-->
                                <?php //= $lang['pb_other'] ?><!--</option>-->
                                <!--                                            <option value="guest" >-->
                                <?php //= $lang['pb_guest'] ?><!--</option>-->
                                <!---->
                                <!--                                        </select>-->
                                <!--                                    </div>-->
                                <!--                                </div>-->
                                <div class="mj-new-pbook-message2">
                                    <textarea name="pbook-message2" id="pbook-message2"
                                              placeholder="message"></textarea>
                                </div>

                            </div>
                            <div class="mj-pbook-message-drafts-btn">
                                <div class="mj-pbook-message-drafts">
                                    <div class="mj-pbook-message-draft-item" data-draft-text="متن پیشنویس اول">
                                        <span>اعلام بار زمینی</span>
                                    </div>
                                    <div class="mj-pbook-message-draft-item" data-draft-text="متن پیشنویس دوم">
                                        <span>اعلام بار هوایی</span>
                                    </div>
                                    <div class="mj-pbook-message-draft-item" data-draft-text="متن پیش نویس سوم">
                                        <span>اعلام بار ریلی</span>
                                    </div>
                                </div>
                                <div class="mj-send-message-btn"   >
                                    <div class="fa-check"></div>
                                </div>
                            </div>
                        </div>
                        <div class="mj-pbook-members-list">


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