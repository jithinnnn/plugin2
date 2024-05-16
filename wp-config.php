<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'plugin_test' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'jithingeorgejose123' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'zj %T]F%l7a?.u31UIcE*: s`8NLGT|1U.h;q{+z|gi-=uj@S2pH(?,j9Qk)|g{9' );
define( 'SECURE_AUTH_KEY',  'B%^E/U6&w88D}>P/a|d .PC&6>Lkjvfc^LK5pAeAi^EHv2~:}zC8H^huE7sI2/`=' );
define( 'LOGGED_IN_KEY',    'wpz0`jG,Vrgwvpk}Ndu]0|,D1-UX(B^2y|F<;1X+{Deaov;B,JuQ::<c PdA?x2U' );
define( 'NONCE_KEY',        'fldLag[:}f|)61%=6/IQO#YJ(>;VumnwM=8hA]:BzU7U,(_6:-?n;oZ;3AgcGM)+' );
define( 'AUTH_SALT',        's:hRzl{h4_W,>  |-0|m X~5>-q gB._&DznMmT<tULeRezuCkH87;<9zJ-3t@if' );
define( 'SECURE_AUTH_SALT', ']|SaS<QVe4%sXD-s?+1Xv=3`0;PL5RqC [:sn10`Ar96]MS+#R5j^<4Fs|/a~vrz' );
define( 'LOGGED_IN_SALT',   '9~o]|0[|6JhNbL_H%d4h7_NL|J~p8dAa2gEIY8{JzH,w2qQlVAj}MCJ(D#-%:>Fu' );
define( 'NONCE_SALT',       '5zHF}`P|QX=g<:Q5M0j;>%pV>o9}+8D:)~9#tVh)Mb5-`V^4>vkeD@,gU64NxnVh' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define('WP_DEBUG', true);
// Enable Debug logging to the /wp-content/debug.log file 
define( 'WP_DEBUG_LOG', true );
// Disable display of errors and warnings 
define( 'WP_DEBUG_DISPLAY', false ); 
@ini_set( 'display_errors', 0 );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
