<?php
/**
 * Template de départ du thème initialisé pour le kit de dev.
 * php version 8.4
 *
 * @category Template
 * @package  Formation
 * @author   Paul Schuhmacher <contact@pschuhmacher.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.fr.html#license-text GNU/GPL-3
 * @link     https://github.com/paul-schuhm/wordpress-php/tree/main/kit-dev
 */

//require_once __DIR__ . '/vendor/autoload.php';

/**
 * Point d'entrée du thème
 */

$theme = wp_get_theme();

echo sprintf("<p>Thème activé : %s</p>", $theme->get('Name'));

if (wp_mail('jdoe@example.com', 'MailHog test', 'Hello from WordPress !')) {
    echo '<p>Un email de test a bien été envoyé ! <a href="http://localhost:8025">Le consulter dans le Mailcatcher </a> </p>';
} else {
    echo "<p>Erreur : l'email de test n'a pas été envoyé :( Il va falloir vérifier la configuration du serveur mail... !</p>";
}

/*Dé-commenter pour générer volontairement une erreur 
et vérifier que les erreurs sont bien log dans wp-content/debug.log
*/

// error_log("test error_log(); avant crash");
// trigger_error("Erreur volontaire", E_USER_ERROR);

/*Activer le mode debug. Placer ces instructions dans le fichier wp-config.php :
    define( 'WP_ENVIRONMENT_TYPE', 'development' ); 
    define( 'WP_DISABLE_FATAL_ERROR_HANDLER', true );
    define( 'WP_DEBUG', true );               
    define( 'WP_DEBUG_DISPLAY', true );       
    define( 'WP_DEBUG_LOG', true );           
    define( 'SAVEQUERIES', true );           
    define( 'SCRIPT_DEBUG', true );
*/

/*Vérifier la configuration du php.ini :
     1) le module redis est bien activé ;
     2) l'opcache est bien activé ;
     3) la compression zlib est bien activée
*/

phpinfo();
