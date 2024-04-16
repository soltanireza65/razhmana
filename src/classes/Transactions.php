<?php


use MJ\Database\DB;
use MJ\Security\Security;
use MJ\Utils\Utils;
use function MJ\Keys\sendResponse;

class Transactions
{


    /**
     * Get All Transactions By Type
     * @param $type $status
     * @return Object
     * @author Tjavan
     * @version 2.0.0
     */
    public static function getAllTransactions($type = null)
    {

        $response = sendResponse(0, "Error Msg");
        if (empty($type)) {
            $sql = "SELECT * FROM `tbl_transactions`  ORDER BY transaction_id DESC ;";
            $params = [];
        } else {
            $sql = "SELECT * FROM `tbl_transactions`  WHERE `transaction_type`=:transaction_type ORDER BY transaction_id DESC ;";
            $params = [
                'transaction_type' => $type
            ];
        }


        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get All User Transactions By User ID
     * @param $type $status
     * @return Object
     * @author Tjavan
     * @version 2.0.0
     */
    public static function getAllUserTransactionsByUserID($userID)
    {

        $response = sendResponse(0, "Error Msg");
        $sql = "SELECT * FROM `tbl_transactions`  WHERE `user_id`=:user_id ORDER BY transaction_id DESC ;";
        $params = [
            'user_id' => $userID
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get All Transactions By Status
     * @param $status
     * @return Object
     * @author Tjavan
     * @version 2.0.0
     */
    public static function getAllTransactionsByStatus($status)
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_transactions`  WHERE `transaction_status`=:transaction_status ORDER BY transaction_id DESC ;";
        $params = [
            'transaction_status' => $status
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Get Transaction By ID
     * @param $ID
     * @return Object
     * @author Tjavan
     * @version 2.0.0
     */
    public static function getTransactionsByID($ID)
    {

        $response = sendResponse(0, "Error Msg");

        $sql = "SELECT * FROM `tbl_transactions`  WHERE `transaction_id`=:transaction_id ;";
        $params = [
            'transaction_id' => $ID
        ];

        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }

        return $response;
    }


    /**
     * Change User Balance  Only balance_in_withdraw
     * @param $userID
     * @param $currencyID
     * @param $amount
     * @return stdClass
     */
    public static function changeUserBalanceWithdraw($userID, $currencyID, $amount)
    {
        $response = sendResponse(0, "Error Msg");


        $sql = "UPDATE tbl_balance SET balance_in_withdraw = balance_in_withdraw + {$amount} WHERE user_id = :user_id AND currency_id=:currency_id ";
        $params = [
            'currency_id' => $currencyID,
            'user_id' => $userID,
        ];

        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }

        return $response;
    }


    /**
     * Change User Balance    balance_in_withdraw and balance_value
     * @param $userID
     * @param $currencyID
     * @param $amount
     * @param $amount2
     * @return stdClass
     */
    public static function changeUserBalanceWithdraw2($userID, $currencyID, $amount, $amount2)
    {
        $response = sendResponse(0, "Error Msg");


        $sql = "UPDATE tbl_balance SET balance_in_withdraw = balance_in_withdraw + {$amount} , balance_value = balance_value + {$amount2} WHERE user_id = :user_id AND currency_id=:currency_id ";
        $params = [
            'currency_id' => $currencyID,
            'user_id' => $userID,
        ];

        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }

        return $response;
    }


    /**
     * Change Transaction Status
     * @param $type
     * @param $newValue
     * @param $transactionID
     * @param $array
     * @return stdClass
     */
    public static function changeTransactionSatsus($type, $newValue, $transactionID, $array)
    {
        $response = sendResponse(0, "Error Msg");

        $sql = 'UPDATE `tbl_transactions` SET ' . $type . '=:newValue ,`transaction_updates`=:transaction_updates WHERE `transaction_id`=:transaction_id';
        $params = [
            'newValue' => $newValue,
            'transaction_updates' => json_encode($array),
            'transaction_id' => $transactionID,
        ];


        $result = DB::update($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "");
        }
        return $response;
    }


    /**
     * Edit Transaction Status AND Balance By Admin
     * @param $transactionID
     * @param $type
     * @param $newValue
     * @return stdClass
     */
    public static function editTransactionStatusByAdmin($transactionID, $type, $newValue)
    {
        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $res = self::getTransactionsByID($transactionID);
        $temp = [];
        $tempType = '';
        $tempStatus = "";
        $tempUSerID = 0;
        $tempCurrencyID = 0;
        $tempAmount = 0;
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->transaction_updates;
            $tempType = $res->response[0]->transaction_type;
            $tempStatus = $res->response[0]->transaction_status;
            $tempUSerID = $res->response[0]->user_id;
            $tempAmount = $res->response[0]->transaction_amount;
            $tempCurrencyID = $res->response[0]->currency_id;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = $type;
            $a['value'] = $newValue;
            $a['date'] = time();
            array_push($array['update'], $a);

        } else {
            $a = [];
            $array['update'] = [];

            $a['admin'] = $admin_id;
            $a['type'] = $type;
            $a['value'] = $newValue;
            $a['date'] = time();
            array_push($array['update'], $a);
        }


        if ($admin_id > 0) {

            if ($tempType == "withdraw" && $tempStatus == "pending") {
                if ($newValue == "completed") {

                    $sql = 'UPDATE `tbl_transactions` SET transaction_status=:newValue ,`transaction_updates`=:transaction_updates WHERE `transaction_id`=:transaction_id;
                            UPDATE tbl_balance SET balance_in_withdraw = balance_in_withdraw - :balance_in_withdraw WHERE user_id = :user_id AND currency_id=:currency_id';
                    $params = [
                        'newValue' => "completed",
                        'transaction_updates' => json_encode($array),
                        'transaction_id' => $transactionID,
                        'balance_in_withdraw' => $tempAmount,
                        'user_id' => $tempUSerID,
                        'currency_id' => $tempCurrencyID,
                    ];
                    $result = DB::transactionQuery($sql, $params);
                    if ($result->status == 200) {
                        Admin::SetAdminLog("a_change_transaction_withdraw_completed_" . $transactionID, "transaction");
                        $response = sendResponse(200, "withdraw completed");
                    }

                } elseif ($newValue == "rejected") {

                    $sql = 'UPDATE `tbl_transactions` SET transaction_status=:newValue ,`transaction_updates`=:transaction_updates WHERE `transaction_id`=:transaction_id;
                            UPDATE tbl_balance SET balance_in_withdraw = balance_in_withdraw - :balance_in_withdraw
                            ,`balance_value`=balance_value + :balance_value WHERE user_id = :user_id AND currency_id=:currency_id';
                    $params = [
                        'newValue' => "rejected",
                        'transaction_updates' => json_encode($array),
                        'transaction_id' => $transactionID,
                        'balance_in_withdraw' => $tempAmount,
                        'balance_value' => $tempAmount,
                        'user_id' => $tempUSerID,
                        'currency_id' => $tempCurrencyID,
                    ];

                    $result = DB::transactionQuery($sql, $params);
                    if ($result->status == 200) {
                        Admin::SetAdminLog("a_change_transaction_withdraw_rejected_" . $transactionID, "transaction");
                        $response = sendResponse(200, "withdraw rejected");
                    }

                } else {
                    return $response;
                }
            } elseif ($tempType == "deposit") {
                if ($newValue == "paid") {

                    $sql = 'UPDATE `tbl_transactions` SET transaction_status=:newValue ,`transaction_updates`=:transaction_updates WHERE `transaction_id`=:transaction_id;
                            UPDATE tbl_balance SET `balance_value`=balance_value + :balance_value WHERE user_id = :user_id AND currency_id=:currency_id';
                    $params = [
                        'newValue' => "paid",
                        'transaction_updates' => json_encode($array),
                        'transaction_id' => $transactionID,
                        'balance_value' => $tempAmount,
                        'user_id' => $tempUSerID,
                        'currency_id' => $tempCurrencyID,
                    ];
                    $result = DB::transactionQuery($sql, $params);
                    if ($result->status == 200) {
                        Admin::SetAdminLog("a_change_transaction_deposit_paid_" . $transactionID, "transaction");
                        $response = sendResponse(200, "deposit paid");
                    }

                } elseif ($newValue == "rejected_deposit") {


                    $sql = 'UPDATE `tbl_transactions` SET transaction_status=:newValue ,`transaction_updates`=:transaction_updates WHERE `transaction_id`=:transaction_id';
                    $params = [
                        'newValue' => "rejected_deposit",
                        'transaction_updates' => json_encode($array),
                        'transaction_id' => $transactionID,
                    ];

                    $result = DB::transactionQuery($sql, $params);
                    if ($result->status == 200) {
                        Admin::SetAdminLog("a_change_transaction_deposit_rejected_" . $transactionID, "transaction");
                        $response = sendResponse(200, "deposit rejected");
                    }

                } else {
                    return $response;
                }
            } else {
                return $response;
            }
        }
        return $response;
    }


    /**
     * Change  Transaction Info By Admin
     * @param $transactionID
     * @param $type
     * @param $newValue
     * @return stdClass
     */
    public static function editTransactionInfoByAdmin($transactionID, $type, $newValue)
    {
        $response = sendResponse(0, "Error Msg");

        $admin_id = 0;
        if (isset($_COOKIE['UID']) && !empty($_COOKIE['UID'])) {
            $admin_id = intval(Security::decrypt($_COOKIE['UID']));
        }


        $res = self::getTransactionsByID($transactionID);
        $temp = [];
        $tempType = '';
        if ($res->status == 200 && !empty($res->response) && isset($res->response[0])) {
            $temp = $res->response[0]->transaction_updates;
            $tempType = $res->response[0]->transaction_type;
        }

        $array = [];
        if (!empty($temp)) {
            $array = json_decode($temp, true);
            $a = [];
            $a['admin'] = $admin_id;
            $a['type'] = $type;
            $a['value'] = $newValue;
            $a['date'] = time();
            array_push($array['update'], $a);

        } else {
            $a = [];
            $array['update'] = [];

            $a['admin'] = $admin_id;
            $a['type'] = $type;
            $a['value'] = $newValue;
            $a['date'] = time();
            array_push($array['update'], $a);
        }

//
//        if ($tempType == "withdraw") {
//            if ($newValue == "completed" || $newValue == "pending" || $newValue == "rejected") {
//
//            } else {
//                return $response;
//            }
//        } else {
//            if ($newValue == "unpaid" || $newValue == "expired") {
//
//            } else {
//                return $response;
//            }
//        }
        if ($admin_id > 0) {
            $sql = 'UPDATE `tbl_transactions` SET ' . $type . '=:newValue ,`transaction_updates`=:transaction_updates WHERE `transaction_id`=:transaction_id';
            $params = [
                'newValue' => $newValue,
                'transaction_updates' => json_encode($array),
                'transaction_id' => $transactionID,
            ];


            $result = DB::update($sql, $params);

            if ($result->status == 200) {
                $response = sendResponse(200, "");
            }
        }
        return $response;
    }


    /**
     * Get All Transaction By Time
     * @param $dateStart
     * @param $dateEnd
     * @return Object
     * @author Tjavan
     * @version 2.0.0
     */
    public static function getTransactionByTime($dateStart, $dateEnd)
    {
        $response = sendResponse(-10, "");

        $sql = 'SELECT * FROM `tbl_transactions` WHERE transaction_date>=:transaction_Start AND transaction_date<=:transaction_End;';
        $params = [
            'transaction_Start' => $dateStart,
            'transaction_End' => $dateEnd,
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $response = sendResponse(200, "", $result->response);
        }

        return $response;

    }


    public static function getTransactionsList2($userId, $currency, $status = 'all', $search = null)
    {
        $response = sendResponse(200, $status, []);
        $language = 'fa_IR';
        if (isset($_COOKIE['language'])) {
            $language = $_COOKIE['language'];
        }
        if (is_null($search) || empty($search)) {
            $sql_search = '';
        } else {
            $search = str_replace(['=', 'and', 'or', '&', '|', '>', '<', ',', ';', ":", 'delete', 'insert', 'update', 'select', '*', 'user_id', ''], '', htmlspecialchars(stripslashes(strtolower($search))));
            $sql_search = ' AND ( transaction_authority LIKE "%' . $search . '%" OR transaction_tracking_code LIKE "%' . $search . '%" OR transaction_amount LIKE "%' . $search . '%" OR card_bank LIKE "%' . $search . '%" ) ';
        }
        if ($status == 'all') {
            $sql = "select * from `tbl_transactions`
            LEFT JOIN tbl_currency ON tbl_currency.currency_id=tbl_transactions.currency_id
            LEFT JOIN tbl_bank_card ON tbl_bank_card.card_id=tbl_transactions.card_id
            where tbl_transactions.user_id = :userId AND tbl_transactions.currency_id=:currency " . $sql_search . "
            order by transaction_id desc";
            $params = [
                'userId' => $userId,
                'currency' => $currency,
            ];
        } elseif ($status == 'pending') {
            $sql = "select * from `tbl_transactions`
            LEFT JOIN tbl_currency ON tbl_currency.currency_id=tbl_transactions.currency_id
            LEFT JOIN tbl_bank_card ON tbl_bank_card.card_id=tbl_transactions.card_id
            where tbl_transactions.user_id AND tbl_transactions.currency_id=:currency 
              and transaction_status in ('pending') " . $sql_search . "
            order by transaction_id desc";
            $params = [
                'userId' => $userId,
                'currency' => $currency,
            ];
        } elseif ($status == 'completed') {
            $sql = "select * from `tbl_transactions`
            LEFT JOIN tbl_currency ON tbl_currency.currency_id=tbl_transactions.currency_id
            LEFT JOIN tbl_bank_card ON tbl_bank_card.card_id=tbl_transactions.card_id
            where tbl_transactions.user_id = :userId AND tbl_transactions.currency_id=:currency 
              and transaction_status in ('completed') " . $sql_search . "
            order by transaction_id desc";
            $params = [
                'userId' => $userId,
                'currency' => $currency,
            ];
        } elseif ($status == 'rejected') {
            $sql = "select * from `tbl_transactions`
            LEFT JOIN tbl_currency ON tbl_currency.currency_id=tbl_transactions.currency_id
            LEFT JOIN tbl_bank_card ON tbl_bank_card.card_id=tbl_transactions.card_id
            where tbl_transactions.user_id = :userId AND tbl_transactions.currency_id=:currency 
              and transaction_status in ('rejected') " . $sql_search . "
            order by transaction_id desc";
            $params = [
                'userId' => $userId,
                'currency' => $currency,
            ];
        } elseif ($status == 'paid') {
            $sql = "select * from `tbl_transactions`
            LEFT JOIN tbl_currency ON tbl_currency.currency_id=tbl_transactions.currency_id
            LEFT JOIN tbl_bank_card ON tbl_bank_card.card_id=tbl_transactions.card_id
            where tbl_transactions.user_id = :userId AND tbl_transactions.currency_id=:currency 
              and transaction_status in ('paid') " . $sql_search . "
            order by transaction_id desc";
            $params = [
                'userId' => $userId,
                'currency' => $currency,
            ];
        } elseif ($status == 'unpaid') {
            $sql = "select * from `tbl_transactions`
            LEFT JOIN tbl_currency ON tbl_currency.currency_id=tbl_transactions.currency_id
            LEFT JOIN tbl_bank_card ON tbl_bank_card.card_id=tbl_transactions.card_id
            where tbl_transactions.user_id = :userId AND tbl_transactions.currency_id=:currency 
              and transaction_status in ('unpaid','expired') " . $sql_search . "
            order by transaction_id desc";
            $params = [
                'userId' => $userId,
                'currency' => $currency,
            ];
        } elseif ($status == 'pending_deposit') {
            $sql = "select * from `tbl_transactions`
            LEFT JOIN tbl_currency ON tbl_currency.currency_id=tbl_transactions.currency_id
            LEFT JOIN tbl_bank_card ON tbl_bank_card.card_id=tbl_transactions.card_id
            where tbl_transactions.user_id = :userId AND tbl_transactions.currency_id=:currency 
              and transaction_status in ('pending_deposit') " . $sql_search . "
            order by transaction_id desc";
            $params = [
                'userId' => $userId,
                'currency' => $currency,
            ];
        } elseif ($status == 'rejected_deposit') {
            $sql = "select * from `tbl_transactions`
            LEFT JOIN tbl_currency ON tbl_currency.currency_id=tbl_transactions.currency_id
            LEFT JOIN tbl_bank_card ON tbl_bank_card.card_id=tbl_transactions.card_id
            where tbl_transactions.user_id = :userId AND tbl_transactions.currency_id=:currency 
              and transaction_status in ('rejected_deposit') " . $sql_search . "
            order by transaction_id desc";
            $params = [
                'userId' => $userId,
                'currency' => $currency,
            ];
        } else {
            $sql = "select * from `tbl_transactions`
            LEFT JOIN tbl_currency ON tbl_currency.currency_id=tbl_transactions.currency_id
            LEFT JOIN tbl_bank_card ON tbl_bank_card.card_id=tbl_transactions.card_id
            where tbl_transactions.user_id = :userId AND tbl_transactions.currency_id=:currency " . $sql_search . "
            order by transaction_id desc";
            $params = [
                'userId' => $userId,
                'currency' => $currency,
            ];
        }
        $result = DB::rawQuery($sql, $params);
        if ($result->status == 200) {
            $transactionList = [];
            foreach ($result->response as $item) {
                $tx = new stdClass();
                $tx->TransactionId = $item->transaction_id;
                $tx->TransactionAuthority = $item->transaction_authority;
                $tx->TransactionTrackingCode = $item->transaction_tracking_code;
                $tx->TransactionAmount = number_format($item->transaction_amount);
                $tx->TransactionType = $item->transaction_type;
                $tx->TransactionGateway = $item->transaction_gateway;
                $tx->TransactionDepositType = $item->transaction_deposit_type;
                $tx->TransactionReceipt = Utils::fileExist($item->transaction_receipt, BOX_EMPTY);
                $tx->TransactionCardId = $item->card_id;
                $tx->TransactionCurrencyId = $item->currency_id;
                $tx->TransactionCurrency = array_column(json_decode($item->currency_name, true), 'value', 'slug')[$language];
                $tx->TransactionDestination = (!empty($item->card_id)) ? $item->card_bank : '';
                $tx->TransactionCardNumber = (!empty($item->card_id)) ? $item->card_number : '';
                $tx->TransactionStatus = $item->transaction_status;
                $tx->TransactionTime = $item->transaction_date;
                $tx->TransactionTime1 = ($language == 'fa_IR') ? Utils::jDate('h:i:s', $item->transaction_date) : date('h:i:s', $item->transaction_date);
                $tx->TransactionTime2 = ($language == 'fa_IR') ? Utils::jDate('Y-m-d', $item->transaction_date) : date('Y-m-d', $item->transaction_date);

                if (in_array($item->transaction_status, ['paid', 'completed'])) {
                    $tx->TransactionColor = 'green';
                } elseif (in_array($item->transaction_status, ['unpaid', 'rejected', 'rejected_deposit'])) {
                    $tx->TransactionColor = 'red';
                } else {
                    $tx->TransactionColor = 'yellow';
                }


                array_push($transactionList, $tx);
            }

            $final_out_put = [];
            $final_out_put2 = [];
            foreach ($transactionList as $item) {
                if (in_array($item->TransactionId, $final_out_put)) {

                } else {
                    array_push($final_out_put, $item->TransactionId);
                    array_push($final_out_put2, $item);
                }
            }


            $response = sendResponse(200, $status, $final_out_put2);
        }
        return $response;
    }


    public static function backToWalletByAdmin($transactionId, $posterID)
    {

        $response = sendResponse(200, 'error', []);

        $sql = "SELECT * FROM `tbl_transactions`  WHERE  `transaction_id`=:transaction_id ";
        $params = [
            'transaction_id' => $transactionId
        ];
        $result = DB::rawQuery($sql, $params);

        if ($result->status == 200) {
            $data = $result->response[0];
            $sqlInsert = "insert into `tbl_transactions` (`user_id`,  `currency_id`, `transaction_amount`,
                             `transaction_type`, `transaction_deposit_type`, `transaction_status`,
                             `transaction_date`,`transaction_detail`,`transaction_kind`) 
                        values (:userId, :currency_id, :amount, :type, :deposit, :status, :time,:detail,:kind) ;
                        UPDATE `tbl_balance` SET  balance_value = balance_value + :amount
                        WHERE `user_id`=:userId AND `currency_id`=:currency_id";
            $paramsInsert = [
                'userId' => intval($data->user_id),
                'currency_id' => intval($data->currency_id),
                'amount' => $data->transaction_amount,
                'type' => 'deposit',
                'deposit' => 'online',
                'status' => 'paid',
                'time' => time(),
                'detail' => 'بازگشت وجه در قبال عدم ارسال کارشناس برای آگهی با ای دی' . $posterID,
                'kind' => 'poster',
            ];
            $resultInsert = DB::transactionQuery($sqlInsert, $paramsInsert);
            if ($resultInsert->status == 200) {
                $response = sendResponse(200, 'Deposit successfully');
            } else {
                $response = sendResponse(-10, 'Error');
            }
            return $response;
        }
        return $response;

    }


    public static function transactionPoster($userID, $currencyID, $amount, $detail = null)
    {
        $response = sendResponse(200, 'error', []);
        $sql = "insert into `tbl_transactions` (`user_id`,  `currency_id`, `transaction_amount`,
                             `transaction_type`, `transaction_status`,
                             `transaction_date`,`transaction_detail`,`transaction_kind`) 
                        values (:userId, :currency_id, :amount, :type, :status, :time,:detail,:kind) ;";
        $params = [
            'userId' => $userID,
            'currency_id' => $currencyID,
            'amount' => $amount,
            'type' => 'withdraw',
            'status' => 'completed',
            'time' => time(),
            'detail' => $detail,
            'kind' => 'poster',
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful", $result->response);
        }
        return $response;
    }


    public static function deleteTransaction($transactionId)
    {
        $response = sendResponse(200, 'error', []);
        $sql = "DELETE FROM `tbl_transactions` WHERE transaction_id=:transaction_id ;";
        $params = [
            'transaction_id' => $transactionId
        ];
        $result = DB::delete($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, "Successful");
        }
        return $response;
    }

    /**
     * @param $userId
     * @param $authority
     * @param $amount
     * @param $currency
     * @param $receipt
     * @param $token
     *
     * @return stdClass
     */
    public static function depositWithReceipt($userId, $authority, $amount, $currency, $receipt, $token)
    {
        if (!Security::verifyCSRF('deposit', $token)) {
            return sendResponse(-1, 'CSRF-Token error');
        }
        $csrf = Security::initCSRF('deposit');

        $sql = "insert into tbl_transactions(`user_id`,  `currency_id`,`transaction_authority`, `transaction_amount`,
                             `transaction_type`, `transaction_deposit_type`,`transaction_receipt`, `transaction_status`, `transaction_date`) 
        values (:userId, :currency_id,:authority, :amount, :type, :deposit,:receipt, :status, :time);";
        $params = [
            'userId' => $userId,
            'currency_id' => $currency,
            'authority' => $authority,
            'amount' => $amount,
            'type' => 'deposit',
            'deposit' => 'receipt',
            'receipt' => $receipt,
            'status' => 'pending_deposit',
            'time' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Deposit successfully');
            User::createUserLog($userId, 'uLog_deposit_receipt', 'wallet');
        } else {
            $response = sendResponse(-10, 'Error', $csrf);
        }
        return $response;
    }

    /**
     * @param $userId
     * @param $authority
     * @param $amount
     * @param $currency
     * @param $receipt
     * @param $token
     *
     * @return stdClass
     */
    public static function depositOnline($userId, $authority, $amount, $currency, $status = "pending_deposit")
    {


        $sql = "insert into tbl_transactions(`user_id`,  `currency_id`,`transaction_authority`, `transaction_amount`,
                             `transaction_type`, `transaction_deposit_type`, `transaction_status`, `transaction_date`) 
        values (:userId, :currency_id,:authority, :amount, :type, :deposit, :status, :time);";
        $params = [
            'userId' => $userId,
            'currency_id' => $currency,
            'authority' => $authority,
            'amount' => $amount,
            'type' => 'deposit',
            'deposit' => 'online',
            'status' => $status,
            'time' => time(),
        ];
        $result = DB::insert($sql, $params);
        if ($result->status == 200) {
            $response = sendResponse(200, 'Deposit successfully');
            User::createUserLog($userId, 'uLog_deposit_receipt', 'online');
        } else {
            $response = sendResponse(-10, 'Error');
        }
        return $response;
    }

    public static function getTransactionsUserIdByRefNumber($ID)
    {
        $sql = "select  *  from tbl_transactions   where tbl_transactions.transaction_authority  = :ref_number";
        $params = [
            'ref_number' => $ID
        ];
        $result = DB::rawQuery($sql, $params);
        return $result->response[0]->user_id;
    }

    public static function getOnlinePendingTransaction()
    {
        $sql = "SELECT * FROM `tbl_transactions` WHERE tbl_transactions.transaction_deposit_type = 'online' and tbl_transactions.transaction_status  = 'pending_deposit'";
        $params = [];
        $result = DB::rawQuery($sql, $params);
        return $result->response;
    }

    public static function expireTransaction($tx_id)
    {
        $sql = "  update tbl_transactions set tbl_transactions.transaction_status = 'rejected_deposit' where tbl_transactions.transaction_id = :tx_id";

        $params = [
            'tx_id' => $tx_id,

        ];
        $result = DB::update($sql, $params);
        if ($result->status == 200 || $result->status == 208) {
            return 'success';
        } else {
            return 'failed';
        }
    }

    /**
     * @param $userId
     * @param $authority
     * @param $amount
     * @param $currency
     * @param $receipt
     * @param $token
     *
     * @return stdClass
     */
    public static function updateDepositOnline($ref_number, $amount, $status)
    {
        if ($status == "paid") {
            $currency_id = 1;
            $sql = " update tbl_transactions set  tbl_transactions.transaction_status =  :status  where tbl_transactions.transaction_authority  = :ref_number;
        UPDATE tbl_balance SET `balance_value`=balance_value + :amount WHERE user_id = :user_id AND currency_id=:currency_id";
            $user_id = self::getTransactionsUserIdByRefNumber($ref_number);
            $params = [
                'ref_number' => $ref_number,
                'amount' => $amount,
                'status' => $status,
                'user_id' => $user_id,
                'currency_id' => $currency_id
            ];
            $result = DB::update($sql, $params);
            if ($result->status == 200) {
                $response = sendResponse(200, 'Deposit successfully');

            } elseif ($result->status == 208) {
                $sql = " update tbl_transactions set  tbl_transactions.transaction_status =  :status  where tbl_transactions.transaction_authority  = :ref_number;
        UPDATE tbl_balance SET `balance_value`=balance_value - :amount WHERE user_id = :user_id AND currency_id=:currency_id";
                $user_id = self::getTransactionsUserIdByRefNumber($ref_number);
                $params = [
                    'ref_number' => $ref_number,
                    'amount' => $amount,
                    'status' => $status,
                    'user_id' => $user_id,
                    'currency_id' => $currency_id
                ];
                $result = DB::update($sql, $params);
                $response = sendResponse(200, 'Deposit repetitive');
            } else {
                $response = sendResponse(-10, 'Error1', $result);
            }
            return $response;
        } else {

            $sql = " update tbl_transactions set  tbl_transactions.transaction_status =  :status  where tbl_transactions.transaction_authority  = :ref_number;";

            $params = [
                'ref_number' => $ref_number,
                'status' => $status,
            ];
            $result = DB::update($sql, $params);
            if ($result->status == 200 || $result->status == 208) {
                $response = sendResponse(200, 'update successfully');

            } else {
                $response = sendResponse(-10, 'Error2');
            }
            return $response;
        }

    }


}