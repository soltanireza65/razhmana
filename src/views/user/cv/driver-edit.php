<?php

global $lang, $Settings;

use MJ\Security\Security;


if (User::userIsLoggedIn()) {


    $user_id = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;

    $my_cv = CV::getCvDetailByUserId($user_id);
    if ($my_cv->status == 200) {
        $my_cv = $my_cv->response[0];
    } else {
        $my_cv = [];
    }


    file_put_contents('temp.json', json_encode($my_cv));


    include_once getcwd() . '/views/user/header-footer.php';
    enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.min.css');
    enqueueStylesheet('FA-css', '/dist/libs/select2/css/select2.min.css');
    enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/dropzone6.css');
    enqueueStylesheet('persian-datepicker-css', '/dist/libs/persian-calendar/persian-datepicker.min.css');


    enqueueScript('persian-date-js', '/dist/libs/persian-calendar/persian-date.min.js');
    enqueueScript('persian-datepicker-js', '/dist/libs/persian-calendar/persian-datepicker.min.js');
    enqueueScript('FA-js', '/dist/libs/select2/js/select2.min.js');
    enqueueScript('FAs-js', '/dist/libs/fontawesome/all.min.js');
    enqueueScript('accounts-js', '/dist/libs/lottie/lottie-player.js');

    enqueueScript('dropzone-js', '/dist/libs/dropzone/dropzone-min6.js');
    enqueueScript('persian-date-js', '/dist/libs/persian-calendar/persian-date.min.js');
    enqueueScript('persian-datepicker-js', '/dist/libs/persian-calendar/persian-datepicker.min.js');
//    enqueueScript('add-js', '/dist/js/user/drivers-edit.js');
    enqueueScript('add-js', '/dist/js/user/drivers-edit2.js');
    getHeader($lang['d_faq_title']);

    ?>
    <script>
        let my_cv = <?=json_encode($my_cv)?>;
        console.log(my_cv)

    </script>

    <section class="container" style="padding: 180px 10px 180px 10px;">
        <div class="mj-cv-avatar-upload">
            <div class="avatar-edit">
                <input type='file' id="imageUpload" accept=".png, .jpg, .jpeg"
                       value="<?= isset($my_cv->cv_user_avatar) ? $my_cv->cv_user_avatar : '' ?>"/>
                <label class="mj-cv-avatar-label" for="imageUpload">
                    <div><span class="fa-camera"></span></div>
                    <div class="avatar-preview">
                        <div id="imagePreview"
                             style="background-image: url('<?= isset($my_cv->cv_user_avatar) ? $my_cv->cv_user_avatar : POSTER_DEFAULT ?>');">
                        </div>
                    </div>

                </label>
            </div>

        </div>
        <div class="mj-cv-add-form">
            <h5 class="mt-4 mb-2"><?= $lang['u_driver_cv_input_corent_alert'] ?></h5>
            <div class="container">
                <div class="row">
                    <div class="form-floating mb-1 col-5 no-pad-left">
                        <input type="text" class="form-control mj-cv-add-input" id="firstname"
                               placeholder="name@example.com" value="<?= $my_cv->cv_name ?>">
                        <label class="mj-floating-labels" for="firstname"><?= $lang['u_driver_cv_name'] ?></label>
                    </div>
                    <div class="form-floating mb-1 col-7 no-pad-right">
                        <input type="text" class="form-control mj-cv-add-input" id="lastname"
                               placeholder="name@example.com" value="<?= $my_cv->cv_lname ?>">
                        <label for="lname"><?= $lang['u_driver_cv_lname'] ?></label>
                    </div>
                    <div class="form-floating mb-1 col-6 no-pad-left mj-select2-selects">
                        <div class="mj-driver-add-date-picker">
                            <input type="text"
                                   id="brithday"
                                   readonly="readonly"
                                   name="brithday"
                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                   style="min-height: 38px;" value="<?= $my_cv->cv_brith_date ?>">

                            <input type="hidden" id="brithday-ts" name="brithday-ts">
                            <span><?= $lang['auth_birthday_date'] ?></span>
                        </div>
                    </div>
                    <div class="form-floating mb-1 col-6 no-pad-right mj-select2-selects">
                        <select id="sex-type" class="form-select" aria-label="Default select example">
                            <option value=""></option>
                            <option
                                value="mr" <?= $my_cv->cv_gender == 'mr' ? 'selected' : '' ?>><?= $lang['u_mr'] ?></option>
                            <option
                                value="ms" <?= $my_cv->cv_gender == 'ms' ? 'selected' : '' ?>><?= $lang['u_ms'] ?></option>

                        </select>
                    </div>

                    <div id="marriage" class="form-floating mb-1 col-12 mj-select2-selects">
                        <select id="marriage-select" class="form-select" aria-label="Default select example">
                            <option value=""></option>
                            <option
                                value="married" <?= $my_cv->cv_marital_status == 'married' ? 'selected' : '' ?>><?= $lang['u_married'] ?></option>
                            <option
                                value="single" <?= $my_cv->cv_marital_status == 'single' ? 'selected' : '' ?>><?= $lang['u_single'] ?></option>
                        </select>
                    </div>
                    <div class="form-floating mb-1 col-6 no-pad-left mj-select2-selects">
                        <select id="country-select" class="form-select" aria-label="Default select example">
                            <option value=""></option>
                            <?php
                            $countries = Location::getCountriesList();
                            foreach ($countries->response as $item) {
                                ?>
                                <option
                                    value="<?= $item->CountryId ?>" <?= ($item->CountryId == $my_cv->country_id) ? 'selected' : '' ?>><?= $item->CountryName ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-floating mb-1 col-6 no-pad-right mj-select2-selects">
                        <select id="city-select" class="form-select" aria-label="Default select example">

                        </select>
                    </div>

                    <!--                    dropzpne soldier start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title"><?= $lang['cv_military_status'] ?> :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1"
                                       value="option1" <?= ($my_cv->cv_military_status == 'yes') ? 'checked' : '' ?>>
                                <label id="soldier-yes" class="form-check-label" for="exampleRadios1" data-state="true">
                                    <?= $lang['u_driver_cv_have'] ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2"
                                       value="option2" <?= ($my_cv->cv_military_status == 'no') ? 'checked' : '' ?>>
                                <label id="soldier-no" class="form-check-label" for="exampleRadios2">
                                    <?= $lang['u_driver_cv_nothave'] ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div style="display:<?= ($my_cv->cv_military_status == 'no') ? 'none' : 'block' ?>;" id="soldier-input1">
                        <div id="soldier-input">
                            <div class="mb-1 mt-3 col-12 no-pad-left form-floating ">
                                <input type="email" class="form-control mj-cv-add-input" id="cv-military-number"
                                       placeholder="name@example.com" value="<?=$my_cv->cv_military_number?>">
                                <label class="mj-floating-labels"
                                       for="cv-military-number"> <?= $lang['cv_military_number'] ?> </label>
                            </div>
                            <div class="form-floating mb-1 col-12 no-pad-left mj-select2-selects">
                                <div class="mj-input-filter-box mj-driver-add-date-picker">
                                    <input type="text"
                                           id="cv-military-date"
                                           name="cv-military-date" readonly="readonly"
                                           class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                           style="min-height: 38px;"
                                           value=""><span><?= $lang['cv_millitry_date'] ?></span>
                                    <input type="hidden" id="cv-military-date-ts" name="cv-military-date-ts">
                                </div>
                            </div>


                            <DIV id="dropzone">
                                <FORM class="dropzone needsclick mj-add-dropzone" id="soldier-dz" action="/upload">
                                    <DIV class="dz-message needsclick"> <?= $lang['cv_military_dropzone'] ?>
                                        <div class="fa-plus mt-2"></div>
                                        <div style="color: red" id="soldier-error">
                                        </div>
                                    </DIV>

                                </FORM>
                            </DIV>

                        </div>
                        <DIV id="preview-template" style="display: none;">
                            <DIV class="dz-preview dz-file-preview">
                                <DIV class="dz-image"><IMG data-dz-thumbnail=""></DIV>
                                <DIV class="dz-details"></DIV>
                                <DIV class="dz-progress"><SPAN class="dz-upload"
                                                               data-dz-uploadprogress=""></SPAN></DIV>
                                <div class="dz-success-mark">
                                    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1"
                                         xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <title>Check</title>
                                        <desc>Created with Sketch.</desc>
                                        <defs></defs>
                                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <path
                                                d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                                id="Oval-2" stroke-opacity="0.198794158" stroke="#747474"
                                                fill-opacity="0.816519475" fill="#FFFFFF"></path>
                                        </g>
                                    </svg>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!--                    dropzpne soldier end-->

                    <!--                    dropzpne ai cart start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title"><?= $lang['cv_smartcard_status'] ?> :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="aicard" id="exampleRadios3"
                                    <?= ($my_cv->cv_smartcard_status == 'yes') ? 'checked' : '' ?>
                                       value="option1">
                                <label id="ai-card-yes" class="form-check-label ai-card-yes" for="exampleRadios3"
                                       data-state="true">
                                    <?= $lang['u_driver_cv_have'] ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="aicard" id="exampleRadios4"
                                       value="option2"
                                    <?= ($my_cv->cv_smartcard_status == 'no') ? 'checked' : '' ?>
                                >
                                <label id="ai-card-no" class="form-check-label" for="exampleRadios4">
                                    <?= $lang['u_driver_cv_nothave'] ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div style="display:<?= ($my_cv->cv_smartcard_status == 'no') ? 'none' : 'block' ?>;" id="ai-card-input1">
                        <div id="ai-card-input">
                            <div class="mb-1 mt-3 col-12 no-pad-left form-floating ">
                                <input type="email" class="form-control mj-cv-add-input" id="smart-card-number"
                                       placeholder="name@example.com"  value="<?=$my_cv->cv_smartcard_number?>">
                                <label class="mj-floating-labels"
                                       for="smart-card-number"> <?= $lang['cv_aicard_number'] ?></label>
                            </div>
                            <div class="form-floating mb-1 col-12 no-pad-left mj-select2-selects">
                                <div class="mj-input-filter-box mj-driver-add-date-picker">
                                    <input type="text"
                                           id="cv-smart-card-date"
                                           name="cv-smart-card-date" readonly="readonly"
                                           class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                           style="min-height: 38px;"
                                           value=""><span><?= $lang['cv_expire_date'] ?></span>
                                    <input type="hidden" id="cv-smart-card-date-ts" name="cv-smart-card-date-ts">
                                </div>
                            </div>


                            <DIV id="dropzone">
                                <FORM class="dropzone needsclick mj-add-dropzone" id="aicard-dz" action="/upload">
                                    <DIV class="dz-message needsclick"><?= $lang['cv_aicard_dropzone'] ?>
                                        <div class="fa-plus mt-2"></div>
                                        <div style="color: red" id="aicard-error">
                                        </div>
                                    </DIV>

                                </FORM>
                            </DIV>

                        </div>
                        <DIV id="preview-template" style="display: none;">
                            <DIV class="dz-preview dz-file-preview">
                                <DIV class="dz-image"><IMG data-dz-thumbnail=""></DIV>
                                <DIV class="dz-details"></DIV>
                                <DIV class="dz-progress"><SPAN class="dz-upload"
                                                               data-dz-uploadprogress=""></SPAN></DIV>
                                <div class="dz-success-mark">
                                    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1"
                                         xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <title>Check</title>
                                        <desc>Created with Sketch.</desc>
                                        <defs></defs>
                                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <path
                                                d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                                id="Oval-2" stroke-opacity="0.198794158" stroke="#747474"
                                                fill-opacity="0.816519475" fill="#FFFFFF"></path>
                                        </g>
                                    </svg>
                                </div>

                            </div>
                        </div>

                    </div>

                    <!--                    dropzpne ai cart end-->

                    <!--                    dropzpne passport start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title"><?= $lang['cv_passport_status'] ?> :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="passport" id="exampleRadios5"
                                       value="option1"
                                    <?= ($my_cv->cv_passport_status == 'yes') ? 'checked' : '' ?>>
                                <label id="passport-yes" class="form-check-label" for="exampleRadios5"
                                       data-state="true">
                                    <?= $lang['u_driver_cv_have'] ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="passport" id="exampleRadios6"
                                       value="option2"
                                    <?= ($my_cv->cv_passport_status == 'no') ? 'checked' : '' ?>
                                >
                                <label id="passport-no" class="form-check-label" for="exampleRadios6">
                                    <?= $lang['u_driver_cv_nothave'] ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div style="display:<?= ($my_cv->cv_passport_status == 'no') ? 'none' : 'block' ?>;"  id="passport-input1">
                        <div id="passport-input" class="mj-cv-detail-input">
                            <div class="mb-1 mt-3 col-12 no-pad-left form-floating ">
                                <input type="email" class="form-control mj-cv-add-input" id="passport-number"
                                       value="<?=$my_cv->cv_passport_number?>"
                                       placeholder="name@example.com">
                                <label class="mj-floating-labels"
                                       for="passport-number"><?= $lang['cv_passport_number'] ?></label>
                            </div>
                            <div class="form-floating mb-1 col-12 no-pad-left mj-select2-selects">
                                <div class="mj-input-filter-box mj-driver-add-date-picker">
                                    <input type="text"
                                           id="cv-passport-date"
                                           name="cv-passport-date" readonly="readonly"
                                           class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                           style="min-height: 38px;"
                                           value=""><span><?= $lang['cv_expire_date'] ?></span>
                                    <input type="hidden" id="cv-passport-date-ts" name="cv-passport-date-ts">
                                </div>
                            </div>
                            <DIV id="dropzone">
                                <FORM class="dropzone needsclick mj-add-dropzone" id="passport-dz" action="/upload">
                                    <DIV class="dz-message needsclick"><?= $lang['cv_passport_dropzone'] ?>
                                        <div class="fa-plus mt-2"></div>
                                        <div style="color: red" id="passport-error">
                                        </div>
                                    </DIV>

                                </FORM>
                            </DIV>

                        </div>
                        <DIV id="preview-template" style="display: none;">
                            <DIV class="dz-preview dz-file-preview">
                                <DIV class="dz-image"><IMG data-dz-thumbnail=""></DIV>
                                <DIV class="dz-details"></DIV>
                                <DIV class="dz-progress"><SPAN class="dz-upload"
                                                               data-dz-uploadprogress=""></SPAN></DIV>
                                <div class="dz-success-mark">
                                    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1"
                                         xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <title>Check</title>
                                        <desc>Created with Sketch.</desc>
                                        <defs></defs>
                                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <path
                                                d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                                id="Oval-2" stroke-opacity="0.198794158" stroke="#747474"
                                                fill-opacity="0.816519475" fill="#FFFFFF"></path>
                                        </g>
                                    </svg>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!--                    dropzpne passport end-->

                    <!--                    dropzpne visa start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title"><?= $lang['cv_visa_status'] ?> :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visa" id="exampleRadios7"
                                       value="option1"
                                    <?= ($my_cv->cv_visa_status == 'yes') ? 'checked' : ' ' ?>>
                                <label id="visa-yes" class="form-check-label" for="exampleRadios7" data-state="true">
                                    <?= $lang['u_driver_cv_have'] ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visa" id="exampleRadios8"
                                       value="option2"
                                    <?= ($my_cv->cv_visa_status == 'no') ? 'checked' : ' ' ?>>
                                <label id="visa-no" class="form-check-label" for="exampleRadios8">
                                    <?= $lang['u_driver_cv_nothave'] ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div style="display:<?= ($my_cv->cv_visa_status == 'no') ? 'none' : 'block' ?>;" id="visa-input1">


                        <div id="visa-input" class="mj-cv-detail-input">
                            <div class="mb-1 mt-3 col-12 no-pad-left form-floating ">
                                <input type="email" class="form-control mj-cv-add-input" id="visa-number"
                                       value="<?=$my_cv->cv_visa_number?>"
                                       placeholder="name@example.com">
                                <label class="mj-floating-labels"
                                       for="visa-number"><?= $lang['cv_visa_number'] ?></label>
                            </div>

                            <div class="mj-custom-select mj-select2-selects ">


                                <select class=" width-95 my-1 mb-3"
                                        id="visa-location"
                                        name="visa-location"
                                        data-width="100%"
                                        multiple="multiple"
                                        data-placeholder="">

                                </select>

                                <div class="form-floating mb-1 col-12 no-pad-left mj-select2-selects">
                                    <div class="mj-input-filter-box mj-driver-add-date-picker">
                                        <input type="text"
                                               id="cv-visa-date"
                                               name="cv-visa-date" readonly="readonly"
                                               class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                               style="min-height: 38px;"
                                               value=""><span><?= $lang['cv_expire_date'] ?></span>
                                        <input type="hidden" id="cv-visa-date-ts" name="cv-visa-date-ts">
                                    </div>
                                </div>


                                <DIV id="dropzone">
                                    <FORM class="dropzone needsclick mj-add-dropzone" id="visa-dz" action="/upload">
                                        <DIV class="dz-message needsclick"><?= $lang['cv_visa_dropzone'] ?>
                                            <div class="fa-plus mt-2"></div>
                                            <div style="color: red" id="visa-error">
                                            </div>
                                        </DIV>

                                    </FORM>
                                </DIV>

                            </div>
                            <DIV id="preview-template" style="display: none;">
                                <DIV class="dz-preview dz-file-preview">
                                    <DIV class="dz-image"><IMG data-dz-thumbnail=""></DIV>
                                    <DIV class="dz-details"></DIV>
                                    <DIV class="dz-progress"><SPAN class="dz-upload"
                                                                   data-dz-uploadprogress=""></SPAN></DIV>
                                    <div class="dz-success-mark">
                                        <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1"
                                             xmlns="http://www.w3.org/2000/svg"
                                             xmlns:xlink="http://www.w3.org/1999/xlink">
                                            <title>Check</title>
                                            <desc>Created with Sketch.</desc>
                                            <defs></defs>
                                            <g id="Page-1" stroke="none" stroke-width="1" fill="none"
                                               fill-rule="evenodd">
                                                <path
                                                    d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                                    id="Oval-2" stroke-opacity="0.198794158" stroke="#747474"
                                                    fill-opacity="0.816519475" fill="#FFFFFF"></path>
                                            </g>
                                        </svg>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>

                    <!--                    dropzpne visa end-->


                    <!--                    dropzpne work book start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title"><?= $lang['cv_workbook_status'] ?> :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="workbook" id="exampleRadios9"
                                       value="option1"
                                    <?= ($my_cv->cv_workbook_status == 'yes') ? 'checked' : '' ?>
                                >
                                <label id="workbook-yes" class="form-check-label" for="exampleRadios9"
                                       data-state="true">
                                    <?= $lang['u_driver_cv_have'] ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="workbook"
                                       id="exampleRadios10"
                                       value="option2"
                                    <?= ($my_cv->cv_workbook_status == 'no') ? 'checked' : '' ?>
                                >
                                <label id="workbook-no" class="form-check-label" for="exampleRadios10">
                                    <?= $lang['u_driver_cv_nothave'] ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div style="display:<?= ($my_cv->cv_workbook_status == 'no') ? 'none' : 'block' ?>;" id="workbook-input1">


                        <div id="workbook-input" class="mj-cv-detail-input">
                            <div class="mb-1 mt-3 col-12 no-pad-left form-floating ">
                                <input type="email" class="form-control mj-cv-add-input"
                                       id="workbook-number"
                                       value="<?=$my_cv->cv_workbook_number?>"
                                       placeholder="name@example.com">
                                <label class="mj-floating-labels"
                                       for="workbook-number"><?= $lang['cv_workbook_number'] ?></label>
                            </div>
                            <div class="form-floating mb-1 col-12 no-pad-left mj-select2-selects">
                                <div class="mj-input-filter-box mj-driver-add-date-picker">
                                    <input type="text"
                                           id="cv-workbook-date"
                                           name="cv-workbook-date" readonly="readonly"
                                           class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                           style="min-height: 38px;"
                                           value=""><span><?= $lang['cv_expire_date'] ?></span>
                                    <input type="hidden" id="cv-workbook-date-ts"
                                           name="cv-workbook-date-ts">
                                </div>
                            </div>


                            <DIV id="dropzone">
                                <FORM class="dropzone needsclick mj-add-dropzone" id="workbook-dz"
                                      action="/upload">
                                    <DIV class="dz-message needsclick"><?= $lang['cv_workbook_dropzone'] ?>
                                        <div class="fa-plus mt-2"></div>
                                        <div style="color: red" id="workbook-error">
                                        </div>
                                    </DIV>

                                </FORM>
                            </DIV>

                        </div>
                        <DIV id="preview-template" style="display: none;">
                            <DIV class="dz-preview dz-file-preview">
                                <DIV class="dz-image"><IMG data-dz-thumbnail=""></DIV>
                                <DIV class="dz-details"></DIV>
                                <DIV class="dz-progress"><SPAN class="dz-upload"
                                                               data-dz-uploadprogress=""></SPAN></DIV>
                                <div class="dz-success-mark">
                                    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1"
                                         xmlns="http://www.w3.org/2000/svg"
                                         xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <title>Check</title>
                                        <desc>Created with Sketch.</desc>
                                        <defs></defs>
                                        <g id="Page-1" stroke="none" stroke-width="1" fill="none"
                                           fill-rule="evenodd">
                                            <path
                                                d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                                id="Oval-2" stroke-opacity="0.198794158" stroke="#747474"
                                                fill-opacity="0.816519475" fill="#FFFFFF"></path>
                                        </g>
                                    </svg>
                                </div>

                            </div>
                        </div>


                    </div>

                    <!--                    dropzpne work book end-->


                    <!--                    dropzpne drive license start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title"><?= $lang['cv_driver_license_image'] ?> :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="drivelicense"
                                       id="exampleRadios11"
                                       value="option1"
                                    <?= ($my_cv->cv_driver_license_status == 'yes') ? 'checked' : '' ?>
                                >
                                <label id="drivelicense-yes" class="form-check-label" for="exampleRadios11"
                                       data-state="true">
                                    <?= $lang['u_driver_cv_have'] ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="drivelicense"
                                       id="exampleRadios12"
                                       value="option2"
                                    <?= ($my_cv->cv_driver_license_status == 'no') ? 'checked' : '' ?>
                                >
                                <label id="drivelicense-no" class="form-check-label" for="exampleRadios12">
                                    <?= $lang['u_driver_cv_nothave'] ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div style="display:<?= ($my_cv->cv_driver_license_status == 'no') ? 'none' : 'block' ?>;" id="drivelicense-input1">
                        <?php if ($my_cv->cv_driver_license_status == 'yes') {
                            ?>


                            <?php
                        } ?>


                        <div id="drivelicense-input" class="mj-cv-detail-input">
                            <div class="mb-1 mt-3 col-12 no-pad-left form-floating ">
                                <input type="email" class="form-control mj-cv-add-input"
                                       id="drivelicense-number"
                                       value="<?=$my_cv->cv_driver_license_number?>"
                                       placeholder="name@example.com">
                                <label class="mj-floating-labels"
                                       for="drivelicense-number"><?= $lang['cv_driver_license_number'] ?></label>
                            </div>
                            <div class="form-floating mb-1 col-12 no-pad-left mj-select2-selects">
                                <div class="mj-input-filter-box mj-driver-add-date-picker">
                                    <input type="text"
                                           id="cv-driver-license-date"
                                           name="cv-driver-license-date" readonly="readonly"
                                           class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                           style="min-height: 38px;"
                                           value=""><span><?= $lang['cv_expire_date'] ?></span>
                                    <input type="hidden" id="cv-driver-license-date-ts"
                                           name="cv-driver-license-date-ts">
                                </div>
                            </div>


                            <DIV id="dropzone">
                                <FORM class="dropzone needsclick mj-add-dropzone" id="drivelicense-dz"
                                      action="/upload">
                                    <DIV
                                        class="dz-message needsclick"><?= $lang['cv_driver_license_dropzone'] ?>
                                        <div class="fa-plus mt-2"></div>
                                        <div style="color: red" id="drivelicense-error">
                                        </div>
                                    </DIV>

                                </FORM>
                            </DIV>

                        </div>
                        <DIV id="preview-template" style="display: none;">
                            <DIV class="dz-preview dz-file-preview">
                                <DIV class="dz-image"><IMG data-dz-thumbnail=""></DIV>
                                <DIV class="dz-details"></DIV>
                                <DIV class="dz-progress"><SPAN class="dz-upload"
                                                               data-dz-uploadprogress=""></SPAN></DIV>
                                <div class="dz-success-mark">
                                    <svg width="54px" height="54px" viewBox="0 0 54 54" version="1.1"
                                         xmlns="http://www.w3.org/2000/svg"
                                         xmlns:xlink="http://www.w3.org/1999/xlink">
                                        <title>Check</title>
                                        <desc>Created with Sketch.</desc>
                                        <defs></defs>
                                        <g id="Page-1" stroke="none" stroke-width="1" fill="none"
                                           fill-rule="evenodd">
                                            <path
                                                d="M23.5,31.8431458 L17.5852419,25.9283877 C16.0248253,24.3679711 13.4910294,24.366835 11.9289322,25.9289322 C10.3700136,27.4878508 10.3665912,30.0234455 11.9283877,31.5852419 L20.4147581,40.0716123 C20.5133999,40.1702541 20.6159315,40.2626649 20.7218615,40.3488435 C22.2835669,41.8725651 24.794234,41.8626202 26.3461564,40.3106978 L43.3106978,23.3461564 C44.8771021,21.7797521 44.8758057,19.2483887 43.3137085,17.6862915 C41.7547899,16.1273729 39.2176035,16.1255422 37.6538436,17.6893022 L23.5,31.8431458 Z M27,53 C41.3594035,53 53,41.3594035 53,27 C53,12.6405965 41.3594035,1 27,1 C12.6405965,1 1,12.6405965 1,27 C1,41.3594035 12.6405965,53 27,53 Z"
                                                id="Oval-2" stroke-opacity="0.198794158" stroke="#747474"
                                                fill-opacity="0.816519475" fill="#FFFFFF"></path>
                                        </g>
                                    </svg>
                                </div>

                            </div>
                        </div>


                        <!--                    dropzpne drive license end-->

                        <!--                    phone number start-->


                    </div>

                    <div class="form-floating mb-1 col-12 " style="margin-top: 10px !important;">
                        <input type="number" class="form-control mj-cv-add-input" id="phonenumber"
                               placeholder="09143302964" value="<?= $my_cv->cv_mobile ?>">
                        <label class="mj-floating-labels"
                               for="phonenumber"><?= $lang['u_driver_cv_phonenumber'] ?></label>
                    </div>
                    <div class="form-floating mb-1 col-12 ">
                        <input type="number" class="form-control mj-cv-add-input" id="whatsappnumber"
                               placeholder="09143302964" value="<?= $my_cv->cv_whatsapp ?>">
                        <label class="mj-floating-labels"
                               for="whatsappnumber"><?= $lang['u_driver_cv_whatsapp'] ?></label>
                    </div>

                    <div class="form-floating mb-1 col-12 ">
                                <textarea type="text" class="form-control mj-cv-add-input " id="address"
                                          placeholder="آدرس" cols="30" rows="10"
                                > <?= $my_cv->cv_address ?></textarea>
                        <label class="mj-floating-labels"
                               for="address"><?= $lang['u_driver_cv_address'] ?></label>
                    </div>
                    <div class="form-floating mb-1 col-12 mj-select2-selects">
                        <select id="fav-road-select" class="form-select" aria-label="Default select example"
                                multiple="multiple">
                            <option value=""></option>
                            <?php
                            if (($my_cv->cv_faviroite_country) != null) {
                                $countries = Location::getCountriesList();
                                foreach ($countries->response as $item) {
                                    ?>
                                    <option value="<?= $item->CountryId ?>"
                                        <?= (in_array($item->CountryId, $my_cv->cv_faviroite_country)) ? 'selected' : '' ?>
                                    ><?= $item->CountryName ?></option>
                                    <?php
                                }
                            } else {
                                $countries = Location::getCountriesList();
                                foreach ($countries->response as $item) {
                                    ?>
                                    <option
                                        value="<?= $item->CountryId ?>">   <?= $item->CountryName ?></option>
                                    <?php
                                }
                            }

                            ?>
                        </select>
                    </div>

                    <form style="margin: 10px;display:flex;align-items: center;">
                        <input type="checkbox" id="contract"
                               name="contract" <?= ($my_cv->cv_role_status == 'yes') ? 'checked' : '' ?> >
                        <label style="padding-right: 5px"
                               for="contract"><?= $lang['u_driver_cv_role'] ?></label>
                    </form>
                    <div class="col-12 mj-cv-add-button">

                        <button id="submit-driver" type="button"
                                class="btn mj-btn mj-btn-primary shadow-none w-100 mb-3 d-flex justify-content-center align-items-center"
                        >
                            <?= $lang['u_driver_cv_submit'] ?>
                        </button>


                    </div>
                    <!--                    phone number license end-->
                    <input type="hidden" id="token" name="token"
                           value="<?= Security::initCSRF2() ?>">
    </section>


    <?php
    getFooter('', false);
} else {
    header('location:/login');
}