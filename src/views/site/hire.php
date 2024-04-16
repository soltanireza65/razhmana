<?php

global $lang, $Settings;

use MJ\Security\Security;
use MJ\Utils\Utils;

include_once 'header-footer.php';

enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/min/dropzone.min.css');
enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');
enqueueStylesheet('hire-css', '/dist/css/hire.css');

enqueueScript('bootstrap-wizard-js', '/dist/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js');
enqueueScript('repeater', '/dist/libs/jquery.repeater/jquery.repeater.min.js');
enqueueScript('dropzone-js', '/dist/libs/dropzone/min/dropzone.min.js');
enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
enqueueScript('hire-js', '/dist/js/site/hire.js');

getHeader($lang['employee']);
?>
    <main class="container" style="padding-bottom: 180px;">
        <style>
            .dropzone {
                min-height: 150px;
                border: 1px dotted rgba(0,0,0,.3);
                background: #fff;
                padding: 0;
                display: flex;
                width: 100%;
                justify-content: center;
                align-items: center;
            }
            .dz-message img{
                width: 50px !important;
            }
            .dz-message h5{
                font-size: 13px !important;
            }
            .dz-message .fa-folder-plus{
                font-size: 50px !important;
                color: var(--primary) !important;
            }

            .mj-a-border-danger {
                border:1px solid #f35d5d !important;
            }
        </style>
        <div class="card">
            <div class="">
                <form>
                    <div id="progressbarwizard">

                        <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-1 d-none">
                            <li class="nav-item">
                                <a href="#tab-1" data-bs-toggle="tab" data-toggle="tab"
                                   class="nav-link rounded-0 pt-2 pb-2">
                                    <span class="d-none d-sm-inline">Account</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-1-1" data-bs-toggle="tab" data-toggle="tab"
                                   class="nav-link rounded-0 pt-2 pb-2">
                                    <span class="d-none d-sm-inline">Profile</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-2" data-bs-toggle="tab" data-toggle="tab"
                                   class="nav-link rounded-0 pt-2 pb-2">
                                    <span class="d-none d-sm-inline">Profile</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#tab-3" data-bs-toggle="tab" data-toggle="tab"
                                   class="nav-link rounded-0 pt-2 pb-2">
                                    <span class="d-none d-sm-inline">Finish</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#tab-4" data-bs-toggle="tab" data-toggle="tab"
                                   class="nav-link rounded-0 pt-2 pb-2">
                                    <span class="d-none d-sm-inline">Finish</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#tab-5" data-bs-toggle="tab" data-toggle="tab"
                                   class="nav-link rounded-0 pt-2 pb-2">
                                    <span class="d-none d-sm-inline">Finish</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="#tab-6" data-bs-toggle="tab" data-toggle="tab"
                                   class="nav-link rounded-0 pt-2 pb-2">
                                    <span class="d-none d-sm-inline">Finish</span>
                                </a>
                            </li>
                        </ul>

                        <div class="tab-content b-0 mb-0 pt-0">

                            <div id="bar" class="progress mb-2 progress-xl">
                                <div class="bar progress-bar progress-bar-striped progress-bar-animated"></div>
                            </div>


                            <div class="tab-pane" id="tab-1">
                                <div class="container">
                                    <div class="col-12 mj-employee-inputs">

                                        <div class="row mb-1">
                                            <h4 class="text-center"><?= $lang['u_company_tadbir']; ?></h4>
                                            <span><?= $lang['u_hire_notic_1']; ?></span>
                                        </div>

                                        <div class="row mb-1">
                                            <label class="col-form-label" for="form-name"><?= $lang['name']; ?>
                                                <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                            </label>
                                            <div class="mj-employee-inputs">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control"
                                                       id="form-name"
                                                       name="form-name"
                                                       placeholder="<?= $lang['u_enter_name']; ?>"
                                                       value="">
                                            </div>
                                        </div>

                                        <div class="row mb-1">
                                            <label class=" col-form-label"
                                                   for="form-lname"><?= $lang['b_lastname']; ?>
                                                <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                            </label>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control"
                                                       id="form-lname"
                                                       name="form-lname"
                                                       placeholder="<?= $lang['u_enter_lname']; ?>"
                                                       value="">
                                            </div>
                                        </div>

                                        <div class="row mb-1">
                                            <label class=" col-form-label"
                                                   for="form-father"><?= $lang['auth_father_name']; ?></label>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control"
                                                       id="form-father"
                                                       name="form-father"
                                                       placeholder="<?= $lang['u_enter_father_name']; ?>"
                                                       value="">
                                            </div>
                                        </div>

                                        <div class="row mb-1 d-none">
                                            <label class=" col-form-label"
                                                   for="form-birthday-location"><?= $lang['u_location_birthday']; ?></label>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control"
                                                       id="form-birthday-location"
                                                       name="form-birthday-location"
                                                       placeholder="<?= $lang['u_enter_location_birthday']; ?>"
                                                       value="">
                                            </div>
                                        </div>

                                        <div class="row mb-1">
                                            <label class=" col-form-label"
                                                   for="form-birthday-time"><?= $lang['auth_birthday_date']; ?></label>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="text"
                                                       inputmode="decimal"
                                                       class="form-control"
                                                       id="form-birthday-time"
                                                       name="form-birthday-time"
                                                       dir="ltr"
                                                       placeholder="1374-4-4"
                                                       value="">
                                            </div>
                                        </div>


                                        <div class="row mb-1">
                                            <label class=" col-form-label"
                                                   for="form-code-national"><?= $lang['u_code_melle']; ?>
                                                <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                            </label>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="tel"
                                                       dir="ltr"
                                                       inputmode="decimal"
                                                       class="form-control"
                                                       id="form-code-national"
                                                       name="form-code-national"
                                                       placeholder="136168****"
                                                       value="">
                                            </div>
                                        </div>

                                        <div class="row mb-1">
                                            <label class="col-form-label"
                                                   for="form-gender"><?= $lang['a_gender']; ?></label>
                                            <div class="col-12 mj-employee-inputs-grid ">
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-man"
                                                           name="form-gender"
                                                           class="form-check-input"
                                                           data-tj-val="man"
                                                           checked>
                                                    <label class="form-check-label"
                                                           for="form-man"><?= $lang['a_man']; ?></label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-female"
                                                           name="form-gender"
                                                           class="form-check-input"
                                                           data-tj-val="female">
                                                    <label class="form-check-label"
                                                           for="form-female"><?= $lang['a_female']; ?></label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-1" id="form-military-service">
                                            <label class="col-form-label"
                                                   for="form-military-service"><?= $lang['u_military_service']; ?></label>
                                            <div class="col-12 mj-employee-inputs mj-employee-inputs-grid">
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-military-end"
                                                           name="form-military-service"
                                                           class="form-check-input"
                                                           data-tj-val="end"
                                                           checked>
                                                    <label class="form-check-label"
                                                           for="form-military-end"><?= $lang['u_military_service_end']; ?></label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-military-sponsorship"
                                                           name="form-military-service"
                                                           class="form-check-input"
                                                           data-tj-val="sponsorship">
                                                    <label class="form-check-label"
                                                           for="form-military-sponsorship"><?= $lang['a_military_service_sponsorship']; ?></label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio" id="form-military-exempt"
                                                           name="form-military-service"
                                                           class="form-check-input"
                                                           data-tj-val="exempt">
                                                    <label class="form-check-label"
                                                           for="form-military-exempt"><?= $lang['a_military_service_exempt']; ?></label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row mb-1" style="display: none">
                                            <label class="col-form-label"
                                                   for="form-exemption-type"><?= $lang['a_military_service_exempt_type']; ?></label>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control"
                                                       id="form-exemption-type"
                                                       name="form-exemption-type"
                                                       placeholder="<?= $lang['u_enter_military_service_exempt_type']; ?>"
                                                       value="">
                                            </div>
                                        </div>


                                        <div class="row mb-1">
                                            <label class="col-form-label"
                                                   for="form-marital"><?= $lang['a_employ_marital']; ?></label>
                                            <div class="col-12 mj-employee-inputs-grid">
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-single"
                                                           name="form-marital"
                                                           class="form-check-input"
                                                           data-tj-val="single"
                                                           checked>
                                                    <label class="form-check-label"
                                                           for="form-single"><?= $lang['a_single']; ?></label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-married"
                                                           name="form-marital"
                                                           class="form-check-input"
                                                           data-tj-val="married">
                                                    <label class="form-check-label"
                                                           for="form-married"><?= $lang['a_married']; ?></label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row mb-1" style="display: none">
                                            <label class="col-form-label"
                                                   for="form-count-child"><?= $lang['u_number_child']; ?></label>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="number"
                                                       inputmode="decimal"
                                                       class="form-control"
                                                       id="form-count-child"
                                                       name="form-count-child"
                                                       placeholder="1"
                                                       value="">
                                            </div>
                                        </div>


                                        <div class="row mb-1">
                                            <label class="col-form-label"
                                                   for="form-home"><?= $lang['a_employ_home_status']; ?></label>
                                            <div class="col-12 mj-employee-inputs-grid">
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-home-personal"
                                                           name="form-home"
                                                           class="form-check-input"
                                                           data-tj-val="personal"
                                                           checked>
                                                    <label class="form-check-label"
                                                           for="form-home-personal"><?= $lang['a_employ_home_status_personal']; ?></label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-home-rental"
                                                           name="form-home"
                                                           class="form-check-input"
                                                           data-tj-val="rental">
                                                    <label class="form-check-label"
                                                           for="form-home-rental"><?= $lang['a_employ_home_status_rental']; ?></label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-home-father"
                                                           name="form-home"
                                                           class="form-check-input"
                                                           data-tj-val="father">
                                                    <label class="form-check-label"
                                                           for="form-home-father"><?= $lang['a_employ_home_status_father']; ?></label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row mb-1">
                                            <label class="col-form-label"
                                                   for="form-insurance"><?= $lang['a_employ_insurance_status']; ?></label>
                                            <div class="col-12 mj-employee-inputs-grid">
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-insurance-1"
                                                           name="form-insurance"
                                                           class="form-check-input"
                                                           data-tj-val="tamin"
                                                           checked>
                                                    <label class="form-check-label"
                                                           for="form-insurance-1"><?= $lang['a_employ_insurance_status_tamin']; ?>
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-insurance-2"
                                                           name="form-insurance"
                                                           class="form-check-input"
                                                           data-tj-val="darman">
                                                    <label class="form-check-label"
                                                           for="form-insurance-2"><?= $lang['a_employ_insurance_status_darman']; ?></label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-insurance-3"
                                                           name="form-insurance"
                                                           class="form-check-input"
                                                           data-tj-val="mosalah">
                                                    <label class="form-check-label"
                                                           for="form-insurance-3"><?= $lang['a_employ_insurance_status_mosalah']; ?></label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-insurance-4"
                                                           name="form-insurance"
                                                           class="form-check-input"
                                                           data-tj-val="no">
                                                    <label class="form-check-label"
                                                           for="form-insurance-4"><?= $lang['u_not_have']; ?></label>
                                                </div>
                                            </div>
                                        </div>


                                        <div id="hire-insurance" class="row mb-1">
                                            <label class="col-form-label"
                                                   for="form-insurance-time"><?= $lang['a_employ_insurance_date']; ?></label>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control"
                                                       id="form-insurance-time"
                                                       name="form-insurance-time"
                                                       placeholder="3 <?= $lang['month']; ?>"
                                                       value="">
                                            </div>
                                        </div>

                                        <div class="row mb-1 mt-2">
