<?php
/*
 * Plugin Name: Cookiebar by Beard
 * Plugin URI:  https://wearebeard.com/cookiebar
 * Description: Cookiebar by beard is aplugin developed to give your visitord control over the cookies you set.
 * Version:     1.0.0
 * Author:      Martin Greenwood
 * Author URI:  http://wearebeard.com
 * Domain Path: /languages
 * Text Domain: cbbb
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// The Class
class WAB_Cookiebar {

	private static $instance = null;
	private $cbbb;

	public static function get_instance()
	{
		if ( is_null( self::$instance ) )
		{
			self::$instance = new self;
		}
		return self::$instance;
	}

	private function __construct()
	{
		add_action( 'admin_menu', array( $this, 'cbbb_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'cbbb_page_init' ) );

		/*----------  load action links  ----------*/
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links' );

		function add_action_links ( $link )
		{
			$cbbb_links = array(
				'<a href="' . admin_url( 'options-general.php?page=cookiebar-by-beard' ) . '">Cookiebar Settings</a>',
			);
			return array_merge( $link, $cbbb_links );
		}

	}

	// adds plugin settings page
	// -------------------------

	public function cbbb_add_plugin_page()
	{
		add_options_page(
			'Cookiebar by Beard',	// page_title
			'Cookiebar by Beard',	// menu_title
			'manage_options',		// capability
			'cookiebar-by-beard',	// menu_slug
			array( $this, 'cbbb_create_admin_page' ) // function
		);
	}

	// creates the admin / options page form
	// -------------------------------------

	public function cbbb_create_admin_page()
	{
		include dirname( __FILE__ ) . '/includes/settings.php';
	}

	// defines the options pages settings
	// ----------------------------------

	public function cbbb_page_init()
	{
		include dirname( __FILE__ ) . '/includes/get-options.php';
	}

	// Callback for cookie bar title
	//------------------------------

    public function cbbb_title_cb()
	{
        printf(
            '<input class="regular-text" type="text" name="cbbb_option_name[cbbb_title]" id="cbbb_title" value="%s">',
            isset( $this->cbbb['cbbb_title'] ) ? esc_attr( $this->cbbb['cbbb_title']) : ''
        );
    }

	// Callback function for the  cookiebar intro
	// ------------------------------------------

	public function cbbb_intro_cb()
	{
		printf(
			'<textarea class="big-text" cols="50" rows="6" name="cbbb_option_name[cbbb_intro]" id="cbbb_intro">%s</textarea>',
			isset( $this->cbbb['cbbb_intro'] ) ? esc_attr( $this->cbbb['cbbb_intro']) : ''
		);
	}

	// Callback to select the cookie page
	// ----------------------------------

	public function cbbb_cookiepage_cb()
	{
	?>
	<select name="cbbb_option_name[cbbb_cookiepage]" id="cbbb_cookiepage">
		<option value="">Please selct a page</option>
		<?php
		if( $pages = get_pages() ){
			foreach( $pages as $page ){
				echo '<option value="' . $page->ID . '" ' . selected( $page->ID, $this->cbbb['cbbb_cookiepage'] ) . '>' . $page->post_title . '</option>';
			}
		}
		?>
	</select>
	<?php
	}

	// Callback function for the  cookiebar cookie intro
	// ------------------------------------------

	public function cbbb_cookiebartime_cb()
	{
		printf(
            '<input class="regular-text" type="text" name="cbbb_option_name[cbbb_cookiebar_time]" id="cbbb_cookiebar_time" value="%s">',
            isset( $this->cbbb['cbbb_cookiebar_time'] ) ? esc_attr( $this->cbbb['cbbb_cookiebar_time']) : ''
        );
	}

	// Display the cookiebar
	// ---------------------

	public static function display_cookiebar_by_beard() {

		$cbbb_options = get_option( 'cbbb_option_name' );
		$cbbb_title = $cbbb_options['cbbb_title'];
		$cbbb_intro = $cbbb_options['cbbb_intro'];
		$cbbb_cookiepage = $cbbb_options['cbbb_cookiepage'];
		$cbbb_cookiebar_time = $cbbb_options['cbbb_cookiebar_time'];
		?>
		<div class="cbbb-cookie-icon <?php if( isset($_COOKIE['cbbb_cookie']) || !is_page($cbbb_cookiepage) ): ?>show<?php endif; ?>">
			Cookies
		</div>
		<?php if (!is_page($cbbb_cookiepage)): ?>
		<div class="cbbb-cookie-check <?php if( isset($_COOKIE['cbbb_cookie']) || is_page($cbbb_cookiepage) ): ?>closed<?php endif; ?>">

			<div class="container">

				<div class="row">

					<div class="box">

						<div class="info">
							<header>
								<?php if ($cbbb_title): ?>
									<h3><?php echo $cbbb_title; ?></h3>
								<?php else: ?>
									<h3>Cookies on <?php bloginfo( 'name' ) ?></h3>
								<?php endif; ?>
								<?php if ($cbbb_intro): ?>
									<p><?php echo $cbbb_intro; ?></p>
								<?php else: ?>
									<p><?php bloginfo( 'name' ) ?> uses cookies – including third party cookies – to collect information about how visitors use our website. They help us give you the best possible experience &amp; continually improve our site. By clicking the "Accept" button you agree to the use of these cookies. For further details about our use of cookies, or to change your preferences at any time, please click "Find out more including how to reject cookies".</p>
								<?php endif; ?>

							</header>
						</div>

						<div class="actions">
							<a class="more" href="<?php echo get_permalink( $cbbb_cookiepage ); ?>">Find out more including how to reject cookies</a>
							<button type="button" name="consent">Accept All</button>
						</div>
					</div>

				</div>

			</div>

		</div>
		<?php endif; ?>
		<?php
	}

	// Sanitize inputs
	// ---------------

	public function cbbb_sanitize($input)
	{
		$sanitary_values = array();

		if ( isset( $input['cbbb_title'] ) ) {
			$sanitary_values['cbbb_title'] = sanitize_text_field( $input['cbbb_title'] );
		}
		if ( isset( $input['cbbb_intro'] ) ) {
			$sanitary_values['cbbb_intro'] = sanitize_text_field( $input['cbbb_intro'] );
		}

		return $sanitary_values;
	}
}
add_action( 'wp_footer', array( 'WAB_Cookiebar', 'display_cookiebar_by_beard' ), 99, 0 );

