<?php

class WPUIPH_User_Profile_View {

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
		add_action( 'show_user_profile', array( $this, 'add_user_profile_section' ) );
		add_action( 'edit_user_profile', array( $this, 'add_user_profile_section' ) );
	}

	public function add_user_profile_section( $profile_user ) {
		?>
			<h2>
				<?php esc_html_e( 'IP Address History', 'wpuiph' ); ?>
			</h2>

			<table class="form-table">
				<tbody>
					<tr>
						<th>
							<?php esc_html_e( 'IP Address History', 'wpuiph' ); ?>
						</th>
						<td>
							<?php $this->render_history_for_user_id( $profile_user->ID ); ?>
						</td>
					</tr>
				</tbody>
			</table>
		<?php
	}

	protected function render_history_for_user_id( $user_ID ) {
		$history = WPUIPH_Data_Store::getInstance()->get_history_for_user_id( $user_ID );

		if ( empty( $history ) ) {
			esc_html_e( 'No IP address history is available.', 'wpuiph' );
			return;
		}

		?>
			<table class="widefat striped">
				<tbody>
					<tr>
						<td>
							<?php esc_html_e( 'IP Address', 'wpuiph' ); ?>
						</td>
						<td>
							<?php esc_html_e( 'First Seen (YYYY-MM-DD)', 'wpuiph' ); ?>
						</td>
						<td>
							<?php esc_html_e( 'Last Seen (YYYY-MM-DD)', 'wpuiph' ); ?>
						</td>
					</tr>
					<?php
						foreach( $history as $ip_addr => $history_item ) {
							?>
								<tr>
									<td>
										<?php echo esc_html( $ip_addr ); ?>
									</td>
									<td>
										<?php echo esc_html( $history_item[ 'first_seen_ymd' ] ); ?>
									</td>
									<td>
										<?php echo esc_html( $history_item[ 'last_seen_ymd' ] ); ?>
									</td>
								</tr>
							<?php
						}
					?>
				</tbody>
			</table>
		<?php
	}

}

WPUIPH_User_Profile_View::getInstance();
