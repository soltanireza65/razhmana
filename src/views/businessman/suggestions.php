<?php

global $lang;

use MJ\Router\Router;
use MJ\Security\Security;

if (User::userIsLoggedIn()) {
    User::checkUserSlugAccess();
    $user = User::getUserInfo();
    $sort = (isset($_REQUEST['sort']) && in_array($_REQUEST['sort'], ['asc', 'desc'])) ? $_REQUEST['sort'] : 'asc';
    $requests = Businessman::getCargoRequests($user->UserId, $_REQUEST['id'], $sort);

    $cargo = Businessman::getCargoDetail($user->UserId, $_REQUEST['id']);

    if ($requests->status == 200) {
        $cargo = $cargo->response;
        $requests = $requests->response;

        include_once 'header-footer.php';

        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');

        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('suggestions-init', '/dist/js/businessman/suggestions.init.js');

        getHeader($lang['b_title_suggestions']);

        ?>

        <main>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="card mj-card">
                            <div class="card-body">
                                <div class="mb-3">
                                    <span class="mj-b-icon-title d-block mj-fw-700 mj-font-13 mb-1"><?= $lang['b_suggestions_list'] ?></span>
                                    <p class="mj-b-cargo-item-desc mb-0">
                                <span>
                                    <img src="/dist/images/icons/circle-exclamation-gray.svg" class="me-1" alt=""/>
                                </span>
                                        <?= $lang['b_suggestions_list_desc'] ?>
                                    </p>
                                </div>

                                <div class="row align-items-center mb-1">
                                    <div class="col">
                                        <div class="mj-input-filter-box">
                                            <input type="text" class="mj-input-filter" id="request-search"
                                                   name="request-search"
                                                   placeholder="<?= $lang['b_mycargo_my_cargo_search_placeholder'] ?>">
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
                                                <?= $lang['b_filter'] ?>
                                            </button>

                                            <div class="dropdown-menu mj-dropdown-menu mj-b-dropdown-menu">
                                                <div class="mb-2">
                                                    <span class="mj-b-icon-title d-block mj-fw-700 mj-font-13 mb-1"> <?= $lang['b_filter'] ?></span>

                                                </div>

                                                <div class="row">
                                                    <div class="col-6">
                                                        <a href="/businessman/suggestions/<?= $_REQUEST['id'] ?>"
                                                           class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                            <img class="me-2"
                                                                 src="/dist/images/icons/arrow-down-9-1.svg"
                                                                 alt="asc">
                                                            <span><?= $lang['b_filter_asc'] ?></span>
                                                        </a>
                                                    </div>

                                                    <div class="col-6">
                                                        <a href="/businessman/suggestions/<?= $_REQUEST['id'] ?>/desc"
                                                           class="d-flex align-items-center mj-btn-filter-link mb-2">
                                                            <img class="me-2" src="/dist/images/icons/arrow-up-1-9.png"
                                                                 alt="desc">
                                                            <span><?= $lang['b_filter_desc'] ?></span>
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
                                            <th><?= $lang['b_car_type'] ?></th>
                                            <th><?= $lang['b_suggestions_price'] ?></th>
                                            <th class="text-center"><?= $lang['b_suggestions_action'] ?></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach ($requests as $key => $item) {
                                            ?>
                                            <tr class="align-middle">
                                                <td><?= $key + 1 ?></td>
                                                <td><?= $item->CarType ?></td>
                                                <td><?= number_format($item->RequestPrice) ?> <?= $cargo->CargoMonetaryUnit ?></td>

                                                <td>
                                                    <a href="javascript:void(0);"
                                                       data-driver="<?= $item->DriverId ?>"
                                                       data-request="<?= $item->RequestId ?>"
                                                       data-cargo="<?= $cargo->CargoId ?>"
                                                       data-detail
                                                       class="mj-btn-more">
                                                        <?= $lang['b_suggestions_details'] ?>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                    <input type="hidden" id="token" name="token"
                                           value="<?= Security::initCSRF('request-change') ?>">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <template id="template-detail">
                    <div class="modal fade" id="modal-detail" role="dialog">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <?= $lang['b_suggestions_ready'] ?>
                                </div>
                                <div class="modal-footer justify-content-center">
                                    <button type="button"
                                            data-status="accepted"
                                            data-btn-request
                                            data-driver="#DRIVER#"
                                            data-request="#REQUEST#"
                                            data-cargo="#CARGO#"
                                            class="btn btn-primary approved-btn shadow-none px-4">
                                        <?= $lang['b_suggestions_accept'] ?>
                                    </button>

                                    <button type="button" class="btn btn-secondary close-btn shadow-none px-4"
                                            data-status="rejected"
                                            data-btn-request
                                            data-driver="#DRIVER#"
                                            data-request="#REQUEST#"
                                            data-cargo="#CARGO#">
                                        <?= $lang['rejecting'] ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </main>


        <?php

        getFooter('', false);
    } else {
        Router::trigger404();
    }
} else {
    header('location: /login');
}