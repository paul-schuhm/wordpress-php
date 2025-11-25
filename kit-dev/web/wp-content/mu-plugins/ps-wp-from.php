<?php

/**
 * Définit l'adresse email d’expéditeur du site WordPress
 * 
 * @category Plugin
 * @package  Formation
 * @author   Paul Schuhmacher <contact@pschuhmacher.com>
 * @license  https://www.gnu.org/licenses/gpl-3.0.fr.html#license-text GNU/GPL-3
 * @link     https://github.com/paul-schuhm/wordpress-php/tree/main/kit-dev
 */


define('PS_WP_MAIL_FROM', 'formation@wordpress.local');

/**
 * Retourne l'adresse email utilisée comme expéditeur pour wp_mail().
 *
 * Cette fonction est attachée au filtre 'wp_mail_from' afin de remplacer
 * l'adresse par défaut de WordPress par la constante PS_WP_MAIL_FROM.
 *
 * @return string L'adresse email d'expédition des emails du site.
 */
function ps_wp_mail_from(): string
{
    return PS_WP_MAIL_FROM;
}

add_filter('wp_mail_from', 'ps_wp_mail_from');


add_action('plugins_loaded', function () {

    /**
     * Configurer les points de chargement et de sauvegarde des champs ACF (mode Local JSON)
     * @link: https://www.advancedcustomfields.com/resources/local-json/
     */
    if (function_exists('get_field')) {

        //Personnaliser le point de sauvegarde du JSON.
        function ps_acf_json_save_point($path)
        {
            return WP_CONTENT_DIR . '/acf-json';
        }
        add_filter('acf/settings/save_json', 'ps_acf_json_save_point');

        //Personnaliser le point de chargement du JSON.
        function ps_json_load_point($paths)
        {
            // Remove the original path (optional).
            unset($paths[0]);

            // Append the new path and return it.
            $paths[] = WP_CONTENT_DIR . '/acf-json';

            return $paths;
        }
        add_filter('acf/settings/load_json', 'ps_json_load_point');
    }
});
