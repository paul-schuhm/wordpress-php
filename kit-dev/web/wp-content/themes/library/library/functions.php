<?php
/**
 * Fonctions du thème
 *
 * @package BP
 */

/**
 * Initialise le thème
 *
 * @return void
 */
function bp_after_setup_theme() {

	/**
	 * Créer les emplacements de mnu
	 */
	register_nav_menus(
		array(
			'primary'   => __(
				'Main navigation menu',
				'BP'
			),
			'secondary' => __(
				'Secondary menu in left sidebar',
				'BP'
			),
			'mobile'    => __(
				'Mobile menu',
				'BP'
			),
		)
	);
	/**
	 * Enregistre les shortcodes
	 */
	add_shortcode(
		'span-color',
		function ( $atts = array(), $content = null ) {
			$atts = shortcode_atts(
				array(
					'color' => 'red',
				),
				$atts
			);
			return '<span style="color:' . esc_attr( $atts['color'] ) . '">' . esc_html( $content ) . '</span>';
		}
	);
}

add_action( 'after_setup_theme', 'bp_after_setup_theme' );

/**
 * Log chaque publication d'article
 *
 * @param string  $new_status L'id du post qui a changé de status.
 * @param string  $old_status L'ancien status.
 * @param WP_Post $post Le post concerné.
 * @return void
 */
function enis_log_on_post_change_status( string $new_status, string $old_status, WP_Post $post ) {
	$author_id = $post->post_author;
	if ( WP_DEBUG ) {
		error_log( "Post {$post->ID} Nouveau status : $new_status - Ancien status : $old_status. Auteur : $author_id. Titre : {$post->post_title}" );
	}
}

add_action( 'transition_post_status', 'enis_log_on_post_change_status', 10, 3 );
