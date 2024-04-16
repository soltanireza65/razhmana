<?php

global $lang;

use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    $user = User::getUserInfo();
    include_once 'header-footer.php';

    enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
    enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');

    enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
    enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
    enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
    enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
    enqueueScript('menu-init-js', '/dist/js/driver/dashboard.init.js');
    enqueueScript('transactions-init', '/dist/js/driver/transactions.init.js');

    getHeader($lang['d_transactions_title']);

    ?>
    <main class="container" style="padding-bottom: 180px;">
        <div class="row">
            <div class="col-12 mt-3">
                <div class="card mj-card">
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="mj-b-icon-title d-block mj-fw-700 mj-font-13 mb-1">
                                <?= $lang['d_transactions_title'] ?>
                            </span>
                        </div>

                        <div class="row align-items-center mb-1">
                            <div class="col">
                                <div class="mj-input-filter-box">
                                    <input type="text" class="mj-input-filter" id="tx-search" name="tx-search"
                                           placeholder="<?= $lang['transaction_list_search_placeholder'] ?>">
                                    <label for="tx-search" class="mj-input-filter-search">
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
                                                <a href="/driver/transactions"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-filter-color me-2"
                                                          style="background-color: #0384e8"></span>
                                                    <span><?= $lang['d_filter_all'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/driver/transactions/completed"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-filter-color me-2"
                                                          style="background-color: #00e517"></span>
                                                    <span><?= $lang['d_filter_completed'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/driver/transactions/rejected"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-filter-color me-2"
                                                          style="background-color: #ff1d1d"></span>
                                                    <span><?= $lang['d_filter_rejected'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/driver/transactions/pending"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-filter-color me-2"
                                                          style="background-color: #ffc700"></span>
                                                    <span><?= $lang['d_filter_pending'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/driver/transactions/expired"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-filter-color me-2"
                                                          style="background-color: #3d3d3d"></span>
                                                    <span><?= $lang['d_filter_expired'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/driver/transactions/unpaid"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-filter-color me-2"
                                                          style="background-color: #9f03e8"></span>
                                                    <span><?= $lang['d_filter_unpaid'] ?></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive hidden-search">
                            <table id="transactions-table"
                                   class="table mj-table mj-table-bordered mj-table-rounded mj-table-stripped mj-table-row-number w-100">
                                <thead class="text-nowrap">
                                <tr>
                                    <th>#</th>
                                    <th><?= $lang['transaction_list_amount'] ?></th>
                                    <th><?= $lang['transaction_list_date'] ?></th>
                                    <th><?= $lang['transaction_list_status'] ?></th>
                                    <th class="text-center"><?= $lang['d_table_action'] ?></th>
                                </tr>
                                </thead>
                                <tbody id="transactions-list">
                                <?php
                                $status = (isset($_REQUEST['status']) && in_array($_REQUEST['status'], ['completed', 'pending', 'unpaid', 'rejected', 'expired'])) ? $_REQUEST['status'] : 'all';
                                $records = User::getTransactionsList($user->UserId, $status);
                                foreach ($records->response as $key => $item) {
                                    ?>
                                    <tr class="align-middle">
                                        <td><?= $key + 1 ?></td>
                                        <td>
                                            <?= number_format($item->TransactionAmount) . " {$item->TransactionCurrency}" ?>
                                        </td>
                                        <td><?= ($_COOKIE['language'] == 'fa_IR') ? Utils::jDate('Y/m/d', $item->TransactionTime) : date('Y-m-d', $item->TransactionTime) ?></td>
                                        <td>
                                            <div class="text-center">
                                                <span class="mj-badge <?php
                                                if (in_array($item->TransactionStatus, ['completed', 'paid'])) {
                                                    echo 'mj-badge-success';
                                                } elseif (in_array($item->TransactionStatus, ['pending', 'pending_deposit'])) {
                                                    echo 'mj-badge-warning';
                                                } elseif (in_array($item->TransactionStatus, ['rejected', 'rejected_deposit'])) {
                                                    echo 'mj-badge-danger';
                                                } elseif ($item->TransactionStatus == 'expired') {
                                                    echo 'mj-badge-dark';
                                                } elseif ($item->TransactionStatus == 'unpaid') {
                                                    echo 'mj-badge-purple';
                                                } else {
                                                    echo 'mj-badge-primary';
                                                }
                                                ?>"></span>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="/driver/tx/<?= $item->TransactionId ?>" class="mj-btn-more">
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
                        <h4 class="mj-b-icon-title d-block mj-fw-700 mj-font-13 mt-0 mb-3">راهنمای جدول</h4>

                        <div class="row">
                            <div class="col-6">
                                <span class="mj-badge mj-badge-success">
                                    <?= $lang['d_filter_completed'] . ' / ' . $lang['d_filter_paid'] ?>
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="mj-badge mj-badge-danger">
                                    <?= $lang['d_filter_rejected'] ?>
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="mj-badge mj-badge-warning">
                                    <?= $lang['d_filter_pending'] ?>
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="mj-badge mj-badge-dark">
                                    <?= $lang['d_filter_expired'] ?>
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="mj-badge mj-badge-purple">
                                    <?= $lang['d_filter_unpaid'] ?>
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