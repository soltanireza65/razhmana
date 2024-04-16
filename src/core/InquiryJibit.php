<?php

class InquiryJibit
{
    private static function generateToken()
    {
        $url = "https://napi.jibit.ir/ide/v1/tokens/generate";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $headers = array(
            "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $data = '{"apiKey": "ktFTF8zTTR", "secretKey": "uqHA5V1KZ9X_eQ0tzdL0PESDO"}';
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return json_decode($resp);
    }
    public static function convertAccountNumberToIBAN( $bank , $accountNumber)
    {
        $url = "https://napi.jibit.ir/ide/v1/deposits?bank=$bank&number=$accountNumber&iban=true";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }
    // شبا
    public static function inquiryIBAN($IBAN)
    {
        ///v1/ibans?value=IR1234567891012345678910
        $url = "https://napi.jibit.ir/ide/v1/ibans?value=$IBAN";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }

    // شماره کارت
    public static function inquiryCardInfo($cardNumber  )
    {
        $url = "https://napi.jibit.ir/ide/v1/cards?number=$cardNumber";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }

    // کارت به حساب
    public static function convertCardToAccount($cardNumber  )
    {
        $url = "https://napi.jibit.ir/ide/v1/cards?number=$cardNumber&deposit=true";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }

    // کارت به شبا
    public static function convertCardToIBAN($cardNumber  )
    {
        $url = "https://napi.jibit.ir/ide/v1/cards?number=$cardNumber&iban=true";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }

    // بررسی آنلاین بودن سرویس استعلام
    public static function chackIBANConverrtIsOnline(  )
    {
        $url = "https://napi.jibit.ir/ide/v1/services/availability?cardToIBAN=true";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }


    public static function matchIbanAndNationalCode( $IBAN , $nationalCode , $birthDate  )
    {
        $url = "https://napi.jibit.ir/ide/v1/services/matching?iban=$IBAN&nationalCode=$nationalCode&birthDate=$birthDate";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }
    public static function matchIbanAndName( $IBAN , $firstName , $lastName  )
    {
        $url = "https://napi.jibit.ir/ide//v1/services/matching?iban=$IBAN&name=$firstName $lastName";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }
    public static function matchCardAndName( $card , $firstName , $lastName  )
    {
        $url = "https://napi.jibit.ir/ide/v1/services/matching?cardNumber=$card&name=$firstName $lastName";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }
    public static function matchAccountAndName( $card , $firstName , $lastName , $bank  )
    {
        $url = "https://napi.jibit.ir/ide/v1/services/matching?bank=$bank&depositNumber=$card&name=$firstName $lastName";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }
    public static function matchCardAndNationalCode( $card , $nationalCode , $birthDate  )
    {
        $url = "https://napi.jibit.ir/ide/v1/services/matching?cardNumber=$card&nationalCode=$nationalCode&birthDate=$birthDate";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }
    public static function matchPhoneAndNationalCode( $phone, $nationalCode   )
    {
        $url = "https://napi.jibit.ir/ide/v1/services/matching?nationalCode=$nationalCode&mobileNumber=$phone";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }
    public static function matchPostalCode( $postalCode   )
    {
        $url = "https://napi.jibit.ir/ide/v1/services/postal?code=$postalCode";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }
    public static function matchSimilarityInfo( $nationalCode , $birthDate , $firstName , $lastName , $fullName , $fatherName   )
    {
        $url = "https://napi.jibit.ir/ide/v1/services/identity/similarity?nationalCode=$nationalCode&birthDate=$birthDate&firstName=$firstName&lastName=$lastName&fullName=$fullName&fatherName=$fatherName";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }
    public static function getBalance()
    {
        $url = "https://napi.jibit.ir/ide/v1/balances";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }
    public static function getDailyUsage($date)
    {
        $url = "https://napi.jibit.ir/ide/v1/reports/daily?yearMonthDay=$date";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $accessToken  = self::generateToken()->accessToken ;
        $headers = array(
            "Accept: application/json",
            "Authorization: Bearer {$accessToken}",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return $resp ;
    }
}