<?php
/**
 * Public (website) side of the God's Care Pharmacy Manager.
 *
 *  [psm_medicine_search]  Read-only availability search. Customers see a
 *                         label (In stock / Few left / Out of stock /
 *                         Prescription required), never quantities,
 *                         suppliers or batch numbers. Expired stock is ignored.
 *
 *  [psm_booking_form]     Appointment request form. Saves the request in the
 *                         appointments table, emails the pharmacy and the
 *                         customer, and offers WhatsApp for a faster reply.
 *
 * Every database call goes through PSM_DB, which uses prepared statements.
 *
 * @package pharmacy-stock
 */

defined( 'ABSPATH' ) || exit;

class PSM_Public {

	/** Minutes between bookable times. */
	const SLOT_MINUTES = 30;

	/** Days ahead a customer can book. */
	const MAX_DAYS_AHEAD = 60;

	/** Successful requests allowed per visitor (IP) per hour, to stop spam. */
	const RATE_LIMIT = 5;

	/** Stock at or below this shows as "Few left". */
	const FEW_LEFT = 10;

	/** @var string[] Booking errors for the current request. */
	private static $errors = array();

	/** @var array Values the visitor typed, to refill the form after an error. */
	private static $values = array();

	public static function init() {
		add_shortcode( 'psm_medicine_search', array( __CLASS__, 'medicine_search' ) );
		add_shortcode( 'psm_booking_form', array( __CLASS__, 'booking_form' ) );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_assets' ) );
		add_action( 'template_redirect', array( __CLASS__, 'handle_booking' ) );
	}

	public static function register_assets() {
		wp_register_style( 'psm-public', PSM_URL . 'assets/public.css', array(), PSM_VERSION );
		wp_register_script(
			'psm-booking',
			PSM_URL . 'assets/booking.js',
			array(),
			PSM_VERSION,
			array(
				'strategy'  => 'defer',
				'in_footer' => true,
			)
		);
	}

	/* =================================================================
	 * Medicine availability search
	 * ================================================================= */

