<?php
$pageSlug = "a_poster_expert";
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

        $data = Poster::getPosterExpertByID($id);
        if (empty($data)) {
            header('Location: /admin/poster-expert');
        }

        $experts = Expert::getAllActiveExperts();

        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }


        // Load Stylesheets & Icons
        enqueueStylesheet('editable-css', '/dist/libs/x-editable/bootstrap-editable/css/bootstrap-editable.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        enqueueStylesheet('select2', '/dist/libs/select2/css/select2.min.css');


        // Load Script In Footer
        enqueueScript('editable-js', '/dist/libs/x-editable/bootstrap-editable/js/bootstrap-editable.min.js');
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('select2', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('poster-expert-info', '/dist/js/admin/poster/poster-expert-info.init.js');

        getHeader($lang["a_request_expert"], [
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
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase1" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase1">
                                    <i class="mdi mdi-minus"></i>
                                </a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"
                                data-tj-id="<?= $id; ?>"><?= $lang["a_request_expert"]; ?></h5>

                            <div class="row show" id="cardCollpase1">

                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?= $lang["name_and_family"]; ?> :
                                                </td>
                                                <td>
                                                    <a href="/admin/users/info/<?= $data->user_id; ?>"
                                                       target="_self">
                                                        <h5 class="m-0 fw-normal">
                                                            <i class="mdi mdi-account"></i>
                                                            <?= Security::decrypt($data->user_firstname) . " " . Security::decrypt($data->user_lastname); ?>
                                                        </h5>
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <?= $lang["a_poster_info"]; ?> :
                                                </td>
                                                <td>
                                                    <a href="/admin/poster/info/<?= $data->poster_id; ?>"
                                                       target="_self">
                                                        <h5 class="m-0 fw-normal">
                                                            <i class="mdi mdi-book-alert-outline"></i>
                                                            <?= $lang['show']; ?>
                                                        </h5>
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <?= $lang["transaction_info"]; ?> :
                                                </td>
                                                <td>
                                                    <a href="/admin/transaction/info/<?= $data->transaction_id; ?>"
                                                       target="_self">
                                                        <h5 class="m-0 fw-normal">
                                                            <i class="mdi mdi-currency-usd-circle-outline"></i>
                                                            <?= $lang['show']; ?>
                                                        </h5>
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td>
                                                    <?= $lang["address"]; ?> :
                                                </td>
                                                <td>
                                                    <span class="m-0 fw-normal"
                                                          data-type="text"
                                                          id="change_pe_address"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          title="<?= $lang['click_for_edit']; ?>"
                                                          data-mj-type="pe_address">
                                                            <?= $data->pe_address; ?>
                                                        </span>
                                                </td>
                                            </tr>


                                            <tr>
                                                <td>
                                                    <?= $lang["a_expert_name"]; ?> :
                                                </td>
                                                <td>
                                                    <i class="mdi mdi-account-hard-hat"></i>
                                                    <span class="m-0 fw-normal"
                                                          data-type="select"
                                                          id="change_expert_id"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          title="<?= $lang['click_for_edit']; ?>"
                                                          data-mj-type="expert_id">
                                                            <?= $data->expert_firstname . " " . $data->expert_lastname; ?>
                                                        </span>

                                                </td>
                                            </tr>


                                            <tr>
                                                <td>
                                                    <?= $lang["description"]; ?> :
                                                </td>
                                                <td>
                                                    <span class="m-0 fw-normal"
                                                          data-type="text"
                                                          id="change_pe_reason"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          title="<?= $lang['click_for_edit']; ?>"
                                                          data-mj-type="pe_reason">
                                                            <?= $data->pe_reason; ?>
                                                        </span>
                                                </td>
                                            </tr>


                                            <tr>
                                                <td><?= $lang["request_submit_date"]; ?> :</td>
                                                <td>
                                                    <bdi class="m-0 fw-normal">
                                                        <?= Utils::getTimeCountry($Settings['date_format'], $data->pe_submit_date) ?>
                                                    </bdi>
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
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase5" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase5">
                                    <i class="mdi mdi-minus"></i>
                                </a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["all_info"]; ?></h5>
                            <div class="table-responsive show"
                                 id="cardCollpase5">
                                <table class="table mb-0 table-sm">
                                    <tbody>
                                    <tr>
                                        <td colspan="2"><?= $lang['status']; ?></td>
                                        <td colspan="2">
                                            <?php
                                            if ($data->pe_status == "accepted") {
                                                echo "<span class='badge badge-soft-info font-12'>" . $lang['a_answer_expert_pending'] . "</span>";
                                            } elseif ($data->pe_status == "pending") {
                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['pending_answer'] . "</span>";
                                            } elseif ($data->pe_status == "rejected") {
                                                echo "<span class='badge badge-soft-danger font-12'>" . $lang['reject'] . "</span>";
                                            } elseif ($data->pe_status == "canceled") {
                                                echo "<span class='badge badge-soft-danger font-12'>" . $lang['canceled'] . "</span>";
                                            } elseif ($data->pe_status == "completed") {
                                                echo "<span class='badge badge-soft-primary font-12'>" . $lang['a_done'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-pink font-12'>" . $data->pe_status . "</span>";
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    if (!empty($data->pe_options)) {
                                        $temp = json_decode($data->pe_options);
                                        foreach ($temp as $loop) {
                                            ?>
                                            <tr>
                                                <td>
                                                    <bdi>
                                                        <?= Utils::getTimeCountry($Settings['data_time_format'], $loop->date); ?>
                                                    </bdi>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($loop->type == 'pe_address') {
                                                        echo $lang['a_change_address'];
                                                    } elseif ($loop->type == 'expert_id') {
                                                        echo $lang['a_select_experts'];
                                                    } elseif ($loop->type == 'pe_reason') {
                                                        echo $lang['a_add_description'];
                                                    } elseif ($loop->type == "pe_status") {
                                                        echo $lang['a_change_status'];
                                                    } else {
                                                        echo $loop->type;
                                                    }
                                                    ?>
                                                </td>
                                                <td class="d-none">
                                                    <?= $loop->value; ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    $name = '';
                                                    foreach ($dataAllAdmins as $dataAllAdminsITEM) {
                                                        if ($dataAllAdminsITEM->admin_id == $loop->admin) {
                                                            $name = $dataAllAdminsITEM->admin_nickname;
                                                        }
                                                    }
                                                    echo $name; ?>
                                                </td>
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
                </div>

                <div class="col-lg-4">

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <div class="text-center progress-demo">

                                <?php if ($data->pe_status == "pending") { ?>
                                    <button id="accepted"
                                            type="button"
                                            data-style="zoom-in"
                                            class="btn-submit btn w-sm btn-soft-primary waves-effect shadow-none waves-light mt-1">
                                        <?= $lang["acceptedes"]; ?>
                                    </button>
                                    <button id="rejected"
                                            type="button"
                                            data-style="zoom-in"
                                            class="btn-submit btn w-sm btn-soft-danger waves-effect shadow-none waves-light mt-1">
                                        <?= $lang["b_reject"]; ?>
                                    </button>
                                    <?php
                                } elseif ($data->pe_status == "accepted") {
                                    ?>
                                    <button id="completed"
                                            type="button"
                                            data-style="zoom-in"
                                            class="btn-submit btn w-sm btn-soft-success waves-effect shadow-none waves-light mt-1">
                                        <?= $lang["a_done"]; ?>
                                    </button>
                                    <button id="canceled"
                                            type="button"
                                            data-style="zoom-in"
                                            class="btn-submit btn w-sm btn-soft-danger waves-effect shadow-none waves-light mt-1">
                                        <?= $lang["a_to_cancel"]; ?>
                                    </button>
                                <?php } else {
                                    echo $lang['status'] . " : ";
                                    if ($data->pe_status == "accepted") {
                                        echo "<span class='badge badge-soft-info font-12'>" . $lang['a_answer_expert_pending'] . "</span>";
                                    } elseif ($data->pe_status == "pending") {
                                        echo "<span class='badge badge-soft-warning font-12'>" . $lang['pending_answer'] . "</span>";
                                    } elseif ($data->pe_status == "rejected") {
                                        echo "<span class='badge badge-soft-danger font-12'>" . $lang['reject'] . "</span>";
                                    } elseif ($data->pe_status == "canceled") {
                                        echo "<span class='badge badge-soft-danger font-12'>" . $lang['canceled'] . "</span>";
                                    } elseif ($data->pe_status == "completed") {
                                        echo "<span class='badge badge-soft-primary font-12'>" . $lang['a_done'] . "</span>";
                                    } else {
                                        echo "<span class='badge badge-soft-pink font-12'>" . $data->pe_status . "</span>";
                                    }
                                }

                                if (isset($data->expert_id)) {
                                    ?>

                                    <hr>
                                    <button id="send-sms"
                                            class="btn w-sm btn-soft-info waves-effect shadow-none waves-light mt-1">
                                        <i class="mdi mdi-cellphone-information"></i>
                                        <?= $lang['a_sent_address']; ?></button>
                                    <p class="text-info font-12"><?= $lang['a_notic_expert_but_not_sent_sms']; ?></p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>


                </div>
            </div>


            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('admin-pe-info') ?>">
            <script>
                var var_lang = '<?php
                    $array_experts = [];
                    foreach ($experts as $expert) {
                        array_push($array_experts, ["text" => $expert->expert_firstname . " " . $expert->expert_lastname, 'value' => $expert->expert_id]);
                    }
                    $var_lang = [
                        'error' => $lang['error'],
                        'successful' => $lang['successful'],
                        'warning' => $lang['warning'],
                        'successful_update_mag' => $lang['successful_update_mag'],
                        'successful_admin_set' => $lang['successful_admin_set'],
                        'before_admin_set' => $lang['before_admin_set'],
                        'before_admin_closed' => $lang['before_admin_closed'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
                        'token_error' => $lang['token_error'],
                        'u_no_result_found' => $lang['u_no_result_found'],
                        'successful_submit_mag' => $lang['successful_submit_mag'],
                        'a_empty' => $lang['a_empty'],
                        'a_sent_address_sms' => $lang['a_sent_address_sms'],
                        'array_experts' => $array_experts,
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