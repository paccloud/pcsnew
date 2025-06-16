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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', ':/Users/ryanhorwath/Library/Application Support/Local/run/sQDvh9mcV/mysql/mysqld.sock' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          '%raP!Qc@2L~xpJ26mR?8nW!Nc-ZEP:?Qn7SGq_@`)fi`Bz;@jmo ?;s3-Np/e6Kh' );
define( 'SECURE_AUTH_KEY',   'SvC7jZ7girw8O-L(rYt)yX5 7w@Em}9Z[LjQ5MSoo-/?nReS%xk.T]/UMu}mnU?!' );
define( 'LOGGED_IN_KEY',     '+hM3:s!l(&IEGI.l~Y.Y6Z@ q[`yp^d@Xw)r0C^@`o^l}Oz2sN?nfn#{tUn(}CS_' );
define( 'NONCE_KEY',         '0wksP$g<%eiFnn_JF`~A{3wyZ9=o,}$:c+$-9d2w?{2@yJWoL_InA=SMJOx-d3_v' );
define( 'AUTH_SALT',         '![1oJBjTZ/Dnc~gh%i>ciag}]`S`I[UE2oLB0X8[PJ25GmF%Eb7N1hGY.HvsKPBN' );
define( 'SECURE_AUTH_SALT',  '`x1[sfSERv@s!KU1r+},t|3F1O=~6ieBnz;{_ns-v6VD>TTZOHDB=8TNnue<N,Z ' );
define( 'LOGGED_IN_SALT',    ';dA/:$iVNYo0Ght$+/QvxE;EsT}]f(]XHfv.?8n=Lgw6_EAmY9F@?)^1Gm^N^Ni:' );
define( 'NONCE_SALT',        '^1|K$#.Sn;Z907.VHFl~.mAZ>cBV1CDR;<w_LSic&qX}X_ITKSNuv>V*hj%^ZZG{' );
define( 'WP_CACHE_KEY_SALT', '}(N6trrc0UPxyS3:&`A/5&Oe5dAnv&~8BLO%9[xJX5Kx&=]cO+6c?sgj>KisZE[P' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */

define( 'WP_APPLICATION_PASSWORDS', true );



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
