<?php

global $lang, $Settings;

use MJ\Security\Security;


if (User::userIsLoggedIn()) {
    $user_id = json_decode(Security::decrypt($_COOKIE['user-login']))->UserId;
    $user_type = 'other';
    if (isset($_COOKIE['user-type']) && $_COOKIE['user-type'] == 'driver') {
        $user_type = 'driver';
    } else {
        header('Location: /user/drivers');
    }


    if (CV::getUserCvCount($user_id) == 0) {

    } else {
        header('Location: /user/drivers/edit');
    }


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
    enqueueScript('add-js', '/dist/js/user/drivers-add.js');
    enqueueScript('dropzone-js', '/dist/libs/dropzone/dropzone-min6.js');
    enqueueScript('persian-date-js', '/dist/libs/persian-calendar/persian-date.min.js');
    enqueueScript('persian-datepicker-js', '/dist/libs/persian-calendar/persian-datepicker.min.js');
    getHeader($lang['u_driver_cv_add_cv']);


    ?>
    <style>
        .select2-search__field{
            width: 100% !important;
        }
    </style>

    <section class="container" style="padding: 180px 10px 180px 10px;">
        <div class="mj-cv-avatar-upload">
            <div class="avatar-edit">
                <input type='file' id="imageUpload" accept=".png, .jpg, .jpeg"/>
                <label class="mj-cv-avatar-label" for="imageUpload">
                    <div><span class="fa-camera"></span></div>
                    <div class="avatar-preview">
                        <div id="imagePreview" style="background-image: url('/dist/images/drivers/empty-profile.svg');">
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
                               placeholder="name@example.com">
                        <label class="mj-floating-labels" for="firstname"><?= $lang['auth_first_name'] ?></label>
                    </div>
                    <div class="form-floating mb-1 col-7 no-pad-right">
                        <input type="text" class="form-control mj-cv-add-input" id="lastname"
                               placeholder="name@example.com">
                        <label for="lname"><?= $lang['auth_last_name'] ?></label>
                    </div>
                    <div class="form-floating mb-1 col-6 no-pad-left mj-select2-selects">
                        <div class=" mj-driver-add-date-picker">
                            <input type="text"
                                   id="brithday"
                                   readonly="readonly"
                                   name="brithday"
                                   class="mj-input-filter mj-fw-400 mj-font-13 px-0"
                                   style="min-height: 38px;" value="<?= date('Y/m/d') ?>">
                            <span id="birthday-cv"><?= $lang['auth_birthday_date'] ?></span>
                            <input type="hidden" id="brithday-ts" name="brithday-ts">
                        </div>
                    </div>

                    <div id="sex-select" class="form-floating mb-1 col-6 no-pad-right mj-select2-selects">
                        <select id="sex-type" class="form-select" aria-label="Default select example">
                            <option value=""></option>
                            <option value="mr"><?= $lang['u_mr'] ?></option>
                            <option value="ms"><?= $lang['u_ms'] ?></option>

                        </select>
                    </div>

                    <div id="marriage" class="form-floating mb-1 col-12 mj-select2-selects ">
                        <select id="marriage-select" class="form-select" aria-label="Default select example">
                            <option value=""></option>
                            <option value="married"><?= $lang['u_married'] ?></option>
                            <option value="single"><?= $lang['u_single'] ?></option>
                        </select>
                    </div>
                    <div class="form-floating mb-1 col-6 no-pad-left mj-select2-selects">
                        <select id="country-select" class="form-select" aria-label="Default select example">
                            <option value=""></option>
                            <?php
                            $countries = Location::getCountriesList();
                            foreach ($countries->response as $item) {
                                ?>
                                <option value="<?= $item->CountryId ?>"><?= $item->CountryName ?></option>
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
                        <div class="mj-cv-item-title"><?= $lang['a_military_service'] ?> :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1"
                                       value="option1">
                                <label id="soldier-yes" class="form-check-label" for="exampleRadios1" data-state="true">
                                    <?= $lang['u_driver_cv_have'] ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2"
                                       value="option2" checked>
                                <label id="soldier-no" class="form-check-label" for="exampleRadios2">
                                    <?= $lang['u_driver_cv_nothave'] ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="soldier-input1">

                    </div>

                    <!--                    dropzpne soldier end-->

                    <!--                    dropzpne ai cart start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title"><?= $lang['cv_smartcard_status'] ?> :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="aicard" id="exampleRadios3"
                                       value="option1">
                                <label id="ai-card-yes" class="form-check-label ai-card-yes" for="exampleRadios3"
                                       data-state="true">
                                    <?= $lang['u_driver_cv_have'] ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="aicard" id="exampleRadios4"
                                       value="option2" checked>
                                <label id="ai-card-no" class="form-check-label" for="exampleRadios4">
                                    <?= $lang['u_driver_cv_nothave'] ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="ai-card-input1">


                    </div>

                    <!--                    dropzpne ai cart end-->

                    <!--                    dropzpne passport start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title"><?= $lang['cv_passport_status'] ?> :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="passport" id="exampleRadios5"
                                       value="option1">
                                <label id="passport-yes" class="form-check-label" for="exampleRadios5"
                                       data-state="true">
                                    <?= $lang['u_driver_cv_have'] ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="passport" id="exampleRadios6"
                                       value="option2" checked>
                                <label id="passport-no" class="form-check-label" for="exampleRadios6">
                                    <?= $lang['u_driver_cv_nothave'] ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="passport-input1">

                    </div>

                    <!--                    dropzpne passport end-->

                    <!--                    dropzpne visa start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title"><?= $lang['cv_visa_status'] ?> :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visa" id="exampleRadios7"
                                       value="option1">
                                <label id="visa-yes" class="form-check-label" for="exampleRadios7" data-state="true">
                                    <?= $lang['u_driver_cv_have'] ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visa" id="exampleRadios8"
                                       value="option2" checked>
                                <label id="visa-no" class="form-check-label" for="exampleRadios8">
                                    <?= $lang['u_driver_cv_nothave'] ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="visa-input1">


                    </div>

                    <!--                    dropzpne visa end-->


                    <!--                    dropzpne work book start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title"><?= $lang['cv_workbook_status']  ?> :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="workbook" id="exampleRadios9"
                                       value="option1">
                                <label id="workbook-yes" class="form-check-label" for="exampleRadios9"
                                       data-state="true">
                                    <?= $lang['u_driver_cv_have'] ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="workbook" id="exampleRadios10"
                                       value="option2" checked>
                                <label id="workbook-no" class="form-check-label" for="exampleRadios10">
                                    <?= $lang['u_driver_cv_nothave'] ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="workbook-input1">


                    </div>

                    <!--                    dropzpne work book end-->


                    <!--                    dropzpne drive license start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title"><?= $lang['cv_driver_license_image'] ?> :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="drivelicense" id="exampleRadios11"
                                       value="option1">
                                <label id="drivelicense-yes" class="form-check-label" for="exampleRadios11"
                                       data-state="true">
                                    <?= $lang['u_driver_cv_have'] ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="drivelicense" id="exampleRadios12"
                                       value="option2" checked>
                                <label id="drivelicense-no" class="form-check-label" for="exampleRadios12">
                                    <?= $lang['u_driver_cv_nothave'] ?>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="drivelicense-input1">


                    </div>

                    <!--                    dropzpne drive license end-->

                    <!--                    phone number start-->


                    <div class="form-floating mb-1 col-12 " style="margin-top: 10px !important;">
                        <input type="number" class="form-control mj-cv-add-input" id="phonenumber"
                               placeholder="09143302964">
                        <label class="mj-floating-labels" for="phonenumber"><?= $lang['u_driver_cv_phonenumber'] ?></label>
                    </div>
                    <div class="form-floating mb-1 col-12 ">
                        <input type="number" class="form-control mj-cv-add-input" id="whatsappnumber"
                               placeholder="09143302964">
                        <label class="mj-floating-labels" for="whatsappnumber"><?= $lang['u_driver_cv_whatsapp'] ?></label>
                    </div>

                    <div class="form-floating mb-1 col-12 ">
                                <textarea type="text" class="form-control mj-cv-add-input " id="address"
                                          placeholder="آدرس" cols="30" rows="10"></textarea>
                        <label class="mj-floating-labels" for="address"><?= $lang['u_driver_cv_address'] ?></label>
                    </div>
                    <div class="form-floating mb-1 col-12 mj-select2-selects">
                        <select id="fav-road-select" class="form-select" aria-label="Default select example"
                                multiple="multiple">
                            <option value=""></option>
                            <?php
                            $countries = Location::getCountriesList();
                            foreach ($countries->response as $item) {
                                ?>
                                <option value="<?= $item->CountryId ?>"><?= $item->CountryName ?></option>
                                <?php
                            }
                            ?>
                        </select>
                    </div>

                    <form style="margin: 10px;display:flex;align-items: center;">
                        <input type="checkbox" id="contract" name="contract">
                        <label id="allow-label" style="padding-right: 5px"
                               for="contract"><?= $lang['u_driver_cv_role'] ?></label>
                    </form>
                    <div class="col-12 mj-cv-add-button">
                        <button id="submit-driver" type="button"
                                class="btn mj-btn mj-btn-primary shadow-none w-100 mb-3 d-flex justify-content-center align-items-center"
                        >
                            <?= $lang['u_driver_cv_submit'] ?>
                        </button>


                        <script>


                        </script>
                    </div>


                    <!--                    phone number license end-->
                    <input type="hidden" id="token" name="token"
                           value="<?= Security::initCSRF2() ?>">
                </div>
    </section>




<!---->
<!--    <div class="mj-custom-select ">-->
<!---->
<!---->
<!--        <select class=" width-95 my-1 mb-3"-->
<!--                id="visa-location"-->
<!--                name="visa-location"-->
<!--                data-width="100%"-->
<!--                multiple="multiple"-->
<!--                data-placeholder="1">-->
<!--            <option value=""></option>-->
<!--            <option value="2">2</option>-->
<!--            <option value="3">3</option>-->
<!--            <option value="3">3</option>-->
<!--        </select>-->
<!---->
<!---->
<!--    </div>-->








    <?php

    getFooter('', false);
} else {
    header('location:/login');
}