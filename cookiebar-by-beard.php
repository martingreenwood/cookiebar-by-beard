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

	/*===============================
    =            General            =
    ===============================*/

    // Callback for Social Wall Title
    public function cbbb_title_cb()
	{
        printf(
            '<input class="regular-text" type="text" name="cbbb_option_name[cbbb_title]" id="cbbb_title" value="%s">',
            isset( $this->cbbb['cbbb_title'] ) ? esc_attr( $this->cbbb['cbbb_title']) : ''
        );
    }

    public function cbbb_intro_cb()
	{
        printf(
            '<textarea class="big-text" cols="50" rows="6" name="cbbb_option_name[cbbb_intro]" id="cbbb_intro">%s</textarea>',
            isset( $this->cbbb['cbbb_intro'] ) ? esc_attr( $this->cbbb['cbbb_intro']) : ''
        );
    }

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

	// Display the cookiebar
	// ----------------------------------

    public static function display_cookiebar_by_beard() {

    	$cbbb_options = get_option( 'cbbb_option_name' );
        $cbbb_title = $cbbb_options['cbbb_title'];
		$cbbb_intro = $cbbb_options['cbbb_intro'];
		$cbbb_cokepage = $cbbb_options['cbbb_cookiepage']
    	?>
		<div class="cookie-icon <?php if(isset($_COOKIE['cbbb_cookie'])): ?>show<?php endif; ?>">
			Cookies
		</div>
		<div class="cookie-check <?php if(isset($_COOKIE['cbbb_cookie'])): ?>closed<?php endif; ?>">

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
							<p><?php bloginfo( 'name' ) ?> uses cookies – including third party cookies – to collect information about how visitors use our website.
								They help us give you the best possible experience &amp; continually improve our site.
								By clicking the "Accept" button you agree to the use of these cookies. For further details about our use of cookies, or to change your preferences at any time, please click "Find out more including how to reject cookies".</p>
						<?php endif; ?>

					</header>
				</div>

				<!-- <div class="types">
					<div class="type">
						<div class="toggle">
							<input class="tgl tgl-ios" id="cb2" type="checkbox" <?php if(isset($_COOKIE['analyticcheck'])): ?>checked<?php endif; ?>>
							<label class="tgl-btn" for="cb2"></label>
						</div>
						<h3>Analytics cookies</h3>
						<p>We'd like to set Google Analytics cookies to help us to improve our website by collecting and reporting information on how you use it. The cookies collect information in a way that does not directly identify anyone. For more information on how these cookies work, please see our Cookies page.</p>
					</div>
				</div> -->

				<div class="actions">
					<a class="reject" href="<?php echo get_permalink( $cbbb_cokepage ); ?>">Find out more including how to reject cookies</a>
					<button type="button" name="content">ACCEPT</button>
				</div>
			</div>

		</div>
    	<?php
    }

	/*================================
   =            Sanitize            =
   ================================*/

   public function cbbb_sanitize($input) {
	   $sanitary_values = array();

	   // genral
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

WAB_Cookiebar::get_instance();


?>
