<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$filename = __DIR__ . preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

use MJ\Keys\KEYS;
use MJ\Router\Router;
use MJ\Utils\Utils;
use voku\helper\AntiXSS;

require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/core/autoload.php';
require_once __DIR__ . '/classes/autoload.php';

$antiXSS = new AntiXSS();

/**
 * SET THEME MODE COOKIE
 *
 * @author Tjavan
 */
if (!isset($_COOKIE['theme']) || !in_array($_COOKIE['theme'], KEYS::$themes)) {
    Utils::setTheme('light');
}


/**
 * SET LANGUAGE COOKIE
 *
 * @author Tjavan
 */
if (!isset($_COOKIE['language']) || file_exists('languages/' . $_COOKIE['language'] . '.php') == false) {
    setcookie('language', 'fa_IR', time() + STABLE_COOKIE_TIMEOUT, "/");
    User::changeUserLanguageOnChangeLanguage('fa_IR');
}

if (!isset($_COOKIE['time'])) {
    setcookie('time', 'ir', time() + STABLE_COOKIE_TIMEOUT, "/", null, false, true);
}

if (isset($_COOKIE['language']) && file_exists(__DIR__ . "/languages/{$_COOKIE['language']}.php")
    && file_exists(__DIR__ . "/settings/settings_{$_COOKIE['language']}.php")
    && file_exists(__DIR__ . "/settings/tour_{$_COOKIE['language']}.php")) {
    $langCookie = $_COOKIE['language'];
    include_once __DIR__ . "/languages/{$langCookie}.php";
    include_once __DIR__ . "/settings/settings_{$langCookie}.php";
    include_once __DIR__ . "/settings/tour_{$langCookie}.php";
} else {
    include_once __DIR__ . "/languages/fa_IR.php";
    include_once __DIR__ . "/settings/settings_fa_IR.php";
    include_once __DIR__ . "/settings/tour_fa_IR.php";
}

Router::set404(function () {
    header($_SERVER['SERVER_PORT'] . '404 Not found');
    include_once __DIR__ . '/views/site/404.php';
});

Router::before('GET', '/.*', function () {
    header('X-Powered-By: MJ Team');
});

/**
 * Start Site
 */
Router::all('/', function () {
    include_once __DIR__ . '/views/site/home.php';
});


Router::all('/academy', function () {
    include_once __DIR__ . '/views/site/academy/academy.php';
});
Router::all('/academycat/{id}', function ($id) {
    global $antiXSS;
    $_REQUEST['id'] = $antiXSS->xss_clean($id);
    include_once __DIR__ . '/views/site/academy/academy-cat.php';
});
Router::all('/academysubcat', function () {
    include_once __DIR__ . '/views/site/academy/academy-subcat.php';
});
Router::all('/academy/{id}', function ($id) {
    global $antiXSS;
    $_REQUEST['id'] = $antiXSS->xss_clean($id);
    include_once __DIR__ . '/views/site/academy/academy-detail.php';
});
Router::all('/academy_p/{id}', function ($id) {
    global $antiXSS;
    $_REQUEST['id'] = $antiXSS->xss_clean($id);
    include_once __DIR__ . '/views/site/academy/academy_p.php';
});

Router::all('/cargo-ads', function () {
    include_once __DIR__ . '/views/site/cargo-ads.php';
});
Router::all('/cargo-ads/{id}', function ($id) {
    global $antiXSS;
    $_REQUEST['id'] = $antiXSS->xss_clean($id);
    include_once __DIR__ . '/views/site/cargo-detail.php';
});

Router::all('/cargo-in-ads', function () {
    include_once __DIR__ . '/views/site/cargo-in-ads.php';
});
Router::all('/links', function () {
    include_once __DIR__ . '/views/site/links.php';
});
Router::all('/personnel/{id}', function ($id) {
    global $antiXSS;
    $_REQUEST['id'] = $antiXSS->xss_clean($id);
    include_once __DIR__ . '/views/site/personels.php';
});
Router::all('/cargo-in-ads/{id}', function ($id) {
    global $antiXSS;
    $_REQUEST['id'] = $antiXSS->xss_clean($id);
    include_once __DIR__ . '/views/site/cargo-in-detail.php';
});

Router::mount('/blog', function () {
    Router::all('/', function () {
        include_once __DIR__ . '/views/site/blog.php';
    });

    Router::all('/{slug}', function ($slug) {
        global $antiXSS;
        $_REQUEST['slug'] = $antiXSS->xss_clean($slug);
        include_once __DIR__ . '/views/site/single-blog.php';
    });
});

Router::all('/blog_p/{id}', function ($id) {
    global $antiXSS;
    $_REQUEST['id'] = $antiXSS->xss_clean($id);
    include_once __DIR__ . '/views/site/blog_p.php';
});

Router::all('/about', function () {
    include_once __DIR__ . '/views/site/about.php';
});
Router::all('/lang', function () {
    include_once __DIR__ . '/views/site/changelang.php';
});

Router::all('/soon', function () {
    include_once __DIR__ . '/views/site/coming-soon.php';
});
Router::all('/employment', function () {
    include_once __DIR__ . '/views/site/hire.php';
});
Router::all('/developer', function () {
    include_once __DIR__ . '/views/site/developer.php';
});
Router::all('/exchange', function () {
    include_once __DIR__ . '/views/site/exchange.php';
});
Router::all('/drph', function () {
    include_once __DIR__ . '/views/site/drprv.php';
});
//Router::all('/nsupport', function () {
//    include_once __DIR__ . '/views/user/newsupport.php';
//});

Router::all('/nnoti', function () {
    include_once __DIR__ . '/views/user/new-notifications.php';
});

/**
 * End Site
 */


/**
 * Start Admin
 */
Router::post('/api/adminAjax', function () {
    // url: '/api/adminAjax'
    include_once __DIR__ . '/api/adminAjax.php';
});

