<?php

global $lang, $Settings;

use MJ\Utils\Utils;

include_once 'header-footer.php';
enqueueStylesheet('FA-css', '/dist/libs/fontawesome/all.min.css');
enqueueStylesheet('FA-css', '/dist/libs/select2/css/select2.min.css');
enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/dropzone6.css');

enqueueScript('FA-js', '/dist/libs/select2/js/select2.min.js');


enqueueScript('FA-js', '/dist/libs/fontawesome/all.min.js');
enqueueScript('accounts-js', '/dist/libs/lottie/lottie-player.js');
enqueueScript('add-js', '/dist/js/user/drivers-add.js');
enqueueScript('dropzone-js', '/dist/libs/dropzone/dropzone-min6.js');

getHeader($lang['d_faq_title']);


?>


    <section style="padding: 120px 10px 180px 10px;">
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
            <h5 class="mt-4 mb-2">لطفا اطلاعات خود را به دقت وارد نمائید</h5>
            <div>
                <div class="row">
                    <div class="form-floating mb-1 col-5 no-pad-left">
                        <input type="email" class="form-control mj-cv-add-input" id="firstname"
                               placeholder="name@example.com">
                        <label class="mj-floating-labels" for="firstname">نام</label>
                    </div>
                    <div class="form-floating mb-1 col-7 no-pad-right">
                        <input type="email" class="form-control mj-cv-add-input" id="lname"
                               placeholder="name@example.com">
                        <label for="lname">نام خانوادگی</label>
                    </div>
                    <div class="form-floating mb-1 col-6 no-pad-left">
                        <input type="email" class="form-control mj-cv-add-input" id="lname"
                               placeholder="name@example.com">
                        <label for="lname">نام خانوادگی</label>
                    </div>
                    <div class="form-floating mb-1 col-6 no-pad-right mj-select2-selects">
                        <select id="sex-type" class="form-select" aria-label="Default select example">
                            <option value=""></option>
                            <option value="1">خانم</option>
                            <option value="2">آقا</option>

                        </select>
                    </div>

                    <div class="form-floating mb-1 col-12 mj-select2-selects">
                        <select id="marriage-select" class="form-select" aria-label="Default select example">
                            <option value=""></option>
                            <option value="1">مجرد</option>
                            <option value="2">متاهل</option>
                        </select>
                    </div>
                    <div class="form-floating mb-1 col-6 no-pad-left mj-select2-selects">
                        <select id="country-select" class="form-select" aria-label="Default select example">
                            <option value=""></option>
                            <option value="1">ایران</option>
                            <option value="2">ترکیه</option>
                        </select>
                    </div>
                    <div class="form-floating mb-1 col-6 no-pad-right mj-select2-selects">
                        <select id="city-select" class="form-select" aria-label="Default select example">
                            <option value=""></option>
                            <option value="1">تبریز</option>
                            <option value="2">تهران</option>
                        </select>
                    </div>

                    <!--                    dropzpne soldier start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title">پایان خدمت :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1"
                                       value="option1">
                                <label id="soldier-yes" class="form-check-label" for="exampleRadios1" data-state="true">
                                    دارم
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2"
                                       value="option2">
                                <label id="soldier-no" class="form-check-label" for="exampleRadios2">
                                    ندارم
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="soldier-input1">

                    </div>

                    <!--                    dropzpne soldier end-->

                    <!--                    dropzpne ai cart start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title">کارت هوشمند :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="aicard" id="exampleRadios3"
                                       value="option1">
                                <label id="ai-card-yes" class="form-check-label ai-card-yes" for="exampleRadios3"
                                       data-state="true">
                                    دارم
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="aicard" id="exampleRadios4"
                                       value="option2">
                                <label id="ai-card-no" class="form-check-label" for="exampleRadios4">
                                    ندارم
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="ai-card-input1">


                    </div>

                    <!--                    dropzpne ai cart end-->

                    <!--                    dropzpne passport start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title">پاسپورت :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="passport" id="exampleRadios5"
                                       value="option1">
                                <label id="passport-yes" class="form-check-label" for="exampleRadios5"
                                       data-state="true">
                                    دارم
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="passport" id="exampleRadios6"
                                       value="option2">
                                <label id="passport-no" class="form-check-label" for="exampleRadios6">
                                    ندارم
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="passport-input1">

                    </div>

                    <!--                    dropzpne passport end-->

                    <!--                    dropzpne visa start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title">ویزا :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visa" id="exampleRadios7"
                                       value="option1">
                                <label id="visa-yes" class="form-check-label" for="exampleRadios7" data-state="true">
                                    دارم
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="visa" id="exampleRadios8"
                                       value="option2">
                                <label id="visa-no" class="form-check-label" for="exampleRadios8">
                                    ندارم
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="visa-input1">


                    </div>

                    <!--                    dropzpne visa end-->


                    <!--                    dropzpne work book start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title">دفتچه کار :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="workbook" id="exampleRadios9"
                                       value="option1">
                                <label id="workbook-yes" class="form-check-label" for="exampleRadios9"
                                       data-state="true">
                                    دارم
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="workbook" id="exampleRadios10"
                                       value="option2">
                                <label id="workbook-no" class="form-check-label" for="exampleRadios10">
                                    ندارم
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="workbook-input1">


                    </div>

                    <!--                    dropzpne work book end-->


                    <!--                    dropzpne drive license start-->

                    <div class="mj-radio-cv-item-row orm-floating mb-1 col-12 ">
                        <div class="mj-cv-item-title">گواهینامه :</div>
                        <div class="mj-radio-cv-item-row-btn">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="drivelicense" id="exampleRadios11"
                                       value="option1">
                                <label id="drivelicense-yes" class="form-check-label" for="exampleRadios11"
                                       data-state="true">
                                    دارم
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="drivelicense" id="exampleRadios12"
                                       value="option2">
                                <label id="drivelicense-no" class="form-check-label" for="exampleRadios12">
                                    ندارم
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
                            <label class="mj-floating-labels" for="phonenumber">شماره همراه</label>
                        </div>
                        <div class="form-floating mb-1 col-12 ">
                            <input type="number" class="form-control mj-cv-add-input" id="whatsappnumber"
                                   placeholder="09143302964">
                            <label class="mj-floating-labels" for="whatsappnumber">شماره واتساپ</label>
                        </div>

                        <div class="form-floating mb-1 col-12 ">
                                <textarea type="text" class="form-control mj-cv-add-input " id="address"
                                          placeholder="آدرس" cols="30" rows="10"></textarea>
                            <label class="mj-floating-labels" for="address">آدرس خود را وارد کنید</label>
                        </div>
                    <div class="form-floating mb-1 col-12 mj-select2-selects">
                        <select id="fav-road-select" class="form-select" aria-label="Default select example" multiple="multiple">
                            <option value=""></option>
                            <option value="1">قسطنتنیه برای</option>
                            <option value="2">متاهل</option>
                            <option value="3">11</option>
                            <option value="4">22</option>
                        </select>
                    </div>

                    <form action="/action_page.php" style="margin: 10px;display:flex;align-items: center;">
                        <input type="checkbox" id="contract" name="contract">
                        <label style="padding-right: 5px" for="contract">اجازه پخش میدهم</label>
                    </form>
                    <div class="col-12 mj-cv-add-button">
                        <button type="submit">ثبت و ارسال</button>
                    </div>

                    



                    <!--                    phone number license end-->
                </div>
    </section>


<?php
getFooter('', false);