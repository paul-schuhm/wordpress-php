<?php
/**
 *
 * Template pour lister les actualités.
 *
 * @package BM
 */

?>

<?php
$is_sale_period = true;
get_header( $is_sale_period ? 'sale' : '' );

get_footer();
