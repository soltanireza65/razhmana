<?php
global $lang;

if (User::userIsLoggedIn()) {
    include_once getcwd() . '/views/user/header-footer.php';

    enqueueStylesheet('poster-css', '/dist/css/poster/poster.css');
    enqueueStylesheet('fontawesome-css', '/dist/libs/fontawesome/all.css');


    // Load Script In Footer

//    enqueueScript('fontawesome-js', '/dist/libs/fontawesome/all.min.js');
    enqueueScript('report-js', '/dist/js/poster/report-poster.js');


    getHeader($lang['home']);
    ?>
    <style>
        body {
            background-color: #FFFFFF !important;
        }
    </style>
    <main class="container" style="padding-top: 135px !important; ">

        <!-- Start General 1 -->
        <div class="row">
            <h4>مشکل در کدام قسمت آگهی می باشد؟!</h4>
            <p class="font-12">نزدیک ترین گزینه را انتخاب کنید ، کارشناسان ما گزارش شما را بررسی می کنند.</p>


            <div class="col-12">
                <div class="mj-a-radio-poster mt-1">
                    <input id="report-1" class="custom-radio" type="radio"
                           name="report">
                    <label for="report-1">کلاهبرداری ، نقض قانون یا وقوع جرم</label>
                </div>
            </div>

            <div class="col-12 mt-1">
                <div class="mj-a-radio-poster mt-1">
                    <input id="report-2" class="custom-radio" type="radio"
                           name="report">
                    <label for="report-2">محتوای آگهی </label>
                </div>
            </div>

            <div class="col-12 mt-1">
                <div class="mj-a-radio-poster mt-1">
                    <input id="report-3" class="custom-radio" type="radio"
                           name="report">
                    <label for="report-3">مشکلات با صاحب آگهی</label>
                </div>
            </div>

            <div class="col-12 mt-1">
                <div class="mj-a-radio-poster mt-1">
                    <input id="report-other" class="custom-radio" type="radio"
                           name="report">
                    <label for="report-other">سایر</label>
                </div>
            </div>

            <div class="col-12 mt-1">
                <div class="m-2 mj-a-height-0" id="report-other-div">
                        <textarea type="text"
                                  inputmode="text"
                                  class="form-control mj-a-textarea-poster mt-2"
                                  id="desc-report-other"
                                  lang="en"
                                  placeholder="کلا با یارو مشکل دارم"
                                  style="min-height: 38px;"></textarea>
                </div>
            </div>

            <div class="col-12">
                <div class="row">
                    <div class="col-6 ps-3">
                        <button class="btn w-100 py-2 mj-a-btn-cancel">انصراف</button>
                    </div>
                    <div class="col-6 pe-3"><a class="btn w-100 py-2 mj-a-btn-submit">تایید</a></div>
                </div>
            </div>

        </div>
        <!-- End General 1-->

    </main>
    <?php
    getFooter('', false);
} else {
    header('location: /login');
}