<?php
$pageSlug = "city_c";
// permission_can_edit

global $lang, $antiXSS, $Settings;

use MJ\Utils\Utils;
use MJ\Security\Security;

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

        $id = (int)$antiXSS->xss_clean($_REQUEST['id']);

        /**
         * Get Department By ID
         */
        $resultCityById = Location::getCityById($id);
        $dataCityById = [];
        if ($resultCityById->status == 200 && !empty($resultCityById->response)) {
            $dataCityById = $resultCityById->response[0];
        }
        if (empty($dataCityById)) {
            header('Location: /admin/city');
        }

        /**
         * Get All Languages
         */
        $resultLanguages = Utils::getFileValue("languages.json", "", false);
        $dataLanguages = [];
        if (!empty($resultLanguages)) {
            $dataLanguages = json_decode($resultLanguages);
        }


        /**
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }

        /**
         * Get All Countries
         */
        $resultAllCountries = Location::getAllCountriesSomeValues();
        $dataAllCountries = [];
        if ($resultAllCountries->status == 200 && !empty($resultAllCountries->response)) {
            $dataAllCountries = $resultAllCountries->response;
        }

        // Load Stylesheets & Icons
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('city-edit', '/dist/js/admin/location/city-edit.init.js');

        getHeader($lang["city_edit"], [
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
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["city_edit"]; ?></h5>
                            <div class="row">
                                <?php
                                if (!empty($dataLanguages)) {
                                    foreach ($dataLanguages as $dataLanguagesTEMP) {
                                        ?>
                                        <div class="col-lg-12">
                                            <div class="form-floating mb-3">
                                                <input type="text" class="form-control titleCategory"
                                                       data-id="<?= $dataLanguagesTEMP->id; ?>"
                                                       data-slug="<?= $dataLanguagesTEMP->slug; ?>"
                                                       placeholder="<?= $lang["title"]; ?>(<?= $lang[$dataLanguagesTEMP->name]; ?>)"
                                                    <?php
                                                    foreach (json_decode($dataCityById->city_name) as $temp) {
                                                        if ($temp->slug == $dataLanguagesTEMP->slug) {
                                                            echo 'value="' . $temp->value . '"';
                                                        }
                                                    }
                                                    ?>
                                                       value="">
                                                <label for="nameCAteGory"><?= $lang["title"];
                                                    if ($dataLanguagesTEMP->status == "inactive") {
                                                        echo '<span class="text-danger font-11"> (' . $lang[$dataLanguagesTEMP->name] . ')</span>';
                                                    } else {
                                                        echo '<span class="text-success font-11"> (' . $lang[$dataLanguagesTEMP->name] . ')</span>';
                                                    }; ?></label>
                                                <small class="form-text text-muted">
                                                    <?= $lang['length_text']; ?> : <span
                                                            class="text-danger"
                                                            data-id-length="<?= $dataLanguagesTEMP->id; ?>">0</span>
                                                </small>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase1" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase1"><i class="mdi mdi-minus"></i></a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_city_international_name"]; ?></h5>
                            <div class="row show" id="cardCollpase1">
                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="xInternationalName"
                                               value="<?= $dataCityById->city_international_name; ?>"
                                               placeholder="<?= $lang["title"]; ?>">
                                        <label for="xInternationalName"><?= $lang["title"]; ?></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase2" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase2"><i class="mdi mdi-minus"></i></a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_city_international_name"]; ?></h5>
                            <div class="row show" id="cardCollpase2">
                                <p>
                                    <a target="_self"
                                       href="https://www.openstreetmap.org/node/26038590#map=19/<?= $dataCityById->city_long . "/" . $dataCityById->city_lat; ?>">
                                        <i class="mdi mdi-map-marker-radius-outline  mdi-18px h6 text-primary "></i>
                                        <?= $lang['show']; ?>
                                    </a>
                                </p>
                                <div class="col-lg-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control"
                                               id="xlat"
                                               value="<?= $dataCityById->city_lat; ?>"
                                               placeholder="lat">
                                        <label for="xlat">lat</label>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control"
                                               id="xlong"
                                               value="<?= $dataCityById->city_long; ?>"
                                               placeholder="long">
                                        <label for="xlong">long</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <div class="text-center progress-demo">
                                <button id="btnActive" type="button"
                                        class="setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light"
                                        data-id="<?= $dataCityById->city_id; ?>"
                                        data-style="zoom-in">
                                    <?= $lang["submit_change"]; ?>
                                </button>
                                <a href="/admin/city"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light">
                                    <?= $lang["btn_back"]; ?>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["select_country"]; ?></h5>
                            <div class="col-lg-12">
                                <div class="mb-3">
                                    <select class="form-control" id="countriesC" data-toggle="select2"
                                            data-width="100%">
                                        <?php
                                        if (!empty($dataAllCountries)) {
                                            foreach ($dataAllCountries as $loop) {
                                                ?>
                                                <option value="<?= $loop->country_id; ?>"
                                                    <?= ($dataCityById->country_id == $loop->country_id) ? " selected " : ""; ?>>
                                                    <?=
                                                    $countryName = (!empty(array_column(json_decode($loop->country_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                        array_column(json_decode($loop->country_name, true), 'value', 'slug')[$_COOKIE['language']] : $lang['no_value'];
                                                    ?>
                                                </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["status_select"]; ?></h5>
                            <div class="row">
                                <ul class="list-group list-group-flush">

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <label class="form-check-label"
                                               for="a_status_ground"><?= $lang["a_status_ground"]; ?></label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox"
                                                <?= ($dataCityById->city_status_ground == "yes") ? " checked " : "" ?>
                                                   class="form-check-input"
                                                   id="a_status_ground">
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <label class="form-check-label"
                                               for="a_status_ship"><?= $lang["a_status_ship"]; ?></label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox"
                                                <?= ($dataCityById->city_status_ship == "yes") ? " checked " : "" ?>
                                                   class="form-check-input"
                                                   id="a_status_ship">
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <label class="form-check-label"
                                               for="a_status_air"><?= $lang["a_status_air"]; ?></label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox"
                                                <?= ($dataCityById->city_status_air == "yes") ? " checked " : "" ?>
                                                   class="form-check-input"
                                                   id="a_status_air">
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <label class="form-check-label"
                                               for="a_status_railroad"><?= $lang["a_status_railroad"]; ?></label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox"
                                                <?= ($dataCityById->city_status_railroad == "yes") ? " checked " : "" ?>
                                                   class="form-check-input"
                                                   id="a_status_railroad">
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <label class="form-check-label"
                                               for="a_status_inventory"><?= $lang["a_status_inventory"]; ?></label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox"
                                                <?= ($dataCityById->city_status_inventory == "yes") ? " checked " : "" ?>
                                                   class="form-check-input"
                                                   id="a_status_inventory">
                                        </div>
                                    </li>

                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <label class="form-check-label"
                                               for="a_status_poster"><?= $lang["a_status_poster"]; ?></label>
                                        <div class="form-check form-switch">
                                            <input type="checkbox"
                                                <?= ($dataCityById->city_status_poster == "yes") ? " checked " : "" ?>
                                                   class="form-check-input"
                                                   id="a_status_poster">
                                        </div>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["priority_show"]; ?></h5>
                            <div class="col-lg-12">
                                <div class="form-floating mb-3">
                                    <input type="number"
                                           id="priority"
                                           class="form-control"
                                           value="<?= $dataCityById->city_priority; ?>"
                                           placeholder="<?= $lang["priority_show"]; ?>">
                                    <label for="priority"><?= $lang["priority_show"]; ?></label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg collapsed" data-bs-toggle="collapse" href="#cardCollpase12"
                                   role="button"
                                   aria-expanded="true" aria-controls="cardCollpase12">
                                    <i class="mdi mdi-minus"></i>
                                </a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["all_info"]; ?></h5>
                            <div class="table-responsive collapse" style="max-height: 176px;overflow: auto;"
                                 id="cardCollpase12">
                                <table class="table mb-0 table-sm">
                                    <tbody>
                                    <tr>
                                        <td colspan="2"><?= $lang["country"]; ?> :</td>
                                        <td><?= $countryName; ?></td>

                                    </tr>

                                    <?php
                                    if (!empty($dataCityById->city_options)) {

                                        $temp = json_decode($dataCityById->city_options);
                                        $name = "";
                                        if (!empty($dataAllAdmins)) {
                                            foreach ($dataAllAdmins as $dataAllAdminsLOOP) {
                                                if ($dataAllAdminsLOOP->admin_id == $temp->admin) {
                                                    $name = $dataAllAdminsLOOP->admin_nickname;
                                                }
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $lang['d_create']; ?></td>
                                            <td><?= (!empty($name)) ? $name : $temp->admin; ?></td>
                                            <td>
                                                <bdi><?= Utils::getTimeCountry($Settings['date_format'], $temp->date_create); ?></bdi>
                                            </td>
                                        </tr>
                                        <?php
                                        if (!empty($temp->update)) {
                                            foreach ($temp->update as $loop) {
                                                ?>
                                                <tr>
                                                    <td><?= $lang['d_update']; ?></td>
                                                    <td>
                                                        <?php
                                                        if (!empty($dataAllAdmins)) {
                                                            foreach ($dataAllAdmins as $dataAllAdminsLOOP) {
                                                                if ($dataAllAdminsLOOP->admin_id == $loop->create) {
                                                                    $name = $dataAllAdminsLOOP->admin_nickname;
                                                                }
                                                            }
                                                        }
                                                        echo (!empty($name)) ? $name : $loop->create;
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <bdi><?= Utils::getTimeCountry($Settings['date_format'], $loop->date); ?></bdi>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>

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
                        'warning' => $lang['warning'],
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
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
                $lang['help_cate_1'],
                $lang['help_cate_2'],
                $lang['help_cate_3'],
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