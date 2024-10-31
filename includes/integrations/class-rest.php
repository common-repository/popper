<?php
/**
 * Base class to manage API.
 *
 * @package popper-pro
 */

namespace Popper\Integrations;

/**
 * MailChimp API Handler
 */
class Rest extends \WP_REST_Controller {

	/**
	 * The namespace.
	 *
	 * @var string
	 */
	protected $namespace;

	/**
	 * The service name.
	 *
	 * @var string
	 */
	protected $service = 'integrations';

	/**
	 * The sdk class.
	 *
	 * @var string
	 */
	protected $sdk = 'integrations';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->namespace = 'popper-pro/v1';
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes
	 *
	 * @return void
	 */
	public function register_routes() {
		register_rest_route(
			$this->namespace,
			'/integrations/lists',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_lists' ),
				'permission_callback' => array( $this, 'update_settings_permissions' ),
				'args'                => array(
					'service' => array(
						'description' => __( 'The integration service name.' ),
						'type'        => 'string',
						'required'    => true,
					),
				),
			)
		);
		register_rest_route(
			$this->namespace,
			'/integrations/validate',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'validate' ),
				'permission_callback' => array( $this, 'update_settings_permissions' ),
			)
		);
		register_rest_route(
			$this->namespace,
			'/integrations/merge-fields',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_merge_fields' ),
				'permission_callback' => array( $this, 'update_settings_permissions' ),
				'args'                => array(
					'service' => array(
						'description' => __( 'The integration service name.' ),
						'type'        => 'string',
						'required'    => true,
					),
					'list' => array(
						'description' => __( 'The integration service name.' ),
						'type'        => 'numeric',
						'required'    => true,
					),
				),
			)
		);
	}

	/**
	 * Retrieves a collection of lists.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_lists( $request ) {

		$service = $request->get_param( 'service' );

		$api = $this->get_sdk( $service );

		$result = $api->get_lists_select();

		$service = strtolower( $service );
		$lists = apply_filters( "popper_pro_rest_{$service}_lists", $result );

		return rest_ensure_response( $lists );
	}

	/**
	 * Retrieves a collection of merge fields.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function get_merge_fields( $request ) {

		$service = $request->get_param( 'service' );
		$list  = $request->get_param( 'list' );

		if ( empty( $list ) ) {
			return new \WP_Error( 400, __( 'No list provided.', 'formello-pro' ) );
		}

		$api = $this->get_sdk( $service );

		$result = $api->get_list_merge_fields_select( $list );

		$service = strtolower( $service );
		$merge_fields = apply_filters( "popper_pro_rest_{$service}_merge_fields", $result );

		return rest_ensure_response( $merge_fields );
	}

	/**
	 * Check validity of api key.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 *
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */
	public function validate( $request ) {

		$key = $request->get_param( 'key' );

		if ( empty( $key ) ) {
			$error = new \WP_Error( 400, __( 'Missing key.' ) );
			return rest_ensure_response( $error );
		}
		$service = $request->get_param( 'service' );

		$sdk = '\\Popper_Pro\\Integrations\\' . $service;
		$api = new $sdk( $key );

		$result = $api->get_lists();

		return rest_ensure_response( $result );
	}

	/**
	 * Retrieve the service sdk class.
	 *
	 * @param string $service The service.
	 * @return mixed $api
	 */
	private function get_sdk( $service ) {
		$settings    = get_option( 'popper' );
		$this->sdk   = '\\Popper_Pro\\Integrations\\' . $service;
		$api         = new $this->sdk( $settings['integrations'][ $service ] );

		return $api;
	}

	/**
	 * Rest permissions check
	 *
	 * @return boolean
	 */
	public function update_settings_permissions() {
		return current_user_can( 'manage_options' );
	}
}
