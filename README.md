# PHP client for Renault Zoe API

![PHP Checks](https://github.com/PysX/renault-zoe-api/workflows/PHP%20Composer/badge.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](LICENSE.md)


This PHP package manages the communication with the private My Renault API used by the official mobile applications.

It is fully inspired by the Python client [Pyze](https://github.com/jamesremuscat/pyze/) by jamesremuscat.

## Quickstart

File RunExample.php provides an example of the use.

For the first use, you have to "login" to retrieve and the tokens. Tokens are saved in a local file, it prevents to retrieve them for each API call.
You have to test if tokens needs to be refreshed (see RunExample.php for an example).

```php
use RenaultZoeApi\Giya;
use RenaultZoeApi\Kamereon;

// Login, you need to replace login and password
$arrGiyaTokens = Giya::login('my_renault_login','my_renault_password');
// Get AccountId and save tokens in credentials.json file
$arrTokens = Kamereon::getAccounts($arrGiyaTokens);
// Get vehicle, You can get VIN here
Kamereon::getVehicles($arrTokens); 

// Get battery status, you need to replace vin
// Result is json string that you can decode
$strJson = Kamereon::getBattery('my_vin', $arrTokens);
```

## Contributing

You are all invited to contribute to this project for maintenance or improvement.
Even if you are not a developer, you can probably help to report some bugs, share improvements ideas, or contribute to the documentation.

### How to submit changes

#### Development environment

You need PHP 7.1+

```bash
composer global require "squizlabs/php_codesniffer=*"
composer global require friendsofphp/php-cs-fixer
composer global require rector/rector
```

#### How to test the project

TODO: not implemented

#### Open a pull request

Open a [pull request](https://github.com/PysX/renault-zoe-api/pulls) to submit changes to this project.

Your pull request needs to meet the following guidelines for acceptance:

- Run following [Rector](https://getrector.org/) code sets : code-quality dead-code coding-style
- Run ``` ~/.config/composer/vendor/bin/phpcs --standard=PSR12 src/ ```
- Run ``` ~/.config/composer/vendor/bin/php-cs-fixer src/ ```
- Run and check result ``` ~/.config/composer/vendor/bin/phpcs --standard=PSR12 src/ ```

### Feature suggestion or issues

If you want to suggest a new feature for this project, please open an [`issue`](https://github.com/PysX/renault-zoe-api/issues) by using the `feature request` template.

If you want to report an issue for this project, please open an [`issue`](https://github.com/PysX/renault-zoe-api/issues) by using the `bug report` template. First please be sure that an issue for this problem. I suggest that you check [Pyze 'known issues with the Renault API'](https://github.com/jamesremuscat/pyze/wiki/Known-issues-with-the-Renault-API).


## Disclaimer

This project is not affiliated with, endorsed by, or connected to Renault. I accept no responsibility for any consequences, intended or accidental, as a as a result of interacting with Renault's API using this project.

## Licence

This code is licensed under the terms of the standard MIT licence.