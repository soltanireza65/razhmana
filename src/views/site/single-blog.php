<?php
global $lang, $antiXSS;

use MJ\Utils\Utils;

include_once 'header-footer.php';

$slug = $antiXSS->xss_clean($_REQUEST['slug']);

/**
 * Get Post By Slug
 */
$resultPostBySlug = Post::getPostBySlug($slug, "published");
$dataPostBySlug = [];
$createDate = 0;
if ($resultPostBySlug->status == 200 && !empty($resultPostBySlug->response)) {
    $dataPostBySlug = $resultPostBySlug->response[0];
    if (isset($dataPostBySlug->post_submit_time)) {
        $createDate = $dataPostBySlug->post_submit_time;
    }
}
if (empty($dataPostBySlug)) {
    header('Location: /blog');
}

/**
 * Get Post Category By id
 */
$cateName = '';
$cateStatus = 'inactive';
$resultPostCategoryById = Post::getPostCategoryById($dataPostBySlug->category_id);
$dataPostCategoryById = [];
if ($resultPostCategoryById->status == 200 && !empty($resultPostCategoryById->response) && isset($resultPostCategoryById->response[0])) {
    $dataPostCategoryById = $resultPostCategoryById->response[0];
    $cateName = $dataPostCategoryById->category_name;
    $cateStatus = $dataPostCategoryById->category_status;
}
if ($cateStatus == "inactive") {
    header('Location: /blog');
}

getHeader($dataPostBySlug->post_title);
?>
    <script type="application/ld+json">
        <?php print_r($dataPostBySlug->post_schema); ?>

    </script>
    <main class="container" style="padding-bottom: 70px">
        <div class="row">
            <div class="col-12">
                <div class="mj-card p-1">
                    <div class="mj-singleblog-content">
                        <h2 class="p-2"><?= strip_tags($dataPostBySlug->post_title); ?></h2>
                        <img src="<?= Utils::fileExist($dataPostBySlug->post_thumbnail, BOX_EMPTY); ?>"
                             alt="<?= strip_tags(mb_strimwidth($dataPostBySlug->post_title, 0, 18, '...')); ?>">
                        <div class="mj-singleblog-info">
                            <span><?= $cateName; ?></span>
                            <span dir="ltr">
                                <bdi>
                                <?= Utils::getTimeCountry('Y / m / d', $createDate); ?>
                                </bdi>
                            </span>
                        </div>
                        <div class="mj-singleblog-body mj-from-tag px-2">
                            <?php echo $dataPostBySlug->post_description; ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
<?php
getFooter('', false);