	/**
	 * @param array $atts compact="1" shows only the search box;
	 *                    action="/url/" sends the search to another page.
	 */
	public static function medicine_search( $atts ) {
		$atts = shortcode_atts(
			array(
				'compact' => '0',
				'action'  => '',
			),
			$atts,
			'psm_medicine_search'
		);
		wp_enqueue_style( 'psm-public' );

		$compact = '1' === (string) $atts['compact'];
		$action  = $atts['action'] ? home_url( $atts['action'] ) : '';
		$term    = isset( $_GET['medicine'] ) ? sanitize_text_field( wp_unslash( $_GET['medicine'] ) ) : '';
		$term    = mb_substr( trim( $term ), 0, 60 );

		ob_start();
		?>
		<div class="psm-search-wrap">
			<form class="psm-search" role="search" method="get" action="<?php echo esc_url( $action ); ?>">
				<label class="psm-label" for="psm-medicine">Medicine name</label>
				<div class="psm-search-row">
					<input type="search" id="psm-medicine" name="medicine" value="<?php echo esc_attr( $term ); ?>" placeholder="e.g. Paracetamol, Amoxicillin, ORS" minlength="2" maxlength="60" autocomplete="off" required>
					<button type="submit" class="psm-btn">Check availability</button>
				</div>
			</form>

			<?php if ( ! $compact ) : ?>
				<?php self::category_chips( $term ); ?>
				<?php if ( '' !== $term ) : ?>
					<?php self::search_results( $term ); ?>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	private static function category_chips( $term ) {
		$base = remove_query_arg( 'medicine' );
		echo '<p class="psm-chips"><span class="psm-chips-label">Browse:</span> ';
		foreach ( PSM_Admin::CATEGORIES as $category ) {
			if ( 'Other' === $category ) {
				continue;
			}
			printf(
				'<a class="psm-chip%s" href="%s">%s</a> ',
				strcasecmp( $term, $category ) === 0 ? ' is-current' : '',
				esc_url( add_query_arg( 'medicine', $category, $base ) ),
				esc_html( $category )
			);
		}
		echo '</p>';
	}

	private static function search_results( $term ) {
		if ( mb_strlen( $term ) < 2 ) {
			echo '<p class="psm-message">Type at least two letters of the medicine name.</p>';
			return;
		}

		$rows = PSM_DB::search_medicines( $term );
		$wa   = psm_setting( 'whatsapp' );
		?>
		<div class="psm-results-wrap" aria-live="polite">
			<?php if ( empty( $rows ) ) : ?>
				<div class="psm-message">
					<p><strong>We could not find "<?php echo esc_html( $term ); ?>" in our list.</strong> We may still be able to get it for you.</p>
					<p><a class="psm-btn psm-btn-wa" href="<?php echo esc_url( self::wa_link( $wa, "Hello God's Care Pharmacy, do you have " . $term . '?' ) ); ?>">Ask us on WhatsApp</a></p>
				</div>
			<?php else : ?>
				<p class="psm-results-count"><?php echo esc_html( sprintf( _n( '%1$d result for "%2$s"', '%1$d results for "%2$s"', count( $rows ), 'pharmacy-stock' ), count( $rows ), $term ) ); ?></p>
				<ul class="psm-results">
					<?php foreach ( $rows as $row ) : ?>
						<?php
						$in_date = (int) $row->in_date;
						$rx      = (int) $row->requires_prescription;
						if ( $in_date <= 0 ) {
							$status = array( 'is-out', 'Out of stock' );
							$cta    = array( 'Ask us to order it', "Hello God's Care Pharmacy, " . $row->name . ' (' . $row->form . ') shows out of stock. Can you order it for me?' );
						} elseif ( $in_date <= self::FEW_LEFT ) {
							$status = array( 'is-low', 'Few left' );
							$cta    = $rx
								? array( 'Send my prescription', "Hello God's Care Pharmacy, I have a prescription for " . $row->name . '. Can I send it?' )
								: array( 'Reserve on WhatsApp', "Hello God's Care Pharmacy, please reserve " . $row->name . ' (' . $row->form . ') for me.' );
						} else {
							$status = array( 'is-in', 'In stock' );
							$cta    = $rx
								? array( 'Send my prescription', "Hello God's Care Pharmacy, I have a prescription for " . $row->name . '. Can I send it?' )
								: array( 'Reserve on WhatsApp', "Hello God's Care Pharmacy, please reserve " . $row->name . ' (' . $row->form . ') for me.' );
						}
						?>
						<li class="psm-result">
							<div class="psm-result-main">
								<span class="psm-result-name"><?php echo esc_html( $row->name ); ?></span>
								<span class="psm-result-meta"><?php echo esc_html( $row->form . ', ' . $row->category ); ?></span>
							</div>
							<div class="psm-result-status">
								<span class="psm-status <?php echo esc_attr( $status[0] ); ?>"><?php echo esc_html( $status[1] ); ?></span>
								<?php if ( $rx ) : ?>
									<span class="psm-status is-rx">Prescription required</span>
								<?php endif; ?>
							</div>
							<a class="psm-result-cta" href="<?php echo esc_url( self::wa_link( $wa, $cta[1] ) ); ?>"><?php echo esc_html( $cta[0] ); ?></a>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<p class="psm-footnote">Availability is updated by our staff and can change during the day. Message us to reserve before you travel. Prescription-only medicines are dispensed only against a valid prescription.</p>
		</div>
		<?php
	}

	/* =================================================================
	 * Appointment booking
	 * ================================================================= */

	/** Process a submitted booking form (runs before the page is drawn). */
	public static function handle_booking() {
		if ( 'POST' !== ( $_SERVER['REQUEST_METHOD'] ?? '' ) || empty( $_POST['psm_booking'] ) ) {
			return;
		}

		$page_url = get_permalink( get_queried_object_id() );
		if ( ! $page_url ) {
			$page_url = home_url( '/' );
		}

		// Spam trap: real people never see or fill this field.
		if ( ! empty( $_POST['psm_website'] ) ) {
			wp_safe_redirect( $page_url );
			exit;
		}

		$v = array(
			'service_id'       => absint( $_POST['service_id'] ?? 0 ),
			'appointment_date' => self::post_text( 'appointment_date' ),
			'appointment_time' => self::post_text( 'appointment_time' ),
			'customer_name'    => mb_substr( self::post_text( 'customer_name' ), 0, 120 ),
			'phone'            => mb_substr( preg_replace( '/[^0-9+\s-]/', '', self::post_text( 'phone' ) ), 0, 30 ),
			'email'            => mb_substr( sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ), 0, 120 ),
			'customer_note'    => mb_substr( sanitize_textarea_field( wp_unslash( $_POST['customer_note'] ?? '' ) ), 0, 500 ),
			'consent'          => ! empty( $_POST['consent'] ),
		);
		self::$values = $v;

		if ( ! isset( $_POST['psm_booking_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['psm_booking_nonce'] ), 'psm_booking' ) ) {
			self::$errors[] = 'This form expired. Please check your details and send it again.';
			return;
		}

		if ( ! psm_setting( 'bookings_open' ) ) {
			self::$errors[] = 'Online booking is paused right now. Please contact us on WhatsApp or by email.';
			return;
		}

		$rate_key = 'psm_rl_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) );
		if ( (int) get_transient( $rate_key ) >= self::RATE_LIMIT ) {
			self::$errors[] = 'We have received several requests from you already. Please contact us on WhatsApp if you need another appointment.';
			return;
		}

		// Validate.
		$service = $v['service_id'] ? PSM_DB::get_service( $v['service_id'] ) : null;
		if ( ! $service || ! (int) $service->is_active ) {
			self::$errors[] = 'Choose the service you would like to book.';
		}
		if ( '' === $v['customer_name'] ) {
			self::$errors[] = 'Enter your name.';
		}
		$digits = strlen( preg_replace( '/\D/', '', $v['phone'] ) );
		if ( $digits < 9 || $digits > 13 ) {
			self::$errors[] = 'Enter a phone number we can call or WhatsApp, for example 0745 546 101.';
		}
		if ( '' !== trim( wp_unslash( $_POST['email'] ?? '' ) ) && ! is_email( $v['email'] ) ) {
			self::$errors[] = 'Check your email address, or leave it empty.';
		}
		if ( ! $v['consent'] ) {
			self::$errors[] = 'Tick the box to agree that we can store your details for this appointment.';
		}
		$slot_error = self::validate_slot( $v['appointment_date'], $v['appointment_time'] );
		if ( $slot_error ) {
			self::$errors[] = $slot_error;
		}
		if ( self::$errors ) {
			return;
		}

		$time = $v['appointment_time'] . ':00';
		if ( PSM_DB::count_slot( $v['appointment_date'], $time ) >= max( 1, (int) psm_setting( 'slot_capacity' ) ) ) {
			self::$errors[] = 'Sorry, that time has just been booked. Please choose another time.';
			return;
		}

		$id = PSM_DB::insert_appointment(
			array(
				'service_id'       => $service->id,
				'customer_name'    => $v['customer_name'],
				'phone'            => $v['phone'],
				'email'            => $v['email'],
				'appointment_date' => $v['appointment_date'],
				'appointment_time' => $time,
				'channel'          => 'website',
				'status'           => 'pending',
				'customer_note'    => $v['customer_note'],
				'staff_note'       => '',
			)
		);
		if ( ! $id ) {
			self::$errors[] = 'We could not save your request. Please try again or contact us on WhatsApp.';
			return;
		}

		set_transient( $rate_key, (int) get_transient( $rate_key ) + 1, HOUR_IN_SECONDS );

		$appointment = PSM_DB::get_appointment( $id );
		self::email_pharmacy( $appointment );
		if ( $appointment->email ) {
			self::email_customer( $appointment, 'received' );
		}

		wp_safe_redirect( add_query_arg( 'booked', psm_reference( $id ), $page_url ) . '#psm-booking' );
		exit;
	}

	/** Booking rules: valid date/time, open that day, not in the past, not too far ahead. */
	private static function validate_slot( $date, $time ) {
		$tz = wp_timezone();
		$dt = DateTimeImmutable::createFromFormat( '!Y-m-d H:i', $date . ' ' . $time, $tz );
		if ( ! $dt || $dt->format( 'Y-m-d H:i' ) !== $date . ' ' . $time ) {
			return 'Choose a date and a time for your appointment.';
		}
		if ( 0 !== (int) $dt->format( 'i' ) % self::SLOT_MINUTES ) {
			return 'Choose one of the times in the list.';
		}

		$now = current_datetime();
		if ( $dt < $now->modify( '+30 minutes' ) ) {
			return 'Choose a time at least 30 minutes from now.';
		}
		if ( $dt > $now->modify( '+' . self::MAX_DAYS_AHEAD . ' days' ) ) {
			return 'You can book up to ' . self::MAX_DAYS_AHEAD . ' days ahead.';
		}

		$hours = psm_opening_hours();
		$day   = strtolower( $dt->format( 'D' ) );
		if ( empty( $hours[ $day ] ) ) {
			return 'We are closed on that day. Please choose another date.';
		}
		$start = self::minutes( $hours[ $day ][0] );
		$end   = self::minutes( $hours[ $day ][1] ) - self::SLOT_MINUTES;
		$at    = self::minutes( $time );
		if ( $at < $start || $at > $end ) {
			return sprintf( 'On %s we take appointments from %s to %s.', $dt->format( 'l' ), self::time_label( $hours[ $day ][0] ), self::time_label( gmdate( 'H:i', $end * 60 ) ) );
		}
		return '';
	}

	/** Render the booking form, or the confirmation after a successful request. */
	public static function booking_form() {
		wp_enqueue_style( 'psm-public' );
		$wa    = psm_setting( 'whatsapp' );
		$email = psm_setting( 'notify_email' );

		ob_start();
		echo '<div id="psm-booking" class="psm-booking-wrap">';

		$ref = isset( $_GET['booked'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_GET['booked'] ) ) ) : '';
		if ( preg_match( '/^GC-\d{4,}$/', $ref ) ) {
			?>
			<div class="psm-success" role="status">
				<h3>Request received</h3>
				<p>Your reference is <strong><?php echo esc_html( $ref ); ?></strong>. Our team will confirm your appointment by phone or WhatsApp, and by email if you gave one.</p>
				<p>Want a faster reply? Send us your reference on WhatsApp.</p>
				<p class="psm-actions">
					<a class="psm-btn psm-btn-wa" href="<?php echo esc_url( self::wa_link( $wa, "Hello God's Care Pharmacy, I just booked appointment " . $ref . '.' ) ); ?>">Send my reference on WhatsApp</a>
					<a class="psm-btn psm-btn-ghost" href="<?php echo esc_url( remove_query_arg( 'booked' ) . '#psm-booking' ); ?>">Book another appointment</a>
				</p>
			</div>
			<?php
			echo '</div>';
			return ob_get_clean();
		}

		$services = PSM_DB::get_services( true );
		if ( ! psm_setting( 'bookings_open' ) || empty( $services ) ) {
			?>
			<div class="psm-message">
				<p><strong>Online booking is paused right now.</strong> You can still book by WhatsApp or email.</p>
				<p class="psm-actions">
					<a class="psm-btn psm-btn-wa" href="<?php echo esc_url( self::wa_link( $wa, "Hello God's Care Pharmacy, I would like to book an appointment." ) ); ?>">Book on WhatsApp</a>
					<?php if ( is_email( $email ) ) : ?>
						<a class="psm-btn psm-btn-ghost" href="<?php echo esc_url( 'mailto:' . $email . '?subject=' . rawurlencode( 'Appointment request' ) ); ?>">Book by email</a>
					<?php endif; ?>
				</p>
			</div>
			<?php
			echo '</div>';
			return ob_get_clean();
		}

		wp_enqueue_script( 'psm-booking' );
		$v     = self::$values;
		$today = current_datetime();
		$hours = psm_opening_hours();
		?>
		<?php if ( self::$errors ) : ?>
			<div class="psm-errors" role="alert">
				<p><strong>Please fix the following:</strong></p>
				<ul>
					<?php foreach ( self::$errors as $error ) : ?>
						<li><?php echo esc_html( $error ); ?></li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<form class="psm-booking" method="post" action="<?php echo esc_url( remove_query_arg( 'booked' ) . '#psm-booking' ); ?>" data-hours="<?php echo esc_attr( wp_json_encode( $hours ) ); ?>" data-slot="<?php echo (int) self::SLOT_MINUTES; ?>" data-today="<?php echo esc_attr( $today->format( 'Y-m-d' ) ); ?>" data-now="<?php echo esc_attr( $today->format( 'H:i' ) ); ?>">
			<input type="hidden" name="psm_booking" value="1">
			<?php wp_nonce_field( 'psm_booking', 'psm_booking_nonce', false ); ?>

			<div class="psm-field">
				<label class="psm-label" for="psm-service">Service</label>
				<select id="psm-service" name="service_id" required>
					<option value="">Choose a service</option>
					<?php foreach ( $services as $s ) : ?>
						<option value="<?php echo (int) $s->id; ?>" <?php selected( (int) ( $v['service_id'] ?? 0 ), (int) $s->id ); ?>>
							<?php echo esc_html( $s->name . ' (' . (int) $s->duration_minutes . ' min)' ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>

			<div class="psm-field-row">
				<div class="psm-field">
					<label class="psm-label" for="psm-date">Date</label>
					<input type="date" id="psm-date" name="appointment_date" value="<?php echo esc_attr( $v['appointment_date'] ?? '' ); ?>" min="<?php echo esc_attr( $today->format( 'Y-m-d' ) ); ?>" max="<?php echo esc_attr( $today->modify( '+' . self::MAX_DAYS_AHEAD . ' days' )->format( 'Y-m-d' ) ); ?>" required>
				</div>
				<div class="psm-field">
					<label class="psm-label" for="psm-time">Time</label>
					<select id="psm-time" name="appointment_time" required>
						<option value="">Choose a time</option>
						<?php foreach ( self::all_slots( $hours ) as $slot ) : ?>
							<option value="<?php echo esc_attr( $slot ); ?>" <?php selected( $v['appointment_time'] ?? '', $slot ); ?>><?php echo esc_html( self::time_label( $slot ) ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
			<p class="psm-help psm-day-hours" aria-live="polite"></p>

			<div class="psm-field">
				<label class="psm-label" for="psm-name">Your name</label>
				<input type="text" id="psm-name" name="customer_name" value="<?php echo esc_attr( $v['customer_name'] ?? '' ); ?>" autocomplete="name" maxlength="120" required>
			</div>

			<div class="psm-field-row">
				<div class="psm-field">
					<label class="psm-label" for="psm-phone">Phone or WhatsApp</label>
					<input type="tel" id="psm-phone" name="phone" value="<?php echo esc_attr( $v['phone'] ?? '' ); ?>" autocomplete="tel" placeholder="0745 546 101" maxlength="30" required>
				</div>
				<div class="psm-field">
					<label class="psm-label" for="psm-email">Email <span class="psm-optional">(optional)</span></label>
					<input type="email" id="psm-email" name="email" value="<?php echo esc_attr( $v['email'] ?? '' ); ?>" autocomplete="email" maxlength="120">
				</div>
			</div>
			<p class="psm-help">Add your email to get a confirmation in your inbox.</p>

			<div class="psm-field">
				<label class="psm-label" for="psm-note">Anything we should know? <span class="psm-optional">(optional)</span></label>
				<textarea id="psm-note" name="customer_note" rows="3" maxlength="500"><?php echo esc_textarea( $v['customer_note'] ?? '' ); ?></textarea>
				<p class="psm-help">Please keep private medical details for when we meet in person.</p>
			</div>

			<div class="psm-hp" aria-hidden="true">
				<label for="psm-website">Leave this field empty</label>
				<input type="text" id="psm-website" name="psm_website" value="" tabindex="-1" autocomplete="off">
			</div>

			<label class="psm-consent">
				<input type="checkbox" name="consent" value="1" <?php checked( ! empty( $v['consent'] ) ); ?> required>
				<span>I agree that God's Care Pharmacy can store these details to manage my appointment. They are not shared with anyone else.</span>
			</label>

			<button type="submit" class="psm-btn psm-btn-block">Request appointment</button>
		</form>
		<?php
		echo '</div>';
		return ob_get_clean();
	}

	/* =================================================================
	 * Emails
	 * ================================================================= */

	/** Tell the pharmacy about a new request. */
	public static function email_pharmacy( $a ) {
		$to = psm_setting( 'notify_email' );
		if ( ! is_email( $to ) ) {
			return false;
		}
		$ref     = psm_reference( $a->id );
		$subject = sprintf( 'New appointment request %s: %s, %s', $ref, $a->service_name, self::when( $a ) );
		$body    = implode(
			"\n",
			array(
				'A new appointment request came in through the website.',
				'',
				'Reference: ' . $ref,
				'Service:   ' . $a->service_name,
				'When:      ' . self::when( $a ),
				'Name:      ' . $a->customer_name,
				'Phone:     ' . $a->phone,
				'WhatsApp:  ' . self::wa_link( psm_whatsapp_number( $a->phone ), 'Hello ' . $a->customer_name . ", this is God's Care Pharmacy about your appointment " . $ref . '.' ),
				'Email:     ' . ( $a->email ? $a->email : '(not given)' ),
				'Note:      ' . ( $a->customer_note ? $a->customer_note : '(none)' ),
				'',
				'Confirm or change it here:',
				admin_url( 'admin.php?page=psm-appointments&action=edit&id=' . (int) $a->id ),
			)
		);
		$headers = array();
		if ( $a->email ) {
			$headers[] = 'Reply-To: ' . str_replace( array( "\r", "\n", '<', '>' ), '', $a->customer_name ) . ' <' . $a->email . '>';
		}
		return wp_mail( $to, $subject, $body, $headers );
	}

	/**
	 * Email the customer.
	 *
	 * @param object $a    Appointment (with service_name).
	 * @param string $type received | confirmed | cancelled
	 */
	public static function email_customer( $a, $type ) {
		if ( ! $a->email || ! is_email( $a->email ) ) {
			return false;
		}
		$ref  = psm_reference( $a->id );
		$site = "God's Care Pharmacy";
		switch ( $type ) {
			case 'confirmed':
				$subject = "Your appointment is confirmed ($ref)";
				$intro   = 'Good news: your appointment is confirmed. We look forward to seeing you.';
				break;
			case 'cancelled':
				$subject = "Your appointment has been cancelled ($ref)";
				$intro   = 'Your appointment has been cancelled. If this is a mistake or you would like a new time, please contact us.';
				break;
			default:
				$subject = "We received your appointment request ($ref)";
				$intro   = 'Thank you. We have received your request and will confirm it shortly by phone or WhatsApp.';
		}

		$body = implode(
			"\n",
			array(
				'Hello ' . $a->customer_name . ',',
				'',
				$intro,
				'',
				'Reference: ' . $ref,
				'Service:   ' . $a->service_name,
				'When:      ' . self::when( $a ),
				'Where:     ' . $site . ', Ruhandiika Street, Fort Portal City',
				'',
				'To change or cancel, reply to this email or WhatsApp us on 0745 546 101.',
				'For an emergency, go to the nearest hospital straight away.',
				'',
				$site,
			)
		);

		$headers = array();
		$reply   = psm_setting( 'notify_email' );
		if ( is_email( $reply ) ) {
			$headers[] = 'Reply-To: ' . $site . ' <' . $reply . '>';
		}
		return wp_mail( $a->email, $subject, $body, $headers );
	}

	/* =================================================================
	 * Helpers
	 * ================================================================= */

	public static function wa_link( $number, $text ) {
		return 'https://wa.me/' . preg_replace( '/\D/', '', (string) $number ) . '?text=' . rawurlencode( $text );
	}

	/** "Tue 12 Nov 2026 at 10:30 am" */
	public static function when( $a ) {
		$ts = strtotime( $a->appointment_date . ' ' . $a->appointment_time );
		return gmdate( 'D j M Y', $ts ) . ' at ' . self::time_label( gmdate( 'H:i', $ts ) );
	}

	public static function time_label( $hhmm ) {
		list( $h, $m ) = array_map( 'intval', explode( ':', $hhmm ) );
		return sprintf( '%d:%02d %s', ( $h % 12 ) ? $h % 12 : 12, $m, $h < 12 ? 'am' : 'pm' );
	}

	private static function minutes( $hhmm ) {
		list( $h, $m ) = array_map( 'intval', explode( ':', $hhmm ) );
		return $h * 60 + $m;
	}

	/** Every bookable time across the week (the browser narrows it to the chosen day). */
	private static function all_slots( $hours ) {
		$first = 24 * 60;
		$last  = 0;
		foreach ( $hours as $day ) {
			if ( $day ) {
				$first = min( $first, self::minutes( $day[0] ) );
				$last  = max( $last, self::minutes( $day[1] ) - self::SLOT_MINUTES );
			}
		}
		$slots = array();
		for ( $t = $first; $t <= $last; $t += self::SLOT_MINUTES ) {
			$slots[] = sprintf( '%02d:%02d', intdiv( $t, 60 ), $t % 60 );
		}
		return $slots;
	}

	private static function post_text( $key ) {
		return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
	}
}
