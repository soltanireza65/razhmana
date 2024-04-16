<?php
global $lang,$Settings;

use MJ\Utils\Utils;

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Admin.php';

setcookie('UID', null, -1, '/');
setcookie('EID', null, -1, '/');
setcookie('INF', null, -1, '/');
unset($_COOKIE['UID']);
unset($_COOKIE['EID']);
unset($_COOKIE['INF']);
?>

<!DOCTYPE html>
<html lang="fa" dir="<?=Utils::getThemeDirection();?>">
<head>
    <meta charset="utf-8"/>
    <title><?= $lang['login_admin'] . ' | ' . $Settings['site_name']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <!-- App favicon -->
    <link rel="shortcut icon" href="<?= Utils::fileExist('uploads/site/favicon.webp', BOX_EMPTY); ?>">


    <link href="<?= SITE_URL; ?>/dist/libs/jquery-toast-plugin/jquery.toast.min.css" rel="stylesheet" type="text/css"
          id="toast"/>
    <link href="<?= SITE_URL; ?>/dist/libs/ladda/ladda.min.css" rel="stylesheet" type="text/css"
          id="ladda"/>

    <!-- App css -->
    <link href="<?= SITE_URL; ?>/dist/css/admin/app<?=(Utils::getThemeDirection()=='rtl')?"-rtl":null ;?>.min.css" rel="stylesheet"
          type="text/css"
          id="app-style"/>

    <!-- icons -->
    <link href="<?= SITE_URL; ?>/dist/css/admin/icons.min.css" rel="stylesheet" type="text/css"
          id="icons.min"/>

    <link href="<?= SITE_URL; ?>/dist/css/admin/fontiran.css" rel="stylesheet" type="text/css"
          id="fontiran"/>
</head>

<body class="loading authentication-bg authentication-bg-pattern" data-layout-color="<?= $_COOKIE['theme']; ?>">
<!-- start canvas-->
<style>
    canvas {
        display: block;
        vertical-align: bottom;

    }
    /* ---- particles.js container ---- */
    #particles-js {
        position: absolute;
        width: 100%;
        height: 100vh;
        background-repeat: no-repeat;
        background-size: cover;
        background-position: 50% 50%;
        top: 0;
        bottom: 0;
    }
    .input-group > :not(:first-child):not(.dropdown-menu):not(.valid-tooltip):not(.valid-feedback):not(.invalid-tooltip):not(.invalid-feedback){
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;

        border-top-left-radius: 0.2rem;
        border-bottom-left-radius: 0.2rem;
    }
    .input-group:not(.has-validation) > .dropdown-toggle:nth-last-child(n+3), .input-group:not(.has-validation) > :not(:last-child):not(.dropdown-toggle):not(.dropdown-menu){
        border-top-left-radius: 0 !important;
        border-bottom-left-radius: 0 !important;

        border-top-right-radius: 0.2rem;
        border-bottom-right-radius: 0.2rem;
    }

</style>
<div id="particles-js"></div>
<!-- end canvas-->
<div class="account-pages mt-5 mb-5">
    <div class="container">

        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-4">
                <div class="card bg-pattern">

                    <div class="card-body p-4">

                        <div class="text-center w-75 m-auto">
                            <div class="auth-logo">
                                <a href="<?= SITE_URL; ?>" class="logo logo-dark text-center">
                                            <span class="logo-lg">
                                                <img src="<?= Utils::fileExist('uploads/site/logo-dark.webp', BOX_EMPTY); ?>"
                                                     alt="<?= $Settings['site_name']; ?>"
                                                     height="22">
                                            </span>
                                </a>

                                <a href="<?= SITE_URL; ?>" class="logo logo-light text-center">
                                            <span class="logo-lg">
                                                <img src="<?= Utils::fileExist('uploads/site/logo-light.webp', BOX_EMPTY); ?>"
                                                     alt="<?= $Settings['site_name']; ?>"
                                                     height="22">
                                            </span>
                                </a>
                            </div>
                            <p class="text-muted mb-4 mt-3"><?= $lang['enter_email_and_pass']; ?></p>
                        </div>


                        <div class="mb-3">
                            <label for="emailaddress" class="form-label"><?= $lang['email']; ?></label>
                            <input class="form-control" type="email" id="emailaddress"
                                   autofocus
                                   autocomplete required pattern=".+@globex\.com"
                                   placeholder="<?= $lang['example_email']; ?>">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label"><?= $lang['password']; ?></label>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control"
                                       placeholder="<?= $lang['enter_password']; ?>">
                                <div class="input-group-text" data-password="false">
                                    <span class="password-eye"></span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="checkbox-signin" checked>
                                <label class="form-check-label"
                                       for="checkbox-signin"><?= $lang['remember_me']; ?></label>
                            </div>
                        </div>

                        <div class="text-center d-grid">
                            <button data-style="zoom-in" id="submit" class="btn btn-primary"
                                    type="button"> <?= $lang['login']; ?> </button>
                        </div>

                    </div> <!-- end card-body -->

                    <div class="row mt-3">
                        <div class="col-12 text-center">
                            <p class="text-dark-50">
                                <?= $lang['back_to']; ?>
                                <a href="<?= SITE_URL; ?>" class="text-dark ms-1">
                                    <b><?= $lang['home_page']; ?></b>
                                </a>
                            </p>
                        </div> <!-- end col -->
                    </div>
                </div>
                <!-- end card -->


                <!-- end row -->

            </div> <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</div>
