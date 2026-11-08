<?php
/**
 * Title: God's Care — Services page
 * Slug: pharmacy-child/page-services
 * Categories: gods-care
 * Keywords: services, pharmacy, health checks
 * Post Types: page
 * Block Types: core/post-content
 * Description: Grid of pharmacy services with a refill call-to-action.
 *
 * @package pharmacy-child
 */
?>
<!-- wp:group {"align":"full","className":"gcp-section","backgroundColor":"accent-2","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-section has-accent-2-background-color has-background">
	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading">Our services</h1>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"fontSize":"large"} -->
	<p class="has-large-font-size">More than medicine: advice, checks and convenience for your whole family.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"wide","className":"gcp-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide gcp-section">
	<!-- wp:paragraph {"className":"gcp-note"} -->
	<p class="gcp-note">[Keep only the services God's Care actually offers, and add prices only if the pharmacy wants them shown.]</p>
	<!-- /wp:paragraph -->

	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Prescription dispensing</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>We fill prescriptions accurately and explain the dose, timing and side effects.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Pharmacist consultation</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>Talk to our pharmacist about minor illnesses, drug interactions or how to manage a long-term condition.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Blood pressure checks</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>A quick reading with advice on what it means and when to see a doctor.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Blood sugar testing</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>Fast glucose checks for people managing diabetes or worried about their levels.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Prescription refills</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>Send your prescription ahead and collect your medicine without waiting.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3} -->
			<h3 class="wp-block-heading">Delivery</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>[Delivery areas, fee and times, as agreed with the pharmacy.]</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"gcp-section gcp-cta","backgroundColor":"accent-3","textColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-section gcp-cta has-base-color has-accent-3-background-color has-text-color has-background">
	<!-- wp:heading {"textAlign":"center","textColor":"base"} -->
	<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color">Not sure what you need?</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"align":"center"} -->
	<p class="has-text-align-center">Call or WhatsApp our pharmacist. Advice is free.</p>
	<!-- /wp:paragraph -->
	<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"center"}} -->
	<div class="wp-block-buttons">
		<!-- wp:button {"backgroundColor":"base","textColor":"accent-3"} -->
		<div class="wp-block-button"><a class="wp-block-button__link has-accent-3-color has-base-background-color has-text-color has-background wp-element-button" href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">Contact us</a></div>
		<!-- /wp:button -->
	</div>
	<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
