<?php
$pageSlug = "user_currency";
// permission_can_edit
header('Location: /admin');
global $lang,$antiXSS;

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

        $resultBalanceInfoById = AUser::getBalanceInfoById($id);
        $dataBalanceInfoById = [];
        if ($resultBalanceInfoById->status == 200 && !empty($resultBalanceInfoById->response)) {
            $dataBalanceInfoById = $resultBalanceInfoById->response[0];
        }
        if (empty($dataBalanceInfoById)) {
            header('Location: /admin');
        }


        /**
         * Get User Info By Id
         */
        $userID = $dataBalanceInfoById->user_id;
        $resultUserInfoById = AUser::getUserInfoById($userID);
        $dataUserInfoById = [];
        if ($resultUserInfoById->status == 200 && !empty($resultUserInfoById->response)) {
            $dataUserInfoById = $resultUserInfoById->response[0];
        }
        if (empty($dataUserInfoById)) {
            header('Location: /admin');
        }

        $UserNam = $lang['guest_user'];
        if (!empty($dataUserInfoById->user_firstname)) {
            $UserNam = Security::decrypt($dataUserInfoById->user_firstname) . " " . Security::decrypt($dataUserInfoById->user_lastname);
        }


        /**
         * Get Currency By ID
         */
        $resultCurrencyById = Currency::getCurrencyById($dataBalanceInfoById->currency_id);
        $dataCurrencyById = [];
        if ($resultCurrencyById->status == 200 && !empty($resultCurrencyById->response)) {
            $dataCurrencyById = $resultCurrencyById->response[0];
        }

        // Load Stylesheets & Icons
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
        enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

        // Load Script In Footer
        enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
        enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('user-currency-edit', '/dist/js/admin/currency/user-currency-edit.init.js');

        getHeader($lang["user_currency_edit"], [
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
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2"><?= $lang["user_currency_edit"]; ?></h5>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="number"
                                               id="balanceValue"
                                               class="form-control"
                                               placeholder="<?= $lang["balance_value"]; ?>"
                                               value="<?= $dataBalanceInfoById->balance_value; ?>">
                                        <label for="balanceValue">
                                            <?= $lang["balance_value"]; ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="number"
                                               id="balanceFrozen"
                                               class="form-control"
                                               placeholder="<?= $lang["balance_frozen"]; ?>"
                                               value="<?= $dataBalanceInfoById->balance_frozen; ?>">
                                        <label for="balanceFrozen">
                                            <?= $lang["balance_frozen"]; ?>
                                        </label>
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
                                <button id="btnSubmit"
                                        type="button"
                                        class="btn w-sm btn-soft-success waves-effect shadow-none waves-light"
                                        data-mj-balance-id="<?= $id; ?>"
                                        data-style="zoom-in">
                                    <?= $lang["submit_change"]; ?>
                                </button>
                                <a href="/admin/users/info/<?= $userID; ?>"
                                   class="btn w-sm btn-soft-secondary waves-effect shadow-none waves-light">
                                    <?= $lang["btn_back"]; ?>
                                </a>
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
                                        <td><?= $lang["name_and_family"]; ?> :</td>
                                        <td>
                                            <a href="/admin/users/info/<?= $userID; ?>">
                                                <?= $UserNam; ?>
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= $lang["currency_type"]; ?> :</td>
                                        <td>
                                            <?= (!empty(array_column(json_decode($dataCurrencyById->currency_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                array_column(json_decode($dataCurrencyById->currency_name, true), 'value', 'slug')[$_COOKIE['language']] : ""; ?>
                                        </td>
                                    </tr>

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