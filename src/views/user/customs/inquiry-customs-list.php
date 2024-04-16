<?php

global $lang;

use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    include_once getcwd() . '/views/user/header-footer.php';

    enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
    enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');

    enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
    enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
    enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
    enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');

    enqueueScript('datatables-init', '/dist/js/user/data-tables.init.js');

    getHeader($lang['u_inquiry_customs_list']);

    $user = User::getUserInfo();
    ?>
    <main class="container" style="padding-bottom: 180px;">
        <style>
            .mj-backbtn{
                display: none !important;
            }
        </style>
        <div class="row">
            <div class="col-12">
                <div class="card mj-card">
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="mj-d-icon-title d-block mj-fw-700 mj-font-13 mb-1"><?= $lang['u_inquiry_customs_list'] ?></span>
                            <p class="mj-d-cargo-item-desc mb-0">
                                <span>
                                    <img src="/dist/images/icons/circle-exclamation-gray.svg" class="me-1"
                                         alt="exclamation"/>
                                </span>
                                <?= $lang['u_inquiry_customs_list_sub_title'] ?>
                            </p>
                        </div>

                        <div class="row align-items-center mb-1">
                            <div class="col">
                                <div class="mj-input-filter-box">
                                    <input type="text" class="mj-input-filter" id="request-search" name="request-search"
                                           placeholder="<?= $lang['u_inquiry_customs_list_search_place_holder'] ?>">
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
                                        <?= $lang['u_filter'] ?>
                                    </button>

                                    <div class="dropdown-menu mj-dropdown-menu">
                                        <div class="mb-2">
                                            <span class="mj-d-icon-title d-block mj-fw-700 mj-font-13 mb-1"><?= $lang['d_filter_title'] ?></span>
                                            <p class="mj-d-cargo-item-desc mb-0">
                                                <?= $lang['d_filter_sub_title'] ?>
                                            </p>
                                        </div>
                                        <!-- start filter -->
                                        <div class="row">
                                            <div class="col-6">
                                                <a href="/user/customs/inquiry-list"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-all"></span>
                                                    <span style="padding-right: 3px;"><?= $lang['u_inquiry_customs_all'] ?></span>
                                                </a>
                                            </div>

                                            <div class="col-6">
                                                <a href="/user/customs/inquiry-list/completed"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-success"
                                                          style="background-color: #00e517"></span>
                                                    <span style="padding-right: 3px;"><?= $lang['u_complete_inquiry_2'] ?></span>
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="/user/customs/inquiry-list/process"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-primary"
                                                          style="background-color: #ff1d1d"></span>
                                                    <span style="padding-right: 3px;"><?= $lang['u_process_inquiry_2'] ?></span>
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="/user/customs/inquiry-list/pending"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-warning"
                                                          style="background-color: #ff1d1d"></span>
                                                    <span style="padding-right: 3px;"><?= $lang['u_pending_inquiry_2'] ?></span>
                                                </a>
                                            </div>
                                            <div class="col-6">
                                                <a href="/user/customs/inquiry-list/read"
                                                   class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                    <span class="mj-badge mj-badge-read"
                                                          style="background-color: #ff1d1d"></span>
                                                    <span style="padding-right: 3px;"><?= $lang['u_read_inquiry_filter'] ?></span>
                                                </a>
                                            </div>

                                        </div>
                                        <!-- end filter -->
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
                                    <th><?= $lang['u_inquery_customs_table_title'] ?></th>
                                    <th><?= $lang['d_my_request_table_status'] ?></th>
                                    <th><?= $lang['d_table_action'] ?></th>
                                </tr>
                                </thead>
                                <tbody id="requests-list">
                                <?php
                                $status = (isset($_REQUEST['status']) && in_array($_REQUEST['status'], ['completed', 'pending', 'process','read'])) ? $_REQUEST['status'] : 'all';
                                $records = Customs::inquiryCustomsList($user->UserId, $status);
                                if (!empty($records->response)) {
                                    $counter=1;
                                    foreach ($records->response as $key => $item) {
                                        ?>
                                        <tr class="align-middle">
                                            <td><?= $counter ?></td>
                                            <td><?=$lang['u_inquery_customs_name_prefix']?> <?= $item->freight_id ?></td>
                                            <td>
                                                <div class="text-center">
                                                    <span class="mj-badge <?php
                                                    if ($item->freight_status == 'pending') {
                                                        echo 'mj-badge-warning';
                                                    } elseif ($item->freight_status == 'completed') {
                                                        echo 'mj-badge-success';
                                                    } elseif ($item->freight_status == 'process') {
                                                        echo 'mj-badge-primary';
                                                    }elseif ($item->freight_status == 'read') {
                                                        echo 'mj-badge-read';
                                                    }
                                                    ?>"></span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="/user/customs/inquiry-detail/<?= $item->freight_id ?>"
                                                   class="mj-btn-more">
                                                    <?= $lang['d_button_detail'] ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                        $counter++;
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                            <script type="text/javascript">
                                const status = '<?= $status ?>';
                            </script>
                        </div>

                    </div>
                </div>

                <div class="card mj-card">
                    <div class="card-body">
                        <h4 class="mj-d-icon-title d-block mj-fw-700 mj-font-13 mt-0 mb-3"><?= $lang['d_table_help'] ?></h4>
                        <div class="row">
                            <div class="col-6">
                                <span class="mj-badge mj-badge-all">
                                    <?= $lang['u_inquiry_customs_all'] ?>
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="mj-badge mj-badge-success">
                                    <?= $lang['u_complete_inquiry_2'] ?>
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="mj-badge mj-badge-primary">
                                     <?= $lang['u_process_inquiry_2'] ?>
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="mj-badge mj-badge-warning">
                                    <?= $lang['u_pending_inquiry_2'] ?>
                                </span>
                            </div>
                            <div class="col-6">
                                <span class="mj-badge mj-badge-read">
                                    <?= $lang['u_read_inquiry_filter'] ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <?php
    getFooter();
} else {
    header('location: /login');
}