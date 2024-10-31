<?php
/**
 * This file handles the form submission.
 *
 * @package popper
 */

namespace Popper;

/**
 * The conditions class.
 */
class Submission {
	/**
	 * Instance.
	 *
	 * @var instance
	 */
	private static $instance;

	/**
	 * The sanitized form data.
	 *
	 * @var array
	 */
	private $data;

	/**
	 * The form data errors.
	 *
	 * @var array
	 */
	private $errors;

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
		add_action( 'wp_ajax_popper', array( $this, 'handle_form' ), 5 );
		add_action( 'wp_ajax_nopriv_popper', array( $this, 'handle_form' ), 5 );
	}

	/**
	 * Process form submission.
	 *
	 * @since 1.0
	 */
	public function handle_form() {
		if ( check_ajax_referer( 'popper', '_nonce', false ) ) {
			// sanitize the request.
			$this->data = map_deep( $_POST, 'sanitize_text_field' );

			if ( $this->is_valid() ) {
				$this->process();
				wp_send_json_success(
					array(
						'errors' => $this->errors,
						'message' => __( 'Form submitted, thanks!', 'popper' ),
					)
				);
			} else {
				wp_send_json_error(
					array(
						'errors' => $this->errors,
						'message' => __( 'Ops, an error occurred.', 'popper' ),
					)
				);
			}
		} else {
			wp_send_json_error( __( 'Are you a bot?', 'popper' ) );
		}
	}

	/**
	 * Process actions.
	 *
	 * @return void
	 */
	private function process() {
		$actions = get_post_meta( $this->data['id'], '_popper_actions', true );

		$this->clean_data();

		foreach ( $actions as $settings ) {
			/**
			 * Processes the specified form action and passes related data.
			 *
			 * @param array $settings
			 * @param Form $form
			 */
			if ( $settings['async'] ) {
				wp_schedule_single_event(
					time() + 60,
					'popper_process_form_action_' . $settings['type'],
					array(
						'settings' => $settings,
						'data'     => $this->data,
					),
					true
				);
			} else {
				do_action(
					'popper_process_form_action_' . $settings['type'],
					$settings,
					$this->data
				);
			}
		}
	}

	/**
	 * Validate the data.
	 *
	 * This will check only for email field.
	 *
	 * @return boolean
	 */
	private function is_valid() {

		if ( empty( $this->data['id'] ) ) {
			$this->errors[] = __( 'Missing id', 'popper' );
			return false;
		}

		$fields = get_post_meta( $this->data['id'], '_popper_fields', true );

		foreach ( $fields as $name => $constraints ) {
			$rules = explode( ':', $constraints );
			if ( in_array( 'email', $rules ) && ! filter_var( $this->data[ $name ], FILTER_VALIDATE_EMAIL ) ) {
				$this->errors[] = __( 'Please insert a valid email', 'popper' );
			}
			if ( in_array( 'required', $rules ) && empty( $this->data[ $name ] ) ) {
				$this->errors[] = __( 'Please insert a value', 'popper' );
			}
		}

		if ( ! empty( $this->errors ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Clean the data.
	 *
	 * This will remove all non field value(ex. _nonce, ajax action...).
	 *
	 * @return boolean
	 */
	private function clean_data() {
		$fields = get_post_meta( $this->data['id'], '_popper_fields', true );
		$this->data = array_intersect_key( $this->data, $fields );
	}
}

Submission::get_instance();
