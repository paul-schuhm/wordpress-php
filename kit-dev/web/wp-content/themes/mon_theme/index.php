<?php

//require_once __DIR__ . '/vendor/autoload.php';

/**
 * Point d'entrée du thème
 */

add_filter(
    'wp_mail_from',
    function () {
        return 'test@mailhog.local';
    }
);

$theme = wp_get_theme();
echo sprintf("<p>Thème activé : %s</p>", $theme->get('Name'));

if (wp_mail('test@example.com', 'MailHog test', 'Hello from WordPress')) {
    echo '<p>Un email de test a bien été envoyé ! <a href="http://localhost:8025">Le consulter dans le mailcatcher </a> </p>';
} else {
    echo "<p>Erreur : l'email de test n'a pas été envoyé :( Il va falloir vérifier la configuration du serveur mail... !</p>";
}

error_log("Activer le mode (WP_DEBUG=1 et WP_DEBUG_LOG=1) pour écrire les logs de WordPress dans le fichier wp-content/debug.log (par défaut)");