<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

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
			'page-attributes',
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
			$content = "<p>Enter information about the cookie script here. Maybe include the cookies it creates and what they are used for. Remember that the cookies used for content will be 'post-name'-consent.</p>";
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
			'cbbb_custom_box_html',		// Content callback, must be of type callable
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
	<h4>Add your custom code / script here. This should be a peice of code (javascript) you want to run on the front end after gaining the visitors consent.</h4>
	<label class="screen-reader-text" for="cbbb_script_field">Cusatom tracking / code</label>
	<textarea name="cbbb_script_field" class="components-textarea-control__input" id="cbbb_script_field" rows="8" style="width:100%"><?php echo $cbbb_script; ?></textarea>
	<?php
}

// Save the Meta information
// -------------------------

function cbbb_save_postdata($post_id)
{
	// script
	if (isset($_POST['cbbb_script_field'])) {
		update_post_meta(
			$post_id,
			'_cbbb_script_meta_key',
			htmlspecialchars($_POST['cbbb_script_field'])
		);
	}
	// location§
	if (array_key_exists('cbbb_sctipt_location_field', $_POST)) {
		update_post_meta(
			$post_id,
			'_cbbb_script_location_meta_key',
			sanitize_text_field( $_POST['cbbb_sctipt_location_field'] )
		);
	}
}
add_action('save_post', 'cbbb_save_postdata');


// Inline JS to save cookies
// -------------------------

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

// Adds cookies scrips from custom post types tothe header / footer
// ----------------------------------------------------------------

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
	echo htmlspecialchars_decode( get_post_meta(get_the_id(), '_cbbb_script_meta_key', true) );
	// echo esc_html();
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
	echo htmlspecialchars_decode( get_post_meta(get_the_id(), '_cbbb_script_meta_key', true) );
	endwhile;
	endif;
	wp_reset_query();
}
add_action( 'wp_footer', 'cbbb_footer_scripts', 99, 0 );


// Adds plugin action links
// -------------------------

function cbbb_plugin_action_links( $links ) {

	$links = array_merge( array(
		'<a href="' . esc_url( admin_url( '/options-general.php' ) ) . '">' . __( 'Settings', 'cbbb' ) . '</a>'
	), $links );

	return $links;

}
add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'cbbb_plugin_action_links' );
