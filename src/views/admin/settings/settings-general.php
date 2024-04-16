<?php
$pageSlug = "settings_general";
// permission_can_edit

global $lang;

use MJ\Utils\Utils;
use  MJ\Security\Security;

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
//                if ($item000->slug_name == $pageSlug && ($item000->permission_can_edit == "yes" || $item000->permission_can_show == "yes" || $item000->permission_can_delete == "yes" || $item000->permission_can_insert == "yes")) {
                if ($item000->slug_name == $pageSlug && ($item000->permission_can_edit == "yes")) {
                    $flagSlug = true;
                }
            }
        }
        // end roles 1


        /**
         * Get All Settings
         */
        $resultSettings = Utils::getFileValue("settings.txt");
        $dataSettings = [];
        if (!empty($resultSettings)) {
            $dataSettings = json_decode($resultSettings, true);
        }

        $whatsapp = '';
        $support_call = '';
        $support_call_2 = '';
        $cargo_expire = '';
        $cargo_distance = 0;
        $r_card_account = '';
        $r_card_iban = '';
        $r_card_number = '';
        $r_card_number_name = '';

        if (!empty($dataSettings)) {
            foreach ($dataSettings as $index => $loop) {


                if ($index == "whatsapp") {
                    $whatsapp = $loop;
                }

                if ($index == "support_call") {
                    $support_call = $loop;
                }

                if ($index == "support_call_2") {
                    $support_call_2 = $loop;
                }

                if ($index == "cargo_expire") {
                    $cargo_expire = $loop;
                }

                if ($index == "cargo_distance") {
                    $cargo_distance = $loop;
                }

                if ($index == "r_card_account") {
                    $r_card_account = $loop;
                }

                if ($index == "r_card_iban") {
                    $r_card_iban = $loop;
                }

                if ($index == "r_card_number") {
                    $r_card_number = $loop;
                }

                if ($index == "r_card_number_name") {
                    $r_card_number_name = $loop;
                }
            }
        }


        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        enqueueStylesheet('dropzone', '/dist/libs/dropzone/min/dropzone.min.css');

        // Load Script In Footer
        enqueueScript('dropzone', '/dist/libs/dropzone/min/dropzone.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('settings-general', '/dist/js/admin/settings/settings-general.init.js');

        getHeader($lang['main_settings'], [
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
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title"><?= $lang['main_settings']; ?></h4>
                            <p class="sub-header"><?= $lang['main_desc_settings']; ?></p>
                            <div class="nav nav-pills flex-column navtab-bg nav-pills-tab text-center" id="v-pills-tab"
                                 role="tablist" aria-orientation="vertical">

                                <a class="nav-link  active show  mt-2 py-2" id="custom-v-logos-admin-tab"
                                   data-bs-toggle="pill"
                                   href="#custom-v-logos-admin" role="tab" aria-controls="custom-v-logos-admin"
                                   aria-selected="true">
                                    <?= $lang['settings_logos_admin']; ?>
                                </a>
                                <a class="nav-link mt-2 py-2" id="custom-v-logos-tab" data-bs-toggle="pill"
                                   href="#custom-v-logos" role="tab" aria-controls="custom-v-logos"
                                   aria-selected="false">
                                    <?= $lang['settings_logos']; ?>
                                </a>
                                <a class="nav-link mt-2 py-2" id="custom-v-logos_manifest-tab" data-bs-toggle="pill"
                                   href="#custom-v-logos_manifest" role="tab" aria-controls="custom-v-logos_manifest"
                                   aria-selected="false">
                                    <?= $lang['settings_logos_manifest']; ?>
                                </a>
                                <a class="nav-link mt-2 py-2" id="custom-v-social-network-tab" data-bs-toggle="pill"
                                   href="#custom-v-social-network" role="tab" aria-controls="custom-v-social-network"
                                   aria-selected="false">
                                    <?= $lang['settings_social_network']; ?>
                                </a>
                                <a class="nav-link mt-2 py-2" id="custom-v-overall-tab" data-bs-toggle="pill"
                                   href="#custom-v-overall" role="tab" aria-controls="custom-v-overall"
                                   aria-selected="false">
                                    <?= $lang['a_settings_overall']; ?>
                                </a>
                            </div>
                        </div>
                    </div> <!-- end col-->
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content p-3">

                                <!-- start settings theme Admin site-->
                                <div class="tab-pane fade  active show" id="custom-v-logos-admin" role="tabpanel"
                                     aria-labelledby="custom-v-logos-admin-tab">
                                    <div>
                                        <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang['settings_logos_admin_panels']; ?></h5>

                                        <div class="row">

                                            <div class="col-lg-12">
                                                <div class="row mb-4">
                                                    <label for="horizontal-password-input"
                                                           class="col-sm-3 col-form-label mb-3">
                                                        <?= $lang['favicon_site']; ?> (16px * 16px)
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <form action="/" method="post" class="dropzone" id="faviconSite"
                                                              data-plugin="dropzone">
                                                            <div class="fallback">
                                                                <input name="file" type="file">
                                                            </div>
                                                            <div class="dz-message needsclick">
                                                                <img class="img-fluid rounded"
                                                                     src="/uploads/site/favicon.webp">
                                                            </div>
                                                        </form>
                                                        <div class="mt-1">
                                                            <small><?= $lang['format_support']; ?> :
                                                                <bdi>.image/*</bdi>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="row mb-4">
                                                    <label for="horizontal-password-input"
                                                           class="col-sm-3 col-form-label mb-3">
                                                        <?= $lang['logo_ms']; ?><br>(22px * 22px)
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <form action="/" method="post" class="dropzone" id="logoSm"
                                                              data-plugin="dropzone">
                                                            <div class="fallback">
                                                                <input name="file" type="file" accept="image/*">
                                                            </div>
                                                            <div class="dz-message needsclick">
                                                                <img class="img-fluid rounded"
                                                                     src="/uploads/site/logo-sm.webp">
                                                            </div>
                                                        </form>
                                                        <div class="mt-1">
                                                            <small><?= $lang['format_support']; ?> :
                                                                <bdi>.image/*</bdi>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="row mb-4">
                                                    <label for="horizontal-password-input"
                                                           class="col-sm-3 col-form-label mb-3">
                                                        <?= $lang['logo_light']; ?><br>(97px * 20px)
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <form action="/" method="post" class="dropzone" id="logoLight"
                                                              data-plugin="dropzone">
                                                            <div class="fallback">
                                                                <input name="file" type="file" accept="image/*">
                                                            </div>
                                                            <div class="dz-message needsclick">
                                                                <img class="img-fluid rounded"
                                                                     src="/uploads/site/logo-light.webp">
                                                            </div>
                                                        </form>
                                                        <div class="mt-1">
                                                            <small><?= $lang['format_support']; ?> :
                                                                <bdi>.image/*</bdi>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="row mb-4">
                                                    <label for="horizontal-password-input"
                                                           class="col-sm-3 col-form-label mb-3">
                                                        <?= $lang['logo_dark']; ?><br>(97px * 20px)
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <form action="/" method="post" class="dropzone" id="logoDark"
                                                              data-plugin="dropzone">
                                                            <div class="fallback">
                                                                <input name="file" type="file" accept="image/*">
                                                            </div>
                                                            <div class="dz-message needsclick">
                                                                <img class="img-fluid rounded"
                                                                     src="/uploads/site/logo-dark.webp">
                                                            </div>
                                                        </form>
                                                        <div class="mt-1">
                                                            <small><?= $lang['format_support']; ?> :
                                                                <bdi>.image/*</bdi>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="text-center my-3">
                                                <button type="submit" class="btn btn-primary btn-block"
                                                        data-style="zoom-in"
                                                        id="submit_theme"><?= $lang['submit_change']; ?></button>
                                            </div>

                                        </div>
                                        <!-- end row-->

                                    </div>
                                </div>
                                <!-- end settings theme Admin site-->

                                <!-- start settings theme site-->
                                <div class="tab-pane fade" id="custom-v-logos" role="tabpanel"
                                     aria-labelledby="custom-v-logos-tab">
                                    <div>
                                        <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang['settings_logos_panels']; ?></h5>

                                        <div class="row">

                                            <div class="col-lg-12">
                                                <div class="row mb-4">
                                                    <label for="horizontal-password-input"
                                                           class="col-sm-3 col-form-label mb-3">
                                                        <?= $lang['logo_light']; ?>
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <form action="/" method="post" class="dropzone"
                                                              id="logoLightUser"
                                                              data-plugin="dropzone">
                                                            <div class="fallback">
                                                                <input name="file" type="file" accept="image/*">
                                                            </div>
                                                            <div class="dz-message needsclick">
                                                                <img class="img-fluid rounded"
                                                                     src="/uploads/site/user-logo-light.svg">
                                                            </div>
                                                        </form>
                                                        <div class="mt-1">
                                                            <small><?= $lang['format_support']; ?> :
                                                                <bdi>svg</bdi>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="row mb-4">
                                                    <label for="horizontal-password-input"
                                                           class="col-sm-3 col-form-label mb-3">
                                                        <?= $lang['logo_dark']; ?>
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <form action="/" method="post" class="dropzone"
                                                              id="logoDarkUser"
                                                              data-plugin="dropzone">
                                                            <div class="fallback">
                                                                <input name="file" type="file" accept="image/*">
                                                            </div>
                                                            <div class="dz-message needsclick">
                                                                <img class="img-fluid rounded"
                                                                     src="/uploads/site/user-logo-dark.svg">
                                                            </div>
                                                        </form>
                                                        <div class="mt-1">
                                                            <small><?= $lang['format_support']; ?> :
                                                                <bdi>svg</bdi>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="text-center my-3">
                                                <button type="submit" class="btn btn-primary btn-block"
                                                        data-style="zoom-in"
                                                        id="submit_theme_site"><?= $lang['submit_change']; ?></button>
                                            </div>

                                        </div>
                                        <!-- end row-->

                                    </div>
                                </div>
                                <!-- end settings theme site-->

                                <!-- start settings theme site-->
                                <div class="tab-pane fade" id="custom-v-logos_manifest" role="tabpanel"
                                     aria-labelledby="custom-v-logos_manifest-tab">
                                    <div>
                                        <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang['settings_logos_manifest']; ?></h5>

                                        <div class="row">

                                            <div class="col-lg-12">
                                                <div class="row mb-4">
                                                    <label for="horizontal-password-input"
                                                           class="col-sm-3 col-form-label mb-3">
                                                        <?= $lang['logo_size']; ?> 144px * 144px
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <form action="/" method="post" class="dropzone"
                                                              id="manifest144"
                                                              data-plugin="dropzone">
                                                            <div class="fallback">
                                                                <input name="file" type="file" accept="image/png">
                                                            </div>
                                                            <div class="dz-message needsclick">
                                                                <img class="img-fluid rounded"
                                                                     src="/uploads/site/144.png">
                                                            </div>
                                                        </form>
                                                        <div class="mt-1">
                                                            <small><?= $lang['format_support']; ?> :
                                                                <bdi>png</bdi>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-12">
                                                <div class="row mb-4">
                                                    <label for="horizontal-password-input"
                                                           class="col-sm-3 col-form-label mb-3">
                                                        <?= $lang['logo_size']; ?> 180px * 180px
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <form action="/" method="post" class="dropzone"
                                                              id="manifest180"
                                                              data-plugin="dropzone">
                                                            <div class="fallback">
                                                                <input name="file" type="file" accept="image/*">
                                                            </div>
                                                            <div class="dz-message needsclick">
                                                                <img class="img-fluid rounded"
                                                                     src="/uploads/site/180.png">
                                                            </div>
                                                        </form>
                                                        <div class="mt-1">
                                                            <small><?= $lang['format_support']; ?> :
                                                                <bdi>png</bdi>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-lg-12">
                                                <div class="row mb-4">
                                                    <label for="horizontal-password-input"
                                                           class="col-sm-3 col-form-label mb-3">
                                                        <?= $lang['logo_size']; ?> 192px * 192px
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <form action="/" method="post" class="dropzone"
                                                              id="manifest192"
                                                              data-plugin="dropzone">
                                                            <div class="fallback">
                                                                <input name="file" type="file" accept="image/*">
                                                            </div>
                                                            <div class="dz-message needsclick">
                                                                <img class="img-fluid rounded"
                                                                     src="/uploads/site/192.png">
                                                            </div>
                                                        </form>
                                                        <div class="mt-1">
                                                            <small><?= $lang['format_support']; ?> :
                                                                <bdi>png</bdi>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-lg-12">
                                                <div class="row mb-4">
                                                    <label for="horizontal-password-input"
                                                           class="col-sm-3 col-form-label mb-3">
                                                        <?= $lang['logo_size']; ?> 384px * 384px
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <form action="/" method="post" class="dropzone"
                                                              id="manifest384"
                                                              data-plugin="dropzone">
                                                            <div class="fallback">
                                                                <input name="file" type="file" accept="image/*">
                                                            </div>
                                                            <div class="dz-message needsclick">
                                                                <img class="img-fluid rounded"
                                                                     src="/uploads/site/384.png">
                                                            </div>
                                                        </form>
                                                        <div class="mt-1">
                                                            <small><?= $lang['format_support']; ?> :
                                                                <bdi>png</bdi>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="col-lg-12">
                                                <div class="row mb-4">
                                                    <label for="horizontal-password-input"
                                                           class="col-sm-3 col-form-label mb-3">
                                                        <?= $lang['logo_size']; ?> 512px * 512px
                                                    </label>
                                                    <div class="col-sm-9">
                                                        <form action="/" method="post" class="dropzone"
                                                              id="manifest512"
                                                              data-plugin="dropzone">
                                                            <div class="fallback">
                                                                <input name="file" type="file" accept="image/*">
                                                            </div>
                                                            <div class="dz-message needsclick">
                                                                <img class="img-fluid rounded"
                                                                     src="/uploads/site/512.png">
                                                            </div>
                                                        </form>
                                                        <div class="mt-1">
                                                            <small><?= $lang['format_support']; ?> :
                                                                <bdi>png</bdi>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="text-center my-3">
                                                <button type="submit" class="btn btn-primary btn-block"
                                                        data-style="zoom-in"
                                                        id="submit_manifest"><?= $lang['submit_change']; ?></button>
                                            </div>

                                        </div>
                                        <!-- end row-->

                                    </div>
                                </div>
                                <!-- end settings theme site-->

                                <!-- start settings social network-->
                                <div class="tab-pane fade" id="custom-v-social-network" role="tabpanel"
                                     aria-labelledby="custom-v-social-network-tab">
                                    <div>
                                        <h5 class="text-uppercase mt-0 bg-light p-2"><?= $lang['settings_social_network']; ?></h5>

                                        <div class="row">


                                            <div class="row mt-3 mb-4">
                                                <label for="whatsapp"
                                                       class="col-sm-3 col-form-label"><?= $lang['support_whatsapp']; ?></label>
                                                <div class="col-sm-9">
                                                    <div class="input-group">
                                                        <span class="input-group-text" id="basic-whatsapp">
                                                            <i class="mdi mdi-whatsapp font-24 text-success"></i>
                                                        </span>
                                                        <input type="text" class="form-control" id="whatsapp"
                                                               placeholder="+989143302964"
                                                               value="<?= $whatsapp; ?>"
                                                               aria-label="+989143302964"
                                                               aria-describedby="basic-whatsapp">
                                                    </div>
                                                    <small>
                                                        <?= $lang['support_whatsapp_desc']; ?>
                                                    </small>
                                                </div>
                                            </div>


                                            <div class="row mt-3 mb-4">
                                                <label for="support_call"
                                                       class="col-sm-3 col-form-label"><?= $lang['support_call']; ?></label>
                                                <div class="col-sm-9">
                                                    <div class="input-group">
                                                        <span class="input-group-text" id="basic-support_call">
                                                            <i class="mdi mdi-cellphone font-24 text-primary"></i>
                                                        </span>
                                                        <input type="text" class="form-control" id="support_call"
                                                               placeholder="+989143302964"
                                                               value="<?= $support_call; ?>"
                                                               aria-label="+989143302964"
                                                               aria-describedby="basic-support_call">
                                                    </div>
                                                    <small>
                                                        <?= $lang['support_call_desc']; ?>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="row mt-3 mb-4">
                                                <label for="support_call_2"
                                                       class="col-sm-3 col-form-label"><?= $lang['support_call']; ?> 2</label>
                                                <div class="col-sm-9">
                                                    <div class="input-group">
                                                        <span class="input-group-text" id="basic-support_call_2">
                                                            <i class="mdi mdi-cellphone font-24 text-primary"></i>
                                                        </span>
                                                        <input type="text" class="form-control" id="support_call_2"
                                                               placeholder="+989143302964"
                                                               value="<?= $support_call_2; ?>"
                                                               aria-label="+989143302964"
                                                               aria-describedby="basic-support_call_2">
                                                    </div>
                                                    <small>
                                                        <?= $lang['support_call_desc']; ?>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="text-center my-3">
                                                <button type="submit" class="btn btn-primary btn-block"
                                                        data-style="zoom-in"
                                                        id="submit_social"><?= $lang['submit_change']; ?></button>
                                            </div>

                                        </div>
                                        <!-- end row-->

                                    </div>
                                </div>
                                <!-- end settings social network-->

                                <!-- start settings social network-->
                                <div class="tab-pane fade" id="custom-v-overall" role="tabpanel"
                                     aria-labelledby="custom-v-overall-tab">
                                    <div>
                                        <h5 class="text-uppercase mt-0 bg-light p-2"><?= $lang['a_settings_overall']; ?></h5>

                                        <div class="row">

                                            <div class="row mt-3 mb-4">
                                                <label for="cargo_expire"
                                                       class="col-sm-3 col-form-label"><?= $lang['a_settings_text_cargo_expire']; ?></label>
                                                <div class="col-sm-9">
                                                    <input type="number"
                                                           class="form-control"
                                                           id="cargo_expire"
                                                           placeholder="1"
                                                           value="<?= $cargo_expire; ?>">
                                                    <small>
                                                        <?= $lang['a_alert_settings_cargo_expire']; ?>
                                                    </small>
                                                </div>
                                            </div>

                                            <div class="row mt-3 mb-4">
                                                <label for="cargo_distance"
                                                       class="col-sm-3 col-form-label"><?= $lang['a_distance_setting']; ?></label>
                                                <div class="col-sm-9">
                                                    <input type="number"
                                                           class="form-control"
                                                           id="cargo_distance"
                                                           placeholder="1"
                                                           value="<?= $cargo_distance; ?>">
                                                </div>
                                            </div>

                                            <hr>

                                            <div class="row mt-3 mb-4">
                                                <label for="r_card_account"
                                                       class="col-sm-3 col-form-label"><?= $lang['card_account']; ?></label>
                                                <div class="col-sm-9">
                                                    <input type="text"
                                                           class="form-control"
                                                           id="r_card_account"
                                                           placeholder="1322342342342"
                                                           value="<?= $r_card_account; ?>">
                                                </div>
                                            </div>

                                            <div class="row mt-3 mb-4">
                                                <label for="r_card_iban"
                                                       class="col-sm-3 col-form-label"><?= $lang['card_iban']; ?></label>
                                                <div class="col-sm-9">
                                                    <input type="text"
                                                           class="form-control"
                                                           id="r_card_iban"
                                                           placeholder="IR - 1322342342342"
                                                           value="<?= $r_card_iban; ?>">
                                                </div>
                                            </div>

                                            <div class="row mt-3 mb-4">
                                                <label for="r_card_number"
                                                       class="col-sm-3 col-form-label"><?= $lang['card_number']; ?></label>
                                                <div class="col-sm-9">
                                                    <input type="text"
                                                           class="form-control"
                                                           id="r_card_number"
                                                           placeholder="1322342342342"
                                                           value="<?= $r_card_number; ?>">
                                                </div>
                                            </div>

                                            <div class="row mt-3 mb-4">
                                                <label for="r_card_number_name"
                                                       class="col-sm-3 col-form-label"><?= $lang['u_card_account']; ?></label>
                                                <div class="col-sm-9">
                                                    <input type="text"
                                                           class="form-control"
                                                           id="r_card_number_name"
                                                           placeholder=""
                                                           value="<?= $r_card_number_name; ?>">
                                                </div>
                                            </div>

                                            <div class="text-center my-3">
                                                <button type="submit" class="btn btn-primary btn-block"
                                                        data-style="zoom-in"
                                                        id="submit_overall"><?= $lang['submit_change']; ?></button>
                                            </div>

                                        </div>
                                        <!-- end row-->

                                    </div>
                                </div>
                                <!-- end settings social network-->

                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('admin-settings-general') ?>">
            <input type="hidden" id="tokenM" name="tokenM" value="<?= Security::initCSRF2() ?>">
            <script>
                var var_lang = '<?php
                    $var_lang = [
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'successful' => $lang['successful'],
                        'warning' => $lang['warning'],
                        'error_mag' => $lang['error_mag'],
                        'dictMaxFilesExceeded' => $lang['dictMaxFilesExceeded'],
                        'delete' => $lang['delete'],
                        'cancel_upload' => $lang['cancel_upload'],
                        'empty_input' => $lang['empty_input'],
                        'info' => $lang['info'],
                        'error' => $lang['error'],
                        'token_error' => $lang['token_error'],
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
