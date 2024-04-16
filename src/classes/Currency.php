<?php


use MJ\Database\DB;
use MJ\Security\Security;
use function MJ\Keys\sendResponse;

class Currency
{


    /**
     * Get All Currencies
     * @param $status
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getAllCurrencies($status = null)
    {

        $response = sendResponse(0, "Error Msg");

        if (empty($status)) {
            $sql = "SELECT * FROM tbl_currency ORDER BY currency_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM tbl_currency WHERE currency_status=:currency_status ORDER BY currency_id DESC ;";
            $params = [
                'currency_status' => $status
            ];
        }

        $result = DB::rawQuery($sql, $params);


        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get Currency Info By Id
     * @param $id int
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getCurrencyById($id)
    {
        $response = sendResponse(0, "");

        $sql = "SELECT * FROM `tbl_currency` WHERE currency_id=:currency_id";
        $params = [
            'currency_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;
    }


    /**
     * Edit Car Category By ID
     * @param $id
     * @param $title
     * @param $status
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function editCurrency($id, $title, $status)
    {

        $response = sendResponse(0, "");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }

        $res = self::getCurrencyById($id);
        $temp = [];
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->currency_options;
        }


        if (!empty($temp)) {
            $value = json_decode($temp, true);

            $array = [];
            $array['create'] = $admin_id;
            $array['date'] = time();
            array_push($value['update'], ($array));

        }

        if ($admin_id > 0) {

            $sql = 'UPDATE `tbl_currency` SET `currency_name`=:currency_name,`currency_status`=:currency_status,`currency_options`=:currency_options
                    WHERE `currency_id`=:currency_id';
            $params = [
                'currency_name' => $title,
                'currency_status' => $status,
                'currency_options' => json_encode($value),
                'currency_id' => $id,
            ];

            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }
    /**
     * Get Currency Info By Id
     * @param $id int
     * @return Object
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getCurrencyNameById($id)
    {
        $sql = "SELECT * FROM `tbl_currency` WHERE currency_id=:currency_id";
        $params = [
            'currency_id' => $id,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $currency  =  $result->response ;
            return $currency[0]->currency_name;
        }else{
            return null ;
        }
    }


}