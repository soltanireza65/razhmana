<?php
$pageSlug = "transaction";
// permission_can_show

global $lang, $Settings;

use MJ\Security\Security;
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

        /**
         * Get All Transactions
         */
        $Result = Transactions::getAllTransactionsByStatus('pending');
        $Data = [];
        if ($Result->status == 200 && !empty($Result->response)) {
            $Data = $Result->response;
        }

        getHeader($lang["withdraws_pending"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_show',
        ]);

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }
        // start roles 2
        if ($flagSlug) {
            // end roles 2
            ?>
            <div class="row">
                <?php
                if (!empty($Data)) {
                    foreach ($Data as $DataITEM) {

                        /**
                         * Get User Info By Id
                         */
                        $resultUserInfoById = AUser::getUserInfoById($DataITEM->user_id);
                        $name = $lang['guest_user'];
                        if ($resultUserInfoById->status == 200 && !empty($resultUserInfoById->response) && !empty($resultUserInfoById->response[0])) {
                            $name = Security::decrypt($resultUserInfoById->response[0]->user_firstname) . " " . Security::decrypt($resultUserInfoById->response[0]->user_lastname);
                        }

                        $currency = '';
                        $resultCurrencyById = Currency::getCurrencyById($DataITEM->currency_id);
                        $dataCurrencyById = [];
                        if ($resultCurrencyById->status == 200 && !empty($resultCurrencyById->response)) {
                            $dataCurrencyById = $resultCurrencyById->response[0];
                            $currency = (!empty(array_column(json_decode($dataCurrencyById->currency_name, true), 'value', 'slug')[$language])) ?
                                array_column(json_decode($dataCurrencyById->currency_name, true), 'value', 'slug')[$language] : "";
                        }

                        $cardID = '';
                        if (isset($DataITEM->card_id) && !empty($DataITEM->card_id)) {
                            $resultBankCardByID = AUser::getBankCardByID($DataITEM->card_id);
                            $dataBankCardByID = [];
                            if ($resultBankCardByID->status == 200 && !empty($resultBankCardByID->response)) {
                                $dataBankCardByID = $resultBankCardByID->response[0];
                                $cardID = $dataBankCardByID->card_number;
                            }
                        }
                        ?>
                        <div class="col-md-4">
                            <div class="card mb-0 mt-3 border border-warning">
                                <div class="card-body">
                                    <span class="badge badge-soft-warning float-end"><?= $lang['pending']; ?></span>
                                    <h5 class="mt-0">
                                        <a href="/admin/transaction/info/<?= $DataITEM->transaction_id; ?>"
                                           target="_self"
                                           data-bs-toggle="tooltip"
                                           data-bs-placement="top"
                                           title="<?= $lang['show_detail']; ?>"
                                           class="text-warning">
                                            <?= $cardID; ?>
                                        </a>
                                    </h5>
                                    <p><?= $lang['request_amount'] . " : " . number_format($DataITEM->transaction_amount) . " " . $currency; ?></p>
                                    <div class="clearfix"></div>
                                    <div class="row">
                                        <div class="col">
                                            <a href="/admin/users/info/<?= $DataITEM->user_id; ?>"
                                               class="text-reset">
                                                <img src="<?= Utils::fileExist((!empty($resultUserInfoById->user_avatar)) ? $resultUserInfoById->user_avatar : USER_AVATAR, USER_AVATAR); ?>"
                                                     alt="<?= $name; ?>"
                                                     class="avatar-sm img-thumbnail rounded-circle">
                                                <span class="d-none d-md-inline-block ms-1 fw-semibold"><?= $name; ?></span>
                                            </a>
                                        </div>
                                        <div class="col-auto">
                                            <div class="text-end text-muted">
                                                <p class="font-13 mt-2 mb-0">
                                                    <i class="mdi mdi-calendar"></i>
                                                    <bdi><?= Utils::getTimeCountry($Settings['date_format'], $DataITEM->transaction_date); ?></bdi>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    ?>
                    <div class="col-lg-12 ">
                        <p class="text-center">
                            <img src="<?= BOX_EMPTY; ?>"
                                 style="width: 50%;max-width: fit-content;"
                                 alt="<?= $lang['no_massages']; ?>">
                        </p>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3
        getFooter(
            [
                $lang['help_transaction_9'],
                $lang['help_transaction_8'],
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