<?php

require_once __DIR__ . '/vendor/autoload.php';
 
use RenaultZoeApi\Giya;
use RenaultZoeApi\Kamereon;

// Run example
run();


/**
 * Run Example function
 * To avoid to login to API each call, we save tokens in credentials.json
 *
 * @return void
 */
function run()
{
    say('> Run started');
    $arrTokens = []; // Will contains token from credentials.json
    $strFile = dirname(__FILE__) . '/data/credentials.json';
    if (file_exists($strFile)) {
        say('> credentias.json exists, reading it');
        $arrTokens = json_decode(file_get_contents($strFile), true);
        // Check token validity
        checkTokens($arrTokens);
    } else {
        say('> credentials.json not found, first login');
        $arrGiyaTokens = Giya::login('my_renault_login', 'my_renault_password');
        // get AccountId and save tokens in credentials.json file
        $arrTokens = Kamereon::getAccounts($arrGiyaTokens);
        Kamereon::getVehicles($arrTokens); // You can get VIN here
    }
      
    // Example via Kamereon class
    say('> getBattery via Kamereon class');
    $strJson = Kamereon::getBattery('my_vin', $arrTokens);
    say($strJson);

    say('> getCockpit via Kamereon class');
    $strJson = Kamereon::getCockpit('my_vin', $arrTokens);
    say($strJson);

    say('> getLocation via Kamereon class');
    $strJson = Kamereon::getLocation('my_vin', $arrTokens);
    say($strJson);

    say('> getChargingSettings via Kamereon class');
    $strJson = Kamereon::getChargingSettings('my_vin', $arrTokens);
    say($strJson);

    say('> getChargeMode via Kamereon class');
    $strJson = Kamereon::getChargeMode('my_vin', $arrTokens);
    say($strJson);
    
    /* Not implemented server side
    say('> getLockStatus via Kamereon class');
    $strJson = Kamereon::getLockStatus('my_vin', $arrTokens);
    say($strJson);
    */
    /* Not implemented server side
    say('> getNotificationSettings via Kamereon class');
    $strJson = Kamereon::getNotificationSettings('my_vin', $arrTokens);
    say($strJson);
    */

    /* Not implemented server side
    say('> getHvacHistory via Kamereon class');
    $strJson = Kamereon::getHvacHistory('my_vin',['type'=>'day','start'=>'20200701','end'=>'20200731'], $arrTokens);
    say($strJson);
    */

    /*
    say('> getHvacHSessions via Kamereon class');
    $strJson = Kamereon::getHvacSessions('my_vin',['start'=>'20200701','end'=>'20200731'], $arrTokens);
    say($strJson);
    */

    /*
    say('> getHvacStatus via Kamereon class');
    $strJson = Kamereon::getHvacStatus('my_vin', $arrTokens);
    say($strJson);
    */

    // Example via Vehicle class
    // TODO
}

/**
 * Check token validity and update them
 *
 * @param array $arrTokens
 * @return void
 */
function checkTokens(&$arrTokens)
{
    $objDate = new \DateTime($arrTokens['GiyaIdTokenTime']);
    $objDateNow = new \DateTime("now");

    $objDate->add(new \DateInterval('PT900S'));
    if ($objDate < $objDateNow) {
        // Refresh token
        say('> Tokens needs refresh');
        // Refresh tokens
        $arrGiyaIdToken = Giya::getJwtToken($arrTokens['GiyaToken'], '', '');
        $arrTokens['GiyaIdTokenTime'] = $arrGiyaIdToken['GiyaIdTokenTime'];
        $arrTokens['GiyaIdToken'] = $arrGiyaIdToken['GiyaIdToken'];

        Kamereon::saveTokens($arrTokens);
        // From an update of Renault API in june 2020, kamereon-authorization token is no more required
    }
}

/**
 * Makes echo !
 *
 * @param string $strMessage
 * @return void
 */
function say($strMessage)
{
    echo $strMessage . "\n\r";
}
