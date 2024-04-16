<?php

use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Exchange
{

    public static function getPricesFromLivePrice()
    {
        $response = sendResponse(0, 'error');
        $sql = "select * from tbl_live_price ";
        $params = [];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'success', $result->response);
        }
        return $response;


    }

    public static function getPricesFromLivePriceBonBast()
    {
        $response = sendResponse(0, 'error');
        $sql = "select * from tbl_live_price_bonbast ";
        $params = [];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'success', $result->response);
        }
        return $response;


    }

    public static function getPricesFromLivePriceByStatus($status = "active")
    {
        $response = sendResponse(0, 'error');
        $sql = "select * from tbl_live_price where tbl_live_price.status = :status";
        $params = [
            'status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'success', $result->response);
        }
        return $response;


    }

    public static function getPricesFromBonbast($order_type, $status = "active")
    {
        $response = sendResponse(0, 'error');
        $sql = "select * from tbl_live_price_bonbast where $order_type = :status";
        $params = [
            'status' => $status
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'success', $result->response);
        }
        return $response;


    }

    public static function updatePriceSetting($price_id, $ir_price_status, $ru_price_status, $du_price_status, $tr_price_status, $ir_plus_value, $ru_plus_value, $du_plus_value, $tr_plus_value, $ir_minus_value, $ru_minus_value, $du_minus_value, $tr_minus_value)
    {

        $sql = "UPDATE  tbl_live_price_bonbast set 
                           tbl_live_price_bonbast.ir_order_status = :ir_price_status,
                           tbl_live_price_bonbast.ru_order_status = :ru_price_status,
                           tbl_live_price_bonbast.du_order_status = :du_price_status,
                           tbl_live_price_bonbast.tr_order_status = :tr_price_status,
                           tbl_live_price_bonbast.ir_plus_value = :ir_plus_value,
                           tbl_live_price_bonbast.ru_plus_value = :ru_plus_value,
                           tbl_live_price_bonbast.du_plus_value = :du_plus_value,
                           tbl_live_price_bonbast.tr_plus_value = :tr_plus_value,
                           tbl_live_price_bonbast.ir_mines_value = :ir_minus_value,
                           tbl_live_price_bonbast.ru_mines_value = :ru_minus_value,
                           tbl_live_price_bonbast.du_mines_value = :du_minus_value,
                           tbl_live_price_bonbast.tr_mines_value = :tr_minus_value
                      
                           
                           where    tbl_live_price_bonbast.id = :price_id
";
        $params = [
            "ir_price_status" => $ir_price_status,
            "ru_price_status" => $ru_price_status,
            "du_price_status" => $du_price_status,
            "tr_price_status" => $tr_price_status,
            "ir_plus_value" => $ir_plus_value,
            "ru_plus_value" => $ru_plus_value,
            "du_plus_value" => $du_plus_value,
            "tr_plus_value" => $tr_plus_value,
            "ir_minus_value" => $ir_minus_value,
            "ru_minus_value" => $ru_minus_value,
            "du_minus_value" => $du_minus_value,
            "tr_minus_value" => $tr_minus_value,
            "price_id" => $price_id
        ];
        $response = sendResponse(0, 'error');


        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'success');
        }
        return $response;

    }

    public static function getBonBastPrices()
    {
        $arrContextOptions = array();
        $data = array('hash' => 'fbfc3c76c92b60ac4080458e265ecfb5');
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => "Content-type: application/x-www-form-urlencoded",
                'content' => http_build_query($data)
            ),
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );

        $context = stream_context_create($opts);

        $content = file_get_contents('https://bonbast.net/api/ntirapp', false, $context);
        $json = json_decode($content, true);


        $output = [
            'USD' => $json->usd1
        ];
    }


    public static function insertExchangeRequest($user_id, $request_type, $price_id, $price_buy, $price_sell, $request_side ,$count)
    {
        $response = sendResponse(0, 'ERR');
        $sql = 'insert into  tbl_exchange_request
    ( user_id, request_type, price_id, price_buy, price_sell, request_side ,request_count, request_create_at, request_updated_at, request_status)
    values(:user_id, :request_type, :price_id, :price_buy, :price_sell, :request_side,:request_count ,  :request_create_at, :request_updated_at, :request_status)
';
        $params = [
            "user_id" => $user_id,
            "request_type" => $request_type,
            "price_id" => $price_id,
            "price_buy" => $price_buy,
            "price_sell" => $price_sell,
            "request_side" => $request_side,
            "request_count" => $count,
            "request_create_at" => time(),
            "request_updated_at" => time(),
            "request_status" => 'pending',
        ];

        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, $result, $result->response);
        }
        return $response;
    }

    public static function getRequestDetail($request_id)
    {
        $response = sendResponse(0, 'error');
        $sql = "select * from tbl_exchange_request where request_id = :request_id";
        $params = [
            'request_id' => $request_id
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'success', $result->response[0]);
        }
        return $response;
    }

    public static function getPriceDetail($price_id)
    {
        $response = sendResponse(0, 'error');
        $sql = "select * from tbl_live_price_bonbast where id = :price_id";
        $params = [
            'price_id' => $price_id
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'success', $result->response[0]);
        }
        return $response;
    }


    public static function getRequestDescs($request_id)
    {
        $response = sendResponse(0, 'error');
        $sql = "select * from tbl_exchange_request_desc where request_id = :request_id";
        $params = [
            'request_id' => $request_id
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'success', $result->response);
        }
        return $response;
    }


    public static function insertRequestDescription($request_id, $admin_description)
    {
        $response = sendResponse(0, 'ERR');
        $sql = 'insert into  tbl_exchange_request_desc
                    (request_id, desc_text, admin_id, desc_created_at)
                values
                    (:request_id, :desc_text, :admin_id, :desc_created_at)';
        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }
        $params = [
            "request_id" => $request_id,
            "desc_text" => $admin_description,
            "admin_id" => $admin_id,
            "desc_created_at" => time(),
        ];

        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'success', $result->response);
        }
        return $response;
    }
   public static function updateRequestStatus($request_id, $request_status)
    {
        $response = sendResponse(0, 'ERR');
        $sql = 'update tbl_exchange_request set request_status = :request_status where request_id = :request_id';

        $params = [
            "request_id" => $request_id,

            "request_status" => $request_status,
        ];

        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            $response = sendResponse(200, 'success');
        }
        return $response;
    }

    public static function countExchangeRequest($status)
    {
        $response = 0;
        $sql = "SELECT count(*) AS count FROM `tbl_exchange_request`  WHERE `tbl_exchange_request`.request_status=:request_status";
        $params = [
            'request_status' => $status,
        ];
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $response = $result->response[0]->count;
        }
        return $response;
    }

}