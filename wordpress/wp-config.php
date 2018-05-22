<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'sefab_intranet');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'CX(xRFu3>YAtq|OqthbZ{b1|RnD`9I9K YhIVJr&mx2giGTg!UZA8Wd/)lgw33-j');
define('SECURE_AUTH_KEY',  'Q(So)^1nG/>_p!S=?!Xqdk.Bi^!s~>^9wJz=p /)N]}6;TYz#}|]v_%8;^<OLXo[');
define('LOGGED_IN_KEY',    'Ia]8-|MTEP~||v]&ZhKbIpk?uGhmeOS>}e1M^Yz0/?=IL4|bk;pF1GONz-XY|BfG');
define('NONCE_KEY',        'R*5>p6SaYYJGy$|<dNW*b&]D,0&gBwsRpUIIX=HpN?}DDn@~+&$n,N+kZ$?4!P*B');
define('AUTH_SALT',        '*gpJ(VHKY(TbLFgKA8{0Q{Ps7xo>/CJ]5d#<SPnLDzMX`18DqGM`$ngG:4fDWM.H');
define('SECURE_AUTH_SALT', 'f&6?IPViXv}WO_K6tw|>GkM>q~S7SxDn6L71lNSTy3E~.T/FIg4Q5F/g?bjN2:m{');
define('LOGGED_IN_SALT',   '&J%pED#&@z)<91c9DvbjKN^RYVb2:&=A&ky8@I,2vxF3a##YAEdYda5:4Tc8pU:8');
define('NONCE_SALT',       '{AX5W=/Ze=|P|Uj30xTS!uVph$BplBYZq0O~q,2@v 3/u7XrLlF{?/$=jzK|DR c');

//JWT AUTHENTICATION
define('JWT_AUTH_SECRET_KEY', 'WgeKMhM^~ER7hz]8%_Dzd(xS-y#Mvt/&{tIU7q3JO~S;?Y46e]X82cR~;pp<#kKS');
define('JWT_AUTH_CORS_ENABLE', true);
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
