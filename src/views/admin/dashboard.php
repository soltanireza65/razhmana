<?php
global $lang;

use MJ\Utils\Utils;

include_once 'header-footer.php';

$resultCheckAdminLogin = Admin::checkAdminLogin();

$dataCheckAdminLogin = [];
if ($resultCheckAdminLogin->status == 200) {
    $dataCheckAdminLogin = $resultCheckAdminLogin->response;

    if ($dataCheckAdminLogin->admin_status == "active") {

        $dataCheckAdminRoleForCheck = [];
        if (!empty($dataCheckAdminLogin->role_id)) {
            $resultCheckAdminRoleForCheck = Admin::checkAdminRoleForCheck($dataCheckAdminLogin->role_id);
            if ($resultCheckAdminRoleForCheck->status == 200) {
                $dataCheckAdminRoleForCheck = $resultCheckAdminRoleForCheck->response;
            }
        }


        // Get User suspend
        $resultSuspendCountUser = AUser::getCountUsers('suspend');
        $CountSuspendUser = $resultSuspendCountUser->response;

        // get Count Users Auth Status
        $resultCountUsersAuthStatus = AUser::getCountUsersAuthStatus('pending');
        $countUsersAuthStatus = $resultCountUsersAuthStatus->response;


        // get Count Users Optional Auth Status
        $countUsersOptionalAuthStatus = AUser::getCountUsersOptionalAuthStatus('pending');


        /**
         * Get Cargoes pending
         */
        $CountCargoePending = 0;
        $resultCountCargoePending = Cargo::getCountCargoes('pending');
        if ($resultCountCargoePending->status == 200) {
            $CountCargoePending = $resultCountCargoePending->response;
        }


        /**
         * Get Cargoes Accepted
         */
        $CountCargoeAccepted = 0;
        $resultCountCargoeAccepted = Cargo::getCountCargoes('accepted');
        if ($resultCountCargoeAccepted->status == 200) {
            $CountCargoeAccepted = $resultCountCargoeAccepted->response;
        }
        $CountCargoeInPending = Cargo::getCountCargoesIn('pending')->response;
        $CountCargoeInAccepted = Cargo::getCountCargoesIn('accepted')->response;

        /**
         * Get Count requset  By Status
         */
        $CountRequestsPending =  Cargo::getAllRequestsCount('pending');
        $CountRequestsInPending =  Cargo::getAllRequestsInCount('pending');
        $CountRequestsAccepted =  Cargo::getAllRequestsCount('accepted');
        $CountRequestsInAccepted =  Cargo::getAllRequestsInCount('accepted');



        /**
         * Get All cars pending
         */
        $resultCarsPending = Car::getAllCars("pending");
        $dataCarsPending = 0;
        if ($resultCarsPending->status == 200 && !empty($resultCarsPending->response)) {
            $dataCarsPending = count($resultCarsPending->response);
        }


        /**
         * Get All Open Tickets
         */
        $ResultOpenTicket = ATicket::getCountTicketsOpen();
        $ticket_driver = (isset($Result['status_1'])) ? $Result['status_1'] : 0;
        $ticket_businessman = (isset($Result['status_2'])) ? $Result['status_2'] : 0;
        $ticket_ship = (isset($Result['status_3'])) ? $Result['status_3'] : 0;
        $ticket_air = (isset($Result['status_4'])) ? $Result['status_4'] : 0;
        $ticket_railroad = (isset($Result['status_5'])) ? $Result['status_5'] : 0;
        $ticket_inventory = (isset($Result['status_6'])) ? $Result['status_6'] : 0;
        $ticket_poster = (isset($Result['status_7'])) ? $Result['status_7'] : 0;
        $ticket_customs = (isset($Result['status_8'])) ? $Result['status_8'] : 0;

        /**
         * Get All Pending Bank Card
         */
        $resultBankCard = AUser::getBankCard('pending');
        $dataBankCard = 0;
        if ($resultBankCard->status == 200 && !empty($resultBankCard->response)) {
            $dataBankCard = count($resultBankCard->response);
        }


        /**
         * Get Complaint Pending
         */
        $resultCountComplaintPending = Complaint::getCountComplaint('pending');
        $CountComplaintPending = 0;
        if ($resultCountComplaintPending->status == 200 && !empty($resultCountComplaintPending->response)) {
            $CountComplaintPending = $resultCountComplaintPending->response;
        }


        /**
         * Get Complaint Accepted
         */
        $resultCountComplaintAccepted = Complaint::getCountComplaint('accepted');
        $CountComplaintAccepted = 0;
        if ($resultCountComplaintAccepted->status == 200 && !empty($resultCountComplaintAccepted->response)) {
            $CountComplaintAccepted = $resultCountComplaintAccepted->response;
        }


        /**
         * Get All Pending Withdrow Transactions
         */
        $ResultTransactionsWithdrawsPending = Transactions::getAllTransactionsByStatus('pending');
        $DataTransactionsWithdrawsPending = 0;
        if ($ResultTransactionsWithdrawsPending->status == 200 && !empty($ResultTransactionsWithdrawsPending->response)) {
            $DataTransactionsWithdrawsPending = count($ResultTransactionsWithdrawsPending->response);
        }


        /**
         * Get All Transactions
         */
        $ResultTransactionsWithdrawsPendingDeposit = Transactions::getAllTransactionsByStatus('pending_deposit');
        $DataTransactionsWithdrawsPendingDeposit = 0;
        if ($ResultTransactionsWithdrawsPendingDeposit->status == 200 && !empty($ResultTransactionsWithdrawsPendingDeposit->response)) {
            $DataTransactionsWithdrawsPendingDeposit = count($ResultTransactionsWithdrawsPendingDeposit->response);
        }


        /**
         * Get All Transactions
         */
        $ResultTask = Tasks::getTaskFromDashboard();
        $DataTask = [];
        if ($ResultTask->status == 200 && !empty($ResultTask->response)) {
            $DataTask = $ResultTask->response;
        }


        /**
         * Get Count Inquiry  By Status
         */
        $getCountInquiryGroundByStatus = Ground::getCountInquiryGroundByStatus('pending')->response;
        $getCountInquiryShipByStatus = Ship::getCountInquiryShipByStatus('pending')->response;
        $getCountInquiryAirByStatus = Air::getCountInquiryAirByStatus('pending')->response;
        $getCountInquiryRailroadByStatus = Railroad::getCountInquiryRailroadByStatus('pending')->response;
        $getCountInquiryInventoryByStatus = Inventory::getCountInquiryInventoryByStatus('pending')->response;
        $getCountInquiryCustomsByStatus = Customs::getCountInquiryCustomsByStatus('pending')->response;
        $getCountInquiryMiniCargoByStatus = MiniCargo::getCountInquiryMiniCargoByStatus('pending')->response;


        /**
         * Get Count Poster By Status
         */
        $getCountPostersByStatus = Poster::getCountPostersByStatus('pending');
        $getAllPosterExpertByStatus = Poster::getAllPosterExpertByStatus();
        $countPosterReportsPending = Poster::countPosterReportsPending();

        $countEmployPending = Hire::countEmployStatus('pending');
        $countDriverService = CV::countDriverService('pending');
        $countExchangeRequest = Exchange::countExchangeRequest('pending');

        $myPermission = Admin::getInfoPermissionsForSidebar();
        $slug__users = Admin::getSidebarPermission($myPermission, 'users');
        $slug__a_authorization = Admin::getSidebarPermission($myPermission, 'a_authorization');
        $slug__cargo = Admin::getSidebarPermission($myPermission, 'cargo');
        $slug__cargo_in = Admin::getSidebarPermission($myPermission, 'cargo_in');
        $slug__cars = Admin::getSidebarPermission($myPermission, 'cars');
        $slug__tickets_driver = Admin::getSidebarPermission($myPermission, 'tickets_driver');
        $slug__tickets_businessman = Admin::getSidebarPermission($myPermission, 'tickets_businessman');
        $slug__tickets_ship = Admin::getSidebarPermission($myPermission, 'tickets_ship');
        $slug__tickets_air = Admin::getSidebarPermission($myPermission, 'tickets_air');
        $slug__tickets_railroad = Admin::getSidebarPermission($myPermission, 'tickets_railroad');
        $slug__tickets_inventory = Admin::getSidebarPermission($myPermission, 'tickets_inventory');
        $slug__tickets_poster = Admin::getSidebarPermission($myPermission, 'tickets_poster');
        $slug__tickets_customs = Admin::getSidebarPermission($myPermission, 'tickets_customs');
        $slug__card_bank = Admin::getSidebarPermission($myPermission, 'card_bank');
        $slug__complaint = Admin::getSidebarPermission($myPermission, 'complaint');
        $slug__transaction = Admin::getSidebarPermission($myPermission, 'transaction');
        $slug__inquiry_ground = Admin::getSidebarPermission($myPermission, 'inquiry_ground');
        $slug__inquiry_air = Admin::getSidebarPermission($myPermission, 'inquiry_air');
        $slug__inquiry_ship = Admin::getSidebarPermission($myPermission, 'inquiry_ship');
        $slug__inquiry_railroad = Admin::getSidebarPermission($myPermission, 'inquiry_railroad');
        $slug__inquiry_inventory = Admin::getSidebarPermission($myPermission, 'inquiry_inventory');
        $slug__a_poster = Admin::getSidebarPermission($myPermission, 'a_poster');
        $slug__a_poster_expert = Admin::getSidebarPermission($myPermission, 'a_poster_expert');
        $slug__a_employ = Admin::getSidebarPermission($myPermission, 'a_employ');
        $slug__a_driver_service = Admin::getSidebarPermission($myPermission, 'driver-cv');
        $slug__a_exchange = Admin::getSidebarPermission($myPermission, 'exchange');
        $slug__inquiry_customs = Admin::getSidebarPermission($myPermission, 'inquiry_customs');
        $slug__inquiry_minicargo = Admin::getSidebarPermission($myPermission, 'inquiry_minicargo');

        enqueueScript('dashboard', '/dist/js/admin/dashboard.init.js');

        getHeader($lang['dashboard'], [
            'lang' => $lang,
            'roleInfo' => $dataCheckAdminRoleForCheck,
            'adminInfo' => $dataCheckAdminLogin,
            'pageSlugName' => 'dashboard',
            'pageSlugValue' => 'general',
        ]);
        ?>
        <div class="row">
            <div class="col-12">
                <div class="card widget-inline">
                    <div class="card-body">
                        <div class="row">

<!--                            --><?php //if ($slug__users == "yes") : ?>
<!--                                <div class="col-sm-6 col-xl-3">-->
<!--                                    <a href="/admin/users">-->
<!--                                        <div class="p-2 text-center mj-admin-monitor-item">-->
<!--                                            <i class="mdi mdi-account-clock mdi-24px color_blue"></i>-->
<!--                                            <h3><span data-plugin="counterup"-->
<!--                                                      class="color_blue">--><?php //= $CountSuspendUser; ?><!--</span></h3>-->
<!--                                            <p class="text-muted mb-0">--><?php //= $lang['a_users_suspend']; ?><!--</p>-->
<!--                                        </div>-->
<!--                                    </a>-->
<!--                                </div>-->
<!---->
<!--                                <div class="col-sm-6 col-xl-3">-->
<!--                                    <a href="/admin/users">-->
<!--                                        <div class="p-2 text-center mj-admin-monitor-item">-->
<!--                                            <i class="mdi mdi-account-clock mdi-24px color_blue"></i>-->
<!--                                            <h3><span data-plugin="counterup"-->
<!--                                                      class="color_blue">--><?php //= $countUsersAuthStatus; ?><!--</span></h3>-->
<!--                                            <p class="text-muted font-15 mb-0">--><?php //= $lang['a_users_auth_pending']; ?><!--</p>-->
<!--                                        </div>-->
<!--                                    </a>-->
<!--                                </div>-->
<!--                            --><?php //endif; ?>
<!---->
<!--                            --><?php //if ($slug__a_authorization == "yes"): ?>
<!--                                <div class="col-sm-6 col-xl-3">-->
<!--                                    <a href="/admin/authorization">-->
<!--                                        <div class="p-2 text-center mj-admin-monitor-item">-->
<!--                                            <i class="mdi mdi-account-clock mdi-24px color_blue"></i>-->
<!--                                            <h3><span data-plugin="counterup"-->
<!--                                                      class="color_blue">--><?php //= $countUsersOptionalAuthStatus; ?><!--</span>-->
<!--                                            </h3>-->
<!--                                            <p class="text-muted font-15 mb-0">--><?php //= $lang['a_users_auth_optional_pending']; ?><!--</p>-->
<!--                                        </div>-->
<!--                                    </a>-->
<!--                                </div>-->
<!--                            --><?php //endif; ?>

                            <?php if ($slug__cargo == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/cargo/pending">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-truck-trailer mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $CountCargoePending; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0 "><?= $lang['cargoes_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/cargo/accepted">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-truck-check mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $CountCargoeAccepted; ?></span></h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['cargo_accepted_open']; ?></p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/request-out">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-truck-trailer mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $CountRequestsPending; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0 "><?= $lang['request_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>



                            <?php endif; ?>

                            <?php if ($slug__cargo_in == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/cargo-in/pending">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-truck-trailer mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $CountCargoeInPending; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0 "><?= $lang['cargoes_in_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/cargo-in/accepted">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-truck-check mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $CountCargoeInAccepted; ?></span></h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['cargo_in_accepted_open']; ?></p>
                                        </div>
                                    </a>
                                </div>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/request-in">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-truck-trailer mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $CountRequestsInPending; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0 "><?= $lang['request_in_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>


                            <?php endif; ?>

<!--                            --><?php //if ($slug__cars == "yes"): ?>
<!--                                <div class="col-sm-6 col-xl-3">-->
<!--                                    <a href="/admin/car/pending">-->
<!--                                        <div class="p-2 text-center mj-admin-monitor-item">-->
<!--                                            <i class="mdi mdi-dump-truck mdi-24px color_blue"></i>-->
<!--                                            <h3><span data-plugin="counterup"-->
<!--                                                      class="color_blue">--><?php //= $dataCarsPending; ?><!--</span></h3>-->
<!--                                            <p class="text-muted font-15 mb-0">--><?php //= $lang['cars_pending']; ?><!--</p>-->
<!--                                        </div>-->
<!--                                    </a>-->
<!--                                </div>-->
<!--                            --><?php //endif; ?>

                            <?php if ($slug__tickets_driver == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/ticket/d/open/1">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-account-details mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $ticket_driver; ?></span></h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['tickets_open_driver']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__tickets_businessman == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/ticket/d/open/2">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-account-details mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $ticket_businessman; ?></span></h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['tickets_open_businessman']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__tickets_ship == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/ticket/d/open/3">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-account-details mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $ticket_ship; ?></span></h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['tickets_open_ship']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__tickets_air == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/ticket/d/open/4">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-account-details mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $ticket_air; ?></span></h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['tickets_open_air']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__tickets_railroad == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/ticket/d/open/5">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-account-details mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $ticket_railroad; ?></span></h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['tickets_open_railroad']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__tickets_inventory == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/ticket/d/open/6">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-account-details mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $ticket_inventory; ?></span></h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['tickets_open_inventory']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__tickets_poster == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/ticket/d/open/7">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-account-details mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $ticket_poster; ?></span></h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['tickets_open_poster']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__tickets_customs == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/ticket/d/open/8">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-account-details mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $ticket_customs; ?></span></h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['tickets_open_customs']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__card_bank == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/credit/pending">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-credit-card-clock-outline mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $dataBankCard; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['card_banks_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__complaint == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/complaint/pending">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-information-outline mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $CountComplaintPending; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['complaints_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/complaint/accepted">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-alert-outline mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $CountComplaintAccepted; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['complaints_accepted']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__transaction == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/transaction/withdraw/pending">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-currency-usd mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $DataTransactionsWithdrawsPending; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['withdraws_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>

                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/transaction/deposit/pending">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-currency-usd mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $DataTransactionsWithdrawsPendingDeposit; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['withdraws_pending_deposit']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>


                            <?php if ($slug__inquiry_ground == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/inquiry/ground/pending">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-currency-rial mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $getCountInquiryGroundByStatus; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['a_inquiry_ground_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__inquiry_ship == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/inquiry/ship/pending">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-currency-rial mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $getCountInquiryShipByStatus; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['a_inquiry_ship_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__inquiry_air == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/inquiry/air/pending">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-currency-rial mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $getCountInquiryAirByStatus; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['a_inquiry_air_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__inquiry_railroad == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/inquiry/railroad/pending">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-currency-rial mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $getCountInquiryRailroadByStatus; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['a_inquiry_railroad_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__inquiry_inventory == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/inquiry/inventory/pending">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-currency-rial mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $getCountInquiryInventoryByStatus; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['a_inquiry_inventory_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__a_poster == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/poster/pending">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-book-clock mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $getCountPostersByStatus; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['a_pending_poster']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>


                            <?php if ($slug__inquiry_customs == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/inquiry/customs/pending">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-book-clock mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $getCountInquiryCustomsByStatus; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['a_inquiry_customs_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <?php if ($slug__inquiry_minicargo == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/inquiry/minicargo/pending">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-book-clock mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $getCountInquiryMiniCargoByStatus; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['inquiry_minicargo_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__a_poster_expert == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/poster-expert">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-account-hard-hat mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $getAllPosterExpertByStatus['pending']; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['a_request_expert_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__a_poster_expert == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/poster-expert">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-account-hard-hat mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $getAllPosterExpertByStatus['accepted']; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['a_request_expert_accepted']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__a_poster == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/poster/reports">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-restore-alert mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $countPosterReportsPending; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['a_new_report_poster']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>

                            <?php if ($slug__a_employ == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/hire">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-restore-alert mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $countEmployPending; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['a_hire_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <?php if ($slug__a_driver_service == "yes"): ?>
                                <div class="col-sm-6 col-xl-3">
                                    <a href="/admin/driver-services">
                                        <div class="p-2 text-center mj-admin-monitor-item">
                                            <i class="mdi mdi-restore-alert mdi-24px color_blue"></i>
                                            <h3><span data-plugin="counterup"
                                                      class="color_blue"><?= $countDriverService; ?></span>
                                            </h3>
                                            <p class="text-muted font-15 mb-0"><?= $lang['driver_service_pending']; ?></p>
                                        </div>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="col-sm-6 col-xl-3">
                                <a href="/admin/exchange/request-list">
                                    <div class="p-2 text-center mj-admin-monitor-item">
                                        <i class="mdi mdi-restore-alert mdi-24px color_blue"></i>
                                        <h3><span data-plugin="counterup"
                                                  class="color_blue"><?= $countExchangeRequest ?></span>
                                        </h3>
                                        <p class="text-muted font-15 mb-0"><?= $lang['buy_sell_currencies']; ?></p>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <!-- end row -->
                    </div>
                </div>
            </div>
        </div>

        <?php
        if (!empty($DataTask)) {
            ?>
            <div class="row">
                <?php
                foreach ($DataTask as $loop) {
                    ?>
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <div class="card">
                            <div class="card-body">
                                <?php
                                if ($loop->task_status == "pending") {
                                    echo '<span class="badge bg-soft-warning text-warning float-end">' . $lang['a_task_pending'] . '</span>';
                                } elseif ($loop->task_status == "process") {
                                    echo '<span class="badge bg-soft-info text-warning float-end">' . $lang['a_task_process'] . '</span>';
                                } elseif ($loop->task_status == "rejected") {
                                    echo '<span class="badge bg-soft-danger text-warning float-end">' . $lang['a_task_rejected'] . '</span>';
                                } elseif ($loop->task_status == "ok") {
                                    echo '<span class="badge bg-soft-success text-warning float-end">' . $lang['a_task_ok'] . '</span>';
                                }
                                ?>
                                <h5 class="mt-0">
                                    <a href="/admin/tasks/info/<?= $loop->task_id; ?>"
                                       class="text-dark">
                                        <?= $loop->task_title; ?>
                                    </a>
                                </h5>
                                <p><?= mb_strimwidth($loop->task_desc, 0, 50, '...'); ?></p>
                                <div class="clearfix"></div>
                                <div class="row">
                                    <div class="col-3 col-sm-3 col-md-4 col-lg-4 text-center">
                                        <img src="<?= Utils::fileExist($loop->admin_avatar, USER_AVATAR); ?>"
                                             alt="<?= $loop->admin_nickname; ?>"
                                             data-bs-toggle="tooltip"
                                             data-bs-placement="top"
                                             title="<?= $loop->admin_nickname; ?>"
                                             width="45"
                                             class="img-fluid rounded-circle img-thumbnail">

                                    </div>
                                    <div class="col-9 col-sm-9 col-md-8 col-lg-8">
                                        <div class="font-13 mt-2 mb-0">
                                            <i class="mdi mdi-calendar"></i>
                                            <span class="my-timer-class"
                                                  id="timer-id-<?= $loop->task_id; ?>"
                                                  data-tj-start-time="<?= ($loop->task_start_date <= time()) ? $loop->task_start_date : ""; ?>"
                                                  data-tj-end-time="<?= $loop->task_end_date; ?>"></span>
                                            <div class="progress progress-sm">
                                                <div class="progress-bar  progress-bar-striped progress-bar-animated"
                                                     role="progressbar"
                                                     data-tj-id="timer-id-<?= $loop->task_id; ?>"
                                                     aria-label="Animated striped example"
                                                     aria-valuenow="75"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100"
                                                     style=""></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>


        <?php
        getFooter(
            [
                $lang['help_logout'],
                $lang['help_lock_screen'],
                $lang['help_myaccount'],
                $lang['help_myaccount_1'],
                $lang['help_login'],
                $lang['help_login_1'],
                $lang['help_dark_light'],
                $lang['help_time'],
                $lang['help_time_2'],
            ]
        );

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