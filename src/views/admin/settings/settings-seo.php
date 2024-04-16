<?php
$pageSlug = "settings_seo";

// permission_can_edit

use Carbon\Factory;
use MJ\Utils\Utils;
use MJ\Security\Security;

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


        /**
         * Get All Settings
         */
        $resultSettings = Utils::getFileValue("settings.txt");
        $dataSettings = [];
        if (!empty($resultSettings)) {
            $dataSettings = json_decode($resultSettings, true);
        }

        $seo_home = "";
        $seo_user_laws = "";
        $seo_404 = "";
        $seo_about_us = "";
        $seo_contact_us = "";
        $seo_developer = "";
        $seo_user_faq = "";
        $seo_blog = "";
        $a_seo_robots = file_get_contents(SITE_ROOT . '/robots.txt');
        $a_sitemap = file_get_contents(SITE_ROOT . '/sitemap.xml');
        $a_sitemap_blog = file_get_contents(SITE_ROOT . '/sitemap-blog.xml');
        $a_sitemap_academy = file_get_contents(SITE_ROOT . '/sitemap-academy.xml');
        $a_sitemap_cargo_out = file_get_contents(SITE_ROOT . '/sitemap-cargo-out.xml');
        $a_sitemap_cargo_in = file_get_contents(SITE_ROOT . '/sitemap-cargo-in.xml');
        $a_sitemap_poster = file_get_contents(SITE_ROOT . '/sitemap-poster.xml');

        if (!empty($dataSettings)) {
            foreach ($dataSettings as $index => $loop) {
                if ($index == "seo_home") {
                    $seo_home = $loop;
                }

                if ($index == "seo_user_laws") {
                    $seo_user_laws = $loop;
                }

                if ($index == "seo_404") {
                    $seo_404 = $loop;
                }

                if ($index == "seo_about_us") {
                    $seo_about_us = $loop;
                }

                if ($index == "seo_contact_us") {
                    $seo_contact_us = $loop;
                }

                if ($index == "seo_developer") {
                    $seo_developer = $loop;
                }

                if ($index == "seo_user_faq") {
                    $seo_user_faq = $loop;
                }

                if ($index == "seo_blog") {
                    $seo_blog = $loop;
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
        enqueueScript('settings-seo', '/dist/js/admin/settings/settings-seo.init.js');

        getHeader($lang['settings_seo'], [
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
                            <h5 class="text-uppercase mt-0 mb-3  bg-light p-2"><?= $lang['settings_seo']; ?></h5>
                            <div class="nav nav-pills flex-column navtab-bg nav-pills-tab text-center"
                                 id="tabs-tab" role="tablist" aria-orientation="vertical">

                                <a class="nav-link mt-2 py-2 active" id="custom-jibit-tab"
                                   data-bs-toggle="pill" href="#custom-jibit" role="tab"
                                   aria-controls="custom-jibit"
                                   aria-selected="true">
                                    <?= $lang['settings_seo']; ?>
                                </a>

                                <a class="nav-link mt-2 py-2" id="custom-v-sitemap-tab" data-bs-toggle="pill"
                                   href="#custom-v-sitemap" role="tab" aria-controls="custom-v-sitemap"
                                   aria-selected="false">
                                    <?= $lang['a_settings_sitemap']; ?>
                                </a>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="tab-content p-3">

                                <div class="tab-pane active" id="custom-jibit" role="tabpanel"
                                     aria-labelledby="custom-jibit-tab">

                                    <h5 class="text-uppercase mt-0 mb-3  bg-light p-2"><?= $lang['settings_seo']; ?></h5>

                                    <div class="row mt-3 mb-4">
                                        <label for="seo_home"
                                               class="col-sm-3 col-form-label"><?= $lang['a_seo_home']; ?></label>
                                        <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          placeholder=""
                                                          id="seo_home"
                                                          style="height: 150px;direction: ltr"><?= $seo_home; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-3 mb-4">
                                        <label for="seo_user_laws"
                                               class="col-sm-3 col-form-label"><?= $lang['a_seo_user_laws']; ?></label>
                                        <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          placeholder=""
                                                          id="seo_user_laws"
                                                          style="height: 150px;direction: ltr"><?= $seo_user_laws; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-3 mb-4">
                                        <label for="seo_404"
                                               class="col-sm-3 col-form-label"><?= $lang['a_seo_404']; ?></label>
                                        <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          placeholder=""
                                                          id="seo_404"
                                                          style="height: 150px;direction: ltr"><?= $seo_404; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-3 mb-4">
                                        <label for="seo_about_us"
                                               class="col-sm-3 col-form-label"><?= $lang['a_seo_about_us']; ?></label>
                                        <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          placeholder=""
                                                          id="seo_about_us"
                                                          style="height: 150px;direction: ltr"><?= $seo_about_us; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-3 mb-4">
                                        <label for="seo_contact_us"
                                               class="col-sm-3 col-form-label"><?= $lang['a_seo_contact_us']; ?></label>
                                        <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          placeholder=""
                                                          id="seo_contact_us"
                                                          style="height: 150px;direction: ltr"><?= $seo_contact_us; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-3 mb-4">
                                        <label for="seo_developer"
                                               class="col-sm-3 col-form-label"><?= $lang['a_seo_developer']; ?></label>
                                        <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          placeholder=""
                                                          id="seo_developer"
                                                          style="height: 150px;direction: ltr"><?= $seo_developer; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-3 mb-4">
                                        <label for="seo_user_faq"
                                               class="col-sm-3 col-form-label"><?= $lang['a_seo_user_faq']; ?></label>
                                        <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          placeholder=""
                                                          id="seo_user_faq"
                                                          style="height: 150px;direction: ltr"><?= $seo_user_faq; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-3 mb-4">
                                        <label for="seo_blog"
                                               class="col-sm-3 col-form-label"><?= $lang['a_seo_blog']; ?></label>
                                        <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          placeholder=""
                                                          id="seo_blog"
                                                          style="height: 150px;direction: ltr"><?= $seo_blog; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="row mt-3 mb-4">
                                        <label for="seo_robots"
                                               class="col-sm-3 col-form-label"><?= $lang['a_seo_robots']; ?></label>
                                        <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          placeholder=""
                                                          id="seo_robots"
                                                          style="height: 150px;direction: ltr"><?= $a_seo_robots; ?></textarea>
                                        </div>
                                    </div>

                                    <div class="text-center my-3">
                                        <button type="submit" class="btn btn-primary btn-block"
                                                data-style="zoom-in"
                                                id="btnSubmit"><?= $lang['submit_change']; ?></button>
                                    </div>


                                </div>

                                <!-- start settings all-->
                                <div class="tab-pane fade" id="custom-v-sitemap" role="tabpanel"
                                     aria-labelledby="custom-v-sitemap-tab">
                                    <div>
                                        <h5 class="text-uppercase mt-0 bg-light p-2"><?= $lang['a_settings_sitemap']; ?></h5>


                                        <div class="row mt-3 mb-4">
                                            <label for="sitemap_all"
                                                   class="col-sm-3 col-form-label">sitemap.xml</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          rows="15"
                                                          placeholder=""
                                                          id="sitemap_all"
                                                          style="direction: ltr"><?= $a_sitemap; ?></textarea>
                                            </div>
                                        </div>


                                        <div class="row mt-3 mb-4">
                                            <label for="sitemap_blog"
                                                   class="col-sm-3 col-form-label">sitemap-blog.xml</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          rows="15"
                                                          placeholder=""
                                                          id="sitemap_blog"
                                                          style="direction: ltr"><?= $a_sitemap_blog; ?></textarea>
                                            </div>
                                        </div>


                                        <div class="row mt-3 mb-4">
                                            <label for="sitemap_academy"
                                                   class="col-sm-3 col-form-label">sitemap-academy.xml</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          rows="15"
                                                          placeholder=""
                                                          id="sitemap_academy"
                                                          style="direction: ltr"><?= $a_sitemap_academy; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="row mt-3 mb-4">
                                            <label for="sitemap_cargo_out"
                                                   class="col-sm-3 col-form-label">sitemap-cargo-out.xml</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          rows="15"
                                                          placeholder=""
                                                          id="sitemap_cargo_out"
                                                          style="direction: ltr"><?= $a_sitemap_cargo_out; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="row mt-3 mb-4">
                                            <label for="sitemap_cargo_in"
                                                   class="col-sm-3 col-form-label">sitemap-cargo-in.xml</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          rows="15"
                                                          placeholder=""
                                                          id="sitemap_cargo_in"
                                                          style="direction: ltr"><?= $a_sitemap_cargo_in; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="row mt-3 mb-4">
                                            <label for="sitemap_poster"
                                                   class="col-sm-3 col-form-label">sitemap-poster.xml</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control"
                                                          rows="15"
                                                          placeholder=""
                                                          id="sitemap_poster"
                                                          style="direction: ltr"><?= $a_sitemap_poster; ?></textarea>
                                            </div>
                                        </div>

                                        <div class="text-center my-3">
                                            <button type="submit" class="btn btn-primary btn-block"
                                                    data-style="zoom-in"
                                                    id="submit_sitemap"><?= $lang['submit_change']; ?></button>
                                        </div>

                                        <!-- end row-->

                                    </div>
                                </div>
                                <!-- end settings all-->


                            </div>

                        </div>
                    </div>
                </div>
            </div> <!-- end row-->
            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF2() ?>">
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
        getFooter(
            [
                $lang['help_setting_payment_alert_merchantid']
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