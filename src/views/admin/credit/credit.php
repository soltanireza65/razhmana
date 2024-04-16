<?php
$pageSlug = "card_bank";
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
        $result = AUser::getBankCardByID($id);
        $data = [];
        if ($result->status == 200 && !empty($result->response)) {
            $data = $result->response[0];
        }
        if (empty($data)) {
            header('Location: /admin/credit');
        }


        /**
         * Get User Info By Id
         */
        $resultUserInfoById = AUser::getUserInfoById($data->user_id);
        $dataUserInfoById = [];
        if ($resultUserInfoById->status == 200 && !empty($resultUserInfoById->response) && !empty($resultUserInfoById->response[0])) {
            $dataUserInfoById = $resultUserInfoById->response[0];
        }
        $name = $lang['guest_user'];
        $type = "businessman";
        if (!empty($dataUserInfoById)) {
            $name = Security::decrypt($dataUserInfoById->user_firstname) . " " . Security::decrypt($dataUserInfoById->user_lastname);
            $type = $dataUserInfoById->user_type;
        }


        /**
         * Get All Admins
         */
        $resultAllAdmins = Admin::getAllAdmins();
        $dataAllAdmins = [];
        if ($resultAllAdmins->status == 200 && !empty($resultAllAdmins->response)) {
            $dataAllAdmins = $resultAllAdmins->response;
        }


        $resultInquiry = AUser::getInquiryCardBankByCardId($id);
        $dataInquiry = [];
        if ($resultInquiry->status == 200 && !empty($resultInquiry->response)) {
            $dataInquiry = $resultInquiry->response;
        }

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }


        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('credit', '/dist/js/admin/credit/credit.init.js');

        getHeader($lang["card_bank_info"], [
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
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["card_bank_info"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <td><?= $lang["name_and_family"]; ?> :</td>
                                                <td>
                                                    <a href="/admin/users/info/<?= $data->user_id; ?>"
                                                       target="_self">
                                                        <?= $name; ?>
                                                    </a>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["card_bank"]; ?> :</td>
                                                <td>
                                                    <bdi>
                                                        <span data-bs-toggle="tooltip"
                                                              data-bs-placement="top"
                                                              title="<?= $lang['card_bank']; ?>">
                                                         <?= $data->card_bank; ?>
                                                        </span>
                                                    </bdi>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["card_number"]; ?> :</td>
                                                <td>
                                                       <span data-bs-toggle="tooltip"
                                                             data-bs-placement="top"
                                                             title="<?= $lang['card_number']; ?>">
                                                            <img src="<?= Utils::getBankIranInfo(substr($data->card_number, 0, 6))->icon; ?>"
                                                                 alt="<?= Utils::getBankIranInfo(substr($data->card_number, 0, 6))->name; ?>"
                                                                 width="34px"
                                                                 class="me-2 rounded-circle">
                                                            <bdi><?= $data->card_number; ?></bdi>
                                                       </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["card_account"]; ?> :</td>
                                                <td>
                                                    <bdi>
                                                        <span data-bs-toggle="tooltip"
                                                              data-bs-placement="top"
                                                              title="<?= $lang['card_account']; ?>">
                                                              <?= $data->card_account; ?>
                                                        </span>
                                                    </bdi>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["card_iban"]; ?> :</td>
                                                <td>
                                                    <bdi>
                                                        <span data-bs-toggle="tooltip"
                                                              data-bs-placement="top"
                                                              title="<?= $lang['card_iban']; ?>">
                                                              <?= $data->card_iban; ?>
                                                        </span>
                                                    </bdi>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["a_currency_type"]; ?> :</td>
                                                <td>
                                                    <bdi>
                                                            <span data-bs-toggle="tooltip"
                                                                  data-bs-placement="top"
                                                                  title="<?= $lang['card_iban']; ?>">
                                                                  <?php
                                                                  if (!empty($dataAllCurrencies)) {
                                                                      foreach ($dataAllCurrencies as $dataAllCurrenciesITEM) {
                                                                          if ($dataAllCurrenciesITEM->currency_id == $data->currency_id) {
                                                                              echo (!empty(array_column(json_decode($dataAllCurrenciesITEM->currency_name, true), 'value', 'slug')[$language])) ?
                                                                                  array_column(json_decode($dataAllCurrenciesITEM->currency_name, true), 'value', 'slug')[$language] : "";
                                                                          }
                                                                      }
                                                                  }
                                                                  ?>
                                                            </span>
                                                    </bdi>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["status"]; ?> :</td>
                                                <td>
                                                    <span id="change_credit_status"
                                                          data-bs-toggle="tooltip"
                                                          data-bs-placement="top"
                                                          title="<?= $lang['status']; ?>">
                                                             <?php
                                                             if ($data->card_status == "accepted") {
                                                                 echo $lang['accepted'];
                                                             } elseif ($data->card_status == "pending") {
                                                                 echo $lang['pending'];
                                                             } elseif ($data->card_status == "rejected") {
                                                                 echo $lang['rejected'];
                                                             } elseif ($data->card_status == "deleted") {
                                                                 echo $lang['deleted'];
                                                             } else {
                                                                 echo $data->card_status;
                                                             }
                                                             ?>
                                                    </span>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["date"]; ?> :</td>
                                                <td>
                                                     <span data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['date']; ?>">
                                                             <bdi> <?= Utils::getTimeCountry($Settings['date_format'], $data->card_time); ?></bdi>
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
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["a_result_inquiry"]; ?></h5>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                    <tr>
                                        <th><?= $lang['name_and_family']; ?></th>
                                        <th><?= $lang['card_account']; ?></th>
                                        <th><?= $lang['card_iban']; ?></th>
                                        <th><?= $lang['bank_name']; ?></th>
                                        <th><?= $lang['status']; ?></th>
                                        <th><?= $lang['date_submit']; ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($dataInquiry)) {
                                        foreach ($dataInquiry as $loop) {
                                            ?>
                                            <tr>
                                                <td><?= $loop->inquiry_owner_first_name . " " . $loop->inquiry_owner_last_name; ?></td>
                                                <td class="<?= ($data->card_account == $loop->inquiry_deposit_number) ? "text-success" : "text-danger"; ?>"><?= $loop->inquiry_deposit_number; ?></td>
                                                <td class="<?= (substr($data->card_iban, 4) == substr($loop->inquiry_iban, 2)) ? "text-success" : "text-danger"; ?>"><?= $loop->inquiry_iban; ?></td>
                                                <td><?= $loop->inquiry_bank; ?></td>
                                                <td><?= $loop->inquiry_status; ?></td>
                                                <td><?= Utils::getTimeCountry($Settings['date_format'], $loop->inquiry_submit_date); ?></td>
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
                                <button id="accepted"
                                        type="button"
                                        data-mj-id="<?= $data->card_id; ?>"
                                        data-style="zoom-in"
                                        class="<?= ($data->card_status == "accepted") ? "active" : ""; ?> setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["acceptedes"]; ?>
                                </button>
                                <button id="rejected"
                                        type="button"
                                        data-mj-id="<?= $data->card_id; ?>"
                                        data-style="zoom-in"
                                        class="<?= ($data->card_status == "rejected") ? "active" : ""; ?> setSubmitBtn btn w-sm btn-soft-pink waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["rejecting"]; ?>
                                </button>
                                <!--                                <button id="pending"-->
                                <!--                                        type="button"-->
                                <!--                                        data-mj-id="< ?= $data->card_id; ?>"-->
                                <!--                                        data-style="zoom-in"-->
                                <!--                                        class="< ?= ($data->card_status == "pending") ? "active" : ""; ?> setSubmitBtn btn w-sm btn-warning waves-effect shadow-none waves-light mt-1">-->
                                <!--                                    < ?= $lang["pending"]; ?>-->
                                <!--                                </button>-->
                                <button id="deleted"
                                        type="button"
                                        data-mj-id="<?= $data->card_id; ?>"
                                        data-style="zoom-in"
                                        class="<?= ($data->card_status == "deleted") ? "active" : ""; ?> setSubmitBtn btn w-sm btn-soft-warning waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["deleted_user"]; ?>
                                </button>
                                <button id="deleteBTN"
                                        type="button"
                                        data-mj-id="<?= $data->card_id; ?>"
                                        data-style="zoom-in"
                                        class="btn w-sm btn-soft-danger waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["delete_panel"]; ?>
                                </button>
                                <a href="/admin/credit"
                                   class="btn w-sm btn-soft-secondary  waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["btn_back"]; ?>
                                </a>
                                <hr>
                                <button id="inquiryBTN"
                                        type="button"
                                        data-mj-id="<?= $data->card_id; ?>"
                                        data-tj-card="<?= $data->card_number; ?>"
                                        data-style="zoom-in"
                                        class="btn w-sm btn-outline-info waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["a_inquiry_card_bank"]; ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <div class="card-widgets">
                                <a class="pe-1 lh-lg" data-bs-toggle="collapse" href="#cardCollpase1" role="button"
                                   aria-expanded="true" aria-controls="cardCollpase1">
                                    <i class="mdi mdi-minus"></i>
                                </a>
                            </div>
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["all_info"]; ?></h5>
                            <div class="table-responsive show" style="max-height: 176px;overflow: auto;"
                                 id="cardCollpase1">
                                <table class="table mb-0 table-sm">
                                    <tbody>
                                    <tr>
                                        <td colspan="2"><?= $lang["status"]; ?> :</td>
                                        <td><?php
                                            if ($data->card_status == "accepted") {
                                                echo "<span class='badge badge-soft-success font-13'>" . $lang['accepted'] . "</span>";
                                            } elseif ($data->card_status == "pending") {
                                                echo "<span class='badge badge-soft-warning font-13'>" . $lang['pending'] . "</span>";
                                            } elseif ($data->card_status == "rejected") {
                                                echo "<span class='badge badge-soft-danger font-13'>" . $lang['rejected'] . "</span>";
                                            } elseif ($data->card_status == "deleted") {
                                                echo "<span class='badge badge-soft-secondary font-13'>" . $lang['deleted'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-pink font-13'>" . $data->card_status . "</span>";
                                            }
                                            ?>
                                        </td>

                                    </tr>

                                    <?php
                                    if (!empty($data->card_options)) {
                                        $temp = json_decode($data->card_options);

                                        if (!empty($temp->update)) {
                                            foreach ($temp->update as $loop) {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php
                                                        if (!empty($dataAllAdmins)) {
                                                            foreach ($dataAllAdmins as $dataAllAdminsLOOP) {
                                                                if ($dataAllAdminsLOOP->admin_id == $loop->admin) {
                                                                    $name = $dataAllAdminsLOOP->admin_nickname;
                                                                }
                                                            }
                                                        }
                                                        echo (!empty($name)) ? $name : $loop->admin;
                                                        ?>
                                                    </td>
                                                    <td><?= $lang[$loop->status]; ?></td>
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
                        'successful_delete_mag' => $lang['successful_delete_mag'],
                        'error_mag' => $lang['error_mag'],
                        'empty_input' => $lang['empty_input'],
                        'token_error' => $lang['token_error'],
                        'accepted' => $lang['accepted'],
                        'rejected' => $lang['rejected'],
                        'pending' => $lang['pending'],
                        'deleted' => $lang['deleted'],
                        'a_inquiry_error' => $lang['a_inquiry_error'],
                        'a_inquiry_success' => $lang['a_inquiry_success'],
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
                $lang['help_credit_7'],
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