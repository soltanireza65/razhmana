<?php
require_once 'autoload.php';


$transactions  =  Transactions::getOnlinePendingTransaction() ;
 foreach ($transactions as $transaction){
     if($transaction->transaction_date <  (time()  - 1800)){
        $result  =  Transactions::expireTransaction($transaction->transaction_id);
         $output = [
             'transaction_date' => $transaction->transaction_date,
             'run_time' => time() ,
             'transaction_id' => $transaction->transaction_id,
             'result' => $result
         ];
        if($result == 'success'){
            file_put_contents( 'success-expire.json', json_encode($output) , FILE_APPEND);
        }else{
            file_put_contents('failed-expire.json',json_encode($output), FILE_APPEND);

        }
     }

 }
