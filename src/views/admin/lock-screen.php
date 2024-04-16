<?php
global $lang;
global $antiXSS;
global $Settings;


use MJ\Utils\Utils;

require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/Admin.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/Builder.php';

Admin::SetAdminLog("lock_screen");


setcookie('UID', null, -1, '/');
setcookie('INF', null, -1, '/');
unset($_COOKIE['UID']);
unset($_COOKIE['INF']);

$info = [];


if (isset($_COOKIE['EID']) && !empty($_COOKIE['EID'])) {
    $EID = $antiXSS->xss_clean($_COOKIE['EID']);
    $result = Admin::getAdminByEmail($EID);
    if ($result->status == 200 && !empty($result->response) && !empty($result->response[0])) {
        $info = $result->response[0];
    } else {
        header('Location: /admin/login');
    }
} else {
    header('Location: /admin/login');
}


?>

<!DOCTYPE html>
<html lang="fa" dir="<?=Utils::getThemeDirection() ;?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $lang['lock_screen'] . ' | ' . $Settings['site_name']; ?></title>
    <link rel="shortcut icon" href="<?= Utils::fileExist('uploads/site/favicon.webp', BOX_EMPTY); ?>">

    <link href="<?= SITE_URL; ?>/dist/libs/jquery-toast-plugin/jquery.toast.min.css" rel="stylesheet" type="text/css"
          id="toast"/>
    <link href="<?= SITE_URL; ?>/dist/libs/ladda/ladda.min.css" rel="stylesheet" type="text/css"
          id="ladda"/>

    <link href="<?= SITE_URL; ?>/dist/css/admin/app<?=(Utils::getThemeDirection()=='rtl')?"-rtl":null ;?>.min.css" rel="stylesheet" type="text/css"
          id="app-style"/>
    <!-- icons -->
    <link href="<?= SITE_URL; ?>/dist/css/admin/icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="<?= SITE_URL; ?>/dist/css/admin/fontiran.css" rel="stylesheet" type="text/css"/>
</head>

<body class="loading authentication-bg authentication-bg-pattern"
      data-layout-color="<?= $_COOKIE['theme']; ?>">

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
</style>
<div id="particles-js"></div>
<!-- end canvas-->

<div class="account-pages mt-5 mb-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6 col-xl-4">
                <div class="card bg-pattern">

                    <div class="card-body p-4">

                        <div class="text-center mb-4">
                            <div class="auth-logo">
                                <a href="<?= SITE_URL; ?>" class="logo logo-dark text-center">
                                            <span class="logo-lg">
                                                <img src="<?=Utils::fileExist('uploads/site/logo-dark.webp',BOX_EMPTY) ;?>"
                                                     alt="<?= $Settings['site_name']; ?>" height="22">
                                            </span>
                                </a>

                                <a href="<?= SITE_URL; ?>" class="logo logo-light text-center">
                                            <span class="logo-lg">
                                                <img src="<?=Utils::fileExist('uploads/site/logo-light.webp',BOX_EMPTY) ;?>"
                                                     alt="<?= $Settings['site_name']; ?>" height="22">
                                            </span>
                                </a>
                            </div>
                        </div>

                        <div class="text-center w-75 m-auto">
                            <img src="<?=Utils::fileExist($info->admin_avatar,USER_AVATAR) ; ?>" height="88"
                                 alt="<?= $info->admin_nickname; ?>"
                                 class="rounded-circle shadow avatar-xl">
                            <h4 class="text-dark-50 text-center mt-3"><?= $lang['hi']." ". $info->admin_nickname; ?> </h4>
                            <p class="text-muted mb-4"><?= $lang['enter_pass']; ?></p>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label"><?= $lang['password']; ?></label>
                            <input class="form-control" type="password" required="" id="password"
                                   placeholder="<?= $lang['enter_password']; ?>">
                        </div>

                        <div class="text-center d-grid">
                            <button data-style="zoom-in" class="btn btn-primary" id="submit"
                                    type="button"> <?= $lang['login']; ?> </button>
                        </div>

                    </div>
                </div>

            </div> <!-- end col -->
        </div> <!-- end row -->
    </div> <!-- end container -->
</div>

<footer class="footer footer-alt text-white-50">
    2015 -
    <script>document.write(new Date().getFullYear())</script> &copy; <?= $lang['design_by']; ?> <a
            href="https://ntirapp.com/" class="text-white-50"><?= $lang['designer']; ?></a>
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
<script src="<?= SITE_URL; ?>/dist/libs/particles/particles.min.js"></script>
<script src="<?= SITE_URL; ?>/dist/js/admin/lock-screen.init.js"></script>
<!-- App js -->
<script src="<?= SITE_URL; ?>/dist/js/admin/app.min.js"></script>
<script>
    <?php
    if( $_COOKIE['theme']=="dark"){
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