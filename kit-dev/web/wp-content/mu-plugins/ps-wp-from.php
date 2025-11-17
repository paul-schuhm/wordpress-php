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
