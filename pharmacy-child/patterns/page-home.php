<?php
/**
 * Title: God's Care — Home page
 * Slug: pharmacy-child/page-home
 * Categories: gods-care
 * Keywords: home, homepage, pharmacy
 * Post Types: page
 * Block Types: core/post-content
 * Description: Full homepage: hero, services, why choose us, refill call-to-action, visit us and latest health tips.
 *
 * @package pharmacy-child
 */
?>
<!-- wp:pattern {"slug":"pharmacy-child/hero"} /-->

<!-- wp:group {"align":"full","className":"gcp-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-section">
	<!-- wp:heading {"textAlign":"center"} -->
	<h2 class="wp-block-heading has-text-align-center">How we can help</h2>
	<!-- /wp:heading -->

	<!-- wp:paragraph {"align":"center"} -->
	<p class="has-text-align-center">Walk in, call, or message us on WhatsApp. Our pharmacist is here to help you get the right medicine and use it safely.</p>
	<!-- /wp:paragraph -->

	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Prescriptions</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>Bring or send your doctor's prescription and we prepare it, explain the dose and answer your questions.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Health checks</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>Quick blood pressure and blood sugar checks, with advice on what your numbers mean.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Everyday health products</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>Vitamins, first aid, baby care and personal care products, available in store and online.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"className":"is-style-outline"} -->
		<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/services/' ) ); ?>">See all services</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"gcp-section","backgroundColor":"accent-5","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-section has-accent-5-background-color has-background">
	<!-- wp:columns {"verticalAlignment":"center","align":"wide"} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center">
		<!-- wp:column {"verticalAlignment":"center"} -->
		<div class="wp-block-column is-vertically-aligned-center">
			<!-- wp:heading -->
			<h2 class="wp-block-heading">Why choose God's Care</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>[Replace with the pharmacy's own words from the client meeting: what makes it different from others nearby.]</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center"} -->
		<div class="wp-block-column is-vertically-aligned-center">
			<!-- wp:list {"className":"gcp-checklist"} -->
			<ul class="wp-block-list gcp-checklist">
				<!-- wp:list-item -->
				<li>Genuine medicines from trusted suppliers</li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li>Advice from a qualified pharmacist</li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li>Expiry dates checked on every batch</li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li>Friendly service and fair prices</li>
				<!-- /wp:list-item -->
			</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"gcp-section gcp-cta","backgroundColor":"accent-3","textColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-section gcp-cta has-base-color has-accent-3-background-color has-text-color has-background">
	<!-- wp:heading {"textAlign":"center","textColor":"base"} -->
	<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color">Running low on your regular medicine?</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"align":"center"} -->
	<p class="has-text-align-center">Send us your prescription and we will have your refill ready when you arrive.</p>
	<!-- /wp:paragraph -->
	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"backgroundColor":"base","textColor":"accent-3"} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-accent-3-color has-base-background-color has-text-color has-background wp-element-button" href="<?php echo esc_url( home_url( '/prescription-refill/' ) ); ?>">Request a refill</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"gcp-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-section">
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading -->
			<h2 class="wp-block-heading">Visit us</h2>
			<!-- /wp:heading -->
			<!-- wp:shortcode -->
			[gcp_open_status]
			<!-- /wp:shortcode -->
			<!-- wp:paragraph -->
			<p><strong>Address:</strong> [street, landmark, town]<br><strong>Phone:</strong> [phone number]<br><strong>WhatsApp:</strong> [WhatsApp number]</p>
			<!-- /wp:paragraph -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Directions and contacts</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:heading -->
			<h2 class="wp-block-heading">Latest health tips</h2>
			<!-- /wp:heading -->
			<!-- wp:latest-posts {"postsToShow":3,"displayPostDate":true} /-->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
