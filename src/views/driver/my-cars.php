<?php

global $lang;

use MJ\Security\Security;
use MJ\Utils\Utils;

if (User::userIsLoggedIn()) {
    User::checkUserSlugAccess();
    include_once 'header-footer.php';

    enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');
    enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/min/dropzone.min.css');

    enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
    enqueueScript('dropzone-js', '/dist/libs/dropzone/min/dropzone.min.js');
    enqueueScript('lottie-player', '/dist/libs/lottie/lottie-player.js');
    enqueueScript('my-cars-init', '/dist/js/driver/my-cars.init.js');

    getHeader($lang['d_my_cars_title']);

    $user = User::getUserInfo();

    ?>
    <main class="container" style="padding-bottom: 180px;">
        <div class="row">
            <div class="col-12">
                <div class="card mj-card">
                    <div class="card-body">
                        <div class="mb-3">
                            <span class="mj-d-icon-title d-block mj-fw-700 mj-font-13 mb-1"><?= $lang['d_my_cars_list'] ?></span>
                            <p class="mj-d-cargo-item-desc mb-0">
                                <span>
                                    <img src="/dist/images/icons/circle-exclamation-gray.svg" class="me-1"
                                         alt="exclamation"/>
                                </span>
                                <?= $lang['d_my_cars_list_sub_title'] ?>
                            </p>
                        </div>

                        <div class="table-responsive">
                            <table id="cars-table"
                                   class="table mj-table mj-table-bordered mj-table-rounded mj-table-stripped mj-table-row-number w-100 mb-4">
                                <thead class="text-nowrap">
                                <tr>
                                    <th>#</th>
                                    <th><?= $lang['d_my_cars_table_type'] ?></th>
                                    <th><?= $lang['d_my_cars_table_plaque'] ?></th>
                                    <th><?= $lang['d_table_action'] ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $records = Driver::getMyCarsList($user->UserId);
                                if (!empty($records->response)) {
                                    foreach ($records->response as $key => $item) {
                                        ?>
                                        <tr class="align-middle">
                                            <td><?= $key + 1 ?></td>
                                            <td><?= $item->CarType . '(' . $item->car_name . ')' ?></td>
                                            <td>
                                                <div class="d-block text-start" dir="ltr">
                                                    <span class="d-inline-block"><?= $item->CarPlaque ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="/driver/car-detail/<?= $item->CarId ?>"
                                                   class="mj-btn-more">
                                                    <?= $lang['d_button_detail'] ?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr class="align-middle">
                                        <td colspan="4">
                                            <div class="text-center">
                                                <?= $lang['table_no_record_found'] ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>

                        <?php
//                        if ($user->UserStatus == 'active') {
                            ?>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="car-name"
                                               class="text-dark mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['d_my_cars_table_type'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-input-filter-box">
                                            <input type="text" inputmode="text" id="car-name" name="car-name"
                                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                                   placeholder="<?= $lang['b_car_name_example'] ?>"
                                                   style="min-height: 38px;">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="car-type"
                                               class="form-label mj-form-label mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['d_my_cars_choose_car_type'] ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="mj-custom-select car-type">
                                            <select class="is-invalid"
                                                    name="car-type"
                                                    id="car-type"
                                                    data-width="100%">
                                                <option value="-1">
                                                </option>
                                                <?php
                                                $carTypes = Driver::getCarTypes();
                                                foreach ($carTypes->response as $item) {
                                                    ?>
                                                    <option data-image="<?=$item->TypeImage ; ?>" value="<?= $item->TypeId ?>"><?= $item->TypeName ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="plaque-type"
                                               class="form-label mj-form-label mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['d_my_cars_choose_plaque_type'] ?>

                                        </label>
                                        <div class="mj-custom-select plaque-type">
                                            <select class="is-invalid"
                                                    name="plaque-type"
                                                    id="plaque-type"
                                                    data-width="100%"
                                                    data-placeholder="<?= $lang['d_my_cars_choose_plaque_type'] ?>">
                                                <option value=""></option>
                                                <?php
                                                $plaqueTypes = json_decode(Utils::getFileValue('plaque_types.json', null, false));
                                                $language = trim($_COOKIE['language']);
                                                foreach ($plaqueTypes as $key => $type) {
                                                    ?>
                                                    <option value="<?= $key ?>"><?= $type->name->$language ?></option>
                                                    <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="plaque-type"
                                               class="form-label mj-form-label mj-fw-500 mj-font-12 mb-1">
                                            <?= $lang['d_my_cars_table_plaque'] ?>

                                        </label>
                                        <div>
                                            <div class="row flex-row-reverse">
                                                <div class="col">
                                                    <div class="mb-3">
                                                        <div class="mj-input-filter-box">
                                                            <input type="text"
                                                                   id="plaque-number"
                                                                   inputmode="text"
                                                                   class="mj-input-filter text-center mj-fw-700 mj-font-13 px-0 py-1"
                                                                   name="plaque-number"
                                                                   placeholder="<?=$lang['d_my_cars_plaque_type'];?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <form action="#" method="post" class="dropzone mj-dropzone" id="carImages"
                                          data-plugin="dropzone" data-previews-container="#file-previews"
                                          data-upload-preview-template="#uploadPreviewTemplate">
                                        <div class="fallback">
                                            <input type="file" name="file">
                                        </div>

                                        <div class="dz-message needsclick">
                                            <img src="/dist/images/icons/folder-plus.svg" class="mb-2"
                                                 alt="choose-images">
                                            <h5><?= $lang['d_my_cars_choose_images'] ?></h5>
                                        </div>
                                    </form>
                                </div>

                                <div class="col-12">
                                    <div class="row dropzone-previews mt-3" id="file-previews"></div>

                                    <div class="d-none" id="uploadPreviewTemplate">
                                        <div class="col-12">
                                            <div class="card shadow-none border">
                                                <div class="p-2">
                                                    <div class="row align-items-center">
                                                        <div class="col-auto">
                                                            <img data-dz-thumbnail src="#"
                                                                 class="avatar-sm rounded" alt="">
                                                        </div>
                                                        <div class="col">
                                                            <strong class="text-muted" data-dz-name></strong>
                                                            <p class="mb-0" data-dz-size></p>
                                                            <div class="progress">
                                                                <div class="progress-bar progress-bar-striped"
                                                                     role="progressbar" data-dz-progress
                                                                     aria-valuemin="0"
                                                                     aria-valuemax="100"></div>
                                                            </div>
                                                            <p class="text-center mj-dropzone-progress">
                                                                <?= $lang['dropzone_inprogress'] ?>
                                                            </p>
                                                        </div>
                                                        <div class="col-auto">
                                                            <a href="javascript:void(0)"
                                                               class="btn btn-lg btn-link text-danger shadow-none"
                                                               data-dz-remove>
                                                                <i class="fe-x align-middle"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-7 mx-auto">
                                    <input type="hidden" id="token" name="token"
                                           value="<?= Security::initCSRF2() ?>">
                                    <button type="button" class="mj-btn-more py-2 w-100" id="submit-car"
                                            name="submit-car">
                                        <?= $lang['d_button_add'] ?>
                                    </button>
                                </div>
                            </div>

                            <div class="modal fade" id="modal-processing" data-bs-backdrop="static"
                                 data-bs-keyboard="false"
                                 role="dialog">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="text-center my-3">
                                                <lottie-player src="/dist/lottie/loading.json" class="mx-auto"
                                                               style="max-width: 400px;" speed="1" loop
                                                               autoplay></lottie-player>

                                                <h6 class="mb-0"><?= str_replace('#ACTION#', $lang['submit_car'], $lang['b_info_processing']) ?>
                                                    ...</h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade" id="modal-submitted"
                                 role="dialog">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body">
                                            <div class="text-center my-3" id="submitting-alert">

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
//                        }
                        ?>
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