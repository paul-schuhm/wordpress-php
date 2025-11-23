<?php
/**
 *
 * Template pour lister les best sellers
 *
 * @package BM
 */

?>

<?php
$is_sale_period = false;
get_header( $is_sale_period ? 'sale' : '' );
?>

<div id="primary" class="content-area">
	
<?php
if ( have_posts() ) :
	while ( have_posts() ) :
		the_post();
		?>
		<a href="<?php the_permalink(); ?>">
			<?php the_title( '<h2>', '</h2>' ); ?>
		</a>
		<?php
endwhile;
endif;

?>

</div>

<?php
get_footer();
