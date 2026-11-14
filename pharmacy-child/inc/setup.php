<?php
/**
 * God's Care one-click site setup.
 *
 * Appearance → God's Care setup. Builds or refreshes the site's pages from the
 * theme's page patterns, sets the homepage and blog page, switches to
 * "Post name" permalinks, moves the WordPress sample content to the trash and
 * adds three starter health articles (with featured images from the Media
 * Library when they are found).
 *
 * Safe to run again: pages are refreshed in place and articles that already
 * exist are left alone.
 *
 * @package pharmacy-child
 */

defined( 'ABSPATH' ) || exit;

/**
 * Pages the site needs: slug => [ title, pattern slug or '' for an empty page ].
 */
function gcp_setup_pages() {
	return array(
		'home'                => array( 'Home', 'pharmacy-child/page-home' ),
		'about'               => array( 'About', 'pharmacy-child/page-about' ),
		'services'            => array( 'Services', 'pharmacy-child/page-services' ),
		'health-tips'         => array( 'Health Tips', 'pharmacy-child/page-health-tips' ),
		'book-appointment'      => array( 'Book an Appointment', 'pharmacy-child/page-book' ),
		'medicine-availability' => array( 'Medicine Availability', 'pharmacy-child/page-medicines' ),
		'prescription-refill' => array( 'Prescription Refill', 'pharmacy-child/page-refill' ),
		'contact'             => array( 'Contact', 'pharmacy-child/page-contact' ),
		'health-blog'         => array( 'Health Blog', '' ),
	);
}

/**
 * Starter blog articles.
 */
