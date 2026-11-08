<?php
/**
 * Title: God's Care hero
 * Slug: pharmacy-child/hero
 * Categories: banner, featured
 * Keywords: hero, pharmacy, homepage
 * Description: Homepage banner with headline, opening-hours badge and two calls to action.
 *
 * @package pharmacy-child
 */
?>
<!-- wp:group {"className":"gcp-hero","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60","left":"var:preset|spacing|40","right":"var:preset|spacing|40"}}},"backgroundColor":"accent-2","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-hero has-accent-2-background-color has-background" style="padding-top:var(--wp--preset--spacing--60);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--60);padding-left:var(--wp--preset--spacing--40)">

	<!-- wp:shortcode -->
	[gcp_open_status]
	<!-- /wp:shortcode -->

	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading"><?php esc_html_e( 'Your health, in caring hands.', 'pharmacy-child' ); ?></h1>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"fontSize":"large"} -->
	<p class="has-large-font-size"><?php esc_html_e( "Genuine medicines, friendly pharmacist advice and everyday health products at God's Care Pharmacy.", 'pharmacy-child' ); ?></p>
	<!-- /wp:paragraph -->

	<!-- wp:buttons -->
	<div class="wp-block-buttons">
		<!-- wp:button -->
		<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/shop/' ) ); ?>"><?php esc_html_e( 'Shop health products', 'pharmacy-child' ); ?></a></div>
		<!-- /wp:button -->
		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/prescription-refill/' ) ); ?>"><?php esc_html_e( 'Request a prescription refill', 'pharmacy-child' ); ?></a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->

</div>
<!-- /wp:group -->