<!-- end page -->


<footer class="footer footer-alt text-white-50">
    2015 -
    <script>document.write(new Date().getFullYear())</script> &copy; <?= $lang['design_by']; ?> <a
            href="https://ntirapp.com" class="text-white-50"><?= $lang['designer']; ?></a>
</footer>
<script>
    var var_lang = '<?php
        $var_lang = [
            'error' => $lang['error'],
            'error_mag' => $lang['error_mag'],
            'email_error' => $lang['email_error'],
            'pass_error' => $lang['pass_error'],
            'email_pass_error' => $lang['email_pass_error'],
            'admin_not_find' => $lang['admin_not_find'],
            'admin_block_time' => $lang['admin_block_time'],
            'admin_status_inactive' => $lang['admin_status_inactive'],
        ];
        print_r(json_encode($var_lang));  ?>';
</script>
<!-- Vendor js -->
<script src="<?= SITE_URL; ?>/dist/js/admin/vendor.min.js"></script>


<script src="<?= SITE_URL; ?>/dist/libs/ladda/spin.min.js"></script>
<script src="<?= SITE_URL; ?>/dist/libs/ladda/ladda.min.js"></script>
<script src="<?= SITE_URL; ?>/dist/libs/jquery-toast-plugin/jquery.toast.min.js"></script>