function cbbbcookies()
{
	$cbbb_options = get_option( 'cbbb_option_name' );
	$cbbb_cookiebar_time = $cbbb_options['cbbb_cookiebar_time'];
	?>
	<div class="cbbb-cookie-wrap">
		<div class="cbbb-cookie-box">
			<div class="cbbb-cookie-options">

				<div class="cbbb-cookie-header">
					<div class="cbbb-cookie-header-title">
						<h3>You have the control.</h3>
						<p>You have activated/deactivated the following cookie categories.</p>
					</div>
				</div>

				<div class="cbbb-cookie-necessary">
					<div class="cbbb-cookie-necessary-title">
						<h3>Necessary cookies</h3>
						<p>Necessary cookies enable core functionality such as security, network management, and accessibility. You may disable these by changing your browser settings, but this may affect how the website functions</p>
					</div>
				</div>

				<div class="cbbb-cookie-controls">
				<?php
					$cbbb_cpt_args = array(
						'post_type' 		=> 'cbbb_cookie',
						'posts_per_page' 	=> -1,
					);
					$loop = new WP_Query($cbbb_cpt_args);

					if ($loop->have_posts()):
						while ($loop->have_posts()) : $loop->the_post();
							$cbbb_script = get_post_meta(get_the_id(), '_cbbb_script_meta_key', true);
							$cbbb_script_location = get_post_meta(get_the_id(), '_cbbb_script_location_meta_key', true);
							$cookie_title = get_the_title();
							$cookie_content = get_the_content();
							$cookie_name = str_replace(" ","", strtolower($cookie_title));
							?>
							<div class="cbbb-cookie-control">
								<div class="cbbb-cookie-control-title">
									<h3><?php echo $cookie_title; ?></h3>
									<?php echo $cookie_content; ?>
								</div>
								<div Class="cbbb-cookie-control-toggle">
									<div class="onoffswitch">

										<input type="checkbox" name="<?php echo $cookie_name ?>" class="cbbb-cookie-checkbox" id="<?php echo $cookie_name; ?>" <?php if (isset($_COOKIE[$cookie_name])): ?>checked<?php endif; ?>>

									</div>
								</div>
							</div>
							<?php
						endwhile;
					else:
						// no cookie things to set.
					endif;
					wp_reset_query();
				?>
				<div class="cbbb-cookie-save">
					<button type="button" data-cookie-time="<?php echo $cbbb_cookiebar_time; ?>" name="cbbb-save">Save Settings</button>
				</div>
			</div>
		</div>
	</div>
	<?php
}
add_shortcode( 'cbbbcookies', 'cbbbcookies' );



