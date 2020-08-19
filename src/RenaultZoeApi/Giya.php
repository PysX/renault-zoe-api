<?php

namespace RenaultZoeApi;

/**
 * Class for Giya auth platform
 */
class Giya
{
    // TODO should be retrieved from a local parameter
    private static $strRootUrl = "https://accounts.eu1.gigya.com";
    private static $strApiKey = "3_e8d4g4SE_Fo8ahyHwwP7ohLGZ79HKNN2T8NjQqoNnk6Epj6ilyYwKdHUyCw3wuxz";

    /**
     * Login de l'utilisateur
     * Permet de récupérer le token puis dans un second temps le personId
     *
     * @param  string $strUserName
     * @param  string $strPassword
     * @return array ['GiyaToken', 'GiyaPersonId', GiyaIdToken', 'GiyaIdTokenTime'] or 'KO' or null
     */
    public static function login($strUserName, $strPassword)
    {
        $objClient = new \GuzzleHttp\Client(
            [
            'headers' => [ 'Content-Type' => 'application/json' ]
            ]
        );
        $objRes = $objClient->post(
            self::$strRootUrl . '/accounts.login',
            ['form_params' => [
                'ApiKey' => self::$strApiKey,
                'loginID' => $strUserName,
                'password' => $strPassword
            ]]
        );
        $strResult = $objRes->getBody()->getContents();
        // TODO : LOG
        $objJsonRes = json_decode($strResult);

        if ($objJsonRes->{'statusCode'} != 200) {
            // TODO : LOG
            return 'KO';
        } else {
            // TODO : LOG
            $strToken = $objJsonRes->{'sessionInfo'}->{'cookieValue'};
            // TODO : LOG

            $arrTokens = self::getPersonId($strToken);

            if ($arrTokens != null) {
                return array_merge(['GiyaToken' => $strToken], $arrTokens);
            } else {
                return null;
            }
        }
    }

    /**
     * Permet de récupérer le personId après avoir récupére le token
     *
     * @param  string $strGiyaToken
     * @return array ['GiyaPersonId', GiyaIdToken', 'GiyaIdTokenTime'] or null
     */
    private static function getPersonId($strGiyaToken)
    {
        $objClient = new \GuzzleHttp\Client(
            [
            'headers' => [ 'Content-Type' => 'application/json' ]
            ]
        );
        $objRes = $objClient->post(
            self::$strRootUrl . '/accounts.getAccountInfo',
            ['form_params' => [
                'ApiKey' => self::$strApiKey,
                'login_token' => $strGiyaToken
            ]]
        );
        $strResult = $objRes->getBody()->getContents();
        // TODO : LOG
        $objJsonRes = json_decode($strResult);

        if ($objJsonRes->{'statusCode'} != 200) {
            // TODO : LOG
            return null;
        } else {
            // TODO : LOG
            $strPersonId = $objJsonRes->{'data'}->{'personId'};
            // TODO : LOG

            $arrIdToken = self::getJwtToken($strGiyaToken);
            if ($arrIdToken != null) {
                return array_merge(['GiyaPersonId' => $strPersonId], $arrIdToken);
            } else {
                return null;
            }
        }
    }

    /**
     * Get personnal token, public as this expires it could be call for refresh
     *
     * @param  string $strGiyaToken
     * @return array ['GiyaIdToken', 'GiyaIdTokenTime'] or null
     */
    public static function getJwtToken($strGiyaToken)
    {
        $objClient = new \GuzzleHttp\Client(
            [
            'headers' => [ 'Content-Type' => 'application/json' ]
            ]
        );

        $objRes = $objClient->post(
            self::$strRootUrl . '/accounts.getJWT',
            ['form_params' => [
                'ApiKey' => self::$strApiKey,
                'login_token' => $strGiyaToken,
                'fields' => 'data.personId,data.gigyaDataCenter',
                'expiration' => 900
            ]]
        );
        $strResult = $objRes->getBody()->getContents();
        // TODO : LOG
        $objJsonRes = json_decode($strResult);

        if ($objJsonRes->{'statusCode'} != 200) {
            // TODO : LOG
            return null;
        } else {
            // TODO : LOG
            $strIdToken = $objJsonRes->{'id_token'};
            // TODO : LOG
            return ['GiyaIdToken' => $strIdToken, 'GiyaIdTokenTime' => $objJsonRes->{'time'}];
        }
    }
}
