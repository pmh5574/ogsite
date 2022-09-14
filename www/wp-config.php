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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'pmh5547' );

/** Database username */
define( 'DB_USER', 'pmh5547' );

/** Database password */
define( 'DB_PASSWORD', 'park2772' );

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
define( 'AUTH_KEY',         'uk?SP)$`r&0F(Cgfhet60QI@-),i9VQj96>VW^f[:FHRhEQN;?QK8J5@C80JOA3T' );
define( 'SECURE_AUTH_KEY',  '2%T?)p*b@oSH1dNcUS5lfm}^/(Y>$pu(d{vzx/a IJP7^7dx*@*WY3O`<hLxL:zr' );
define( 'LOGGED_IN_KEY',    'XvJfWiw}GM8(TGs3JjB>(nr+.{Ih8g?0=7%yz`(jJs~!^h/rL`tkrCrg&^7:mF}h' );
define( 'NONCE_KEY',        '::c^=r~fTW>g)<vp#5bnl|Ll->SYNcHe+evr_&]eO(^}gaNH!`La#oMKd|BcFvHy' );
define( 'AUTH_SALT',        'KE@&*Fc-DYgp(kxN8y)3L1!X_^Kqu#]c2{w__>24M->3k&Q|vkL*#(g[aCmHv,=e' );
define( 'SECURE_AUTH_SALT', 'Q<QO]kh7eSBS@}jvMw<)Yl,h]v8*2S>U2Cv1h9Rq&MD!#V4B&age4:bz<= *lfat' );
define( 'LOGGED_IN_SALT',   'ZbBpAB[MRsa[$a$}7|*:|H}d`[, %B?.j#]<u`:%9R;zMhw~%Vk3p{<y._-lBANy' );
define( 'NONCE_SALT',       'cwGxO/E%rx~I>+9Jk{L4|oEUg.cXIRm|yaupxo(}(LN@nR Wz^7}Sp}w#Y!S>krM' );

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

/* custom security setting */
define('DISALLOW_FILE_EDIT',true);
define('IMAGE_EDIT_OVERWRITE',true);
define('DISABLE_WP_CRON',true);
define('EMPTY_TRASH_DAYS',7);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

/* disble core major update */
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
