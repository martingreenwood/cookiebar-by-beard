<?php
if( !defined( 'ABSPATH' ) )
{
	exit;
}

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
