<?php
$pageSlug = "cargo_in";
// permission_can_show

global $lang;

use MJ\Security\Security;
use MJ\Utils\Utils;

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


        /**
         * Get All cars
         */
        $resultAllCargo = Cargo::getAllCargoIn("progress");
        $dataAllCargo = [];
        if ($resultAllCargo->status == 200 && !empty($resultAllCargo->response)) {
            $dataAllCargo = $resultAllCargo->response;
        }

        /**
         * Get All Category Cars
         */
        $resultAllCargoCategory = Cargo::getAllCargoCategory();
        $dataAllCargoCategory = [];
        if ($resultAllCargoCategory->status == 200 && !empty($resultAllCargoCategory->response)) {
            $dataAllCargoCategory = $resultAllCargoCategory->response;
        }

        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }
        $cargoName="cargo_name_".$language;

        getHeader($lang["cargo_in_progress"], [
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
            <div class="row justify-content-between mb-3">
                <div class="col-auto">
                    <h4 class="header-title"><?= $lang["cargo_in_progress"]; ?></h4>
                </div>
                <div class="col-sm-6">
                    <input type="text" class="form-control" id="myInput" placeholder="<?= $lang['search']; ?>">
                </div><!-- end col-->
            </div>
            <div class="row" id="myTable">
                <?php
                if (!empty($dataAllCargo)) {
                    foreach ($dataAllCargo as $dataAllCargoITEM) {

                        /**
                         * Get User Info By Id
                         */
                        $resultUserInfoById = AUser::getUserInfoById($dataAllCargoITEM->user_id);
                        $name = $lang['guest_user'];
                        if ($resultUserInfoById->status == 200 && !empty($resultUserInfoById->response) && !empty($resultUserInfoById->response[0])) {
                            $name = Security::decrypt($resultUserInfoById->response[0]->user_firstname) . " " . Security::decrypt($resultUserInfoById->response[0]->user_lastname);
                        }
                        if (!empty($dataAllCargoCategory)) {
                            foreach ($dataAllCargoCategory as $dataAllCargoCategoryITEM) {
                                if ($dataAllCargoCategoryITEM->category_id == $dataAllCargoITEM->category_id) {
                                    $image = $dataAllCargoCategoryITEM->category_image;
                                    $categoryName = (!empty(array_column(json_decode($dataAllCargoCategoryITEM->category_name, true), 'value', 'slug')[$language])) ?
                                        array_column(json_decode($dataAllCargoCategoryITEM->category_name, true), 'value', 'slug')[$language] : "";
                                    break;
                                }
                            }
                        }
                        ?>
                        <div class="col-lg-3">
                            <div class="text-center card ribbon-box">
                                <div class="card-body ">
                                    <div class="ribbon-two ribbon-two-info">
                                        <span><?= $lang['progress']; ?></span>
                                    </div>
                                    <div class="pt-2 pb-2 ">
                                        <img src="<?= Utils::fileExist($image, BOX_EMPTY); ?>"
                                             class="rounded-circle img-thumbnail avatar-xl" alt="<?= $name; ?>">

                                        <h4 class="mt-3">
                                            <a href="/admin/users/info/<?= $dataAllCargoITEM->user_id; ?>"
                                               class="text-dark">
                                                <?= $name; ?>
                                            </a>
                                        </h4>
                                        <p class="text-muted">
                                            <?= $categoryName; ?>
                                        </p>
                                        <p class="text-dark">
                                            <?= $dataAllCargoITEM->$cargoName; ?>
                                        </p>
                                        <a target="_self"
                                           href="/admin/cargo-in/<?= $dataAllCargoITEM->cargo_id; ?>"
                                           class="btn btn-soft-primary btn-sm waves-effect waves-light">
                                            <?= $lang['show']; ?>
                                        </a>
                                        <a href="/admin/users/info/<?= $dataAllCargoITEM->user_id; ?>"
                                           class="btn btn-soft-info btn-sm waves-effect waves-light">
                                            <?= $lang['user_info']; ?>
                                        </a>
                                    </div> <!-- end .padding -->
                                </div>
                            </div> <!-- end card-->
                        </div>
                        <!-- end col -->
                        <?php
                    }
                } else {
                    ?>
                    <div class="col-lg-12 ">
                        <p class="text-center">
                            <img src="<?= BOX_EMPTY; ?>"
                                 style="width: 50%;max-width: fit-content;"
                                 alt="<?= $lang['no_massages']; ?>">
                        </p>

                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
            [
                $lang['help_cargo_13'],
                $lang['help_cargo_11'],
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
?>
<script>
    $(document).ready(function () {
        $("#myInput").on("keyup", function () {
            var value = $(this).val().toLowerCase();
            $("#myTable .col-lg-3").filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>