<?php 

namespace RenaultZoeApi;


class Giya
{
   private static $strRootUrl = "https://accounts.eu1.gigya.com";
    private static $strApiKey = "3_e8d4g4SE_Fo8ahyHwwP7ohLGZ79HKNN2T8NjQqoNnk6Epj6ilyYwKdHUyCw3wuxz";

    /**
     * Login de l'utilisateur
     * Permet de récupérer le token puis dans un second temps le personId
     *
     * @param [type] $strUserName
     * @param [type] $strPassword
     * @return void
     */
    public static function login($strUserName, $strPassword) {

        $objClient = new \GuzzleHttp\Client([
            'headers' => [ 'Content-Type' => 'application/json' ]
        ]);
        $objRes = $objClient->post(self::$strRootUrl .'/accounts.login',
            ['form_params' => [
                'ApiKey'=> self::$strApiKey,
                'loginID'=> $strUserName,
                'password'=> $strPassword
            ]]
        );
        $strResult = $objRes->getBody()->getContents();
        ////log::add('zoe', 'debug', 'Giya response: ' . $strResult);
        $objJsonRes = json_decode($strResult);

        if($objJsonRes->{'statusCode'} != 200) {
            ////log::add('zoe', 'debug', 'Giya : login error');
            return 'KO';
        } else {
            ////log::add('zoe', 'debug', 'Giya : login success');
            $strToken = $objJsonRes->{'sessionInfo'}->{'cookieValue'};
            ////log::add('zoe', 'debug', 'Giya : token = '. $strToken);

            $arrTokens = self::getPersonId($strToken);

            if($arrTokens != null) {
                return array_merge(['GiyaToken' => $strToken], $arrTokens);
            } else {
                return null;
            }
        }
    }

/**
     * Permet de récupérer le personId après avoir récupére le token
     *
     * @param [type] $strGiyaToken
     * @return void
     */
    private static function getPersonId($strGiyaToken) {
        $objClient = new \GuzzleHttp\Client([
            'headers' => [ 'Content-Type' => 'application/json' ]
        ]);
        $objRes = $objClient->post(self::$strRootUrl .'/accounts.getAccountInfo',
            ['form_params' => [
                'oauth_token'=> $strGiyaToken
            ]]
        );
        $strResult = $objRes->getBody()->getContents();
        //log::add('zoe', 'debug', 'Giya response: ' . $strResult);
        $objJsonRes = json_decode($strResult);

        if($objJsonRes->{'statusCode'} != 200) {
            //log::add('zoe', 'debug', 'Giya : error retrieving personId');
            return null;
        } else {
            //log::add('zoe', 'debug', 'Giya : personId success');
            $strPersonId = $objJsonRes->{'data'}->{'personId'};
            //log::add('zoe', 'debug', 'Giya : personId = '. $strPersonId);

            $arrIdToken = self::getJwtToken($strGiyaToken, $strPersonId, $objJsonRes->{'data'}->{'gigyaDataCenter'});
            if($arrIdToken != null) {
                return array_merge(['GiyaPersonId' => $strPersonId], $arrIdToken);
            } else {
                return null;
            }
        }
    }

    // TODO supprimer parametres inutiles (personid datacenter)
    public static function getJwtToken($strGiyaToken, $strGiyaPersonId, $strGiyaDatacenter) {
        $objClient = new \GuzzleHttp\Client([
            'headers' => [ 'Content-Type' => 'application/json' ]
        ]);

        $objRes = $objClient->post(self::$strRootUrl .'/accounts.getJWT',
            ['form_params' => [
                'oauth_token'=> $strGiyaToken,
                'fields' => 'data.personId,data.gigyaDataCenter',//$strGiyaPersonId.','.$strGiyaDatacenter,
                'expiration' => 900
            ]]
        );
        $strResult = $objRes->getBody()->getContents();
        //log::add('zoe', 'debug', 'Giya response: ' . $strResult);
        $objJsonRes = json_decode($strResult);

        if($objJsonRes->{'statusCode'} != 200) {
            //log::add('zoe', 'debug', 'Giya : error retrieving id token');
            return null;
        } else {
            //log::add('zoe', 'debug', 'Giya : id token success');
            $strIdToken = $objJsonRes->{'id_token'};
            //log::add('zoe', 'debug', 'Giya : Id Token = '. $strIdToken);
            return ['GiyaIdToken' => $strIdToken, 'GiyaIdTokenTime' => $objJsonRes->{'time'}];
        }

    }    
}