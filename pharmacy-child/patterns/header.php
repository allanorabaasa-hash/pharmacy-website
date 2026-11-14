<?php
/**
 * Title: God's Care header
 * Slug: pharmacy-child/header
 * Categories: header
 * Block Types: core/template-part/header
 * Inserter: no
 * Description: Site header with pharmacy mark, menu and WhatsApp button.
 *
 * @package pharmacy-child
 */
?>
<!-- wp:group {"align":"full","className":"gcp-header","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-header">
	<!-- wp:group {"align":"wide","className":"gcp-header-inner","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignwide gcp-header-inner">
		<!-- wp:site-title {"level":0,"className":"gcp-brand"} /-->

		<!-- wp:group {"className":"gcp-header-actions","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"right"}} -->
		<div class="wp-block-group gcp-header-actions">
			<!-- wp:navigation {"overlayBackgroundColor":"base","overlayTextColor":"contrast","className":"gcp-nav","layout":{"type":"flex","justifyContent":"right","flexWrap":"nowrap"}} -->
				<!-- wp:navigation-link {"label":"Home","url":"<?php echo esc_url( home_url( '/' ) ); ?>","kind":"custom"} /-->
				<!-- wp:navigation-link {"label":"About","url":"<?php echo esc_url( home_url( '/about/' ) ); ?>","kind":"custom"} /-->
				<!-- wp:navigation-link {"label":"Services","url":"<?php echo esc_url( home_url( '/services/' ) ); ?>","kind":"custom"} /-->
				<!-- wp:navigation-link {"label":"Medicines","url":"<?php echo esc_url( home_url( '/medicine-availability/' ) ); ?>","kind":"custom"} /-->
				<!-- wp:navigation-link {"label":"Health Tips","url":"<?php echo esc_url( home_url( '/health-tips/' ) ); ?>","kind":"custom"} /-->
				<!-- wp:navigation-link {"label":"Contact","url":"<?php echo esc_url( home_url( '/contact/' ) ); ?>","kind":"custom"} /-->
			<!-- /wp:navigation -->

			<!-- wp:buttons {"className":"gcp-header-cta"} -->
			<div class="wp-block-buttons gcp-header-cta">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/book-appointment/' ) ); ?>">Book appointment</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
