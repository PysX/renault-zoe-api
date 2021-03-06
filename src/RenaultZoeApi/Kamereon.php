<?php

namespace RenaultZoeApi;

/**
 * Class for Giya auth platform
 */
class Kamereon
{
    // TODO should be retrieved from a local parameter
    private static $strRootUrl = "https://api-wired-prod-1-euw1.wrd-aws.com";
    private static $strApiKey = "oF09WnKqvBDcrQzcW1rJNpjIuy7KdGaB";
    
    /**
     * Get accounts from Kamereon with Giya tokens. Save tokens in a file
     *
     * @param  array $arrGiyaTokens
     * @return array with all tokens or null
     */
    public static function getAccounts($arrGiyaTokens)
    {
        $objClient = new \GuzzleHttp\Client(
            [
            'headers' => [
                'Content-Type' => 'application/json',
                'apikey' => self::$strApiKey,
                'x-gigya-id_token' => $arrGiyaTokens['GiyaIdToken']
                ]
            ]
        );

        $strUrl = self::$strRootUrl . '/commerce/v1/persons/' . $arrGiyaTokens['GiyaPersonId'] . '?country=GB';
        $objRes = $objClient->get($strUrl);
        $strResult = $objRes->getBody()->getContents();
        // TODO : LOG
        $objJsonRes = json_decode($strResult);

        if ($objJsonRes->{'accounts'} == null) {
            // TODO : LOG
            return null;
        } else {
            // TODO : LOG
            $arrAccounts = $objJsonRes->{'accounts'};
            // TODO : LOG
            // TODO : ADD possibility for multiple accounts
            $strAccountId = $arrAccounts[0]->{'accountId'};

            // Kamereon token no more needed: no more "getToken" function
            // save tokens in file
            $arrGiyaTokens['accountId'] = $strAccountId;
            return self::saveTokens($arrGiyaTokens);
        }
    }

    /**
     * Save tokens in file
     *
     * @param  array  $arrGiyaTokens
     * @return array
     */
    public static function saveTokens($arrGiyaTokens)
    {
        // save tokens
        $strFile = dirname(__FILE__) . '/../../data/credentials.json';
        // TODO : LOG
        file_put_contents($strFile, json_encode($arrGiyaTokens, JSON_PRETTY_PRINT));
            
        return $arrGiyaTokens;
    }

    /**
     * Generic call to API
     *
     * @param  string $strUrl
     * @param  array  $arrGiyaTokens
     * @return string
     */
    private static function get($strUrl, $arrGiyaTokens)
    {
        $arrHeaders = [
            'apikey' => self::$strApiKey,
            'x-gigya-id_token' => $arrGiyaTokens['GiyaIdToken']
        ];

        $objClient = new \GuzzleHttp\Client(['headers' => $arrHeaders]);
        try {
            $objRes = $objClient->get($strUrl);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return $strUrl . " " . $e->getResponse()->getBody()->getContents();
        }

        // TODO : LOG
        return $objRes->getBody()->getContents();
    }

    /**
     * Get full infomations about vehicles
     *
     * @param  array $arrTokens
     * @return string
     */
    public static function getVehicles($arrTokens)
    {
        $strUrl = self::$strRootUrl . '/commerce/v1/accounts/' . $arrTokens['accountId'] . '/vehicles?country=GB';
        return self::get($strUrl, $arrTokens);
    }

    /**
     * Generic call api for infos about a vehicle
     *
     * @param  array   $arrTokens
     * @param  string  $strVin
     * @param  string  $strEndpoint
     * @param  array   $arrGiyaTokens
     * @param  integer $intVersion
     * @return string
     */
    private static function getInfo($arrTokens, $strVin, $strEndpoint, $intVersion = 1)
    {
        $strUrl = self::$strRootUrl . '/commerce/v1/accounts/'
                . $arrTokens['accountId']
                . '/kamereon/kca/car-adapter/v'
                . $intVersion . '/cars/'
                . $strVin . '/' . $strEndpoint .
                (strstr($strEndpoint, '?') ? '&' : '?') . 'country=FR';
        return self::get($strUrl, $arrTokens);
    }
    
    /**
     * Get battery status
     *
     * @param  string $strVin
     * @param  array  $arrTokens
     * @return string
     */
    public static function getBattery($strVin, $arrTokens)
    {
        return self::getInfo($arrTokens, $strVin, 'battery-status', 2);
    }
    
    /**
     * Get Cockpit
     *
     * @param  string $strVin
     * @param  array  $arrTokens
     * @return string
     */
    public static function getCockpit($strVin, $arrTokens)
    {
        return self::getInfo($arrTokens, $strVin, 'cockpit');
    }

    /**
     * Get Location
     * Only for ZE50
     *
     * @param  string $strVin
     * @param  array  $arrTokens
     * @return string
     */
    public static function getLocation($strVin, $arrTokens)
    {
        return self::getInfo($arrTokens, $strVin, 'location');
    }
    
    /**
     * Get Charging settings
     *
     * @param  string $strVin
     * @param  array  $arrTokens
     * @return string
     */
    public static function getChargingSettings($strVin, $arrTokens)
    {
        return self::getInfo($arrTokens, $strVin, 'charging-settings');
    }
    
    /**
     * Get Charge Mode
     *
     * @param  string $strVin
     * @param  array  $arrTokens
     * @return string
     */
    public static function getChargeMode($strVin, $arrTokens)
    {
        return self::getInfo($arrTokens, $strVin, 'charge-mode');
    }

    /**
      * Get HVAC Status
      * Not implemented server side
      *
      * @param  string $strVin
      * @param  array  $arrTokens
      * @return string
      */
    public static function getHvacStatus($strVin, $arrTokens)
    {
        return self::getInfo($arrTokens, $strVin, 'hvac-status');
    }

    /**
     * Get HVAC history
     * Not implemented server side
     *
     * @param  string $strVin
     * @param array $arrParams type = day or month, date start and end format YYYYMMDD
     * @param  array  $arrTokens
     * @return string
     */
    public static function getHvacHistory($strVin, $arrParams, $arrTokens)
    {
        return self::getInfo(
            $arrTokens,
            $strVin,
            'hvac-history?type=' . $arrParams['type'] . '&start=' . $arrParams['start'] . '&end=' . $arrParams['end']
        );
    }

    /**
     * Get HVAC sessions
     * Not implemented server side
     *
     * @param  string $strVin
     * @param array $arrParams date start and end format YYYYMMDD
     * @param  array  $arrTokens
     * @return string
     */
    public static function getHvacSessions($strVin, $arrParams, $arrTokens)
    {
        return self::getInfo(
            $arrTokens,
            $strVin,
            'hvac-sessions?start=' . $arrParams['start'] . '&end=' . $arrParams['end']
        );
    }



    /**
     * Get Notification settings
     * Not implemented server side
     *
     * @param  string $strVin
     * @param  array  $arrTokens
     * @return string
     */
    public static function getNotificationSettings($strVin, $arrTokens)
    {
        return self::getInfo($arrTokens, $strVin, 'notification-settings');
    }

    /**
     * Get Lock Status
     * Not implemented server side
     *
     * @param  string $strVin
     * @param  array  $arrTokens
     * @return string
     */
    public static function getLockStatus($strVin, $arrTokens)
    {
        return self::getInfo($arrTokens, $strVin, 'lock-status');
    }
}
