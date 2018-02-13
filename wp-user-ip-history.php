<?php
/*
Plugin Name: WP User IP History
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once( plugin_basename( 'classes/class-wpuiph-data-store.php' ) );
require_once( plugin_basename( 'classes/class-wpuiph-user-profile-view.php' ) );

