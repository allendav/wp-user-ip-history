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

		add_filter( 'privacy_policy_section', array( $this, 'get_privacy_policy_section' ), 10, 2 );
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

	public function get_privacy_policy_section( $content, $name ) {
		if ( ! is_array( $content ) ) {
			$content = array();
		}

		if ( 'privacy-what-personal-data-collected' === $name ) {
			$content[] = __( 'We collect your IP address when you visit this website <strong>but only if you are logged in</strong>.', 'wpuiph' );
		}

		if ( 'privacy-why-personal-data-collected' === $name ) {
			$content[] = __( 'We store a list of the IP addresses you use to access this website as a guard against unauthorized access to your account.', 'wpuiph' );
		}

		if ( 'privacy-sharing-personal-data' === $name ) {
			$content[] = __( 'We never share your IP address history with others.', 'wpuiph' );
		}

		if ( 'privacy-storing-personal-data' === $name ) {
			$content[] = __( 'We store your IP address history in a database on this server. Only administrators can see your IP address history, and we require all administrator accounts to use strong passwords.', 'wpuiph' );
		}

		if ( 'privacy-retaining-personal-data' === $name ) {
			$content[] = __( 'We retain your IP address history forever, or until you request it to be deleted.', 'wpuiph' );
		}

		if ( 'privacy-user-options-personal-data' === $name ) {
			$content[] = __( 'You cannot opt-out of us collecting your IP address.', 'wpuiph' );
		}

		if ( 'privacy-user-managing-personal-data' === $name ) {
			if ( is_user_logged_in() ) {
				$content[] = sprintf(
					__( 'You may view your own IP address history in your <a href="%s">User Profile</a> at any time, and you may request deletion of the history at any time.', 'wpuiph' ),
					admin_url( 'profile.php' )
				);
			} else {
				$content[] = __( 'You may view your own IP address history in your User Profile at any time, and you may request deletion of the history at any time.', 'wpuiph' );
			}
		}

		return $content;
	}

}

WPUIPH_Data_Store::getInstance();