function gcp_setup_posts() {
	return array(
		array(
			'slug'    => 'malaria-in-fort-portal-prevent-test-treat',
			'title'   => 'Malaria in Fort Portal: How to Prevent It, Test Early and Treat It Right',
			'image'   => 'health_tips_1.jpg',
			'excerpt' => 'Malaria is still one of the most common reasons people visit a pharmacy in Fort Portal. Here is how to protect your family, why you should test before you treat, and the danger signs that need a hospital.',
			'content' => <<<'HTML'
<!-- wp:paragraph -->
<p>Malaria is still one of the most common reasons people walk into a pharmacy in Fort Portal City. Many of us have had it more than once, and that familiarity can make us careless: we buy tablets at the first headache, stop them when we feel better, and hope for the best. This guide explains how to protect your family, why testing comes before treatment, and when a fever needs a hospital rather than a pharmacy.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">How malaria spreads</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Malaria is caused by a parasite carried by female Anopheles mosquitoes. They bite mostly between dusk and dawn, which is why protection at night matters most. After an infected bite, symptoms usually start between one and three weeks later. Children under five and pregnant women are most at risk of severe illness, because their bodies have less ability to fight the parasite.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Simple ways to prevent malaria at home</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<!-- wp:list-item -->
<li><strong>Sleep under a treated mosquito net every night.</strong> Tuck it under the mattress and repair holes quickly. Make sure children sleep under one too.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Remove standing water.</strong> Old tyres, open containers and blocked drainage near your home are where mosquitoes breed.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Cover up in the evening.</strong> Long sleeves and mosquito repellent help when you are outside after sunset.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Keep doors and windows screened</strong> or closed from early evening.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Pregnant women should attend antenatal care,</strong> where they can receive medicine that helps prevent malaria during pregnancy.</li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Test before you treat</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Fever, chills, headache, body pains, tiredness and loss of appetite are classic malaria signs, but the same symptoms can come from typhoid, flu, urinary infections and other illnesses. Taking malaria medicine when you do not have malaria wastes money, delays the right treatment and helps the parasite become resistant to the medicines we rely on.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>A malaria rapid diagnostic test needs only a finger-prick of blood and gives a result in about 15 minutes. If you have a fever, get tested within 24 hours. At God's Care Pharmacy on Ruhandiika Street we can test you and explain the result on the spot.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Treating malaria the right way</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>In Uganda, uncomplicated malaria is usually treated with an artemisinin-based combination therapy (ACT), such as artemether-lumefantrine. To make treatment work:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list">
<!-- wp:list-item -->
<li>Take every dose at the times on the label, and finish the whole course even if you feel better after the first day.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Some malaria tablets work better when taken with food or milk. Ask your pharmacist how to take yours.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>If you vomit within an hour of a dose, tell your pharmacist. You may need to repeat it.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Drink plenty of fluids and rest. Paracetamol can help bring down fever.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Never share your tablets or keep leftovers for next time.</li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>Children, pregnant women and people with other health conditions need doses chosen for them. Always tell the pharmacist the age and weight of the person who will take the medicine, and whether they are pregnant.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Danger signs: go to a hospital immediately</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Severe malaria is an emergency. Do not wait for tablets to work if someone with fever has any of these signs:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list">
<!-- wp:list-item -->
<li>Convulsions (fits), confusion, or being very sleepy and hard to wake</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Unable to drink, breastfeed or keep anything down</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Difficulty breathing</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Yellow eyes, very pale palms, or dark (cola-coloured) urine</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>A fever that does not improve after two to three days of treatment</li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2 class="wp-block-heading">We are here to help</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>If you are not sure whether a fever is malaria, or how to take your medicine, talk to us. Visit God's Care Pharmacy on Ruhandiika Street, Fort Portal City, or message our team on WhatsApp at 0745 546 101. This article is general information and does not replace advice from a doctor or pharmacist who knows your situation.</p>
<!-- /wp:paragraph -->
HTML,
		),
		array(
			'slug'    => 'how-to-store-medicine-safely-at-home',
			'title'   => 'How to Store Medicine Safely at Home',
			'image'   => 'health_tips_2.jpg',
			'excerpt' => 'Heat, moisture and curious children are the biggest threats to the medicine in your home. Six simple habits keep it safe and working.',
			'content' => <<<'HTML'
<!-- wp:paragraph -->
<p>Medicine only works as it should when it is stored properly. Heat, damp and sunlight can weaken tablets and syrups long before the expiry date, and medicine left within reach is one of the most common causes of poisoning in young children. These six habits keep your family's medicine safe and effective.</p>
<!-- /wp:paragraph -->

<!-- wp:list {"ordered":true} -->
<ol class="wp-block-list">
<!-- wp:list-item -->
<li><strong>Choose a cool, dry place.</strong> A cupboard in a bedroom is usually better than the kitchen or bathroom, where heat and steam build up. Never leave medicine in a parked car or on a sunny windowsill.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Keep it high and out of sight.</strong> Children copy adults and colourful tablets can look like sweets. A high shelf or a cupboard that locks is safest.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Keep the original packet.</strong> The label tells you the dose, the expiry date and any warnings. Loose tablets in an unlabelled container are easy to mix up.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Follow special storage instructions.</strong> Some medicines, such as certain eye drops, insulin and some antibiotic syrups, must be kept in a fridge or used within a set number of days after opening. Write the opening date on the bottle.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Check expiry dates every few months.</strong> Expired medicine may not work and some can become harmful.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Dispose of old medicine safely.</strong> Do not throw it in a pit latrine, a drain or open rubbish. Bring expired or unused medicine to God's Care Pharmacy and we will dispose of it properly.</li>
<!-- /wp:list-item -->
</ol>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>Not sure how to store a particular medicine? Ask us in store on Ruhandiika Street or on WhatsApp at 0745 546 101.</p>
<!-- /wp:paragraph -->
HTML,
		),
		array(
			'slug'    => 'understanding-your-blood-pressure-numbers',
			'title'   => 'Understanding Your Blood Pressure Numbers',
			'image'   => 'health_tips_3.jpg',
			'excerpt' => 'High blood pressure rarely causes symptoms, so the only way to know is to check. Here is what the two numbers mean and what you can do about them.',
			'content' => <<<'HTML'
<!-- wp:paragraph -->
<p>High blood pressure is often called a silent condition because most people who have it feel completely well. Over years, it quietly strains the heart, kidneys, eyes and blood vessels in the brain, and it is a major cause of strokes and heart attacks. The good news is that it is easy to check and, once found, can usually be controlled.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What the two numbers mean</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>A reading such as 120/80 has two parts. The top number (systolic) is the pressure in your arteries when your heart beats. The bottom number (diastolic) is the pressure between beats. As a general guide:</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list">
<!-- wp:list-item -->
<li><strong>Below 120/80</strong> is usually considered normal.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>Between 120/80 and 139/89</strong> is raised. It is a sign to make healthy changes and check again.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li><strong>140/90 or higher</strong> on repeated checks usually means high blood pressure, and you should see a doctor.</li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>One high reading does not mean you have high blood pressure. Stress, rushing, caffeine or a full bladder can push it up for a short time. Sit quietly for five minutes before a check, and look at the pattern over several readings.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">Habits that help</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list">
<!-- wp:list-item -->
<li>Use less salt and fewer salty packaged foods.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Eat more vegetables, fruit and beans.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Move for about 30 minutes on most days.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>Limit alcohol and stop smoking.</li>
<!-- /wp:list-item -->
<!-- wp:list-item -->
<li>If you are prescribed medicine, take it every day, even when you feel fine.</li>
<!-- /wp:list-item -->
</ul>
<!-- /wp:list -->

<!-- wp:paragraph -->
<p>Get a quick blood pressure check at God's Care Pharmacy on Ruhandiika Street, Fort Portal City. If you have a sudden severe headache, chest pain, weakness on one side of the body or trouble speaking, go to a hospital immediately.</p>
<!-- /wp:paragraph -->
HTML,
		),
	);
}

