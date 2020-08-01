# PHP client for Renault Zoe API

This PHP package manages the communication with the private My Renault API used by the official mobile applications.

It is fully inspired by the Python client [Pyze](https://github.com/jamesremuscat/pyze/) by jamesremuscat.

## Quickstart

File RunExample.php provides an example of the use.

For the first use, you have to "login" to retrieve and the tokens. Tokens are saved in a local file, it prevents to retrieve them for each API call.
You have to test if tokens needs to be refreshed (see RunExample.php for an example).


## Contributing

You are all invited to contribute to this project for maintenance or improvement.
Even if you are not a developer, you can probably help to report some bugs, share improvements ideas, or contribute to the documentation.

### How to submit changes

#### Development environment

You need PHP 7.1+

#### How to test the project

TODO

#### Open a pull request

Open a [pull request](https://github.com/PysX/renault-zoe-api/pulls) to submit changes to this project.

Your pull request needs to meet the following guidelines for acceptance:

- Run following [Rector](https://getrector.org/) code sets : code-quality dead-code coding-style

### Feature suggestion or issues

If you want to suggest a new feature for this project, please open an [`issue`](https://github.com/PysX/renault-zoe-api/issues) by using the `feature request` template.

If you want to report an issue for this project, please open an [`issue`](https://github.com/PysX/renault-zoe-api/issues) by using the `bug report` template. First please be sure that an issue for this problem. I suggest that you check [Pyze 'known issues with the Renault API'](https://github.com/jamesremuscat/pyze/wiki/Known-issues-with-the-Renault-API).


## Disclaimer

This project is not affiliated with, endorsed by, or connected to Renault. I accept no responsibility for any consequences, intended or accidental, as a as a result of interacting with Renault's API using this project.

## Licence

This code is licensed under the terms of the standard MIT licence.