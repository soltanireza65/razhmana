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
                if ($item000->slug_name == $pageSlug && $item000->permission_can_show == "yes") {
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
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');
        enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
        enqueueScript('live-price-setting', '/dist/js/admin/exchange/live-price-setting.js');
// header text
        getHeader($lang["live_price"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_show',
        ]);
        $princes = Exchange::getPricesFromLivePriceBonBast()->response;
// start roles 2

        if ($flagSlug) {
// end roles 2


            ?>

            <style>
                .accordion {
                    display: grid;
                    grid-auto-columns: 1fr;
                    gap: 10px;
                }

                .accordion-item {
                    border-radius: 14px !important;
                    border: 1px solid #c4ddff !important;
                    overflow: hidden !important;
                    box-shadow: 0 4px 5px #5e5e5e29 !important;
                    background: aliceblue !important;

                }

                .accordion-header button {
                    font-size: 15px !important;
                    font-weight: bold !important;
                    color: #303030 !important;
                }

                .arz-card-header {

                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    border-bottom: 1px solid rgba(48, 48, 48, 0.38);
                }

                .arz-card-header h4 {
                    font-weight: bold !important;
                    color: #fff !important;
                }

                .arz-card {
                    border-radius: 25px;
                    overflow: hidden;
                }

                .arz-card .card-body {
                    background: #111740 !important;
                    color: #fff !important;
                    position: relative;
                    padding-bottom: 70px;
                }

                .arz-accordion-body {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 10px;
                }

                .arz-card-up-down {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 5px;
                }

                #lowest-price {
                    color: #ff1641 !important;
                    font-size: 16px !important;
                    font-weight: 500;
                    padding-inline: 10px;
                }

                #highest-price {
                    color: #1fe380 !important;
                    font-size: 16px !important;
                    font-weight: 500;
                    padding-inline: 10px;
                }

                .arz-change-info {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .arz-price-text {
                    background: white !important;
                    padding: 7px !important;
                    border-radius: 13px !important;
                    margin-top: 10px !important ;
                    margin-bottom: 10px !important ;
                    display: flex;
                    padding-inline: 10px;
                    justify-content: space-between;
                    align-items: center;
                }
                .arz-price-text span:nth-child(1){
                    font-size: 13px !important;

                }
                .arz-price-text span:nth-child(2){
                    color: #303030 !important;
                    font-size: 20px !important;
                    font-weight: bold;
                }
                .arz-price-text span:nth-child(3){
                    font-size: 13px !important;
                }

                .mj-arz-search {
                    width: 50%;
                    position: relative;
                    /*grid-column: 2 / span 2;*/
                    margin-bottom: 15px;
                }

                .mj-arz-search:after {
                    position: absolute;
                    content: url("/dist/images/admin/pb-search.svg");
                    left: 9px;
                    top: 50%;
                    transform: translateY(-50%);
                    height: 23px;
                }

                .mj-arz-search input {
                    width: 100%;
                    height: 50px;

                    border-radius: 15px;
                    border: 1px solid #a1a1a1;
                    position: relative;
                    padding-inline: 10px 50px;
                }


                .mj-arz-search input:focus {

                    outline: unset !important;
                }

                @media screen and (min-width: 656px) {
                    .arz-accordion-body {
                        display: grid;
                        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                        gap: 10px;
                    }
                }

                @media screen and (max-width: 500px) {
                    .arz-change-info {
                        display: flex;
                        justify-content: space-between;
                        flex-direction: column;
                        align-items: start !important;
                        gap: 10px;
                    }

                    .arz-card-header {
                        display: flex;
                        justify-content: space-between;
                        flex-direction: column;
                        align-items: start !important;
                        border-bottom: 1px solid rgba(48, 48, 48, 0.38);
                    }
                }
            </style>
            <!--start custom html-->
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="mj-arz-search">
                                <input type="search" id="search-keys" placeholder="<?= $lang['search_currency'] ?>">
                            </div>
                            <div class="mb-3"><?= $lang['currency_list'] ?></div>
                      <!--      <div class="mj-default-price-value-card">

                                <div class="mj-default-price-value">

                                    <div class="mj-filter-switcher">
                                        <input type="checkbox" id="switch"
                                               name="filter-switch2">
                                        <label for="switch">Toggle</label>
                                    </div>
                                    <div class="mj-default-price-calue-title">قیمت های پیشفرض</div>

                                </div>
                                <div class="mj-default-price-inputs">
                                    <div id="arz-normal-input" class="mj-arz-inputs-setting ">

                                        <div id="normal" class="mj-arz-plus-minus-default">
                                            <div class="position-relative mj-arz-default-input ">
                                                <input type="number" class="plus-arz " disabled

                                                       value="<?php /*= isset($price->plus_value) ? $price->plus_value : 0 */?>">
                                                <span class="mj-arz-input-label"><?php /*= $lang['buy'] */?> </span>
                                            </div>
                                            <div class="position-relative mj-arz-default-input ">
                                                <input type="number" class="minus-arz " disabled
                                                       value="<?php /*= isset($price->mines_value) ? $price->mines_value : 0 */?>">
                                                <span class="mj-arz-input-label"><?php /*= $lang['sell'] */?> </span>
                                            </div>
                                            <button class="btn btn-primary mj-default-price-submit  " disabled>
                                                <?php /*= $lang['submit'] */?>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>-->
                            <div class="accordion-body arz-accordion-body">

                                <?php
                                $language = $_COOKIE['language'];
                                $slug = 'title_' . $language  ;
                                foreach ($princes as $price) {

                                    ?>
                                    <div class="card arz-card">
                                        <div class="card-body bg-white mj-arz-card-main">
                                            <div class="arz-card-header">
                                                <h4 class="mt-0 "><?= $price->$slug ?></h4>
                                                <div class="arz-last-change">
                                                    <span class="d-block"><?= $lang['last_change'] ?>:</span>

                                                    <span><?= Utils::getTimeByLangWithHour($price->updated_at) ?></span>


                                                </div>
                                            </div>

                                            <h2 class="text-primary my-3 text-center arz-price-text">
                                                <span><?=$lang['buy']?></span>

                                                <span
                                                    data-plugin="counterup"><?= number_format($price->price_buy) ?>
                                                </span>
                                                <span>
                                                    <?= $lang['toman'] ?>
                                                </span>
                                            </h2>
                                            <h2 class="text-primary my-3 text-center arz-price-text">
                                                <span>
                                                    <?=$lang['sell']?>
                                                </span>

                                                <span
                                                    data-plugin="counterup"><?= number_format($price->price_sell) ?>
                                                </span>
                                                <span>
                                                    <?= $lang['toman'] ?>
                                                </span>
                                            </h2>
<!--                                            <div class="arz-change-info mb-0">-->
<!--                                                        <span class="float-end">-->
<!--                                                            <span class="d-block">-->
<!--                                                                <i class="fa fa-sun text-success me-1">-->
<!--                                                                </i>-->
<!--                                                                --><?php //=$lang['buy']?><!-- :-->
<!--                                                            </span>-->
<!--                                                             <span-->
<!--                                                                 id="lowest-price">--><?php //= number_format($price->price_buy) ?><!--</span><span-->
<!--                                                                id="">--><?php //= $lang['toman'] ?><!--</span>-->
<!--                                                        </span>-->
<!---->
<!--                                                <div class="arz-card-up-down">-->
<!--                                                            <span> --><?php //=$lang['sell']?><!-- : <span-->
<!--                                                                    id="lowest-price">--><?php //= number_format($price->price_sell) ?><!--</span><span-->
<!--                                                                    id="">--><?php //= $lang['toman'] ?><!--</span></span>-->
<!--                                                </div>-->
<!--                                            </div>-->

                                            <div class="mj-arz-active-inactive mt-2">

                                                <div class="mj-status-input">
                                                    <input type="checkbox" id="arz-normal-<?= $price->id ?>"
                                                           data-arz-input="arz-normal-<?= $price->id ?>"
                                                           name="ir-arz-status-<?= $price->id ?>" <?= $price->ir_order_status == 'active' ? 'checked' : '' ?> >
                                                    <label for="arz-normal-<?= $price->id ?>"
                                                           data-label-id="arz-normal-<?= $price->id ?>">
                                                            <?=$lang['ir_order_exchange']?>
                                                    </label>
                                                </div>
                                                <div class="mj-status-input">
                                                    <input type="checkbox" id="russia-normal-<?= $price->id ?>"
                                                           data-arz-input="russia-normal-<?= $price->id ?>"
                                                           name="ru-arz-status-<?= $price->id ?>" <?= $price->ru_order_status == 'active' ? 'checked' : '' ?> >
                                                    <label for="russia-normal-<?= $price->id ?>"
                                                           data-label-id="russia-normal-<?= $price->id ?>">
                                                        <?=$lang['ru_order_exchange']?>
                                                    </label>
                                                </div>
                                                <div class="mj-status-input">
                                                    <input type="checkbox" id="emarat-normal-<?= $price->id ?>"
                                                           data-arz-input="emarat-normal-<?= $price->id ?>"
                                                           name="du-arz-status-<?= $price->id ?>" <?= $price->du_order_status == 'active' ? 'checked' : '' ?> >
                                                    <label for="emarat-normal-<?= $price->id ?>"
                                                           data-label-id="emarat-normal-<?= $price->id ?>">

                                                        <?=$lang['du_order_exchange']?>
                                                    </label>
                                                </div>
                                                <div class="mj-status-input">
                                                    <input type="checkbox" id="turkey-normal-<?= $price->id ?>"
                                                           data-arz-input="turkey-normal-<?= $price->id ?>"
                                                           name="tr-arz-status-<?= $price->id ?>" <?= $price->tr_order_status == 'active' ? 'checked' : '' ?> >
                                                    <label for="turkey-normal-<?= $price->id ?>"
                                                           data-label-id="turkey-normal-<?= $price->id ?>">
                                                        <?=$lang['tr_order_exchange']?>

                                                    </label>
                                                </div>
                                            </div>
                                            <div id="arz-normal-input" class="mj-arz-inputs-setting "
                                                 style="<?= $price->ir_order_status == 'active' ? '' : 'display:none;' ?>"
                                                 data-input-id="arz-normal-<?= $price->id ?>">
                                                <div>  <?=$lang['ir_order_exchange']?></div>
                                                <div id="normal" class="mj-arz-plus-minus">
                                                    <div class="position-relative">
                                                        <input type="number" class="ir-plus-arz plus-arz"
                                                               data-price-id="<?= $price->id ?>"
                                                               value="<?= isset($price->ir_plus_value) ? $price->ir_plus_value : 0 ?>">
                                                        <span class="mj-arz-input-label"><?=$lang['buy']?> </span>
                                                    </div>
                                                    <div class="position-relative">
                                                        <input type="number" class="ir-minus-arz minus-arz"
                                                               data-price-id="<?= $price->id ?>"
                                                               value="<?= isset($price->ir_mines_value) ? $price->ir_mines_value : 0 ?>">
                                                        <span class="mj-arz-input-label"><?=$lang['sell']?> </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="arz-russia-input" class="mj-arz-inputs-setting "
                                                 style="<?= $price->ru_order_status == 'active' ? '' : 'display:none;' ?>"
                                                 data-input-id="russia-normal-<?= $price->id ?>">
                                                <div>      <?=$lang['ru_order_exchange']?>  </div>
                                                <div id="normal" class="mj-arz-plus-minus">
                                                    <div class="position-relative">
                                                        <input type="number" class="ru-plus-arz plus-arz"
                                                               data-price-id="<?= $price->id ?>"
                                                               value="<?= isset($price->ru_plus_value) ? $price->ru_plus_value : 0 ?>">
                                                        <span class="mj-arz-input-label"><?=$lang['buy']?> </span>
                                                    </div>
                                                    <div class="position-relative">
                                                        <input type="number" class="ru-minus-arz minus-arz"
                                                               data-price-id="<?= $price->id ?>"
                                                               value="<?= isset($price->ru_mines_value) ? $price->ru_mines_value : 0 ?>">
                                                        <span class="mj-arz-input-label"><?=$lang['sell']?> </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="arz-emarat-input" class="mj-arz-inputs-setting "
                                                 style="<?= $price->du_order_status == 'active' ? '' : 'display:none;' ?>"
                                                 data-input-id="emarat-normal-<?= $price->id ?>">
                                                <div>       <?=$lang['du_order_exchange']?>  </div>
                                                <div id="normal" class="mj-arz-plus-minus">
                                                    <div class="position-relative">
                                                        <input type="number" class="du-plus-arz plus-arz"
                                                               data-price-id="<?= $price->id ?>"
                                                               value="<?= isset($price->du_plus_value) ? $price->du_plus_value : 0 ?>">
                                                        <span class="mj-arz-input-label"><?=$lang['buy']?> </span>
                                                    </div>
                                                    <div class="position-relative">
                                                        <input type="number" class="du-minus-arz minus-arz"
                                                               data-price-id="<?= $price->id ?>"
                                                               value="<?= isset($price->du_mines_value) ? $price->du_mines_value : 0 ?>">
                                                        <span class="mj-arz-input-label"><?=$lang['sell']?> </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="arz-turkey-input" class="mj-arz-inputs-setting "
                                                 style="<?= $price->tr_order_status == 'active' ? '' : 'display:none;' ?>"
                                                 data-input-id="turkey-normal-<?= $price->id ?>">
                                                <div>      <?=$lang['tr_order_exchange']?>  </div>
                                                <div id="normal" class="mj-arz-plus-minus">
                                                    <div class="position-relative">
                                                        <input type="number" class="tr-plus-arz plus-arz"
                                                               data-price-id="<?= $price->id ?>"
                                                               value="<?= isset($price->tr_plus_value) ? $price->tr_plus_value : 0 ?>">
                                                        <span class="mj-arz-input-label"><?=$lang['buy']?> </span>
                                                    </div>
                                                    <div class="position-relative">
                                                        <input type="number" class="tr-minus-arz minus-arz"
                                                               data-price-id="<?= $price->id ?>"
                                                               value="<?= isset($price->tr_mines_value) ? $price->tr_mines_value : 0 ?>">
                                                        <span class="mj-arz-input-label"><?=$lang['sell']?> </span>
                                                    </div>
                                                </div>
                                            </div>


                                            <button class="btn btn-primary mj-arz-submit"
                                                    data-price-id="<?= $price->id ?>"><?= $lang['submit'] ?></button>
                                        </div>

                                    </div>

                                <?php } ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end custom html-->


            <input type="hidden" id="token" name="token"
                   value="<?= $_SESSION['dt-cv'] = "dt-cv-44"; ?>">

            <input type="hidden" id="token-price" name="token-price"
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
<script>
    $(document).ready(function () {
        $('#search-keys').on('keyup', function () {
            var searchText = $(this).val().toLowerCase();
            $('.accordion-body.arz-accordion-body .arz-card').hide().filter(function () {
                return $(this).text().toLowerCase().indexOf(searchText) > -1;
            }).show();
        });
    });
</script>