Router::mount('/api/datatable', function () {
    Router::post('/dt-admins-log', function () {
        include_once __DIR__ . '/api/datatable/dt-admins-log.php';
    });

    Router::post('/dt-admin-log(/\w+)?', function ($id = null) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/api/datatable/dt-admin-log.php';
    });

    Router::post('/dt-city', function () {
        include_once __DIR__ . '/api/datatable/dt-city.php';
    });

    Router::post('/dt-customs', function () {
        include_once __DIR__ . '/api/datatable/dt-customs.php';
    });

    Router::post('/dt-ports', function () {
        include_once __DIR__ . '/api/datatable/dt-ports.php';
    });

    Router::post('/dt-airport', function () {
        include_once __DIR__ . '/api/datatable/dt-airport.php';
    });

    Router::post('/dt-railroad', function () {
        include_once __DIR__ . '/api/datatable/dt-railroad.php';
    });

    Router::post('/dt-inventory', function () {
        include_once __DIR__ . '/api/datatable/dt-inventory.php';
    });

    Router::post('/dt-inquiry-ground', function () {
        include_once __DIR__ . '/api/datatable/dt-inquiry-ground.php';
    });

    Router::post('/dt-inquiry-ship', function () {
        include_once __DIR__ . '/api/datatable/dt-inquiry-ship.php';
    });

    Router::post('/dt-inquiry-air', function () {
        include_once __DIR__ . '/api/datatable/dt-inquiry-air.php';
    });

    Router::post('/dt-inquiry-railroad', function () {
        include_once __DIR__ . '/api/datatable/dt-inquiry-railroad.php';
    });

    Router::post('/dt-academy', function () {
        include_once __DIR__ . '/api/datatable/dt-academy.php';
    });
    Router::post('/dt-cv', function () {
        include_once __DIR__ . '/api/datatable/dt-cv.php';
    });
    Router::post('/dt-personel', function () {
        include_once __DIR__ . '/api/datatable/dt-personel.php';
    });

    Router::post('/dt-posts', function () {
        include_once __DIR__ . '/api/datatable/dt-posts.php';
    });
    Router::post('/dt-offices', function () {
        include_once __DIR__ . '/api/datatable/dt-offices.php';
    });

    Router::post('/dt-inquiry-inventory', function () {
        include_once __DIR__ . '/api/datatable/dt-inquiry-inventory.php';
    });

    Router::post('/dt-task-for-me', function () {
        include_once __DIR__ . '/api/datatable/dt-task-for-me.php';
    });

    Router::post('/dt-task-i-creator', function () {
        include_once __DIR__ . '/api/datatable/dt-task-i-creator.php';
    });

    Router::post('/dt-task-all', function () {
        include_once __DIR__ . '/api/datatable/dt-task-all.php';
    });

    Router::post('/dt-share-whatsapp', function () {
        include_once __DIR__ . '/api/datatable/dt-share-whatsapp.php';
    });

    Router::post('/dt-cargo', function () {
        include_once __DIR__ . '/api/datatable/dt-cargo.php';
    });

    Router::post('/dt-cargo-in', function () {
        include_once __DIR__ . '/api/datatable/dt-cargo-in.php';
    });

    Router::post('/dt-users-list', function () {
        include_once __DIR__ . '/api/datatable/dt-users-list.php';
    });

    Router::post('/dt-authorizations', function () {
        include_once __DIR__ . '/api/datatable/dt-authorizations.php';
    });

    Router::post('/dt-notifications', function () {
        include_once __DIR__ . '/api/datatable/dt-notifications.php';
    });

    Router::post('/dt-notification(/\w+)?', function ($id = null) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/api/datatable/dt-notification.php';
    });

    Router::post('/dt-balance', function () {
        include_once __DIR__ . '/api/datatable/dt-balance.php';
    });

    Router::post('/dt-tickets-department(/\w+)?', function ($id = null) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/api/datatable/dt-tickets-department.php';
    });

    Router::post('/dt-transactions', function () {
        include_once __DIR__ . '/api/datatable/dt-transactions.php';
    });

    Router::post('/dt-transactions-deposit', function () {
        include_once __DIR__ . '/api/datatable/dt-transactions-deposit.php';
    });

    Router::post('/dt-transactions-withdraw', function () {
        include_once __DIR__ . '/api/datatable/dt-transactions-withdraw.php';
    });

    Router::post('/dt-transactions-user(/\w+)?', function ($id = null) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/api/datatable/dt-transactions-user.php';
    });

    Router::post('/dt-credits', function () {
        include_once __DIR__ . '/api/datatable/dt-credits.php';
    });

    Router::post('/dt-posters', function () {
        include_once __DIR__ . '/api/datatable/dt-posters.php';
    });

    Router::post('/dt-poster-user(/\w+)?', function ($id = null) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/api/datatable/dt-poster-user.php';
    });

    Router::post('/dt-poster-expert', function () {
        include_once __DIR__ . '/api/datatable/dt-poster-expert.php';
    });

    Router::post('/dt-posters-reports', function () {
        include_once __DIR__ . '/api/datatable/dt-posters-reports.php';
    });

    Router::post('/dt-medias', function () {
        include_once __DIR__ . '/api/datatable/dt-medias.php';
    });

    Router::post('/dt-hire', function () {
        include_once __DIR__ . '/api/datatable/dt-hire.php';
    });

    Router::post('/dt-inquiry-customs', function () {
        include_once __DIR__ . '/api/datatable/dt-inquiry-customs.php';
    });
    Router::post('/dt-inquiry-minicargo', function () {
        include_once __DIR__ . '/api/datatable/dt-inquiry-minicargo.php';
    });

    Router::post('/dt-request-out', function () {
        include_once __DIR__ . '/api/datatable/dt-request-out.php';
    });

    Router::post('/dt-request-in', function () {
        include_once __DIR__ . '/api/datatable/dt-request-in.php';
    });
    Router::post('/dt-exchange-requests', function () {
        include_once __DIR__ . '/api/datatable/dt-exchange-requests.php';
    });

});

Router::all('/maintenance(/\w+)?(/\w+)?(/\w+)?(/\w+)?(/\w+)?(/\w+)?(/\w+)?(/\w+)?(/\w+)?', function ($param1 = null, $param2 = null, $param3 = null, $param4 = null, $param5 = null, $param6 = null, $param7 = null, $param8 = null, $param9 = null) {
    global $antiXSS;
    $_REQUEST['param1'] = $antiXSS->xss_clean($param1);
    include_once __DIR__ . '/views/admin/maintenance.php';
});

