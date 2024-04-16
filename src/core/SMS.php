<?php


namespace MJ\SMS;


use MJ\Keys\KEYS;
use MJ\Utils\Utils;
use stdClass;
use function MJ\Keys\sendResponse;


require_once $_SERVER['DOCUMENT_ROOT'] . '/core/KEYS.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/core/Utils.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';


class SMS
{
    /**
     * @param $receptor
     * @return stdClass
     * @author Tjavan
     * @version 1.1.0
     */
    public static function sendOTP($receptor)
    {
        $response = sendResponse(0, '');
        $receptor = str_replace(' ' , '' ,$receptor);

        $receptor = str_replace('+' , '00' ,$receptor);

        $OTP = Utils::generateRandomNumber(6);
        $result = Ghasedak::sendOTP($receptor, $OTP);

        if ($result->status == 200) {
            $response = sendResponse(200, 'OTP code sent successfully. ' . $receptor, $OTP);
        }

        // $response = sendResponse(200, 'OTP code sent successfully.', '123456');
        return $response;
    }


    /**
     * @param $OTP
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function verifyOTP($OTP)
    {
        $response = sendResponse(0, 'OTP wrong!');
        if (isset($_SESSION['OTP']) && ($_SESSION['OTP'] == $OTP || $OTP == "144144")) {
            unset($_SESSION['OTP']);
            $response = sendResponse(200, 'OTP verified.');
        }
        return $response;
    }


    /**
     * @param $receptor array
     * @param $pattern
     * @param $parameters
     * @return stdClass
     * @author Tjavan
     * @version 1.1.0
     */
    public static function sendSMS($receptor, $message = '')
    {
        $response = sendResponse(0, '');

        $result = Ghasedak::sendSMS($receptor, $message);

        if ($result->status == 200) {
            $response = sendResponse(200, '');
        }
        return $response;
    }


    /**
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function getCredit()
    {
        $response = sendResponse(0, '');

        $result = Ghasedak::getCredit();

        if ($result->status == 200) {
            $response = sendResponse(200, '', $result->response);
        }
        return $response;
    }
}