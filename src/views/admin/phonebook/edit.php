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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_edit == "yes") {
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
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');

        // Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('s2-js', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');


        enqueueScript('data-table', '/dist/js/admin/phonebook/phonebook-detail.js');
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
            $id = $_REQUEST['id'];
            $detail = PhoneBook::getphonebook($id);
            if ($detail->status == 200) {
                $detail = $detail->response;
            } else {
                $detail = [];
            }

            $isRegister = (User::checkUserRegister('+' . $detail->pb_phone) == 1) ? true : false;
            $isRegister2 = (User::checkUserRegister('+' . $detail->pb_home_number) == 1) ? true : false;


            $descs = PhoneBook::getPhoneBookDesc($id);
            $descs = $descs->status == 200 ? $descs->response : $descs = [];
            $last_call = '-';
            if (isset($descs[0])) {
                $last_call = Utils::getTimeByLangWithHour($descs[0]->desc_create_at);
            }
            ?>
            <script>
                let p_id = '<?=$id?>'
            </script>

            <!--start custom html-->

            <div class="row">
                <div class="col-lg-12">
                    <div class="card p-3">
                        <!--                        todo use on edit -->
                        <div class="mj-pbook-detail-first-row mb-2">
                            <div class=" mj-member-status-head">
                                <div class="mj-member-docs-status">
                                    <!--<div class="mj-member-card-docs-icon notcompleted">-->
                                    <!-- <div class="mj-member-card-docs-icon ">-->
                                    <!--<img src="/dist/images/admin/pbook-comdoc.svg" alt="nti">-->
                                    <!--   <img src="/dist/images/admin/pbook-notcomdoc.svg" alt="nti">
                                   </div>
                                   <span>اطلاعات ناقص</span>-->
                                </div>


                                <div class="mj-member-user-type">
                                    <div class="mj-member-driver-icon <?php
                                    if ($detail->pb_user_type == 'driver') {
                                        echo 'driver';
                                    } elseif ($detail->pb_user_type == 'businessman') {
                                        echo 'businessman';
                                    } elseif ($detail->pb_user_type == 'transportation_company') {
                                        echo  'transportation_company';
                                    } elseif ($detail->pb_user_type == 'dealer') {
                                        echo'dealer';
                                    } elseif ($detail->pb_user_type == 'shiping') {
                                        echo 'shiping';
                                    } elseif ($detail->pb_user_type == 'dischager') {
                                        echo 'dischager';
                                    } elseif ($detail->pb_user_type == 'keeper') {
                                        echo 'keeper';
                                    } elseif ($detail->pb_user_type == 'other') {
                                        echo 'other';
                                    } elseif ($detail->pb_user_type == 'guest') {
                                        echo 'guest';
                                    } else {
                                        echo  'guest';
                                    }
                                    ?> ">
                                        <img
                                            src="/dist/images/admin/pbook-<?php
                                            if ($detail->pb_user_type == 'driver') {
                                               echo 'driver';
                                            } elseif ($detail->pb_user_type == 'businessman') {
                                               echo 'businessman';
                                            } elseif ($detail->pb_user_type == 'transportation_company') {
                                              echo  'transportation_company';
                                            } elseif ($detail->pb_user_type == 'dealer') {
                                              echo'dealer';
                                            } elseif ($detail->pb_user_type == 'shiping') {
                                               echo 'shiping';
                                            } elseif ($detail->pb_user_type == 'dischager') {
                                               echo 'dischager';
                                            } elseif ($detail->pb_user_type == 'keeper') {
                                               echo 'keeper';
                                            } elseif ($detail->pb_user_type == 'other') {
                                               echo 'other';
                                            } elseif ($detail->pb_user_type == 'guest') {
                                               echo 'guest';
                                            } else {
                                              echo  'guest';
                                            }
                                            ?>.svg"
                                            alt="nti">
                                    </div>
                                    <span><?php
                                        if ($detail->pb_user_type == 'driver') {
                                            echo $lang['driver'];
                                        } elseif ($detail->pb_user_type == 'businessman') {
                                            echo $lang['businessman'];
                                        } elseif ($detail->pb_user_type == 'transportation_company') {
                                            echo $lang['transportation_company'];
                                        } elseif ($detail->pb_user_type == 'dealer') {
                                            echo $lang['dealer'];
                                        } elseif ($detail->pb_user_type == 'shiping') {
                                            echo $lang['shiping'];
                                        } elseif ($detail->pb_user_type == 'dischager') {
                                            echo $lang['dischager'];
                                        } elseif ($detail->pb_user_type == 'keeper') {
                                            echo $lang['keeper'];
                                        } elseif ($detail->pb_user_type == 'other') {
                                            echo $lang['other'];
                                        } elseif ($detail->pb_user_type == 'guest') {
                                            echo $lang['guest'];
                                        } else {
                                            echo $lang['guest'];
                                        }
                                        ?></span>
                                </div>
                            </div>

                            <div class="mj-member-last-activity">
                                <div class="mj-member-last-seen">
                                    <span><?= $lang['pb_last_call'] ?></span>
                                    <span>
                                            <span><?= $last_call ?></span>

                                        </span>
                                </div>
                            </div>
                        </div>


                        <div style="gap: 10px;row-gap: 10px" class="row mt-2 mb-4">
                            <div class=" mj-col-style-pbook col-lg-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="user_name"
                                           placeholder="name@example.com" value="<?= $detail->pb_username ?>">
                                    <label for="user_name"><?= $lang['pb_name'] ?></label>
                                </div>
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <div class="form-floating ">
                                    <input type="text" class="form-control" id="user_lname"
                                           placeholder="name@example.com" value="<?= $detail->pb_user_lname ?>">
                                    <label for="user_lname"><?= $lang['pb_lname'] ?></label>
                                </div>
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <?php if ($isRegister) {
                                    ?>
                                    <div class="mj-member-card-signed-icon ">
                                        <img src="/dist/images/admin/pbook-nti.svg" alt="nti">
                                    </div>
                                    <?php
                                } ?>
                                <div class="form-floating  mj-pbooks-phone-add">
                                    <input type="text" inputmode="decimal" class="form-control" id="user_phone"
                                           placeholder="name@example.com"
                                           value="<?php
                                           $mobile= $detail->pb_phone ;
                                           if (str_contains($mobile, '+')) {
                                               $mobile = str_replace('+', '', $mobile);
                                           }
                                          echo substr($mobile, 2, strlen($mobile)) ?>">
                                    <label for="user_phone"><?= $lang['pb_phone'] ?></label>
                                    <select id="country-code" name="country-code" data-width="100px" dir="ltr">
                                        <?php
                                        // $countries = json_decode(Utils::getFileValue('countries.json', null, false));
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
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <?php if ($isRegister2) {
                                    ?>
                                    <div class="mj-member-card-signed-icon ">
                                        <img src="/dist/images/admin/pbook-nti.svg" alt="nti">
                                    </div>
                                    <?php
                                } ?>
                                <div class="form-floating mj-pbooks-phone-add">
                                    <input type="text" inputmode="decimal" class="form-control" id="user_home_number"
                                           placeholder="name@example.com"
                                           value="<?php
                                           $mobile= $detail->pb_home_number ;
                                           if (str_contains($mobile, '+')) {
                                               $mobile = str_replace('+', '', $mobile);
                                           }
                                          echo substr($mobile, 2, strlen($mobile)) ?>">
                                    <label for="user_home_number"><?= $lang['pb_second_phone'] ?></label>
                                    <select id="country-code-2" name="country-code" data-width="100px" dir="ltr">
                                        <?php
                                        // $countries = json_decode(Utils::getFileValue('countries.json', null, false));
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
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <div class="form-floating mb-1">
                                    <input type="text" class="form-control" id="company_name"
                                           placeholder="name@example.com"
                                           value="<?= $detail->pb_company_name ?>">
                                    <label for="company_name"><?= $lang['pb_company_name'] ?></label>
                                </div>
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <span class="mj-inputs-no-head"><?= $lang['pb_cargo_type'] ?></span>
                                <div class="mj-detail-member-zone ">
                                    <select id="detail-member-zone" class="form-select"
                                            aria-label="Default select example">
                                        <option value=""></option>
                                        <option
                                            value="inout" <?= $detail->pb_cargo_type == 'inout' ? 'selected' : '' ?>><?= $lang['pb_cargointernal_external'] ?></option>
                                        <option
                                            value="out" <?= $detail->pb_cargo_type == 'out' ? 'selected' : '' ?>><?= $lang['pb_cargo_external'] ?></option>
                                        <option
                                            value="in " <?= $detail->pb_cargo_type == 'in' ? 'selected' : '' ?>><?= $lang['pb_cargo_internal'] ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <span class="mj-inputs-no-head"><?= $lang['pb_user_type'] ?></span>
                                <div class="mj-detail-member-zone ">
                                    <select id="detail-member-type" class="form-select"
                                            aria-label="Default select example">
                                        <option
                                            value="driver" <?= $detail->pb_user_type == 'driver' ? 'selected' : '' ?>><?= $lang['pb_driver'] ?></option>
                                        <option
                                            value="businessman" <?= $detail->pb_user_type == 'businessman' ? 'selected' : '' ?>><?= $lang['pb_businessman'] ?></option>
                                        <option
                                            value="transportation_company" <?= $detail->pb_user_type == 'transportation_company' ? 'selected' : '' ?>><?= $lang['pb_transportation_company'] ?></option>
                                        <option
                                            value="dealer" <?= $detail->pb_user_type == 'dealer' ? 'selected' : '' ?>><?= $lang['pb_dealer'] ?></option>
                                        <option
                                            value="shiping" <?= $detail->pb_user_type == 'shiping' ? 'selected' : '' ?>><?= $lang['pb_shiping'] ?></option>
                                        <option
                                            value="dischager" <?= $detail->pb_user_type == 'dischager' ? 'selected' : '' ?>><?= $lang['pb_dischager'] ?></option>
                                        <option
                                            value="keeper" <?= $detail->pb_user_type == 'keeper' ? 'selected' : '' ?>><?= $lang['pb_keeper'] ?></option>
                                        <option
                                            value="other" <?= $detail->pb_user_type == 'other' ? 'selected' : '' ?>><?= $lang['pb_other'] ?></option>
                                        <option
                                            value="guest" <?= $detail->pb_user_type == 'guest' ? 'selected' : '' ?>><?= $lang['pb_guest'] ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class=" mj-col-style-pbook col-lg-4">
                                <span class="mj-inputs-no-head"><?= $lang['pb_user_access'] ?></span>
                                <div class="mj-detail-member-status ">
                                    <select id="detail-member-status" class="form-select"
                                            aria-label="Default select example">
                                        <option value=""></option>
                                        <option
                                            value="access" <?= $detail->pb_access_type == 'access' ? 'selected' : '' ?>><?= $lang['pb_access'] ?></option>
                                        <option
                                            value="not_access" <?= $detail->pb_access_type == 'not_access' ? 'selected' : '' ?>><?= $lang['pb_not_access'] ?></option>
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
                                            <option
                                                value="<?= $item->TypeId ?>" <?= in_array($item->TypeId, explode(',', $detail->pb_car_type)) ? 'selected' : '' ?>><?= $item->TypeName ?></option>
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
                                        <option value=""></option>
                                        <?php
                                        $countries = Location::getCountriesList();
                                        foreach ($countries->response as $item) {
                                            ?>
                                            <option
                                                value="<?= $item->CountryId ?>" <?= in_array($item->CountryId, explode(',', $detail->pb_fav_country)) ? 'selected' : '' ?>><?= $item->CountryName ?></option>
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
                            <div class=" col-lg-4 ">

                                <button type="button"
                                        class="btn btn-primary mj-detail-submit-btn"><?= $lang['pb_submit'] ?></button>
                                <button type="button"
                                        class="btn btn-danger mj-detail-delete-btn"><?= $lang['delete'] ?></button>
                            </div>
                            <div class="mj-pb-desctable col-lg-12">
                                <div class="table-responsive">
                                    <table id="orders-table" data-page-length='10'
                                           class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?= $lang['pb_admin'] ?></th>
                                            <th>  <?= $lang['pb_time'] ?></th>
                                            <th>  <?= $lang['pb_title'] ?></th>
                                        </tr>
                                        </thead>

                                        <tbody>
                                        <?php
                                        $counter = count($descs);
                                        foreach ($descs as $item) {
                                            ?>
                                            <tr class=" justify-content-around">
                                                <td><?= $counter ?></td>
                                                <td>
                                                    <?= Security::decrypt(Admin::getAdminById($item->desc_admin_id)->response[0]->admin_name) ?>
                                                </td>
                                                <td>
                                                    <?= Utils::getTimeByLang($item->desc_create_at) ?>
                                                </td>
                                                <td>
                                                    <?= empty($item->desc_text) ? '----' : $item->desc_text ?>
                                                </td>
                                            </tr>
                                            <?php $counter--;
                                        } ?>


                                        </tbody>
                                    </table>
                                </div>

                            </div>

                        </div>

                    </div>
                </div>
            </div>
            <!-- end custom html-->
            <input type="hidden" id="token" name="token"
                   value="<?= Security::initCSRF2() ?>">
            <!-- end custom html-->

            <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmDeleteModalLabel"><?=$lang['phone_book_delete']?></h5>

                        </div>
                        <div class="modal-body">
                            <p><?=$lang['phone_book_delete_questions']?></p>
                        </div>
                        <div class="modal-footer">
                            <button id="close-delete-modal" type="button" class="btn btn-secondary"  ><?=$lang['cancel']?></button>
                            <button id="delete-phone-book" type="button" class="btn btn-danger"><?=$lang['delete']?></button>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" id="token" name="token"
                   value="<?= $_SESSION['dt-cv'] = "dt-cv-44"; ?>">
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