Router::mount('/admin', function () {

    if (!isset($_COOKIE['sidebar-admin']) || !in_array($_COOKIE['sidebar-admin'], KEYS::$sidebar)) {
        Utils::setSideBar('default');
    }
    Router::all('/org-user-detail', function () {
        include_once __DIR__ . '/views/admin/organization/org-user-detail.php';
    });
    Router::all('/', function () {
        include_once __DIR__ . '/views/admin/dashboard.php';
    });
    Router::all('/level', function () {
        include_once __DIR__ . '/views/admin/ground-detail-level.php';
    });
    Router::all('/exchange', function () {
        include_once __DIR__ . '/views/admin/exchange/live-price.php';
    });
    Router::all('/exchange/request-list', function () {
        include_once __DIR__ . '/views/admin/exchange/request-list.php';
    });
    Router::all('/exchange/request/{id}', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/views/admin/exchange/edit-request.php';
    });
    Router::all('/product-detail', function () {
        include_once __DIR__ . '/views/admin/shop/product-detail.php';
    });
    Router::all('/seller-detail', function () {
        include_once __DIR__ . '/views/admin/shop/seller-detail.php';
    });
    Router::all('/add-brand', function () {
        include_once __DIR__ . '/views/admin/shop/add-brand.php';
    });
    Router::all('/add-category', function () {
        include_once __DIR__ . '/views/admin/shop/add-category.php';
    });
    Router::all('/exchange-setting', function () {
        include_once __DIR__ . '/views/admin/exchange/live-price-setting.php';
    });
    Router::all('/exchange-setting-bonbast', function () {
        include_once __DIR__ . '/views/admin/exchange/bonbast-live-price-setting.php';
    });
    Router::all('/pbook', function () {
        include_once __DIR__ . '/views/admin/phonebook/list.php';
    });
    Router::all('/pbook-sms', function () {
        include_once __DIR__ . '/views/admin/phonebook/send-sms.php';
    });
    Router::all('/pbookadd', function () {
        include_once __DIR__ . '/views/admin/phonebook/add.php';
    });
    Router::all('/pbookedit/{id}', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/views/admin/phonebook/edit.php';

    });


    Router::all('/login', function () {
        include_once __DIR__ . '/views/admin/login.php';
    });


    Router::all('/lock-screen', function () {
        include_once __DIR__ . '/views/admin/lock-screen.php';
    });


    Router::all('/myaccount', function () {
        include_once __DIR__ . '/views/admin/myaccount.php';
    });


    Router::all('/help', function () {
        include_once __DIR__ . '/views/admin/help.php';
    });


    Router::mount('/admin', function () {

        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/admin/admins.php';
        });
        Router::all('/add', function () {
            include_once __DIR__ . '/views/admin/admin/admin-add.php';
        });
        Router::all('/edit/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/admin/admin-edit.php';
        });

        Router::mount('/log', function () {

            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/admin/admins-log.php';
            });

            Router::all('/{$id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/admin/admin-log.php';
            });

        });


        Router::mount('/role', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/admin/admins.php';
            });
            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/admin/admin-role-add.php';
            });
            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/admin/admin-role-edit.php';
            });
            Router::all('/delete/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/admin/admin-role-delete.php';
            });
        });

    });


    Router::mount('/census', function () {
        Router::all('/admin', function () {
            include_once __DIR__ . '/views/admin/census/census-admin.php';
        });

        Router::all('/admins', function () {
            include_once __DIR__ . '/views/admin/census/census-admins.php';
        });

        Router::all('/admins/{start}/{end}', function ($start, $end) {
            global $antiXSS;
            $_REQUEST['start'] = $antiXSS->xss_clean($start);
            $_REQUEST['end'] = $antiXSS->xss_clean($end);
            include_once __DIR__ . '/views/admin/census/census-admins-show.php';
        });

        Router::all('/transaction', function () {
            include_once __DIR__ . '/views/admin/census/census-transaction.php';
        });

        Router::all('/transaction/{start}/{end}', function ($start, $end) {
            global $antiXSS;
            $_REQUEST['start'] = $antiXSS->xss_clean($start);
            $_REQUEST['end'] = $antiXSS->xss_clean($end);
            include_once __DIR__ . '/views/admin/census/census-transaction-show.php';
        });

        Router::all('/credit', function () {
            include_once __DIR__ . '/views/admin/census/census-inquiry-credit.php';
        });

        Router::all('/general', function () {
            include_once __DIR__ . '/views/admin/census/census-general.php';
        });

        Router::all('/general/(\d+)/(\d+)', function ($start, $end) {
            global $antiXSS;
            $_REQUEST['start'] = $antiXSS->xss_clean($start);
            $_REQUEST['end'] = $antiXSS->xss_clean($end);
            include_once __DIR__ . '/views/admin/census/census-general-show.php';
        });

    });


    Router::mount('/department', function () {

        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/ticket/departments.php';
        });
        Router::all('/add', function () {
            include_once __DIR__ . '/views/admin/ticket/department-add.php';
        });

        Router::all('/edit/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/ticket/department-edit.php';
        });

    });

    Router::mount('/driver-services', function () {

        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/cv/cv.php';
        });
        Router::all('/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/cv/driver-cv.php';
        });

    });
    Router::mount('/personel', function () {

        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/cv/personel-list.php';
        });
        Router::all('/edit/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/cv/personel-detail.php';
        });
        Router::all('/add', function () {

            include_once __DIR__ . '/views/admin/cv/personel-add.php';
        });

    });


    Router::mount('/ticket', function () {


        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/ticket/tickets.php';
        });
        Router::all('/add/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/ticket/ticket-add.php';
        });
        Router::mount('/open', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/ticket/tickets-open.php';
            });
            Router::all('/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/ticket/ticket-single.php';
            });
        });
        Router::all('/user/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/ticket/ticket-user-list.php';
        });


        Router::all('/d/all/{deId}', function ($deId) {
            global $antiXSS;
            $_REQUEST['deId'] = $antiXSS->xss_clean($deId);
            include_once __DIR__ . '/views/admin/ticket/tickets-department.php';
        });

        Router::all('/d/open/{deId}', function ($deId) {
            global $antiXSS;
            $_REQUEST['deId'] = $antiXSS->xss_clean($deId);
            include_once __DIR__ . '/views/admin/ticket/tickets-open-department.php';
        });

    });


    Router::mount('/users', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/users/users-list.php';
        });
        Router::all('/currency/edit/(\d+)', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/currency/user-currency-edit.php';
        });
        Router::all('/info/(\d+)', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/users/user-info.php';
        });
        Router::all('/log/(\d+)', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/users/user-log.php';
        });
        Router::all('/notification/(\d+)', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/notification/notification-user.php';
        });
        Router::all('/transaction/(\d+)', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/transaction/user-transaction.php';
        });
        Router::all('/request/(\d+)', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/ground/user-requests.php';
        });
        Router::all('/request-in/(\d+)', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/ground/user-requests-in.php';
        });
        Router::all('/cargo/(\d+)', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/ground/user-cargoes.php';
        });
        Router::all('/cargo-in/(\d+)', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/ground/user-cargoes-in.php';
        });
        Router::all('/car/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/ground/user-cars.php';
        });
    });


    Router::all('/balance', function () {
        include_once __DIR__ . '/views/admin/currency/balances.php';
    });


    Router::mount('/settings', function () {

        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/settings/settings-general.php';
        });
        Router::all('/site', function () {
            include_once __DIR__ . '/views/admin/settings/settings_site.php';
        });
        Router::all('/languages', function () {
            include_once __DIR__ . '/views/admin/settings/settings_languages.php';
        });
        Router::all('/payment', function () {
            include_once __DIR__ . '/views/admin/settings/settings-payment.php';
        });
        Router::all('/sms', function () {
            include_once __DIR__ . '/views/admin/settings/settings-sms.php';
        });
        Router::all('/field', function () {
            include_once __DIR__ . '/views/admin/settings/settings-field.php';
        });
        Router::all('/seo', function () {
            include_once __DIR__ . '/views/admin/settings/settings-seo.php';
        });
        Router::all('/security', function () {
            include_once __DIR__ . '/views/admin/settings/settings-security.php';
        });
        Router::all('/export', function () {
            include_once __DIR__ . '/views/admin/settings/settings-export.php';
        });
        Router::all('/poster', function () {
            include_once __DIR__ . '/views/admin/settings/settings-poster.php';
        });
    });


    Router::mount('/category', function () {

        Router::mount('/post', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/post/category-post.php';
            });
            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/post/category-post-add.php';
            });
            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/post/category-post-edit.php';
            });
            Router::all('/delete/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/post/category-post-delete.php';
            });
        });


        Router::mount('/academy', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/academy/category-academy.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/academy/category-academy-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/academy/category-academy-edit.php';
            });

            Router::all('/delete/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/academy/category-academy-delete.php';
            });
        });


        Router::mount('/customs', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/ground/customs.php';
            });
            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/ground/customs-add.php';
            });

            Router::all('/edit/{$id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/ground/customs-edit.php';
            });