/**
 * Find a Media Library image by its file name (e.g. health_tips_1.jpg).
 *
 * @param string $file File name.
 * @return int Attachment ID or 0.
 */
function gcp_setup_find_attachment( $file ) {
	global $wpdb;
	return (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s ORDER BY post_id DESC LIMIT 1",
			'%' . $wpdb->esc_like( '/' . $file )
		)
	);
}

/**
 * Run the setup. Returns a list of what was done, for the report.
 */
function gcp_run_site_setup() {
	$log      = array();
	$registry = WP_Block_Patterns_Registry::get_instance();
	$ids      = array();

	// 1. Pages.
	foreach ( gcp_setup_pages() as $slug => $page ) {
		list( $title, $pattern ) = $page;

		$content = '';
		if ( $pattern ) {
			$registered = $registry->get_registered( $pattern );
			if ( ! $registered ) {
				$log[] = "Skipped {$title}: pattern {$pattern} not found.";
				continue;
			}
			$content = $registered['content'];
		}

		$existing = get_page_by_path( $slug );
		$data     = array(
			'post_title'   => $title,
			'post_name'    => $slug,
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => $content,
		);

		if ( $existing ) {
			$data['ID'] = $existing->ID;
			$id         = wp_update_post( wp_slash( $data ), true );
			$verb       = 'Refreshed';
		} else {
			$id   = wp_insert_post( wp_slash( $data ), true );
			$verb = 'Created';
		}

		if ( is_wp_error( $id ) ) {
			$log[] = "Could not save {$title}: " . $id->get_error_message();
			continue;
		}

		if ( $pattern ) {
			update_post_meta( $id, '_wp_page_template', 'page-no-title' );
		}
		$ids[ $slug ] = $id;
		$log[]        = "{$verb} page: {$title}";
	}

	// 2. Homepage, blog page and site details.
	if ( isset( $ids['home'], $ids['health-blog'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $ids['home'] );
		update_option( 'page_for_posts', $ids['health-blog'] );
		$log[] = 'Set Home as the homepage and Health Blog as the posts page';
	}
	update_option( 'blogname', "God's Care Pharmacy" );
	update_option( 'blogdescription', 'Community pharmacy on Ruhandiika Street, Fort Portal City' );
	update_option( 'timezone_string', 'Africa/Kampala' );
	$log[] = 'Set site title, tagline and time zone (Africa/Kampala)';

	// 3. Clean addresses such as /health-tips/.
	update_option( 'permalink_structure', '/%postname%/' );
	flush_rewrite_rules( true );
	$log[] = 'Switched permalinks to "Post name"';

	// 4. Move WordPress sample content to the trash (restorable from Trash).
	foreach ( array( array( 'sample-page', 'page' ), array( 'hello-world', 'post' ) ) as $sample ) {
		$post = get_page_by_path( $sample[0], OBJECT, $sample[1] );
		if ( $post && 'trash' !== $post->post_status ) {
			wp_trash_post( $post->ID );
			$log[] = 'Moved to trash: ' . $post->post_title;
		}
	}

	// 5. Starter health articles.
	$term = term_exists( 'Health Tips', 'category' );
	if ( ! $term ) {
		$term = wp_insert_term( 'Health Tips', 'category', array( 'slug' => 'health-tips-articles' ) );
	}
	$cat_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;

	foreach ( gcp_setup_posts() as $article ) {
		if ( get_page_by_path( $article['slug'], OBJECT, 'post' ) ) {
			$log[] = 'Already exists, left unchanged: ' . $article['title'];
			continue;
		}
		$id = wp_insert_post(
			wp_slash(
				array(
					'post_title'    => $article['title'],
					'post_name'     => $article['slug'],
					'post_type'     => 'post',
					'post_status'   => 'publish',
					'post_excerpt'  => $article['excerpt'],
					'post_content'  => $article['content'],
					'post_category' => $cat_id ? array( $cat_id ) : array(),
				)
			),
			true
		);
		if ( is_wp_error( $id ) ) {
			$log[] = 'Could not add article: ' . $article['title'];
			continue;
		}
		$image = gcp_setup_find_attachment( $article['image'] );
		if ( $image ) {
			set_post_thumbnail( $id, $image );
		}
		$log[] = 'Added article: ' . $article['title'] . ( $image ? '' : ' (no featured image found for ' . $article['image'] . ')' );
	}

	return $log;
}

/* -------------------------------------------------------------------------
 * Admin screen
 * ---------------------------------------------------------------------- */

add_action(
	'admin_menu',
	function () {
		add_theme_page( "God's Care setup", "God's Care setup", 'manage_options', 'gcp-setup', 'gcp_setup_screen' );
	}
);

add_action(
	'admin_post_gcp_run_setup',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You are not allowed to do this.', 'pharmacy-child' ), 403 );
		}
		check_admin_referer( 'gcp_run_setup' );

		set_transient( 'gcp_setup_log_' . get_current_user_id(), gcp_run_site_setup(), 10 * MINUTE_IN_SECONDS );
		wp_safe_redirect( admin_url( 'themes.php?page=gcp-setup&done=1' ) );
		exit;
	}
);

