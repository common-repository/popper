<?php
/**
 * Set our block attribute defaults.
 *
 * @package popper-pro
 */

namespace Popper\Actions;

/**
 * Action Handler
 *
 * @since 1.0.0
 */
abstract class Action {

	/**
	 * The action label.
	 *
	 * @var string $type Type of action.
	 */
	protected $type = '';

	/**
	 * The action label.
	 *
	 * @var string $label Action label.
	 */
	protected $label = '';

	/**
	 * The action settings.
	 *
	 * @var array $settings Array of settings.
	 */
	protected $settings = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'popper_process_form_action_' . $this->type, array( $this, 'process' ), 10, 3 );
	}

	/**
	 * Hooks
	 */
	public function hook() {
		add_action( 'popper_process_form_action_' . $this->type, array( $this, 'process' ), 10, 3 );
	}

	/**
	 * Process action.
	 *
	 * @param array $settings The action settings.
	 * @param array $data The form data to process.
	 */
	abstract public function process( $settings, $data );

	/**
	 * Get key stored in DB.
	 *
	 * @return string
	 */
	protected function get_key() {
		$options = get_option( 'popper' );
		$key = $options['integrations'][ $this->type ];
		return $key;
	}
}