//            Router::all('/delete/{$id}', function ($id) {
//                global $antiXSS;
//                $_REQUEST['id'] = $antiXSS->xss_clean($id);
//                include_once __DIR__ . '/views/admin/ground/customs-delete.php';
//            });

        });

        Router::mount('/car', function () {

            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/ground/category-car.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/ground/category-car-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/ground/category-car-edit.php';
            });

        });

        Router::mount('/cargo', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/ground/category-cargo.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/ground/category-cargo-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/ground/category-cargo-edit.php';
            });
        });


        Router::mount('/port', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/ship/category-port.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/ship/category-port-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/ship/category-port-edit.php';
            });

            Router::all('/delete/{$id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/ship/category-port-delete.php';
            });
        });

        Router::mount('/container', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/ship/category-container.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/ship/category-container-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/ship/category-container-edit.php';
            });
        });

        Router::mount('/ship-cargo', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/ship/category-ship-cargo.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/ship/category-ship-cargo-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/ship/category-ship-cargo-edit.php';
            });
        });

        Router::mount('/ship-packing', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/ship/category-ship-packing.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/ship/category-ship-packing-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/ship/category-ship-packing-edit.php';
            });
        });


        Router::mount('/airport', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/air/category-airport.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/air/category-airport-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/air/category-airport-edit.php';
            });

            Router::all('/delete/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/air/category-airport-delete.php';
            });
        });

        Router::mount('/air-cargo', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/air/category-air-cargo.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/air/category-air-cargo-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/air/category-air-cargo-edit.php';
            });
        });

        Router::mount('/air-packing', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/air/category-air-packing.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/air/category-air-packing-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/air/category-air-packing-edit.php';
            });
        });


        Router::mount('/railroad', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/railroad/category-railroad.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/railroad/category-railroad-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/railroad/category-railroad-edit.php';
            });

            Router::all('/delete/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/railroad/category-railroad-delete.php';
            });
        });


        Router::mount('/wagon', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/railroad/category-wagon.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/railroad/category-wagon-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/railroad/category-wagon-edit.php';
            });
        });
        Router::mount('/visa-location', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/cv/visa-location-list.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/cv/visa-location-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/cv/visa-location-edit.php';
            });
        });

        Router::mount('/railroad-cargo', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/railroad/category-railroad-cargo.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/railroad/category-railroad-cargo-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/railroad/category-railroad-cargo-edit.php';
            });
        });

        Router::mount('/railroad-packing', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/railroad/category-railroad-packing.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/railroad/category-railroad-packing-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/railroad/category-railroad-packing-edit.php';
            });
        });

        Router::mount('/container-railroad', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/railroad/category-container-railroad.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/railroad/category-container-railroad-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/railroad/category-container-railroad-edit.php';
            });
        });


        Router::mount('/inventory', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/inventory/category-inventory.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/inventory/category-inventory-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/inventory/category-inventory-edit.php';
            });

            Router::all('/delete/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/inventory/category-inventory-delete.php';
            });
        });

        Router::mount('/inventory-cargo', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/inventory/category-inventory-cargo.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/inventory/category-inventory-cargo-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/inventory/category-inventory-cargo-edit.php';
            });
        });

        Router::mount('/inventory-type', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/inventory/category-inventory-type.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/inventory/category-inventory-type-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/inventory/category-inventory-type-edit.php';
            });
        });


        Router::mount('/brand', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/poster-category/brands.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/poster-category/brand-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/poster-category/brand-edit.php';
            });
        });

        Router::mount('/model', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/poster-category/models.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/poster-category/model-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/poster-category/model-edit.php';
            });
        });

        Router::mount('/gearbox', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/poster-category/gearboxs.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/poster-category/gearbox-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/poster-category/gearbox-edit.php';
            });
        });

        Router::mount('/fuel', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/poster-category/fuels.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/poster-category/fuel-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/poster-category/fuel-edit.php';
            });
        });

        Router::mount('/property', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/poster-category/properties.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/poster-category/property-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/poster-category/property-edit.php';
            });
        });

        Router::mount('/report', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/poster-category/reports.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/poster-category/report-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/poster-category/report-edit.php';
            });
        });

        Router::mount('/poster-delete', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/poster-category/poster-delete.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/poster-category/poster-delete-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/poster-category/poster-delete-edit.php';
            });
        });

        Router::mount('/transportation', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/customs/category-transportation.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/customs/category-transportation-add.php';
            });

            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/customs/category-transportation-edit.php';
            });
        });
    });


    Router::mount('/currency', function () {

        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/currency/currency.php';
        });

        Router::all('/edit/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/currency/currency-edit.php';
        });

    });


    Router::all('/medias', function () {
        include_once __DIR__ . '/views/admin/medias.php';
    });


    Router::all('/media', function () {
        include_once __DIR__ . '/views/admin/media.php';;
    });


    Router::mount('/ngroup', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/ngroup/ngroups.php';
        });
        Router::all('/add', function () {
            include_once __DIR__ . '/views/admin/ngroup/ngroup-add.php';
        });
        Router::all('/edit/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/ngroup/ngroup-edit.php';
        });
    });


    Router::mount('/post', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/post/posts.php';
        });

        Router::all('/add', function () {
            include_once __DIR__ . '/views/admin/post/post-add.php';
        });

        Router::all('/edit/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/post/post-edit.php';
        });

        Router::all('/delete/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/post/post-delete.php';
        });

    });

    Router::mount('/office', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/office/office-list.php';
        });

        Router::all('/add', function () {
            include_once __DIR__ . '/views/admin/office/office-add.php';
        });

        Router::all('/edit/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/office/office-edit.php';
        });

    });


    Router::mount('/country', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/location/country.php';
        });
        Router::all('/add', function () {
            include_once __DIR__ . '/views/admin/location/country-add.php';
        });

        Router::all('/edit/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/location/country-edit.php';
        });
    });


    Router::mount('/city', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/location/city.php';
        });
        Router::all('/add', function () {
            include_once __DIR__ . '/views/admin/location/city-add.php';
        });

        Router::all('/edit/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/location/city-edit.php';
        });