function gcp_setup_screen() {
	$log = get_transient( 'gcp_setup_log_' . get_current_user_id() );
	?>
	<div class="wrap">
		<h1>God's Care setup</h1>

		<?php if ( isset( $_GET['done'] ) && is_array( $log ) ) : ?>
			<div class="notice notice-success"><p><strong>Setup finished.</strong> <a href="<?php echo esc_url( home_url( '/' ) ); ?>">View the site</a></p></div>
			<ul style="list-style:disc;padding-left:1.5em">
				<?php foreach ( $log as $line ) : ?>
					<li><?php echo esc_html( $line ); ?></li>
				<?php endforeach; ?>
			</ul>
			<?php delete_transient( 'gcp_setup_log_' . get_current_user_id() ); ?>
		<?php endif; ?>

		<p>This builds the whole site from the theme's designs in one step:</p>
		<ul style="list-style:disc;padding-left:1.5em">
			<li>Creates or refreshes the pages Home, About, Services, Book an Appointment, Medicine Availability, Health Tips, Prescription Refill, Contact and Health Blog.</li>
			<li>Sets Home as the homepage and Health Blog as the blog page.</li>
			<li>Sets the site title, tagline, time zone and "Post name" permalinks.</li>
			<li>Moves the WordPress "Hello world!" post and "Sample Page" to the trash.</li>
			<li>Adds three health articles, using health_tips_1, _2 and _3 from the Media Library as featured images.</li>
		</ul>
		<p><strong>Note:</strong> refreshing a page replaces its content with the latest design. Edits made directly to those pages will be overwritten. Existing articles are never changed.</p>

		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="gcp_run_setup">
			<?php wp_nonce_field( 'gcp_run_setup' ); ?>
			<?php submit_button( 'Build the site', 'primary', 'submit', false ); ?>
		</form>
	</div>
	<?php
}
