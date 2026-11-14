<?php
/**
 * wp-admin screens and form handlers for Pharmacy Stock Manager.
 *
 * Security on every request:
 *  - capability check (only allowed users),
 *  - nonce check (stops forged requests / CSRF),
 *  - input sanitised before it reaches the database layer,
 *  - output escaped when printed (stops XSS).
 *
 * @package pharmacy-stock
 */

defined( 'ABSPATH' ) || exit;

class PSM_Admin {

	/** Who may use the module: administrators and the Pharmacy Staff role. */
	const CAP = 'psm_manage';

	const CATEGORIES = array( 'Analgesic', 'Antibiotic', 'Antimalarial', 'Antihypertensive', 'Antidiabetic', 'Vitamin / Supplement', 'First Aid', 'Personal Care', 'Baby Care', 'Other' );
	const FORMS      = array( 'Tablet', 'Capsule', 'Syrup', 'Suspension', 'Injection', 'Cream / Ointment', 'Drops', 'Inhaler', 'Other' );

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
		add_action( 'admin_post_psm_save', array( $this, 'handle_save' ) );
		add_action( 'admin_post_psm_delete', array( $this, 'handle_delete' ) );
	}

	/* =================================================================
	 * Menu & assets
	 * ================================================================= */

	public function register_menu() {
		add_menu_page( 'Pharmacy', 'Pharmacy', self::CAP, 'psm-overview', array( $this, 'page_overview' ), 'dashicons-plus-alt', 26 );
		add_submenu_page( 'psm-overview', 'Overview', 'Overview', self::CAP, 'psm-overview', array( $this, 'page_overview' ) );
		add_submenu_page( 'psm-overview', 'Stock Batches', 'Stock Batches', self::CAP, 'psm-batches', array( $this, 'page_batches' ) );
		add_submenu_page( 'psm-overview', 'Medicines', 'Medicines', self::CAP, 'psm-medicines', array( $this, 'page_medicines' ) );
		add_submenu_page( 'psm-overview', 'Suppliers', 'Suppliers', self::CAP, 'psm-suppliers', array( $this, 'page_suppliers' ) );
	}

	public function enqueue_assets( $hook ) {
		if ( false !== strpos( $hook, 'psm-' ) ) {
			wp_enqueue_style( 'psm-admin', PSM_URL . 'assets/admin.css', array(), PSM_VERSION );
		}
	}

	/* =================================================================
	 * Form handlers (CREATE / UPDATE / DELETE)
	 * ================================================================= */

	/** Handles every add/edit form. */
	public function handle_save() {
		if ( ! current_user_can( self::CAP ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'pharmacy-stock' ), 403 );
		}

		$type = isset( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : '';
		check_admin_referer( 'psm_save_' . $type );

		$id = isset( $_POST['id'] ) ? absint( $_POST['id'] ) : 0;

		// A duplicate name/batch makes MySQL reject the query; show our own
		// friendly notice instead of WordPress's raw database error.
		global $wpdb;
		$wpdb->suppress_errors( true );

		switch ( $type ) {
			case 'supplier':
				$page = 'psm-suppliers';
				$data = array(
					'name'           => $this->text( 'name' ),
					'contact_person' => $this->text( 'contact_person' ),
					'phone'          => $this->text( 'phone' ),
					'email'          => sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ),
					'location'       => $this->text( 'location' ),
				);
				if ( '' === $data['name'] ) {
					$this->redirect( $page, 'required', $id );
				}
				$result = $id ? PSM_DB::update_supplier( $id, $data ) : PSM_DB::insert_supplier( $data );
				break;

			case 'medicine':
				$page = 'psm-medicines';
				$data = array(
					'supplier_id'           => absint( $_POST['supplier_id'] ?? 0 ),
					'name'                  => $this->text( 'name' ),
					'category'              => $this->choice( 'category', self::CATEGORIES ),
					'form'                  => $this->choice( 'form', self::FORMS ),
					'unit_price'            => max( 0, round( (float) ( $_POST['unit_price'] ?? 0 ), 2 ) ),
					'requires_prescription' => empty( $_POST['requires_prescription'] ) ? 0 : 1,
				);
				if ( '' === $data['name'] || ! $data['supplier_id'] || ! PSM_DB::get_supplier( $data['supplier_id'] ) ) {
					$this->redirect( $page, 'required', $id );
				}
				$result = $id ? PSM_DB::update_medicine( $id, $data ) : PSM_DB::insert_medicine( $data );
				break;

			case 'batch':
				$page = 'psm-batches';
				$data = array(
					'medicine_id' => absint( $_POST['medicine_id'] ?? 0 ),
					'batch_no'    => strtoupper( $this->text( 'batch_no' ) ),
					'quantity'    => absint( $_POST['quantity'] ?? 0 ),
					'expiry_date' => $this->date( 'expiry_date' ),
					'received_on' => $this->date( 'received_on' ),
				);
				if ( '' === $data['batch_no'] || '' === $data['expiry_date'] || ! PSM_DB::get_medicine( $data['medicine_id'] ) ) {
					$this->redirect( $page, 'required', $id );
				}
				$result = $id ? PSM_DB::update_batch( $id, $data ) : PSM_DB::insert_batch( $data );
				break;

			default:
				wp_die( esc_html__( 'Unknown record type.', 'pharmacy-stock' ), 400 );
		}

		if ( false === $result ) {
			$this->redirect( $page, 'duplicate', $id ); // most likely cause: unique name / batch number clash
		}
		$this->redirect( $page, $id ? 'updated' : 'added' );
	}

	/** Handles delete links. */
	public function handle_delete() {
		if ( ! current_user_can( self::CAP ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'pharmacy-stock' ), 403 );
		}

		$type = isset( $_GET['type'] ) ? sanitize_key( $_GET['type'] ) : '';
		$id   = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
		check_admin_referer( 'psm_delete_' . $type . '_' . $id );

		switch ( $type ) {
			case 'supplier':
				$supplier_in_use = false;
				foreach ( PSM_DB::get_suppliers() as $s ) {
					if ( (int) $s->id === $id && (int) $s->medicine_count > 0 ) {
						$supplier_in_use = true;
					}
				}
				if ( $supplier_in_use ) {
					$this->redirect( 'psm-suppliers', 'in_use' );
				}
				$result = PSM_DB::delete_supplier( $id );
				$page   = 'psm-suppliers';
				break;
			case 'medicine':
				$result = PSM_DB::delete_medicine( $id );
				$page   = 'psm-medicines';
				break;
			case 'batch':
				$result = PSM_DB::delete_batch( $id );
				$page   = 'psm-batches';
				break;
			default:
				wp_die( esc_html__( 'Unknown record type.', 'pharmacy-stock' ), 400 );
		}

		$this->redirect( $page, false === $result ? 'error' : 'deleted' );
	}

	/* =================================================================
	 * Pages (READ)
	 * ================================================================= */

	public function page_overview() {
		$stats    = PSM_DB::get_stats();
		$expiring = PSM_DB::get_batches( PSM_DB::EXPIRY_WINDOW );
		?>
		<div class="wrap psm">
			<h1>God's Care Pharmacy — Overview</h1>
			<?php $this->notice(); ?>

			<?php $appt = PSM_DB::get_appointment_stats(); ?>
			<div class="psm-cards">
				<?php
				$this->card( 'Appointments today', (int) $appt->today );
				$this->card( 'Waiting for confirmation', (int) $appt->pending, (int) $appt->pending ? 'warn' : '' );
				$this->card( 'Upcoming appointments', (int) $appt->upcoming );
				?>
			</div>
			<p><a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=psm-appointments' ) ); ?>">Manage appointments</a></p>

			<h2>Stock</h2>
			<div class="psm-cards">
				<?php
				$this->card( 'Medicines listed', (int) $stats->medicines );
				$this->card( 'Units in date', number_format( (int) $stats->units ) );
				$this->card( 'Batches expiring ≤ ' . PSM_DB::EXPIRY_WINDOW . ' days', (int) $stats->expiring, 'warn' );
				$this->card( 'Expired batches', (int) $stats->expired, 'danger' );
				?>
			</div>

			<h2>Expiring soon &amp; expired</h2>
			<?php if ( empty( $expiring ) ) : ?>
				<p>No batches expire in the next <?php echo (int) PSM_DB::EXPIRY_WINDOW; ?> days. 🎉</p>
			<?php else : ?>
				<?php $this->batch_table( $expiring, false ); ?>
			<?php endif; ?>
		</div>
		<?php
	}

	public function page_suppliers() {
		$editing = $this->editing( 'supplier' );
		$rows    = PSM_DB::get_suppliers();
		?>
		<div class="wrap psm">
			<h1>Suppliers</h1>
			<?php $this->notice(); ?>
			<div class="psm-layout">
				<div class="psm-form">
					<h2><?php echo $editing ? 'Edit supplier' : 'Add supplier'; ?></h2>
					<?php $this->form_open( 'supplier', $editing ); ?>
						<?php $this->field( 'name', 'Supplier name *', $editing->name ?? '', 'text', true ); ?>
						<?php $this->field( 'contact_person', 'Contact person', $editing->contact_person ?? '' ); ?>
						<?php $this->field( 'phone', 'Phone', $editing->phone ?? '', 'tel' ); ?>
						<?php $this->field( 'email', 'Email', $editing->email ?? '', 'email' ); ?>
						<?php $this->field( 'location', 'Location', $editing->location ?? '' ); ?>
					<?php $this->form_close( 'psm-suppliers', $editing ); ?>
				</div>
				<div class="psm-list">
					<table class="widefat striped">
						<thead><tr><th>Name</th><th>Contact</th><th>Phone</th><th>Location</th><th>Medicines</th><th></th></tr></thead>
						<tbody>
						<?php if ( empty( $rows ) ) : ?>
							<tr><td colspan="6">No suppliers yet.</td></tr>
						<?php endif; ?>
						<?php foreach ( $rows as $r ) : ?>
							<tr>
								<td><strong><?php echo esc_html( $r->name ); ?></strong><br><small><?php echo esc_html( $r->email ); ?></small></td>
								<td><?php echo esc_html( $r->contact_person ); ?></td>
								<td><?php echo esc_html( $r->phone ); ?></td>
								<td><?php echo esc_html( $r->location ); ?></td>
								<td><?php echo (int) $r->medicine_count; ?></td>
								<td class="psm-actions"><?php $this->row_actions( 'supplier', $r->id, 'psm-suppliers' ); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php
	}

	public function page_medicines() {
		$editing   = $this->editing( 'medicine' );
		$rows      = PSM_DB::get_medicines();
		$suppliers = PSM_DB::get_suppliers();
		?>
		<div class="wrap psm">
			<h1>Medicines</h1>
			<?php $this->notice(); ?>
			<?php if ( empty( $suppliers ) ) : ?>
				<div class="notice notice-info"><p>Add a <a href="<?php echo esc_url( admin_url( 'admin.php?page=psm-suppliers' ) ); ?>">supplier</a> first — every medicine belongs to a supplier.</p></div>
			<?php endif; ?>
			<div class="psm-layout">
				<div class="psm-form">
					<h2><?php echo $editing ? 'Edit medicine' : 'Add medicine'; ?></h2>
					<?php $this->form_open( 'medicine', $editing ); ?>
						<?php $this->field( 'name', 'Medicine name *', $editing->name ?? '', 'text', true ); ?>
						<p><label for="psm-supplier_id">Supplier *</label>
							<select id="psm-supplier_id" name="supplier_id" required>
								<option value="">— Select —</option>
								<?php foreach ( $suppliers as $s ) : ?>
									<option value="<?php echo (int) $s->id; ?>" <?php selected( (int) ( $editing->supplier_id ?? 0 ), (int) $s->id ); ?>><?php echo esc_html( $s->name ); ?></option>
								<?php endforeach; ?>
							</select></p>
						<?php $this->select( 'category', 'Category', self::CATEGORIES, $editing->category ?? '' ); ?>
						<?php $this->select( 'form', 'Form', self::FORMS, $editing->form ?? '' ); ?>
						<?php $this->field( 'unit_price', 'Unit price (UGX)', $editing->unit_price ?? '', 'number' ); ?>
						<p><label><input type="checkbox" name="requires_prescription" value="1" <?php checked( (int) ( $editing->requires_prescription ?? 0 ), 1 ); ?>> Prescription-only medicine</label></p>
					<?php $this->form_close( 'psm-medicines', $editing ); ?>
				</div>
				<div class="psm-list">
					<table class="widefat striped">
						<thead><tr><th>Medicine</th><th>Supplier</th><th>Category</th><th>Price (UGX)</th><th>Stock in date</th><th></th></tr></thead>
						<tbody>
						<?php if ( empty( $rows ) ) : ?>
							<tr><td colspan="6">No medicines yet.</td></tr>
						<?php endif; ?>
						<?php foreach ( $rows as $r ) : ?>
							<tr>
								<td><strong><?php echo esc_html( $r->name ); ?></strong> <small><?php echo esc_html( $r->form ); ?></small>
									<?php if ( (int) $r->requires_prescription ) : ?><span class="psm-badge psm-rx">Rx</span><?php endif; ?></td>
								<td><?php echo esc_html( $r->supplier_name ); ?></td>
								<td><?php echo esc_html( $r->category ); ?></td>
								<td><?php echo esc_html( number_format( (float) $r->unit_price ) ); ?></td>
								<td><?php echo esc_html( number_format( (int) $r->stock_in_date ) ); ?></td>
								<td class="psm-actions"><?php $this->row_actions( 'medicine', $r->id, 'psm-medicines', 'Deleting this medicine also deletes all its stock batches. Continue?' ); ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<?php
	}

	public function page_batches() {
		$editing   = $this->editing( 'batch' );
		$medicines = PSM_DB::get_medicines();
		$expiring  = isset( $_GET['filter'] ) && 'expiring' === $_GET['filter'];
		$rows      = PSM_DB::get_batches( $expiring ? PSM_DB::EXPIRY_WINDOW : null );
		$base      = admin_url( 'admin.php?page=psm-batches' );
		?>
		<div class="wrap psm">
			<h1>Stock Batches</h1>
			<?php $this->notice(); ?>
			<?php if ( empty( $medicines ) ) : ?>
				<div class="notice notice-info"><p>Add a <a href="<?php echo esc_url( admin_url( 'admin.php?page=psm-medicines' ) ); ?>">medicine</a> first.</p></div>
			<?php endif; ?>
			<div class="psm-layout">
				<div class="psm-form">
					<h2><?php echo $editing ? 'Edit batch' : 'Receive new batch'; ?></h2>
					<?php $this->form_open( 'batch', $editing ); ?>
						<p><label for="psm-medicine_id">Medicine *</label>
							<select id="psm-medicine_id" name="medicine_id" required>
								<option value="">— Select —</option>
								<?php foreach ( $medicines as $m ) : ?>
									<option value="<?php echo (int) $m->id; ?>" <?php selected( (int) ( $editing->medicine_id ?? 0 ), (int) $m->id ); ?>><?php echo esc_html( $m->name . ' (' . $m->form . ')' ); ?></option>
								<?php endforeach; ?>
							</select></p>
						<?php $this->field( 'batch_no', 'Batch number *', $editing->batch_no ?? '', 'text', true ); ?>
						<?php $this->field( 'quantity', 'Quantity (units)', $editing->quantity ?? '', 'number' ); ?>
						<?php $this->field( 'expiry_date', 'Expiry date *', $editing->expiry_date ?? '', 'date', true ); ?>
						<?php $this->field( 'received_on', 'Received on', $editing->received_on ?? '', 'date' ); ?>
					<?php $this->form_close( 'psm-batches', $editing ); ?>
				</div>
				<div class="psm-list">
					<ul class="subsubsub">
						<li><a href="<?php echo esc_url( $base ); ?>" class="<?php echo $expiring ? '' : 'current'; ?>">All batches</a> |</li>
						<li><a href="<?php echo esc_url( add_query_arg( 'filter', 'expiring', $base ) ); ?>" class="<?php echo $expiring ? 'current' : ''; ?>">Expiring ≤ <?php echo (int) PSM_DB::EXPIRY_WINDOW; ?> days</a></li>
					</ul>
					<?php $this->batch_table( $rows, true ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/* =================================================================
	 * Helpers
	 * ================================================================= */

	/** Batch table, shared by Overview and Stock Batches. Data comes from the 3-table JOIN. */
	protected function batch_table( $rows, $with_actions ) {
		?>
		<table class="widefat striped">
			<thead><tr><th>Medicine</th><th>Batch</th><th>Supplier</th><th>Qty</th><th>Expiry</th><th>Status</th><?php echo $with_actions ? '<th></th>' : ''; ?></tr></thead>
			<tbody>
			<?php if ( empty( $rows ) ) : ?>
				<tr><td colspan="7">No batches found.</td></tr>
			<?php endif; ?>
			<?php foreach ( $rows as $r ) : ?>
				<?php
				$days = (int) $r->days_left;
				if ( $days < 0 ) {
					$status = array( 'psm-expired', 'Expired ' . abs( $days ) . 'd ago' );
				} elseif ( $days <= 30 ) {
					$status = array( 'psm-critical', $days . ' days left' );
				} elseif ( $days <= PSM_DB::EXPIRY_WINDOW ) {
					$status = array( 'psm-warning', $days . ' days left' );
				} else {
					$status = array( 'psm-ok', 'OK' );
				}
				?>
				<tr>
					<td><strong><?php echo esc_html( $r->medicine_name ); ?></strong> <small><?php echo esc_html( $r->form ); ?></small></td>
					<td><code><?php echo esc_html( $r->batch_no ); ?></code></td>
					<td><?php echo esc_html( $r->supplier_name ); ?></td>
					<td><?php echo esc_html( number_format( (int) $r->quantity ) ); ?></td>
					<td><?php echo esc_html( mysql2date( 'j M Y', $r->expiry_date ) ); ?></td>
					<td><span class="psm-badge <?php echo esc_attr( $status[0] ); ?>"><?php echo esc_html( $status[1] ); ?></span></td>
					<?php if ( $with_actions ) : ?>
						<td class="psm-actions"><?php $this->row_actions( 'batch', $r->id, 'psm-batches' ); ?></td>
					<?php endif; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
	}

	/** The record being edited, if ?action=edit&id=N is in the URL. */
	protected function editing( $type ) {
		if ( ! isset( $_GET['action'], $_GET['id'] ) || 'edit' !== $_GET['action'] ) {
			return null;
		}
		$id = absint( $_GET['id'] );
		switch ( $type ) {
			case 'supplier':
				return PSM_DB::get_supplier( $id );
			case 'medicine':
				return PSM_DB::get_medicine( $id );
			case 'batch':
				return PSM_DB::get_batch( $id );
		}
		return null;
	}

	protected function form_open( $type, $editing ) {
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		echo '<input type="hidden" name="action" value="psm_save">';
		echo '<input type="hidden" name="type" value="' . esc_attr( $type ) . '">';
		echo '<input type="hidden" name="id" value="' . (int) ( $editing->id ?? 0 ) . '">';
		wp_nonce_field( 'psm_save_' . $type );
	}

	protected function form_close( $page, $editing ) {
		submit_button( $editing ? 'Save changes' : 'Add', 'primary', 'submit', false );
		if ( $editing ) {
			echo ' <a class="button" href="' . esc_url( admin_url( 'admin.php?page=' . $page ) ) . '">Cancel</a>';
		}
		echo '</form>';
	}

	protected function field( $name, $label, $value, $type = 'text', $required = false ) {
		$extra = 'number' === $type ? ' min="0" step="' . ( 'unit_price' === $name ? 'any' : '1' ) . '"' : '';
		printf(
			'<p><label for="psm-%1$s">%2$s</label><input class="regular-text" type="%3$s" id="psm-%1$s" name="%1$s" value="%4$s"%5$s%6$s></p>',
			esc_attr( $name ),
			esc_html( $label ),
			esc_attr( $type ),
			esc_attr( (string) $value ),
			$required ? ' required' : '',
			$extra // Built from fixed strings above, not user input.
		);
	}

	protected function select( $name, $label, $options, $current ) {
		printf( '<p><label for="psm-%1$s">%2$s</label><select id="psm-%1$s" name="%1$s">', esc_attr( $name ), esc_html( $label ) );
		foreach ( $options as $opt ) {
			printf( '<option value="%1$s" %2$s>%1$s</option>', esc_attr( $opt ), selected( $current, $opt, false ) );
		}
		echo '</select></p>';
	}

	protected function row_actions( $type, $id, $page, $confirm = 'Delete this record?' ) {
		$edit   = add_query_arg(
			array(
				'page'   => $page,
				'action' => 'edit',
				'id'     => (int) $id,
			),
			admin_url( 'admin.php' )
		);
		$delete = wp_nonce_url(
			add_query_arg(
				array(
					'action' => 'psm_delete',
					'type'   => $type,
					'id'     => (int) $id,
				),
				admin_url( 'admin-post.php' )
			),
			'psm_delete_' . $type . '_' . (int) $id
		);
		printf(
			'<a href="%s">Edit</a> | <a class="psm-delete" href="%s" onclick="return confirm(%s);">Delete</a>',
			esc_url( $edit ),
			esc_url( $delete ),
			esc_attr( wp_json_encode( $confirm ) )
		);
	}

	protected function card( $label, $value, $tone = '' ) {
		printf(
			'<div class="psm-card %s"><span class="psm-card-value">%s</span><span class="psm-card-label">%s</span></div>',
			esc_attr( $tone ),
			esc_html( $value ),
			esc_html( $label )
		);
	}

	protected function notice() {
		$messages = array(
			'added'     => array( 'success', 'Record added.' ),
			'updated'   => array( 'success', 'Record updated.' ),
			'deleted'   => array( 'success', 'Record deleted.' ),
			'required'  => array( 'error', 'Please fill in all required fields.' ),
			'duplicate' => array( 'error', 'Could not save — that name or batch number already exists.' ),
			'in_use'    => array( 'error', 'This supplier still has medicines. Reassign or delete them first.' ),
			'error'     => array( 'error', 'Something went wrong. Please try again.' ),
			'confirmed' => array( 'success', 'Appointment confirmed.' ),
			'completed' => array( 'success', 'Appointment marked as done.' ),
			'cancelled' => array( 'success', 'Appointment cancelled.' ),
			'emailed'   => array( 'success', 'Status updated and the customer was emailed.' ),
			'taken'     => array( 'error', 'That time slot is already fully booked. Choose another time or raise the slot capacity in Settings.' ),
			'svc_used'  => array( 'error', 'This service has appointments. Untick "Bookable" to hide it instead of deleting it.' ),
			'saved'     => array( 'success', 'Settings saved.' ),
			'purged'    => array( 'success', 'Old appointments deleted.' ),
		);
		$key = isset( $_GET['psm_msg'] ) ? sanitize_key( $_GET['psm_msg'] ) : '';
		if ( isset( $messages[ $key ] ) ) {
			printf( '<div class="notice notice-%s is-dismissible"><p>%s</p></div>', esc_attr( $messages[ $key ][0] ), esc_html( $messages[ $key ][1] ) );
		}
	}

	/** Redirect back to a page with a message (Post/Redirect/Get, so refresh won't resubmit). */
	protected function redirect( $page, $msg, $edit_id = 0 ) {
		$args = array(
			'page'    => $page,
			'psm_msg' => $msg,
		);
		if ( $edit_id ) {
			$args['action'] = 'edit';
			$args['id']     = $edit_id;
		}
		wp_safe_redirect( add_query_arg( $args, admin_url( 'admin.php' ) ) );
		exit;
	}

	protected function text( $key ) {
		return isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '';
	}

	protected function choice( $key, array $allowed ) {
		$value = $this->text( $key );
		return in_array( $value, $allowed, true ) ? $value : 'Other';
	}

	/** Returns a valid Y-m-d date or an empty string. */
	protected function date( $key ) {
		$value = $this->text( $key );
		$d     = DateTime::createFromFormat( 'Y-m-d', $value );
		return ( $d && $d->format( 'Y-m-d' ) === $value ) ? $value : '';
	}
}
