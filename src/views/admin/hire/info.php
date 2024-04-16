<?php
$pageSlug = "a_employ";
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
        $result = Hire::getEmployInfoById($id);
        $data = [];
        if ($result->status == 200 && !empty($result->response)) {
            $data = $result->response;
        }
        if (empty($data)) {
            header('Location: /admin/hire');
        }

        $title = Hire::getEmployTitle($data->employ_title)->response;


        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }


        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('hire-info', '/dist/js/admin/hire/hire-info.js');

        getHeader($lang["a_employ"], [
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
            <script>
                let hire_id = '<?=$id?>'
            </script>
            <div class="row">
                <div class="col-lg-8">

                    <div class="card" id="printDIV">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["change_loction_to_new_status"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12 my-2">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="selectCountry"
                                                   class="form-label"><?= $lang['a_select_country']; ?></label>
                                            <select class="form-select selectLocation"
                                                    name="selectCountry"
                                                    id="selectCountry"
                                                    data-toggle="select2"
                                                    data-width="100%"
                                                    data-tj-type="">
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="selectCity"
                                                   class="form-label"><?= $lang['select_city']; ?></label>
                                            <select class="form-select selectLocation"
                                                    name="selectCity"
                                                    id="selectCity"
                                                    data-toggle="select2"
                                                    data-width="100%"
                                                    data-mj-type="">
                                            </select>
                                        </div>
                                    </div>
                                    <button id="change-location" class="btn btn-primary"> ویرایش شهر و کشور</button>
                                    <?=  is_numeric($data->employ_city) ? $lang['change_loction_to_new_status_updated']: $lang['change_loction_to_new_status_not_updated'] ?>
                                </div>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_employ"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>

                                            <tr>
                                                <td><?= $lang["u_employ_category_request"]; ?> :</td>
                                                <td><?php
                                                    foreach ($title as $loop) {
                                                        echo (isset($loop->category_name) && !empty(array_column(json_decode($loop->category_name, true), 'value', 'slug')[$language])) ?
                                                            array_column(json_decode($loop->category_name, true), 'value', 'slug')[$language] . " <br> " : $loop->category_id . " <br> ";
                                                    }
                                                    ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["name_and_family"]; ?> :</td>
                                                <td><?= $data->employ_first_name . " " . $data->employ_last_name; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["auth_father_name"]; ?> :</td>
                                                <td><?= $data->employ_father_name; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["auth_birthday_city"]; ?> :</td>
                                                <td><?= $data->employ_birthday_location; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["auth_birthday_date"]; ?> :</td>
                                                <td><?= $data->employ_birthday_date; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["u_employ_country"]; ?> :</td>
                                                <?php
                                                if (is_numeric($data->employ_country)) {
                                                    $resultCountryById = Location::getCountryById($data->employ_country);
                                                    $dataCountryById = [];
                                                    if ($resultCountryById->status == 200 && !empty($resultCountryById->response)) {
                                                        $dataCountryById = $resultCountryById->response[0];
                                                    }
                                                    foreach (json_decode($dataCountryById->country_name) as $temp) {
                                                        if ($temp->slug == $language) {
                                                            echo '<td>' . $temp->value . '</td>';
                                                        }
                                                    }
                                                } else {
                                                    ?>
                                                    <td><?= ($data->employ_country); ?></td>
                                                    <?php
                                                }
                                                ?>

                                            </tr>

                                            <!--                                            <tr>-->
                                            <!--                                                <td>-->
                                            <?php //= $lang["u_employ_state"]; ?><!-- :</td>-->
                                            <!--                                                <td>-->
                                            <?php //= $data->employ_state; ?><!--</td>-->
                                            <!--                                            </tr>-->

                                            <tr>
                                                <td><?= $lang["u_employ_city"]; ?> :</td>
                                                <?php
                                                if (is_numeric($data->employ_city)) {
                                                    $resultcityById = Location::getCityNameById($data->employ_city);

                                                    echo '<td>' . $resultcityById->response . '</td>';

                                                } else {
                                                    ?>
                                                    <td><?= ($data->employ_city); ?></td>
                                                    <?php
                                                }
                                                ?>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["auth_id_card_number"]; ?> :</td>
                                                <td><?= $data->employ_code_national; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_gender"]; ?> :</td>
                                                <td><?= ($data->employ_gender == "man") ? $lang['a_man'] : $lang['a_female']; ?></td>
                                            </tr>

                                            <?php if ($data->employ_gender == "man") { ?>
                                                <tr>
                                                    <td><?= $lang["a_military_service"]; ?> :</td>
                                                    <td>
                                                        <?php
                                                        if ($data->employ_military == "end") {
                                                            echo $lang['a_military_service_end'];
                                                        } elseif ($data->employ_military == "sponsorship") {
                                                            echo $lang['a_military_service_sponsorship'];
                                                        } elseif ($data->employ_military == "exempt") {
                                                            echo $lang['a_military_service_exempt'];
                                                        } else {
                                                            echo $data->employ_military;
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>

                                                <?php
                                                if ($data->employ_military == "exempt") {
                                                    ?>
                                                    <tr>
                                                        <td><?= $lang["a_military_service_exempt_type"]; ?> :</td>
                                                        <td><?= $data->employ_exemption_type; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>

                                            <tr>
                                                <td><?= $lang["a_employ_marital"]; ?> :</td>
                                                <td><?= ($data->employ_marital == "single") ? $lang['a_single'] : $lang['a_married']; ?></td>
                                            </tr>

                                            <?php if ($data->employ_marital == "married") { ?>
                                                <tr>
                                                    <td><?= $lang["a_employ_marital"]; ?> :</td>
                                                    <td><?= $data->employ_count_child; ?></td>
                                                </tr>
                                            <?php } ?>

                                            <tr>
                                                <td><?= $lang["a_employ_home_status"]; ?> :</td>
                                                <td>
                                                    <?php
                                                    if ($data->employ_home_status == "personal") {
                                                        echo $lang['a_employ_home_status_personal'];
                                                    } elseif ($data->employ_home_status == "rental") {
                                                        echo $lang['a_employ_home_status_rental'];
                                                    } elseif ($data->employ_home_status == "father") {
                                                        echo $lang['a_employ_home_status_father'];
                                                    } else {
                                                        echo $data->employ_home_status;
                                                    }
                                                    ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_employ_insurance_status"]; ?> :</td>
                                                <td>
                                                    <?php
                                                    if ($data->employ_insurance_status == "tamin") {
                                                        echo $lang['a_employ_insurance_status_tamin'];
                                                    } elseif ($data->employ_insurance_status == "darman") {
                                                        echo $lang['a_employ_insurance_status_darman'];
                                                    } elseif ($data->employ_insurance_status == "mosalah") {
                                                        echo $lang['a_employ_insurance_status_mosalah'];
                                                    } elseif ($data->employ_insurance_status == "no") {
                                                        echo $lang['a_employ_insurance_status_no'];
                                                    } else {
                                                        echo $data->employ_insurance_status;
                                                    }
                                                    ?>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_employ_insurance_date"]; ?> :</td>
                                                <td><?= $data->employ_insurance_date; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["login_phone_number"]; ?> :</td>
                                                <td><?= $data->employ_mobile; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["auth_phone"]; ?> :</td>
                                                <td><?= $data->employ_phone; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["auth_address"]; ?> :</td>
                                                <td><?= $data->employ_address_location; ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_employ_company"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <th><?= $lang["name_company"]; ?></th>
                                                <th><?= $lang["date_start"]; ?></th>
                                                <th><?= $lang["date_end"]; ?></th>
                                                <th><?= $lang["a_reason"]; ?></th>
                                            </tr>
                                            <?php
                                            $employ_company = json_decode($data->employ_company);
                                            if (!empty($employ_company)) {
                                                foreach ($employ_company as $loop) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $loop->name; ?></td>
                                                        <td><?= $loop->timeStart; ?></td>
                                                        <td><?= $loop->timeEnd; ?></td>
                                                        <td><?= $loop->reason; ?></td>
                                                    </tr>
                                                    <?php
                                                }
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_education_list"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <td>#</td>
                                                <td><?= $lang['a_education_title']; ?></td>
                                                <td><?= $lang['a_education_location']; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_education_1"]; ?> :</td>
                                                <td><?= $data->employ_edu_name_1; ?></td>
                                                <td><?= $data->employ_edu_address_1; ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["a_education_2"]; ?> :</td>
                                                <td><?= $data->employ_edu_name_2; ?></td>
                                                <td><?= $data->employ_edu_address_2; ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["a_education_3"]; ?> :</td>
                                                <td><?= $data->employ_edu_name_3; ?></td>
                                                <td><?= $data->employ_edu_address_3; ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["a_education_4"]; ?> :</td>
                                                <td><?= $data->employ_edu_name_4; ?></td>
                                                <td><?= $data->employ_edu_address_4; ?></td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["a_education_5"]; ?> :</td>
                                                <td><?= $data->employ_edu_name_5; ?></td>
                                                <td><?= $data->employ_edu_address_5; ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>


                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_languages_gh"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <th><?= $lang["language"]; ?></th>
                                                <th><?= $lang["a_languages_talk"]; ?></th>
                                                <th><?= $lang["a_languages_read"]; ?></th>
                                                <th><?= $lang["a_languages_write"]; ?></th>
                                            </tr>
                                            <?php
                                            $employ_language = json_decode($data->employ_language);
                                            if (!empty($employ_language)) {
                                                foreach ($employ_language as $loop) {
                                                    if (!empty($loop->title)) {
                                                        ?>
                                                        <tr>
                                                            <td><?= $loop->title; ?></td>
                                                            <td>
                                                                <?php
                                                                if ($loop->talk == 1) {
                                                                    echo $lang['a_star_1'];
                                                                } elseif ($loop->talk == 2) {
                                                                    echo $lang['a_star_2'];
                                                                } elseif ($loop->talk == 3) {
                                                                    echo $lang['a_star_3'];
                                                                } elseif ($loop->talk == 4) {
                                                                    echo $lang['a_star_4'];
                                                                } else {
                                                                    echo $loop->talk;
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                if ($loop->read == 1) {
                                                                    echo $lang['a_star_1'];
                                                                } elseif ($loop->read == 2) {
                                                                    echo $lang['a_star_2'];
                                                                } elseif ($loop->read == 3) {
                                                                    echo $lang['a_star_3'];
                                                                } elseif ($loop->read == 4) {
                                                                    echo $lang['a_star_4'];
                                                                } else {
                                                                    echo $loop->read;
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                if ($loop->write == 1) {
                                                                    echo $lang['a_star_1'];
                                                                } elseif ($loop->write == 2) {
                                                                    echo $lang['a_star_2'];
                                                                } elseif ($loop->write == 3) {
                                                                    echo $lang['a_star_3'];
                                                                } elseif ($loop->write == 4) {
                                                                    echo $lang['a_star_4'];
                                                                } else {
                                                                    echo $loop->write;
                                                                }
                                                                ?>
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
                            <br>

                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_training_courses_degrees"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <th><?= $lang["title"]; ?></th>
                                                <th><?= $lang["address"]; ?></th>
                                                <th><?= $lang["b_inquiry_duration_day"]; ?></th>
                                            </tr>
                                            <?php
                                            $employ_record = json_decode($data->employ_record);
                                            if (!empty($employ_record)) {
                                                foreach ($employ_record as $loop) {
                                                    if (!empty($loop->title)) {
                                                        ?>
                                                        <tr>
                                                            <td><?= $loop->title; ?></td>
                                                            <td><?= $loop->address; ?></td>
                                                            <td><?= $loop->period; ?></td>
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
                            <br>

                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["all_info"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <td><?= $lang["a_employ_work"]; ?> :</td>
                                                <td><?= ($data->employ_work == "yes") ? $lang['a_yes'] : $lang['a_no']; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_employ_guarantee"]; ?> :</td>
                                                <td><?= ($data->employ_guarantee == "yes") ? $lang['a_yes'] : $lang['a_no']; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["u_are_you_ready_to_go_to_other_cities_or_branches"]; ?>
                                                    :
                                                </td>
                                                <td><?= ($data->employ_transfer == "yes") ? $lang['a_yes'] : $lang['a_no']; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_employ_price"]; ?> :</td>
                                                <td><?= number_format((int)$data->employ_price); ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_employ_representative_name"]; ?> :</td>
                                                <td><?= $data->employ_representative_name; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_employ_representative_phone"]; ?> :</td>
                                                <td><?= $data->employ_representative_phone; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_employ_representative_job"]; ?> :</td>
                                                <td><?= $data->employ_representative_job; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_employ_representative_address"]; ?> :</td>
                                                <td><?= $data->employ_representative_address; ?></td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_employ_employ"]; ?> :</td>
                                                <td>
                                                    <?php
                                                    if ($data->employ_employ == "post") {
                                                        echo $lang['a_employ_employ_post'];
                                                    } elseif ($data->employ_employ == "relatives") {
                                                        echo $lang['a_employ_employ_relatives'];
                                                    } elseif ($data->employ_employ == "jober") {
                                                        echo $lang['a_employ_employ_jober'];
                                                    } elseif ($data->employ_employ == "other") {
                                                        echo $lang['a_employ_employ_other'];
                                                    } else {
                                                        echo $data->employ_employ;
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["admins_log"]; ?></h5>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th><?= $lang['description']; ?></th>
                                        <th><?= $lang['date']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $employ_options = json_decode($data->employ_options);
                                    if (!empty($employ_options)) {
                                        foreach ($employ_options as $loop) {
                                            if ($loop->type == "desc") {
                                                ?>
                                                <tr>
                                                    <td><?= $loop->new; ?></td>
                                                    <td><?= Utils::getTimeCountry($Settings['data_time_format'], $loop->date); ?></td>
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

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <div class="text-center progress-demo">
                                <?php if ($data->employ_status == "pending") { ?>
                                    <button id="process"
                                            type="button"
                                            data-mj-id="<?= $data->employ_id; ?>"
                                            data-style="zoom-in"
                                            class="setSubmitBtn btn w-sm btn-soft-info waves-effect shadow-none waves-light mt-1">
                                        <?= $lang["a_start_process"]; ?>
                                    </button>
                                    <button id="reject"
                                            type="button"
                                            data-mj-id="<?= $data->employ_id; ?>"
                                            data-style="zoom-in"
                                            class="setSubmitBtn btn w-sm btn-soft-pink waves-effect shadow-none waves-light mt-1">
                                        <?= $lang["rejecting"]; ?>
                                    </button>
                                <?php } elseif ($data->employ_status == "process") { ?>
                                    <button id="completed"
                                            type="button"
                                            data-mj-id="<?= $data->employ_id; ?>"
                                            data-style="zoom-in"
                                            class="setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light mt-1">
                                        <?= $lang["a_start_completed"]; ?>
                                    </button>
                                    <button id="reject"
                                            type="button"
                                            data-mj-id="<?= $data->employ_id; ?>"
                                            data-style="zoom-in"
                                            class="setSubmitBtn btn w-sm btn-soft-pink waves-effect shadow-none waves-light mt-1">
                                        <?= $lang["rejecting"]; ?>
                                    </button>
                                <?php } ?>


                                <button id="btnDeleted"
                                        type="button"
                                        data-mj-id="<?= $data->employ_id; ?>"
                                        data-style="zoom-in"
                                        class="btn w-sm btn-soft-danger waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["delete"]; ?>
                                </button>

                                <button type="button"
                                        onclick="printContent('printDIV')"
                                        class="btn width-md btn-soft-primary waves-effect waves-light mt-1">
                                    <?= $lang['print']; ?>
                                </button>
                                <hr>
                                <div class="input-group mt-3">
                                    <textarea class="form-control form-control-light" id="employ-desc"
                                              placeholder="<?= $lang['a_add_admin_desc']; ?>"></textarea>
                                    <button data-style="zoom-in" class="btnDesc btn input-group-text btn-light"
                                            id="btn-desc" data-mj-id="<?= $data->employ_id; ?>"
                                            type="button"><?= $lang['submit']; ?></button>
                                </div>
                                <hr>

                                <table class="table mb-0">
                                    <tbody>
                                    <tr>
                                        <td><?= $lang["status"]; ?> :</td>
                                        <td>
                                            <?php
                                            if ($data->employ_status == "pending") {
                                                echo "<span class='badge badge-soft-warning font-13'>" . $lang['u_inquiry_air_pending'] . "</span>";
                                            } elseif ($data->employ_status == "process") {
                                                echo "<span class='badge badge-soft-info font-13'>" . $lang['u_inquiry_air_process'] . "</span>";
                                            } elseif ($data->employ_status == "completed") {
                                                echo "<span class='badge badge-soft-success font-13'>" . $lang['u_inquiry_air_completed'] . "</span>";
                                            } elseif ($data->employ_status == "reject") {
                                                echo "<span class='badge badge-soft-pink font-13'>" . $lang['reject'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-danger font-13'>" . $data->employ_status . "</span>";
                                            }
                                            ?>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["date"]; ?> :</td>
                                        <td>
                                            <bdi> <?= Utils::getTimeCountry($Settings['date_format'], $data->employ_submit_date); ?></bdi>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["attachment_files"]; ?></h5>
                            <?php if (strlen($data->employ_profile) > 0) { ?>
                                <p class="d-flex justify-content-center mb-2">
                                    <img src="<?= Utils::fileExist($data->employ_profile, USER_AVATAR); ?>"
                                         onclick="this.requestFullscreen()"
                                         class="rounded-circle avatar-lg img-thumbnail"
                                         alt="profile-image">
                                </p>
                            <?php } ?>

                            <?php
                            if (!empty($data->employ_files) && @json_decode($data->employ_files)) {
                                foreach (json_decode($data->employ_files) as $loop) {
                                    ?>
                                    <div class="card mb-1 shadow-none border">
                                        <div class="p-2">
                                            <div class="row align-items-center">
                                                <div class="col-auto">
                                                    <div class="avatar-sm">
                                                            <span
                                                                class="avatar-title badge-soft-primary text-primary rounded">
                                                                 <?= strtoupper(pathinfo($loop, PATHINFO_EXTENSION)); ?>
                                                            </span>
                                                    </div>
                                                </div>
                                                <div class="col ps-0">
                                                    <a href="<?= SITE_URL . $loop; ?>"
                                                       target="_self"
                                                       class="text-muted fw-bold">
                                                        <bdi>
                                                            <?= Utils::formatSizeUnits(filesize(getcwd() . $loop)); ?>
                                                        </bdi>
                                                    </a>
                                                </div>
                                                <div class="col-auto">
                                                    <!-- Button -->
                                                    <a href="<?= SITE_URL . $loop; ?>"
                                                       download=""
                                                       class="btn btn-link font-16 text-muted">
                                                        <i class="dripicons-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            } else {
                                ?>
                                <div class="alert alert-warning" role="alert">
                                    <i class="mdi mdi-alert-outline me-2"></i>
                                    <?= $lang['u_employ_a_no_file_attach']; ?>
                                </div>
                                <?php
                            }
                            ?>
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
                        'successful_delete_mag' => $lang['successful_delete_mag'],
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

        getFooter([]);

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

?>

