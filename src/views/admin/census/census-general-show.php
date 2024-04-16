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
        $start = strtotime(date('Y-m-d', $start) . date('00:00'));

        $end = $antiXSS->xss_clean($_REQUEST['end']);
        $end = strtotime(date('Y-m-d', $end) . date('23:59'))+59;

        if (!is_numeric($start) || !is_numeric($end)) {
            header('Location: /admin/census/general');
        }

        $userType = AUser::getCountUserFromCensus($end, $start);
        $cargoStatus = Cargo::getCountCargoFromCensus($end, $start);
        $inquiryAir = Air::getCountAirFromCensus($end, $start);
        $inquiryShip = Ship::getCountShipFromCensus($end, $start);
        $inquiryRailroad = Railroad::getCountRailroadFromCensus($end, $start);
        $inquiryInventory = Inventory::getCountInventoryFromCensus($end, $start);
        $poster = Poster::getCountPosterFromCensus($end, $start);
        $posterDeleted = Poster::getReasonDeletedPosterFromCensus($end, $start);
        $cargoCategory = Cargo::getCargoCategoryFromCensus($end, $start);
        $cargoType = Cargo::getCargoTypeFromCensus($end, $start);

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }

        getHeader($lang['a_report_general'], [
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
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="text-uppercase mt-0 mb-3 bg-light p-2 text-center"><?= $lang["a_report_general"]; ?></h5>
                            <div class="row d-flex justify-content-center">

                                <div class="col-sm-4 col-md-4 col-lg-4 printDIVs">
                                    <h4 class="font-13 text-muted text-uppercase mb-1">
                                        <?= $lang['date_start']; ?> :
                                    </h4>
                                    <p class=""> <?= Utils::getTimeCountry('Y/m/d  H:i a', $start); ?></p>
                                </div>

                                <div class="col-sm-4 col-md-4 col-lg-4 printDIVs">
                                    <h4 class="font-13 text-muted text-uppercase mb-1"><?= $lang['date_end']; ?> :</h4>
                                    <p class=""> <?= Utils::getTimeCountry('Y/m/d H:i a', $end); ?></p>
                                </div>

                                <div class="col-sm-4 col-md-4 col-lg-4 ">
                                    <a href="/admin/census/general/"
                                       class="btn btn-lg width-lg btn-soft-primary waves-effect waves-light">
                                        <i class="mdi mdi-reload me-1"></i>
                                        <?= $lang['start_again']; ?>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>


                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">

                                <h5 class="text-uppercase mt-0 mb-3 bg-light p-2 text-center"><?= $lang['a_report_result']; ?></h5>

                                <div class="col-12 col-sm-12 col-md-12 col-lg-12">
                                    <div class="table-responsive">
                                        <table class="table mb-0 table-striped">
                                            <tbody>
                                            <tr>
                                                <td><?= $lang['a_submit_users']; ?></td>
                                                <td colspan="2">
                                                    <table class="table mb-0">
                                                        <tr>
                                                            <td><?= $lang['driver']; ?></td>
                                                            <td><?= $userType['driver']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['businessman']; ?></td>
                                                            <td><?= $userType['businessman']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['guest_user']; ?></td>
                                                            <td><?= $userType['guest']; ?></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang['cargo']; ?></td>
                                                <td colspan="2">
                                                    <table class="table mb-0">
                                                        <tr>
                                                            <td><?= $lang['pending']; ?></td>
                                                            <td><?= $cargoStatus['pending']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['accepted']; ?></td>
                                                            <td><?= $cargoStatus['accepted']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['rejected']; ?></td>
                                                            <td><?= $cargoStatus['rejected']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['progress']; ?></td>
                                                            <td><?= $cargoStatus['progress']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['canceled']; ?></td>
                                                            <td><?= $cargoStatus['canceled']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['completed']; ?></td>
                                                            <td><?= $cargoStatus['completed']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['expired']; ?></td>
                                                            <td><?= $cargoStatus['expired']; ?></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="2"><?= $lang['a_category_selected']; ?></td>
                                                <td>
                                                    <table class="table mb-0">
                                                        <?php
                                                        $x2Array = [];
                                                        foreach ($cargoCategory as $loop) {
                                                            if (isset($x2Array[$loop->category_id]['count'])) {
                                                                $x2Array[$loop->category_id]['count'] += 1;
                                                            } else {
                                                                $x2Array[$loop->category_id]['count'] = 1;
                                                                $x2Array[$loop->category_id]['name'] = $loop->category_name;
                                                            }
                                                        }

                                                        foreach ($x2Array as $loop) {
                                                            ?>
                                                            <tr>
                                                                <td><?= (!empty(array_column(json_decode($loop['name'], true), 'value', 'slug')[$language])) ?
                                                                        array_column(json_decode($loop['name'], true), 'value', 'slug')[$language] : ""; ?></td>
                                                                <td><?= $loop['count']; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </table>
                                                </td>
                                            </tr>


                                            <tr>
                                                <td colspan="2"><?= $lang['a_requested_car']; ?></td>
                                                <td>
                                                    <table class="table mb-0">
                                                        <?php
                                                        $x3Array = [];
                                                        foreach ($cargoType as $loop) {
                                                            if (isset($x3Array[$loop->type_id]['count'])) {
                                                                $x3Array[$loop->type_id]['count'] += 1;
                                                            } else {
                                                                $x3Array[$loop->type_id]['count'] = 1;
                                                                $x3Array[$loop->type_id]['name'] = $loop->type_name;
                                                            }
                                                        }

                                                        foreach ($x3Array as $loop) {
                                                            ?>
                                                            <tr>
                                                                <td><?= (!empty(array_column(json_decode($loop['name'], true), 'value', 'slug')[$language])) ?
                                                                        array_column(json_decode($loop['name'], true), 'value', 'slug')[$language] : ""; ?></td>
                                                                <td><?= $loop['count']; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </table>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang['b_inquiry_air']; ?></td>
                                                <td colspan="2">
                                                    <table class="table mb-0">
                                                        <tr>
                                                            <td><?= $lang['a_freight_pending']; ?></td>
                                                            <td><?= $inquiryAir['pending']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['a_freight_process']; ?></td>
                                                            <td><?= $inquiryAir['process']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['a_freight_completed']; ?></td>
                                                            <td><?= $inquiryAir['completed'] + $inquiryAir['read']; ?></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang['b_inquiry_ship']; ?></td>
                                                <td colspan="2">
                                                    <table class="table mb-0">
                                                        <tr>
                                                            <td><?= $lang['a_freight_pending']; ?></td>
                                                            <td><?= $inquiryShip['pending']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['a_freight_process']; ?></td>
                                                            <td><?= $inquiryShip['process']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['a_freight_completed']; ?></td>
                                                            <td><?= $inquiryShip['completed'] + $inquiryShip['read']; ?></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang['b_inquiry_rail']; ?></td>
                                                <td colspan="2">
                                                    <table class="table mb-0">
                                                        <tr>
                                                            <td><?= $lang['a_freight_pending']; ?></td>
                                                            <td><?= $inquiryRailroad['pending']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['a_freight_process']; ?></td>
                                                            <td><?= $inquiryRailroad['process']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['a_freight_completed']; ?></td>
                                                            <td><?= $inquiryRailroad['completed'] + $inquiryRailroad['read']; ?></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang['b_inquiry_inventory']; ?></td>
                                                <td colspan="2">
                                                    <table class="table mb-0">
                                                        <tr>
                                                            <td><?= $lang['a_freight_pending']; ?></td>
                                                            <td><?= $inquiryInventory['pending']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['a_freight_process']; ?></td>
                                                            <td><?= $inquiryInventory['process']; ?></td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['a_freight_completed']; ?></td>
                                                            <td><?= $inquiryInventory['completed'] + $inquiryInventory['read']; ?></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td><?= $lang['a_poster']; ?></td>
                                                <td colspan="2">
                                                    <table class="table mb-0">
                                                        <tr>
                                                            <td><?= $lang['a_truck']; ?></td>
                                                            <td>
                                                                <table class="table mb-0">
                                                                    <tr>
                                                                        <td><?= $lang['u_inquiry_air_pending']; ?></td>
                                                                        <td><?= $poster['pendingT']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?= $lang['a_user_inquiry_accepted']; ?></td>
                                                                        <td><?= $poster['acceptedT']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?= $lang['reject']; ?></td>
                                                                        <td><?= $poster['rejectT']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?= $lang['deleted']; ?></td>
                                                                        <td><?= $poster['deletedT']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?= $lang['expire']; ?></td>
                                                                        <td><?= $poster['expiredT']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?= $lang['needed']; ?></td>
                                                                        <td><?= $poster['neededT']; ?></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td><?= $lang['a_trailer']; ?></td>
                                                            <td>
                                                                <table class="table mb-0">
                                                                    <tr>
                                                                        <td><?= $lang['u_inquiry_air_pending']; ?></td>
                                                                        <td><?= $poster['pending']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?= $lang['a_user_inquiry_accepted']; ?></td>
                                                                        <td><?= $poster['accepted']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?= $lang['reject']; ?></td>
                                                                        <td><?= $poster['reject']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?= $lang['deleted']; ?></td>
                                                                        <td><?= $poster['deleted']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?= $lang['expire']; ?></td>
                                                                        <td><?= $poster['expired']; ?></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td><?= $lang['needed']; ?></td>
                                                                        <td><?= $poster['needed']; ?></td>
                                                                    </tr>
                                                                </table>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="2"><?= $lang['a_reason_deleted']; ?></td>
                                                <td>
                                                    <table class="table mb-0">
                                                        <?php
                                                        $x1Array = [];
                                                        foreach ($posterDeleted as $loop) {
                                                            if (isset($x1Array[$loop->delete_id]['count'])) {
                                                                $x1Array[$loop->delete_id]['count'] += 1;
                                                            } else {
                                                                $x1Array[$loop->delete_id]['count'] = 1;
                                                                $x1Array[$loop->delete_id]['name'] = $loop->category_name;
                                                            }
                                                        }

                                                        foreach ($x1Array as $loop) {
                                                            ?>
                                                            <tr>
                                                                <td><?= (!empty(array_column(json_decode($loop['name'], true), 'value', 'slug')[$language])) ?
                                                                        array_column(json_decode($loop['name'], true), 'value', 'slug')[$language] : ""; ?></td>
                                                                <td><?= $loop['count']; ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </table>
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