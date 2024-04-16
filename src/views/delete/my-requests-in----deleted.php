<?php

global $lang;

use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    include_once 'header-footer.php';

    enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
    enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');

    enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
    enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
    enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
    enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
    enqueueScript('my-requests-init', '/dist/js/driver/my-requests.init.js');

    getHeader($lang['d_my_requests_title']);

    $user = User::getUserInfo();

    ?>
    <main class="container" style="padding-bottom: 180px;">


        <div class="row">
            <div class="col-12">
                <div class="card mj-card">
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="mj-d-icon-title d-block mj-fw-700 mj-font-13 mb-1"><?= $lang['d_my_requests_list'] ?></span>
                            <p class="mj-d-cargo-item-desc mb-0">
                                <span>
                                    <img src="/dist/images/icons/circle-exclamation-gray.svg" class="me-1"
                                         alt="exclamation"/>
                                </span>
                                <?= $lang['d_my_requests_list_sub_title'] ?>
                            </p>
                        </div>

                        <div class="row align-items-center mb-1">
                            <div class="col">
                                <div class="mj-input-filter-box">
                                    <input type="text" class="mj-input-filter" id="request-search" name="request-search"
                                           placeholder="<?= $lang['d_my_request_search_placeholder'] ?>">
                                    <label for="request-search" class="mj-input-filter-search">
                                        <img src="/dist/images/icons/search.svg" alt="search"/>
                                    </label>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="dropdown">
                                    <button type="button" class="mj-btn-filter" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        <img src="/dist/images/icons/sliders.svg" class="me-1" alt="filter">
                                        <?= $lang['d_filter'] ?>
                                    </button>

                                    <div class="dropdown-menu mj-dropdown-menu">
                                        <div class="mb-2">
                                            <span class="mj-d-icon-title d-block mj-fw-700 mj-font-13 mb-1"><?= $lang['d_filter_title'] ?></span>
                                            <p class="mj-d-cargo-item-desc mb-0">
                                                <?= $lang['d_filter_sub_title'] ?>
                                            </p>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <a href="/driver/my-requests-in"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-all"></span>
                                                    <span style="padding-right: 3px;"><?= $lang['d_filter_all'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/driver/my-requests-in/pending"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-warning"
                                                          style="background-color: #ffc700"></span>
                                                    <span style="padding-right: 3px;"><?= $lang['d_filter_pending'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/driver/my-requests-in/accepted"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-primary"
                                                          style="background-color: #3d3d3d"></span>
                                                    <span style="padding-right: 3px;"><?= $lang['d_filter_accepted'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/driver/my-requests-in/progress"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-purple"
                                                          style="background-color: #9f03e8"></span>
                                                    <span style="padding-right: 3px;"><?= $lang['d_filter_progress'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/driver/my-requests-in/completed"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-success"
                                                          style="background-color: #00e517"></span>
                                                    <span style="padding-right: 3px;"><?= $lang['d_filter_completed'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/driver/my-requests-in/expired"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-dark"
                                                          style="background-color: #ffc700"></span>
                                                    <span style="padding-right: 3px;"><?= $lang['d_filter_expired'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/driver/my-requests-in/rejected"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-danger"
                                                          style="background-color: #ff1d1d"></span>
                                                    <span style="padding-right: 3px;"><?= $lang['d_filter_rejected'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/driver/my-requests-in/canceled"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-cancel"
                                                          style="background-color: #ffc700"></span>
                                                    <span style="padding-right: 3px;"><?= $lang['d_filter_canceled'] ?></span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive hidden-search">
                            <table id="requests-table"
                                   class="table mj-table mj-table-bordered mj-table-rounded mj-table-stripped mj-table-row-number w-100">
                                <thead class="text-nowrap">
                                <tr>
                                    <th>#</th>
                                    <th><?= $lang['d_my_request_table_cargo_title'] ?></th>
                                    <th><?= $lang['d_my_request_price'] ?></th>
                                    <th><?= $lang['d_my_request_table_status'] ?></th>
                                    <th><?= $lang['d_table_action'] ?></th>
                                </tr>
                                </thead>
                                <tbody id="requests-list">
                                <?php
                                $status = (isset($_REQUEST['status']) && in_array($_REQUEST['status'], ['completed', 'pending', 'accepted', 'rejected', 'progress', 'canceled' ,'expired'])) ? $_REQUEST['status'] : 'all';
                                $records = Driver::getMyRequestsInList($user->UserId, $status);
                                if (!empty($records->response)) {
                                    foreach ($records->response as $key => $item) {
                                        ?>
                                        <tr class="align-middle">
                                            <td><?= $key + 1 ?></td>
                                            <td><?= $item->CargoName ?></td>
                                            <td><?= number_format($item->RequestPrice) . " " . $item->CurrencyName; ?></td>
                                            <td>
                                                <div class="text-center">
                                                    <span class="mj-badge <?php
                                                    if ($item->RequestStatus == 'pending') {
                                                        echo 'mj-badge-warning';
                                                    }  elseif ($item->RequestStatus == 'accepted') {
                                                        echo 'mj-badge-primary';
                                                    }elseif ($item->RequestStatus == 'progress') {
                                                        echo 'mj-badge-purple';
                                                    } elseif ($item->RequestStatus == 'completed') {
                                                        echo 'mj-badge-success';
                                                    } elseif ($item->RequestStatus == 'expired') {
                                                        echo 'mj-badge-dark';
                                                    }  elseif ($item->RequestStatus == 'rejected') {
                                                        echo 'mj-badge-danger';
                                                    } elseif ($item->RequestStatus == 'canceled') {
                                                        echo 'mj-badge-cancel';
                                                    }else {
                                                        echo 'mj-badge-primary';
                                                    }
                                                    ?>"></span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="/driver/cargo-in/<?= $item->CargoId ?>" class="mj-btn-more">
                                                    <?= $lang['d_button_detail'] ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
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
                        <h4 class="mj-d-icon-title d-block mj-fw-700 mj-font-13 mt-0 mb-3"><?= $lang['d_table_help'] ?></h4>

                        <div class="row">
                            <div class="col-6">
                                <span class="mj-badge mj-badge-all">
                                    <?= $lang['d_filter_all'] ?>
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="mj-badge mj-badge-warning">
                                    <?= $lang['d_filter_pending'] ?>
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="mj-badge mj-badge-primary">
                                     <?= $lang['d_filter_accepted'] ?>
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="mj-badge mj-badge-purple">
                                    <?= $lang['d_filter_progress'] ?>
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="mj-badge mj-badge-success">
                                    <?= $lang['d_filter_completed'] ?>
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="mj-badge mj-badge-dark">
                                    <?= $lang['d_filter_expired'] ?>
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="mj-badge mj-badge-danger">
                                    <?= $lang['d_filter_rejected'] ?>
                                </span>
                            </div>

                            <div class="col-6">
                                <span class="mj-badge mj-badge-cancel">
                                    <?= $lang['d_filter_canceled'] ?>
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