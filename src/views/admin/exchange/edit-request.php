<?php

use MJ\Security\Security;
use MJ\Utils\Utils;

$pageSlug = "exchange";
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


// Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
// Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('toast', '/dist/js/admin/exchange/edit-request.js');
        // header text
        getHeader($lang["live_price"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_edit',
        ]);

// start roles 2

        if ($flagSlug) {
// end roles 2


            $request = Exchange::getRequestDetail($_REQUEST['id']);
            $request = $request->status == 200 ? $request->response : [];


            if ($request->request_type == 'ir') {
                $type = $lang['ir_order_exchange'];
            } elseif ($request->request_type == 'ru') {
                $type = $lang['ru_order_exchange'];
            } elseif ($request->request_type == 'du') {
                $type = $lang['du_order_exchange'];
            } elseif ($request->request_type == 'tr') {
                $type = $lang['tr_order_exchange'];
            }

            $price = Exchange::getPriceDetail($request->price_id);
            $price = $price->status == 200 ? $price->response : [];
            $user = User::getUserInfo($request->user_id);


            $descs = Exchange::getRequestDescs($_REQUEST['id']);
            $descs = $descs->status == 200 ? $descs->response : [];

            $slug = 'title_' . $_COOKIE['language']
            ?>


            <!--start custom html-->


            <div class="card ribbon-box">
                <div class="card-body">
                    <div
                        class="ribbon ribbon-<?= $request->request_status == 'pending' ? 'warning' : '' ?><?= $request->request_status == 'accepted' ? 'primary' : '' ?><?= $request->request_status == 'rejected' ? 'danger' : '' ?> float-start">
                        <i class="mdi mdi-access-point me-1"></i> <?= $lang[$request->request_status] ?></div>
                    <h5 class="text-primary float-start mt-2 mx-3"><?= $type ?></h5>
                    <div class="ribbon-content">
                        <div>
                            <p class="text-muted mb-2 font-13">
                                <strong><?= $lang['user_name'] ?>:</strong>
                                <a href="/admin/users/info/<?= $request->user_id ?>">
                                <span
                                    class="ms-2"><?= $user->UserFirstName ?> <?= $user->UserLastName ?></span>
                                </a>
                            </p>
                            <p class="text-muted mb-2 font-13">
                                <strong><?= $lang['currenty_request_price'] ?>:</strong>
                                <span
                                    class="ms-2"><?= $price->$slug ?> </span>

                            </p>
                            <p class="text-muted mb-2 font-13">
                                <strong><?= $lang['request_buy_price_in_submit_time'] ?>:</strong>
                                <span
                                    class="ms-2"><?= $request->price_buy ?>  </span>
                                <?=$lang['toman']?>
                            </p>

                            <p class="text-muted mb-2 font-13">
                                <strong><?= $lang['request_sell_price_in_submit_time'] ?>:</strong>
                                <span
                                    class="ms-2"><?= $request->price_sell ?>  </span>
                                <?=$lang['toman']?>
                            </p>

                            <p class="text-muted mb-2 font-13">
                                <strong><?= $lang['request_side'] ?>:</strong>
                                <span
                                    class="ms-2"><?= $request->request_side == 'buy' ? $lang['buy'] : $lang['sell']; ?>  </span>
                            </p>
                            <p class="text-muted mb-2 font-13">
                                <strong><?= $lang['request_count'] ?>:</strong>
                                <span
                                    class="ms-2"><?= $request->request_count   ?>  </span>
                            </p>

                            <p class="text-muted mb-2 font-13">
                                <strong><?= $lang['ask_total_price'] ?>:</strong>
                                <span
                                    class="ms-2"><?= $request->request_side == 'buy' ? $request->price_buy  * $request->request_count: $request->price_sell  * $request->request_count;   ?>  </span>
                                <?=$lang['toman']?>
                            </p>

                        </div>
                    </div>
                </div>
            </div>
            <div class="timeline">

                <article class="timeline-item">
                    <div class="time-show mt-0">
                        <a href="#" class="btn btn-primary width-lg"><?= $lang['admin_description'] ?></a>
                    </div>
                </article>
                <?php
                $counter = 0;
                foreach ($descs as $desc) {
                    if ($counter % 2 == 0) {
                        ?>
                        <article class="timeline-item timeline-item">
                            <div class="timeline-desk">
                                <div class="timeline-box">
                                    <span class="arrow"></span>
                                    <span class="timeline-icon"><i class="mdi mdi-adjust"></i></span>
                                    <h4 class="mt-0 font-16 text-start"><?= Utils::getTimeByLangWithHour($desc->desc_created_at); ?></h4>
                                    <p class="mb-0 text-start">
                                        <?= $desc->desc_text ?>
                                    </p>
                                    <p class="text-muted mb-2 font-13">
                                        <strong><?= $lang['admin_name'] ?>:</strong>
                                        <span class="ms-2">
                                 <?= Security::decrypt(Admin::getAdminById($desc->admin_id)->response[0]->admin_name) ?>
                                </span>
                                    </p>
                                </div>
                            </div>
                        </article>
                        <?php
                    } else {
                        ?>
                        <article class="timeline-item timeline-item-left">
                            <div class="timeline-desk">
                                <div class="timeline-box">
                                    <span class="arrow"></span>
                                    <span class="timeline-icon"><i class="mdi mdi-adjust"></i></span>
                                    <h4 class="mt-0 font-16 text-start"><?= Utils::getTimeByLangWithHour($desc->desc_created_at); ?></h4>
                                    <p class="mb-0 text-start">
                                        <?= $desc->desc_text ?>
                                    </p>
                                    <p class="text-muted mb-2 font-13">
                                        <strong><?= $lang['admin_name'] ?>:</strong>
                                        <span class="ms-2">
                                 <?= Security::decrypt(Admin::getAdminById($desc->admin_id)->response[0]->admin_name) ?>
                                </span>
                                    </p>

                                </div>
                            </div>
                        </article>

                        <?php
                    }
                    $counter++;
                    ?>


                <?php } ?>
            </div>

            <div class="mb-3">
                <label for="admin-description" class="form-label"><?= $lang['admin_description'] ?> </label>
                <textarea class="form-control" id="admin-description" rows="5" placeholder=" "></textarea>

                <button data-request-id="<?= $_REQUEST['id'] ?>" id="submit-desciption" type="button"
                        class="btn btn-success waves-effect waves-light m-1"><i
                        class="fe-check-circle me-1"></i> <?= $lang['submit'] ?></button>

            </div>
            <div class="mb-3">
                <label class="form-label"><?= $lang['change_status'] ?> </label>
                <br>
                <button data-request-id="<?= $_REQUEST['id'] ?>" id="reject-request" type="button"
                        class="btn btn-danger waves-effect waves-light m-1"><i
                        class="fe-check-circle me-1"></i> <?= $lang['rejecting'] ?></button>


                <button data-request-id="<?= $_REQUEST['id'] ?>" id="accept-request" type="button"
                        class="btn btn-success waves-effect waves-light m-1"><i
                        class="fe-check-circle me-1"></i> <?= $lang['accept'] ?></button>

            </div>
            <!-- end custom html-->


            <input type="hidden" id="token" name="token"
                   value="<?= Security::initCSRF2() ?>">
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
?>
