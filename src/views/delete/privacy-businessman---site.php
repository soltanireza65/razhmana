<?php
global $Settings, $lang;
use MJ\Utils\Utils;

include_once 'header-footer.php';

getHeader($lang['privacy_businessman']);
?>
    <script type="application/ld+json">
        <?php print_r(Utils::getFileValue("settings.txt" , "seo_privacy_businessman"))?>
    </script>
    <main class="container" style="padding-bottom: 180px">
        <div class="row">
            <div class="col-sm-12 col-lg-12 px-4 mj-text-align">
                <?php
                echo $Settings['privacy_businessman'];
                ?>
            </div>
        </div>
    </main>
<?php
getFooter('', false);