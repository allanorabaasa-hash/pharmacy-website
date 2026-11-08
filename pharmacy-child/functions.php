<?php
/**
 * God's Care Pharmacy child theme functions.
 *
 * @package pharmacy-child
 */

defined( 'ABSPATH' ) || exit;

/*
 * ------------------------------------------------------------------
 * Business details used by the theme.
 * God's Care Pharmacy, Ruhandiika Street, Fort Portal City.
 * TODO: confirm opening hours with the pharmacy.
 * ------------------------------------------------------------------
 */
define( 'GCP_WHATSAPP', '256745546101' ); // 0745 546 101 in international format.

/**
 * URL of a photo the pharmacy uploaded to the Media Library (wp-content/uploads).
 * Used by the page patterns so the same pattern works on localhost and the live domain.
 *
 * @param string $file File name inside the uploads month folder.
 * @param string $month Year/month folder, e.g. 2026/11.
 */
function gcp_img( $file, $month = '2026/11' ) {
	return esc_url( content_url( 'uploads/' . $month . '/' . $file ) );
}

/**
 * Opening hours, 24h format, in the pharmacy's time zone (Africa/Kampala).
 * A day set to null is closed.
 */
function gcp_opening_hours() {
	return array(
		'mon' => array( '08:00', '21:00' ),
		'tue' => array( '08:00', '21:00' ),
		'wed' => array( '08:00', '21:00' ),
		'thu' => array( '08:00', '21:00' ),
		'fri' => array( '08:00', '21:00' ),
		'sat' => array( '08:00', '21:00' ),
		'sun' => array( '10:00', '18:00' ),
	);
}

/**
 * Load the child theme stylesheet and script.
 * (Twenty Twenty-Five already loads its own style.css.)
 */
function gcp_enqueue_assets() {
	$version = wp_get_theme()->get( 'Version' );

	wp_enqueue_style( 'gcp-child', get_stylesheet_uri(), array(), $version );

	wp_enqueue_script(
		'gcp-open-status',
		get_stylesheet_directory_uri() . '/assets/js/open-status.js',
		array(),
		$version,
		array(
			'strategy'  => 'defer',
			'in_footer' => true,
		)
	);
}
add_action( 'wp_enqueue_scripts', 'gcp_enqueue_assets' );

/**
 * Shortcode [gcp_open_status] — shows "Open now" / "Closed" badge.
 * The hours are passed to JavaScript in a data attribute so the badge
 * stays correct even when the page is served from cache.
 */
function gcp_open_status_shortcode() {
	return sprintf(
		'<span class="gcp-open-status" data-hours="%s" data-tz="Africa/Kampala">%s</span>',
		esc_attr( wp_json_encode( gcp_opening_hours() ) ),
		esc_html__( 'Opening hours', 'pharmacy-child' )
	);
}
add_shortcode( 'gcp_open_status', 'gcp_open_status_shortcode' );

/**
 * Floating "Chat on WhatsApp" button on every page.
 */
function gcp_whatsapp_button() {
	$number = preg_replace( '/\D/', '', GCP_WHATSAPP );
	if ( '' === $number ) {
		return;
	}
	$message = rawurlencode( "Hello God's Care Pharmacy, I'd like to ask about..." );
	printf(
		'<a class="gcp-whatsapp" href="%1$s" target="_blank" rel="noopener" aria-label="%2$s">%3$s<span>%2$s</span></a>',
		esc_url( 'https://wa.me/' . $number . '?text=' . $message ),
		esc_html__( 'Chat on WhatsApp', 'pharmacy-child' ),
		'<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.3-.4.7-1.4a.5.5 0 0 0 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.1 5.1 0 0 0 1.1 2.7 11.7 11.7 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6a2.7 2.7 0 0 0 1.8-1.2 2.2 2.2 0 0 0 .1-1.2c0-.1-.2-.2-.4-.3Z"/></svg>'
	);
}
add_action( 'wp_footer', 'gcp_whatsapp_button' );

/**
 * Shortcode blocks inside the header/footer template parts are not passed
 * through the_content, so run shortcodes on them directly.
 */
function gcp_render_shortcode_block( $content ) {
	return do_shortcode( $content );
}
add_filter( 'render_block_core/shortcode', 'gcp_render_shortcode_block' );

/**
 * Group the God's Care page patterns under their own category in the editor.
 */
function gcp_register_pattern_category() {
	register_block_pattern_category(
		'gods-care',
		array( 'label' => __( "God's Care pages", 'pharmacy-child' ) )
	);
}
add_action( 'init', 'gcp_register_pattern_category' );

/**
 * Light hardening: don't advertise the WordPress version in the page source.
 */
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );
