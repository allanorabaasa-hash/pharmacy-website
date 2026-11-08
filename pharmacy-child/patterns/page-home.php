<?php
/**
 * Title: God's Care — Home page
 * Slug: pharmacy-child/page-home
 * Categories: gods-care
 * Keywords: home, homepage, pharmacy
 * Post Types: page
 * Block Types: core/post-content
 * Description: Full homepage: hero, services, why choose us, medicine safety tips, refill call-to-action, visit us and latest health tips.
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
	<p class="has-text-align-center">Walk in to our pharmacy on Ruhandiika Street, call, or message us on WhatsApp. Our team helps you get the right medicine and use it safely.</p>
	<!-- /wp:paragraph -->

	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3,"className":"gcp-icon gcp-icon-rx"} -->
			<h3 class="wp-block-heading gcp-icon gcp-icon-rx">Prescriptions</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>We dispense your doctor's prescription accurately and explain the dose, timing and possible side effects before you leave.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3,"className":"gcp-icon gcp-icon-bp"} -->
			<h3 class="wp-block-heading gcp-icon gcp-icon-bp">Health checks</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>Quick blood pressure, blood sugar and malaria rapid tests, with clear advice on what your results mean and when to see a doctor.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"className":"gcp-card"} -->
		<div class="wp-block-column gcp-card">
			<!-- wp:heading {"level":3,"className":"gcp-icon gcp-icon-family"} -->
			<h3 class="wp-block-heading gcp-icon gcp-icon-family">Everyday health products</h3>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>Vitamins and supplements, first aid supplies, baby and mother care, oral rehydration salts, mosquito repellents and personal care.</p>
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
		<!-- wp:column {"verticalAlignment":"center","width":"46%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:46%">
			<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"gcp-photo gcp-photo-tall"} -->
			<figure class="wp-block-image size-full gcp-photo gcp-photo-tall"><img src="<?php echo gcp_img( 'godscare_reception.jpg' ); ?>" alt="Reception counter at God's Care Pharmacy, Fort Portal"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"54%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:54%">
			<!-- wp:heading -->
			<h2 class="wp-block-heading">Why families in Fort Portal choose God's Care</h2>
			<!-- /wp:heading -->
			<!-- wp:paragraph -->
			<p>Medicine only helps when it is genuine, stored correctly and taken the right way. We take care of all three, so you can focus on getting better.</p>
			<!-- /wp:paragraph -->
			<!-- wp:list {"className":"gcp-checklist"} -->
			<ul class="wp-block-list gcp-checklist">
				<!-- wp:list-item -->
				<li>Genuine medicines from licensed suppliers</li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li>Every batch checked for expiry before it reaches the shelf</li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li>Clear advice on dosage, side effects and drug interactions</li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li>Private, respectful service and fair prices</li>
				<!-- /wp:list-item -->
				<!-- wp:list-item -->
				<li>Quick answers on WhatsApp: 0745 546 101</li>
				<!-- /wp:list-item -->
			</ul>
			<!-- /wp:list -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"gcp-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-section">
	<!-- wp:heading {"textAlign":"center"} -->
	<h2 class="wp-block-heading has-text-align-center">Use your medicine safely</h2>
	<!-- /wp:heading -->

	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:paragraph {"className":"gcp-tip"} -->
			<p class="gcp-tip"><strong>Finish your antibiotics.</strong> Take the full course even when you feel better. Stopping early helps germs become resistant.</p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"gcp-tip"} -->
			<p class="gcp-tip"><strong>Never share prescriptions.</strong> A medicine that helped someone else may be wrong or dangerous for you.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:paragraph {"className":"gcp-tip"} -->
			<p class="gcp-tip"><strong>Store medicine properly.</strong> Keep it in a cool, dry place away from sunlight and out of children's reach.</p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"gcp-tip"} -->
			<p class="gcp-tip"><strong>Check the expiry date.</strong> Bring expired or unused medicine to us for safe disposal instead of throwing it away.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->

	<!-- wp:paragraph {"className":"gcp-alert"} -->
	<p class="gcp-alert"><strong>Go to a hospital immediately</strong> for chest pain, difficulty breathing, a seizure, severe bleeding, a child who cannot drink or is unusually sleepy, or a high fever with confusion. A pharmacy cannot replace emergency care.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"gcp-section gcp-cta","backgroundColor":"accent-3","textColor":"base","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-section gcp-cta has-base-color has-accent-3-background-color has-text-color has-background">
	<!-- wp:heading {"textAlign":"center","textColor":"base"} -->
	<h2 class="wp-block-heading has-text-align-center has-base-color has-text-color">Running low on your regular medicine?</h2>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"align":"center"} -->
	<p class="has-text-align-center">For blood pressure, diabetes, asthma or other long-term treatment, send us your prescription and we will have your refill ready when you arrive.</p>
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
			<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"gcp-photo"} -->
			<figure class="wp-block-image size-full gcp-photo"><img src="<?php echo gcp_img( 'pharmacy_outlook.jpg' ); ?>" alt="Shop front of God's Care Pharmacy on Ruhandiika Street, Fort Portal City"/></figure>
			<!-- /wp:image -->
			<!-- wp:heading -->
			<h2 class="wp-block-heading">Visit us</h2>
			<!-- /wp:heading -->
			<!-- wp:shortcode -->
			[gcp_open_status]
			<!-- /wp:shortcode -->
			<!-- wp:paragraph -->
			<p><strong>Address:</strong> Ruhandiika Street, Fort Portal City<br><strong>Phone / WhatsApp:</strong> 0745 546 101</p>
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
