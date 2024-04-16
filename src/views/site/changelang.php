<?php
global $Settings, $lang, $Tour;

use MJ\Security\Security;
use MJ\Utils\Utils;

if (isset($_POST['lang-radio'])) {
    setcookie('language', $_POST['lang-radio'], time() + 31536000, "/");
    User::changeUserLanguageOnChangeLanguage($_POST['lang-radio']);
    setcookie('language-modal', $_POST['lang-radio'], time() + 31536000, "/");
    if (isset($_COOKIE['login-back-url'])){
        header("Location: ".$_COOKIE['login-back-url']);
    }else{
        header("Location: /");
    }

}

include_once 'header-footer.php';

enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
enqueueStylesheet('all-css', '/dist/css/all.css');
enqueueStylesheet('user-css', '/dist/css/user.css');

enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
enqueueScript('slider-js', '/dist/js/user/slider.js');
enqueueScript('home-init', '/dist/js/site/home.js');
enqueueScript('lazyload-js', '/dist/libs/lazyload/lazyload.js');

getHeader($lang['home']);

$sliders = $Settings['u_home_sliders'];

?>
<script type="application/ld+json">
        <?php print_r(Utils::getFileValue("settings.txt", "seo_home")) ?>

</script>

<!-- call owner modal start  -->

<!-- call owner modal end  -->
    <style>
        .mj-app-header{
            display: none !important;
        }
    </style>
<main class="container" style="padding-bottom: 60px !important;">


    <div class=" "
         id="lang-modal-h">
        <div class="mj-language-select2 p-2 ">
            <div class="fa-language">

            </div>

            <div style="width: 100%; padding-top: 30px;">
                <form class="mj-lang-items" action="" method="post">
                    <div class="radio">
                        <input id="lang-en" value="en_US" name="lang-radio"
                               type="radio" >
                        <label for="lang-en" class="radio-label">
                            <div class="mj-lang-home-item">
                                <img src="/dist/images/language/en.svg" alt="EN">
                                <span>EN</span>
                            </div>
                        </label>
                    </div>
                    <div class="radio">
                        <input id="lang-ir" value="fa_IR" name="lang-radio"
                               type="radio">
                        <label for="lang-ir" class="radio-label">
                            <div class="mj-lang-home-item">
                                <img src="/dist/images/language/ir.svg" alt="IR">
                                <span>IR</span>
                            </div>
                        </label>
                    </div>

                    <div class="radio">
                        <input id="lang-tr" value="tr_Tr" name="lang-radio"
                               type="radio">
                        <label for="lang-tr" class="radio-label">
                            <div class="mj-lang-home-item">
                                <img src="/dist/images/language/tr.svg" alt="TR">
                                <span>TR</span>
                            </div>
                        </label>
                    </div>
                    <div class="radio">
                        <input id="radio-ru" value="ru_RU" name="lang-radio"
                               type="radio">
                        <label for="radio-ru" class="radio-label">
                            <div class="mj-lang-home-item">
                                <img src="/dist/images/language/ru.svg" alt="RU">
                                <span>RU</span>
                            </div>
                        </label>
                    </div>
                    <div class="radio">
                        <input id="lang-ar" value="fa_IR" name="lang-radio"
                               type="radio">
                        <label for="lang-ar" class="radio-label">
                            <div class="mj-lang-home-item">
                                <img src="/dist/images/language/AR.svg" alt="IR">
                                <span>AR</span>
                            </div>
                        </label>
                    </div>

                    <div class="mj-lang-submit">
                        <input id="lang-submit" type="submit" name="submit" value="OK">
                    </div>
                    <style>
                        form{
                            display: grid;
                            gap: 7px;
                            grid-template-columns: repeat(2, 1fr);
                            width: 100%;
                            position: relative;
                            padding-bottom: 50px;
                        }
                        .mj-lang-submit{
                            position: absolute;
                            bottom: 0;
                            transform: translateX(50%);
                            right: 50%;
                            width: 100%;
                            text-align: center;
                        }

                        .mj-lang-submit #lang-submit{
                            width: 50% !important;
                            background: var(--primary);
                            outline: unset;
                            border: unset;
                            border-radius: 10px;
                            padding:5px  ;
                            color: #fff;
                        }
                    </style>
                </form>
            </div>
        </div>
    </div>


</main>
<?php
getFooterHome();