<?php
$pageSlug = "tester";
// permission_can_show

global $lang;

include_once getcwd() . '/views/admin/header-footer.php';

// start roles 1
$resultCheckAdminLogin = Admin::checkAdminLogin();
$dataCheckAdminLogin = [];
if ($resultCheckAdminLogin->status == 200 && !empty($resultCheckAdminLogin->response)) {
    $dataCheckAdminLogin = $resultCheckAdminLogin->response;

    if ($dataCheckAdminLogin->admin_status == "active") {


        $dataCheckAdminRoleForCheck = [];
        if (!empty($dataCheckAdminLogin->role_id)) {
            $resultCheckAdminRoleForCheck = Admin::checkAdminRoleForCheck($dataCheckAdminLogin->role_id);
            if ($resultCheckAdminRoleForCheck->status == 200) {
                $dataCheckAdminRoleForCheck = $resultCheckAdminRoleForCheck->response;
            }
        }


        $flagSlug = false;
        if (!empty($dataCheckAdminRoleForCheck) && json_decode($dataCheckAdminRoleForCheck)->role_status == "active") {
            foreach (json_decode($dataCheckAdminRoleForCheck)->permissons as $item000) {
                if ($item000->slug_name == $pageSlug && $item000->permission_can_show == "yes") {
                    $flagSlug = true;
                }
            }
        }
// end roles 1
        //custom css


        // Load Stylesheets & Icons
        enqueueStylesheet('baguette-css', '/dist/libs/lighbox/baguetteBox.css');
        enqueueStylesheet('fa-css', '/dist/libs/fontawesome/all.min.css');
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
        enqueueStylesheet('product-detail-css', '/dist/css/admin/shop/product-detail.css');

        // Load Script In Footer
        enqueueScript('fa-js', '/dist/libs/fontawesome/all.min.js');
        enqueueScript('baguette-js', '/dist/libs/lighbox/baguetteBox.js');
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
        enqueueScript('data-table', '/dist/js/admin/data-table.init.js');
        enqueueScript('product-detail-js', '/dist/js/admin/shop/product-detail.js');
        // header text

        getHeader($lang["driver_cv_list"], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => $pageSlug,
            'pageSlugValue' => 'permission_can_show',
        ]);

        // start roles 2
        if ($flagSlug) {
            // end roles 2
            ?>


            <!-- reject product modal start -->
            <div class="modal fade" id="reject-product-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">تاییدیه رد محصول</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mj-divider"></div>
                            <div class="d-flex align-items-center gap-1 mb-3">

                                <div class="fa fa-circle-notch text-danger"></div>
                                <span>
                                    دراین بخش شما (ادمین) باید دلایل رد خود را برای کمک به فروشنده در جهت رفع آن ها را وارد کنید
                                </span>

                            </div>
                            <div class="mj-normal-input">
                                <textarea name="reject-desc " id="admin-reject-desc" cols="30" rows="10"
                                          placeholder="دلیل رد محصول ..."></textarea>
                            </div>
                            <div class="d-flex align-items-center gap-1 mt-3">
                                <div class="fa fa-circle-info text-info"></div>
                                <span>
                                    لطفا بعد نوشتن هر مورد دکمه Enter را بزنید تا در نمایش یه فروشنده مشکلی وجود نداشته باشد
                                </span>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="javascript:void(0)"
                               class="btn btn-danger rounded-pill waves-effect waves-light mj-icon-rounded-btn"
                               id="reject-product">
                                <div class="mdi mdi-close"></div>
                                <span>
                                         رد محصول
                                </span>
                            </a>


                        </div>
                    </div>
                </div>
            </div>
            <!-- reject product modal end -->


            <!-- accept product modal start -->
            <div class="modal fade" id="accept-product-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">تاییدیه تایید محصول</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mj-divider"></div>
                            <div class="d-flex align-items-center gap-1 justify-content-center mb-3">

                                <div class="fa fa-circle-notch text-danger"></div>
                                <h4>
                                    آیا از تایید محصول اطمینان دارید؟
                                </h4>

                            </div>

                        </div>
                        <div class="modal-footer justify-content-center">
                            <a href="javascript:void(0)"
                               class="btn btn-success rounded-pill waves-effect waves-light mj-icon-rounded-btn"
                               id="reject-product">
                                <div class="mdi mdi-check-all"></div>
                                <span>
                                         بله اطمینان دارم
                                </span>
                            </a>
                            <a href="javascript:void(0)"
                               class="btn btn-outline-danger  rounded-pill waves-effect waves-light mj-icon-rounded-btn2"
                               data-bs-dismiss="modal"
                               id="reject-product">
                                <div class="mdi mdi-image-frame"></div>
                                <span>
                                        نه دوباره چک میکنم !
                                </span>
                            </a>


                        </div>
                    </div>
                </div>
            </div>
            <!-- accept product modal end -->


            <!-- accept product modal start -->
            <div class="modal fade" id="suspend-product-modal" tabindex="-1" aria-labelledby="exampleModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">تاییدیه تعلیق محصول</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mj-divider"></div>
                            <div class="d-flex align-items-center gap-1 justify-content-center mb-3">

                                <div class="fa fa-circle-notch text-danger"></div>
                                <h4>
                                    آیا از تعلیق کردن محصول اطمینان دارید؟
                                </h4>

                            </div>

                        </div>
                        <div class="modal-footer justify-content-center">
                            <a href="javascript:void(0)"
                               class="btn btn-secondary rounded-pill waves-effect waves-light mj-icon-rounded-btn"
                               id="reject-product">
                                <div class="mdi mdi-cancel"></div>
                                <span>
                                         بله تعلیق کن
                                </span>
                            </a>
                            <a href="javascript:void(0)"
                               class="btn btn-outline-danger  rounded-pill waves-effect waves-light mj-icon-rounded-btn2"
                               data-bs-dismiss="modal"
                               id="reject-product">
                                <div class="mdi mdi-image-frame"></div>
                                <span>
                                        نه دوباره چک میکنم !
                                </span>
                            </a>


                        </div>
                    </div>
                </div>
            </div>
            <!-- accept product modal end -->


            <!--start custom html-->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-7">
                                    <div class="ps-xl-3 mt-3 mt-xl-0 mj-col px-2">
                                        <h3><span class="badge mj-product-status pending mb-2">در حال بررسی</span></h3>
                                        <div>
                                            <span>دسته بندی : </span>
                                            <a href="javascript:void(0)" id="product-cat-name" class="text-primary">Jack
                                                & Jones</a>
                                        </div>
                                        <div class="mt-2">
                                            <span>برند : </span>
                                            <a href="javascript:void(0)" id="product-cat-name"
                                               class="text-primary">Benz</a>
                                        </div>
                                        <h4 id="product-name" class="mb-3">توربو شارژ حلزونی ساده مدل 202Ls</h4>
                                        <h6 class="text-danger text-uppercase mj-product-offer-percentage">
                                            <span id="product-offer-num">20</span>
                                            <span>%</span>
                                            <span>OFF</span>
                                        </h6>
                                        <h4 class="">قیمت: </h4>
                                        <div>
                                            <h3>
                                                <b id="product-price">22,000,000 تومان</b>
                                            </h3>
                                            <h4>
                                                <span id="product-discount-price" class="text-muted me-2"><del>25,000,000</del><del>تومان</del></span>
                                            </h4>
                                        </div>

                                        <h4 class="mt-3">فروشنده: </h4>
                                        <div>
                                            <h5>
                                                <a href="javascript:void(0)" id="product-seller-name">طراحان جوان</a>
                                            </h5>
                                        </div>


                                        <h4 class="mt-2 mj-product-detail-title">توضیحات کالا :</h4>
                                        <p id="product-desc" class=" mb-4">
                                            لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از
                                            طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که
                                            لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود
                                            ابزارهای کاربردی می باشد. کتابهای زیادی در شصت و سه درصد گذشته، حال و آینده
                                            شناخت فراوان جامعه و متخصصان را می طلبد تا با نرم افزارها شناخت بیشتری را
                                            برای طراحان رایانه ای علی الخصوص طراحان خلاقی و فرهنگ پیشرو در زبان فارسی
                                            ایجاد کرد. در این صورت می توان امید داشت که تمام و دشواری موجود در ارائه
                                            راهکارها و شرایط سخت تایپ به پایان رسد وزمان مورد نیاز شامل حروفچینی
                                            دستاوردهای اصلی و جوابگوی سوالات پیوسته اهل دنیای موجود طراحی اساسا مورد
                                            استفاده قرار گیرد.
                                        </p>
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <h4 class=" mj-product-detail-title">مشخصات کالا :</h4>
                                                <div class="mj-product-futures-list">
                                                    <div class="mj-product-futures-list-item">
                                                        <div class="d-flex align-items-center pe-2">
                                                            <i class="mdi d-flex align-items-center mdi-checkbox-marked-circle-outline  text-primary me-2"></i>
                                                            <span>
                                                                مدل حلزون
                                                            </span>
                                                            <span>
                                                                :
                                                            </span>
                                                        </div>
                                                        <div>
                                                            حلزون هاش خوبه
                                                        </div>
                                                    </div>
                                                    <div class="mj-product-futures-list-item">
                                                        <div class="d-flex align-items-center pe-2">
                                                            <i class="mdi d-flex align-items-center mdi-checkbox-marked-circle-outline text-primary me-2"></i>
                                                            <span>
                                                                مدل حلزون
                                                            </span>
                                                            <span>
                                                                :
                                                            </span>
                                                        </div>
                                                        <div>
                                                            حلزون هاش خوبه
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <h4 class=" mj-product-detail-title">تعداد موجود در انبار :</h4>
                                                <div class="mj-product-count-item">
                                                    <div class="d-flex align-items-center pe-2">
                                                        <i class="mdi d-flex align-items-center mdi-collage text-primary me-2"></i>
                                                        <span>
                                                               20
                                                            </span>
                                                        <span>
                                                                عدد
                                                            </span>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>


                                    </div>
                                </div>
                                <div class="col-lg-5">
                                    <div class=" pt-0 px-2 ">
                                        <h4 class="mt-4 mj-product-detail-title">تصاویر کالا :</h4>

                                        <div class="mj-product-images">

                                            <div class="mj-product-images-item">

                                                <a href="/dist/images/default.png">
                                                    <img src="/dist/images/default.png" alt=""
                                                         class="img-fluid mx-auto d-block rounded">
                                                </a>
                                            </div>
                                            <div class="mj-product-images-item">

                                                <a href="/dist/images/16095173631.png">
                                                    <img src="/dist/images/16095173631.png" alt=""
                                                         class="img-fluid mx-auto d-block rounded">
                                                </a>
                                            </div>


                                        </div>

                                    </div>


                                </div>

                            </div>


                        </div>
                    </div> <!-- end card-->
                </div> <!-- end col-->
            </div>


            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="mj-products-operation-btns-card">
                                <h3>عملیات محصول :</h3>

                                <div class="mj-products-operation-btns">
                                    <a href="javascript:void(0)" data-bs-toggle="modal"
                                       data-bs-target="#accept-product-modal"
                                       class="btn btn-success rounded-pill waves-effect waves-light mj-icon-rounded-btn"
                                       id="accept-product">
                                        <div class="mdi mdi-check-all"></div>
                                        <span>
                                         تایید محصول
                                    </span>
                                    </a>

                                    <a href="javascript:void(0)" data-bs-toggle="modal"
                                       data-bs-target="#suspend-product-modal"
                                       class="btn btn-secondary rounded-pill waves-effect waves-light mj-icon-rounded-btn"
                                       id="suspend-product">
                                        <div class="mdi mdi-cancel"></div>
                                        <span>
                                         تعلیق محصول
                                    </span>
                                    </a>

                                    <a href="javascript:void(0)" data-bs-toggle="modal"
                                       data-bs-target="#reject-product-modal"
                                       class="btn btn-danger rounded-pill waves-effect waves-light mj-icon-rounded-btn"
                                    >
                                        <div class="mdi mdi-close"></div>
                                        <span>
                                         رد محصول
                                    </span>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4>توضیحات ادمین</h4>
                            <div class="d-flex align-items-center gap-1 my-3">

                                <div class="fa fa-circle-notch text-danger"></div>
                                <span>
                                    دراین بخش شما (ادمین) توضیحات خود را (برای مدیریت بهتر و اطلاع به همکاران) وارد میکنید
                                </span>

                            </div>
                            <div class="mj-normal-input">
                                <textarea name="" id="product-detail-admin-desc" cols="30" rows="10"
                                          placeholder="توضیحات ادمین برای محصول ..."></textarea>
                            </div>
                            <div class="mj-products-operation-btns">
                                <a href="javascript:void(0)"
                                   class="btn btn-primary rounded-pill waves-effect waves-light mj-icon-rounded-btn"
                                   id="submit-admin-desc">
                                    <div class="mdi mdi-account-star"></div>
                                    <span>
                                         ثبت توضیح ادمین
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!--            style="display: none"-->
            <div id="admin-desc-display-row" class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4>تاریخچه یادداشت های ادمین</h4>

                            <div class="mj-admin-desc-display-card">
                                <div class="mj-admin-desc-display-item">
                                    <div class="mj-admin-desc-display-item-header">
                                        <div class="mj-admin-desc-img">
                                            <img src="/dist/images/default.png" alt="admin">
                                        </div>
                                        <div class="mj-admin-desc-name">
                                            سعید نجمی
                                        </div>
                                        <div class="mj-admin-desc-divider"></div>
                                        <div class="mj-admin-desc-date">
                                            2023/13/31 - 20:32
                                        </div>
                                    </div>
                                    <div class="mj-admin-desc-display-item-body">
                                        <p>
                                            لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از
                                            طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که
                                            لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود
                                            ابزارهای کاربردی می باشد. کتابهای زیادی در شصت و سه درصد گذشته، حال و آینده
                                            شناخت فراوان جامعه و متخصصان را می طلبد تا با نرم افزارها شناخت بیشتری را
                                            برای طراحان رایانه ای علی الخصوص طراحان خلاقی و فرهنگ پیشرو در زبان فارسی
                                            ایجاد کرد. در این صورت می توان امید داشت که تمام و دشواری موجود در ارائه
                                            راهکارها و شرایط سخت تایپ به پایان رسد وزمان مورد نیاز شامل حروفچینی
                                            دستاوردهای اصلی و جوابگوی سوالات پیوسته اهل دنیای موجود طراحی اساسا مورد
                                            استفاده قرار گیرد.
                                        </p>
                                    </div>
                                </div>
                                <div class="mj-admin-desc-display-item">
                                    <div class="mj-admin-desc-display-item-header">
                                        <div class="mj-admin-desc-img">
                                            <img src="/dist/images/default.png" alt="admin">
                                        </div>
                                        <div class="mj-admin-desc-name">
                                            سعید نجمی
                                        </div>
                                        <div class="mj-admin-desc-divider"></div>
                                        <div class="mj-admin-desc-date">
                                            2023/13/31 - 20:32
                                        </div>
                                    </div>
                                    <div class="mj-admin-desc-display-item-body">
                                        <p>
                                            لورم ایپسوم متن ساختگی با تولید سادگی نامفهوم از صنعت چاپ و با استفاده از
                                            طراحان گرافیک است. چاپگرها و متون بلکه روزنامه و مجله در ستون و سطرآنچنان که
                                            لازم است و برای شرایط فعلی تکنولوژی مورد نیاز و کاربردهای متنوع با هدف بهبود
                                            ابزارهای کاربردی می باشد. کتابهای زیادی در شصت و سه درصد گذشته، حال و آینده
                                            شناخت فراوان جامعه و متخصصان را می طلبد تا با نرم افزارها شناخت بیشتری را
                                            برای طراحان رایانه ای علی الخصوص طراحان خلاقی و فرهنگ پیشرو در زبان فارسی
                                            ایجاد کرد. در این صورت می توان امید داشت که تمام و دشواری موجود در ارائه
                                            راهکارها و شرایط سخت تایپ به پایان رسد وزمان مورد نیاز شامل حروفچینی
                                            دستاوردهای اصلی و جوابگوی سوالات پیوسته اهل دنیای موجود طراحی اساسا مورد
                                            استفاده قرار گیرد.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end custom html-->


            <input type="hidden" id="token" name="token"
                   value="<?= $_SESSION['dt-cv'] = "dt-cv-44"; ?>">
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
            [
                $lang['help_academy_1'],
                $lang['help_academy_2'],
            ]
        );

        // start roles 4
    } else {
        setcookie('EID', null, -1, '/');
        setcookie('UID', null, -1, '/');
        setcookie('INF', null, -1, '/');
        unset($_COOKIE['EID']);
        unset($_COOKIE['UID']);
        unset($_COOKIE['INF']);

        header('Location: ' . ADMIN_HEADER_LOCATION);
    }
} else {

    setcookie('EID', null, -1, '/');
    setcookie('UID', null, -1, '/');
    setcookie('INF', null, -1, '/');
    unset($_COOKIE['EID']);
    unset($_COOKIE['UID']);
    unset($_COOKIE['INF']);

    header('Location: ' . ADMIN_HEADER_LOCATION);
}
// end roles 4