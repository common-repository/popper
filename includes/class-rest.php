<?php
/**
 * Manage Template API.
 *
 * @package Popper
 */

namespace Popper;

/**
 * REST_API Handler
 */
class Rest extends \WP_REST_Controller {

	/**
	 * Instance.
	 *
	 * @access private
	 * @var object Instance
	 */
	private static $instance;

	/**
	 * Namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'popper/v1';

	/**
	 * Initiator.
	 *
	 * @return object initialized object of class.
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * GenerateBlocks_Rest constructor.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register the routes
	 *
	 * @return void
	 */
	public function register_routes() {

		// Get Patterns.
		register_rest_route(
			$this->namespace,
			'/patterns',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_patterns' ),
				'permission_callback' => '__return_true',
			)
		);

		// Get Templates.
		register_rest_route(
			$this->namespace,
			'/support',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'open_ticket' ),
				'permission_callback' => '__return_true',
			)
		);

		// Regenerate CSS Files.
		register_rest_route(
			$this->namespace,
			'/select',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_select_data' ),
				'permission_callback' => array( $this, 'update_settings_permissions' ),
			)
		);
	}

	/**
	 * Get patterns.
	 *
	 * @return mixed
	 */
	public function get_patterns() {
		global $wp_filesystem;
		// Initialize the WP filesystem, no more using 'file-put-contents' function.
		if ( empty( $wp_filesystem ) ) {
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem( true );
		}

		$local_file = WP_PLUGIN_DIR . '/popper/assets/patterns.json';

		if ( $wp_filesystem->exists( $local_file ) ) {
			$patterns = json_decode( $wp_filesystem->get_contents( $local_file ), true );
			return new \WP_REST_Response( $patterns, 200 );
		}

		return new \WP_REST_Response( array(), 200 );
	}

	/**
	 * Get templates.
	 *
	 * @return mixed
	 */
	public function get_select_data() {
		$locations = \Popper\Conditions::get_conditions();
		$users     = \Popper\Conditions::get_user_conditions();

		return array(
			'locations' => $locations,
			'users'     => $users,
		);
	}

	/**
	 * Open ticket.
	 *
	 * @param \WP_REST_Request $request  request object.
	 *
	 * @return mixed
	 */
	public function open_ticket( \WP_REST_Request $request ) {
		return $this->success( true );
	}

	/**
	 * Checks if a given request has access to read the items.
	 *
	 * @param  WP_REST_Request $request Full details about the request.
	 *
	 * @return true|WP_Error True if the request has read access, WP_Error object otherwise.
	 */
	public function update_settings_permissions( $request ) {
		return current_user_can( 'manage_options' );
	}

	/**
	 * Retrieves the query params for the items collection.
	 *
	 * @return array Collection parameters.
	 */
	public function get_collection_params() {
		return array();
	}

	/**
	 * Success rest.
	 *
	 * @param mixed $response response data.
	 * @return mixed
	 */
	public function success( $response ) {
		return new \WP_REST_Response(
			array(
				'success'  => true,
				'response' => $response,
			),
			200
		);
	}

	/**
	 * Failed rest.
	 *
	 * @param mixed $response response data.
	 * @return mixed
	 */
	public function failed( $response ) {
		return new \WP_REST_Response(
			array(
				'success'  => false,
				'response' => $response,
			),
			200
		);
	}

	/**
	 * Error rest.
	 *
	 * @param mixed $code     error code.
	 * @param mixed $response response data.
	 * @return mixed
	 */
	public function error( $code, $response ) {
		return new \WP_REST_Response(
			array(
				'error'      => true,
				'success'    => false,
				'error_code' => $code,
				'response'   => $response,
			),
			401
		);
	}
}
Rest::get_instance();