//        Router::all('/delete/{$id}', function ($id) {
//            global $antiXSS;
//            $_REQUEST['id'] = $antiXSS->xss_clean($id);
//            include_once __DIR__ . '/views/admin/location/city-delete.php';
//        });
    });


    Router::mount('/car', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/ground/car.php';
        });

        Router::all('/pending', function () {
            include_once __DIR__ . '/views/admin/ground/car-pending.php';
        });
        Router::all('/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/ground/car-edit.php';
        });
    });


    Router::mount('/cargo', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/ground/cargo.php';
        });

        Router::all('/pending', function () {
            include_once __DIR__ . '/views/admin/ground/cargo-pending.php';
        });
        Router::all('/progress', function () {
            include_once __DIR__ . '/views/admin/ground/cargo-progress.php';
        });
        Router::all('/accepted', function () {
            include_once __DIR__ . '/views/admin/ground/cargo-accepted.php';
        });
        Router::all('/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/ground/cargo-info.php';
        });
    });


    Router::mount('/cargo-in', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/ground/cargo-in.php';
        });
        Router::all('/pending', function () {
            include_once __DIR__ . '/views/admin/ground/cargo-in-pending.php';
        });
        Router::all('/progress', function () {
            include_once __DIR__ . '/views/admin/ground/cargo-in-progress.php';
        });
        Router::all('/accepted', function () {
            include_once __DIR__ . '/views/admin/ground/cargo-in-accepted.php';
        });
        Router::all('/(\d+)', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/ground/cargo-in-info.php';
        });
    });


    Router::all('/request', function () {
        include_once __DIR__ . '/views/admin/ground/requests.php';
    });

    Router::all('/request-out', function () {
        include_once __DIR__ . '/views/admin/ground/requests-out.php';
    });

    Router::all('/request-in', function () {
        include_once __DIR__ . '/views/admin/ground/requests-in.php';
    });

    Router::mount('/notification', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/notification/notifications.php';
        });

        Router::all('/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/notification/notification.php';
        });
    });


    Router::mount('/credit', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/credit/credits.php';
        });

        Router::all('/pending', function () {
            include_once __DIR__ . '/views/admin/credit/credit-pending.php';
        });

        Router::all('/status', function () {
            include_once __DIR__ . '/views/admin/credit/credit-inquiry-status.php';
        });

        Router::all('/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/credit/credit.php';
        });
    });


    Router::mount('/transaction', function () {

        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/transaction/transactions.php';
        });

        Router::mount('/withdraw', function () {

            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/transaction/transactions-withdraw.php';
            });

            Router::all('/pending', function () {
                include_once __DIR__ . '/views/admin/transaction/transactions-withdraw-pending.php';
            });

        });


        Router::mount('/deposit', function () {

            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/transaction/transactions-deposit.php';
            });

            Router::all('/pending', function () {
                include_once __DIR__ . '/views/admin/transaction/transactions-deposit-pending.php';
            });

        });


        Router::all('/info/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/transaction/transactions-info.php';
        });
    });


    Router::mount('/complaint', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/complaint/complaints.php';
        });

        Router::all('/pending', function () {
            include_once __DIR__ . '/views/admin/complaint/complaints-pending.php';
        });

        Router::all('/accepted', function () {
            include_once __DIR__ . '/views/admin/complaint/complaints-accepted.php';
        });

        Router::all('/in/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/complaint/complaint-in-info.php';
        });

        Router::all('/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/complaint/complaint-info.php';
        });
    });


    Router::mount('/inquiry', function () {
        Router::mount('/ground', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/ground/inquiry-ground-list.php';
            });

            Router::all('/pending', function () {
                include_once __DIR__ . '/views/admin/ground/inquiry-ground-pending.php';
            });

            Router::all('/process', function () {
                include_once __DIR__ . '/views/admin/ground/inquiry-ground-process.php';
            });

            Router::all('/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/ground/inquiry-ground-info.php';
            });
        });


        Router::mount('/ship', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/ship/inquiry-ship-list.php';
            });

            Router::all('/pending', function () {
                include_once __DIR__ . '/views/admin/ship/inquiry-ship-pending.php';
            });

            Router::all('/process', function () {
                include_once __DIR__ . '/views/admin/ship/inquiry-ship-process.php';
            });

            Router::all('/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/ship/inquiry-ship-info.php';
            });
        });


        Router::mount('/air', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/air/inquiry-air-list.php';
            });

            Router::all('/pending', function () {
                include_once __DIR__ . '/views/admin/air/inquiry-air-pending.php';
            });

            Router::all('/process', function () {
                include_once __DIR__ . '/views/admin/air/inquiry-air-process.php';
            });

            Router::all('/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/air/inquiry-air-info.php';
            });
        });


        Router::mount('/railroad', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/railroad/inquiry-railroad-list.php';
            });

            Router::all('/pending', function () {
                include_once __DIR__ . '/views/admin/railroad/inquiry-railroad-pending.php';
            });

            Router::all('/process', function () {
                include_once __DIR__ . '/views/admin/railroad/inquiry-railroad-process.php';
            });

            Router::all('/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/railroad/inquiry-railroad-info.php';
            });
        });


        Router::mount('/inventory', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/inventory/inquiry-inventory-list.php';
            });

            Router::all('/pending', function () {
                include_once __DIR__ . '/views/admin/inventory/inquiry-inventory-pending.php';
            });

            Router::all('/process', function () {
                include_once __DIR__ . '/views/admin/inventory/inquiry-inventory-process.php';
            });

            Router::all('/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/inventory/inquiry-inventory-info.php';
            });
        });


        Router::mount('/customs', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/customs/inquiry-customs-list.php';
            });

            Router::all('/pending', function () {
                include_once __DIR__ . '/views/admin/customs/inquiry-customs-pending.php';
            });

            Router::all('/process', function () {
                include_once __DIR__ . '/views/admin/customs/inquiry-customs-process.php';
            });

            Router::all('/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/customs/inquiry-customs-info.php';
            });
        });
        Router::mount('/minicargo', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/minicargo/inquiry-minicargo-list.php';
            });

            Router::all('/pending', function () {
                include_once __DIR__ . '/views/admin/minicargo/inquiry-minicargo-pending.php';
            });

            Router::all('/process', function () {
                include_once __DIR__ . '/views/admin/minicargo/inquiry-minicargo-process.php';
            });

            Router::all('/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/minicargo/inquiry-minicargo-info.php';
            });
        });

    });


    Router::mount('/academy', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/academy/academy.php';
        });

        Router::all('/add', function () {
            include_once __DIR__ . '/views/admin/academy/academy-add.php';
        });

        Router::all('/edit/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/academy/academy-edit.php';
        });

        Router::all('/delete/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/academy/academy-delete.php';
        });

    });


    Router::mount('/tasks', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/tasks/tasks.php';
        });

        Router::all('/add', function () {
            include_once __DIR__ . '/views/admin/tasks/task-add.php';
        });

        Router::all('/info/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/tasks/task-info.php';
        });

        Router::all('/me', function () {
            include_once __DIR__ . '/views/admin/tasks/task-list-for-me.php';
        });

        Router::all('/creator', function () {
            include_once __DIR__ . '/views/admin/tasks/task-list-i-creator.php';
        });

        Router::all('/all', function () {
            include_once __DIR__ . '/views/admin/tasks/tasks-all.php';
        });

        Router::all('/show/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/tasks/task-show.php';
        });
    });


    Router::mount('/share', function () {
        Router::mount('/whatsapp', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/share/whatsapp-list.php';
            });

            Router::all('/default', function () {
                include_once __DIR__ . '/views/admin/share/whatsapp-default-text.php';
            });

            Router::all('/{$id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/share/whatsapp-info.php';
            });
        });

        Router::all('/user/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/share/whatsapp-user.php';
        });
    });


    Router::mount('/authorization', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/users/authorization-list.php';
        });
    });


    Router::mount('/poster', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/poster/posters.php';
        });

        Router::all('/pending', function () {
            include_once __DIR__ . '/views/admin/poster/poster-pending.php';
        });

        Router::all('/reports', function () {
            include_once __DIR__ . '/views/admin/poster/reports.php';
        });

        Router::all('/user/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/poster/poster-user.php';
        });

        Router::all('/info/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/poster/poster-info.php';
        });
    });


    Router::mount('/expert', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/expert/experts.php';
        });

        Router::all('/add', function () {
            include_once __DIR__ . '/views/admin/expert/expert-add.php';
        });

        Router::all('/edit/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/expert/expert-edit.php';
        });
    });


    Router::mount('/poster-expert', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/poster/poster-expert.php';
        });

        Router::all('/info/{$id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/poster/poster-expert-info.php';
        });
    });


    Router::mount('/hire', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/admin/hire/hire.php';
        });

        Router::all('/info/(\d+)', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = $antiXSS->xss_clean($id);
            include_once __DIR__ . '/views/admin/hire/info.php';
        });

        Router::mount('/category', function () {
            Router::all('/', function () {
                include_once __DIR__ . '/views/admin/hire/category.php';
            });

            Router::all('/add', function () {
                include_once __DIR__ . '/views/admin/hire/category-add.php';
            });


            Router::all('/edit/{id}', function ($id) {
                global $antiXSS;
                $_REQUEST['id'] = $antiXSS->xss_clean($id);
                include_once __DIR__ . '/views/admin/hire/category-edit.php';
            });
        });

    });

