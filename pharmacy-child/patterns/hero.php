<?php
/**
 * Title: God's Care hero
 * Slug: pharmacy-child/hero
 * Categories: banner, featured, gods-care
 * Keywords: hero, pharmacy, homepage
 * Description: Deep green banner with headline, calls to action and a pharmacy dispensing-label card showing live opening status.
 *
 * @package pharmacy-child
 */
?>
<!-- wp:group {"align":"full","className":"gcp-hero","backgroundColor":"accent-3","textColor":"accent-2","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-hero has-accent-2-color has-accent-3-background-color has-text-color has-background">
	<!-- wp:group {"className":"gcp-slides","layout":{"type":"default"}} -->
	<div class="wp-block-group gcp-slides">
		<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
		<figure class="wp-block-image size-full"><img src="<?php echo gcp_img( 'homepage_2.jpg' ); ?>" alt="God's Care Pharmacy, Fort Portal City"/></figure>
		<!-- /wp:image -->
		<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
		<figure class="wp-block-image size-full"><img src="<?php echo gcp_img( 'godscare_reception.jpg' ); ?>" alt="Reception counter at God's Care Pharmacy"/></figure>
		<!-- /wp:image -->
		<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
		<figure class="wp-block-image size-full"><img src="<?php echo gcp_img( 'homepage_3.jpg' ); ?>" alt="Health care at God's Care Pharmacy"/></figure>
		<!-- /wp:image -->
		<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
		<figure class="wp-block-image size-full"><img src="<?php echo gcp_img( 'pharmacy_outlook.jpg' ); ?>" alt="Shop front of God's Care Pharmacy on Ruhandiika Street"/></figure>
		<!-- /wp:image -->
	</div>
	<!-- /wp:group -->

	<!-- wp:columns {"verticalAlignment":"center","align":"wide","className":"gcp-hero-grid"} -->
	<div class="wp-block-columns alignwide are-vertically-aligned-center gcp-hero-grid">
		<!-- wp:column {"verticalAlignment":"center","width":"58%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:58%">
			<!-- wp:paragraph {"className":"gcp-hero-kicker"} -->
			<p class="gcp-hero-kicker">Community pharmacy on Ruhandiika Street, Fort Portal City</p>
			<!-- /wp:paragraph -->

			<!-- wp:heading {"level":1,"className":"gcp-hero-title","textColor":"white"} -->
			<h1 class="wp-block-heading gcp-hero-title has-white-color has-text-color">The right medicine, and the advice to take it well.</h1>
			<!-- /wp:heading -->

			<!-- wp:paragraph {"className":"gcp-hero-lead"} -->
			<p class="gcp-hero-lead">Genuine medicines from licensed suppliers, quick health checks, and a pharmacist who takes the time to explain.</p>
			<!-- /wp:paragraph -->

			<!-- wp:buttons {"className":"gcp-hero-actions"} -->
			<div class="wp-block-buttons gcp-hero-actions">
				<!-- wp:button {"backgroundColor":"white","textColor":"accent-3"} -->
				<div class="wp-block-button"><a class="wp-block-button__link has-accent-3-color has-white-background-color has-text-color has-background wp-element-button" href="<?php echo esc_url( home_url( '/prescription-refill/' ) ); ?>">Request a prescription refill</a></div>
				<!-- /wp:button -->
				<!-- wp:button {"className":"is-style-outline gcp-btn-light"} -->
				<div class="wp-block-button is-style-outline gcp-btn-light"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/shop/' ) ); ?>">Shop health products</a></div>
				<!-- /wp:button -->
			</div>
			<!-- /wp:buttons -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"verticalAlignment":"center","width":"42%"} -->
		<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:42%">
			<!-- wp:group {"className":"gcp-label","layout":{"type":"default"}} -->
			<div class="wp-block-group gcp-label">
				<!-- wp:paragraph {"className":"gcp-label-head"} -->
				<p class="gcp-label-head"><strong>God's Care Pharmacy</strong><span class="gcp-label-rx">Rx</span></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"gcp-label-row"} -->
				<p class="gcp-label-row"><span class="gcp-label-key">Today</span>[gcp_open_status]</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"gcp-label-row"} -->
				<p class="gcp-label-row"><span class="gcp-label-key">Where</span>Ruhandiika Street, Fort Portal City</p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"gcp-label-row"} -->
				<p class="gcp-label-row"><span class="gcp-label-key">Ask us</span><a href="https://wa.me/256745546101">0745 546 101 on WhatsApp</a></p>
				<!-- /wp:paragraph -->

				<!-- wp:paragraph {"className":"gcp-label-foot"} -->
				<p class="gcp-label-foot">Dispensed with care. Keep out of reach of children.</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"gcp-trust","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-trust">
	<!-- wp:columns {"align":"wide"} -->
	<div class="wp-block-columns alignwide">
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:paragraph -->
			<p><strong>Licensed suppliers only</strong><br>No street or unverified stock.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:paragraph -->
			<p><strong>Expiry checked</strong><br>Every batch tracked before it reaches the shelf.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
		<!-- wp:column -->
		<div class="wp-block-column">
			<!-- wp:paragraph -->
			<p><strong>Free pharmacist advice</strong><br>In store, by phone or on WhatsApp.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
