<?php

use MJ\HTML\Builder;use MJ\Utils\Utils;
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/Builder.php';

$stylesheets = [];
$headerScripts = [];
$footerScripts = [];

function enqueueStylesheet($name, $src, $rel = 'stylesheet', $type = 'text/css', $version = '1.5.0')
{
    global $stylesheets , $Settings;
    $version = $Settings['admin_version'];

    $stylesheets[] = [
        'name' => $name,
        'src' => "{$src}?v={$version}",
        'rel' => $rel,
        'type' => $type
    ];
}


function enqueueScript($name, $src, $type = 'text/javascript', $version = '1.5.0', $inFooter = true)
{
    global $headerScripts, $footerScripts, $Settings;
        $version = $Settings['admin_version'];

    if ($inFooter) {
        $footerScripts[] = [
            'name' => $name,
            'src' => "{$src}?v={$version}",
            'type' => $type
        ];
    } else {
        $headerScripts[] = [
            'name' => $name,
            'src' => "{$src}?v={$version}",
            'type' => $type
        ];
    }
}


function getHeader($title = '',$data=[],$version="1.5.0")
{

    global $stylesheets, $headerScripts, $Settings , $lang;
        $version = $Settings['admin_version'];

    if (!isset($stylesheets)) {
        $stylesheets = [];
    }

    if (!isset($headerScripts)) {
        $headerScripts = [];
    }

    $lang=$data['lang'];
    $roleInfo= $data['roleInfo'];
    $adminInfo = $data['adminInfo'];

    $pageSlugName='general';
    if(isset($data['pageSlugName'])){
        $pageSlugName=$data['pageSlugName'];
    }
    $pageSlugValue='general';
      if(isset($data['pageSlugValue'])){
      $pageSlugValue=$data['pageSlugValue'];
      }


        /**
         * Get All Tickets
         */
        $resultAllCargo = Cargo::getAllCargo( "pending");
        $dataAllCargo = [];
        if ($resultAllCargo->status == 200 && !empty($resultAllCargo->response)) {
            $dataAllCargo = $resultAllCargo->response;
        }


      /**
         * Get All Settings
         */
        $resultSiteSettings = Utils::getFileValue("settings.txt");
        $dataSiteSettings = [];
        if (!empty($resultSiteSettings)) {
            $dataSiteSettings = json_decode($resultSiteSettings,true);
        }

        $whatsapp= '';
        $support_call = '';

        if (!empty($dataSiteSettings)) {
            foreach ($dataSiteSettings as $index=>$loop) {

                if ($index == "whatsapp") {
                    $whatsapp = $loop;
                }

                if ($index == "support_call") {
                    $support_call = $loop;
                }

                if ($index == "support_call_2") {
                    $support_call_2 = $loop;
                }

            }
        }

       /**
         * Get All Languages
         */
        $resultLanguagesSite = Utils::getFileValue("languages.json","",false);
        $dataLanguagesSite = [];
        if (!empty($resultLanguagesSite)) {
            $dataLanguagesSite = json_decode($resultLanguagesSite);
        }
    ?>
    <!doctype html >
    <html lang="fa" dir="<?=Utils::getThemeDirection() ;?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title><?= $title." | ".$Settings['site_name'] ;?></title>
        <link rel="shortcut icon" href="<?=Utils::fileExist('uploads/site/favicon.webp',BOX_EMPTY) ;?>">
                <link href="<?= SITE_URL; ?>/dist/css/admin/app<?=(Utils::getThemeDirection()=='rtl')?"-rtl":null ;?>.min.css?v=<?=$version;?>" rel="stylesheet" type="text/css" id="app-style"/>

        <?php
        Builder::loadStylesheet($stylesheets);
        Builder::loadScripts($headerScripts);
        ?>
        <link href="<?= SITE_URL; ?>/dist/css/admin/admin.css?v=<?=$version;?>" rel="stylesheet" type="text/css"/>
        <!-- icons -->
        <link href="<?= SITE_URL; ?>/dist/css/admin/icons.min.css?v=<?=$version;?>" rel="stylesheet" type="text/css"/>
        <link href="<?= SITE_URL; ?>/dist/css/admin/fontiran.css?v=<?=$version;?>" rel="stylesheet" type="text/css"/>
        <link href="<?= SITE_URL; ?>/dist/libs/fontawesome/all.min.css?v=<?=$version;?>" rel="stylesheet" type="text/css"/>

    </head>
    <body>
   <script type="text/javascript">
        let lang_vars = <?= json_encode($lang) ?>;
    </script>
 <!-- body start -->
<!-- oncontextmenu="return false"-->
    <body class="loading" data-layout-mode="detached"
          data-layout-color="<?= $_COOKIE['theme']; ?>" data-topbar-color="<?= $_COOKIE['theme']; ?>" data-menu-position="fixed" data-leftbar-color="<?= $_COOKIE['theme']; ?>" data-leftbar-size='<?= $_COOKIE['sidebar-admin']; ?>' data-sidebar-user='true'>

    <!-- Begin page -->
    <div id="wrapper">

        <!-- Topbar Start -->
        <div class="navbar-custom">
            <div class="container-fluid">
                <ul class="list-unstyled topnav-menu float-end mb-0">

                   <li class="dropdown d-none d-lg-inline-block">
                <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-toggle="fullscreen" href="#">
                    <i class="fe-maximize noti-icon"
                       data-bs-toggle="tooltip"
                       data-bs-placement="bottom"
                       title="<?= $lang['fullscreen']; ?>"></i>
                </a>
            </li>

                    <li class="dropdown d-none d-lg-inline-block topbar-dropdown">

                        <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="/dist/images/language/<?=$_COOKIE['language'];?>.webp"
                            alt="language-<?=$_COOKIE['language'];?>"
                            height="16"
                            data-bs-toggle="tooltip"
                            data-bs-placement="bottom"
                            title="<?= $lang['language']; ?>">
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">

                            <?php
                                 if(!empty($dataLanguagesSite)){
                                      foreach ($dataLanguagesSite as $dataLanguagesSiteITEM){
                                          if($dataLanguagesSiteITEM->status=="active"){
                             ?>
                            <!-- item-->
                            <a href="javascript:void(0);"
                               class="dropdown-item changeLanguageBtn"
                               data-mj-slug="<?=$dataLanguagesSiteITEM->slug ;?>">
                                <img src="/dist/images/language/<?=$dataLanguagesSiteITEM->slug ;?>.webp"
                                     alt="language-<?=$dataLanguagesSiteITEM->slug ;?>"
                                     class="me-1"
                                     height="12"> <span class="align-middle"><?=$lang[$dataLanguagesSiteITEM->name];?></span>
                            </a>
                            <?php
                                     }
                                   }
                                 }
                             ?>
                        </div>
                    </li>

                    <li class="dropdown d-none d-lg-inline-block topbar-dropdown">

                        <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light"
                           data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="/dist/images/time/<?=$_COOKIE['time'];?>.webp"
                            alt="time-<?=$_COOKIE['time'];?>"
                            height="16"
                            data-bs-toggle="tooltip"
                            data-bs-placement="bottom"
                            title="<?= $lang['time']; ?>">
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item languegeBtn" data-mj-type="en">
                                <img src="/dist/images/time/en.webp" alt="time-en" class="me-1" height="12"> <span class="align-middle"><?=$lang['date_gregorian'];?></span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item languegeBtn" data-mj-type="ir">
                                <img src="/dist/images/time/ir.webp" alt="time-ir" class="me-1" height="12"> <span class="align-middle"><?=$lang['date_jalali'];?></span>
                            </a>

                        </div>
                    </li>



                    <?php if(!empty($whatsapp) || !empty($support_call) || !empty($support_call_2)){ ?>
            <li class="dropdown d-none d-lg-inline-block topbar-dropdown">
                        <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="fe-grid noti-icon"
                               data-bs-toggle="tooltip"
                               data-bs-placement="bottom"
                               title="<?= $lang['social']; ?>"></i>
                        </a>
                        <div class="dropdown-menu dropdown-lg dropdown-menu-end">

                            <div class="p-lg-1">
                                <div class="row g-0">

                                     <?php if(!empty($whatsapp)){ ?>
                                    <div class="col-4">
                                        <a class="dropdown-icon-item" href="https://wa.me/<?=$whatsapp;?>">
                                            <img src="/uploads/social/whatsapp.svg" alt="<?=$lang['support_whatsapp_small'];?>">
                                            <span><?=$lang['support_whatsapp_small'];?></span>
                                        </a>
                                    </div>
                                    <?php } ?>
                                     <?php if(!empty($support_call)){ ?>
                                    <div class="col-4">
                                        <a class="dropdown-icon-item" href="tel:<?=$support_call;?>">
                                            <img src="/uploads/social/cellphone.svg" alt="<?=$lang['support_call_small'];?>">
                                            <span><?=$lang['support_call_small'];?></span>
                                        </a>
                                    </div>
                                       <?php } ?>
                                     <?php if(!empty($support_call_2)){ ?>
                                    <div class="col-4">
                                        <a class="dropdown-icon-item" href="tel:<?=$support_call_2;?>">
                                            <img src="/uploads/social/cellphone.svg" alt="<?=$lang['support_call_small'];?>">
                                            <span><?=$lang['support_call_small'];?></span>
                                        </a>
                                    </div>
                                    <?php } ?>

                                </div>
                            </div>

                        </div>
                    </li>
            <?php } ?>

                    <li class="dropdown notification-list topbar-dropdown">
                        <a class="nav-link dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="fe-bell noti-icon"
                               data-bs-toggle="tooltip"
                               data-bs-placement="bottom"
                               title="<?= $lang['cargoes_pending']; ?>"></i>
                            <?php
                            $countCargoPending= count($dataAllCargo);
                                if($countCargoPending >0){
                               ?>
                            <span class="badge bg-danger rounded-circle noti-icon-badge"><?=$countCargoPending;?></span>
                            <?php } ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-lg">

                            <!-- item-->
                            <div class="dropdown-item noti-title">
                                <h5 class="m-0">
                                    <span class="float-end">
                                <a href="" class="text-dark">
                                </a>
                            </span>
                             <?= $lang['cargoes_pending']; ?>
                                </h5>
                            </div>

                            <div class="noti-scroll" data-simplebar>
                             <?php
                               if(!empty($dataAllCargo)){
                                   $cargoName='cargo_name_'.$_COOKIE['language'];

                                   foreach ($dataAllCargo as $index=>$dataAllCargoITEM){
                                       if($index>4){
                                               break;
                                             }
                                 ?>
                                   <!-- item-->
                                <a href="/admin/cargo/<?=$dataAllCargoITEM->cargo_id;?>" class="dropdown-item notify-item <?=($index==0)? "active":""; ?>">
                                    <div class="notify-icon  bg_pr">
                                          <i class="mdi mdi-comment-account-outline"></i>
                                     </div>
                                    <p class="notify-details">
                                     <?=$dataAllCargoITEM->$cargoName;?>
                                    </p>
                                    <p class="text-muted mb-0 user-msg">
                                        <small class="text-muted">
                                        <?=Utils::timeElapsedString('@'.$dataAllCargoITEM->cargo_date);?>
                                        </small>
                                    </p>
                                </a>
                            <?php } }else{ ?>
                                <p class="text-center">
                                    <img class="w-100" src="<?=BOX_EMPTY;?>" alt="<?=$Settings['site_name'];?>">
                                </p>
                             <?php } ?>
                            </div>

                            <!-- All-->
                            <a href="/admin/cargo/pending" class="dropdown-item text-center color_pr notify-item notify-all">
                         <?= $lang['show_more'];?>
                            <i class="fe-arrow-left"></i>
                        </a>

                        </div>
                    </li>

                    <li class="dropdown notification-list">
                    <a href="javascript:void(0);" class="nav-link change-theme waves-effect waves-light"
                       data-theme="<?= ($_COOKIE['theme'] == "light") ? "light" : "dark" ; ?>">
                        <i class="fe-<?= ($_COOKIE['theme'] == 'light') ? 'moon':  'sun'; ?>  noti-icon"></i>
                    </a>
                </li>

                    <li class="dropdown notification-list topbar-dropdown">
                        <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light"
                           data-bs-toggle="dropdown"
                           href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <img src="<?= Utils::fileExist($adminInfo->admin_avatar,USER_AVATAR); ?>" alt="<?= $adminInfo->admin_nickname; ?>" class="rounded-circle">
                            <span class="pro-user-name ms-1">
                        <?= $adminInfo->admin_nickname; ?>
                         <i class="mdi mdi-chevron-down"></i>
                    </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                            <!-- item-->
                            <div class="dropdown-header noti-title">
                                <h6 class="text-overflow m-0"><?= $lang['welcome']; ?></h6>
                            </div>

                            <!-- item-->
                            <a href="/admin/myaccount" class="dropdown-item notify-item">
                                <i class="fe-user"></i>
                                <span><?= $lang['my_account']; ?></span>
                            </a>

                            <!-- item-->
                            <a href="/admin/tasks" class="dropdown-item notify-item">
                                <i class="fe-clipboard"></i>
                                <span><?= $lang['a_task']; ?></span>
                            </a>

                            <!-- item-->
                            <a href="/admin/lock-screen" class="dropdown-item notify-item lock-screen-admin">
                                <i class="fe-lock"></i>
                                <span><?= $lang['lock_screen']; ?></span>
                            </a>

                            <div class="dropdown-divider"></div>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item logoutAdmin">
                                <i class="fe-log-out"></i>
                                <span><?= $lang['logout']; ?></span>
                            </a>

                        </div>
                    </li>

                    <li class="dropdown notification-list">
<!--                <a href="javascript:void(0);" class="nav-link right-bar-toggle waves-effect waves-light">-->
<!--                    <i class="fe-settings noti-icon"></i>-->
<!--                </a>-->
            </li>

                </ul>

                <!-- LOGO -->
                <div class="logo-box">
                    <a href="<?= SITE_URL; ?>/admin" class="logo logo-dark text-center">
                <span class="logo-sm">
                    <img src="<?=Utils::fileExist('uploads/site/logo-sm.webp',BOX_EMPTY) ;?>" alt="<?= $Settings['site_name']; ?>" height="22">
                    <!-- <span class="logo-lg-text-light">UBold</span> -->
                </span>
                        <span class="logo-lg">
                    <img src="<?=Utils::fileExist('uploads/site/logo-dark.webp',BOX_EMPTY) ;?>" alt="<?= $Settings['site_name']; ?>" height="20">
                            <!-- <span class="logo-lg-text-light">U</span> -->
                </span>
                    </a>

                    <a href="<?= SITE_URL; ?>/admin" class="logo logo-light text-center">
                <span class="logo-sm">
                    <img src="<?=Utils::fileExist('uploads/site/logo-sm.webp',BOX_EMPTY) ;?>" alt="<?= $Settings['site_name']; ?>" height="22">
                </span>
                        <span class="logo-lg">
                    <img src="<?=Utils::fileExist('uploads/site/logo-light.webp',BOX_EMPTY) ;?>" alt="<?= $Settings['site_name']; ?>" height="20">
                </span>
                    </a>
                </div>

                <ul class="list-unstyled topnav-menu topnav-menu-left m-0">
                    <li>
                        <button class="button-menu-mobile waves-effect waves-light">
                            <i class="fe-menu"></i>
                        </button>
                    </li>

                    <li>
                        <!-- Mobile menu toggle (Horizontal Layout)-->
                        <a class="navbar-toggle nav-link" data-bs-toggle="collapse"
                           data-bs-target="#topnav-menu-content">
                            <div class="lines">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                        </a>
                        <!-- End mobile menu toggle-->
                    </li>

                    <li class="dropdown d-none d-xl-block">
                        <a class="nav-link dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <?=$lang['quick_add'];?>
                        <i class="mdi mdi-chevron-down"
                           data-bs-toggle="tooltip"
                           data-bs-placement="bottom"
                           title="<?= $lang['quick_add']; ?>"></i>
                    </a>
                        <div class="dropdown-menu">
                            <!-- item-->
                            <a href="/admin/post/add" class="dropdown-item">
                                <i class="fe-layout me-1"></i>
                                <span><?=$lang['post'];?></span>
                            </a>

                            <!-- item-->
                            <a href="/admin/academy/add" class="dropdown-item">
                                <i class="fe-printer me-1"></i>
                                <span><?=$lang['academy'];?></span>
                            </a>

                            <!-- item-->
                            <a href="/admin/city" class="dropdown-item">
                                <i class="fe-map-pin me-1"></i>
                                <span><?=$lang['add_city'];?></span>
                            </a>

                            <!-- item-->
                            <a href="/admin/medias" class="dropdown-item">
                                <i class="fe-layout me-1"></i>
                                <span><?=$lang['add_media'];?></span>
                            </a>

                            <div class="dropdown-divider"></div>

                            <!-- item-->
                            <a href="/admin/help" class="dropdown-item">
                                <i class="fe-help-circle me-1"></i>
                                <span><?=$lang['help'];?></span>
                            </a>

                        </div>
                    </li>



                  <li class="dropdown d-none d-lg-inline-block">
                    <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample">
                        <i class="fe-help-circle noti-icon"
                           data-bs-toggle="tooltip"
                           data-bs-placement="bottom"
                           title="<?= $lang['page_help']; ?>"></i>
                    </a>
                 </li>


                   <li class="dropdown d-none d-lg-inline-block topbar-dropdown">
                        <a class="nav-link dropdown-toggle arrow-none waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="fe-eye noti-icon"
                                data-bs-toggle="tooltip"
                                data-bs-placement="bottom"
                                title="<?= $lang['page_permission']; ?>"></i>
                        </a>
                        <div class="dropdown-menu dropdown-lg">

                            <!-- item-->
                            <div class="dropdown-header noti-title">
                                <h5 class="text-overflow mb-2"><?=$lang['info_page'];?></h5>
                            </div>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="fe-edit-1 me-1"></i>
                                <span class="fw-bold"><?=$lang['page_name'];?> : </span>  <?=$lang[$pageSlugName];?>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item">
                                <i class="fe-sliders me-1"></i>
                                <span class="fw-bold"><?=$lang['page_action_value'];?> : </span> <?=$lang[$pageSlugValue];?>
                            </a>

                        </div>
                    </li>


                </ul>

                <div class="clearfix"></div>
            </div>
        </div>
        <!-- end Topbar -->

        <!-- ========== Sidebar Start ========== -->
        <?php
         getSidebar([
            'lang' => $lang,
            'adminInfo' => $adminInfo,
            'roleInfo' => $roleInfo,
        ]);
        ?>
        <!-- Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start Page Content here -->
        <!-- ============================================================== -->

        <div class="content-page">
            <div class="content">

                <!-- Start Content-->
                <div class="container-fluid">

                    <!-- start page title -->
                    <div class="row">
                        <div class="col-12">
                            <div style="margin-top: 30px;">
                            </div>
                        </div>
                    </div>
                    <!-- end page title -->
    <?php
}


