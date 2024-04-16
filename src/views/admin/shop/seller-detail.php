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
        enqueueStylesheet('fa-css', '/dist/libs/fontawesome/all.min.css');
        enqueueStylesheet('dropzone-css', '/dist/libs/dropzone/dropzone.css');
        enqueueStylesheet('select2-css', '/dist/libs/select2/css/select2.min.css');
        enqueueStylesheet('main-css', '/dist/css/admin/main-custom.css');
        enqueueStylesheet('add-brand-css', '/dist/css/admin/shop/seller-detail.css');

        // Load Script In Footer
        enqueueScript('fa-js', '/dist/libs/fontawesome/all.min.js');
        enqueueScript('dropzone-js', '/dist/libs/dropzone/dropzone.js');
        enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('add-brand-js', '/dist/js/admin/shop/seller-detail.js');
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

            <!--start custom html-->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class=" pt-0 ">
                                    <h4 class="mt-4 mj-detail-title">جزئیات فروشنده </h4>
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="fa fa-circle-info text-primary"></div>
                                        <div>
                                            در این بخش شما (ادمین) جزئیات فروشنده (هر بخش با تب ؛ برای مدیریت بخش جدا شده) عملیات مربوط آن را مشاهده میکنید!!
                                        </div>
                                    </div>
                                    <div class="mj-divider"></div>

                                    <div class="mj-seller-type-date-card">
                                        <div class="mj-seller-type-card">
                                            <span>شخصیت فروشنده :</span>
                                            <div class="mj-seller-type hagigi">
                                                حقیقی
                                            </div>
                                        </div>
                                        <div class="mj-admin-h-divider"></div>
                                        <div class="mj-seller-date-card">
                                            <span>زمان ثبت نام :</span>
                                            <div id="seller-signup-date">
                                                2021/10/11 - 20:21
                                            </div>

                                        </div>
                                       
                                    </div>

                                    <div class="my-3">
                                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="seller-detail-tab" data-bs-toggle="pill"
                                                        data-bs-target="#seller-detail" type="button" role="tab"
                                                        aria-controls="seller-detail" aria-selected="true">
                                                    اطلاعات فروشنده
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="seller-products-tab" data-bs-toggle="pill"
                                                        data-bs-target="#seller-products" type="button" role="tab"
                                                        aria-controls="seller-products" aria-selected="false">
                                                    محصولات فروشنده
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="seller-orders-tab" data-bs-toggle="pill"
                                                        data-bs-target="#seller-orders" type="button" role="tab"
                                                        aria-controls="seller-orders" aria-selected="false">
                                                    سفارشات فروشنده
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="seller-ticket-tab" data-bs-toggle="pill"
                                                        data-bs-target="#seller-ticket" type="button" role="tab"
                                                        aria-controls="seller-ticket" aria-selected="false">
                                                    تیکت فروشنده
                                                </button>
                                            </li>

                                        </ul>
                                        <div class="tab-content mj-custom-tab-content" id="pills-tabContent">
                                            <div class="tab-pane fade show active" id="seller-detail" role="tabpanel"
                                                 aria-labelledby="seller-detail-tab" tabindex="0">
                                                <h3>اطلاعت شخصی فروشنده</h3>
                                                 <div class="mj-seller-info-card">
                                                     <div class="mj-seller-info-item">
                                                         <span class="mj-seller-info-item-title">
                                                             نام و نام خانوادگی
                                                         </span>

                                                         <div id="seller-name" class="mj-seller-info-item-value">
                                                             سعید نجمی
                                                         </div>


                                                     </div>

                                                     <div class="mj-seller-info-item">
                                                         <span class="mj-seller-info-item-title">
                                                             ایمیل
                                                         </span>

                                                         <div id="seller-email" class="mj-seller-info-item-value">
                                                             mnjsaeed@gmail.com
                                                         </div>
                                                     </div>

                                                     <div class="mj-seller-info-item">
                                                         <span class="mj-seller-info-item-title">
                                                             تاریخ تولد
                                                         </span>

                                                         <div id="seller-birthday" class="mj-seller-info-item-value">
                                                             1374/04/20
                                                         </div>
                                                     </div>

                                                     <div class="mj-seller-info-item">
                                                         <span class="mj-seller-info-item-title">
                                                             جنسیت
                                                         </span>

                                                         <div id="seller-sex" class="mj-seller-info-item-value">
                                                             مرد
                                                         </div>
                                                     </div>

                                                 </div>
                                                <div class="mj-divider my-3"></div>
                                                <h3 class="my-3">اطلاعت تماس </h3>
                                                <div class="mj-seller-info-card">

                                                    <div class="mj-seller-info-item">
                                                         <span class="mj-seller-info-item-title">
                                                             شماره همراه
                                                         </span>

                                                        <div id="seller-phone-num" class="mj-seller-info-item-value">

                                                            <bdi>
                                                                +989143302964
                                                            </bdi>
                                                        </div>
                                                    </div>
                                                    <div class="mj-seller-info-item">
                                                         <span class="mj-seller-info-item-title">
                                                             کد پستی
                                                         </span>

                                                        <div id="seller-postal-code" class="mj-seller-info-item-value">
                                                            5136684598
                                                        </div>
                                                    </div>
                                                    <div class="mj-seller-info-item">
                                                         <span class="mj-seller-info-item-title">
                                                             نام فروشگاه
                                                         </span>

                                                        <div id="seller-store-name" class="mj-seller-info-item-value">
                                                            هلدینگ نجمی
                                                        </div>
                                                    </div>
                                                    <div id="address-item" class="mj-seller-info-item">
                                                         <span class="mj-seller-info-item-title">
                                                             آدرس کامل
                                                         </span>

                                                        <div id="seller-address" class="mj-seller-info-item-value">
                                                           تبریز پ 3 بغل پلاک 4 در خیابان 5
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mj-divider my-3"></div>
                                                <h3 class="my-3">تصاویر مجوز ها </h3>
                                                <div class="mj-seller-images-card">
                                                    <div  class="mj-seller-image-item">
                                                        <h5>
                                                            گواهی ارزش افزوده
                                                        </h5>
                                                        <div class="mj-seller-images">
                                                            <a href="/dist/images/default.png">
                                                                <img src="/dist/images/default.png" alt="">
                                                            </a>
                                                            <a href="/dist/images/default.png">
                                                                <img src="/dist/images/default.png" alt="">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div  class="mj-seller-image-item">
                                                        <h5 id="id-card-title">
                                                            کارت ملی
                                                        </h5>
                                                        <div class="mj-seller-images">
                                                            <a href="/dist/images/default.png">
                                                                <img src="/dist/images/default.png" alt="">
                                                            </a>
                                                            <a href="/dist/images/default.png">
                                                                <img src="/dist/images/default.png" alt="">
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div  class="mj-seller-image-item">
                                                        <h5>
                                                            مجوز ها ، پروانه کسب و ...
                                                        </h5>
                                                        <div class="mj-seller-images">
                                                            <a href="/dist/images/default.png">
                                                                <img src="/dist/images/default.png" alt="">
                                                            </a>
                                                            <a href="/dist/images/default.png">
                                                                <img src="/dist/images/default.png" alt="">
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="tab-pane fade" id="seller-products" role="tabpanel"
                                                 aria-labelledby="seller-products-tab" tabindex="0">
                                                 محصولات فروشنده
                                            </div>
                                            <div class="tab-pane fade" id="seller-orders" role="tabpanel"
                                                 aria-labelledby="seller-orders-tab" tabindex="0">
                                                 سفارشات فروشنده
                                            </div>
                                            
                                            <div class="tab-pane fade" id="seller-ticket" role="tabpanel"
                                                 aria-labelledby="seller-ticket-tab" tabindex="0">
                                                 تیکت فروشنده
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
                            <div class=" mj-operation-btns-card">
                                <h3>عملیات فروشنده :</h3>

                                <div class="mj-operation-btns ">
                                    <a href="javascript:void(0)" data-bs-toggle="modal"
                                       data-bs-target="#accept-product-modal"
                                       class="btn btn-success rounded-pill waves-effect waves-light mj-icon-rounded-btn"
                                       id="accept-product">
                                        <div class="mdi mdi-check-all"></div>
                                        <span>
                                         تایید فروشنده
                                    </span>
                                    </a>
                                    <a href="javascript:void(0)" data-bs-toggle="modal"
                                       data-bs-target="#accept-product-modal"
                                       class="btn btn-danger rounded-pill waves-effect waves-light mj-icon-rounded-btn"
                                       id="accept-product">
                                        <div class="mdi mdi-check-all"></div>
                                        <span>
                                         رد فروشنده
                                    </span>
                                    </a>
                                    <a href="javascript:void(0)" data-bs-toggle="modal"
                                       data-bs-target="#accept-product-modal"
                                       class="btn btn-secondary rounded-pill waves-effect waves-light mj-icon-rounded-btn"
                                       id="accept-product">
                                        <div class="mdi mdi-check-all"></div>
                                        <span>
                                         تعلیق فروشنده
                                    </span>
                                    </a>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


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