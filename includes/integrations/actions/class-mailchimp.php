<?php
/**
 * Perform Mailchimp Action.
 *
 * @package popper-pro
 */

namespace Popper\Actions;

use Popper\Integrations\Mailchimp as SDK;

/**
 * Class Mailchimp action
 *
 * @since 1.0.0
 */
class Mailchimp extends Action {

	/**
	 * The action label.
	 *
	 * @var string
	 */
	protected $type = 'Mailchimp';

	public function process( $settings, $data ) {

		if ( empty( $settings['list'] ) ||
			empty( $data['email'] )
		) {
			return false;
		}

		$key = $this->get_key();

		if ( empty( $key ) ) {
			return array();
		}

		$api = new SDK( $key );

		$payload = array(
			'name'  => $data['name'] ?? '',
			'email_address' => $data['email'],
			'status' => $settings['status'],
		);

		if ( ! empty( $settings['merge_fields'] ) ) {
			$payload['merge_fields'] = array();
			foreach ( $settings['merge_fields'] as $key => $value ) {
				$payload['merge_fields'][ $key ] = $data[ $value ];
			}
		}

		if ( ! empty( $settings['tags'] ) ) {
			$payload['tags'] = $settings['tags'];
		}

		$result = $api->add_subsciber( $settings['list'], $payload );

		return $result;
	}
}
