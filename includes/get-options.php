<?php
if( !defined( 'ABSPATH' ) )
{
	exit;
}

// Register settings blocka
register_setting(
    'cbbb_option_group',							// option_group
    'cbbb_option_name',								// option_name
    array( $this, 'cbb_sanitize' )					// sanitize_callback
);

add_settings_section(
    'cbbb_setting_section',							// id
    '',												// title
    '',												// callback
    'cbbb-admin'									// page
);

// Register settings fields
add_settings_field(
	'cbbb_title',									// id
	'Cookiebar Title',								// title
	array( $this, 'cbbb_title_cb' ),				// callback
	'cbbb-admin',									// page
	'cbbb_setting_section'   						// section
);

add_settings_field(
	'cbbb_intro',									// id
	'Cookiebar Intro',								// title
	array( $this, 'cbbb_intro_cb' ),				// callback
	'cbbb-admin',									// page
	'cbbb_setting_section'							// section
);

add_settings_field(
	'cbbb_cookiepage',								// id
	'Cookie Page',									// title
	array( $this, 'cbbb_cookiepage_cb' ),			// callback
	'cbbb-admin',									// page
	'cbbb_setting_section'							// section
);

add_settings_field(
	'cbbb_cookiebar_time',						// id
	'Cookiebar Length',								// title
	array( $this, 'cbbb_cookiebartime_cb' ),			// callback
	'cbbb-admin',									// page
	'cbbb_setting_section'							// section
);
