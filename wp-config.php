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

 * @link https://wordpress.org/support/article/editing-wp-config-php/

 *

 * @package WordPress

 */


// ** MySQL settings - You can get this info from your web host ** //

/** The name of the database for WordPress */

define( 'DB_NAME', "dinner_club" );


/** MySQL database username */

define( 'DB_USER', "root" );


/** MySQL database password */

define( 'DB_PASSWORD', "" );


/** MySQL hostname */

define( 'DB_HOST', "localhost" );


/** Database Charset to use in creating database tables. */

define( 'DB_CHARSET', 'utf8mb4' );


/** The Database Collate type. Don't change this if in doubt. */

define( 'DB_COLLATE', '' );


/**#@+

 * Authentication Unique Keys and Salts.

 *

 * Change these to different unique phrases!

 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}

 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.

 *

 * @since 2.6.0

 */

define( 'AUTH_KEY',         'Jv`:8$8bzTI@tF7 0L9,7WhS:y8mcuEFQZ]df%`iC~C*H%d+_|`,*y~M,:J;|dYD' );

define( 'SECURE_AUTH_KEY',  'ix;?}34WD[l)BfbQPN7-UPT!x,Fw:%ejS1Z&qBo&Y#wWCz9fY{duOLEM:6;T=`h|' );

define( 'LOGGED_IN_KEY',    'k`,dqnxZ[y`p`|++g% <tfxKm=A=7|1.EupUjv[JM9|LcD Xy0Kv.mw;uTxS)pX#' );

define( 'NONCE_KEY',        'P[FV>/P}z=b$>CxedmV}2VNW4P:H6#f5`CCB{y(+;2<CsH]MP`AB[gJ(3|[E;)2O' );

define( 'AUTH_SALT',        '>X&.Li/(BDYlh%xnc?r2.-}k@2BEOWN`J9@]}!P,KFHShUoMw~/6x`)n0t<e)U6@' );

define( 'SECURE_AUTH_SALT', 'mUj?@lPPe2ZrH^sr1H@Yu4h5L>WLDv}[!:87@O<cY+vI+L; h]O8(^:,;7rZ%psx' );

define( 'LOGGED_IN_SALT',   'Fm&+<4OGO=VN6d/6.?O*n-ucXD~h?$Q}b@cT#Ua)RZHIF$MK=^$J}Diy&=cu3-t}' );

define( 'NONCE_SALT',       'x?A1lGaVS6uiO|Yua3EVL=>E(>5i9aV GHl$kuzetUhw9TLpDF?8wx6M7viw?9WG' );


/**#@-*/


/**

 * WordPress Database Table prefix.

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

 * @link https://wordpress.org/support/article/debugging-in-wordpress/

 */

define( 'WP_DEBUG', false );


/* That's all, stop editing! Happy publishing. */


/** Absolute path to the WordPress directory. */

if ( ! defined( 'ABSPATH' ) ) {

	define( 'ABSPATH', __DIR__ . '/' );

}


/** Sets up WordPress vars and included files. */

require_once ABSPATH . 'wp-settings.php';