//    Router::all('/users/{id}/{test}', function ($id, $test) {
//        global $antiXss;
//        $_REQUEST['id'] = $antiXss->xss_clean($id);
//        $_REQUEST['test'] = $antiXss->xss_clean($test);
//        include_once __DIR__ . '/views/admin/users.php';
//    });
});
/**
 * End Admin
 */


/** start driver */
Router::mount('/driver', function () {

    Router::all('/', function () {
        include_once __DIR__ . '/views/driver/dashboard.php';
    });

    Router::all('/cargo/{id}', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/views/site/cargo-detail.php';
    });

    Router::all('/cargo-in/{id}', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/views/site/cargo-in-detail.php';
    });

    Router::mount('/my-requests', function () {
        Router::all('/{status}', function ($status) {
            global $antiXSS;
            $_REQUEST['status'] = $antiXSS->xss_clean($status);
            include_once __DIR__ . '/views/driver/my-requests.php';
        });

        Router::all('/', function () {
            include_once __DIR__ . '/views/driver/my-requests.php';
        });
    });

    Router::all('/my-requests-in(/\d+)?', function ($status = null) {
        global $antiXSS;
        $_REQUEST['status'] = $antiXSS->xss_clean($status);
        include_once __DIR__ . '/views/driver/my-requests-in.php';
    });

    Router::all('/my-cars', function () {
        include_once __DIR__ . '/views/driver/my-cars.php';
    });

    Router::all('/car-detail/{$id}', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/views/driver/car-detail.php';
    });

    Router::all('/send-request/{id}', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/views/driver/send-request.php';
    });

    Router::all('/send-request-in/(\d+)', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/views/driver/send-request-in.php';
    });

    Router::all('/start-transportation/{id}', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/views/driver/start-transportation.php';
    });

    Router::all('/start-transportation-in(/\d+)', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/views/driver/start-transportation-in.php';
    });

    Router::all('/end-transportation/{id}', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/views/driver/end-transportation.php';
    });

    Router::all('/end-transportation-in/{id}', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/views/driver/end-transportation-in.php';
    });

    Router::all('/auth', function () {
        include_once __DIR__ . '/views/driver/authorize.php';
    });

});
/** end driver*/

