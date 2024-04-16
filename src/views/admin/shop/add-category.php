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
        enqueueStylesheet('add-category-css', '/dist/css/admin/shop/add-category-shop.css');

        // Load Script In Footer
        enqueueScript('fa-js', '/dist/libs/fontawesome/all.min.js');
        enqueueScript('dropzone-js', '/dist/libs/dropzone/dropzone.js');
        enqueueScript('select2-js', '/dist/libs/select2/js/select2.min.js');
        enqueueScript('add-category-js', '/dist/js/admin/shop/add-category-shop.js');
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
                                    <h4 class="mt-4 mj-detail-title">افزودن دسته بندی </h4>
                                    <div class="d-flex align-items-center gap-1">
                                        <div class="fa fa-circle-info text-primary"></div>
                                        <div>
                                            در این بخش شما (ادمین) دسته بندی مورد نظر را نسبت به دسته بندی مورد نظر ایجاد
                                            نمایید!!
                                        </div>
                                    </div>
                                    <div class="mj-divider"></div>
                                    <div class="mj-add-category-card mj-add-stuffs-section">
                                        <div class=" mj-normal-input mt-3 mj-add-stuffs-item">
                                            <label for="" class="mb-1">نام دسته بندی را وارد کنید</label>
                                            <input type="text" placeholder="نام دسته بندی ...">
                                        </div>
                                        <div class=" mj-normal-input mt-3 mj-add-stuffs-item">
                                            <label for="" class="mb-1">دسته بندی والد را انتخاب کنید</label>
                                            <div class="mj-normal-select2">
                                                <select type="text" id="category-cat-select">
                                                    <option value=""></option>
                                                    <option value="1">نوع تستی یک</option>
                                                    <option value="2">نوع تستی دو</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class=" mj-normal-input mt-3 mj-add-stuffs-item">
                                            <label for="" class="mb-1">اولویت نمایش دسته بندی را وارد کنید</label>
                                            <input type="text" inputmode="numeric" placeholder="اولویت بندی دسته بندی">
                                        </div>
                                        <div class=" mj-normal-input mt-3 mj-add-stuffs-item">
                                            <label for="" class="mb-1">فعال / غیر فعال کردن دسته بندی</label>
                                            <div>
                                                <label class="switch">
                                                    <input type="checkbox" checked>
                                                    <span class="slider round"></span>
                                                </label>
                                            </div>

                                        </div>
                                        <div class=" mj-normal-input mt-3 mj-add-stuffs-item">
                                            <label for="" class="mb-1">عکس دسته بندی را آپلود کنید</label>

                                                <div class="mj-in-cargo-inputs-item" id="category-item">
                                                    <div class="mj-admin-custom-dropzone">
                                                        <form class="dropzone needsclick mj-add-dropzone" id="category-dz"
                                                              action="/upload">
                                                            <div class="dz-message mj-dz-message needsclick ">
                                                                <div class="fa fa-arrow-circle-up"></div>
                                                                <div>فایل یا تصویر دسته بندی را آپلود کنید</div>
                                                                <div class="btn btn-primary mj-dropzone-btn">
                                                                    افزودن فایل دسته بندی
                                                                </div>


                                                                <div style="color: red" id="category-error"></div>
                                                            </div>

                                                        </form>
                                                    </div>

                                                    <div id="preview-template-category" class="mj-preview-template">
                                                        <div class="dz-preview dz-file-preview">
                                                            <div class="dz-image"><img data-dz-thumbnail=""></div>
                                                            <div class="dz-details"></div>
                                                            <div class="dz-progress">
                                                                <span class="dz-upload" data-dz-uploadprogress=""></span>
                                                            </div>
                                                        </div>
                                                    </div>
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
                                <h3>عملیات دسته بندی :</h3>

                                <div class="mj-operation-btns ">
                                    <a href="javascript:void(0)" data-bs-toggle="modal"
                                       data-bs-target="#accept-product-modal"
                                       class="btn btn-success rounded-pill waves-effect waves-light mj-icon-rounded-btn"
                                       id="accept-product">
                                        <div class="mdi mdi-check-all"></div>
                                        <span>
                                         ثبت دسته بندی
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