<?php
$pageSlug = "census";
// permission_can_show

global $lang, $antiXSS;

use MJ\Utils\Utils;

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

        $start = $antiXSS->xss_clean($_REQUEST['start']);
        $end = $antiXSS->xss_clean($_REQUEST['end']);

        if (!is_numeric($start) || !is_numeric($end)) {
            header('Location: /admin/census/transaction');
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
         * Get All Admin Slug (Page ID)
         */
        $Result = Transactions::getTransactionByTime($start, $end);
        $Data = [];
        if ($Result->status == 200 && !empty($Result->response)) {
            $Data = $Result->response;
        }


        /**
         * Get All Currencies
         */
        $resultAllCurrencies = Currency::getAllCurrencies();
        $dataAllCurrencies = [];
        if ($resultAllCurrencies->status == 200 && !empty($resultAllCurrencies->response)) {
            $dataAllCurrencies = $resultAllCurrencies->response;
        }
        enqueueScript('printThis', '/dist/libs/printThis/printThis.js');
        enqueueScript('census-transaction', '/dist/js/admin/census/census-transaction-show.init.js');

        getHeader($lang['census_transaction'], [
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
            <style>
                @media print {
                    canvas {
                        display: flex;
                        justify-content: center;
                        min-height: 100%;
                        max-width: 100%;
                        max-height: 100%;
                        height: 200px !important;
                        width: 200px !important;
                    }

                    body {
                        direction: rtl;
                        text-align: right;
                    }
                }
            </style>
            <div class="row" id="printDIV">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">

                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2 text-center"><?= $lang["census_transaction"]; ?></h5>
                            <div class="row d-flex justify-content-center">

                                <div class="col-sm-4 col-md-4 col-lg-4 printDIVs">
                                    <h4 class="font-13 text-muted text-uppercase mb-1">
                                        <?= $lang['date_start']; ?> :
                                    </h4>
                                    <p class=""> <?= Utils::getTimeCountry('Y/m/d', $start); ?></p>
                                </div>

                                <div class="col-sm-4 col-md-4 col-lg-4 printDIVs">
                                    <h4 class="font-13 text-muted text-uppercase mb-1"><?= $lang['date_end']; ?> :</h4>
                                    <p class=""> <?= Utils::getTimeCountry('Y/m/d', $end); ?></p>
                                </div>

                                <div class="col-sm-4 col-md-4 col-lg-4 ">
                                    <button type="button"
                                            onclick="printContent()"
                                            class="btn btn-lg width-lg btn-soft-info waves-effect waves-light">
                                        <i class="mdi mdi-printer me-1"></i>
                                        <?= $lang['print']; ?>
                                    </button>

                                    <a href="/admin/census/transaction/"
                                       class="btn btn-lg width-lg btn-soft-primary waves-effect waves-light">
                                        <i class="mdi mdi-reload me-1"></i>
                                        <?= $lang['start_again']; ?>

                                    </a>


                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <?php
                //                print_r($Data);

                $tempWithdraw = [];
                $tempDeposit = [];
                if (!empty($Data)) {
                    foreach ($Data as $DataLoop) {
                        if ($DataLoop->transaction_type == "withdraw") {
//                            if (isset($tempWithdraw[$DataLoop->transaction_status])) {
                            $currency = json_decode($DataLoop->transaction_options)->currency_id;
                            if (isset($tempWithdraw[$DataLoop->transaction_status][$currency])) {
                                $tempWithdraw[$DataLoop->transaction_status][$currency]['amount'] += $DataLoop->transaction_amount;
                            } else {
                                $tempWithdraw[$DataLoop->transaction_status][$currency]['amount'] = $DataLoop->transaction_amount;
                            }
//                            } else {
//
//                            }

                        } elseif ($DataLoop->transaction_type == "deposit") {
                            $currency = json_decode($DataLoop->transaction_options)->currency_id;
                            if (isset($tempDeposit[$DataLoop->transaction_status][$currency])) {
                                $tempDeposit[$DataLoop->transaction_status][$currency]['amount'] += $DataLoop->transaction_amount;
                            } else {
                                $tempDeposit[$DataLoop->transaction_status][$currency]['amount'] = $DataLoop->transaction_amount;
                            }
                        }
                    }
                }

                ?>


                <div class="col-sm-12 col-md-12 col-lg-6 printDIVs">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">

                                <h5 class="text-uppercase mt-0 mb-3 bg-light p-2 text-center"><?= $lang['census_withdraw']; ?></h5>

                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody style="border-color:#43bfe5;">
                                            <?php
                                            if (!empty($tempWithdraw)) {
                                                foreach ($tempWithdraw as $index => $tempWithdrawLOOP) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $lang[$index]; ?></td>
                                                        <td>
                                                            <?php
                                                            if (!empty($tempWithdrawLOOP)) {
                                                                ?>
                                                                <ul class="list-group">
                                                                    <?php
                                                                    foreach ($tempWithdrawLOOP as $ind => $tempWithdrawLOOPLOOP) {
                                                                        ?>
                                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                            <?php
                                                                            $CurrencyName = '';
                                                                            if (!empty($dataAllCurrencies)) {
                                                                                foreach ($dataAllCurrencies as $dataAllCurrenciesITEM) {
                                                                                    if ($dataAllCurrenciesITEM->currency_id == $ind) {
                                                                                        $CurrencyName = (!empty(array_column(json_decode($dataAllCurrenciesITEM->currency_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                                            array_column(json_decode($dataAllCurrenciesITEM->currency_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                                                                                    }
                                                                                }
                                                                            }
                                                                            echo $CurrencyName;
                                                                            ?>
                                                                            <span data-plugin="counterup">
                                                                                <?= number_format($tempWithdrawLOOPLOOP['amount']); ?>
                                                                            </span>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </ul>
                                                                <?php

                                                            }
                                                            ?>
                                                            <!-- <span data-plugin="counterup">425</span>-->
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
                </div>

                <div class="col-sm-12 col-md-12 col-lg-6 printDIVs">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">

                                <h5 class="text-uppercase mt-0 mb-3 bg-light p-2 text-center"><?= $lang['census_deposit']; ?></h5>

                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody style="border-color:#43bfe5;">
                                            <?php
                                            if (!empty($tempDeposit)) {
                                                foreach ($tempDeposit as $index => $tempWithdrawLOOP) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $lang[$index]; ?></td>
                                                        <td>
                                                            <?php
                                                            if (!empty($tempWithdrawLOOP)) {
                                                                ?>
                                                                <ul class="list-group">
                                                                    <?php
                                                                    foreach ($tempWithdrawLOOP as $ind => $tempWithdrawLOOPLOOP) {
                                                                        ?>
                                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                            <?php
                                                                            $CurrencyName = '';
                                                                            if (!empty($dataAllCurrencies)) {
                                                                                foreach ($dataAllCurrencies as $dataAllCurrenciesITEM) {
                                                                                    if ($dataAllCurrenciesITEM->currency_id == $ind) {
                                                                                        $CurrencyName = (!empty(array_column(json_decode($dataAllCurrenciesITEM->currency_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                                            array_column(json_decode($dataAllCurrenciesITEM->currency_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                                                                                    }
                                                                                }
                                                                            }
                                                                            echo $CurrencyName;
                                                                            ?>
                                                                            <span data-plugin="counterup">
                                                                                <?= number_format($tempWithdrawLOOPLOOP['amount']); ?>
                                                                            </span>
                                                                        </li>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </ul>
                                                                <?php
                                                            }
                                                            ?>
                                                            <!-- <span data-plugin="counterup">425</span>-->
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
                </div>


                <div class="col-sm-12 col-md-12 col-lg-12 printDIVs">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">

                                <h5 class="text-uppercase mt-0 mb-3 bg-light p-2 text-center"><?= $lang['census_detail']; ?></h5>

                                <div class="col-sm-12 col-md-6 col-lg-12">
                                    <div class="table-responsive">
                                        <table id="orders-table" data-page-length='10' data-order='[[ 0, "desc" ]]'
                                               class="table table-hover m-0 table-centered dt-responsive nowrap w-100">
                                            <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><?= $lang['transaction_type']; ?></th>
                                                <th><?= $lang['transaction_amount']; ?></th>
                                                <th><?= $lang['date']; ?></th>
                                                <th><?= $lang['status']; ?></th>
                                                <th><?= $lang['action']; ?></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php

                                            if (!empty($Data)) {
                                                $i = 1;
                                                $Data = array_reverse($Data);
                                                foreach ($Data as $DataITEM) {
                                                    ?>
                                                    <tr>
                                                        <td><?= $i++; ?></td>
                                                        <td>
                                                            <?php
                                                            if ($DataITEM->transaction_type == "deposit") {
                                                                echo "<span class='badge badge-soft-primary font-13'>" . $lang['deposit'] . "</span>";
                                                            } elseif ($DataITEM->transaction_type == "withdraw") {
                                                                echo "<span class='badge badge-soft-success font-13'>" . $lang['withdraw'] . "</span>";
                                                            } else {
                                                                echo "<span class='badge badge-soft-pink font-13'>" . $DataITEM->transaction_type . "</span>";
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $CurrencyName = '';
                                                            $CurrencyID = (@json_decode($DataITEM->transaction_options) && isset(json_decode($DataITEM->transaction_options)->currency_id)) ? json_decode($DataITEM->transaction_options)->currency_id : 0;
                                                            if (!empty($dataAllCurrencies)) {
                                                                foreach ($dataAllCurrencies as $dataAllCurrenciesITEM) {
                                                                    if ($dataAllCurrenciesITEM->currency_id == $CurrencyID) {
                                                                        $CurrencyName = (!empty(array_column(json_decode($dataAllCurrenciesITEM->currency_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                            array_column(json_decode($dataAllCurrenciesITEM->currency_name, true), 'value', 'slug')[$_COOKIE['language']] : "";
                                                                    }
                                                                }
                                                            }

                                                            echo number_format($DataITEM->transaction_amount) . " " . $CurrencyName; ?>
                                                        </td>


                                                        <td>
                                                            <bdi><?= Utils::getTimeCountry('Y/m/d', $DataITEM->transaction_date); ?></bdi>
                                                        </td>


                                                        <td>
                                                            <?php
                                                            if ($DataITEM->transaction_status == "completed") {
                                                                echo "<span class='badge badge-soft-success font-12'>" . $lang['completed'] . "</span>";
                                                            } elseif ($DataITEM->transaction_status == "pending") {
                                                                echo "<span class='badge badge-soft-warning font-12'>" . $lang['pending'] . "</span>";
                                                            } elseif ($DataITEM->transaction_status == "rejected") {
                                                                echo "<span class='badge badge-soft-danger font-12'>" . $lang['rejected'] . "</span>";
                                                            } elseif ($DataITEM->transaction_status == "expired") {
                                                                echo "<span class='badge badge-outline-secondary font-12'>" . $lang['expired'] . "</span>";
                                                            } elseif ($DataITEM->transaction_status == "paid") {
                                                                echo "<span class='badge badge-outline-info font-12'>" . $lang['paid'] . "</span>";
                                                            } elseif ($DataITEM->transaction_status == "unpaid") {
                                                                echo "<span class='badge badge-outline-dark font-12'>" . $lang['unpaid'] . "</span>";
                                                            } elseif ($DataITEM->transaction_status == "pending_deposit") {
                                                                echo "<span class='badge badge-outline-warning font-12'>" . $lang['pending_deposit'] . "</span>";
                                                            } elseif ($DataITEM->transaction_status == "rejected_deposit") {
                                                                echo "<span class='badge badge-outline-danger font-12'>" . $lang['rejected_deposit'] . "</span>";
                                                            } else {
                                                                echo "<span class='badge badge-soft-pink font-12'>" . $DataITEM->transaction_status . "</span>";
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <a target="_blank"
                                                               data-bs-toggle="tooltip"
                                                               data-bs-placement="top"
                                                               title="<?= $lang['info_transaction']; ?>"
                                                               href="/admin/transaction/info/<?= $DataITEM->transaction_id; ?>"
                                                               class="action-icon">
                                                                <i class="mdi mdi-square-edit-outline"></i>
                                                            </a>
                                                            <a data-bs-toggle="tooltip"
                                                               data-bs-placement="top"
                                                               title="<?= $lang['user_info']; ?>"
                                                               href="/admin/users/info/<?= $DataITEM->user_id; ?>"
                                                               target="_blank"
                                                               class="action-icon">
                                                                <i class="mdi mdi-eye"></i>
                                                            </a>
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
                </div>

            </div>

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