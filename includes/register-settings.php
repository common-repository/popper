<?php
/**
 * Register the plugin settings
 *
 * @package formello
 * @since   1.0.0
 */

namespace Popper;

use function Formello\Utils\recursive_sanitize_text_field;
use function Formello\Utils\formello_dir_url;
use function Formello\Utils\formello_default_options;

defined( 'ABSPATH' ) || exit;

/**
 * Register plugin settings.
 *
 * @since 1.0.0
 */
function register_settings() {
	$settings = array(
		'log' => array(
			'type' => 'boolean',
		),
		'version' => array(
			'type' => 'string',
		),
		'captcha' => array(
			'type' => 'object',
		),
		'integrations' => array(
			'type' => 'object',
			'additionalProperties' => true,
		),
		'google' => array(
			'type' => 'object',
			'properties' => array(
				'client_id' => array(
					'type' => 'string',
				),
				'client_secret' => array(
					'type' => 'string',
				),
			),
		),
		'uninstall' => array(
			'type' => 'boolean',
		),
	);

	$settings = apply_filters( 'popper_register_settings', $settings );

	register_setting(
		'popper',
		'popper',
		array(
			'description'  => __(
				'Settings for the Popper Block plugin.',
				'popper'
			),
			'type'         => 'object',
			'show_in_rest' => array(
				'schema' => array(
					'type'       => 'object',
					'properties' => $settings,
					'additionalProperties' => true,
				),
			),
			'default' => array(
				'log' => false,
				'uninstall' => false,
				'integrations' => array(),
				'google' => array(
					'client_id' => '',
					'client_secret' => '',
				),
			),
		)
	);
}
add_action( 'rest_api_init', __NAMESPACE__ . '\register_settings' );
add_action( 'admin_init', __NAMESPACE__ . '\register_settings' );
