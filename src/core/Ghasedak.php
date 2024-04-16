<?php


namespace MJ\SMS;


use Ghasedak\Exceptions\ApiException;
use Ghasedak\Exceptions\HttpException;
use Ghasedak\GhasedakApi;
use MJ\Utils\Utils;
use stdClass;
use function MJ\Keys\sendResponse;


class Ghasedak
{
    /**
     * @param $receptor
     * @param $otp
     *
     * @return stdClass
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function sendOTP($receptor, $otp)
    {
        try {
            $api = new GhasedakApi(Utils::getFileValue('settings.txt', 'ghasedak_api'));
            $result = $api->Verify($receptor, 1, 'otp', $otp);
            if ($result->result->code == 200) {
                $response = sendResponse(200, 'OTP sent successfully');
            } else {
                $response = sendResponse(-10, '');
            }
        } catch (ApiException $e) {
            $response = sendResponse(-1, '');
        } catch (HttpException $e) {
            $response = sendResponse(-1, '');
        }
        return $response;
    }


    /**
     * @return stdClass
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function getCredit()
    {
        try {
            $api = new GhasedakApi(Utils::getFileValue('settings.txt', 'ghasedak_api'));
            $result = $api->AccountInfo();
            if ($result->result->code == 200) {
                $response = sendResponse(200, 'Successful', $result->items->balance);
            } else {
                $response = sendResponse(-10, '');
            }
        } catch (ApiException $e) {
            $response = sendResponse(-1, '');
        } catch (HttpException $e) {
            $response = sendResponse(-1, '');
        }
        return $response;
    }


    /**
     * @return stdClass
     * @author  Tjavan
     * @version 1.0.0
     */
    public static function getCreditCron($apii)
    {
        try {
            $api = new GhasedakApi($apii);
            $result = $api->AccountInfo();
            if ($result->result->code == 200) {
                $response = sendResponse(200, 'Successful', $result->items->balance);
            } else {
                $response = sendResponse(-10, '');
            }
        } catch (ApiException $e) {
            $response = sendResponse(-1, '');
        } catch (HttpException $e) {
            $response = sendResponse(-1, '');
        }
        return $response;
    }


    public static function sendSMS($mobiles, $message )
    {
        $api = new GhasedakApi(Utils::getFileValue('settings.txt', 'ghasedak_api'));


        $response ='';
        try {
            $lineNumber = "90006923";

            foreach ($mobiles as $loop) {
                $result =   $api->SendSimple($loop, $message, $lineNumber);
            }
        } catch (\Ghasedak\Exceptions\ApiException $e) {
            $result= $e->errorMessage();
        } catch (\Ghasedak\Exceptions\HttpException $e) {
            $result = $e->errorMessage();
        }

        if ($result->result->code == 200) {
            $response = sendResponse(200, 'Sent successfully');
        } else {
            $response = sendResponse(-10, 'Sent Error');
        }

        return $response;
    }
}