<!--                                            <label class=" col-form-label"-->
<!--                                                   for="form-live-location-country">--><?php //= $lang['u_employ_country']; ?>
<!--                                                <span class="text-danger mj-fw-300 mj-font-12">--><?php //= $lang['required'] ?><!--</span>-->
<!--                                            </label>-->
<!--                                            <div class="col-12 mj-employee-inputs">-->
<!--                                                <input type="text"-->
<!--                                                       inputmode="text"-->
<!--                                                       class="form-control"-->
<!--                                                       id="form-live-location-country"-->
<!--                                                       name="form-live-location-country"-->
<!--                                                       placeholder="--><?php //= $lang['u_employ_country']; ?><!--"-->
<!--                                                       value="">-->
<!--                                            </div>-->
                                            <label for="live-country"
                                                   class="text-dark mj-fw-500 mj-font-12 mb-1 col-form-label">
                                                <?= $lang['country'] ?>
                                            </label>
                                            <div class="mj-custom-select mj-live-country-hire cargo-origin-country">
                                                <select class="form-select width-95 my-1 mb-3 "
                                                        id="live-country"
                                                        name="live-country"
                                                        data-width="100%"
                                                        data-placeholder="<?= $lang['b_cargo_select_country'] ?>">
                                                    <option value="all-country"><?= $lang['b_filter_by_all'] ?></option>
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
                                        </div>
