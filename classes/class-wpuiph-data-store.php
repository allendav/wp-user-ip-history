<?php

class WPUIPH_Data_Store {

	private static $instance;

	public static function getInstance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

	private function __clone() {
	}

	private function __wakeup() {
	}

	protected function __construct() {
		add_action( 'init', array( $this, 'update_history_for_current_user' ) );
	}

	public function update_history_for_current_user() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$current_time = current_time( 'timestamp' );
		$current_date = date( 'Y-m-d', $current_time );

		$current_user = wp_get_current_user();
		$current_user_ID = $current_user->ID;

		$remote_addr = $_SERVER['REMOTE_ADDR'];

		$user_history_meta = get_user_meta( $current_user_ID, 'wpuiph_ip_history', true );
		if ( empty( $user_history_meta ) ) {
			$user_history_meta = array();
		}

		if ( array_key_exists( $remote_addr, $user_history_meta ) ) {
			$last_seen_ymd = $user_history_meta[ $remote_addr ][ 'last_seen_ymd' ];
			if ( $last_seen_ymd !== $current_date ) {
				$user_history_meta[ $remote_addr ][ 'last_seen_ymd' ] = $current_date;
				update_user_meta( $current_user_ID, 'wpuiph_ip_history', $user_history_meta );
			}
		} else {
			$user_history_meta[ $remote_addr ] = array(
				'first_seen_ymd' => $current_date,
				'last_seen_ymd' => $current_date
			);
			update_user_meta( $current_user_ID, 'wpuiph_ip_history', $user_history_meta );
		}
	}

	public function get_history_for_user_id( $user_ID ) {
		$user_history_meta = get_user_meta( $user_ID, 'wpuiph_ip_history', true );
		if ( empty( $user_history_meta ) ) {
			$user_history_meta = array();
		}

		return $user_history_meta;
	}

}

WPUIPH_Data_Store::getInstance();
