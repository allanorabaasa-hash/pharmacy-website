<?php
/**
 * Title: God's Care — Contact page
 * Slug: pharmacy-child/page-contact
 * Categories: gods-care
 * Keywords: contact, map, hours, location
 * Post Types: page
 * Block Types: core/post-content
 * Description: Contact details, opening hours with live status, and a map of Ruhandiika Street, Fort Portal.
 *
 * @package pharmacy-child
 */
?>
<!-- wp:group {"align":"full","className":"gcp-page-head","backgroundColor":"accent-2","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-page-head has-accent-2-background-color has-background">
	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading">Contact us</h1>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"fontSize":"large"} -->
	<p class="has-large-font-size">Call, WhatsApp or visit us on Ruhandiika Street. We are happy to help.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","className":"gcp-section","layout":{"type":"default"}} -->
<div class="wp-block-group alignwide gcp-section">
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Find us</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>God's Care Pharmacy<br>Ruhandiika Street<br>Fort Portal City, Uganda</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Talk to us</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p><strong>Phone:</strong> <a href="tel:+256745546101">0745 546 101</a><br><strong>WhatsApp:</strong> <a href="https://wa.me/256745546101">0745 546 101</a></p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"fontSize":"small"} -->
			<p class="has-small-font-size">For emergencies such as difficulty breathing, chest pain or severe bleeding, go straight to the nearest hospital.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Opening hours</h3>
			<!-- /wp:heading -->
			<!-- wp:shortcode -->
			[gcp_open_status]
			<!-- /wp:shortcode -->
			<!-- wp:paragraph -->
			<p>Monday to Saturday: 8:00 am – 9:00 pm<br>Sunday: 10:00 am – 6:00 pm</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:heading -->
	<h2 class="wp-block-heading">How to find us</h2>
	<!-- /wp:heading -->
	<!-- wp:html -->
	<div class="gcp-map"><iframe title="Map of Ruhandiika Street, Fort Portal City" src="https://www.google.com/maps?q=Ruhandiika+Street,+Fort+Portal,+Uganda&amp;output=embed" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe></div>
	<!-- /wp:html -->
</div>
<!-- /wp:group -->