function getFooter($myNotic=[],$version="1.5.0")
{
    global $footerScripts ,$lang, $Settings;
        $version = $Settings['admin_version'];

    if (!isset($footerScripts)) {
        $footerScripts = [];
    }
    ?>
  </div>
                <!-- container -->
            </div>
            <!-- content -->
        </div>
        <!-- ============================================================== -->
        <!-- End Page content -->
        <!-- ============================================================== -->
    </div>
    <!-- END wrapper -->

     <!-- Offcanvas -->
    <?php getOffcanvas($myNotic); ?>
     <!-- /Offcanvas -->

     <!-- Right Sidebar -->
    <?php
//    getSidebarLeft();
    ?>
     <!-- /Right-bar -->
    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>


  <!-- Warning Alert Modal -->
    <div id="warningAlertModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content  border-danger border-2">
                <div class="modal-body p-4">
                    <div class="text-center">
                        <i class="mdi mdi-alert-circle-outline h1 text-danger"></i>
                        <h3 class="text-danger" id="warningAlertModalCount">0</h3>
                        <h4 class="mt-2"><?=$lang['complaints_pending'];?></h4>
                        <p class="mt-3">
                        <?=$lang['complaints_pending_desc'];?>
                        </p>
                        <a href="/admin/complaint/pending" class="btn btn-danger my-2"><?=$lang['show'];?></a>
                          <audio id="warningAlertModalAudio" src="/dist/audio/notification.wav" class="d-none" ></audio>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.Warning Alert Modal -->

    <!-- Vendor js -->
    <script src="<?= SITE_URL; ?>/dist/js/admin/vendor.min.js?v=<?=$version;?>"></script>


<?php Builder::loadScripts($footerScripts); ?>
    <!-- App js -->
    <script src="<?= SITE_URL; ?>/dist/js/admin/complaint-alert.init.js?v=<?=$version;?>"></script>
       <script src="<?=SITE_URL; ?>/dist/libs/tippy.js/tippy.all.min.js"></script>
    <script src="<?= SITE_URL; ?>/dist/js/admin/app.min.js?v=<?=$version;?>"></script>
    <script src="<?= SITE_URL; ?>/dist/libs/fontawesome/all.min.js?v=<?=$version;?>"></script>
    <script src="<?=SITE_URL; ?>/dist/libs/jquery-notify/notify.min.js"></script>
    <script src="<?= SITE_URL; ?>/dist/js/admin/all.js"></script>

    </body>
    </html>


    <?php
}

