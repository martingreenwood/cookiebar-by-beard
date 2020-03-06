<?php
if( !defined( 'ABSPATH' ) ) {
	exit;
}

$this->cbbb = get_option( 'cbbb_option_name' );

?>

<style> .indent {padding-left: 2em} </style>

<div class="wrap">

	<h1><?php _e( 'Cookiebar by Beard', 'cbbb') ?></h1>

	<form action="options.php" method="post" id="cbbb">
	<?php settings_fields( 'cbbb_option_group' ); ?>
		<fieldset>

			<ul>

				<li>
					<h3>General Settings</h3>
					<p>General settings for the cookie bar.</p>
					<p>
						Make sure you add the shortcode '[cbbbcookies]' to your cookie page or add '&lt;?php echo do_shortcode['[cbbbcookies]'] ;?&gt;' to your cookie page template.
					</p>
					<?php
					do_settings_sections( 'cbbb-admin' );
					?>

				</li>

			</ul>

		</fieldset>

		<p class="submit">
			<?php submit_button(); ?>
		</p>

	</form>

</div>