/** start businessman */
Router::mount('/businessman', function () {

    Router::all('/', function () {
        include_once __DIR__ . '/views/businessman/dashboard.php';
    });

    Router::all('/add-cargo', function () {
        include_once __DIR__ . '/views/businessman/add-cargo.php';
    });

    Router::all('/add-cargo-in', function () {
        include_once __DIR__ . '/views/businessman/add-cargo-in.php';
    });

    Router::all('/edit-cargo/(\d+)', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = intval($antiXSS->xss_clean($id));
        include_once __DIR__ . '/views/businessman/edit-cargo.php';
    });

    Router::all('/edit-cargo-in/(\d+)', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = intval($antiXSS->xss_clean($id));
        include_once __DIR__ . '/views/businessman/edit-cargo-in.php';
    });

    Router::all('/my-cargoes(/\w+)?(/\w+)?', function ($status = null, $type = null) {
        global $antiXSS;
        $_REQUEST['status'] = $antiXSS->xss_clean($status);
        $_REQUEST['type'] = $antiXSS->xss_clean($type);
        include_once __DIR__ . '/views/businessman/my-cargoes.php';
    });

    Router::all('/cargo-detail/{id}', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = intval($antiXSS->xss_clean($id));
        include_once __DIR__ . '/views/businessman/cargo-detail.php';
    });

    Router::all('/cargo-in-detail/{id}', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = intval($antiXSS->xss_clean($id));
        include_once __DIR__ . '/views/businessman/cargo-in-detail.php';
    });

    Router::mount('/suggestions', function () {
        Router::all('/{cargo}/{sort}', function ($cargo, $sort) {
            global $antiXSS;
            $_REQUEST['id'] = intval($antiXSS->xss_clean($cargo));
            $_REQUEST['sort'] = $antiXSS->xss_clean($sort);
            include_once __DIR__ . '/views/businessman/suggestions.php';
        });


        Router::all('/{cargo}', function ($cargo) {
            global $antiXSS;
            $_REQUEST['id'] = intval($antiXSS->xss_clean($cargo));
            include_once __DIR__ . '/views/businessman/suggestions.php';
        });
    });

    Router::all('/suggestions-in(/\w+)?(/\w+)?', function ($cargo = null, $sort = null) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($cargo);
        $_REQUEST['sort'] = $antiXSS->xss_clean($sort);
        include_once __DIR__ . '/views/businessman/suggestions-in.php';
    });

    Router::all('/auth', function () {
        include_once __DIR__ . '/views/businessman/authorize.php';
    });

});
/** end businessman*/

/** start user */
Router::mount('/user', function () {

    Router::mount('/wallet', function () {

        Router::all('/', function () {
            include_once __DIR__ . '/views/user/wallet/wallet.php';
        });

        Router::all('/deposit/{currencyId}', function ($currencyId) {
            global $antiXSS;
            $_REQUEST['currencyId'] = intval($antiXSS->xss_clean($currencyId));
            include_once __DIR__ . '/views/user/wallet/deposit.php';
        });

        Router::all('/withdraw/{currencyId}', function ($currencyId) {
            global $antiXSS;
            $_REQUEST['currencyId'] = intval($antiXSS->xss_clean($currencyId));
            include_once __DIR__ . '/views/user/wallet/withdraw.php';
        });

        Router::all('/trxlist/{currencyId}(/\w+)?', function ($currencyId, $status = null) {
            global $antiXSS;
            $_REQUEST['currencyId'] = intval($antiXSS->xss_clean($currencyId));
            $_REQUEST['status'] = $antiXSS->xss_clean($status);
            include_once __DIR__ . '/views/user/wallet/trxlist.php';
        });

        Router::all('/accounts/{currencyId}', function ($currencyId) {
            global $antiXSS;
            $_REQUEST['currencyId'] = intval($antiXSS->xss_clean($currencyId));
            include_once __DIR__ . '/views/user/wallet/accounts.php';
        });

    });

    Router::mount('/credit-cards', function () {
        Router::all('/{status}', function ($status) {
            global $antiXSS;
            $_REQUEST['status'] = $antiXSS->xss_clean($status);
            include_once __DIR__ . '/views/user/credit-list.php';
        });


        Router::all('/', function () {
            include_once __DIR__ . '/views/user/credit-list.php';
        });
    });

    Router::all('/notifications', function () {
        include_once __DIR__ . '/views/user/new-notifications.php';
    });

    Router::mount('/notification', function () {
        Router::all('/group/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = intval($antiXSS->xss_clean($id));
            include_once __DIR__ . '/views/user/notification.php';
        });

        Router::all('/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = intval($antiXSS->xss_clean($id));
            include_once __DIR__ . '/views/user/notification.php';
        });
    });

    Router::all('/auth', function () {
        include_once __DIR__ . '/views/user/authorize.php';
    });

    Router::all('/profile', function () {
        include_once __DIR__ . '/views/user/profile.php';
    });

    Router::all('/laws', function () {
        include_once __DIR__ . '/views/user/laws.php';
    });

    Router::all('/faq', function () {
        include_once __DIR__ . '/views/user/faq.php';
    });

    Router::all('/callback', function () {
        include_once __DIR__ . '/views/user/callback.php';
    });


    Router::all('/about', function () {
        include_once __DIR__ . '/views/site/about.php';
    });


    Router::all('/contact-us', function () {
        include_once __DIR__ . '/views/user/contact-us.php';
    });

    Router::mount('/air', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/user/air/inquiry-air-dashboard.php';
        });
        Router::all('/inquiry', function () {
            include_once __DIR__ . '/views/user/air/inquiry-air.php';
        });
        Router::all('/inquiry-list', function () {
            include_once __DIR__ . '/views/user/air/inquiry-air-list.php';
        });
        Router::all('/inquiry-list/{status}', function ($status) {
            global $antiXSS;
            $_REQUEST['status'] = $antiXSS->xss_clean($status);
            include_once __DIR__ . '/views/user/air/inquiry-air-list.php';
        });
        Router::all('/inquiry-detail/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = intval($antiXSS->xss_clean($id));
            include_once __DIR__ . '/views/user/air/inquiry-air-detail.php';
        });
    });


    Router::mount('/ship', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/user/ship/inquiry-ship-dashboard.php';
        });
        Router::all('/inquiry', function () {
            include_once __DIR__ . '/views/user/ship/inquiry-ship.php';
        });
        Router::all('/inquiry-list', function () {
            include_once __DIR__ . '/views/user/ship/inquiry-ship-list.php';
        });
        Router::all('/inquiry-list/{status}', function ($status) {
            global $antiXSS;
            $_REQUEST['status'] = $antiXSS->xss_clean($status);
            include_once __DIR__ . '/views/user/ship/inquiry-ship-list.php';
        });
        Router::all('/inquiry-detail/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = intval($antiXSS->xss_clean($id));
            include_once __DIR__ . '/views/user/ship/inquiry-ship-detail.php';
        });
    });

    Router::mount('/railroad', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/user/railroad/inquiry-railroad-dashboard.php';
        });
        Router::all('/inquiry', function () {
            include_once __DIR__ . '/views/user/railroad/inquiry-rail.php';
        });
        Router::all('/inquiry-list', function () {
            include_once __DIR__ . '/views/user/railroad/inquiry-railroad-list.php';
        });
        Router::all('/inquiry-list/{status}', function ($status) {
            global $antiXSS;
            $_REQUEST['status'] = $antiXSS->xss_clean($status);
            include_once __DIR__ . '/views/user/railroad/inquiry-railroad-list.php';
        });
        Router::all('/inquiry-detail/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = intval($antiXSS->xss_clean($id));
            include_once __DIR__ . '/views/user/railroad/inquiry-railroad-detail.php';
        });

    });

    Router::mount('/inventory', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/user/inventory/inquiry-inventory-dashboard.php';
        });
        Router::all('/inquiry', function () {
            include_once __DIR__ . '/views/user/inventory/inquiry-inventory.php';
        });
        Router::all('/inquiry-list', function () {
            include_once __DIR__ . '/views/user/inventory/inquiry-inventory-list.php';
        });
        Router::all('/inquiry-list/{status}', function ($status) {
            global $antiXSS;
            $_REQUEST['status'] = $antiXSS->xss_clean($status);
            include_once __DIR__ . '/views/user/inventory/inquiry-inventory-list.php';
        });
        Router::all('/inquiry-detail/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = intval($antiXSS->xss_clean($id));
            include_once __DIR__ . '/views/user/inventory/inquiry-inventory-detail.php';
        });

    });

    Router::mount('/customs', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/user/customs/customs-dashboard.php';
        });
        Router::all('/inquiry', function () {
            include_once __DIR__ . '/views/user/customs/inquiry-customs.php';
        });
        Router::all('/inquiry-list', function () {
            include_once __DIR__ . '/views/user/customs/inquiry-customs-list.php';
        });
        Router::all('/inquiry-list/{status}', function ($status) {
            global $antiXSS;
            $_REQUEST['status'] = $antiXSS->xss_clean($status);
            include_once __DIR__ . '/views/user/customs/inquiry-customs-list.php';
        });
        Router::all('/inquiry-detail/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = intval($antiXSS->xss_clean($id));
            include_once __DIR__ . '/views/user/customs/inquiry-customs-detail.php';
        });

    });
    Router::mount('/minicargo', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/user/minicargo/minicargo-dashboard.php';
        });
        Router::all('/inquiry', function () {
            include_once __DIR__ . '/views/user/minicargo/inquiry-minicargo.php';
        });
        Router::all('/inquiry-list', function () {
            include_once __DIR__ . '/views/user/minicargo/inquiry-minicargo-list.php';
        });
        Router::all('/inquiry-list/{status}', function ($status) {
            global $antiXSS;
            $_REQUEST['status'] = $antiXSS->xss_clean($status);
            include_once __DIR__ . '/views/user/minicargo/inquiry-minicargo-list.php';
        });
        Router::all('/inquiry-detail/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = intval($antiXSS->xss_clean($id));
            include_once __DIR__ . '/views/user/minicargo/inquiry-minicargo-detail.php';
        });

    });
    Router::mount('/drivers', function () {
        Router::all('/', function () {
            include_once __DIR__ . '/views/user/cv/driver-list.php';
        });
        Router::all('/detail/{id}', function ($id) {
            global $antiXSS;
            $_REQUEST['id'] = intval($antiXSS->xss_clean($id));
            include_once __DIR__ . '/views/user/cv/driver-detail.php';
        });
        Router::all('/add', function () {
            include_once __DIR__ . '/views/user/cv/driver-add.php';
        });

        Router::all('/edit', function () {
            include_once __DIR__ . '/views/user/cv/driver-edit.php';
        });


    });

    Router::all('/support', function () {
        include_once __DIR__ . '/views/user/newsupport.php';
    });

    Router::all('/ticket-list', function () {
        include_once __DIR__ . '/views/user/ticket-list.php';
    });

    Router::all('/ticket/{id}', function ($id) {
        global $antiXSS;
        $_REQUEST['id'] = $antiXSS->xss_clean($id);
        include_once __DIR__ . '/views/user/ticket.php';
    });
    Router::all('/invite', function () {
        include_once __DIR__ . '/views/user/invite.php';
    });

});
/** end user */