function getSidebar($data=[])
{

            $lang = $data['lang'];
        $adminIfo = $data['adminInfo'];
        $roleInfo = $data['roleInfo'];

        $myPermission=Admin::getInfoPermissionsForSidebar();

        if(empty($myPermission)){
            ?>
            <script>
            window.location.replace("/admin/login");
</script>
            <?php
        }
        $slug__admins=Admin::getSidebarPermission($myPermission,'admins');
        $slug__users=Admin::getSidebarPermission($myPermission,'users');
        $slug__notification=Admin::getSidebarPermission($myPermission,'notification');
//        $slug__ticket_businessman=Admin::getSidebarPermission($myPermission,'ticket_businessman');
//        $slug__user_driver=Admin::getSidebarPermission($myPermission,'user_driver');
//        $slug__ticket_driver=Admin::getSidebarPermission($myPermission,'ticket_driver');
//        $slug__user_driver_currency=Admin::getSidebarPermission($myPermission,'user_driver_currency');
//        $slug__user_businessman_currency=Admin::getSidebarPermission($myPermission,'user_businessman_currency');
//        $slug__user_driver_notification=Admin::getSidebarPermission($myPermission,'user_driver_notification');
//        $slug__user_businessman_notification=Admin::getSidebarPermission($myPermission,'user_businessman_notification');
//        $slug__user_driver_transaction=Admin::getSidebarPermission($myPermission,'user_driver_transaction');
//        $slug__user_businessman_transaction=Admin::getSidebarPermission($myPermission,'user_businessman_transaction');
        $slug__balance=Admin::getSidebarPermission($myPermission,'balance');
        $slug__cargo=Admin::getSidebarPermission($myPermission,'cargo');
        $slug__cargo_in=Admin::getSidebarPermission($myPermission,'cargo_in');
        $slug__request=Admin::getSidebarPermission($myPermission,'request');
        $slug__cars=Admin::getSidebarPermission($myPermission,'cars');
        $slug__tickets=Admin::getSidebarPermission($myPermission,'tickets');

        $slug__card_bank=Admin::getSidebarPermission($myPermission,'card_bank');
        $slug__transaction=Admin::getSidebarPermission($myPermission,'transaction');
        $slug__complaint=Admin::getSidebarPermission($myPermission,'complaint');
        $slug__ngroup=Admin::getSidebarPermission($myPermission,'ngroup');
        $slug__posts=Admin::getSidebarPermission($myPermission,'posts');
        $slug__posts_c=Admin::getSidebarPermission($myPermission,'posts_c');
        $slug__academy=Admin::getSidebarPermission($myPermission,'academy');
        $slug__academy_c=Admin::getSidebarPermission($myPermission,'academy_c');
        $slug__medias=Admin::getSidebarPermission($myPermission,'medias');
        $slug__settings_general=Admin::getSidebarPermission($myPermission,'settings_general');
        $slug__cars_c=Admin::getSidebarPermission($myPermission,'cars_c');
        $slug__cargo_c=Admin::getSidebarPermission($myPermission,'cargo_c');
        $slug__currency_c=Admin::getSidebarPermission($myPermission,'currency_c');
        $slug__city_c=Admin::getSidebarPermission($myPermission,'city_c');
        $slug__customs_c=Admin::getSidebarPermission($myPermission,'customs_c');
        $slug__settings_languages=Admin::getSidebarPermission($myPermission,'settings_languages');
        $slug__settings_site=Admin::getSidebarPermission($myPermission,'settings_site');
        $slug__settings_payment=Admin::getSidebarPermission($myPermission,'settings_payment');
        $slug__settings_sms=Admin::getSidebarPermission($myPermission,'settings_sms');
        $slug__settings_poster=Admin::getSidebarPermission($myPermission,'settings_poster');
        $slug__country_c=Admin::getSidebarPermission($myPermission,'country_c');
        $slug__port_c=Admin::getSidebarPermission($myPermission,'port_c');
        $slug__container_c=Admin::getSidebarPermission($myPermission,'container_c');
        $slug__ship_cargo_c=Admin::getSidebarPermission($myPermission,'ship_cargo_c');
        $slug__airport_c=Admin::getSidebarPermission($myPermission,'airport_c');
        $slug__air_cargo_c=Admin::getSidebarPermission($myPermission,'air_cargo_c');
        $slug__ship_packing_c=Admin::getSidebarPermission($myPermission,'ship_packing_c');
        $slug__air_packing_c=Admin::getSidebarPermission($myPermission,'air_packing_c');
        $slug__railroad_c=Admin::getSidebarPermission($myPermission,'railroad_c');
        $slug__wagon_c=Admin::getSidebarPermission($myPermission,'wagon_c');
        $slug__visa_location_c=Admin::getSidebarPermission($myPermission,'visa_location_c');
        $slug__railroad_cargo_c=Admin::getSidebarPermission($myPermission,'railroad_cargo_c');
        $slug__railroad_packing_c=Admin::getSidebarPermission($myPermission,'railroad_packing_c');
        $slug__inquiry_ground=Admin::getSidebarPermission($myPermission,'inquiry_ground');
        $slug__inquiry_air=Admin::getSidebarPermission($myPermission,'inquiry_air');
        $slug__inquiry_ship=Admin::getSidebarPermission($myPermission,'inquiry_ship');
        $slug__inquiry_railroad=Admin::getSidebarPermission($myPermission,'inquiry_railroad');
        $slug__inquiry_inventory=Admin::getSidebarPermission($myPermission,'inquiry_inventory');
        $slug__inquiry_customs=Admin::getSidebarPermission($myPermission,'inquiry_customs');
        $slug__inquiry_minicargo = Admin::getSidebarPermission($myPermission, 'inquiry_minicargo');
        $slug__field_c=Admin::getSidebarPermission($myPermission,'field_c');
        $slug__census=Admin::getSidebarPermission($myPermission,'census');
        $slug__container_railroad_c=Admin::getSidebarPermission($myPermission,'container_railroad_c');
        $slug__inventory_cargo_c=Admin::getSidebarPermission($myPermission,'inventory_cargo_c');
        $slug__inventory_c=Admin::getSidebarPermission($myPermission,'inventory_c');
        $slug__a_page_tasks_admin=Admin::getSidebarPermission($myPermission,'a_page_tasks_admin');
        $slug__a_task=Admin::getSidebarPermission($myPermission,'a_task');
        $slug__share_whatsapp=Admin::getSidebarPermission($myPermission,'a_share_whatsapp');
        $slug__a_authorization=Admin::getSidebarPermission($myPermission,'a_authorization');
        $slug__a_phonebook=Admin::getSidebarPermission($myPermission,'phonebook');
        $slug__a_phonebook_sms=Admin::getSidebarPermission($myPermission,'phonebook_sms');
        $slug__a_personel=Admin::getSidebarPermission($myPermission,'personel');
        $slug__settings_seo=Admin::getSidebarPermission($myPermission,'settings_seo');
        $slug__inventory_type_c=Admin::getSidebarPermission($myPermission,'inventory_type_c');

//        $slug__departments=Admin::getSidebarPermission($myPermission,'departments');
        $slug__tickets_driver=Admin::getSidebarPermission($myPermission,'tickets_driver');
        $slug__tickets_businessman=Admin::getSidebarPermission($myPermission,'tickets_businessman');
        $slug__tickets_ship=Admin::getSidebarPermission($myPermission,'tickets_ship');
        $slug__tickets_air=Admin::getSidebarPermission($myPermission,'tickets_air');
        $slug__tickets_railroad=Admin::getSidebarPermission($myPermission,'tickets_railroad');
        $slug__tickets_inventory=Admin::getSidebarPermission($myPermission,'tickets_inventory');
        $slug__tickets_poster=Admin::getSidebarPermission($myPermission,'tickets_poster');
        $slug__tickets_customs=Admin::getSidebarPermission($myPermission,'tickets_customs');


        $slug__brands=Admin::getSidebarPermission($myPermission,'brands');
        $slug__a_fuels=Admin::getSidebarPermission($myPermission,'a_fuels');
        $slug__a_gearboxs=Admin::getSidebarPermission($myPermission,'a_gearboxs');
        $slug__a_model_c=Admin::getSidebarPermission($myPermission,'a_model_c');
        $slug__a_property_c=Admin::getSidebarPermission($myPermission,'a_property_c');
        $slug__a_report_c=Admin::getSidebarPermission($myPermission,'a_report_c');
        $slug__a_poster_reason_delete_c=Admin::getSidebarPermission($myPermission,'a_poster_reason_delete_c');
        $slug__a_poster=Admin::getSidebarPermission($myPermission,'a_poster');
        $slug__a_exchange=Admin::getSidebarPermission($myPermission,'exchange');
        $slug__a_experts=Admin::getSidebarPermission($myPermission,'a_experts');
        $slug__a_poster_expert=Admin::getSidebarPermission($myPermission,'a_poster_expert');
        $slug__a_employ=Admin::getSidebarPermission($myPermission,'a_employ');

        $slug__a_request_in_pending=Admin::getSidebarPermission($myPermission,'a_request_in_pending');
        $slug__a_request_in_accepted=Admin::getSidebarPermission($myPermission,'a_request_in_accepted');
        $slug__a_request_in_rejected=Admin::getSidebarPermission($myPermission,'a_request_in_rejected');
        $slug__a_request_in_progress=Admin::getSidebarPermission($myPermission,'a_request_in_progress');
        $slug__a_request_in_canceled=Admin::getSidebarPermission($myPermission,'a_request_in_canceled');
        $slug__a_request_in_completed=Admin::getSidebarPermission($myPermission,'a_request_in_completed');

        $slug__a_request_out_pending=Admin::getSidebarPermission($myPermission,'a_request_out_pending');
        $slug__a_request_out_accepted=Admin::getSidebarPermission($myPermission,'a_request_out_accepted');
        $slug__a_request_out_rejected=Admin::getSidebarPermission($myPermission,'a_request_out_rejected');
        $slug__a_request_out_progress=Admin::getSidebarPermission($myPermission,'a_request_out_progress');
        $slug__a_request_out_canceled=Admin::getSidebarPermission($myPermission,'a_request_out_canceled');
        $slug__a_request_out_completed=Admin::getSidebarPermission($myPermission,'a_request_out_completed');



        $slug__transportation_c=Admin::getSidebarPermission($myPermission,'transportation_c');
        $slug__driver_cv=Admin::getSidebarPermission($myPermission,'driver-cv');
        $slug__shipping_office=Admin::getSidebarPermission($myPermission,'ship-offices');

    ?>
            <div class="left-side-menu">

            <div class="h-100" data-simplebar>

                <!-- User box -->
                <div class="user-box text-center">
                    <img src="<?= Utils::fileExist($adminIfo->admin_avatar,USER_AVATAR); ?>" alt="<?= $adminIfo->admin_nickname; ?>"
                         title="<?= $adminIfo->admin_nickname; ?>"
                         class="rounded-circle avatar-md">
                    <div class="dropdown">
                        <a href="javascript: void(0);"
                           class="text-dark dropdown-toggle h5 mt-2 mb-1 d-block"
                           data-bs-toggle="dropdown"><?= $adminIfo->admin_nickname; ?></a>
                        <div class="dropdown-menu user-pro-dropdown">

                            <!-- item-->
                            <a href="/admin/myaccount" class="dropdown-item notify-item">
                                <i class="fe-user me-1"></i>
                                <span><?= $lang['my_account']; ?></span>
                            </a>

                            <!-- item-->
                            <a href="/admin/tasks" class="dropdown-item notify-item">
                                <i class="fe-clipboard me-1"></i>
                                <span><?= $lang['a_task']; ?></span>
                            </a>

                            <!-- item-->
                            <a href="/admin/lock-screen"
                               class="dropdown-item notify-item lock-screen-admin">
                                <i class="fe-lock me-1"></i>
                                <span><?= $lang['lock_screen']; ?></span>
                            </a>

                            <!-- item-->
                            <a href="javascript:void(0);" class="dropdown-item notify-item logoutAdmin">
                                <i class="fe-log-out me-1"></i>
                                <span><?= $lang['logout']; ?></span>
                            </a>

                        </div>
                    </div>
                    <p class="text-muted">
                        <?php
                        $admin_lang = "";
                        if (isset($_COOKIE['language']) && !empty($_COOKIE['language'])) {
                            $admin_lang = $_COOKIE['language'];
                        }
                        if (!empty($roleInfo) && !empty($admin_lang)) {
                            foreach (json_decode(json_decode($roleInfo)->role_name) as $temp) {
                                if ($temp->slug == $admin_lang) {
                                    echo $temp->value;
                                }
                            }
                        }
                        ?>
                    </p>
                </div>

                <!--- Sidemenu -->
                <div id="sidebar-menu">

                    <ul id="side-menu">

                        <li class="menu-title"><?= $lang['menu']; ?></li>

                        <li>
                            <a href="/admin">
                                <i data-feather="airplay"></i>
                                <span><?= $lang['dashboard']; ?></span>
                            </a>
                        </li>

                       <li class="menu-title"><?= $lang['general']; ?></li>

<?php
if($slug__users=="yes"  || $slug__notification=="yes" || $slug__balance=="yes" || $slug__a_authorization=="yes" || $slug__a_personel =='yes'||$slug__a_phonebook =='yes'){
 ?>
                        <li>
                            <a href="#sidebarUsers" data-bs-toggle="collapse">
                                 <i data-feather="users"></i>
                                <span><?= $lang['users']; ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarUsers">
                                <ul class="nav-second-level">
                                <?php if($slug__users=="yes"){ ?>
                                    <li>
                                        <a href="/admin/users"><?= $lang['user_list']; ?></a>
                                    </li>
                                    <?php }
                                    if($slug__notification=="yes"){ ?>
                                    <li class="d-none">
                                        <a href="/admin/notification"><?= $lang['notifications']; ?></a>
                                    </li>
                                    <?php }
                                    if($slug__balance=="yes"){ ?>
                                    <li>
                                        <a href="/admin/balance"><?= $lang['balance']; ?></a>
                                    </li>
                                    <?php }
                                    if($slug__a_authorization=="yes"){ ?>
                                    <li>
                                        <a href="/admin/authorization"><?= $lang['a_authorization_list']; ?></a>
                                    </li>
                                    <?php } ?>
                                    <?php if($slug__a_phonebook=="yes"){ ?>
                                    <li>
                                        <a href="/admin/pbook"><?= $lang['phonebook']; ?></a>
                                    </li>
                                    <?php } ?>

                                     <?php if($slug__a_personel=="yes"){ ?>
                                    <li>
                                        <a href="/admin/personel"><?= $lang['personel']; ?></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </li>
<?php } ?>



<?php
if($slug__request=="yes" || $slug__cargo=="yes" || $slug__cargo_in=="yes"
 || $slug__a_request_in_pending=="yes" || $slug__a_request_in_accepted=="yes" || $slug__a_request_in_rejected=="yes" || $slug__a_request_in_progress=="yes" || $slug__a_request_in_canceled=="yes" || $slug__a_request_in_completed=="yes"
 || $slug__a_request_out_pending=="yes" || $slug__a_request_out_accepted=="yes" || $slug__a_request_out_rejected=="yes" || $slug__a_request_out_progress=="yes" || $slug__a_request_out_canceled=="yes" || $slug__a_request_out_completed=="yes"){
 ?>
                        <li>
                            <a href="#sidebarCargos" data-bs-toggle="collapse">
                                <i data-feather="layers"></i>
                                <span><?=$lang['cargoes'] ;?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarCargos">
                                <ul class="nav-second-level">
                                <?php if($slug__request=="yes" || $slug__cargo=="yes"){ ?>
                                     <li>
                                         <a href="#sidebarCargoOut" data-bs-toggle="collapse">
                                            <?=$lang['a_cargo_out'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarCargoOut">
                                              <ul class="nav-second-level">
                                                <?php if($slug__cargo=="yes"){ ?>
                                                    <li>
                                                        <a href="/admin/cargo"><?=$lang['cargoes_show'] ;?></a>
                                                    </li>
                                                    <li>
                                                        <a href="/admin/cargo/pending"><?=$lang['cargo_pending'] ;?></a>
                                                    </li>
                                                     <li>
                                                        <a href="/admin/cargo/accepted"><?=$lang['cargo_accepted'] ;?></a>
                                                    </li>
                                                    <li>
                                                        <a href="/admin/cargo/progress"><?=$lang['cargo_progress'] ;?></a>
                                                    </li>
                                                    <?php }
                                                    if($slug__request=="yes"){ ?>
                                                    <li class="d-none">
                                                        <a href="/admin/request"><?=$lang['requests'] ;?></a>
                                                    </li>
                                                     <?php }

                                                    if($slug__a_request_out_pending=="yes" || $slug__a_request_out_accepted=="yes" || $slug__a_request_out_rejected=="yes" || $slug__a_request_out_progress=="yes" || $slug__a_request_out_canceled=="yes" || $slug__a_request_out_completed=="yes"){ ?>
                                                    <li class="">
                                                        <a href="/admin/request-out"><?=$lang['requests_out'] ;?></a>
                                                    </li>
                                                    <?php } ?>
                                                </ul>
                                         </div>
                                     </li>
                                   <?php
                                   }
                                   if($slug__cargo_in=="yes"){ ?>
                                   <li>
                                         <a href="#sidebarCargoIn" data-bs-toggle="collapse">
                                            <?=$lang['a_cargo_in'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarCargoIn">
                                              <ul class="nav-second-level">

                                                    <li>
                                                        <a href="/admin/cargo-in"><?=$lang['cargoes_show'] ;?></a>
                                                    </li>
                                                    <li>
                                                        <a href="/admin/cargo-in/pending"><?=$lang['cargo_pending'] ;?></a>
                                                    </li>
                                                     <li>
                                                        <a href="/admin/cargo-in/accepted"><?=$lang['cargo_accepted'] ;?></a>
                                                    </li>
                                                    <li>
                                                        <a href="/admin/cargo-in/progress"><?=$lang['cargo_progress'] ;?></a>
                                                    </li>
<?php
  if( $slug__a_request_in_pending=="yes" || $slug__a_request_in_accepted=="yes" || $slug__a_request_in_rejected=="yes" || $slug__a_request_in_progress=="yes" || $slug__a_request_in_canceled=="yes" || $slug__a_request_in_completed=="yes"){ ?>
                                                    <li class="">
                                                        <a href="/admin/request-in"><?=$lang['requests_in'] ;?></a>
                                                    </li>
                                                     <?php }
  ?>
                                                </ul>
                                         </div>
                                     </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </li>
<?php } ?>

<?php
if($slug__a_poster=="yes" || $slug__a_experts=="yes" || $slug__a_poster_expert=="yes"){
 ?>
                        <li>
                            <a href="#sidebarPoster" data-bs-toggle="collapse">
                                <i data-feather="file-text"></i>
                                <span><?=$lang['a_poster'] ;?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarPoster">
                                <ul class="nav-second-level">
                                <?php if($slug__a_poster=="yes"){ ?>
                                    <li>
                                        <a href="/admin/poster"><?=$lang['a_poster_list'] ;?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/poster/pending"><?=$lang['a_poster_list_pending'] ;?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/poster/reports"><?=$lang['a_list_poster_reports'] ;?></a>
                                    </li>
                                    <?php }
                                    if($slug__a_poster_expert=="yes"){ ?>
                                    <li>
                                        <a href="/admin/poster-expert"><?=$lang['a_request_expert'] ;?></a>
                                    </li>
                                    <?php }
                                    if($slug__a_experts=="yes"){ ?>
                                    <li>
                                        <a href="/admin/expert"><?=$lang['a_experts_list'] ;?></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </li>
<?php } ?>

<!-- start  exchange -->
                        <li>
                            <a href="#sidebarExchange" data-bs-toggle="collapse">
                                <i data-feather="file-text"></i>
                                <span><?=$lang['exchange'] ;?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarExchange">
                                <ul class="nav-second-level">

                                    <li>
                                        <a href="/admin/exchange"><?=$lang['live_price'] ;?></a>
                                    </li>


                                            <?php if($slug__a_exchange=="yes"){ ?>
                                    <li>
                                        <a href="/admin/exchange-setting-bonbast"><?=$lang['exchange'] ;?></a>
                                    </li>

                                    <?php } ?>
                                           <?php if($slug__a_exchange=="yes"){ ?>
                                    <li>
                                        <a href="/admin/exchange/request-list"><?=$lang['request_list'] ;?></a>
                                    </li>

                                    <?php } ?>
                                </ul>
                            </div>
                        </li>
 <!-- end exchange -->

<?php
if($slug__cars=="yes"){
 ?>
                        <li>
                            <a href="#sidebarCars" data-bs-toggle="collapse">
                                <i data-feather="truck"></i>
                                <span><?=$lang['cars'] ;?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarCars">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="/admin/car"><?=$lang['cars_list'] ;?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/car/pending"><?=$lang['cars_pending'] ;?></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
<?php } ?>

<?php
if($slug__tickets=="yes"){
 ?>
                        <li>
                            <a href="#sidebarTickets" data-bs-toggle="collapse">
                                <i data-feather="message-square"></i>
                                <span><?= $lang['tickets']; ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarTickets">
                                <ul class="nav-second-level">
                                <?php if($slug__tickets=="yes"){ ?>
                                    <li>
                                        <a href="/admin/ticket"><?= $lang['tickets_all']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/ticket/open"><?= $lang['tickets_open']; ?></a>
                                    </li>
                                    <?php }
                                    if($slug__tickets_driver=="yes"){ ?>
                                    <li>
                                        <a href="/admin/ticket/d/all/1"><?= $lang['tickets_all_driver']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/ticket/d/open/1"><?= $lang['tickets_open_driver']; ?></a>
                                    </li>
                                    <?php }
                                    if($slug__tickets_businessman=="yes"){ ?>
                                    <li>
                                        <a href="/admin/ticket/d/all/2"><?= $lang['tickets_all_businessman']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/ticket/d/open/2"><?= $lang['tickets_open_businessman']; ?></a>
                                    </li>
                                    <?php }
                                    if($slug__tickets_ship=="yes"){ ?>
                                    <li>
                                        <a href="/admin/ticket/d/all/3"><?= $lang['tickets_all_ship']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/ticket/d/open/3"><?= $lang['tickets_open_ship']; ?></a>
                                    </li>
                                    <?php }
                                    if($slug__tickets_air=="yes"){ ?>
                                    <li>
                                        <a href="/admin/ticket/d/all/4"><?= $lang['tickets_all_air']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/ticket/d/open/4"><?= $lang['tickets_open_air']; ?></a>
                                    </li>
                                    <?php }
                                    if($slug__tickets_railroad=="yes"){ ?>
                                    <li>
                                        <a href="/admin/ticket/d/all/5"><?= $lang['tickets_all_railroad']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/ticket/d/open/5"><?= $lang['tickets_open_railroad']; ?></a>
                                    </li>
                                    <?php }
                                    if($slug__tickets_inventory=="yes"){ ?>
                                    <li>
                                        <a href="/admin/ticket/d/all/6"><?= $lang['tickets_all_inventory']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/ticket/d/open/6"><?= $lang['tickets_open_inventory']; ?></a>
                                    </li>
                                     <?php }
                                    if($slug__tickets_poster=="yes"){ ?>
                                    <li>
                                        <a href="/admin/ticket/d/all/7"><?= $lang['tickets_all_poster']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/ticket/d/open/7"><?= $lang['tickets_open_poster']; ?></a>
                                    </li>
                                     <?php }
                                    if($slug__tickets_customs=="yes"){ ?>
                                    <li>
                                        <a href="/admin/ticket/d/all/8"><?= $lang['tickets_all_customs']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/ticket/d/open/8"><?= $lang['tickets_open_customs']; ?></a>
                                    </li>
                                    <?php }?>
                                </ul>
                            </div>
                        </li>
<?php } ?>

<?php if($slug__card_bank=="yes"){ ?>
                        <li>
                            <a href="#sidebarCardBank" data-bs-toggle="collapse">
                                <i data-feather="credit-card"></i>
                                <span><?= $lang['card_banks']; ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarCardBank">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="/admin/credit"><?= $lang['list_card_banks']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/credit/pending"><?= $lang['card_banks_pending']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/credit/status"><?= $lang['a_status_inquiry_bank']; ?></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
<?php } ?>

<?php
if($slug__transaction=="yes"){
 ?>
                        <li>
                            <a href="#sidebarTransaction" data-bs-toggle="collapse">
                                <i data-feather="dollar-sign"></i>
                                <span><?= $lang['transactions']; ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarTransaction">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="/admin/transaction"><?= $lang['list_transactions']; ?></a>
                                    </li>
                                      <li>
                                        <a href="/admin/transaction/withdraw"><?= $lang['list_withdraw']; ?></a>
                                    </li>
                                      <li>
                                        <a href="/admin/transaction/deposit"><?= $lang['list_deposit']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/transaction/withdraw/pending"><?= $lang['withdraws_pending']; ?></a>
                                    </li>
                                      <li>
                                        <a href="/admin/transaction/deposit/pending"><?= $lang['deposit_pending']; ?></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
<?php } ?>

<?php
if($slug__complaint=="yes"){
 ?>
                        <li>
                            <a href="#sidebarComplaint" data-bs-toggle="collapse">
                                <i data-feather="alert-triangle"></i>
                                <span><?= $lang['complaints']; ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarComplaint">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="/admin/complaint"><?= $lang['list_complaints']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/complaint/pending"><?= $lang['complaints_pending']; ?></a>
                                    </li>
                                      <li>
                                        <a href="/admin/complaint/accepted"><?= $lang['complaints_accepted']; ?></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
<?php } ?>

<?php
if($slug__ngroup=="yes"){
 ?>
                        <li>
                            <a href="#sidebarNoticG" data-bs-toggle="collapse">
                                <i data-feather="bell"></i>
                                <span><?= $lang['notifications_group']; ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarNoticG">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="/admin/ngroup"><?= $lang['notifications_all']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/ngroup/add"><?= $lang['notification_add']; ?></a>
                                    </li>
                                          <?php if($slug__a_phonebook_sms=="yes"){ ?>
                                    <li>
                                        <a href="/admin/pbook-sms"><?= $lang['phonebook_sms']; ?></a>
                                    </li>
                                    <?php } ?>

<?php
if($slug__share_whatsapp=="yes"){
    ?>


                                    <li>
                                        <a href="/admin/share/whatsapp"><?= $lang['a_list_share_whatsapp']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/share/whatsapp/default"><?= $lang['a_whatsapp_default_text_2']; ?></a>
                                    </li>



          <?php } ?>
                                </ul>
                            </div>
                        </li>
<?php } ?>

<?php
if($slug__posts=="yes" || $slug__posts_c=="yes"){
 ?>
                        <li>
                            <a href="#sidebarBlogs" data-bs-toggle="collapse">
                                <i data-feather="layout"></i>
                                <span><?= $lang['posts']; ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarBlogs">
                                <ul class="nav-second-level">
                                <?php if($slug__posts=="yes"){ ?>
                                    <li>
                                        <a href="/admin/post"><?= $lang['list_posts']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/post/add"><?= $lang['set_new_post']; ?></a>
                                    </li>
                                    <?php }
                                    if($slug__posts_c=="yes"){ ?>
                                    <li>
                                        <a href="/admin/category/post"><?= $lang['categories']; ?></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </li>
<?php } ?>

<?php
if($slug__academy=="yes" || $slug__academy_c=="yes"){
 ?>
                        <li>
                            <a href="#sidebarAcademy" data-bs-toggle="collapse">
                                <i data-feather="printer"></i>
                                <span><?= $lang['academy']; ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarAcademy">
                                <ul class="nav-second-level">
                                <?php if($slug__academy=="yes"){ ?>
                                    <li>
                                        <a href="/admin/academy"><?= $lang['list_academy']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/academy/add"><?= $lang['set_new_academy']; ?></a>
                                    </li>
                                    <?php }
                                    if($slug__academy_c=="yes"){ ?>
                                    <li>
                                        <a href="/admin/category/academy"><?= $lang['categories']; ?></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </li>
<?php } ?>

<?php
if($slug__inquiry_ground=="yes" || $slug__inquiry_air=="yes" || $slug__inquiry_ship=="yes"  || $slug__inquiry_railroad=="yes" || $slug__inquiry_inventory=="yes" || $slug__inquiry_customs=="yes"|| $slug__inquiry_minicargo=="yes"){
 ?>
                        <li>
                            <a href="#sidebarInquiry" data-bs-toggle="collapse">
                                <i data-feather="trending-up"></i>
                                <span><?=$lang['call_for_price'] ;?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarInquiry">
                                <ul class="nav-second-level">
                                <?php if($slug__inquiry_ground=="yes"){ ?>
                                     <li>
                                         <a href="#sidebarGroundInquiry" data-bs-toggle="collapse">
                                            <?=$lang['a_transportation_ground'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarGroundInquiry">
                                             <ul class="nav-second-level">
                                                 <li>
                                                     <a href="/admin/inquiry/ground"><?=$lang['inquiry_list'] ;?></a>
                                                 </li>
                                                  <li>
                                                     <a href="/admin/inquiry/ground/pending"><?=$lang['inquiry_pending'] ;?></a>
                                                 </li>
                                                  <li>
                                                     <a href="/admin/inquiry/ground/process"><?=$lang['inquiry_process'] ;?></a>
                                                 </li>
                                             </ul>
                                         </div>
                                     </li>
                                     <?php }
                                      if($slug__inquiry_ship=="yes"){ ?>
                                     <li>
                                         <a href="#sidebarShipInquiry" data-bs-toggle="collapse">
                                            <?=$lang['a_transportation_ship'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarShipInquiry">
                                             <ul class="nav-second-level">
                                                 <li>
                                                     <a href="/admin/inquiry/ship"><?=$lang['inquiry_list'] ;?></a>
                                                 </li>
                                                 <li>
                                                     <a href="/admin/inquiry/ship/pending"><?=$lang['inquiry_pending'] ;?></a>
                                                 </li>
                                                  <li>
                                                     <a href="/admin/inquiry/ship/process"><?=$lang['inquiry_process'] ;?></a>
                                                 </li>
                                             </ul>
                                         </div>
                                     </li>
                                      <?php }
                                      if($slug__inquiry_air=="yes"){ ?>
                                     <li>
                                         <a href="#sidebarAirInquiry" data-bs-toggle="collapse">
                                            <?=$lang['a_transportation_air'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarAirInquiry">
                                             <ul class="nav-second-level">
                                                 <li>
                                                     <a href="/admin/inquiry/air"><?=$lang['inquiry_list'] ;?></a>
                                                 </li>
                                                 <li>
                                                     <a href="/admin/inquiry/air/pending"><?=$lang['inquiry_pending'] ;?></a>
                                                 </li>
                                                  <li>
                                                     <a href="/admin/inquiry/air/process"><?=$lang['inquiry_process'] ;?></a>
                                                 </li>
                                             </ul>
                                         </div>
                                     </li>
                                     <?php }
                                      if($slug__inquiry_railroad=="yes"){ ?>
                                     <li>
                                         <a href="#sidebarRailroadInquiry" data-bs-toggle="collapse">
                                            <?=$lang['a_transportation_railroad'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarRailroadInquiry">
                                             <ul class="nav-second-level">
                                                 <li>
                                                     <a href="/admin/inquiry/railroad"><?=$lang['inquiry_list'] ;?></a>
                                                 </li>
                                                 <li>
                                                     <a href="/admin/inquiry/railroad/pending"><?=$lang['inquiry_pending'] ;?></a>
                                                 </li>
                                                 <li>
                                                     <a href="/admin/inquiry/railroad/process"><?=$lang['inquiry_process'] ;?></a>
                                                 </li>
                                             </ul>
                                         </div>
                                     </li>
                                     <?php }
                                      if($slug__inquiry_inventory=="yes"){ ?>
                                     <li>
                                         <a href="#sidebarInventoryInquiry" data-bs-toggle="collapse">
                                            <?=$lang['a_inventory'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarInventoryInquiry">
                                             <ul class="nav-second-level">
                                                 <li>
                                                     <a href="/admin/inquiry/inventory"><?=$lang['inquiry_list'] ;?></a>
                                                 </li>
                                                 <li>
                                                     <a href="/admin/inquiry/inventory/pending"><?=$lang['inquiry_pending'] ;?></a>
                                                 </li>
                                                 <li>
                                                     <a href="/admin/inquiry/inventory/process"><?=$lang['inquiry_process'] ;?></a>
                                                 </li>
                                             </ul>
                                         </div>
                                     </li>
                                       <?php }
                                      if($slug__inquiry_customs=="yes"){ ?>
                                     <li>
                                         <a href="#sidebarCustomsInquiry" data-bs-toggle="collapse">
                                            <?=$lang['a_inquiry_customs'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarCustomsInquiry">
                                             <ul class="nav-second-level">
                                                 <li>
                                                     <a href="/admin/inquiry/customs"><?=$lang['inquiry_list'] ;?></a>
                                                 </li>
                                                 <li>
                                                     <a href="/admin/inquiry/customs/pending"><?=$lang['inquiry_pending'] ;?></a>
                                                 </li>
                                                 <li>
                                                     <a href="/admin/inquiry/customs/process"><?=$lang['inquiry_process'] ;?></a>
                                                 </li>
                                             </ul>
                                         </div>
                                     </li>
                                     <?php } ?>
                                      <?php if($slug__inquiry_minicargo=="yes"){ ?>
                                     <li>
                                         <a href="#sidebarMiniCargoInquiry" data-bs-toggle="collapse">
                                            <?=$lang['a_inquiry_minicargo'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarMiniCargoInquiry">
                                             <ul class="nav-second-level">
                                                 <li>
                                                     <a href="/admin/inquiry/minicargo"><?=$lang['inquiry_list'] ;?></a>
                                                 </li>
                                                 <li>
                                                     <a href="/admin/inquiry/minicargo/pending"><?=$lang['inquiry_pending'] ;?></a>
                                                 </li>
                                                 <li>
                                                     <a href="/admin/inquiry/minicargo/process"><?=$lang['inquiry_process'] ;?></a>
                                                 </li>
                                             </ul>
                                         </div>
                                     </li>
                                     <?php } ?>
                                </ul>
                            </div>
                        </li>
<?php } ?>


          <?php
if($slug__medias=="yes"){
 ?>
                         <li>
                            <a href="/admin/medias">
                                <i data-feather="folder-plus"></i>
                                <span><?= $lang['multimedia']; ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="/admin/media">
                                <i data-feather="folder-plus"></i>
                                <span><?= $lang['multimedia']; ?></span>
                            </a>
                        </li>
<?php } ?>
                        <li class="menu-title"><?= $lang['assign']; ?></li>

<?php
if($slug__a_employ=="yes"){
 ?>
                        <li>
                            <a href="#sidebarHire" data-bs-toggle="collapse">
                                <i data-feather="user-plus"></i>
                                <span><?= $lang['a_employ']; ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarHire">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="/admin/hire"><?= $lang['a_list_requests']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/hire/category"><?= $lang['a_employs_titles']; ?></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
<?php } ?>

<?php
if($slug__admins=="yes"){
 ?>
                        <li>
                            <a href="#sidebarAdmins" data-bs-toggle="collapse">
                                <i data-feather="globe"></i>
                                <span><?= $lang['admins']; ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarAdmins">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="/admin/admin"><?= $lang['list_admins']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/admin/log"><?= $lang['admins_log']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/admin/add"><?= $lang['set_new_admin']; ?></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
<?php } ?>


<?php
if($slug__a_page_tasks_admin=="yes" || $slug__a_task=="yes"){
 ?>
                        <li>
                            <a href="#sidebarTasks" data-bs-toggle="collapse">
                                <i data-feather="clipboard"></i>
                                <span><?= $lang['a_tasks']; ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarTasks">
                                <ul class="nav-second-level">
                                    <?php
                                    if($slug__a_page_tasks_admin=="yes"){
                                     ?>
                                    <li>
                                        <a href="/admin/tasks/all"><?= $lang['a_list_task']; ?></a>
                                    </li>
                                    <?php
                                    }
                                    if($slug__a_task=="yes"){
                                     ?>
                                    <li>
                                        <a href="/admin/tasks"><?= $lang['a_task']; ?></a>
                                    </li>
                                    <?php  } ?>
                                </ul>
                            </div>
                        </li>
<?php } ?>
<?php
if($slug__driver_cv=="yes"  ){
 ?>
                        <li>
                            <a href="#sidebarDriverServices" data-bs-toggle="collapse">
                                <i data-feather="clipboard"></i>
                                <span><?= $lang['driver-cv']; ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarDriverServices">
                                <ul class="nav-second-level">
                                    <?php
                                    if($slug__driver_cv=="yes"){
                                     ?>
                                    <li>
                                        <a href="/admin/driver-services"><?= $lang['driver-cv-list']; ?></a>
                                    </li>
                                    <?php
                                    }
                                     ?>
                                </ul>
                            </div>
                        </li>
<?php } ?>
<?php
if($slug__shipping_office=="yes"  ){
 ?>
                        <li>
                            <a href="#sidebarShippingOffice" data-bs-toggle="collapse">
                                <i data-feather="clipboard"></i>
                                <span><?= $lang['office']; ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarShippingOffice">
                                <ul class="nav-second-level">
                                    <?php
                                    if($slug__shipping_office=="yes"){
                                     ?>
                                    <li>
                                        <a href="/admin/office"><?= $lang['list_offices']; ?></a>
                                    </li>
                                    <?php
                                    }
                                     ?>
                                </ul>
                            </div>
                        </li>
<?php } ?>

<?php if($slug__census=="yes"){ ?>
                        <li>
                            <a href="#sidebarChart" data-bs-toggle="collapse">
                                <i data-feather="pie-chart"></i>
                                <span><?= $lang['census']; ?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarChart">
                                <ul class="nav-second-level">
                                    <li>
                                        <a href="/admin/census/admin"><?= $lang['census_admin']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/census/admins"><?= $lang['census_admins']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/census/transaction"><?= $lang['census_transaction']; ?></a>
                                    </li>
                                    <li>
                                        <a href="/admin/census/credit"><?= $lang['a_cart_inquiry']; ?></a>
                                    </li>
                                     <li>
                                        <a href="/admin/census/general"><?= $lang['a_report_general']; ?></a>
                                    </li>
                                </ul>
                            </div>
                        </li>
<?php } ?>

<?php
if($slug__cargo_c=="yes" || $slug__cars_c=="yes" || $slug__currency_c=="yes" ||
$slug__country_c=="yes" || $slug__port_c=="yes" || $slug__container_c=="yes" || $slug__ship_cargo_c=="yes" || $slug__airport_c=="yes"
 || $slug__air_cargo_c=="yes" || $slug__ship_packing_c=="yes" || $slug__air_packing_c=="yes" || $slug__railroad_c=="yes" || $slug__wagon_c=="yes"
  || $slug__railroad_cargo_c=="yes" || $slug__railroad_packing_c=="yes" || $slug__city_c=="yes" || $slug__customs_c=="yes"
  || $slug__container_railroad_c=="yes" || $slug__inventory_cargo_c=="yes" ||
   $slug__inventory_c=="yes" || $slug__inventory_type_c=="yes" ||
   $slug__brands=="yes" ||  $slug__a_fuels=="yes" ||  $slug__a_gearboxs=="yes" ||  $slug__a_model_c=="yes" ||
   $slug__a_property_c=="yes" ||  $slug__a_report_c=="yes" || $slug__a_poster_reason_delete_c=="yes"){
 ?>
                        <li>
                            <a href="#sidebarCategory" data-bs-toggle="collapse">
                                <i data-feather="grid"></i>
                                <span><?=$lang['category'] ;?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarCategory">
                                <ul class="nav-second-level">
                                <?php
                                if($slug__cargo_c=="yes" || $slug__cars_c=="yes" || $slug__customs_c=="yes"){
                                ?>
                                      <li>
                                         <a href="#sidebarGround" data-bs-toggle="collapse">
                                            <?=$lang['a_transportation_ground'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarGround">
                                             <ul class="nav-second-level">
                                             <?php if( $slug__cars_c=="yes" ){ ?>
                                                 <li>
                                                     <a href="/admin/category/car"><?=$lang['a_type_car'] ;?></a>
                                                 </li>
                                                 <?php }
                                                 if( $slug__cargo_c=="yes" ){ ?>
                                                  <li>
                                                     <a href="/admin/category/cargo"><?=$lang['a_type_cargo'] ;?></a>
                                                 </li>
                                                 <?php }
                                                 if( $slug__customs_c=="yes" ){ ?>
                                                  <li>
                                                     <a href="/admin/category/customs"><?=$lang['customs_list'] ;?></a>
                                                 </li>
                                                 <?php } ?>
                                             </ul>
                                         </div>
                                     </li>
                                <?php }
                                if($slug__ship_cargo_c=="yes" || $slug__port_c=="yes" || $slug__container_c=="yes" || $slug__ship_packing_c=="yes"){
                                ?>
                                     <li>
                                         <a href="#sidebarShip" data-bs-toggle="collapse">
                                            <?=$lang['a_transportation_ship'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarShip">
                                             <ul class="nav-second-level">
                                             <?php if($slug__port_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/port"><?=$lang['ports_list'] ;?></a>
                                                 </li>
                                             <?php }
                                             if($slug__container_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/container"><?=$lang['a_type_container'] ;?></a>
                                                 </li>
                                             <?php }
                                             if($slug__ship_cargo_c=="yes"){ ?>
                                                  <li>
                                                     <a href="/admin/category/ship-cargo"><?=$lang['a_type_cargo'] ;?></a>
                                                 </li>
                                             <?php }
                                             if($slug__ship_packing_c=="yes"){ ?>
                                                  <li>
                                                     <a href="/admin/category/ship-packing"><?=$lang['a_type_packing'] ;?></a>
                                                 </li>
                                              <?php } ?>
                                             </ul>
                                         </div>
                                     </li>
                                <?php }
                                if($slug__airport_c=="yes" || $slug__air_cargo_c=="yes" || $slug__air_packing_c=="yes"){
                                ?>
                                     <li>
                                         <a href="#sidebarAir" data-bs-toggle="collapse">
                                            <?=$lang['a_transportation_air'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarAir">
                                             <ul class="nav-second-level">
                                             <?php if($slug__airport_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/airport"><?=$lang['airport_list'] ;?></a>
                                                 </li>
                                             <?php }
                                             if($slug__air_cargo_c=="yes"){?>
                                                 <li>
                                                     <a href="/admin/category/air-cargo"><?=$lang['a_type_cargo'] ;?></a>
                                                 </li>
                                             <?php }
                                             if($slug__air_packing_c=="yes"){?>
                                                  <li>
                                                     <a href="/admin/category/air-packing"><?=$lang['a_type_packing'] ;?></a>
                                                 </li>
                                             <?php } ?>
                                             </ul>
                                         </div>
                                     </li>
                                <?php }
                                if($slug__railroad_c=="yes" || $slug__wagon_c=="yes" || $slug__railroad_cargo_c=="yes" || $slug__railroad_packing_c=="yes" || $slug__container_railroad_c=="yes"){ ?>
                                     <li>
                                         <a href="#sidebarRailroad" data-bs-toggle="collapse">
                                            <?=$lang['a_transportation_railroad'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarRailroad">
                                             <ul class="nav-second-level">
                                             <?php if($slug__railroad_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/railroad"><?=$lang['railroad_list'] ;?></a>
                                                 </li>
                                             <?php }
                                             if($slug__wagon_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/wagon"><?=$lang['a_type_wagons'] ;?></a>
                                                 </li>
                                              <?php }
                                             if($slug__railroad_cargo_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/railroad-cargo"><?=$lang['a_type_cargo'] ;?></a>
                                                 </li>
                                             <?php }
                                              if($slug__container_railroad_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/container-railroad"><?=$lang['a_type_container'] ;?></a>
                                                 </li>
                                             <?php }
                                             if($slug__railroad_packing_c=="yes"){ ?>
                                                  <li>
                                                     <a href="/admin/category/railroad-packing"><?=$lang['a_type_packing'] ;?></a>
                                                 </li>
                                             <?php } ?>

                                             </ul>
                                         </div>
                                     </li>
                               <?php }
                                if($slug__inventory_c=="yes" ||  $slug__inventory_cargo_c=="yes"){ ?>
                                     <li>
                                         <a href="#sidebarInventory" data-bs-toggle="collapse">
                                            <?=$lang['a_inventory'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarInventory">
                                             <ul class="nav-second-level">
                                                <?php if($slug__inventory_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/inventory"><?=$lang['inventory_list'] ;?></a>
                                                 </li>
                                              <?php }
                                             if($slug__inventory_cargo_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/inventory-cargo"><?=$lang['a_type_cargo'] ;?></a>
                                                 </li>
                                             <?php }
                                             if($slug__inventory_type_c=="yes"){ ?>
                                                <li>
                                                     <a href="/admin/category/inventory-type"><?=$lang['a_inventory_type'] ;?></a>
                                                 </li>
                                             <?php } ?>
                                             </ul>
                                         </div>
                                     </li>
                                     <?php
                                      }
                                if($slug__transportation_c=="yes" ){ ?>
                                     <li>
                                         <a href="#sidebar-inquiry-customs" data-bs-toggle="collapse">
                                            <?=$lang['customs_service'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebar-inquiry-customs">
                                             <ul class="nav-second-level">
                                                <?php if($slug__transportation_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/transportation"><?=$lang['customs_transportation_list'] ;?></a>
                                                 </li>
                                              <?php } ?>


                                             </ul>
                                         </div>
                                     </li>
                                     <?php
                                      }

                                if($slug__brands=="yes" ||  $slug__a_fuels=="yes" ||  $slug__a_gearboxs=="yes" ||  $slug__a_model_c=="yes"
                                 ||  $slug__a_property_c=="yes" ||  $slug__a_report_c=="yes" || $slug__a_poster_reason_delete_c=="yes"){ ?>

                                    <li>
                                         <a href="#sidebarPosterC" data-bs-toggle="collapse">
                                            <?=$lang['a_poster'];?>
                                             <span class="menu-arrow"></span>
                                         </a>
                                         <div class="collapse" id="sidebarPosterC">
                                             <ul class="nav-second-level">
                                                <?php if($slug__brands=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/brand"><?=$lang['a_list_brands'] ;?></a>
                                                 </li>
                                                 <?php }
                                                 if($slug__a_model_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/model"><?=$lang['a_list_models'] ;?></a>
                                                 </li>
                                                 <?php }
                                                 if($slug__a_gearboxs=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/gearbox"><?=$lang['a_list_gearboxs'] ;?></a>
                                                 </li>
                                                 <?php }
                                                 if($slug__a_fuels=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/fuel"><?=$lang['a_list_fuels'] ;?></a>
                                                 </li>
                                                 <?php }
                                                 if($slug__a_property_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/property"><?=$lang['a_list_property'] ;?></a>
                                                 </li>
                                                 <?php }
                                                 if($slug__a_report_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/report"><?=$lang['a_list_reports'] ;?></a>
                                                 </li>
                                                 <?php }
                                                 if($slug__a_poster_reason_delete_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/poster-delete"><?=$lang['a_list_reason_delete'] ;?></a>
                                                 </li>
                                                 <?php } ?>
                                             </ul>
                                         </div>
                                     </li>
                                      <?php
                                      }
                                    if($slug__country_c=="yes"){ ?>
                                     <li>
                                        <a href="/admin/country"><?=$lang['category_countries'] ;?></a>
                                    </li>
                                    <?php }
                                    if($slug__city_c=="yes"){ ?>
                                    <li>
                                        <a href="/admin/city"><?=$lang['category_cities'] ;?></a>
                                    </li>
                                    <?php }
                                    if($slug__currency_c=="yes"){ ?>
                                     <li>
                                        <a href="/admin/currency"><?=$lang['currency_list'] ;?></a>
                                    </li>
                                    <?php } ?>
                                       <?php if($slug__visa_location_c=="yes"){ ?>
                                                 <li>
                                                     <a href="/admin/category/visa-location"><?=$lang['visa_location_list'] ;?></a>
                                                 </li>
                                             <?php }?>
                                </ul>
                            </div>
                        </li>
<?php } ?>

<?php
if($slug__settings_general=="yes" || $slug__settings_languages=="yes" || $slug__settings_site=="yes" ||
$slug__settings_payment=="yes" || $slug__settings_sms=="yes" || $slug__field_c=="yes" || $slug__settings_seo=="yes" ||
$slug__settings_poster=="yes" || $slug__admins=="yes"){
 ?>
                        <li>
                            <a href="#sidebarSettings" data-bs-toggle="collapse">
                                <i data-feather="settings"></i>
                                <span><?=$lang['settings'] ;?></span>
                                <span class="menu-arrow"></span>
                            </a>
                            <div class="collapse" id="sidebarSettings">
                                <ul class="nav-second-level">
                                <?php if($slug__settings_general=="yes"){ ?>
                                    <li>
                                        <a href="/admin/settings"><?=$lang['general_settings'] ;?></a>
                                    </li>
                                    <?php }
                                    if($slug__settings_site=="yes"){ ?>
                                     <li>
                                        <a href="/admin/settings/site"><?=$lang['settings_site'] ;?></a>
                                    </li>
                                    <?php }
                                    if($slug__settings_languages=="yes"){ ?>
                                    <li>
                                        <a href="/admin/settings/languages"><?=$lang['settings_language'] ;?></a>
                                    </li>
                                    <?php }
                                    if($slug__settings_payment=="yes"){ ?>
                                    <li>
                                        <a href="/admin/settings/payment"><?=$lang['settings_payment'] ;?></a>
                                    </li>
                                    <?php }
                                    if($slug__settings_sms=="yes"){ ?>
                                    <li>
                                        <a href="/admin/settings/sms"><?=$lang['settings_sms'] ;?></a>
                                    </li>
                                    <?php }
                                    if($slug__settings_seo=="yes"){ ?>
                                    <li>
                                        <a href="/admin/settings/seo"><?=$lang['seo_setting'] ;?></a>
                                    </li>
                                    <?php }
                                    if($slug__field_c=="yes"){?>
                                    <li>
                                        <a href="/admin/settings/field"><?=$lang['auth_fields'] ;?></a>
                                    </li>
                                      <?php }
                                    if($slug__settings_poster=="yes"){?>
                                    <li>
                                        <a href="/admin/settings/poster"><?=$lang['settings_poster'] ;?></a>
                                    </li>
                                     <?php }
                                    if($slug__admins=="yes"){?>
                                    <li>
                                        <a href="/admin/settings/security"><?=$lang['settings_security'] ;?></a>
                                    </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </li>
<?php } ?>

                    </ul>

                </div>
                <!-- End Sidebar -->

                <div class="clearfix"></div>

            </div>
            <!-- Sidebar -left -->

        </div>
    <?php
}



function getPermissionsBlock(){
    global $lang;
    ?>
     <div class="row justify-content-center">
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="error-text-box">
                    <svg viewBox="0 0 600 200">
                        <!-- Symbol-->
                        <symbol id="s-text">
                            <text text-anchor="middle" x="50%" y="50%" dy=".35em"><?= $lang['oppss']; ?></text>
                        </symbol>
                        <!-- Duplicate symbols-->
                        <use class="text" xlink:href="#s-text"></use>
                        <use class="text" xlink:href="#s-text"></use>
                        <use class="text" xlink:href="#s-text"></use>
                        <use class="text" xlink:href="#s-text"></use>
                        <use class="text" xlink:href="#s-text"></use>
                    </svg>
                </div>
                <div class="text-center">
                    <h3 class="mt-0 mb-2"><?= $lang['oppss2']; ?></h3>
                    <p class="text-muted mb-3">
                        <?= $lang['oppss3']; ?>
                    </p>

                    <a href="/admin" class="btn btn-success waves-effect waves-light"><?= $lang['oppss4']; ?></a>
                </div>
                <!-- end row -->

            </div> <!-- end col -->
        </div>
        <!-- end row -->
    <?php
}


function getSidebarLeft(){
       global $lang;
       /**
         * Get All Open Tickets
         */
        $resultTicketsOpen = ATicket::getTicketsByStatus('open');
        $dataTicketsOpen= [];
        if ($resultTicketsOpen->status == 200 && !empty($resultTicketsOpen->response)) {
            $dataTicketsOpen = $resultTicketsOpen->response;
        }



           /**
         * Get All cars
         */
        $resultAllCarsPendingInfo = Car::getAllCars("pending");
        $dataAllCarsPendingInfo = [];
        if ($resultAllCarsPendingInfo->status == 200 && !empty($resultAllCarsPendingInfo->response)) {
            $dataAllCarsPendingInfo = $resultAllCarsPendingInfo->response;
        }
    ?>
            <!-- Right Sidebar -->
        <div class="right-bar">
            <div data-simplebar class="h-100">

                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-bordered nav-justified" role="tablist">

                    <li class="nav-item ">
                        <a class="nav-link py-2 active position-relative" data-bs-toggle="tab" href="#chat-tab" role="tab">

                            <i class="mdi mdi-message-text d-block font-22 my-1"></i>
                            <?php  if(count($dataTicketsOpen)>0){  ?>
                                <span class="badge bg-danger rounded-circle noti-icon-badge" style="display: inline-block;position: absolute;top: 16px;left: 25px;"><?=count($dataTicketsOpen);?></span>
                            <?php } ?>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link py-2" data-bs-toggle="tab" href="#settings-tab" role="tab">
                            <i class="mdi mdi-dump-truck d-block font-22 my-1"></i>
                                 <?php  if(count($dataAllCarsPendingInfo)>0){  ?>
                                <span class="badge bg-danger rounded-circle noti-icon-badge" style="display: inline-block;position: absolute;top: 16px;left: 25px;"><?=count($dataAllCarsPendingInfo);?></span>
                            <?php } ?>
                        </a>
                    </li>

                </ul>

                <!-- Tab panes -->
                <div class="tab-content pt-0">

                    <!--ticket-->
                    <div class="tab-pane active" id="chat-tab" role="tabpanel">
                        <h6 class="fw-medium px-3 m-0 py-2 font-13 text-uppercase bg-light">
                            <span class="d-block py-1"><?=$lang['a_open_ticket'];?></span>
                        </h6>

                        <div class="p-2">
                        <?php
                            if(!empty($dataTicketsOpen)){
                                foreach ($dataTicketsOpen as $dataTicketsDriverITEM){
                             ?>
                            <a href="/admin/ticket/open/<?=$dataTicketsDriverITEM->ticket_id; ?>"
                               class="text-reset notification-item">
                                <div class="d-flex align-items-start noti-user-item">
                                    <div class="position-relative me-2">
                                        <div class="rounded-circle avatar-sm mj-notify-item bg_pr">
                                          <i class="mdi mdi-message-text text-white"></i>
                                     </div>

                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="mt-0 mb-1 font-14"><?=$dataTicketsDriverITEM->ticket_title ;?></h6>
                                        <div class="font-13 text-muted">
                                            <p class="mb-0 text-truncate"><?=Utils::timeElapsedString('@'.$dataTicketsDriverITEM->ticket_submit_date);?></p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                             <?php
                             }
                                }else{
                                ?>
                                <div class="alert alert-info" role="alert">
                                            <i class="mdi mdi-hand-heart me-2"></i><?=$lang['no_ticket'];?>
                                </div>
                                <?php } ?>
                        </div>
                    </div>

                    <!--cars-->
                    <div class="tab-pane " id="settings-tab" role="tabpanel">
                        <h6 class="fw-medium px-3 m-0 py-2 font-13 text-uppercase bg-light">
                            <span class="d-block py-1"><?=$lang['cars_pending'];?></span>
                        </h6>

                        <div class="p-2">
                        <?php
                            if(!empty($dataAllCarsPendingInfo)){
                                foreach ($dataAllCarsPendingInfo as $dataAllCarsPendingInfoITEM){
                             ?>
                            <a href="/admin/car/<?=$dataAllCarsPendingInfoITEM->car_id; ?>"
                               class="text-reset notification-item">
                                <div class="d-flex align-items-start noti-user-item">
                                    <div class="position-relative me-2">
                                        <div class="rounded-circle avatar-sm mj-notify-item bg-pink">
                                          <i class="mdi mdi-tanker-truck text-white"></i>
                                     </div>

                                    </div>
                                    <div class="overflow-hidden">
                                        <h6 class="mt-0 mb-1 font-14"><?= $dataAllCarsPendingInfoITEM->car_plaque;?></h6>
                                        <div class="font-13 text-muted">
                                            <p class="mb-0 text-truncate"><?=Utils::timeElapsedString('@'.$dataAllCarsPendingInfoITEM->car_submit_date);?></p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                             <?php
                             }
                                }else{
                                ?>
                                <div class="alert alert-info" role="alert">
                                            <i class="mdi mdi-hand-heart me-2"></i><?=$lang['no_new_cars'];?>
                                </div>
                                <?php } ?>
                        </div>

                    </div>

                </div>

            </div> <!-- end slimscroll-menu-->
        </div>
        <!-- /Right-bar -->
    <?php
}


function getOffcanvas($myNotic=[],$video='/'){
  global $lang;
  ?>
    <!-- start Canvas -->
        <div class="offcanvas offcanvas-bottom" tabindex="-1" id="offcanvasExample"
             aria-labelledby="offcanvasExampleLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="offcanvasExampleLabel">
                    <?= $lang['help']; ?>
                </h5>
                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <!-- end offcanvas-header-->

            <div class="offcanvas-body">
                <ul class="ps-3">
                <?php
                   if(!empty($myNotic)){
                       foreach ($myNotic as $myNoticLOOP){
                  ?>
                    <li class=""><?= $myNoticLOOP; ?></li>
                    <?php
                       }
                   }else{
                       ?>
                           <li class=""><?= $lang['help_not_desc_for_page']; ?></li>
                       <?php
                   }
                   ?>
                </ul>
                <div>
                    <?= $lang['help_notic']; ?>
                </div>
                <div>
                    <?= $lang['help_notic_1']; ?>
                </div>

                  <button id="show-edu-video" data-bs-dismiss="offcanvas" class="btn btn-sm btn-outline-info mt-2"   data-video="/documentation/123123123.mp4" type="button"  >Play Video</button>

            </div> <!-- end offcanvas-body-->
        </div>
        <!-- start Canvas -->


  <div class="modal fade" id="show-edu-video-modal" tabindex="-1" role="dialog"  aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-body">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          <video controls width="100%">
            <source src="<?=$video;?>" type="video/mp4">
          </video>
        </div>
      </div>
    </div>
  </div>
  <?php
}