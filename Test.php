<?php

require_once __DIR__ . '/vendor/autoload.php';
 

use RenaultZoeApi\Giya;
use RenaultZoeApi\Kamereon;


$strFile = dirname(__FILE__) . '/data/credentials.json';
$arrTokens = json_decode(file_get_contents($strFile), true);

// TODO SI NO TOKEN => LOGIN, SINON CHECK+REFRESH DATAS
//$res = Giya::login('my_renault_login','my_renault_password');
//Kamereon::getAccounts($res);

// TODO CHECK TOKEN VALIDITY OR REFRESH        ;
$objDate = new \DateTime($arrTokens['GiyaIdTokenTime']);    
$dateNow = new \DateTime("now");


$objDate->add(new \DateInterval('PT900S'));
if($objDate < $dateNow) {
    // Refresh token
    
    echo "need refresh\n\r";
    //log::add('zoe', 'debug','Need tokens refresh');
    
    $arrGiyaIdToken = Giya::getJwtToken($arrTokens['GiyaToken'],'','');
    $arrTokens['GiyaIdTokenTime'] = $arrGiyaIdToken['GiyaIdTokenTime'];
    $arrTokens['GiyaIdToken'] = $arrGiyaIdToken['GiyaIdToken'];

    $strAccessToken = Kamereon::getToken($arrTokens['accountId'],$arrTokens, true);
    $arrTokens['kamereon-authorization'] = $strAccessToken;
    
}

Kamereon::getBattery('VIN', $arrTokens);
Kamereon::getCockpit('VIN', $arrTokens); 
