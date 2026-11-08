<?php
/**
 * Title: God's Care footer
 * Slug: pharmacy-child/footer
 * Categories: footer
 * Block Types: core/template-part/footer
 * Inserter: no
 * Description: Dark footer with address, hours, contacts and page links.
 *
 * @package pharmacy-child
 */
?>
<!-- wp:group {"align":"full","className":"gcp-footer","backgroundColor":"accent-3","textColor":"accent-2","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-footer has-accent-2-color has-accent-3-background-color has-text-color has-background">
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"34%"} -->
		<div class="wp-block-column" style="flex-basis:34%">
			<!-- wp:site-title {"level":0,"className":"gcp-brand gcp-brand-light"} /-->
			<!-- wp:paragraph -->
			<p>Genuine medicines, honest advice and everyday health care for families in Fort Portal City.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"className":"gcp-footer-title"} -->
			<h3 class="wp-block-heading gcp-footer-title">Visit</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>Ruhandiika Street<br>Fort Portal City, Uganda</p>
			<!-- /wp:paragraph -->
			<!-- wp:shortcode -->
			[gcp_open_status]
			<!-- /wp:shortcode -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"className":"gcp-footer-title"} -->
			<h3 class="wp-block-heading gcp-footer-title">Talk to us</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p><a href="tel:+256745546101">0745 546 101</a><br><a href="https://wa.me/256745546101">Chat on WhatsApp</a></p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading {"level":3,"className":"gcp-footer-title"} -->
			<h3 class="wp-block-heading gcp-footer-title">Pages</h3>
			<!-- /wp:heading -->
			<!-- wp:list {"className":"gcp-footer-links"} -->
			<ul class="wp-block-list gcp-footer-links">
				<!-- wp:list-item -->
				<li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">About us</a></li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li><a href="<?php echo esc_url( home_url( '/services/' ) ); ?>">Services</a></li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li><a href="<?php echo esc_url( home_url( '/health-tips/' ) ); ?>">Health tips</a></li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact</a></li>
				<!-- /wp:list-item -->
			</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:group {"align":"wide","className":"gcp-footer-bottom","layout":{"type":"flex","flexWrap":"wrap","justifyContent":"space-between"}} -->
	<div class="wp-block-group alignwide gcp-footer-bottom">
		<!-- wp:paragraph {"fontSize":"small"} -->
		<p class="has-small-font-size">© <?php echo esc_html( gmdate( 'Y' ) ); ?> God's Care Pharmacy, Fort Portal City</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"fontSize":"small"} -->
		<p class="has-small-font-size">Information on this site does not replace advice from a doctor or pharmacist.</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->
