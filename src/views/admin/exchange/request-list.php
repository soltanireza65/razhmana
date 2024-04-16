<?php

use MJ\Security\Security;
use MJ\Utils\Utils;

$pageSlug = "exchange";
// permission_can_show

global $lang;

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
//custom css


// Load Stylesheets & Icons
        enqueueStylesheet('dataTable-bs5-css', '/dist/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css');
        enqueueStylesheet('dataTable-responsive-bs5-css', '/dist/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css');
        enqueueStylesheet('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.css');
// Load Script In Footer
        enqueueScript('dataTable-js', '/dist/libs/datatables.net/js/jquery.dataTables.min.js');
        enqueueScript('dataTable-bs5-js', '/dist/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js');
        enqueueScript('dataTable-responsive-js', '/dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js');
        enqueueScript('dataTable-responsive-bs5-js', '/dist/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js');
         enqueueScript('toast', '/dist/libs/jquery-toast-plugin/jquery.toast.min.js');
 // header text
        getHeader($lang["live_price"], [
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
            <style>
                .mj-exchange-req-filter {

                    display: flex;
                    gap: 10px;
                    padding-bottom: 20px;
                    background: #43a8f7;
                    padding: 10px;
                    border-radius: 10px 10px 0 0;
                }
                .mj-exchange-req-filter div{
                    display: flex;
                   flex-direction: column;
                    gap: 10px;
                    width: 40%;
                }
                td a{
                    background: white !important;
                    padding: 5px 30px;
                    border-radius: 10px;
                    color: #43a8f7;
                    border: 1px solid #43a8f7 ;

                }
                td a:hover{
                    color: #43a8f7 !important;


                }
                .mj-exchange-req-filter select{
                    height: 48px;
                    border-radius: 10px;
                    padding-inline: 10px;
                    outline: unset !important;
                    border: unset !important;
                }
                .mj-exchange-req-filter label{
                    color: #fff !important;
                }
                .dataTables_wrapper .row:nth-child(1){
                    background: #43a8f7;
                    padding: 10px;
                    border-radius: 0 0 10px 10px ;
                    margin-inline: 0 !important;
                    align-items: center;
                }
                .dataTables_wrapper .row:nth-child(1) .col-sm-6{
                   padding-inline: 0 !important;
                }
                #example_filter label{
                    color:  #fff !important;
                }
                .dataTables_length label{
                    color:  #fff !important;
                }
                .dataTables_filter label{
                    color:  #fff !important;
                    width: 100% !important;
                    display: flex;
                    flex-direction: column;
                    direction: rtl;
                    text-align: right !important;
                    gap: 10px;
                }
                .dataTables_filter input{
                    width: 100% !important;
                    border-radius: 10px;
                    height: 48px;
                    margin: unset !important;
                }
            </style>

            <!--start custom html-->
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="mj-exchange-req-filter">
                                <div>
                                <label for="requestTypeFilter"><?=$lang['filter_by_request_side']?>:</label>
                                <select id="requestTypeFilter">
                                    <option value=""><?=$lang['all']?></option>
                                    <option value="buy"><?=$lang['buy']?></option>
                                    <option value="sell"><?=$lang['sell']?></option>
                                </select>
                                </div>
                                <div>
                                <label for="requestStatusFilter"><?=$lang['filter_by_request_status']?>:</label>
                                <select id="requestStatusFilter">
                                    <option value=""><?=$lang['all']?></option>
                                    <option value="pending"><?=$lang['pending']?></option>
                                    <option value="accepted"><?=$lang['accepted']?></option>
                                    <option value="rejected"><?=$lang['rejected']?></option>
                                </select>
                                </div>
                            </div>
<!--                            <div class="mj-exchange-req-filter">-->
<!---->
<!--                            </div>-->

                            <table id="example"
                                   class="table table-flush align-middle table-row-bordered table-row-solid gy-4 gs-9">
                                 <thead class="border-gray-200 fs-5 fw-semibold bg-lighten">
                                <tr>

                                    <th> <?=$lang['request_id']?></th>
                                    <th><?=$lang['request_type']?>  </th>
                                    <th><?=$lang['request_title']?>  </th>
                                    <th><?=$lang['request_side']?> </th>
                                    <th><?=$lang['request_status']?> </th>
                                    <th><?=$lang['action']?></th>
                                </tr>
                                </thead>
                                 <tbody class="fw-6 fw-semibold text-gray-600">

                                </tbody>
                                <tfoot>
                                <tr>
                                    <th>Request ID</th>
                                    <th>Request Type</th>
                                    <th>Title</th>
                                    <th>Request Side</th>
                                    <th>Request Status</th>
                                    <th>Action</th>
                                </tr>
                                </tfoot>
                             </table>

                        </div>
                    </div>
                </div>
            </div>
            <!-- end custom html-->


            <input type="hidden" id="token" name="token"
                   value="<?= $_SESSION['dt-cv'] = "dt-cv-44"; ?>">

            <input type="hidden" id="token-price" name="token-price"
                   value="<?= Security::initCSRF2() ?>">
            <?php
            // start roles 3
        } else {
            getPermissionsBlock();
        }
        // end roles 3

        getFooter(
            [
                $lang['help_academy_1'],
                $lang['help_academy_2'],
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
    let dataTable;
    $(document).ready(function () {
        dataTable = $('#example').DataTable({
            processing: true,
            serverSide: true,
            seraching:true,
            ajax: {
                "url": "/api/datatable/dt-exchange-requests",
                "type": "POST",
                "data": function (d) {
                    // Add the filter values to the AJAX request
                    d.requestTypeFilter = $('#requestTypeFilter').val();
                    d.requestStatusFilter = $('#requestStatusFilter').val();
                }
            },
            columns: [
                {"data": "request_id"},
                {"data": "request_type"},
                {"data": "title"},
                {"data": "request_side"},
                {"data": "request_status"},
                {"data": "action"},
            ]
        });
        // Add event listeners to the filter select elements
        $('#requestTypeFilter, #requestStatusFilter').on('change', function () {
            dataTable.draw();
        });

        // Custom filtering function
        $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
            // Get selected filter values
            const requestTypeFilter = $('#requestTypeFilter').val();
            const requestStatusFilter = $('#requestStatusFilter').val();

            // Apply filtering
            if (
                (requestTypeFilter === '' || data[1] === requestTypeFilter) &&
                (requestStatusFilter === '' || data[4] === requestStatusFilter)
            ) {
                return true; // Show the row if both filters match or if filters are empty
            }

            return false; // Hide the row if filters do not match
        });
    });


</script>