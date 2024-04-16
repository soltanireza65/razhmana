<?php
$pageSlug = "complaint";
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
        $Result = Complaint::getComplaintByID($id);
        $Data = [];
        if ($Result->status == 200 && !empty($Result->response)) {
            $Data = $Result->response[0];
        }
        if (empty($Data)) {
            header('Location: /admin/complaint');
        }


        /**
         * Get Complainer Info By Id
         */
        $resultComplainer = AUser::getUserInfoById($Data->complaint_from);
        $Complainer = [];
        if ($resultComplainer->status == 200 && !empty($resultComplainer->response) && !empty($resultComplainer->response[0])) {
            $Complainer = $resultComplainer->response[0];
        }
        $ComplainerName = $lang['guest_user'];
        $ComplainerType = "businessman";
        $ComplainerNumber = "";
        $ComplainerStatus = "";
        if (!empty($Complainer)) {
            $ComplainerName = Security::decrypt($Complainer->user_firstname) . " " . Security::decrypt($Complainer->user_lastname);
            $ComplainerType = $Complainer->user_type;
            $ComplainerNumber = Security::decrypt($Complainer->user_mobile);
            $ComplainerStatus = $Complainer->user_status;
        }


        /**
         * Get Accused Info By Id
         */
        $resultAccused = AUser::getUserInfoById($Data->complaint_to);
        $Accused = [];
        if ($resultAccused->status == 200 && !empty($resultAccused->response) && !empty($resultAccused->response[0])) {
            $Accused = $resultAccused->response[0];
        }
        $AccusedName = $lang['guest_user'];
        $AccusedType = "businessman";
        $AccusedNumber = "";
        $AccusedStatus = "";
        if (!empty($Accused)) {
            $AccusedName = Security::decrypt($Accused->user_firstname) . " " . Security::decrypt($Accused->user_lastname);
            $AccusedType = $Accused->user_type;
            $AccusedNumber = Security::decrypt($Accused->user_mobile);
            $AccusedStatus = $Complainer->user_status;
        }


        /**
         * Get All Admins
         */
        $dataAdminById = [];
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }

        if (!empty($dataAllAdmins)) {
            foreach ($dataAllAdmins as $dataAllAdminsITEM) {
                if ($dataAllAdminsITEM->admin_id == $Data->admin_id) {
                    $dataAdminById = $dataAllAdminsITEM;
                }
            }
        }


        /**
         * Get Cargo Info By ID
         */
        $resultCargoByID = Cargo::getCargoByID($Data->cargo_id);
        $dataCargoByID = [];
        if ($resultCargoByID->status == 200 && !empty($resultCargoByID->response)) {
            $dataCargoByID = $resultCargoByID->response[0];
        }


        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('complaint-info', '/dist/js/admin/complaint/complaint-info.init.js');

        getHeader($lang["complaint_info"], [
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
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                <a href="/admin/users/info/<?= $Data->complaint_from; ?>"
                                   target="_self">
                                    <i class="mdi mdi-account-circle-outline"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="<?= $lang['user_info']; ?>"></i>
                                </a>
                                <?= $lang["complainer"]; ?>
                            </h5>

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
                                                    <a href="/admin/users/info/<?= $Data->complaint_from; ?>"
                                                       target="_self">
                                                        <h5 class="m-0 fw-normal"> <?= $ComplainerName; ?></h5>
                                                        <p class="mb-0 text-muted">
                                                            <small>
                                                                <?php
                                                                if ($ComplainerType == "businessman") {
                                                                    echo $lang['businessman'];
                                                                } elseif ($ComplainerType == "driver") {
                                                                    echo $lang['driver'];
                                                                }
                                                                ?>
                                                            </small>
                                                        </p>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["contact_info"]; ?> :</td>
                                                <td>
                                                    <bdi>
                                                        <a href="tel:<?= $ComplainerNumber; ?>">
                                                                <span data-bs-toggle="tooltip"
                                                                      data-bs-placement="top"
                                                                      title="<?= $lang['click_to_contact']; ?>">
                                                               <?= $ComplainerNumber; ?>
                                                           </span>
                                                        </a>
                                                    </bdi>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["status"]; ?> :</td>
                                                <td>
                                                       <span data-bs-toggle="tooltip"
                                                             data-bs-placement="top"
                                                             title="<?= $lang['status']; ?>">
                                                                  <?php
                                                                  if ($ComplainerStatus == "active") {
                                                                      echo "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
                                                                  } elseif ($ComplainerStatus == "guest") {
                                                                      echo "<span class='badge badge-soft-warning font-12'>" . $lang['guest'] . "</span>";
                                                                  } elseif ($ComplainerStatus == "inactive") {
                                                                      echo "<span class='badge badge-soft-secondary font-12'>" . $lang['inactive'] . "</span>";
                                                                  } elseif ($ComplainerStatus == "suspend") {
                                                                      echo "<span class='badge badge-soft-danger font-12'>" . $lang['suspend'] . "</span>";
                                                                  } else {
                                                                      echo "<span class='badge badge-soft-pink font-12'>" . $ComplainerStatus . "</span>";
                                                                  }
                                                                  ?>
                                                       </span>
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
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase2" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase2">
                                    <i class="mdi mdi-minus"></i>
                                </a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                <a href="/admin/users/info/<?= $Data->complaint_to; ?>"
                                   target="_self">
                                    <i class="mdi mdi-account-circle-outline"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="<?= $lang['user_info']; ?>"></i>
                                </a>
                                <?= $lang["accused"]; ?>
                            </h5>

                            <div class="row show" id="cardCollpase2">

                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?= $lang["name_and_family"]; ?> :
                                                </td>
                                                <td>
                                                    <a href="/admin/users/info/<?= $Data->complaint_to; ?>"
                                                       target="_self">
                                                        <h5 class="m-0 fw-normal"> <?= $AccusedName; ?></h5>
                                                        <p class="mb-0 text-muted">
                                                            <small>
                                                                <?php
                                                                if ($AccusedType == "businessman") {
                                                                    echo $lang['businessman'];
                                                                } elseif ($AccusedType == "driver") {
                                                                    echo $lang['driver'];
                                                                }
                                                                ?>
                                                            </small>
                                                        </p>
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["contact_info"]; ?> :</td>
                                                <td>
                                                    <bdi>
                                                        <a href="tel:<?= $AccusedNumber; ?>">
                                                                <span data-bs-toggle="tooltip"
                                                                      data-bs-placement="top"
                                                                      title="<?= $lang['click_to_contact']; ?>">
                                                               <?= $AccusedNumber; ?>
                                                           </span>
                                                        </a>
                                                    </bdi>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><?= $lang["status"]; ?> :</td>
                                                <td>
                                                       <span data-bs-toggle="tooltip"
                                                             data-bs-placement="top"
                                                             title="<?= $lang['status']; ?>">
                                                                  <?php
                                                                  if ($AccusedStatus == "active") {
                                                                      echo "<span class='badge badge-soft-success font-12'>" . $lang['active'] . "</span>";
                                                                  } elseif ($AccusedStatus == "guest") {
                                                                      echo "<span class='badge badge-soft-warning font-12'>" . $lang['guest'] . "</span>";
                                                                  } elseif ($AccusedStatus == "inactive") {
                                                                      echo "<span class='badge badge-soft-secondary font-12'>" . $lang['inactive'] . "</span>";
                                                                  } elseif ($AccusedStatus == "suspend") {
                                                                      echo "<span class='badge badge-soft-danger font-12'>" . $lang['suspend'] . "</span>";
                                                                  } else {
                                                                      echo "<span class='badge badge-soft-pink font-12'>" . $AccusedStatus . "</span>";
                                                                  }
                                                                  ?>
                                                       </span>
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
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase3" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase3">
                                    <i class="mdi mdi-minus"></i>
                                </a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                <a href="/admin/cargo/<?= $Data->cargo_id; ?>"
                                   target="_self">
                                    <i class="mdi mdi-account-circle-outline"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="<?= $lang['cargo_info']; ?>"></i>
                                </a>
                                <?= $lang["cargo_info"]; ?>
                            </h5>

                            <div class="row show" id="cardCollpase3">

                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?= $lang["title"]; ?> :
                                                </td>
                                                <td>
                                                    <?= (isset($dataCargoByID->cargo_name)) ? $dataCargoByID->cargo_name : ""; ?>
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
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase4" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase4">
                                    <i class="mdi mdi-minus"></i>
                                </a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                <a href="/admin/cargo/<?= $Data->cargo_id; ?>"
                                   target="_self">
                                    <i class="mdi mdi-account-circle-outline"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="<?= $lang['request_info']; ?>"></i>
                                </a>
                                <?= $lang["request_info"]; ?>
                            </h5>

                            <div class="row show" id="cardCollpase4">

                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?= $lang["request_number"]; ?> :
                                                </td>
                                                <td>
                                                    <?= $Data->request_id; ?>
                                                </td>
                                            </tr>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>


                </div>

                <div class="col-lg-4" id="complaintID" data-mj-complaint-id="<?= $id; ?>">
                    <?php
                    if ($Data->complaint_status != "closed") {
                        ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                                <div class="text-center progress-demo">

                                    <?php
                                    if (is_null($Data->admin_id)) {
                                        ?>
                                        <button id="SetAdminID"
                                                type="button"
                                                data-style="zoom-in"
                                                class="btn w-sm btn-soft-primary waves-effect shadow-none waves-light mt-1">
                                            <i class="mdi mdi-hand-right"></i>
                                            <?= $lang["i_answer"]; ?>
                                        </button>
                                        <?php
                                    }
                                    if ($Data->complaint_status == "accepted") {
                                        ?>
                                        <div class="input-group mt-3">
                                            <textarea class="form-control form-control-light"
                                                      id="textAreaComplaint"
                                                      placeholder="<?= $lang["description"]; ?>"></textarea>
                                            <button class="btn input-group-text btn-light"
                                                    id="textAreaComplaintBTN"
                                                    data-style="zoom-in"
                                                    type="button"><?= $lang["closes"]; ?></button>
                                        </div>
                                        <small class="form-text text-muted text-start">
                                            <?= $lang['length_text']; ?> : <span
                                                    id="length_textAreaComplaint"
                                                    class="text-danger">0</span>
                                        </small>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

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
                                        <td><?= $lang["status"]; ?> :</td>
                                        <td>
                                            <?php
                                            if ($Data->complaint_status == "accepted") {
                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['now_answer'] . "</span>";
                                            } elseif ($Data->complaint_status == "pending") {
                                                echo "<span class='badge badge-soft-danger font-12'>" . $lang['pending_answer'] . "</span>";
                                            } elseif ($Data->complaint_status == "closed") {
                                                echo "<span class='badge badge-soft-primary font-12'>" . $lang['closed'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-pink font-12'>" . $Data->complaint_status . "</span>";
                                            }
                                            ?>
                                        </td>

                                    </tr>

                                    <tr>
                                        <td><?= $lang["date_create"]; ?> :</td>
                                        <td>
                                            <bdi>
                                                <?= Utils::getTimeCountry($Settings['data_time_format'], $Data->complaint_date); ?>
                                            </bdi>
                                        </td>
                                    </tr>
                                    <?php
                                    if (!empty($Data->admin_id)) {
                                        $name = '';
                                        if (!empty($dataAllAdmins)) {
                                            foreach ($dataAllAdmins as $dataAllAdminsITEM) {
                                                if ($dataAllAdminsITEM->admin_id == $Data->admin_id) {
                                                    $name = $dataAllAdminsITEM->admin_nickname;
                                                }
                                            }
                                        }
                                        ?>
                                        <tr>
                                            <td><?= $lang["admin_pursue"]; ?> :</td>
                                            <td>
                                                <?= $name; ?>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>

                                    <?php
                                    if (!empty($Data->complaint_options)) {
                                        $temp = json_decode($Data->complaint_options);
                                        if (isset($temp->accepted_date)) {
                                            ?>
                                            <tr>
                                                <td><?= $lang["admin_pursue_date"]; ?> :</td>
                                                <td>
                                                    <bdi>
                                                        <?= Utils::getTimeCountry($Settings['data_time_format'], $temp->accepted_date); ?>
                                                    </bdi>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        if (isset($temp->closed_admin_id)) {
                                            $name = '';
                                            if (!empty($dataAllAdmins)) {
                                                foreach ($dataAllAdmins as $dataAllAdminsITEM) {
                                                    if ($dataAllAdminsITEM->admin_id == $temp->closed_admin_id) {
                                                        $name = $dataAllAdminsITEM->admin_nickname;
                                                    }
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td><?= $lang["admin_terminator"]; ?> :</td>
                                                <td>
                                                    <?= $name; ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        if (isset($temp->closed_date)) {
                                            ?>
                                            <tr>
                                                <td><?= $lang["date_terminator"]; ?> :</td>
                                                <td>
                                                    <bdi>
                                                        <?= Utils::getTimeCountry('d F Y | H:i', $temp->closed_date); ?>
                                                    </bdi>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        if (isset($temp->description)) {
                                            ?>
                                            <tr>
                                                <td><?= $lang["description"]; ?> :</td>
                                                <td>
                                                    <?= $temp->description; ?>
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
            </div>


            <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('admin-complaint-info') ?>">
            <script>
                var var_lang = '<?php
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