// Create custom post type
// -----------------------

function cbbb_cpt()
{
	register_post_type('cbbb_cookie', array(
		'labels' => array(
			'name' => __('Cookie Scripts', 'cbbb'),
			'singular_name' => __('Cookie Script')
		),
		'description' => __('Add your third party cookie scripts here, such as analytics, live chats, tracking cookies etc.'),
		'public' => true,
		'exclude_from_search' => true,
		'publicly_queryable' => false,
		'show_ui' => true,
		'show_in_nav_menus' => true,
		'show_in_rest' => true,
		'hierarchical' => false,
		'supports' => array(
			'title',
			'editor',
			'excerpt',
			'page-attributes',
			'custom-fields'
		),
		'menu_icon' =>  'dashicons-marker',
	));
}
add_action( 'init', 'cbbb_cpt', 10, 0 );

// Default content for the cookie piosts
// Really just to hint what they need to add
// -----------------------------------------

function add_default_content( $content )
{
	global $post_type;
	switch ( $post_type ) {
		case 'cbbb_cookie':
			$content = "<p>Enter information about the script here. Mayne include the cookies it creates and what they are used for. Remember that the cookies used for content will be 'post-name'-consent.</p>";
			break;
	}
	return $content;
}
add_filter( 'default_content', 'add_default_content' );

// Meta box setup function
// --------------------------

function cbbb_add_custom_box()
{
	$screens = ['cbbb_cookie'];
	foreach ($screens as $screen) {
		add_meta_box(
			'wporg_box_id',				// Unique ID
			'Cookie Script',			// Box title
			'cbbb_custom_box_html',	// Content callback, must be of type callable
			$screen						// Post type
		);
	}
}
add_action('add_meta_boxes', 'cbbb_add_custom_box');

// Used by the cbbb_add_custom_box() function
// Adds the html field to save the script.
function cbbb_custom_box_html($post)
{
	$cbbb_script = get_post_meta($post->ID, '_cbbb_script_meta_key', true);
	$cbbb_script_location = get_post_meta($post->ID, '_cbbb_script_location_meta_key', true);
	?>
	<h4>Where should this go?</h4>
	<label class="screen-reader-text" for="cbbb_sctipt_location_field">Select a location</label>
    <select name="cbbb_sctipt_location_field" id="cbbb_sctipt_location_field" style="width:50%" class="components-select-control__input">
        <option value="">Select one....</option>
        <option value="wp_head" <?php selected($cbbb_script_location, 'wp_head'); ?>>Header</option>
        <option value="wp_footer" <?php selected($cbbb_script_location, 'wp_footer'); ?>>Footer</option>
    </select>
	<h4>Add your custom code / script here. This should be a peice of code (javascript) you wanrt to run on the front end after gaining thr visitors consent.</h4>
	<label class="screen-reader-text" for="cbbb_script_field">Cusatom tracking / code</label>
	<textarea name="cbbb_script_field" class="components-textarea-control__input" id="cbbb_script_field" rows="8" style="width:100%"><?php echo $cbbb_script; ?></textarea>
	<?php
}

// Save the Meta information
// -------------------------

function cbbb_save_postdata($post_id)
{
	// script
	if (array_key_exists('cbbb_script_field', $_POST)) {
		update_post_meta(
			$post_id,
			'_cbbb_script_meta_key',
			$_POST['cbbb_script_field']
		);
	}
	// location§
	if (array_key_exists('cbbb_sctipt_location_field', $_POST)) {
		update_post_meta(
			$post_id,
			'_cbbb_script_location_meta_key',
			$_POST['cbbb_sctipt_location_field']
		);
	}
}
add_action('save_post', 'cbbb_save_postdata');


// Enqueue Styles / Scripts
// ------------------------

function cbbb_enqueue_base() {
	// register scripts
	wp_register_script( 'cbbb-js',  plugin_dir_url(__FILE__) . '/js/cbbb.js','','', true );
	wp_register_script( 'cbbb-jscookie',  plugin_dir_url(__FILE__) . '/js/js.cookie.min.js','','', true );
	wp_register_style( 'cbbb-css', plugin_dir_url(__FILE__) . '/css/cbbb.css','','', 'screen' );

	// enqueue scripts
	wp_enqueue_style( 'cbbb-css' );

	// Included jQuery check incase it has not already been added.
	if(!wp_script_is('jquery')) {
		wp_enqueue_script( 'jquery' );
	}

	wp_enqueue_script( 'cbbb-jscookie' );
	wp_enqueue_script( 'cbbb-js' );
}