/** start poster */
Router::mount('/poster', function () {

    Router::all('/', function () {
        include_once __DIR__ . '/views/poster/poster.php';
    });

    Router::all('/detail/{posterId}', function ($posterId) {
        global $antiXSS;
        $_REQUEST['posterId'] = intval($antiXSS->xss_clean($posterId));
        include_once __DIR__ . '/views/poster/detail.php';
    });

    Router::all('/dashboard', function () {
        include_once __DIR__ . '/views/poster/dashboard.php';
    });

    Router::all('/my-list(/\w+)?', function ($status = null) {
        global $antiXSS;
        $_REQUEST['status'] = $antiXSS->xss_clean($status);
        include_once __DIR__ . '/views/poster/my-list.php';
    });

    Router::all('/add', function () {
        include_once __DIR__ . '/views/poster/add.php';
    });

    Router::all('/edit/{posterId}', function ($posterId) {
        global $antiXSS;
        $_REQUEST['posterId'] = intval($antiXSS->xss_clean($posterId));
        include_once __DIR__ . '/views/poster/edit.php';
    });

});
/** end poster */


Router::post('/api/ajax', function () {
    include_once __DIR__ . '/api/ajax.php';
});

Router::all('/purchases/{ref_id}/callback', function ($ref_id) {
    global $antiXSS;
    $_REQUEST['ref_id'] = intval($antiXSS->xss_clean($ref_id));
    include_once __DIR__ . '/views/site/callback.php';
});
Router::all('/start-payment/{amount}', function ($amount) {
    global $antiXSS;
    $_REQUEST['amount'] = intval($antiXSS->xss_clean($amount));
    include_once __DIR__ . '/views/site/start-payment.php';
});

Router::all('/test', function () {
    include_once __DIR__ . '/test.php';
});

Router::all('/m', function () {
    include_once __DIR__ . '/m.php';
});

Router::all('/bonbast', function () {
    include_once __DIR__ . '/cronjob/get-bonbast-price.php';
});

Router::all('/login', function () {
    include_once __DIR__ . '/views/site/login.php';
});


Router::all('/login/{referals}', function ($referals) {
    global $antiXSS;
    $_REQUEST['referals'] = ($antiXSS->xss_clean($referals));
    include_once __DIR__ . '/views/site/login.php';
});

Router::run();