<!---->
<!--                                        <div class="row mb-1">-->
<!--                                            <label class=" col-form-label"-->
<!--                                                   for="form-live-location-state">--><?php //= $lang['u_employ_state']; ?>
<!--                                                <span class="text-danger mj-fw-300 mj-font-12">--><?php //= $lang['required'] ?><!--</span>-->
<!--                                            </label>-->
<!--                                            <div class="col-12 mj-employee-inputs">-->
<!--                                                <input type="text"-->
<!--                                                       inputmode="text"-->
<!--                                                       class="form-control"-->
<!--                                                       id="form-live-location-state"-->
<!--                                                       name="form-live-location-state"-->
<!--                                                       placeholder="--><?php //= $lang['u_employ_state']; ?><!--"-->
<!--                                                       value="">-->
<!--                                            </div>-->
<!--                                        </div>-->


                                        <div class="row mb-1">
                                            <label for="live-city"
                                                   class="text-dark mj-fw-500 mj-font-12 mb-1 col-form-label">
                                                <?= $lang['city'] ?>
                                            </label>
                                            <div class="mj-custom-select mj-live-city-hire cargo-origin">
                                                <select class="form-select width-95 my-1 mb-3"
                                                        id="live-city"
                                                        name="live-city"
                                                        data-width="100%"
                                                        data-placeholder="<?= $lang['b_cargo_select_cities'] ?>">
                                                    <option value=""></option>
                                                </select>
                                            </div>
