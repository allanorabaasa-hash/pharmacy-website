<?php
/**
 * Title: God's Care — Contact page
 * Slug: pharmacy-child/page-contact
 * Categories: gods-care
 * Keywords: contact, map, hours, location
 * Post Types: page
 * Block Types: core/post-content
 * Description: Contact details, opening hours with live status, and a map.
 *
 * @package pharmacy-child
 */
?>
<!-- wp:group {"align":"full","className":"gcp-section","backgroundColor":"accent-2","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-section has-accent-2-background-color has-background">
	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading">Contact us</h1>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"fontSize":"large"} -->
	<p class="has-large-font-size">Call, WhatsApp or visit. We are happy to help.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","className":"gcp-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide gcp-section">
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Find us</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>[Street or road]<br>[Nearby landmark]<br>[Town], Uganda</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Talk to us</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p><strong>Phone:</strong> [phone number]<br><strong>WhatsApp:</strong> [WhatsApp number]<br><strong>Email:</strong> info@[yourdomain]</p>
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
			<p>Monday to Saturday: 8:00 am – 9:00 pm<br>Sunday: 10:00 am – 6:00 pm<br>[Confirm with the pharmacy and update the hours in functions.php to match.]</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:heading -->
	<h2 class="wp-block-heading">Map</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"className":"gcp-note"} -->
	<p class="gcp-note">[On Google Maps, search the pharmacy, click Share → Embed a map → Copy HTML. Then replace this paragraph with a Custom HTML block and paste the code.]</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
