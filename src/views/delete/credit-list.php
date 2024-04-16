<?php

global $lang;

use MJ\Security\Security;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();
    include_once 'header-footer.php';

    enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
    enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');

    enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
    enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
    enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
    enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
    enqueueScript('credit-cards-init', '/dist/js/user/credit-cards.init.js');

    getHeader($lang['d_credit_cards_title']);

    ?>

    <main class="container" style="padding-bottom: 180px;">
        <input type="hidden" id="token" name="token" value="<?= Security::initCSRF('delete-card') ?>">
        <template id="template-card-detail">
            <div class="modal fade" id="card-detail-modal" aria-hidden="true" aria-labelledby="card-detail"
                 tabindex="-1">
                <div class="modal-dialog modal-dialog-centered width-100">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-center" id="card-detail"><?= $lang['credit_card_detail'] ?></h5>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mj-credit-detail-modal d-flex justify-content-between">
                                <span><?= $lang['bank_card_number'] ?> :</span>
                                <span dir="ltr">#NUMBER#</span>
                            </div>
                            <div class="mj-credit-detail-modal d-flex justify-content-between">
                                <span><?= $lang['bank_account'] ?> :</span>
                                <span dir="ltr">#ACCOUNT#</span>
                            </div>
                            <div class="mj-credit-detail-modal d-flex justify-content-between">
                                <span><?= $lang['bank_iban'] ?> :</span>
                                <span dir="ltr">#IBAN#</span>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button class="mj-btn-more mj-btn-cancel px-4" id="delete-credit-card">
                                <?= $lang['inactivate'] ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template id="template-verify-delete">
            <div class="modal fade" id="delete-card-modal" aria-hidden="true"
                 aria-labelledby="delete-card"
                 tabindex="-1">
                <div class="modal-dialog modal-dialog-centered width-100">
                    <div class="modal-content text-center">
                        <div class="modal-header text-center">
                            <h5 class="modal-title" id="delete-card"><?= $lang['verifying'] ?></h5>
                            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <?= $lang['credit_card_delete_message'] ?>
                        </div>

                        <div class="modal-footer justify-content-center">
                            <button class="mj-btn-more mj-btn-cancel-yes px-4" data-verify-delete data-card="#CARD#">
                                <?= $lang['d_btn_yes'] ?>
                            </button>
                            <button class="mj-btn-more mj-btn-cancel px-4" data-bs-dismiss="modal">
                                <?= $lang['d_btn_close'] ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <div class="row">
            <div class="col-12 mt-3">
                <div class="card mj-card">
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="mj-b-icon-title d-block mj-fw-700 mj-font-13 mb-1">
                                <?= $lang['credit_cards_list'] ?>
                            </span>
                            <p class="mj-b-cargo-item-desc mb-0">
                                <span>
                                    <img src="/dist/images/icons/circle-exclamation-gray.svg" class="me-1"
                                         alt="exclamation"/>
                                </span>
                                <?= $lang['credit_cards_list_sub_title'] ?>
                            </p>
                        </div>

                        <div class="row align-items-center mb-1">
                            <div class="col">
                                <div class="mj-input-filter-box">
                                    <input type="text" class="mj-input-filter" id="card-search" name="card-search"
                                           placeholder="<?= $lang['credit_cards_search_placeholder'] ?>">
                                    <label for="card-search" class="mj-input-filter-search">
                                        <img src="/dist/images/icons/search.svg" alt="search"/>
                                    </label>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="dropdown">
                                    <button type="button" class="mj-btn-filter" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        <img src="/dist/images/icons/sliders.svg" class="me-1" alt="">
                                        <?= $lang['d_filter'] ?>
                                    </button>

                                    <div class="dropdown-menu mj-dropdown-menu">
                                        <div class="mb-2">
                                            <span class="mj-b-icon-title d-block mj-fw-700 mj-font-13 mb-1"><?= $lang['d_filter_title'] ?></span>
                                            <p class="mj-b-cargo-item-desc mb-0">
                                                <?= $lang['d_filter_sub_title'] ?>
                                            </p>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <a href="/user/credit-cards"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-filter-color me-2"
                                                          style="background-color: #0384e8"></span>
                                                    <span><?= $lang['d_filter_all'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/user/credit-cards/accepted"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-filter-color me-2"
                                                          style="background-color: #00e517"></span>
                                                    <span><?= $lang['d_filter_active'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/user/credit-cards/rejected"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-filter-color me-2"
                                                          style="background-color: #ff1d1d"></span>
                                                    <span><?= $lang['d_filter_rejected'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/user/credit-cards/pending"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-filter-color me-2"
                                                          style="background-color: #ffc700"></span>
                                                    <span><?= $lang['d_filter_pending'] ?></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive hidden-search">
                            <table id="credit-cards-table"
                                   class="table mj-table mj-table-bordered mj-table-rounded mj-table-stripped mj-table-row-number w-100">
                                <thead class="text-nowrap">
                                <tr>
                                    <th>#</th>
                                    <th><?= $lang['bank_account'] ?></th>
                                    <th><?= $lang['d_my_request_table_status'] ?></th>
                                    <th class="text-center"><?= $lang['d_table_action'] ?></th>
                                </tr>
                                </thead>
                                <tbody id="credit-cards-list">
                                <?php
                                $status = (isset($_REQUEST['status']) && in_array($_REQUEST['status'], ['accepted', 'pending', 'rejected'])) ? $_REQUEST['status'] : 'all';
                                $records = User::getCreditCardsList($user->UserId, $status);
                                foreach ($records->response as $key => $item) {
                                    ?>
                                    <tr class="align-middle">
                                        <td><?= $key + 1 ?></td>
                                        <td>
                                            <span dir="ltr"><?= $item->CardAccountNumber ?></span>
                                        </td>
                                        <td>
                                            <div class="text-center">
                                                <span class="mj-trx-<?php
                                                if ($item->CardStatus == 'pending') {
                                                    echo 'pending';
                                                } elseif ($item->CardStatus == 'accepted') {
                                                    echo 'complete';
                                                }else {
                                                    echo 'reject';
                                                }
                                                ?>"></span>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0);" class="mj-btn-more"
                                               onclick="cardDetail($(this))"
                                               data-number="<?= $item->CardNumber ?>" data-iban="<?= $item->CardIBAN ?>"
                                               data-account="<?= $item->CardAccountNumber ?>"
                                               data-card=" <?= $item->CardId ?>"
                                               data-credit-detail
                                               role="button">
                                                <?= $lang['d_button_detail'] ?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                            <script type="text/javascript">
                                const status = '<?= $status ?>';
                            </script>
                        </div>

                        <?php
                        if (count($records->response) > 0) {
                            ?>
                            <div class="text-center mt-3">
                                <button type="button" class="mj-btn-more mj-fw-400 mj-font-12 px-5 py-2 mx-auto"
                                        data-load-more data-page="1">
                                    <?= $lang['d_button_load_more'] ?>
                                </button>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <div class="card mj-card">
                    <div class="card-body">
                        <h4 class="mj-b-icon-title d-block mj-fw-700 mj-font-13 mt-0 mb-3">
                            <?= $lang['d_table_help'] ?>
                        </h4>

                        <div class="row">
                            <div class="col-6">
                                <span class="mj-trx-complete">
                                    <?= $lang['d_filter_active'] ?>
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="mj-trx-reject">
                                    <?= $lang['d_filter_rejected'] ?>
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="mj-trx-pending">
                                    <?= $lang['d_filter_pending'] ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php

    getFooter('', false);
} else {
    header('location: /login');
}