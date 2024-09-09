<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'serahshani' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',         '4d^?H4x}UrF[?<WM@&+ZjM TZHVBsM~?hyB@Da>kW,+7V?+nQ8%S Vt54w6I}a>x' );
define( 'SECURE_AUTH_KEY',  'u#eQaPa8d%Bdt-8O1t>6Xp8bw}ja2ul29Z,y&FV,Mb3K/#]y/4kq!@xI}6L+R^];' );
define( 'LOGGED_IN_KEY',    'td1O+MZbZg_Y+=bhxu/YX<.KO$;5^iBm7sX}<gU|6c)XQy~#cNkL[HR7s`&=O?%H' );
define( 'NONCE_KEY',        'F]o0$O;5*6^f&n(`tlp_T{?5:vOz30#eVfO-Mx$&*o@!XZ#tZf[27yx{`8>pH@!i' );
define( 'AUTH_SALT',        'Gc,B!Oyu7R_=U_CYi}[Gc.4?x4]^6{5b6=*k?aYVIx;$wbZ/rhL:ZT;6(X-5aME*' );
define( 'SECURE_AUTH_SALT', 's{k.fBXEv-,Bd%o}$0WD+vUZ*,5NhL5aW.|]93kDhy|Gz$<.N[;1q,tFbuW75aoZ' );
define( 'LOGGED_IN_SALT',   'M/&MBqT}QzdqZcSGnW7l5I))Ivh ^[H}/%8Us(zfj4dh(r=Og/;Sr?gnGg=h/8Yj' );
define( 'NONCE_SALT',       '7?K*Y,%n5/-d7)y*AkE=0>.k##UjDMx,2mPI14_-G,jp*rW2xqhto/B59GKW8IcC' );

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
