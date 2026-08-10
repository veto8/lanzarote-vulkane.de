<?php

define('WP_CACHE', false);
define('DB_NAME', 'vulkansql1');
define('DB_USER', 'vulkansql1');
define('DB_PASSWORD', 'passpass');
define('DB_HOST', 'db');

define('DB_CHARSET', 'utf8mb4');

define('DB_COLLATE', '');
define('AUTH_KEY', '|b<|+f(m3c)GE>{;d$w/PBhJx-aLRX tQd-Vs-^[TI*zhtoTpa+-fEbj]UPCIVAE');
define('SECURE_AUTH_KEY', ' >uDst`tr?:Lb%%TC_X{=r!bHA_in~2,ph- -L)./.3U~_x%3| |7(9kK5e$Hg]/');
define('LOGGED_IN_KEY', '2q{P}NrM[V&llXE55rbXw_2lv}:z+jcU(*=E I|-?UAg}70}n18^p|A(;6fq-0- ');
define('NONCE_KEY', 'm_;c~o-]hX7/We[c-57WFS&_g#6iQLU=Xk5c`a9)qgtsV<Vh8o;v-uIN5I_Eh?`Q');
define('AUTH_SALT', '7N@l~oLo-7~3hh+pa/iDYknRL-*,]lt~eH+8wybZU21]3|~.Mi}PuqprvHyQi|u3');
define('SECURE_AUTH_SALT', 'k/`%H_d05%de|p,Ytgy^R/_eS0?PbpCA4U&dhd<RVL*Q:@nqS+Q._gC](G.XqruW');
define('LOGGED_IN_SALT', 'e)C7w>^/hL|m)R+8j#CT,|;j.HIua!Tw7Fq@|Z],{X_Chu5S:v=T2]Eu5dmTW/W?');
define('NONCE_SALT', 'c.k*0Jeo6!;7H_{Rx99:f#/c.:_QLdki`R}%pWCx|(B1ZmBV#]%w(8|-jDV+50M?');
$table_prefix = 'wp_';

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
define('SCRIPT_DEBUG', true);

if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__).'/');
}

require_once ABSPATH.'wp-settings.php';
