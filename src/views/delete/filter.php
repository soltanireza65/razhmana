<?php
global $Settings, $lang;

use MJ\Utils\Utils;


/**
 * Get Post By Limit
 */
$langCookie = 'fa_IR';
if (isset($_COOKIE['language'])) {
    $langCookie = $_COOKIE['language'];
} else {
    $langCookie = 'fa_IR';
    setcookie('language', 'fa_IR', time() + STABLE_COOKIE_TIMEOUT, "/");
    User::changeUserLanguageOnChangeLanguage('fa_IR');
}

include_once getcwd() . '/views/user/header-footer.php';
enqueueStylesheet('poster-css', '/dist/css/poster/filter.css');
enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
enqueueStylesheet('poster-css', '/dist/css/poster/filter.css');
enqueueStylesheet('swiper-css', '/dist/libs/fontawesome/all.css');
enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
enqueueScript('slider-js', '/dist/libs/fontawesome/all.min.js');
enqueueScript('range-js', '/dist/js/poster/range.js');
enqueueScript('filter-js', '/dist/js/poster/filter.js');


getHeader($lang['home']);


?>
    <div class="mj-save-filter-modal">
        <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel"
             tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">

                        <div class="mj-save-filter-modal-header">
                            <div class="mj-title-sf">
                                <img src="/dist/images/poster/save-filter-icon.svg" alt="">
                                <span>فیلتر های من</span>
                            </div>
                            <div class="mj-delete-save-filter">
                                <img src="/dist/images/poster/trash(filter).svg" alt="trash">
                            </div>
                        </div>

                    </div>
                    <div class="modal-body">
                        <div class="mj-saved-filters">

                            <div class="mj-saved-filter-item">
                                <div class="mj-s-item-badge"></div>
                                <div class="mj-saved-filter-title">
                                    کشنده و اتوماتیک - بنزینی و ...
                                </div>
                                <div class="mj-s-item-check">
                                    <img src="/dist/images/poster/check(sfilter).svg" alt="import">
                                </div>

                                <button class="mj-s-item-delete" data-bs-target="#exampleModalToggle2"
                                        data-bs-toggle="modal">
                                    <img src="/dist/images/poster/trash(filter).svg" alt="delete">
                                </button>

                            </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="mj-saved-filters-close" data-bs-dismiss="modal" aria-label="Close">
                            <span class="fa-arrow-right"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="mj-saved-filter-second-modal">
            <div class="modal fade" id="exampleModalToggle2" aria-hidden="true"
                 aria-labelledby="exampleModalToggleLabel2"
                 tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img src="/dist/images/poster/trash(back).svg" alt="trash">
                            <span> آیا از حذف فیلتر اطمینان دارید؟</span>
                        </div>
                        <div class="modal-footer">
                            <button class="mj-back-to-first-modal" data-bs-target="#exampleModalToggle"
                                    data-bs-toggle="modal">بازگشت
                            </button>
                            <button class="mj-second-modal-yes" data-bs-target="#exampleModalToggle"
                                    data-bs-toggle="modal">بله
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <main style="padding-top: 113px; padding-bottom: 110px">

        <!-- filter header start-->

        <div class="mj-filter-header pt-2">
            <div class="mj-filter-save-refresh">
                <a data-bs-toggle="modal" href="#exampleModalToggle">
                    <div class="mj-save-filtr-btn">
                        <img src="/dist/images/poster/save-filter-icon.svg" alt="filtr-icon">
                        <span>فیلتر های من</span>
                    </div>
                </a>
                <div class="mj-refreshh-filter-btn">
                    <img src="/dist/images/poster/filter-refresh.svg" alt="filter-refresh">
                </div>
            </div>
        </div>

        <!-- filter header end-->

        <!-- filter tabs start-->
        <div class="mj-filter-tabs">
            <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home"
                            aria-selected="true">
                        <div class="mj-tab-title">
                            <div class="fa-circle-info"></div>
                            <span>مشخصات</span>
                        </div>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile"
                            aria-selected="false">
                        <div class="mj-tab-title">
                            <div class="fa-star"></div>
                            <span>برند</span>
                        </div>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-contact" type="button" role="tab" aria-controls="pills-contact"
                            aria-selected="false">
                        <div class="mj-tab-title">
                            <div class="fa-sliders"></div>
                            <span>ارزش</span>
                        </div>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="pills-contact-tab" data-bs-toggle="pill"
                            data-bs-target="#pills-4" type="button" role="tab" aria-controls="pills-contact"
                            aria-selected="false">
                        <div class="mj-tab-title">
                            <div class="fa-circle-up"></div>
                            <span>آپشن</span>
                        </div>
                    </button>
                </li>

            </ul>
            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab"
                     tabindex="0">
                    <div class="mj-filter-title mb-2">
                        دسته بندی ها :
                    </div>
                    <div class="mj-filter-cat">

                        <div class="mj-filter-Cat-item mj-filter-truck">
                            <img src="/dist/images/poster/truck(filter).svg" alt="">
                            <span>کشنده</span>
                        </div>
                        <div class="mj-filter-Cat-item mj-filter-trailer">
                            <img src="/dist/images/poster/trailer(filter).svg" alt="">
                            <span>تریلر</span>
                        </div>
                    </div>
                    <div class="mj-filter-divider"></div>
                    <div class="mj-filter-title mb-2">
                        جعبه دنده :
                    </div>
                    <div class="mj-filter-gear">
                        <div class="mj-filter-gear-item mj-hand-gear">
                            <img src="/dist/images/poster/hand-gear.svg" alt="">
                            <span>دستی</span>
                        </div>
                        <div class="mj-filter-gear-item mj-auto-gear">
                            <img src="/dist/images/poster/auto-gear(filter).svg" alt="">
                            <span>اتوماتیک</span>
                        </div>
                    </div>
                    <div class="mj-filter-divider"></div>
                    <div class="mj-filter-title mb-2">
                        نوع سوخت :
                    </div>
                    <div class="mj-filter-gas">
                        <div class="mj-filter-gas-item mj-gas-disel">
                            <img src="/dist/images/poster/disel.svg" alt="">
                            <span>دیزل</span>
                        </div>
                        <div class="mj-filter-gas-item mj-gas-benzin">
                            <img src="/dist/images/poster/benzin.svg" alt="">
                            <span>بنزینی</span>
                        </div>
                    </div>
                    <div class="mj-filter-divider"></div>
                    <div id="filter-trailer-type" class="mj-filter-title mb-2">
                        نوع تریلر :
                    </div>
                    <div id="selected-trailer-type"></div>
                    <div id="selected-trailer-subtype"></div>

                    <div class="mj-trailer-type">

                        <div class="mj-trailer-item" data-type-id="1" data-name="یخچالی">
                            <img src="/dist/images/poster/snow(filter).svg" alt="snow">
                            <span>یخچالی</span>
                        </div>
                        <div class="mj-trailer-item" data-type-id="2" data-name="کفی">
                            <img src="/dist/images/poster/kafi(filter).svg" alt="snow">
                            <span>کفی</span>
                        </div>
                        <div class="mj-trailer-item" data-type-id="3" data-name="چادری">
                            <img src="/dist/images/poster/chadori(filter).svg" alt="snow">
                            <span>چادری</span>
                        </div>
                        <div class="mj-trailer-item" data-type-id="4" data-name="تانکر">
                            <img src="/dist/images/poster/tanker(filter).svg" alt="snow">
                            <span>تانکر</span>
                        </div>
                        <div class="mj-trailer-item" data-type-id="5" data-name="کمپرسی">
                            <img src="/dist/images/poster/kompress(filter).svg" alt="snow">
                            <span>کمپرسی</span>
                        </div>
                        <div class="mj-trailer-item" data-type-id="6" data-name="تیغه">
                            <img src="/dist/images/poster/tige(filter).svg" alt="snow">
                            <span>تیغه</span>
                        </div>
                        <div class="mj-trailer-item" data-type-id="7" data-name="بونکر">
                            <img src="/dist/images/poster/bonker(filter).svg" alt="snow">
                            <span>بونکر</span>
                        </div>


                    </div>
                    <div class="mj-trailer-subtype">

                    </div>

                </div>
                <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab"
                     tabindex="0">
                    <div class="mj-filter-brands">
                        <div class="mj-brand-search">
                            <input type="text" id="filter-brand-search" placeholder="جستجوی برند">
                            <div class="fa-search"></div>
                        </div>
                        <div id="mj-selected-brand-list" class="mj-selected-brand-list"></div>

                        <div class="mj-brands-filter-list">
                            <label>
                                <input class="mj-filter-brand-item-input" type="checkbox" name="" id=""
                                       data-brand="هانو" data-id="1">
                                <div class="mj-filter-brand-item">
                                    <img class="mj-brand-image" src="/dist/images/poster/brands/hino.svg"
                                         alt="brand-logo">
                                    <span>هانو</span>
                                </div>
                            </label>
                            <label>
                                <input class="mj-filter-brand-item-input" type="checkbox" name="" id=""
                                       data-brand="اویکو" data-id="2">
                                <div class="mj-filter-brand-item active">
                                    <img class="mj-brand-image" src="/dist/images/poster/brands/iveco.svg"
                                         alt="brand-logo">
                                    <span>اویکو</span>
                                </div>
                            </label>
                            <label>
                                <input class="mj-filter-brand-item-input" type="checkbox" name="" id=""
                                       data-brand="هانو" data-id="3">
                                <div class="mj-filter-brand-item">
                                    <img class="mj-brand-image" src="/dist/images/poster/brands/hino.svg"
                                         alt="brand-logo">
                                    <span>هانو</span>
                                </div>
                            </label>
                            <label>
                                <input class="mj-filter-brand-item-input" type="checkbox" name="" id=""
                                       data-brand="هانو" data-id="4">
                                <div class="mj-filter-brand-item">
                                    <img class="mj-brand-image" src="/dist/images/poster/brands/hino.svg"
                                         alt="brand-logo">
                                    <span>هانو</span>
                                </div>
                            </label>
                            <label>
                                <input class="mj-filter-brand-item-input" type="checkbox" name="" id=""
                                       data-brand="مان" data-id="5">
                                <div class="mj-filter-brand-item">
                                    <img class="mj-brand-image" src="/dist/images/poster/brands/man.svg"
                                         alt="brand-logo">
                                    <span>مان</span>
                                </div>
                            </label>
                            <label>
                                <input class="mj-filter-brand-item-input" type="checkbox" name="" id=""
                                       data-brand="پاکار" data-id="6">
                                <div class="mj-filter-brand-item">
                                    <img class="mj-brand-image" src="/dist/images/poster/brands/paccar.svg"
                                         alt="brand-logo">
                                    <span>پاکار</span>
                                </div>
                            </label>
                            <label>
                                <input class="mj-filter-brand-item-input" type="checkbox" name="" id=""
                                       data-brand="ولوو" data-id="7">
                                <div class="mj-filter-brand-item">
                                    <img class="mj-brand-image" src="/dist/images/poster/brands/volvo.svg"
                                         alt="brand-logo">
                                    <span>ولوو</span>
                                </div>
                            </label>
                            <label>
                                <input class="mj-filter-brand-item-input" type="checkbox" name="" id=""
                                       data-brand="اسکانیا" data-id="8">
                                <div class="mj-filter-brand-item ">
                                    <img class="mj-brand-image" src="/dist/images/poster/brands/scania.svg"
                                         alt="brand-logo">
                                    <span>اسکانیا</span>
                                </div>
                            </label>

                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab"
                     tabindex="0">

                    <div class="mj-filter-ranges">
                        <div class="mj-price-filter-range">
                            <div class="mj-filter-title mb-2">
                                محدوده قیمت :
                            </div>
                            <div class="range-result mb-3">
                                <div class="range-from">
                                    <span>از</span>
                                    <span id="rangefrom">0</span>
                                </div>
                                <div class="range-to">
                                    <span>تا</span>
                                    <span id="rangeto">10,000,000</span>
                                </div>
                            </div>
                            <div class="slider">
                                <div class="progress"></div>
                            </div>
                            <div dir="ltr" class="range-input">
                                <input type="range" class="range-min" step="1000000" min="0" max="10000000" value="0">
                                <input type="range" class="range-max" step="1000000" min="0" max="10000000"
                                       value="10000000">
                            </div>
                        </div>
                        <div class="mj-filter-divider"></div>
                        <div class="mj-use-filter-range">
                            <div class="mj-filter-title mb-2">
                                کارکرد : <span style="font-size: 11px;font-weight: 300">(براساس کیلومتر)</span>
                            </div>
                            <div class="range-result mb-3">
                                <div class="range-from">
                                    <span>از</span>
                                    <span id="rangefrom">0</span>
                                </div>
                                <div class="range-to">
                                    <span>تا</span>
                                    <span id="rangeto">1,000,000</span>
                                </div>
                            </div>
                            <div class="slider">
                                <div class="progress"></div>
                            </div>
                            <div dir="ltr" class="range-input" data-gap="100">
                                <input type="range" class="range-min" step="1000" min="0" max="1000000" value="0">
                                <input type="range" class="range-max" step="1000" min="0" max="1000000" value="1000000">
                            </div>
                        </div>
                        <div class="mj-filter-divider"></div>
                        <div class="mj-filter-year-type mb-2">
                            <label class="me-2" >
                                <input type="checkbox" checked>
                                <span>شمسی</span>
                            </label>
                            <label >
                                <input type="checkbox" >
                                <span>میلادی</span>
                            </label>
                        </div>
                        <div class="mj-year-filter-range">
                            <div class="mj-filter-title mb-2">
                                سال تولید : <span style="font-size: 11px;font-weight: 300">(شمسی)</span>
                            </div>
                            <div class="range-result mb-3">
                                <div class="range-from">
                                    <span>از</span>
                                    <span id="rangefrom">1375</span>
                                </div>
                                <div class="range-to">
                                    <span>تا</span>
                                    <span id="rangeto">1402</span>
                                </div>
                            </div>
                            <div class="slider">
                                <div class="progress"></div>
                            </div>
                            <div dir="ltr" class="range-input">
                                <input type="range" class="range-min" min="1375" max="1402" value="1375">
                                <input type="range" class="range-max" min="1375" max="1402" value="1402">
                            </div>
                        </div>
                        <div class="mj-filter-divider"></div>
                    </div>

                </div>
                <div class="tab-pane fade" id="pills-4" role="tabpanel" aria-labelledby="pills-4-tab"
                     tabindex="0">
                    <div class="mj-filter-options">
                        <div class="mj-option-search">
                            <input type="text" id="filter-option-search" placeholder="جستجوی آپشن">
                            <div class="fa-search"></div>
                        </div>
                        <div class="mj-filter-option-list">
                            <label>
                                <input type="checkbox">
                                <div class="mj-filter-option-item">
                                    <div class="mj-filter-option-img active">
                                        <img src="/dist/images/poster/option1.svg" alt="option">
                                    </div>
                                    <span>ایربگ</span>
                                </div>
                            </label>
                            <label>
                                <input type="checkbox">
                                <div class="mj-filter-option-item">
                                    <div class="mj-filter-option-img active">
                                        <img src="/dist/images/poster/option1.svg" alt="option">
                                    </div>
                                    <span>ایربگ</span>
                                </div>
                            </label>
                            <label>
                                <input type="checkbox">
                                <div class="mj-filter-option-item">
                                    <div class="mj-filter-option-img active">
                                        <img src="/dist/images/poster/option1.svg" alt="option">
                                    </div>
                                    <span>ایربگ</span>
                                </div>
                            </label>
                            <label>
                                <input type="checkbox">
                                <div class="mj-filter-option-item">
                                    <div class="mj-filter-option-img active">
                                        <img src="/dist/images/poster/option1.svg" alt="option">
                                    </div>
                                    <span>ایربگ</span>
                                </div>
                            </label>
                            <label>
                                <input type="checkbox">
                                <div class="mj-filter-option-item">
                                    <div class="mj-filter-option-img active">
                                        <img src="/dist/images/poster/option1.svg" alt="option">
                                    </div>
                                    <span>ایربگ</span>
                                </div>
                            </label>
                            <label>
                                <input type="checkbox">
                                <div class="mj-filter-option-item">
                                    <div class="mj-filter-option-img active">
                                        <img src="/dist/images/poster/option1.svg" alt="option">
                                    </div>
                                    <span>ایربگ</span>
                                </div>
                            </label>
                            <label>
                                <input type="checkbox">
                                <div class="mj-filter-option-item">
                                    <div class="mj-filter-option-img active">
                                        <img src="/dist/images/poster/option1.svg" alt="option">
                                    </div>
                                    <span>ایربگ</span>
                                </div>
                            </label>
                            <label>
                                <input type="checkbox">
                                <div class="mj-filter-option-item">
                                    <div class="mj-filter-option-img active">
                                        <img src="/dist/images/poster/option1.svg" alt="option">
                                    </div>
                                    <span>ایربگ</span>
                                </div>
                            </label>

                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- filter tabs end-->
        <div class="mj-filter-footer">
            <div class="mj-filter-search-btn">
                <span><?=$lang['u_filter_see']?></span>
            </div>
        </div>
    </main>
<?php
getFooter('', false);
?>