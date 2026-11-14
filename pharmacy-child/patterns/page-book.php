<?php
/**
 * Title: God's Care — Book an appointment page
 * Slug: pharmacy-child/page-book
 * Categories: gods-care
 * Keywords: book, appointment, booking, health check
 * Post Types: page
 * Block Types: core/post-content
 * Description: Online booking form (from the Pharmacy Manager plugin) with WhatsApp and email alternatives.
 *
 * @package pharmacy-child
 */

$gcp_wa_text   = rawurlencode( "Hello God's Care Pharmacy, I would like to book an appointment for " );
$gcp_mail_body = rawurlencode( "Hello God's Care Pharmacy,\n\nI would like to book an appointment.\n\nService:\nPreferred date and time:\nMy name:\nMy phone number:\n" );
?>
<!-- wp:group {"align":"full","className":"gcp-page-head","backgroundColor":"accent-2","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-page-head has-accent-2-background-color has-background">
	<!-- wp:heading {"level":1} -->
	<h1 class="wp-block-heading">Book an appointment</h1>
	<!-- /wp:heading -->
	<!-- wp:paragraph {"fontSize":"large"} -->
	<p class="has-large-font-size">Choose a time for a health check or a private chat with our pharmacist. We will confirm by phone, WhatsApp or email.</p>
	<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->

<!-- wp:group {"align":"full","className":"gcp-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gcp-section">
	<!-- wp:columns {"align":"wide","className":"gcp-book-grid"} -->
	<div class="wp-block-columns alignwide gcp-book-grid">
		<!-- wp:column {"width":"60%"} -->
		<div class="wp-block-column" style="flex-basis:60%">
			<!-- wp:group {"className":"gcp-card gcp-form-card","layout":{"type":"default"}} -->
			<div class="wp-block-group gcp-card gcp-form-card">
				<!-- wp:heading {"level":2,"className":"gcp-form-title"} -->
				<h2 class="wp-block-heading gcp-form-title">Request a time online</h2>
				<!-- /wp:heading -->
				<!-- wp:shortcode -->
				[psm_booking_form]
				<!-- /wp:shortcode -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:column -->

		<!-- wp:column {"width":"40%"} -->
		<div class="wp-block-column" style="flex-basis:40%">
			<!-- wp:group {"className":"gcp-card","layout":{"type":"default"}} -->
			<div class="wp-block-group gcp-card">
				<!-- wp:heading {"level":3,"className":"gcp-icon gcp-icon-consult"} -->
				<h3 class="wp-block-heading gcp-icon gcp-icon-consult">Prefer WhatsApp or email?</h3>
				<!-- /wp:heading -->
				<!-- wp:paragraph -->
				<p>Send us the service you need and a time that suits you. We will reply and add your booking to our appointment list.</p>
				<!-- /wp:paragraph -->
				<!-- wp:buttons {"className":"gcp-stack-buttons"} -->
				<div class="wp-block-buttons gcp-stack-buttons">
					<!-- wp:button {"className":"gcp-btn-wa"} -->
					<div class="wp-block-button gcp-btn-wa"><a class="wp-block-button__link wp-element-button" href="https://wa.me/256745546101?text=<?php echo esc_attr( $gcp_wa_text ); ?>">Book on WhatsApp</a></div>
					<!-- /wp:button -->
					<!-- wp:button {"className":"is-style-outline"} -->
					<div class="wp-block-button is-style-outline"><a class="wp-block-button__link wp-element-button" href="mailto:orallan19@gmail.com?subject=<?php echo esc_attr( rawurlencode( 'Appointment request' ) ); ?>&amp;body=<?php echo esc_attr( $gcp_mail_body ); ?>">Book by email</a></div>
					<!-- /wp:button -->
				</div>
				<!-- /wp:buttons -->
				<!-- wp:paragraph {"fontSize":"small"} -->
				<p class="has-small-font-size">WhatsApp: 0745 546 101<br>Email: orallan19@gmail.com</p>
				<!-- /wp:paragraph -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"gcp-card","layout":{"type":"default"}} -->
			<div class="wp-block-group gcp-card">
				<!-- wp:heading {"level":3,"className":"gcp-icon gcp-icon-shield"} -->
				<h3 class="wp-block-heading gcp-icon gcp-icon-shield">What to expect</h3>
				<!-- /wp:heading -->
				<!-- wp:list {"className":"gcp-checklist"} -->
				<ul class="wp-block-list gcp-checklist">
					<!-- wp:list-item -->
					<li>Most checks take 10 to 20 minutes.</li>
					<!-- /wp:list-item -->
					<!-- wp:list-item -->
					<li>Bring the medicines you already take and any recent test results.</li>
					<!-- /wp:list-item -->
					<!-- wp:list-item -->
					<li>For a blood sugar test, ask us whether you should come before eating.</li>
					<!-- /wp:list-item -->
					<!-- wp:list-item -->
					<li>Can't make it? Reply to your confirmation or WhatsApp us to move or cancel.</li>
					<!-- /wp:list-item -->
				</ul>
				<!-- /wp:list -->
			</div>
			<!-- /wp:group -->

			<!-- wp:paragraph {"className":"gcp-alert"} -->
			<p class="gcp-alert"><strong>Not for emergencies.</strong> For chest pain, difficulty breathing, a seizure or severe bleeding, go to the nearest hospital straight away.</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:column -->
	</div>
	<!-- /wp:columns -->
</div>
<!-- /wp:group -->
