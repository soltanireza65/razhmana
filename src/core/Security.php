<?php


namespace MJ\Security;


use MJ\Keys\KEYS;
use Exception;
use stdClass;
use function MJ\Keys\sendResponse;


require_once $_SERVER['DOCUMENT_ROOT'] . '/core/KEYS.php';


/**
 * Class Security
 * @package MJ\Security
 */
class Security
{
    /**
     * @var string
     */
    private static $ENC_METHOD;


    /**
     * @var string
     */
    private static $SECRET_IV;


    /**
     *
     * @author Tjavan
     * @version 1.0.0
     */
    private static function configEncryption()
    {
        self::$ENC_METHOD = 'AES-256-CBC';
        self::$SECRET_IV = substr(hash('sha256', '!IV@_$99'), 0, 16);
    }


    /**
     *
     * @param mixed $value
     * @param string $key
     * @return string
     * @author Tjavan
     * @version 1.0.0
     */
    public static function encrypt($value, $key = '')
    {
        self::configEncryption();;
        if (!empty($key)) {
            return base64_encode(openssl_encrypt($value, self::$ENC_METHOD, $key, 0, self::$SECRET_IV));
        }

        return base64_encode(openssl_encrypt($value, self::$ENC_METHOD, KEYS::$encryptionKey, 0, self::$SECRET_IV));
    }


    /**
     *
     * @param mixed $value
     * @param string $key
     * @return string
     * @author Tjavan
     * @version 1.0.0
     */
    public static function decrypt($value, $key = '')
    {
        self::configEncryption();;
        if (!empty($key)) {
            return openssl_decrypt(base64_decode($value), self::$ENC_METHOD, $key, 0, self::$SECRET_IV);
        }

        return openssl_decrypt(base64_decode($value), self::$ENC_METHOD, KEYS::$encryptionKey, 0, self::$SECRET_IV);
    }


    /**
     *
     * @param string $key
     * @return mixed
     * @author Tjavan
     * @version 1.0.0
     */
    public static function initCSRF($key)
    {
        if (!isset($_SESSION[$key])) {
            try {
                $_SESSION[$key] = bin2hex(random_bytes(32));
            } catch (Exception $e) {
            }
        }
        return $_SESSION[$key];
    }


    /**
     *
     * @param string $key
     * @param mixed $token
     * @return bool
     * @author Tjavan
     * @version 1.0.0
     */
    public static function verifyCSRF($key, $token, $unSet = true)
    {
        if (isset($_SESSION[$key])) {
            if ($token && $token === $_SESSION[$key]) {
                if ($unSet) {
                    unset($_SESSION[$key]);
                }
                return true;
            }
        }
        return false;
    }


    /**
     *
     * @param string $key
     * @return mixed
     * @author Tjavan
     * @version 2.0.0
     */
    public static function initCSRF2()
    {
        try {
            $token = bin2hex(random_bytes(32));
            $_SESSION[$token] = $token;
        } catch (Exception $e) {
            $token = null;
        }
        return $token;
    }


    /**
     *
     * @param string $key
     * @param mixed $token
     * @return bool
     * @author Tjavan
     * @version 2.0.0
     */
    public static function verifyCSRF2($token, $unSet = true)
    {
        if (isset($_SESSION[$token])) {
            if ($unSet) {
                unset($_SESSION[$token]);
            }
            return true;
        }
        return false;
    }


    /**
     *
     * @param string $password
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function passwordValidator($password)
    {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $length = strlen($password);

        if ($length < 8) {
            $response = sendResponse(0, "Password must be at least 8 characters.");
        } elseif (!$uppercase) {
            $response = sendResponse(0, "Password must contain one uppercase.");
        } elseif (!$lowercase) {
            $response = sendResponse(0, "Password must contain one lowercase.");
        } elseif (!$number) {
            $response = sendResponse(0, "Password must contain one number.");
        } else {
            $response = sendResponse(200, "All rules observed.");
        }

        return $response;
    }


    /**
     *
     * @param string $email
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function emailValidator($email)
    {
        $response = sendResponse(0, "Email address is invalid.");
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = sendResponse(200, "Email address is valid.");
        }
        return $response;
    }


    /**
     *
     * @param string $mobile
     * @return stdClass
     * @author Tjavan
     * @version 1.0.0
     */
    public static function phoneNumberValidator($mobile)
    {
        $response = sendResponse(0, "Phone number is invalid.");
        if (preg_match('/^09[0-9]{9}+$/', $mobile)) {
            $response = sendResponse(200, "Phone number is valid.");
        }
        return $response;
    }


    public static function check_json_for_script_tags($string)
    {
            return str_contains($string, '<script>')  ;
    }
}
