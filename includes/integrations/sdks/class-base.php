<?php
/**
 * Popper Base API Library.
 *
 * @since     1.0
 * @package   popper
 */

namespace Popper\Integrations;

/**
 * Base Class to interact with the service API
 */
class Base {

	/**
	 * The account API key.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $api_key The account API key.
	 */
	protected $api_key;

	/**
	 * The API base url.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $api_url The API url.
	 */
	protected $api_url;

	/**
	 * Brevo account data center.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $data_center Brevo account data center.
	 */
	protected $data_center;

	/**
	 * Initialize API library.
	 *
	 * @since 4.0
	 * @since 4.10 - Transitioned to oAuth2 Connections with Access Tokens and Server Prefixes.
	 *
	 * @access public
	 *
	 * @param string $api_key ConvertKit oAuth2 Access Token.
	 */
	public function __construct( $api_key ) {
		$this->api_key = $api_key;
	}

	/**
	 * Add new subscriber.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param int   $list_id List ID.
	 * @param array $data Subscriber data.
	 *
	 * @return array
	 * @throws Exception In case of error.
	 */
	public function add_subsciber( $list_id, $data ) {
		return $this->process_request( 'subscribers', $data, 'POST' );
	}

	/**
	 * Get a specific list/audience.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param string $list_id The list/audience ID.
	 *
	 * @return array
	 * @throws Exception In case of error.
	 */
	public function get_list( $list_id ) {
		return $this->process_request( 'groups/' . $list_id );
	}

	/**
	 * Get all lists.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 * @throws Exception In case of error.
	 */
	public function get_lists() {
		return $this->process_request( 'forms' );
	}

	/**
	 * Get all lists formatted to fill options for a SelectControl.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 * @throws Exception In case of error.
	 */
	public function get_lists_select() {
		$data = $this->process_request( 'forms' );
		return $data;
	}

	/**
	 * Get all merge fields for list/audience.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param int $list_id The list id.
	 *
	 * @return array
	 */
	public function get_list_merge_fields( $list_id ) {
		return $this->process_request( 'custom_fields' . $list_id );
	}

	/**
	 * Get all merge fields for list/audience formatted to fill options for a SelectControl.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param int $list_id The list id.
	 *
	 * @return array
	 */
	public function get_list_merge_fields_select( $list_id ) {
		$data = $this->process_request( 'custom_fields' . $list_id );
		return $data;
	}

	/**
	 * Process the API request.
	 *
	 * @since  1.0
	 * @access private
	 *
	 * @param string $path Request path.
	 * @param array  $data Request data.
	 * @param string $method Request method. Defaults to GET.
	 * @param string $return_key Array key from response to return. Defaults to null (return full response).
	 *
	 * @throws \Exception In case of error. If API request returns an error, exception is thrown.
	 *
	 * @return array
	 */
	protected function process_request( $path = '', $data = array(), $method = 'GET', $return_key = null ) {

		// If API key is not set, throw exception.
		if ( empty( $this->api_key ) ) {
			throw new \Exception( 'Api Key must be defined to process an API request.' );
		}

		// Build base request URL.
		$request_url = $this->api_url . $path . '?api_key=' . $this->api_key;

		// Build base request arguments.
		$args = array(
			'method'    => $method,
			'headers'   => array(
				'Accept'        => 'application/json',
				'Content-Type'  => 'application/json',
			),
		);

		if ( ! empty( $data ) ) {
			$args['body'] = wp_json_encode( $data );
		}

		$args['user-agent'] = 'Mozilla/5.0 (Windows; U; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 2.0.50727)';

		/**
		 * Filters the The request arguments.
		 *
		 * @param array $args The request arguments sent to The.
		 * @param string $path The request path.
		 *
		 * @return array
		 */
		$args = apply_filters( 'popper_pro_api_request_args', $args, $path );

		// Get request response.
		$response = wp_remote_request( $request_url, $args );

		// If request was not successful, return error.
		if ( is_wp_error( $response ) ) {
			return new \WP_Error( $response->get_error_code(), $response->get_error_message() );
		}

		// Decode response body.
		$response['body'] = json_decode( $response['body'], true );

		// Get the response code.
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( ! in_array( $response_code, array( 200, 201 ) ) ) {
			return new \WP_Error( $response_code, wp_remote_retrieve_response_message( $response ) );
		}

		// If a return key is defined and array item exists, return it.
		if ( ! empty( $return_key ) && isset( $response['body'][ $return_key ] ) ) {
			return $response['body'][ $return_key ];
		}

		return $response['body'];
	}
}
