<?php
/**
 * Fonctions du thème
 *
 * Remarque : de nombreuses fonctions devraient être déplacées dans des plugins.
 * Dès qu'une fonctionnalité ne concerne pas le fonctionnement du thème (affichage),
 * la déplacer dans un plugin
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


/**
 * Description: Crée un Custom Post Type "Book" pour la bibliothèque.
 * Version: 1.0
 */

/**
 * Fonction pour enregistrer le Custom Post Type "Book"
 */
function bp_register_cpt_book() {

	// Labels affichés dans l'admin (Les générer (Plugin, IA, Site web!).
	$labels = array(
		'name'                  => _x( 'Livres', 'Post Type General Name', 'textdomain' ),
		'singular_name'         => _x( 'Livre', 'Post Type Singular Name', 'textdomain' ),
		'menu_name'             => __( 'Livres', 'textdomain' ),
		'name_admin_bar'        => __( 'Livre', 'textdomain' ),
		'archives'              => __( 'Archives des livres', 'textdomain' ),
		'attributes'            => __( 'Attributs du livre', 'textdomain' ),
		'parent_item_colon'     => __( 'Livre parent :', 'textdomain' ),
		'all_items'             => __( 'Tous les livres', 'textdomain' ),
		'add_new_item'          => __( 'Ajouter un nouveau livre', 'textdomain' ),
		'add_new'               => __( 'Ajouter', 'textdomain' ),
		'new_item'              => __( 'Nouveau livre', 'textdomain' ),
		'edit_item'             => __( 'Éditer le livre', 'textdomain' ),
		'update_item'           => __( 'Mettre à jour le livre', 'textdomain' ),
		'view_item'             => __( 'Voir le livre', 'textdomain' ),
		'view_items'            => __( 'Voir les livres', 'textdomain' ),
		'search_items'          => __( 'Rechercher un livre', 'textdomain' ),
		'not_found'             => __( 'Aucun livre trouvé', 'textdomain' ),
		'not_found_in_trash'    => __( 'Aucun livre trouvé dans la corbeille', 'textdomain' ),
		'featured_image'        => __( 'Image à la une', 'textdomain' ),
		'set_featured_image'    => __( 'Définir l’image à la une', 'textdomain' ),
		'remove_featured_image' => __( 'Supprimer l’image à la une', 'textdomain' ),
		'use_featured_image'    => __( 'Utiliser comme image à la une', 'textdomain' ),
		'insert_into_item'      => __( 'Insérer dans le livre', 'textdomain' ),
		'uploaded_to_this_item' => __( 'Téléversé sur ce livre', 'textdomain' ),
		'items_list'            => __( 'Liste de livres', 'textdomain' ),
		'items_list_navigation' => __( 'Navigation liste de livres', 'textdomain' ),
		'filter_items_list'     => __( 'Filtrer la liste de livres', 'textdomain' ),
	);

	// Options principales du CPT.
	$args = array(
		'label'                => __( 'Livre', 'textdomain' ),
		'description'          => __( 'Custom Post Type pour les livres', 'textdomain' ),
		'labels'               => $labels,
		'supports'             => array( 'title', 'editor', 'thumbnail', 'excerpt' ), // Important : pour les options d'édition dans la page admin.
		'taxonomies'           => array( 'genre' ),  // Important : Enregistrer les taxonomies à utiliser par le CPT.
		'hierarchical'         => false,    // true pour type parent/enfant (comme pages).
		'public'               => true,
		'show_ui'              => true,
		'show_in_menu'         => true,
		'menu_position'        => 4,
		'menu_icon'            => 'dashicons-book', // icône dans l'interface (important pour l'UI).
		'show_in_admin_bar'    => true,
		'show_in_nav_menus'    => true,
		'register_meta_box_cb' => '',       // Fonction à appeler pour enregistrer auto des MetaBox.
		'can_export'           => true,
		'has_archive'          => true,     // Important ! Permet d'avoir template archive custom (archive-book.php).
		'exclude_from_search'  => false,
		'publicly_queryable'   => true,
		'capability_type'      => 'post',   // Important pour définir les capabilities (attachées aux roles !). Meme que sur posts par défaut.
		'show_in_rest'         => true,     // Pour Gutenberg et autres Page builder (API utilisée par les blocks) et l’API REST.

		'rewrite'              => array(
			'slug' => 'books', // ! Important ! Permet de réécrire le slug dans les urls. Mettre au pluriel.
		),
		'delete_with_user'     => false,
	);

	// Enregistrement du CPT.
	register_post_type( 'book', $args );
}

// Hook pour initialiser le CPT après le chargement du core.
add_action( 'init', 'bp_register_cpt_book', 0 );



/**
 * Déclare la taxonomie "Genre" pour le post type "book".
 */
function bp_register_taxonomy_genre() {

	$labels = array(
		'name'              => _x( 'Genres', 'taxonomy general name', 'textdomain' ),
		'singular_name'     => _x( 'Genre', 'taxonomy singular name', 'textdomain' ),
		'search_items'      => __( 'Rechercher un genre', 'textdomain' ),
		'all_items'         => __( 'Tous les genres', 'textdomain' ),
		'parent_item'       => __( 'Genre parent', 'textdomain' ),
		'parent_item_colon' => __( 'Genre parent :', 'textdomain' ),
		'edit_item'         => __( 'Modifier le genre', 'textdomain' ),
		'update_item'       => __( 'Mettre à jour le genre', 'textdomain' ),
		'add_new_item'      => __( 'Ajouter un nouveau genre', 'textdomain' ),
		'new_item_name'     => __( 'Nouveau nom de genre', 'textdomain' ),
		'menu_name'         => __( 'Genres', 'textdomain' ),
	);

	$args = array(
		'labels'            => $labels,

		// Taxonomie hiérarchique (true = comme catégories ; false = comme étiquettes).
		'hierarchical'      => true,

		// Permet de gérer la taxonomie dans l’admin.
		'show_ui'           => true,
		'show_admin_column' => true,

		// Disponible dans les requêtes publiques (WP_Query).
		'public'            => true,

		// Réécriture de l’URL.
		'rewrite'           => array(
			'slug'         => 'genre',   // URL de base : /genre/...
			'hierarchical' => true,      // permet /genre/roman/policier/ .
		),
		// Personnaliser la MetaBox. Ici utilise la même que post_categories_meta_box (avoir meme UI que si hiérarchique, avec checkboxes).
		'meta_box_cb'       => 'post_categories_meta_box',
	);

	register_taxonomy(
		'genre',  // slug de la taxonomie.
		array( 'book' ), // CPT sur lequel elle s'applique.
		$args
	);
}
add_action( 'init', 'bp_register_taxonomy_genre' );


/**
 * Add theme support. Permet d'activer des fonctionnalités pour le thème
 *
 * @link https://developer.wordpress.org/reference%2Ffunctions%2Fadd_theme_support%2F/
 */

add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );
add_theme_support(
	'post-formats',
	array(
		'aside',
		'gallery',
		'link',
		'image',
		'quote',
		'status',
		'video',
		'audio',
		'chat',
	)
);
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'custom-background' );
add_theme_support( 'custom-header' );
add_theme_support( 'custom-logo' );
add_theme_support( 'customize-selective-refresh-widgets' );
add_theme_support( 'starter-content' );


add_filter(
	'acf/settings/save_json',
	function ( $path ) {
		return WP_CONTENT_DIR . '/acf-json';
	}
);

add_filter(
	'acf/settings/load_json',
	function ( $paths ) {
		$paths[] = WP_CONTENT_DIR . '/acf-json';
		return $paths;
	}
);
