<?php
/**
 *
 * Template du header du site.
 *
 * @package BM
 */

?>

<!DOCTYPE html>
<html lang="fr">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
	<header>
		<?php
		$args = array(
			'theme_location' => 'primary',
			'menu_class'     => 'nav-menu',
			'container'      => 'nav',
			'before'         => 'foo',
			'link_before'    => 'bar',
		);
		wp_nav_menu( $args );
		?>
	</header>
<div id="main-content" class="main-content">
