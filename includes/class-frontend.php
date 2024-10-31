<?php
/**
 * This file handles the Display Rule conditions for Popper blocks.
 *
 * @package Popper
 */

namespace Popper;

/**
 * The conditions class.
 */
class Frontend {
	/**
	 * Instance.
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * Popups to show.
	 *
	 * @var popups
	 */
	private $popups;

	/**
	 *  Initiator
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'template_redirect', array( $this, 'popper_matcher' ), 5 );
		add_action( 'wp_body_open', array( $this, 'get_popups' ), 5 );
	}

	/**
	 * Output our popups.
	 *
	 * @since 1.7
	 */
	public function get_popups() {
		// phpcs:ignore
		echo $this->popups;
	}

	/**
	 * Get our popups based on conditions.
	 *
	 * @since 1.7
	 *
	 * @return array
	 */
	public function popper_matcher() {
		wp_add_inline_script(
			'popper-form-view-script',
			'const popper = ' . wp_json_encode(
				array(
					'ajaxUrl' => admin_url( 'admin-ajax.php' ),
					'_nonce'  => wp_create_nonce( 'popper' ),
				)
			),
			'before'
		);
		if ( is_admin() || wp_is_json_request() ) {
			return false;
		}
		global $wpdb;
		global $wp_embed;

		remove_filter( 'the_content', 'wpautop' );

		$popups = '';

		$rules = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM {$wpdb->prefix}posts 
				LEFT JOIN {$wpdb->prefix}postmeta 
				ON {$wpdb->prefix}posts.ID = {$wpdb->prefix}postmeta.post_id 
				WHERE {$wpdb->prefix}posts.post_type = %s 
				AND {$wpdb->prefix}postmeta.meta_key = %s 
				AND {$wpdb->prefix}posts.post_status = %s 
				ORDER BY {$wpdb->prefix}posts.ID;",
				'popper',
				'popper_rules',
				'publish',
			)
		);

		$matched = false;

		// loop through all rules for all boxes.
		foreach ( $rules as $rule ) {
			$popper_content = $rule->post_content;

			// Get the rules.
			$rule = maybe_unserialize( $rule->meta_value );

			if ( empty( $rule['date'] ) ) {
				$rule['date'] = array();
			}

			$matched = Conditions::show_data( $rule['location'], $rule['exclude'], $rule['user'], $rule['date'], $rule['running'] );

			if ( $matched ) {

				$popups .= $wp_embed->autoembed( $popper_content );

				$matched = false;
			}
		}
		$this->popups = do_blocks( $popups );
		add_filter( 'the_content', 'wpautop' );
	}
}

Frontend::get_instance();