<script src="<?= SITE_URL; ?>/dist/js/admin/login.init.js"></script>
<script src="<?= SITE_URL; ?>/dist/libs/particles/particles.min.js"></script>
<!-- App js -->
<script src="<?= SITE_URL; ?>/dist/js/admin/app.min.js"></script>
<script>
    <?php
    if( $_COOKIE['theme'] == "dark"){
    ?>
    particlesJS("particles-js", {
        "particles": {
            "number": {"value": 80, "density": {"enable": true, "value_area": 350}},
            "color": {"value": "#fcd214"},
            "shape": {
                "type": "circle",
                "stroke": {"width": 0, "color": "#000000"},
                "polygon": {"nb_sides": 5},
                "image": {"src": "img/github.svg", "width": 100, "height": 100}
            },
            "opacity": {
                "value": 0.5,
                "random": false,
                "anim": {"enable": false, "speed": 1, "opacity_min": 0.1, "sync": false}
            },
            "size": {
                "value": 2,
                "random": true,
                "anim": {"enable": false, "speed": 40, "size_min": 0.1, "sync": false}
            },
            "line_linked": {"enable": true, "distance": 150, "color": "#fcd214", "opacity": 0.4, "width": 1},
            "move": {
                "enable": true,
                "speed": 3,
                "direction": "none",
                "random": false,
                "straight": false,
                "out_mode": "out",
                "bounce": false,
                "attract": {"enable": false, "rotateX": 600, "rotateY": 1200}
            }
        },
        "interactivity": {
            "detect_on": "canvas",
            "events": {
                "onhover": {"enable": true, "mode": "grab"},
                "onclick": {"enable": true, "mode": "bubble"},
                "resize": true
            },
            "modes": {
                "grab": {"distance": 300, "line_linked": {"opacity": 0.647500647500648}},
                "bubble": {"distance": 250, "size": 3, "duration": 2, "opacity": .8, "speed": 3},
                "repulse": {"distance": 200, "duration": 0.4},
                "push": {"particles_nb": 4},
                "remove": {"particles_nb": 2}
            }
        },
        "retina_detect": true
    });
    particlesJS("particles-js", {
        "particles": {
            "number": {"value": 80, "density": {"enable": true, "value_area": 350}},
            "color": {"value": "#fcd214"},
            "shape": {
                "type": "circle",
                "stroke": {"width": 0, "color": "#000000"},
                "polygon": {"nb_sides": 5},
                "image": {"src": "img/github.svg", "width": 100, "height": 100}
            },
            "opacity": {
                "value": 0.5,
                "random": false,
                "anim": {"enable": false, "speed": 1, "opacity_min": 0.1, "sync": false}
            },
            "size": {
                "value": 2,
                "random": true,
                "anim": {"enable": false, "speed": 40, "size_min": 0.1, "sync": false}
            },
            "line_linked": {"enable": true, "distance": 150, "color": "#fcd214", "opacity": 0.4, "width": 1},
            "move": {
                "enable": true,
                "speed": 3,
                "direction": "none",
                "random": false,
                "straight": false,
                "out_mode": "out",
                "bounce": false,
                "attract": {"enable": false, "rotateX": 600, "rotateY": 1200}
            }
        },
        "interactivity": {
            "detect_on": "canvas",
            "events": {
                "onhover": {"enable": true, "mode": "grab"},
                "onclick": {"enable": true, "mode": "bubble"},
                "resize": true
            },
            "modes": {
                "grab": {"distance": 300, "line_linked": {"opacity": 0.647500647500648}},
                "bubble": {"distance": 250, "size": 3, "duration": 2, "opacity": .8, "speed": 3},
                "repulse": {"distance": 200, "duration": 0.4},
                "push": {"particles_nb": 4},
                "remove": {"particles_nb": 2}
            }
        },
        "retina_detect": true
    });
    <?php
    }else{
    ?>
    particlesJS("particles-js", {
        "particles": {
            "number": {"value": 80, "density": {"enable": true, "value_area": 350}},
            "color": {"value": "#EEEEEE"},
            "shape": {
                "type": "circle",
                "stroke": {"width": 0, "color": "#000000"},
                "polygon": {"nb_sides": 5},
                "image": {"src": "img/github.svg", "width": 100, "height": 100}
            },
            "opacity": {
                "value": 0.5,
                "random": false,
                "anim": {"enable": false, "speed": 1, "opacity_min": 0.1, "sync": false}
            },
            "size": {
                "value": 2,
                "random": true,
                "anim": {"enable": false, "speed": 40, "size_min": 0.1, "sync": false}
            },
            "line_linked": {"enable": true, "distance": 150, "color": "#EEEEEE", "opacity": 0.4, "width": 1},
            "move": {
                "enable": true,
                "speed": 3,
                "direction": "none",
                "random": false,
                "straight": false,
                "out_mode": "out",
                "bounce": false,
                "attract": {"enable": false, "rotateX": 600, "rotateY": 1200}
            }
        },
        "interactivity": {
            "detect_on": "canvas",
            "events": {
                "onhover": {"enable": true, "mode": "grab"},
                "onclick": {"enable": true, "mode": "bubble"},
                "resize": true
            },
            "modes": {
                "grab": {"distance": 300, "line_linked": {"opacity": 0.647500647500648}},
                "bubble": {"distance": 250, "size": 3, "duration": 2, "opacity": .8, "speed": 3},
                "repulse": {"distance": 200, "duration": 0.4},
                "push": {"particles_nb": 4},
                "remove": {"particles_nb": 2}
            }
        },
        "retina_detect": true
    });
    particlesJS("particles-js", {
        "particles": {
            "number": {"value": 80, "density": {"enable": true, "value_area": 350}},
            "color": {"value": "#EEEEEE"},
            "shape": {
                "type": "circle",
                "stroke": {"width": 0, "color": "#000000"},
                "polygon": {"nb_sides": 5},
                "image": {"src": "img/github.svg", "width": 100, "height": 100}
            },
            "opacity": {
                "value": 0.5,
                "random": false,
                "anim": {"enable": false, "speed": 1, "opacity_min": 0.1, "sync": false}
            },
            "size": {
                "value": 2,
                "random": true,
                "anim": {"enable": false, "speed": 40, "size_min": 0.1, "sync": false}
            },
            "line_linked": {"enable": true, "distance": 150, "color": "#EEEEEE", "opacity": 0.4, "width": 1},
            "move": {
                "enable": true,
                "speed": 3,
                "direction": "none",
                "random": false,
                "straight": false,
                "out_mode": "out",
                "bounce": false,
                "attract": {"enable": false, "rotateX": 600, "rotateY": 1200}
            }
        },
        "interactivity": {
            "detect_on": "canvas",
            "events": {
                "onhover": {"enable": true, "mode": "grab"},
                "onclick": {"enable": true, "mode": "bubble"},
                "resize": true
            },
            "modes": {
                "grab": {"distance": 300, "line_linked": {"opacity": 0.647500647500648}},
                "bubble": {"distance": 250, "size": 3, "duration": 2, "opacity": .8, "speed": 3},
                "repulse": {"distance": 200, "duration": 0.4},
                "push": {"particles_nb": 4},
                "remove": {"particles_nb": 2}
            }
        },
        "retina_detect": true
    });
    <?php
    }
    ?>
</script>
</body>
</html>