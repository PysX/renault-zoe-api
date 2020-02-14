<?php

namespace RenaultZoeApi;


class Kamereon
{
    private static $strRootUrl = "https://api-wired-prod-1-euw1.wrd-aws.com";
    private static $strApiKey = "oF09WnKqvBDcrQzcW1rJNpjIuy7KdGaB";
    
    public static function getAccounts($arrGiyaTokens) {

        $objClient = new \GuzzleHttp\Client([
            'headers' => [ 
                'Content-Type' => 'application/json',
                'apikey' => self::$strApiKey,
                'x-gigya-id_token' => $arrGiyaTokens['GiyaIdToken']
                ]
        ]);

        $strUrl = self::$strRootUrl .'/commerce/v1/persons/'.$arrGiyaTokens['GiyaPersonId'].'?country=GB';
        $objRes = $objClient->get($strUrl);
        $strResult = $objRes->getBody()->getContents();
        ////log::add('zoe', 'debug', 'Kamereon response: ' . $strResult);
        $objJsonRes = json_decode($strResult);

        if($objJsonRes->{'accounts'} == null) {
            ////log::add('zoe', 'debug', 'Kamereon : error retrieving id token');
            return null;
        } else {
            ////log::add('zoe', 'debug', 'Kamereon : accounts success');
            $arrAccounts = $objJsonRes->{'accounts'};
            ////log::add('zoe', 'debug', 'Kamereon : Accounts = '. $arrAccounts);
            $strAccountId = $arrAccounts[0]->{'accountId'};

            self::getToken($strAccountId,$arrGiyaTokens);
            //return ['GiyaIdToken' => $strIdToken, 'GiyaIdTokenTime' => $objJsonRes->{'time'}];
        }
    }

    // TODO supprimer arrgiyatokens à passer à chaque fois
    public static function getToken($strAccountId,$arrGiyaTokens,$returnTokenOnly = false) {
        $objClient = new \GuzzleHttp\Client([
            'headers' => [ 
                'Content-Type' => 'application/json',
                'apikey' => self::$strApiKey,
                'x-gigya-id_token' => $arrGiyaTokens['GiyaIdToken']
                ]
        ]);

        $strUrl = self::$strRootUrl .'/commerce/v1/accounts/'.$strAccountId.'/kamereon/token?country=GB';
        $objRes = $objClient->get($strUrl);
        $strResult = $objRes->getBody()->getContents();
        ////log::add('zoe', 'debug', 'Kamereon response: ' . $strResult);
        $objJsonRes = json_decode($strResult);

        if($objJsonRes->{'accessToken'} == null) {
            ////log::add('zoe', 'debug', 'Kamereon : error retrieving accessToken');
            return null;
        } else {
            ////log::add('zoe', 'debug', 'Kamereon : accessToken success');
            $strAccessToken = $objJsonRes->{'accessToken'};
            ////log::add('zoe', 'debug', 'Kamereon : accessToken = '. $strAccessToken);

            
            $arrGiyaTokens['accountId'] = $strAccountId;
            $arrGiyaTokens['kamereon-authorization'] = $objJsonRes->{'accessToken'};          
            $arrTokens = $arrGiyaTokens;

            // save tokens
            $strFile = dirname(__FILE__) . '/../../data/credentials.json';
            file_put_contents($strFile, json_encode($arrTokens, JSON_PRETTY_PRINT));

            
            if($returnTokenOnly) {
                return $objJsonRes->{'accessToken'};
            }         
            
            
            self::getVehicules($strAccountId,$strAccessToken,$arrGiyaTokens);
       }
    }


    private static function getVehicules($strAccountId,$strAccessToken,$arrGiyaTokens) {
        $arrHeaders = [ 
            'apikey' => self::$strApiKey,
            'x-gigya-id_token' => $arrGiyaTokens['GiyaIdToken'],
            'x-kamereon-authorization' => 'Bearer '.$strAccessToken
        ];

        $strUrl = self::$strRootUrl .'/commerce/v1/accounts/'.$strAccountId.'/vehicles?country=GB';

        $objJsonRes = self::_get($strUrl, $strAccessToken,$arrGiyaTokens);
        //var_dump($objJsonRes);
        
        self::getInfo($strAccountId,'VF1AG000664302909','battery-status',$strAccessToken,$arrGiyaTokens,2);
        
        //self::getInfo($strAccountId,'VF1AG000664302909','location',$strAccessToken,$arrGiyaTokens);
        
        // not implemented : self::getInfo($strAccountId,'VF1AG000664302909','notification-settings',$strAccessToken,$arrGiyaTokens);
        
        //self::getInf2($strAccountId,'VF1AG000664302909','charge-history?type=day&start=20200101&end=20200201',$strAccessToken,$arrGiyaTokens);
    }

    private static function _get($strUrl, $strAccessToken = null,$arrGiyaTokens) {
        $arrHeaders = [ 
            'apikey' => self::$strApiKey,
            'x-gigya-id_token' => $arrGiyaTokens['GiyaIdToken']            
        ];
        if($strAccessToken != null) {
            $arrHeaders = array_merge($arrHeaders, ['x-kamereon-authorization' => 'Bearer '.$strAccessToken]);
        }

        $objClient = new \GuzzleHttp\Client(['headers' => $arrHeaders]);
        $objRes = $objClient->get($strUrl);
        $strResult = $objRes->getBody()->getContents();
        ////log::add('zoe', 'debug', 'Kamereon response: ' . $strResult);

        return json_decode($strResult);
    }
    
    
    public static function getInfo($strAccountId,$strVin,$strEndpoint,$strAccessToken,$arrGiyaTokens,$intVersion=1) {
        $strUrl = self::$strRootUrl.'/commerce/v1/accounts/'.$strAccountId.'/kamereon/kca/car-adapter/v'.$intVersion.'/cars/'.$strVin.'/'.$strEndpoint.'?country=FR';
        $res = self::_get($strUrl,$strAccessToken,$arrGiyaTokens);
        
        var_dump($res);
    }
    public static function getInf2($strAccountId,$strVin,$strEndpoint,$strAccessToken,$arrGiyaTokens,$intVersion=1) {
        $strUrl = self::$strRootUrl.'/commerce/v1/accounts/'.$strAccountId.'/kamereon/kca/car-adapter/v'.$intVersion.'/cars/'.$strVin.'/'.$strEndpoint.'&country=FR';
        $res = self::_get($strUrl,$strAccessToken,$arrGiyaTokens);
        
        var_dump($res);
    }
    
    public static function getBattery($strVin,$arrTokens) {
        self::getInfo($arrTokens['accountId'],$strVin,'battery-status',$arrTokens['kamereon-authorization'],$arrTokens,2);
    }
    
     public static function getCockpit($strVin,$arrTokens) {
        self::getInfo($arrTokens['accountId'],$strVin,'cockpit',$arrTokens['kamereon-authorization'],$arrTokens);
    }
    
    
    
    
}