<?php
/**
 *
 * Template pour afficher une actualité (post)
 *
 * @package BM
 */

?>

<?php

get_header();

if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();

		?>

<main id="<?php the_ID(); ?>" <?php post_class( 'main-news' ); ?>>
		<?php the_title( '<h1>', '</h1>' ); ?>
		<?php the_content(); ?>
</main>

		<?php
endwhile;
endif;

get_footer();
