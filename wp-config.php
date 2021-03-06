﻿<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clés secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C’est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d’installation. Vous n’avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'nnjy_30avril');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'nnjy_db_30avril');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'azrael00');

/** Adresse de l’hébergement MySQL. */
define('DB_HOST', 'nnjy.myd.infomaniak.com');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');

/** Type de collation de la base de données.
  * N’y touchez que si vous savez ce que vous faites.
  */
define('DB_COLLATE', '');

/**#@+
 * Clés uniques d’authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n’importe quel moment, afin d’invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ']50bZ2ybc<;%z5BCV{ok?KTEb6y:aoV/>QtG>64MIRSc+d!|4L-pNk>~LTHozC2p');
define('SECURE_AUTH_KEY',  ')s.F}cY+Gl)/7%Ghc-p90D`<*`i{5!V<Jl([Qf;f*#*$j{FswLmz6?DD &c8IB>_');
define('LOGGED_IN_KEY',    'GkC)fkX2k<S(.0C;~2}z9Ecj6*A.aWr=I1^ulXPY|^^9@{v&Q>SyuS2Z2~@PS|^?');
define('NONCE_KEY',        'is`OANt:[Uvabz-06:Dq)P4b*&-]v]<d&CC v-v)#ml=[5TTpiPyeyT<&A+.wOnG');
define('AUTH_SALT',        '~hKj>LNuLchDP.$[!mCZ[fRyFi8RF0I#Y4]_rTOIs5vjwL8dk.AY[|v}g=-k7MU]');
define('SECURE_AUTH_SALT', 'N+aeo/k}CMed3Vs`+)tw4euU~L7P%zdX5Ha^u}um{C(Li*;w0sUk4@x=1q/>uSos');
define('LOGGED_IN_SALT',   '73JL%,k(rW(Mw[9 Jr1N,}#e6LJ{}}ja5Z^a.TLc[)+sid$B^i5(91S>9<NE_-i ');
define('NONCE_SALT',       '2gUqB0[pxOfT/9s<ll@i?ppr(03,UvsQmX]?-T%FyUdp-u4w4[Q8 y1kG{jgJb&P');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique.
 * N’utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés !
 */
$table_prefix  = 'wp_';

/**
 * Pour les développeurs : le mode déboguage de WordPress.
 *
 * En passant la valeur suivante à "true", vous activez l’affichage des
 * notifications d’erreurs pendant vos essais.
 * Il est fortemment recommandé que les développeurs d’extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de
 * développement.
 *
 * Pour plus d'information sur les autres constantes qui peuvent être utilisées
 * pour le déboguage, rendez-vous sur le Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

define('WPLANG', fr-FR);


/* C’est tout, ne touchez pas à ce qui suit ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');