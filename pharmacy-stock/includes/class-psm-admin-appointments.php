<?php
/**
 * wp-admin screens for appointments, bookable services and module settings.
 *
 * Reuses the helpers of PSM_Admin (fields, notices, redirects) and follows
 * the same security rules: capability check, nonce check, sanitised input,
 * escaped output.
 *
 * @package pharmacy-stock
 */

defined( 'ABSPATH' ) || exit;

class PSM_Admin_Appointments extends PSM_Admin {

	const STATUSES = array(
		'pending'   => 'Waiting',
		'confirmed' => 'Confirmed',
		'completed' => 'Done',
		'cancelled' => 'Cancelled',
	);

	const CHANNELS = array(
		'website'  => 'Website',
		'whatsapp' => 'WhatsApp',
		'phone'    => 'Phone call',
		'email'    => 'Email',
		'walk-in'  => 'Walk-in',
	);

	/** Days to keep finished appointments before "Delete old appointments" removes them. */
	const KEEP_DAYS = 180;

	// Deliberately does not call parent::__construct(): the parent registers its own hooks.
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_pages' ), 20 );
		add_action( 'admin_post_psm_appt_save', array( $this, 'save_appointment' ) );
		add_action( 'admin_post_psm_appt_status', array( $this, 'change_status' ) );
		add_action( 'admin_post_psm_appt_delete', array( $this, 'delete_appointment' ) );
		add_action( 'admin_post_psm_service_save', array( $this, 'save_service' ) );
		add_action( 'admin_post_psm_service_delete', array( $this, 'delete_service' ) );
		add_action( 'admin_post_psm_settings_save', array( $this, 'save_settings' ) );
		add_action( 'admin_post_psm_purge', array( $this, 'purge' ) );
	}

	public function register_pages() {
		add_submenu_page( 'psm-overview', 'Appointments', 'Appointments', self::CAP, 'psm-appointments', array( $this, 'page_appointments' ), 1 );
		add_submenu_page( 'psm-overview', 'Services', 'Services', self::CAP, 'psm-services', array( $this, 'page_services' ) );
		add_submenu_page( 'psm-overview', 'Settings', 'Settings', 'manage_options', 'psm-settings', array( $this, 'page_settings' ) );
	}

	/* =================================================================
	 * Appointments: handlers
	 * ================================================================= */

	public function save_appointment() {
		$this->guard( 'psm_appt_save' );
		$id = absint( $_POST['id'] ?? 0 );

		$time = $this->text( 'appointment_time' );
		$data = array(
			'service_id'       => absint( $_POST['service_id'] ?? 0 ),
			'customer_name'    => mb_substr( $this->text( 'customer_name' ), 0, 120 ),
			'phone'            => mb_substr( preg_replace( '/[^0-9+\s-]/', '', $this->text( 'phone' ) ), 0, 30 ),
			'email'            => mb_substr( sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ), 0, 120 ),
			'appointment_date' => $this->date( 'appointment_date' ),
			'appointment_time' => preg_match( '/^([01]\d|2[0-3]):[0-5]\d$/', $time ) ? $time . ':00' : '',
			'channel'          => $this->key_choice( 'channel', self::CHANNELS, 'phone' ),
			'status'           => $this->key_choice( 'status', self::STATUSES, 'pending' ),
			'customer_note'    => mb_substr( sanitize_textarea_field( wp_unslash( $_POST['customer_note'] ?? '' ) ), 0, 500 ),
			'staff_note'       => mb_substr( sanitize_textarea_field( wp_unslash( $_POST['staff_note'] ?? '' ) ), 0, 500 ),
		);

		if ( ! $data['service_id'] || ! PSM_DB::get_service( $data['service_id'] ) || '' === $data['customer_name']
			|| '' === $data['phone'] || '' === $data['appointment_date'] || '' === $data['appointment_time'] ) {
			$this->redirect( 'psm-appointments', 'required', $id );
		}

		if ( in_array( $data['status'], array( 'pending', 'confirmed' ), true )
			&& PSM_DB::count_slot( $data['appointment_date'], $data['appointment_time'], $id ) >= max( 1, (int) psm_setting( 'slot_capacity' ) ) ) {
			$this->redirect( 'psm-appointments', 'taken', $id );
		}

		$before = $id ? PSM_DB::get_appointment( $id ) : null;
		$result = $id ? PSM_DB::update_appointment( $id, $data ) : PSM_DB::insert_appointment( $data );
		if ( false === $result ) {
			$this->redirect( 'psm-appointments', 'error', $id );
		}

		// Tell the customer when an edit confirms or cancels their booking.
		$saved_id = $id ? $id : (int) $result;
		if ( $before && $before->status !== $data['status'] && in_array( $data['status'], array( 'confirmed', 'cancelled' ), true ) ) {
			PSM_Public::email_customer( PSM_DB::get_appointment( $saved_id ), $data['status'] );
		}

		$this->redirect( 'psm-appointments', $id ? 'updated' : 'added' );
	}

	public function change_status() {
		if ( ! current_user_can( self::CAP ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'pharmacy-stock' ), 403 );
		}
		$id = absint( $_GET['id'] ?? 0 );
		$to = sanitize_key( $_GET['to'] ?? '' );
		check_admin_referer( 'psm_appt_status_' . $id . '_' . $to );

		if ( ! isset( self::STATUSES[ $to ] ) || ! PSM_DB::get_appointment( $id ) ) {
			$this->redirect( 'psm-appointments', 'error' );
		}
		PSM_DB::set_appointment_status( $id, $to );

		$emailed = false;
		if ( in_array( $to, array( 'confirmed', 'cancelled' ), true ) ) {
			$emailed = PSM_Public::email_customer( PSM_DB::get_appointment( $id ), $to );
		}
		$this->redirect( 'psm-appointments', $emailed ? 'emailed' : $to );
	}

	public function delete_appointment() {
		if ( ! current_user_can( self::CAP ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'pharmacy-stock' ), 403 );
		}
		$id = absint( $_GET['id'] ?? 0 );
		check_admin_referer( 'psm_appt_delete_' . $id );
		$result = PSM_DB::delete_appointment( $id );
		$this->redirect( 'psm-appointments', false === $result ? 'error' : 'deleted' );
	}

	/* =================================================================
	 * Services: handlers
	 * ================================================================= */

	public function save_service() {
		$this->guard( 'psm_service_save' );
		$id    = absint( $_POST['id'] ?? 0 );
		$price = trim( $this->text( 'price' ) );
		$data  = array(
			'name'             => mb_substr( $this->text( 'name' ), 0, 120 ),
			'description'      => mb_substr( $this->text( 'description' ), 0, 255 ),
			'duration_minutes' => min( 240, max( 5, absint( $_POST['duration_minutes'] ?? 15 ) ) ),
			'price'            => '' === $price ? '' : (string) max( 0, round( (float) $price, 2 ) ),
			'is_active'        => empty( $_POST['is_active'] ) ? 0 : 1,
		);
		if ( '' === $data['name'] ) {
			$this->redirect( 'psm-services', 'required', $id );
		}

		global $wpdb;
		$wpdb->suppress_errors( true );
		$result = $id ? PSM_DB::update_service( $id, $data ) : PSM_DB::insert_service( $data );
		if ( false === $result ) {
			$this->redirect( 'psm-services', 'duplicate', $id );
		}
		$this->redirect( 'psm-services', $id ? 'updated' : 'added' );
	}

	public function delete_service() {
		if ( ! current_user_can( self::CAP ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'pharmacy-stock' ), 403 );
		}
		$id = absint( $_GET['id'] ?? 0 );
		check_admin_referer( 'psm_service_delete_' . $id );

		global $wpdb;
		$wpdb->suppress_errors( true );
		$result = PSM_DB::delete_service( $id ); // The foreign key refuses if appointments use it.
		$this->redirect( 'psm-services', false === $result ? 'svc_used' : 'deleted' );
	}

	/* =================================================================
	 * Settings: handlers
	 * ================================================================= */

	public function save_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'pharmacy-stock' ), 403 );
		}
		check_admin_referer( 'psm_settings_save' );

		$email = sanitize_email( wp_unslash( $_POST['notify_email'] ?? '' ) );
		update_option(
			'psm_settings',
			array(
				'notify_email'  => is_email( $email ) ? $email : psm_setting( 'notify_email' ),
				'whatsapp'      => psm_whatsapp_number( $this->text( 'whatsapp' ) ),
				'slot_capacity' => min( 10, max( 1, absint( $_POST['slot_capacity'] ?? 1 ) ) ),
				'bookings_open' => empty( $_POST['bookings_open'] ) ? 0 : 1,
			)
		);
		wp_safe_redirect( admin_url( 'admin.php?page=psm-settings&psm_msg=saved' ) );
		exit;
	}

	public function purge() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'pharmacy-stock' ), 403 );
		}
		check_admin_referer( 'psm_purge' );
		PSM_DB::purge_appointments( self::KEEP_DAYS );
		wp_safe_redirect( admin_url( 'admin.php?page=psm-settings&psm_msg=purged' ) );
		exit;
	}

	/* =================================================================
	 * Pages
	 * ================================================================= */

	public function page_appointments() {
		$editing  = ( isset( $_GET['action'], $_GET['id'] ) && 'edit' === $_GET['action'] ) ? PSM_DB::get_appointment( absint( $_GET['id'] ) ) : null;
		$views    = array(
			'upcoming' => 'Upcoming',
			'today'    => 'Today',
			'pending'  => 'Waiting for confirmation',
			'past'     => 'Past',
			'all'      => 'All',
		);
		$view     = isset( $_GET['view'] ) && isset( $views[ $_GET['view'] ] ) ? sanitize_key( $_GET['view'] ) : 'upcoming';
		$rows     = PSM_DB::get_appointments( $view );
		$services = PSM_DB::get_services();
		$base     = admin_url( 'admin.php?page=psm-appointments' );
		?>
		<div class="wrap psm">
			<h1>Appointments</h1>
			<?php $this->notice(); ?>
			<p class="description">Website bookings arrive here automatically. Add bookings made by WhatsApp, phone, email or walk-in with the form, so every appointment is in one list.</p>

			<div class="psm-layout">
				<div class="psm-form">
					<h2><?php echo $editing ? esc_html( 'Edit ' . psm_reference( $editing->id ) ) : 'Add appointment'; ?></h2>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="psm_appt_save">
						<input type="hidden" name="id" value="<?php echo (int) ( $editing->id ?? 0 ); ?>">
						<?php wp_nonce_field( 'psm_appt_save' ); ?>

						<p><label for="psm-service_id">Service *</label>
							<select id="psm-service_id" name="service_id" required>
								<option value="">— Select —</option>
								<?php foreach ( $services as $s ) : ?>
									<option value="<?php echo (int) $s->id; ?>" <?php selected( (int) ( $editing->service_id ?? 0 ), (int) $s->id ); ?>><?php echo esc_html( $s->name . ( (int) $s->is_active ? '' : ' (hidden)' ) ); ?></option>
								<?php endforeach; ?>
							</select></p>
						<?php $this->field( 'customer_name', 'Customer name *', $editing->customer_name ?? '', 'text', true ); ?>
						<?php $this->field( 'phone', 'Phone / WhatsApp *', $editing->phone ?? '', 'tel', true ); ?>
						<?php $this->field( 'email', 'Email', $editing->email ?? '', 'email' ); ?>
						<?php $this->field( 'appointment_date', 'Date *', $editing->appointment_date ?? current_time( 'Y-m-d' ), 'date', true ); ?>
						<?php $this->field( 'appointment_time', 'Time *', isset( $editing->appointment_time ) ? substr( $editing->appointment_time, 0, 5 ) : '', 'time', true ); ?>
						<?php $this->key_select( 'channel', 'Booked through', self::CHANNELS, $editing->channel ?? 'whatsapp' ); ?>
						<?php $this->key_select( 'status', 'Status', self::STATUSES, $editing->status ?? 'confirmed' ); ?>
						<p><label for="psm-customer_note">Customer's note</label>
							<textarea id="psm-customer_note" name="customer_note" rows="2" maxlength="500"><?php echo esc_textarea( $editing->customer_note ?? '' ); ?></textarea></p>
						<p><label for="psm-staff_note">Staff note (private)</label>
							<textarea id="psm-staff_note" name="staff_note" rows="2" maxlength="500"><?php echo esc_textarea( $editing->staff_note ?? '' ); ?></textarea></p>
						<?php if ( $editing && $editing->email ) : ?>
							<p class="description">Changing the status to Confirmed or Cancelled emails the customer.</p>
						<?php endif; ?>
						<?php
						submit_button( $editing ? 'Save changes' : 'Add appointment', 'primary', 'submit', false );
						if ( $editing ) {
							echo ' <a class="button" href="' . esc_url( $base ) . '">Cancel</a>';
						}
						?>
					</form>
				</div>

				<div class="psm-list">
					<ul class="subsubsub">
						<?php
						$links = array();
						foreach ( $views as $key => $label ) {
							$links[] = sprintf( '<li><a href="%s" class="%s">%s</a>', esc_url( add_query_arg( 'view', $key, $base ) ), $view === $key ? 'current' : '', esc_html( $label ) );
						}
						echo implode( ' |</li>', $links ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput -- built from escaped parts.
						?>
					</ul>
					<table class="widefat striped psm-appointments">
						<thead><tr><th>When</th><th>Customer</th><th>Service</th><th>Status</th><th></th></tr></thead>
						<tbody>
						<?php if ( empty( $rows ) ) : ?>
							<tr><td colspan="5">No appointments in this view.</td></tr>
						<?php endif; ?>
						<?php foreach ( $rows as $r ) : ?>
							<tr>
								<td>
									<strong><?php echo esc_html( mysql2date( 'D j M', $r->appointment_date ) ); ?></strong><br>
									<?php echo esc_html( PSM_Public::time_label( substr( $r->appointment_time, 0, 5 ) ) ); ?><br>
									<small><code><?php echo esc_html( psm_reference( $r->id ) ); ?></code></small>
								</td>
								<td>
									<strong><?php echo esc_html( $r->customer_name ); ?></strong><br>
									<a href="<?php echo esc_url( 'tel:' . preg_replace( '/[^0-9+]/', '', $r->phone ) ); ?>"><?php echo esc_html( $r->phone ); ?></a>
									· <a href="<?php echo esc_url( PSM_Public::wa_link( psm_whatsapp_number( $r->phone ), 'Hello ' . $r->customer_name . ", this is God's Care Pharmacy about your appointment " . psm_reference( $r->id ) . '.' ) ); ?>" target="_blank" rel="noopener">WhatsApp</a>
									<?php if ( $r->email ) : ?>
										<br><a href="<?php echo esc_url( 'mailto:' . $r->email ); ?>"><?php echo esc_html( $r->email ); ?></a>
									<?php endif; ?>
									<?php if ( $r->customer_note ) : ?>
										<br><small><em><?php echo esc_html( $r->customer_note ); ?></em></small>
									<?php endif; ?>
								</td>
								<td><?php echo esc_html( $r->service_name ); ?><br><small><?php echo esc_html( self::CHANNELS[ $r->channel ] ?? $r->channel ); ?></small></td>
								<td><span class="psm-badge psm-appt-<?php echo esc_attr( $r->status ); ?>"><?php echo esc_html( self::STATUSES[ $r->status ] ?? $r->status ); ?></span></td>
								<td class="psm-actions"><?php $this->appointment_actions( $r ); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php
	}

	public function page_services() {
		$editing = ( isset( $_GET['action'], $_GET['id'] ) && 'edit' === $_GET['action'] ) ? PSM_DB::get_service( absint( $_GET['id'] ) ) : null;
		$rows    = PSM_DB::get_services();
		?>
		<div class="wrap psm">
			<h1>Services customers can book</h1>
			<?php $this->notice(); ?>
			<div class="psm-layout">
				<div class="psm-form">
					<h2><?php echo $editing ? 'Edit service' : 'Add service'; ?></h2>
					<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="psm_service_save">
						<input type="hidden" name="id" value="<?php echo (int) ( $editing->id ?? 0 ); ?>">
						<?php wp_nonce_field( 'psm_service_save' ); ?>
						<?php $this->field( 'name', 'Service name *', $editing->name ?? '', 'text', true ); ?>
						<?php $this->field( 'description', 'Short description', $editing->description ?? '' ); ?>
						<?php $this->field( 'duration_minutes', 'Duration (minutes)', $editing->duration_minutes ?? 15, 'number' ); ?>
						<?php $this->field( 'price', 'Price in UGX (leave empty to not show)', isset( $editing->price ) ? (float) $editing->price : '', 'number' ); ?>
						<p><label><input type="checkbox" name="is_active" value="1" <?php checked( (int) ( $editing->is_active ?? 1 ), 1 ); ?>> Bookable on the website</label></p>
						<?php
						submit_button( $editing ? 'Save changes' : 'Add', 'primary', 'submit', false );
						if ( $editing ) {
							echo ' <a class="button" href="' . esc_url( admin_url( 'admin.php?page=psm-services' ) ) . '">Cancel</a>';
						}
						?>
					</form>
				</div>
				<div class="psm-list">
					<table class="widefat striped">
						<thead><tr><th>Service</th><th>Duration</th><th>Price (UGX)</th><th>Bookings</th><th>On website</th><th></th></tr></thead>
						<tbody>
						<?php if ( empty( $rows ) ) : ?>
							<tr><td colspan="6">No services yet.</td></tr>
						<?php endif; ?>
						<?php foreach ( $rows as $r ) : ?>
							<tr>
								<td><strong><?php echo esc_html( $r->name ); ?></strong><br><small><?php echo esc_html( $r->description ); ?></small></td>
								<td><?php echo (int) $r->duration_minutes; ?> min</td>
								<td><?php echo null === $r->price ? '—' : esc_html( number_format( (float) $r->price ) ); ?></td>
								<td><?php echo (int) $r->appointment_count; ?></td>
								<td><?php echo (int) $r->is_active ? 'Yes' : '<span class="psm-badge psm-expired">Hidden</span>'; ?></td>
								<td class="psm-actions">
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=psm-services&action=edit&id=' . (int) $r->id ) ); ?>">Edit</a> |
									<a class="psm-delete" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=psm_service_delete&id=' . (int) $r->id ), 'psm_service_delete_' . (int) $r->id ) ); ?>" onclick="return confirm('Delete this service?');">Delete</a>
								</td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php
	}

	public function page_settings() {
		?>
		<div class="wrap psm">
			<h1>Pharmacy settings</h1>
			<?php $this->notice(); ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="psm-form" style="max-width:560px">
				<input type="hidden" name="action" value="psm_settings_save">
				<?php wp_nonce_field( 'psm_settings_save' ); ?>
				<?php $this->field( 'notify_email', 'Send new booking alerts to', psm_setting( 'notify_email' ), 'email', true ); ?>
				<?php $this->field( 'whatsapp', 'Pharmacy WhatsApp number', psm_setting( 'whatsapp' ), 'tel', true ); ?>
				<?php $this->field( 'slot_capacity', 'Customers per time slot', psm_setting( 'slot_capacity' ), 'number' ); ?>
				<p><label><input type="checkbox" name="bookings_open" value="1" <?php checked( (int) psm_setting( 'bookings_open' ), 1 ); ?>> Accept bookings on the website</label></p>
				<?php submit_button( 'Save settings', 'primary', 'submit', false ); ?>
			</form>

			<h2>Privacy</h2>
			<p>Appointments hold customers' names and phone numbers. Delete finished (done or cancelled) appointments older than <?php echo (int) self::KEEP_DAYS; ?> days when you no longer need them.</p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('Delete finished appointments older than <?php echo (int) self::KEEP_DAYS; ?> days?');">
				<input type="hidden" name="action" value="psm_purge">
				<?php wp_nonce_field( 'psm_purge' ); ?>
				<?php submit_button( 'Delete old appointments', 'secondary', 'submit', false ); ?>
			</form>

			<h2>Website shortcodes</h2>
			<p><code>[psm_medicine_search]</code> availability search with results · <code>[psm_medicine_search compact="1" action="/medicine-availability/"]</code> search box only · <code>[psm_booking_form]</code> appointment form.</p>
		</div>
		<?php
	}

	/* =================================================================
	 * Helpers
	 * ================================================================= */

	private function appointment_actions( $r ) {
		$links = array();
		$next  = array();
		if ( 'pending' === $r->status ) {
			$next = array(
				'confirmed' => 'Confirm',
				'cancelled' => 'Cancel',
			);
		} elseif ( 'confirmed' === $r->status ) {
			$next = array(
				'completed' => 'Mark done',
				'cancelled' => 'Cancel',
			);
		}
		foreach ( $next as $to => $label ) {
			$links[] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=psm_appt_status&id=' . (int) $r->id . '&to=' . $to ), 'psm_appt_status_' . (int) $r->id . '_' . $to ) ),
				esc_html( $label )
			);
		}
		$links[] = sprintf( '<a href="%s">Edit</a>', esc_url( admin_url( 'admin.php?page=psm-appointments&action=edit&id=' . (int) $r->id ) ) );
		$links[] = sprintf(
			'<a class="psm-delete" href="%s" onclick="return confirm(\'Delete this appointment?\');">Delete</a>',
			esc_url( wp_nonce_url( admin_url( 'admin-post.php?action=psm_appt_delete&id=' . (int) $r->id ), 'psm_appt_delete_' . (int) $r->id ) )
		);
		echo implode( ' | ', $links ); // phpcs:ignore WordPress.Security.EscapeOutput -- escaped above.
	}

	private function key_select( $name, $label, array $options, $current ) {
		printf( '<p><label for="psm-%1$s">%2$s</label><select id="psm-%1$s" name="%1$s">', esc_attr( $name ), esc_html( $label ) );
		foreach ( $options as $value => $text ) {
			printf( '<option value="%s" %s>%s</option>', esc_attr( $value ), selected( $current, $value, false ), esc_html( $text ) );
		}
		echo '</select></p>';
	}

	private function key_choice( $key, array $allowed, $fallback ) {
		$value = sanitize_key( $_POST[ $key ] ?? '' );
		return isset( $allowed[ $value ] ) ? $value : $fallback;
	}

	/** Capability + nonce check for POST forms. */
	private function guard( $nonce_action ) {
		if ( ! current_user_can( self::CAP ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'pharmacy-stock' ), 403 );
		}
		check_admin_referer( $nonce_action );
	}
}
