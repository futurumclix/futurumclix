## FuturumClix
Written from scratch, the most advanced Get Paid To Click script on the market.
Made by people familiar with many PTC scripts and sites. Now available on 
[open-source AGPLv3 license](https://github.com/futurumclix/futurumclix/blob/master/LICENSE).

## License
FuturumClix is available under the terms of GNU Affero General Public License,
version 3. To ensure script may be used for creating many awesome sites, 
the view files (the ones with extensions "ctp", "js" and "css") are excluded from AGPL 
and mostly made available under the terms of [MIT license](http://www.opensource.org/licenses/mit-license.php).
If some module/file is an exception from this rule, the proper licensing information is
included in that module/file itself. 
Thanks to that you can freely modify the look and feel of your site. Changes made
in the core files (ones with "php" extension) must be made public available.
Additional information about license is available at beginning of each file.

## Requirements
The following server parameters are required for proper functioning of the script:
- PHP 5.6, with curl, gd, openssl, bcmath, gettext, mbstring extensions installed;
- MySQL database;
- E-mail server, e.g. postfix;
- Nginx (or Apache, but Nginx is a recommended solution). Apache requires active
mod_rewrite.

## Installation
To install FututurmClix from this repository you need working [CakePHP 2
installation](https://book.cakephp.org/2.0/en/installation.html). After
installing CakePHP 2 just merge this repository with directory where you
installed CakePHP 2 (so the lib directory will contain CakePHP 2, and app
directory will contain FuturumClix).
Release packages of FuturumClix comes with CakePHP 2 bundled. For additional
information check the documentation included in release package.

If you want to install [HighCharts](https://www.highcharts.com/) all you need
to do is copy *highcharts.js* file to *app/webroot/js/charts/* directory. 

## Troubleshooting 
In case of any problems with the script, we recommend to activate the debug mode, 
which provides a full reporting of errors. To activate the debug mode, find
the line "level = 0" under the [debug] section in the app/Config/core.ini.php
file and change the value 0 into 1 or 2. Remember that when the debug is active,
the payment systems do not work “live” but in a sandbox mode, so no payments
will be booked.

You can also disable our cache system by adding option enabled in [cache]
section in the app/Config/core.ini.php file and set it’s value to false,
like: "enabled = false".

If you found any bugs, have questions or would like to propose an enhancement
please contact us by [Issues Page](https://github.com/futurumclix/futurumclix/issues)
or submit a [Pull Request](https://github.com/futurumclix/futurumclix/pull/new/master). 
