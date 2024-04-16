<?php
$pageSlug = "transaction";
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
        $Result = Transactions::getTransactionsByID($id);
        $Data = [];
        if ($Result->status == 200 && !empty($Result->response)) {
            $Data = $Result->response[0];
        }
        if (empty($Data)) {
            header('Location: /admin/transaction');
        }


        /**
         * Get User Info By Id
         */
        $resultUserInfoById = AUser::getUserInfoById($Data->user_id);
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


        $cardID = 0;
        if (!empty($Data->card_id) && isset($Data->card_id)) {
            $cardID = $Data->card_id;
        }


        if ($Data->transaction_type == "withdraw") {
//            $resultBankCardByID = AUser::getBankCardByID($cardID);
            $resultBankCardByID = AUser::getBankCardByID($Data->card_id);
            $dataBankCardByID = [];
            if ($resultBankCardByID->status == 200 && !empty($resultBankCardByID->response)) {
                $dataBankCardByID = $resultBankCardByID->response[0];
            }
        }


        /**
         * Get All Currencies
         */
        $resultAllCurrencies = Currency::getAllCurrencies();
        $dataAllCurrencies = [];
        if ($resultAllCurrencies->status == 200 && !empty($resultAllCurrencies->response)) {
            $dataAllCurrencies = $resultAllCurrencies->response;
        }

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');
        enqueueStylesheet('editable-css', '/dist/libs/x-editable/bootstrap-editable/css/bootstrap-editable.css');

        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('editable-js', '/dist/libs/x-editable/bootstrap-editable/js/bootstrap-editable.min.js');
        enqueueScript('transaction-info', '/dist/js/admin/transaction/transaction-info.init.js');

        getHeader($lang["transaction_info"], [
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
                <div class="col-lg-8" id="printDIV">
                    <div class="card">
                        <div class="card-body">
                            <?php
                            if ($Data->transaction_type == "withdraw") {
                                ?>
                                <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                    <a href="/admin/credit/<?= $dataBankCardByID->card_id; ?>"
                                       target="_self">
                                        <i class="mdi mdi-credit-card-check-outline"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="<?= $lang['card_bank_info']; ?>"></i>
                                    </a>
                                    <?= $lang["card_bank_info"]; ?>
                                </h5>
                            <?php } else {
                                ?>
                                <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                    <a href="/admin/users/info/<?= $Data->user_id; ?>"
                                       target="_self">
                                        <i class="mdi mdi-account-circle-outline"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="<?= $lang['user_info']; ?>"></i>
                                    </a>
                                    <?= $lang["user_info"]; ?>
                                </h5>
                                <?php
                            }
                            ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <td>
                                                    <?= $lang["name_and_family"]; ?> :
                                                </td>
                                                <td>
                                                    <a href="/admin/users/info/<?= $Data->user_id; ?>"
                                                       target="_self">
                                                        <h5 class="m-0 fw-normal"> <?= $name; ?></h5>
                                                        <p class="mb-0 text-muted">
                                                            <small>
                                                                <?php
                                                                if ($type == "businessman") {
                                                                    echo $lang['businessman'];
                                                                } elseif ($type == "driver") {
                                                                    echo $lang['driver'];
                                                                } else {
                                                                    echo $lang['guest_user'];
                                                                }
                                                                ?>
                                                            </small>
                                                        </p>
                                                    </a>
                                                </td>

                                            </tr>

                                            <?php
                                            if ($Data->transaction_type == "withdraw") {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?= $lang["card_bank"]; ?> :
                                                    </td>
                                                    <td>
                                                        <bdi>
                                                             <span data-bs-toggle="tooltip"
                                                                   data-bs-placement="top"
                                                                   title="<?= $lang['card_bank']; ?>">
                                                                 <?= $dataBankCardByID->card_bank; ?>
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
                                                                 <img src="<?= Utils::getBankIranInfo(substr($dataBankCardByID->card_number, 0, 6))->icon; ?>"
                                                                      alt="<?= Utils::getBankIranInfo(substr($dataBankCardByID->card_number, 0, 6))->name; ?>"
                                                                      width="34px"
                                                                      class="me-2 rounded-circle">
                                                                <bdi><?= $dataBankCardByID->card_number; ?></bdi>
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
                                                                  <?= $dataBankCardByID->card_account; ?>
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
                                                                  <?= $dataBankCardByID->card_iban; ?>
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
                                                                          if ($dataAllCurrenciesITEM->currency_id == $dataBankCardByID->currency_id) {
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
                                                        <span data-bs-toggle="tooltip"
                                                              data-bs-placement="top"
                                                              title="<?= $lang['status']; ?>">
                                                                 <?php
                                                                 if ($dataBankCardByID->card_status == "accepted") {
                                                                     echo "<span class='badge badge-soft-success font-13'>" . $lang['accepted'] . "</span>";
                                                                 } elseif ($dataBankCardByID->card_status == "pending") {
                                                                     echo "<span class='badge badge-soft-warning font-13'>" . $lang['pending'] . "</span>";
                                                                 } elseif ($dataBankCardByID->card_status == "rejected") {
                                                                     echo "<span class='badge badge-soft-danger font-13'>" . $lang['rejected'] . "</span>";
                                                                 } elseif ($dataBankCardByID->card_status == "deleted") {
                                                                     echo "<span class='badge badge-soft-secondary font-13'>" . $lang['deleted'] . "</span>";
                                                                 } else {
                                                                     echo "<span class='badge badge-soft-pink font-13'>" . $dataBankCardByID->card_status . "</span>";
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
                                                          <bdi><?= Utils::getTimeCountry($Settings['date_format'], $dataBankCardByID->card_time); ?></bdi>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"
                                id="transactionID"
                                data-mj-transaction-id="<?= $id; ?>">
                                <?= $lang["transaction_info"]; ?>
                            </h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                            <tr>
                                                <td><?= $lang["transaction_type"]; ?> :</td>
                                                <td>
                                                    <?php
                                                    $transaction_type_Temp = "";
                                                    if ($Data->transaction_type == "deposit") {
                                                        echo "<span class='badge badge-soft-primary font-13'>" . $lang['deposit'] . "</span>";
                                                        $transaction_type_Temp = "deposit";
                                                    } elseif ($Data->transaction_type == "withdraw") {
                                                        echo "<span class='badge badge-soft-success font-13'>" . $lang['withdraw'] . "</span>";
                                                        $transaction_type_Temp = "withdraw";
                                                    } else {
                                                        echo "<span class='badge badge-soft-danger font-13'>" . $Data->transaction_type . "</span>";
                                                    }
                                                    ?>
                                                </td>
                                            </tr>

                                            <?php
                                            if ($transaction_type_Temp == "deposit") {
                                                ?>
                                                <tr>
                                                    <td><?= $lang["deposit_type"]; ?> :</td>
                                                    <td>
                                                        <bdi>
                                                              <span data-bs-toggle="tooltip"
                                                                    data-bs-placement="top"
                                                                    title="<?= $lang['transaction_deposit_type']; ?>">
                                                                    <?php
                                                                    if ($Data->transaction_deposit_type == "receipt") {
                                                                        echo $lang['receipt'];
                                                                    } elseif ($Data->transaction_deposit_type == "online") {
                                                                        echo $lang['online'];
                                                                    } else {
                                                                        echo $Data->transaction_deposit_type;
                                                                    }
                                                                    ?>
                                                              </span>
                                                        </bdi>
                                                    </td>
                                                </tr>
                                            <?php } ?>

                                            <tr>
                                                <td><?= $lang["authority"]; ?> :</td>
                                                <td>
                                                    <bdi>
                                                        <span
                                                            <?= ($Data->transaction_type == "withdraw" && $Data->transaction_status == "pending") ? 'id="change_transaction_authority" data-mj-type="transaction_authority" data-type="text" ' : '' ?>
                                                              data-bs-toggle="tooltip"
                                                              data-bs-placement="top"
                                                              title="<?= $lang['authority']; ?>">
                                                              <?= $Data->transaction_authority; ?>
                                                        </span>
                                                    </bdi>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["tracking_code"]; ?> :</td>
                                                <td>
                                                    <bdi>
                                                        <span
                                                           <?= ($Data->transaction_type == "withdraw" && $Data->transaction_status == "pending") ? 'id="change_transaction_trackingCode" data-mj-type="transaction_tracking_code" data-type="text" ' : ''; ?>
                                                              data-bs-toggle="tooltip"
                                                              data-bs-placement="top"
                                                              title="<?= $lang['tracking_code']; ?>">
                                                              <?= $Data->transaction_tracking_code; ?>
                                                        </span>
                                                    </bdi>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["transaction_amount"]; ?> :</td>
                                                <td>
                                                    <bdi>
                                                        <span data-bs-toggle="tooltip"
                                                              data-bs-placement="top"
                                                              title="<?= $lang['transaction_amount']; ?>">
                                                              <?php
                                                              echo number_format($Data->transaction_amount) . " ";
                                                              if (!empty($dataAllCurrencies)) {
                                                                  foreach ($dataAllCurrencies as $dataAllCurrenciesITEM) {
                                                                      if ($dataAllCurrenciesITEM->currency_id == $Data->currency_id) {
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
                                                <td><?= $lang["gateway"]; ?> :</td>
                                                <td>
                                                    <bdi>
                                                        <span <?= ($Data->transaction_type == "withdraw" && $Data->transaction_status == "pending") ? 'id="change_transaction_gateway" data-mj-type="transaction_gateway" data-type="text" ' : ''; ?>
                                                              data-bs-toggle="tooltip"
                                                              data-bs-placement="top"
                                                              title="<?= $lang['gateway']; ?>">
                                                              <?= $Data->transaction_gateway; ?>
                                                        </span>
                                                    </bdi>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang["status"]; ?> :</td>
                                                <td>
                                                   <span
                                                           data-bs-toggle="tooltip"
                                                           data-bs-placement="top"
                                                           title="<?= $lang['status']; ?>"
                                                           data-mj-type="cargo_weight">
                                                              <?php
                                                              if ($Data->transaction_status == "completed") {
                                                                  echo "<span class='badge badge-soft-success font-12'>" . $lang['completed'] . "</span>";
                                                              } elseif ($Data->transaction_status == "pending") {
                                                                  echo "<span class='badge badge-soft-warning font-12'>" . $lang['pending'] . "</span>";
                                                              } elseif ($Data->transaction_status == "rejected") {
                                                                  echo "<span class='badge badge-soft-danger font-12'>" . $lang['rejected'] . "</span>";
                                                              } elseif ($Data->transaction_status == "expired") {
                                                                  echo "<span class='badge badge-soft-secondary font-12'>" . $lang['expired'] . "</span>";
                                                              } elseif ($Data->transaction_status == "paid") {
                                                                  echo "<span class='badge badge-soft-success font-12'>" . $lang['paid'] . "</span>";
                                                              } elseif ($Data->transaction_status == "unpaid") {
                                                                  echo "<span class='badge badge-outline-dark font-12'>" . $lang['unpaid'] . "</span>";
                                                              } elseif ($Data->transaction_status == "pending_deposit") {
                                                                  echo "<span class='badge badge-outline-warning font-12'>" . $lang['pending_deposit'] . "</span>";
                                                              } elseif ($Data->transaction_status == "rejected_deposit") {
                                                                  echo "<span class='badge badge-outline-danger font-12'>" . $lang['rejected_deposit'] . "</span>";
                                                              } else {
                                                                  echo "<span class='badge badge-soft-pink font-12'>" . $Data->transaction_status . "</span>";
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
                                                      <bdi><?= Utils::getTimeCountry($Settings['date_format'], $Data->transaction_date); ?></bdi>
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
                                        <td colspan="3"><?= $lang["status"]; ?> :</td>
                                        <td id="change_transaction_status"><?php
                                            if ($Data->transaction_status == "completed") {
                                                echo "<span class='badge badge-soft-success font-12'>" . $lang['completed'] . "</span>";
                                            } elseif ($Data->transaction_status == "pending") {
                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['pending'] . "</span>";
                                            } elseif ($Data->transaction_status == "rejected") {
                                                echo "<span class='badge badge-soft-danger font-12'>" . $lang['rejected'] . "</span>";
                                            } elseif ($Data->transaction_status == "expired") {
                                                echo "<span class='badge badge-soft-secondary font-12'>" . $lang['expired'] . "</span>";
                                            } elseif ($Data->transaction_status == "paid") {
                                                echo "<span class='badge badge-outline-info font-12'>" . $lang['paid'] . "</span>";
                                            } elseif ($Data->transaction_status == "unpaid") {
                                                echo "<span class='badge badge-outline-dark font-12'>" . $lang['unpaid'] . "</span>";
                                            } elseif ($Data->transaction_status == "pending_deposit") {
                                                echo "<span class='badge badge-outline-warning font-12'>" . $lang['pending_deposit'] . "</span>";
                                            } elseif ($Data->transaction_status == "rejected_deposit") {
                                                echo "<span class='badge badge-outline-danger font-12'>" . $lang['rejected_deposit'] . "</span>";
                                            } else {
                                                echo "<span class='badge badge-soft-pink font-12'>" . $Data->transaction_status . "</span>";
                                            }
                                            ?>
                                        </td>

                                    </tr>

                                    <?php
                                    if (!empty($Data->transaction_updates)) {
                                        $temp = json_decode($Data->transaction_updates);

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
                                                    <td><?php
                                                        if ($loop->type == "transaction_authority") {
                                                            echo $lang['authority'];
                                                        } elseif ($loop->type == "transaction_tracking_code") {
                                                            echo $lang['tracking_code'];
                                                        } elseif ($loop->type == "transaction_gateway") {
                                                            echo $lang['gateway'];
                                                        } elseif ($loop->type == "transaction_status") {
                                                            echo $lang['status'];
                                                        } else {
                                                            echo $loop->type;
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php
                                                        if ($loop->type == "transaction_status") {
                                                            if ($loop->value == "completed") {
                                                                echo $lang['completed'];
                                                            } elseif ($loop->value == "pending") {
                                                                echo $lang['pending'];
                                                            } elseif ($loop->value == "rejected") {
                                                                echo $lang['rejected'];
                                                            } elseif ($loop->value == "expired") {
                                                                echo $lang['expired'];
                                                            } elseif ($loop->value == "paid") {
                                                                echo $lang['paid'];
                                                            } elseif ($loop->value == "unpaid") {
                                                                echo $lang['unpaid'];
                                                            } elseif ($loop->value == "rejected_deposit") {
                                                                echo $lang['rejected_deposit'];
                                                            } else {
                                                                echo $loop->value;
                                                            }

                                                        } else {
                                                            $loop->value;
                                                        }
                                                        ?></td>
                                                    <td>
                                                        <bdi><?= Utils::getTimeCountry('d F Y', $loop->date); ?></bdi>
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

                <div class="col-lg-4">

                    <!--Start Status-->
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["action"]; ?></h5>
                            <div class="text-center progress-demo">
                                <?php
                                if ($Data->transaction_type == "withdraw") {
                                    if ($Data->transaction_status == "pending") {
                                        ?>
                                        <button id="completed"
                                                type="button"
                                                data-mj-id="<?= $id; ?>"
                                                data-style="zoom-in"
                                                class="<?= ($Data->transaction_status == "completed") ? "active" : ""; ?> setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light mt-1">
                                            <?= $lang["accepted_deposit"]; ?>
                                        </button>
                                        <button id="rejected"
                                                type="button"
                                                data-mj-id="<?= $Data->transaction_id; ?>"
                                                data-style="zoom-in"
                                                class="<?= ($Data->transaction_status == "rejected") ? "active" : ""; ?> setSubmitBtn btn w-sm btn-soft-danger waves-effect shadow-none waves-light mt-1">
                                            <?= $lang["rejected_deposit"]; ?>
                                        </button>
                                        <?php
                                    }
                                } elseif ($Data->transaction_type == "deposit") {
                                    if ($Data->transaction_status == "pending_deposit" && $Data->transaction_deposit_type == "receipt") {
                                        ?>
                                        <button id="paid"
                                                type="button"
                                                data-mj-id="<?= $Data->transaction_id; ?>"
                                                data-style="zoom-in"
                                                class="<?= ($Data->transaction_status == "pending_deposit") ? "active" : ""; ?> setSubmitBtn btn w-sm btn-soft-success waves-effect shadow-none waves-light mt-1">
                                            <?= $lang["accepted_deposit"]; ?>
                                        </button>
                                        <button id="rejected_deposit"
                                                type="button"
                                                data-mj-id="<?= $Data->transaction_id; ?>"
                                                data-style="zoom-in"
                                                class="<?= ($Data->transaction_status == "rejected_deposit") ? "active" : ""; ?> setSubmitBtn btn w-sm btn-soft-pink waves-effect shadow-none waves-light mt-1">
                                            <?= $lang["rejected_deposit"]; ?>
                                        </button>
                                        <?php
                                    } elseif ($Data->transaction_deposit_type == "online") {
                                        ?>
                                        <?php
                                    }
                                    ?>


                                <?php } ?>
                                <button id="printBTN"
                                        onclick="printContent('printDIV')"
                                        type="button"
                                        class="btn w-sm btn-primary waves-effect shadow-none waves-light mt-1">
                                    <?= $lang["print"]; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!--End Status-->

                    <?php
                    if ($Data->transaction_type == "deposit" && $Data->transaction_deposit_type == "receipt") {
                        if (!empty($Data->transaction_receipt) && isset($Data->transaction_receipt)) {
                            ?>
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="text-uppercase mt-0 mb-3 bg-light p-2">
                                        <a href="<?= $Data->transaction_receipt; ?>"
                                           target="_self">
                                            <i class="mdi mdi-application"
                                               data-bs-toggle="tooltip"
                                               data-bs-placement="top"
                                               title="<?= $lang['show']; ?>"></i>
                                        </a>
                                        <?= $lang["transaction_receipt"]; ?>
                                    </h5>
                                    <img alt="<?= $lang["transaction_receipt"]; ?>" class="w-100"
                                         src="<?= Utils::fileExist($Data->transaction_receipt, BOX_EMPTY); ?>"
                                         onclick="this.requestFullscreen()">
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
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
                        'completed' => $lang['completed'],
                        'rejected' => $lang['rejected'],
                        'pending' => $lang['pending'],
                        'expired' => $lang['expired'],
                        'unpaid' => $lang['unpaid'],
                        'paid' => $lang['paid'],
                        'a_empty' => $lang['a_empty'],
                        'rejected_deposit' => $lang['rejected_deposit'],
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
                $lang['help_transaction_10']
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