<!--                                            <label class=" col-form-label"-->
<!--                                                   for="form-live-location-city">--><?php //= $lang['u_employ_city']; ?>
<!--                                                <span class="text-danger mj-fw-300 mj-font-12">--><?php //= $lang['required'] ?><!--</span>-->
<!--                                            </label>-->
<!--                                            <div class="col-12 mj-employee-inputs">-->
<!--                                                <input type="text"-->
<!--                                                       inputmode="text"-->
<!--                                                       class="form-control"-->
<!--                                                       id="form-live-location-city"-->
<!--                                                       name="form-live-location-city"-->
<!--                                                       placeholder="--><?php //= $lang['u_employ_city']; ?><!--"-->
<!--                                                       value="">-->
<!--                                            </div>-->
                                        </div>

                                    </div> <!-- end col -->
                                </div> <!-- end row -->
                            </div>

                            <div class="tab-pane" id="tab-1-1">
                                <div class="container">

                                    <div class="row mb-1">
                                        <h5 class="text-center"><?= $lang['u_contact_info']; ?></h5>
                                    </div>


                                    <div class="row mb-1">
                                        <label class="col-form-label"
                                               for="form-mobile"><?= $lang['login_phone_number']; ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="col-12 mj-employee-inputs">
                                            <input type="text"
                                                   inputmode="tel"
                                                   dir="ltr"
                                                   class="form-control"
                                                   id="form-mobile"
                                                   name="form-mobile"
                                                   placeholder="0914193****"
                                                   value="">
                                        </div>
                                    </div>

                                    <div class="row mb-1">
                                        <label class="col-form-label"
                                               for="form-phone"><?= $lang['u_home_number']; ?></label>
                                        <div class="col-12 mj-employee-inputs">
                                            <input type="text"
                                                   dir="ltr"
                                                   inputmode="tel"
                                                   class="form-control"
                                                   id="form-phone"
                                                   name="form-phone"
                                                   placeholder="041355****"
                                                   value="">
                                        </div>
                                    </div>

                                    <div class="row mb-1">
                                        <label class="col-form-label"
                                               for="form-address-location"><?= $lang['u_address_home']; ?></label>
                                        <div class="col-12 mj-employee-inputs">
                                    <textarea class="form-control"
                                              id="form-address-location"
                                              name="form-address-location"
                                              rows="2"
                                              placeholder="<?= $lang['u_enter_address_home']; ?>"></textarea>
                                        </div>
                                    </div>



                                    <div class="row mb-1">
                                        <label class="col-form-label"
                                               for="form-address-location"><?= $lang['u_hire_profile']; ?>
                                            <span class="text-danger mj-fw-300 mj-font-12"><?= $lang['required'] ?></span>
                                        </label>
                                        <div class="col-12 mj-employee-inputs">
                                            <div class="d-flex align-items-center">
                                                <div class="dropzone tj-dropzone mj-input-filter-box flex-fill"
                                                      id="profilePassport"
                                                      data-plugin="dropzone"
                                                      data-previews-container="#file-previews"
                                                      data-upload-preview-template="#uploadPreviewTemplate">
                                                    <div class="fallback">
                                                        <input type="file" name="file">
                                                    </div>
                                                    <div class="dz-message needsclick">
                                                        <img src="/dist/images/user-avatar.svg" class="mb-2" alt="">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mb-1">
                                        <label class="col-form-label"
                                               for="form-address-location"><?= $lang['u_file_cv']; ?></label>
                                        <div class="col-12 mj-employee-inputs">
                                            <div class="d-flex align-items-center">
                                                <div class="dropzone tj-dropzone mj-input-filter-box flex-fill"
                                                     id="cvPassport"
                                                     data-plugin="dropzone"
                                                     data-previews-container="#file-previews"
                                                     data-upload-preview-template="#uploadPreviewTemplate">
                                                    <div class="fallback">
                                                        <input type="file" name="file">
                                                    </div>
                                                    <div class="dz-message needsclick">

                                                        <div class="fa-duotone fa-folder-plus text-info"></div>
                                                        <h5><?= $lang['auth_image_upload_desc'] ?></h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="tab-pane" id="tab-2">
                                <div class="container">

                                    <div class="row mb-1">
                                        <h5 class="text-center"><?= $lang['u_employ_company']; ?></h5>
                                        <span><?= $lang['u_enter_employ_company']; ?></span>
                                    </div>

                                    <div class="repeater">
                                        <div data-repeater-list="group-a">
                                            <div data-repeater-item>

                                                <div class="row mb-1">
                                                    <label class="col-form-label"
                                                           for="form-company"><?= $lang['name_company']; ?>
                                                    </label>
                                                    <div class="col-12 mj-employee-inputs">
                                                        <input type="text"
                                                               inputmode="text"
                                                               name="formCompanyName"
                                                               class="form-control"
                                                               placeholder="<?= $lang['u_enter_company_name']; ?>"
                                                               value=""/>

                                                    </div>
                                                </div>

                                                <div class="row mb-1">
                                                    <label class="col-form-label"
                                                           for="form-company-time-start"><?= $lang['date_start']; ?></label>
                                                    <div class="col-12 mj-employee-inputs">
                                                        <input type="text"
                                                               inputmode="decimal"
                                                               dir="ltr"
                                                               name="formCompanyTimeStart"
                                                               class="form-control"
                                                               placeholder="1400-01-01"
                                                               value=""/>

                                                    </div>
                                                </div>

                                                <div class="row mb-1">
                                                    <label class="col-form-label"
                                                           for="form-company-time-end"><?= $lang['date_end']; ?></label>
                                                    <div class="col-12 mj-employee-inputs">
                                                        <input type="text"
                                                               inputmode="decimal"
                                                               dir="ltr"
                                                               name="formCompanyTimeEnd"
                                                               class="form-control"
                                                               placeholder="1400-01-01"
                                                               value=""/>

                                                    </div>
                                                </div>

                                                <div class="row mb-1">
                                                    <label class="col-form-label"
                                                           for="orm-company-left-reason"><?= $lang['u_left_work_reason']; ?></label>
                                                    <div class="col-12 mj-employee-inputs">
                                                        <input type="text"
                                                               inputmode="text"
                                                               name="formCompanyLeftReason"
                                                               class="form-control"
                                                               placeholder="<?= $lang['u_left_work_reason']; ?>"
                                                               value=""/>

                                                    </div>
                                                </div>

                                                <div class="row mb-1">
                                                    <div data-repeater-delete
                                                         class="col-12 mj-employee-delete-history-btn">
                                                        <i class="mdi mdi-minus-circle font-16 text-white "></i>
                                                        <span><?= $lang['u_delete_item']; ?></span>
                                                    </div>
                                                </div>
                                                <hr>
                                            </div>
                                        </div>
                                        <div data-repeater-create class="mj-employee-history-btn">
                                            <div
                                                    class="mdi mdi-plus-circle-outline text-white font-17"></div>
                                            <span><?= $lang['u_add_item_1']; ?></span>
                                        </div>


                                    </div>

                                </div>
                            </div>

                            <div class="tab-pane" id="tab-3">
                                <div class="container">
                                    <div class="col-12 mj-employee-inputs">

                                        <div class="row mb-1">
                                            <h4 class="text-center"><?= $lang['a_education_list']; ?></h4>
                                        </div>

                                        <div class="row mb-1">
                                            <label class="col-form-label"
                                                   for="form-edu-name-1"><?= $lang['a_education_1']; ?></label>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control"
                                                       id="form-edu-name-1"
                                                       name="form-edu-name-1"
                                                       placeholder="<?= $lang['u_education_1_title']; ?>"
                                                       value="">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control mt-1"
                                                       id="form-edu-address-1"
                                                       name="form-edu-address-1"
                                                       placeholder="<?= $lang['a_education_location']; ?>"
                                                       value="">
                                            </div>
                                        </div>

                                        <div class="row mb-1">
                                            <label class="col-form-label"
                                                   for="form-edu-name-2"><?= $lang['a_education_2']; ?></label>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control"
                                                       id="form-edu-name-2"
                                                       name="form-edu-name-2"
                                                       placeholder="<?= $lang['u_education_2_title']; ?>"
                                                       value="">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control mt-1"
                                                       id="form-edu-address-2"
                                                       name="form-edu-address-2"
                                                       placeholder="<?= $lang['a_education_location']; ?>"
                                                       value="">
                                            </div>
                                        </div>

                                        <div class="row mb-1">
                                            <label class="col-form-label"
                                                   for="form-edu-name-3"><?= $lang['a_education_3']; ?></label>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control"
                                                       id="form-edu-name-3"
                                                       name="form-edu-name-3"
                                                       placeholder="<?= $lang['u_education_3_title']; ?>"
                                                       value="">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control mt-1"
                                                       id="form-edu-address-3"
                                                       name="form-edu-address-3"
                                                       placeholder="<?= $lang['a_education_location']; ?>"
                                                       value="">
                                            </div>
                                        </div>

                                        <div class="row mb-1">
                                            <label class="col-form-label"
                                                   for="form-edu-name-4"><?= $lang['a_education_4']; ?></label>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control"
                                                       id="form-edu-name-4"
                                                       name="form-edu-name-4"
                                                       placeholder="<?= $lang['u_education_4_title']; ?>"
                                                       value="">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control mt-1"
                                                       id="form-edu-address-4"
                                                       name="form-edu-address-4"
                                                       placeholder="<?= $lang['a_education_location']; ?>"
                                                       value="">
                                            </div>
                                        </div>

                                        <div class="row mb-1">
                                            <label class="col-form-label"
                                                   for="form-edu-name-5"><?= $lang['a_education_5']; ?></label>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control"
                                                       id="form-edu-name-5"
                                                       name="form-edu-name-5"
                                                       placeholder="<?= $lang['u_education_4_title']; ?>"
                                                       value="">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control mt-1"
                                                       id="form-edu-address-5"
                                                       name="form-edu-address-5"
                                                       placeholder="<?= $lang['a_education_location']; ?>"
                                                       value="">
                                            </div>
                                        </div>


                                    </div> <!-- end col -->
                                </div>
                            </div>

                            <div class="tab-pane" id="tab-4">
                                <div class="container">
                                    <div class="col-12 mj-employee-inputs">

                                        <div class="row mb-1">
                                            <h4 class="text-center"><?= $lang['a_training_courses_degrees']; ?></h4>
                                            <span><?= $lang['u_enter_training_courses_degrees']; ?></span>
                                        </div>

                                        <div class="repeater-2">
                                            <div data-repeater-list="group-b">
                                                <div data-repeater-item>

                                                    <div class="row mb-1">
                                                        <label class="col-form-label"
                                                               for="form-record-title"><?= $lang['title']; ?></label>
                                                        <div class="col-12 mj-employee-inputs">
                                                            <input type="text"
                                                                   inputmode="text"
                                                                   name="formRecordTitle"
                                                                   class="form-control"
                                                                   placeholder="<?= $lang['u_course_title']; ?>"
                                                                   value=""/>

                                                        </div>
                                                    </div>

                                                    <div class="row mb-1">
                                                        <label class="col-form-label"
                                                               for="form-record-address"><?= $lang['u_address_company_2']; ?></label>
                                                        <div class="col-12 mj-employee-inputs">
                                                            <input type="text"
                                                                   inputmode="text"
                                                                   name="formRecordAddress"
                                                                   class="form-control"
                                                                   placeholder="<?= $lang['u_address_company_2']; ?>"
                                                                   value=""/>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-1">
                                                        <label class="col-form-label"
                                                               for="form-record-period"><?= $lang['u_course_time']; ?></label>
                                                        <div class="col-12 mj-employee-inputs">
                                                            <input type="text"
                                                                   inputmode="text"
                                                                   name="formRecordPeriod"
                                                                   class="form-control"
                                                                   placeholder="<?= $lang['u_course_time']; ?>"
                                                                   value=""/>

                                                        </div>
                                                    </div>

                                                    <div class="row mb-1">
                                                        <div data-repeater-delete
                                                             class="col-12 mj-employee-delete-history-btn">
                                                            <i class="mdi mdi-minus-circle font-16 text-white "></i>
                                                            <span><?= $lang['u_delete_item']; ?></span>
                                                        </div>
                                                    </div>

                                                    <hr>
                                                </div>
                                            </div>
                                            <div data-repeater-create class="mj-employee-history-btn">

                                                <div class="mdi mdi-plus-circle-outline text-white font-17"></div>
                                                <span><?= $lang['u_add_item_2']; ?></span>
                                            </div>

                                        </div>


                                    </div> <!-- end col -->
                                </div>
                            </div>

                            <div class="tab-pane" id="tab-5">
                                <div class="container">
                                    <div class="col-12 mj-employee-inputs">

                                        <div class="row mb-1">
                                            <h4 class="text-center"><?= $lang['u_language_title']; ?></h4>
                                        </div>

                                        <div class="repeater-3">
                                            <div data-repeater-list="group-c">
                                                <div data-repeater-item>

                                                    <div class="row mb-1">
                                                        <label class="col-form-label"
                                                               for="form-language-title"><?= $lang['language']; ?></label>
                                                        <div class="col-12 mj-employee-inputs">
                                                            <input type="text"
                                                                   inputmode="text"
                                                                   name="formLanguageTitle"
                                                                   class="form-control"
                                                                   placeholder="<?= $lang['language']; ?>"
                                                                   value=""/>

                                                        </div>
                                                    </div>

                                                    <div class="row mb-1">
                                                        <label class="col-form-label"
                                                               for="form-language-talk"><?= $lang['a_languages_talk']; ?></label>
                                                        <div class="col-12 mj-employee-inputs">
                                                            <div class="form-check">
                                                                <input type="radio"
                                                                       name="formLanguageTalk"
                                                                       data-tj-val="1"
                                                                       value="1"
                                                                       class="form-check-input"
                                                                       checked id="speak-D">
                                                                <label class="form-check-label" for="speak-D"><?= $lang['a_star_1']; ?></label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio"
                                                                       name="formLanguageTalk"
                                                                       data-tj-val="2"
                                                                       value="2"
                                                                       class="form-check-input" id="speak-C">
                                                                <label class="form-check-label" for="speak-C"><?= $lang['a_star_2']; ?></label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio"
                                                                       name="formLanguageTalk"
                                                                       data-tj-val="3"
                                                                       value="3"
                                                                       class="form-check-input" id="speak-B">
                                                                <label class="form-check-label" for="speak-B"><?= $lang['a_star_3']; ?></label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio"
                                                                       name="formLanguageTalk"
                                                                       data-tj-val="4"
                                                                       value="4"
                                                                       class="form-check-input" id="speak-A">
                                                                <label class="form-check-label" for="speak-A"><?= $lang['a_star_4']; ?></label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-1">
                                                        <label class=" col-form-label"
                                                               for="form-language-read"><?= $lang['a_languages_read']; ?></label>
                                                        <div class="col-12 mj-employee-inputs">
                                                            <div class="form-check">
                                                                <input type="radio"
                                                                       data-tj-val="1"
                                                                       value="1"
                                                                       name="formLanguageRead"
                                                                       class="form-check-input"
                                                                        id="read-D"
                                                                       checked>
                                                                <label class="form-check-label " for="read-D"><?= $lang['a_star_1']; ?></label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio"
                                                                       data-tj-val="2"
                                                                       value="2"
                                                                       name="formLanguageRead"
                                                                       class="form-check-input" id="read-C">
                                                                <label class="form-check-label" for="read-C"><?= $lang['a_star_2']; ?></label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio"
                                                                       data-tj-val="3"
                                                                       value="3"
                                                                       name="formLanguageRead"
                                                                       class="form-check-input" id="read-B">
                                                                <label class="form-check-label" for="read-B"><?= $lang['a_star_3']; ?></label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio"
                                                                       data-tj-val="4"
                                                                       value="4"
                                                                       name="formLanguageRead"
                                                                       class="form-check-input" id="read-A">
                                                                <label class="form-check-label" for="read-A"><?= $lang['a_star_4']; ?></label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-1">
                                                        <label class=" col-form-label"
                                                               for="form-language-write"><?= $lang['a_languages_write']; ?></label>
                                                        <div class="col-12 mj-employee-inputs">
                                                            <div class="form-check">
                                                                <input type="radio"
                                                                       data-tj-val="1"
                                                                       value="1"
                                                                       name="formLanguageWrite"
                                                                       class="form-check-input"
                                                                        id="write-D"

                                                                       checked>
                                                                <label class="form-check-label"
                                                                       for="write-D"><?= $lang['a_star_1']; ?></label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio"
                                                                       data-tj-val="2"
                                                                       value="2"
                                                                       name="formLanguageWrite"
                                                                       class="form-check-input"
                                                                id="write-C">
                                                                <label class="form-check-label"
                                                                     for="write-C"><?= $lang['a_star_2']; ?></label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio"
                                                                       data-tj-val="3"
                                                                       value="3"
                                                                       name="formLanguageWrite"
                                                                       class="form-check-input"
                                                                id="write-B">

                                                                <label class="form-check-label"
                                                                      for="write-B"><?= $lang['a_star_3']; ?></label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio"
                                                                       data-tj-val="4"
                                                                       value="4"
                                                                       name="formLanguageWrite"
                                                                       class="form-check-input"
                                                                id="write-A">
                                                                <label class="form-check-label"
                                                                       for="write-A"><?= $lang['a_star_4']; ?></label>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row mb-1">
                                                        <div data-repeater-delete
                                                             class="col-12 mj-employee-delete-history-btn">
                                                            <i class="mdi mdi-minus-circle font-16 text-white "></i>
                                                            <span><?= $lang['u_delete_item']; ?></span>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </div>
                                            <div data-repeater-create class="mj-employee-history-btn">
                                                <div
                                                        class="mdi mdi-plus-circle-outline text-white font-17"></div>
                                                <span><?= $lang['u_add_language_title']; ?></span>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane" id="tab-6">
                                <div class="container">
                                    <div class="col-12 mj-employee-inputs">

                                        <div class="row mb-1">
                                            <h4 class="text-center"><?= $lang['all_info']; ?></h4>
                                        </div>


                                        <div class="row mb-1">
                                            <label class="col-12 mj-employee-inputs col-form-label"
                                                   for="form-work"><?= $lang['u_employ_category_request']; ?></label>
                                            <div class="col-4"></div>
                                            <div class="col-12 mj-employee-inputs mj-jobs-dz" data-dz-jobs="jobs">
                                                <div class="mj-custom-select">
                                                    <select class="form-select my-1 mb-3"
                                                            multiple="multiple"
                                                            id="form-hire-title-category"
                                                            name="form-hire-title-category"
                                                            data-width="100%"
                                                            data-placeholder="<?= $lang['u_employ_category_request'] ?>">
                                                        <option value=""></option>
                                                        <?php
                                                        $categories = Hire::getHireTitle()->response;
                                                        foreach ($categories as $loop) {
                                                            ?>
                                                            <option value="<?= $loop->category_id ?>"><?=  (!empty(array_column(json_decode($loop->category_name, true), 'value', 'slug')[$_COOKIE['language']])) ?
                                                                    array_column(json_decode($loop->category_name, true), 'value', 'slug')[$_COOKIE['language']] :  $loop->category_id;?></option>
                                                            <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row mb-1">
                                            <label class="col-12 mj-employee-inputs col-form-label"
                                                   for="form-work"><?= $lang['u_do_you_work_new']; ?></label>
                                            <div class="col-4"></div>
                                            <div class="col-12 mj-employee-inputs">
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-work-yes"
                                                           name="form-work"
                                                           value="yes"
                                                           class="form-check-input"
                                                           checked>
                                                    <label class="form-check-label"
                                                           for="form-work-yes"><?= $lang['a_yes']; ?></label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-work-no"
                                                           name="form-work"
                                                           value="no"
                                                           class="form-check-input">
                                                    <label class="form-check-label"
                                                           for="form-work-no"><?= $lang['a_no']; ?></label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-1">
                                            <label class="col-12 mj-employee-inputs col-form-label"
                                                   for="form-guarantee"><?= $lang['u_do_you_tazmin']; ?></label>
                                            <div class="col-4"></div>
                                            <div class="col-12 mj-employee-inputs">
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-guarantee-yes"
                                                           value="yes"
                                                           name="form-guarantee"
                                                           class="form-check-input"
                                                           checked>
                                                    <label class="form-check-label"
                                                           for="form-guarantee-yes"><?= $lang['a_yes']; ?></label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-guarantee-no"
                                                           value="no"
                                                           name="form-guarantee"
                                                           class="form-check-input">
                                                    <label class="form-check-label"
                                                           for="form-guarantee-no"><?= $lang['a_no']; ?></label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row mb-1">
                                            <label class="col-12 mj-employee-inputs col-form-label"
                                                   for="form-transfer"><?= $lang['u_are_you_ready_to_go_to_other_cities_or_branches']; ?></label>
                                            <div class="col-4"></div>
                                            <div class="col-12 mj-employee-inputs">
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-transfer-yes"
                                                           value="yes"
                                                           name="form-transfer"
                                                           class="form-check-input"
                                                           checked>
                                                    <label class="form-check-label"
                                                           for="form-transfer-yes"><?= $lang['a_yes']; ?></label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-transfer-no"
                                                           value="no"
                                                           name="form-transfer"
                                                           class="form-check-input">
                                                    <label class="form-check-label"
                                                           for="form-transfer-no"><?= $lang['a_no']; ?></label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mb-1">
                                            <label class="col-12 mj-employee-inputs col-form-label"
                                                   for="form-price"><?= $lang['u_enter_min_price']; ?></label>
                                            <div class="col-4"></div>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="text"
                                                       dir="ltr"
                                                       inputmode="decimal"
                                                       class="form-control"
                                                       id="form-price"
                                                       name="form-price"
                                                       placeholder="<?= $lang['u_enter_min_price_2']; ?>"
                                                       value="">
                                            </div>
                                        </div>


                                        <div class="row mb-1">
                                            <label class="col-12 mj-employee-inputs col-form-label"
                                                   for="form-representative-name"><?= $lang['u_representative_name']; ?></label>
                                            <div class="col-4"></div>
                                            <div class="col-12 mj-employee-inputs">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control"
                                                       id="form-representative-name"
                                                       name="form-representative-name"
                                                       placeholder="<?= $lang['a_employ_representative_name']; ?>"
                                                       value="">

                                                <input type="text"
                                                       dir="ltr"
                                                       inputmode="tel"
                                                       class="form-control mt-1"
                                                       id="form-representative-phone"
                                                       name="form-representative-phone"
                                                       placeholder="0914193****"
                                                       value="">

                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control mt-1"
                                                       id="form-representative-job"
                                                       name="form-representative-job"
                                                       placeholder="<?= $lang['a_employ_representative_job']; ?>"
                                                       value="">
                                                <input type="text"
                                                       inputmode="text"
                                                       class="form-control mt-1"
                                                       id="form-representative-address"
                                                       name="form-representative-address"
                                                       placeholder="<?= $lang['a_employ_representative_address']; ?>"
                                                       value="">

                                            </div>
                                        </div>


                                        <div class="row mb-1">
                                            <label class="col-12  col-form-label"
                                                   for="form-employ"><?= $lang['u_how_to_find_out_about_employment']; ?></label>
                                            <div class="col-4"></div>
                                            <div class="col-12 mj-employee-inputs">
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-employ-post"
                                                           name="form-employ"
                                                           value="post"
                                                           class="form-check-input"
                                                           checked>
                                                    <label class="form-check-label"
                                                           for="form-employ-post"><?= $lang['a_employ_employ_post']; ?></label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-employ-relatives"
                                                           name="form-employ"
                                                           value="relatives"
                                                           class="form-check-input">
                                                    <label class="form-check-label" for="form-employ-relatives"><?= $lang['a_employ_employ_relatives']; ?></label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-employ-jober"
                                                           name="form-employ"
                                                           value="jober"
                                                           class="form-check-input">
                                                    <label class="form-check-label"
                                                           for="form-employ-jober"><?= $lang['a_employ_employ_jober']; ?></label>
                                                </div>
                                                <div class="form-check">
                                                    <input type="radio"
                                                           id="form-employ-other"
                                                           name="form-employ"
                                                           value="other"
                                                           class="form-check-input">
                                                    <label class="form-check-label" for="form-employ-other"><?= $lang['u_hire_2']; ?></label>
                                                </div>
                                            </div>
                                        </div>


                                    </div> <!-- end col -->
                                </div>
                            </div>


                            <ul class="list-inline mb-0 wizard">
                                <li class="previous list-inline-item">
                                    <a href="javascript: void(0);"
                                       class="btn btn-secondary mj-employee-perv-btn"><?= $lang['previous_level']; ?></a>
                                </li>
                                <li class="next list-inline-item float-end">
                                    <a href="javascript: void(0);"
                                       class="btn btn-secondary mj-employee-next-btn"><?= $lang['u_next']; ?></a>
                                </li>
                            </ul>

                        </div> <!-- tab-content -->
                    </div> <!-- end #progressbarwizard-->
                </form>

            </div>
        </div>
    </main>
    <input type="hidden" id="token" name="token"
           value="<?= Security::initCSRF('employ') ?>">
<?php
getFooter('', false);