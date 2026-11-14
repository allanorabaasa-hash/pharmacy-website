<?php
/**
 * Title: God's Care — Medicine availability page
 * Slug: pharmacy-child/page-medicines
 * Categories: gods-care
 * Keywords: medicine, availability, stock, search
 * Post Types: page
 * Block Types: core/post-content
 * Description: Public, read-only search of the pharmacy's stock (from the Pharmacy Manager plugin).
 *
 * @package pharmacy-child
 */
?>
<!-- wp:group {"align":"full","className":"gcp-page-head","backgroundColor":"accent-2","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-page-head has-accent-2-background-color has-background">
	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading">Do we have your medicine?</h1>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"fontSize":"large"} -->
	<p class="has-large-font-size">Search our shelves before you travel. Results come straight from the stock our team records each day.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"gcp-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-section">
	<!-- wp:shortcode -->
	[psm_medicine_search]
	<!-- /wp:shortcode -->

	<!-- wp:columns {"className":"gcp-legend"} -->
	<div class="wp-block-columns gcp-legend">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:paragraph -->
			<p><strong>In stock</strong><br>Available today. Message us to reserve it.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:paragraph -->
			<p><strong>Few left</strong><br>Limited stock. Reserve it before you come.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:paragraph -->
			<p><strong>Prescription required</strong><br>We need a valid prescription to dispense it.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
