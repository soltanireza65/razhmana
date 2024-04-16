<?php

use MJ\Security\Security;
use MJ\Utils\Utils;

global $lang, $Settings;

/**
 * Get Post By Limit
 */
if (isset($_COOKIE['language'])) {
    $lag = $_COOKIE['language'];
} else {
    $lag = 'fa_IR';
}
$resultPostByLimit = Post::getPostByLimit(0, 6, $lag);
$dataPostByLimit = [];
if ($resultPostByLimit->status == 200 && !empty($resultPostByLimit->response)) {
    $dataPostByLimit = $resultPostByLimit->response;
}

include_once 'header-footer.php';
enqueueStylesheet('swiper-css', '/dist/libs/swiper/css/swiper-bundle.min.css');
enqueueStylesheet('ladda', '/dist/libs/ladda/ladda.min.css');

enqueueScript('swiper-js', '/dist/libs/swiper/js/swiper-bundle.min.js');
enqueueScript('spin', '/dist/libs/ladda/spin.min.js');
enqueueScript('ladda', '/dist/libs/ladda/ladda.min.js');

enqueueScript('lottie', '/dist/libs/lottie/lottie-player.js');
enqueueScript('invite', '/dist/js/user/invite.js');

getHeader('', true);


$invite_code = substr(json_decode(\MJ\Security\Security::decrypt($_COOKIE['user-login']))->UserMobile, 1);

$user_detail = User::getUserInfo();
/**
 * get All user  refferal users
 */
$user_referals = AUser::getUserReferrals((str_replace('+', '', $invite_code)));
$user_referals = $user_referals->status == 200 ? $user_referals->response : [];

?>

    <main>
        <style>
            .mj-share-alert {
                position: absolute;
                background: #000000ab;
                padding: 10px 26px;
                color: #fff !important;
                border-radius: 10px;
                display: none;
            }
        </style>
        <div class="mj-invite-code mb-5">
            <div class="mj-invite-label-code"><?= $lang['invite-code-referral'] ?> :</div>
            <div class="mj-invite-code-num"><?= $invite_code ?></div>
            <div id="invite-copy" class="mj-invite-code-copy" data-invite-code="<?= $invite_code ?>">
                <div class="fa-copy"></div>

            </div>
            <div class="mj-share-alert  "><?= $lang['copied'] ?></div>
        </div>
        <div class="mj-invite-code mj-invite-link">
            <div class="mj-invite-label-code"><?= $lang['invite-share-link'] ?> :</div>
            <div class="mj-invite-code-num"><span>https://www.ntirapp.com/login/<?= $invite_code ?></span></div>
            <div class="mj-invite-code-copy" id="share"
                 data-invite-code="<?= $invite_code ?>" data-tj-title=""
                 data-tj-share-setting>
                <div class="fa-share"></div>
            </div>
        </div>
        <div class="mj-inite-animation mt-5">
            <lottie-player src="/dist/lottie/invite.json" class="mx-auto mt-3" style="max-width: 320px;" speed="1" loop
                           autoplay></lottie-player>
        </div>
        <?php if ($user_detail->UserClass == 'marketer') { ?>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th><?= $lang['user_mobile']; ?></th>
                        <th><?= $lang['name_and_family']; ?></th>
                        <th><?= $lang['action']; ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $flagTableEmptyReferals = true;
                    if (!empty($user_referals)) {


                        $i = 1;
                        foreach ($user_referals as $item) {
                            $flagTableEmptyReferals = false;
                            ?>
                            <tr>
                                <th><?= $i; ?></th>
                                <td>
                                    <bdi><?= Security::decrypt($item->user_mobile); ?></bdi>
                                </td>
                                <td><?= Security::decrypt($item->user_firstname) ?> <?= Security::decrypt($item->user_lastname) ?></td>
                                <td>
                                    <a href="javascript:void(0);"
                                       target="_blank"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       title="<?= $lang['all_info']; ?>"
                                       class="action-icon">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                            $i++;
                        }

                    }
                    if ($flagTableEmptyReferals) {
                        ?>
                        <tr>
                            <td class="text-center"
                                colspan="4"><?= $lang['empty_user_referals']; ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </main>
<?php
getFooter('', false);