if (!is_admin()) {
	add_action( 'wp_enqueue_scripts', 'cbbb_enqueue_base', 90 );
}

function cbbb_cookie_inline_js() {
?>
<script>
<?php
$cbbb_options = get_option( 'cbbb_option_name' );
$cbbb_cookiebar_time = $cbbb_options['cbbb_cookiebar_time'];
?>
(function($) {
	$('.cbbb-cookie-check .actions button').on("click", function() {
		Cookies.set('cbbb_cookie', 'closed', { expires: <?php echo $cbbb_cookiebar_time; ?> });
		<?php
		$cbbb_cpt_args = array(
			'post_type' 		=> 'cbbb_cookie',
			'posts_per_page' 	=> -1,
		);
		$loop = new WP_Query($cbbb_cpt_args);
		if ($loop->have_posts()): while ($loop->have_posts()) : $loop->the_post();
		$cookie_title = get_the_title();
		$cookie_name = str_replace(" ","", strtolower($cookie_title));
		?>
		Cookies.set('<?php echo $cookie_name; ?>', 'agreed', { expires: <?php echo $cbbb_cookiebar_time ?> });
		<?php
		endwhile;
		else:
		endif;
		wp_reset_query();
		?>

		$(".cbbb-cookie-check").toggleClass('closed');
		$(".cbbb-cookie-icon").toggleClass('show');

	});
	$('.cbbb-cookie-icon').on("click", function() {
		$(".cbbb-cookie-check").toggleClass('closed');
		$(".cbbb-cookie-icon").toggleClass('show');
	});
	$('.cbbb-cookie-save button').on("click", function() {
		<?php
		$cbbb_cpt_args = array(
			'post_type' 		=> 'cbbb_cookie',
			'posts_per_page' 	=> -1,
		);
		$loop = new WP_Query($cbbb_cpt_args);
		if ($loop->have_posts()): while ($loop->have_posts()) : $loop->the_post();
		$cookie_title = get_the_title();
		$cookie_name = str_replace(" ","", strtolower($cookie_title));
		?>
		if($('#<?php echo $cookie_name; ?>').prop('checked') == true){
			Cookies.set('<?php echo $cookie_name; ?>', 'agreed', { expires: <?php echo $cbbb_cookiebar_time ?> });
		} else {
			Cookies.remove('<?php echo $cookie_name; ?>');
		}
		<?php
		endwhile;
		else:
		endif;
		wp_reset_query();
		?>
		location.reload();
	});
})(jQuery);
</script>
<?php
}
add_action( 'wp_footer', 'cbbb_cookie_inline_js', 99, 0 );


function cbbb_head_scripts()
{
	$cbbb_cpt_args = array(
		'post_type' 		=> 'cbbb_cookie',
		'posts_per_page' 	=> -1,
		'meta_query' 		=> array(
			array(
				'key'     => '_cbbb_script_location_meta_key',
				'value'   => 'wp_head',
				'compare' => '=',
			),
		),
	);
	$loop = new WP_Query($cbbb_cpt_args);
	if ($loop->have_posts()): while ($loop->have_posts()) : $loop->the_post();
	echo "<!-- added by Cookiebar by Beard -->";
	echo get_post_meta(get_the_id(), '_cbbb_script_meta_key', true);
	echo "<!-- // end cookiebar script -->";
	endwhile;
	endif;
	wp_reset_query();
}
add_action( 'wp_head', 'cbbb_head_scripts', 99, 0 );

function cbbb_footer_scripts()
{
	$cbbb_cpt_args = array(
		'post_type' 		=> 'cbbb_cookie',
		'posts_per_page' 	=> -1,
		'meta_query' 		=> array(
			array(
				'key'     => '_cbbb_script_location_meta_key',
				'value'   => 'wp_footer',
				'compare' => '=',
			),
		),
	);
	$loop = new WP_Query($cbbb_cpt_args);
	if ($loop->have_posts()): while ($loop->have_posts()) : $loop->the_post();
	echo get_post_meta(get_the_id(), '_cbbb_script_meta_key');
	endwhile;
	endif;
	wp_reset_query();
}
add_action( 'wp_footer', 'cbbb_footer_scripts', 99, 0 );

WAB_Cookiebar::get_instance();

?>
