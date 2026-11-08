<?php
/**
 * Title: God's Care — Prescription refill page
 * Slug: pharmacy-child/page-refill
 * Categories: gods-care
 * Keywords: refill, prescription, repeat, whatsapp
 * Post Types: page
 * Block Types: core/post-content
 * Description: How to request a prescription refill on WhatsApp, step by step, with privacy and safety notes.
 *
 * @package pharmacy-child
 */

$gcp_refill_msg = rawurlencode( "Hello God's Care Pharmacy, I would like to request a prescription refill. My name is " );
?>
<!-- wp:group {"align":"full","className":"gcp-page-head","backgroundColor":"accent-2","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-page-head has-accent-2-background-color has-background">
	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading">Prescription refills</h1>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"fontSize":"large"} -->
	<p class="has-large-font-size">Send us your prescription on WhatsApp and we will have your medicine ready when you arrive.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"gcp-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-section">
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"width":"58%"} -->
		<div class="wp-block-column" style="flex-basis:58%">
			<!-- wp:heading -->
			<h2 class="wp-block-heading">How it works</h2>
			<!-- /wp:heading -->
			<!-- wp:list {"ordered":true,"className":"gcp-steps"} -->
			<ol class="wp-block-list gcp-steps">
				<!-- wp:list-item -->
				<li><strong>Take a clear photo of your prescription.</strong> Make sure the medicine name, strength, dose and the prescriber's details can be read.</li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li><strong>Send it to 0745 546 101 on WhatsApp.</strong> Include your full name, a phone number we can call, and when you would like to collect.</li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li><strong>We check and confirm.</strong> Our pharmacy team checks the prescription and stock, then replies with the price and a collection time.</li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li><strong>Collect or ask for delivery.</strong> Pick up your medicine at Ruhandiika Street, or ask us about delivery within Fort Portal City. Bring the original prescription when you collect.</li>
				<!-- /wp:list-item -->
			</ol>
			<!-- /wp:list -->
			<!-- wp:buttons -->
			<div class="wp-block-buttons">
				<!-- wp:button -->
				<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="https://wa.me/256745546101?text=<?php echo esc_attr( $gcp_refill_msg ); ?>">Send my prescription on WhatsApp</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"42%"} -->
		<div class="wp-block-column" style="flex-basis:42%">
			<!-- wp:group {"className":"gcp-card","layout":{"type":"default"}} -->
			<div class="wp-block-group gcp-card">
				<!-- wp:heading {"level":3,"className":"gcp-icon gcp-icon-refill"} -->
				<h3 class="wp-block-heading gcp-icon gcp-icon-refill">Good for regular medicines</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>Refills save time if you take medicine every month, for example for high blood pressure, diabetes, asthma, epilepsy or thyroid conditions.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
			<!-- wp:group {"className":"gcp-card","layout":{"type":"default"}} -->
			<div class="wp-block-group gcp-card">
				<!-- wp:heading {"level":3,"className":"gcp-icon gcp-icon-shield"} -->
				<h3 class="wp-block-heading gcp-icon gcp-icon-shield">Your prescription stays private</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>Only our pharmacy team sees the photo you send. We use it to prepare your medicine and nothing else.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:heading -->
	<h2 class="wp-block-heading">Before you send a refill request</h2>
	<!-- /wp:heading -->
	<!-- wp:list -->
	<ul class="wp-block-list">
		<!-- wp:list-item -->
		<li>Prescription-only medicines are given only against a valid prescription from a qualified health worker.</li>
		<!-- /wp:list-item -->
		<!-- wp:list-item -->
		<li>Order your refill a few days before your medicine runs out, so you never miss a dose.</li>
		<!-- /wp:list-item -->
		<!-- wp:list-item -->
		<li>If your doctor has changed your dose or medicine, send the new prescription, not the old one.</li>
		<!-- /wp:list-item -->
		<!-- wp:list-item -->
		<li>Tell us about any new symptoms or side effects. We may advise you to see your doctor before refilling.</li>
		<!-- /wp:list-item -->
	</ul>
	<!-- /wp:list -->

	<!-- wp:paragraph {"className":"gcp-alert"} -->
	<p class="gcp-alert"><strong>Do not wait for a refill in an emergency.</strong> If you have chest pain, difficulty breathing, a seizure or feel very unwell, go to the nearest hospital straight away.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
