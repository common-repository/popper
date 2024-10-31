<?php
/**
 * Popper Mailchimp API Library.
 *
 * @since     4.0
 * @package   Popper
 */

namespace Popper\Integrations;

/**
 * Class to interact with Mailchimp API
 */
class Mailchimp extends Base {

	/**
	 * Mailchimp account data center.
	 *
	 * @since  1.0
	 * @access protected
	 * @var    string $data_center Mailchimp account data center.
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
	 * @param string $access_token Mailchimp oAuth2 Access Token.
	 * @param string $server_prefix Mailchimp oAuth2 Server Prefix (Used as data center).
	 */
	public function __construct( $access_token, $server_prefix = '' ) {
		$this->api_key = $access_token;

		if ( ! empty( $server_prefix ) ) {
			$this->data_center = $server_prefix;
		}
	}

	/**
	 * Get current account details.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @return array
	 * @throws Exception In case of error.
	 */
	public function account_details() {
		return $this->process_request();
	}

	public function get_list( $list_id ) {
		return $this->process_request( 'lists/' . $list_id );
	}

	public function get_lists() {
		return $this->process_request( 'lists' );
	}

	public function get_lists_select() {
		$data = $this->get_lists();
		return $data['lists'];
	}

	public function get_list_merge_fields( $list_id ) {
		return $this->process_request( 'lists/' . $list_id . '/merge-fields', array( 'count' => 9999 ) );
	}

	public function get_list_merge_fields_select( $list_id ) {
		$data = $this->get_list_merge_fields( $list_id );
		$result = array();

		foreach ( $data['merge_fields'] as $field ) {
			$result[] = array(
				'id'    => $field['merge_id'],
				'label' => $field['name'],
				'name'  => $field['tag'],
			);
		}
		return $result;
	}

	public function add_subscriber( $list_id, $data ) {
		return $this->process_request( 'lists/' . $list_id . '/members/', $data, 'POST' );
	}

	protected function process_request( $path = '', $data = array(), $method = 'GET', $return_key = null ) {

		// If API key is not set, throw exception.
		if ( empty( $this->api_key ) ) {
			throw new \Exception( 'Api Key must be defined to process an API request.' );
		}

		// Build base request URL.
		$request_url = 'https://' . $this->get_data_center() . '.api.mailchimp.com/3.0/' . $path;

		$auth = 'Bearer ' . $this->api_key;

		// Deprecated API Key method detected - use that for auth to prevent breakage.
		if ( $this->get_data_center_from_api_key() ) {
			// phpcs:ignore
			$auth = 'Basic ' . base64_encode( ':' . $this->api_key );
		}

		// Build base request arguments.
		$args = array(
			'method'  => $method,
			'headers' => array(
				'Accept'        => 'application/json',
				'Authorization' => $auth,
				'Content-Type'  => 'application/json',
			),
			'data'    => $data,
		);

		/**
		 * Filters the Mailchimp request arguments.
		 *
		 * @param array $args The request arguments sent to Mailchimp.
		 * @param string $path The request path.
		 *
		 * @return array
		 */
		$args = apply_filters( 'popper_pro_mailchimp_request_args', $args, $path );

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

		if ( ! in_array( $response_code, array( 200, 204 ) ) ) {
			return new \WP_Error( $response_code, wp_remote_retrieve_response_message( $response ) );
		}

		// If a return key is defined and array item exists, return it.
		if ( ! empty( $return_key ) && isset( $response['body'][ $return_key ] ) ) {
			return $response['body'][ $return_key ];
		}

		return $response['body'];
	}

	/**
	 * Set data center based on API key.
	 *
	 * @since  1.0
	 * @access private
	 */
	private function get_data_center() {

		// If API key is empty, return.
		if ( empty( $this->api_key ) ) {
			return;
		}

		if ( ! empty( $this->data_center ) ) {
			return $this->data_center;
		}

		$data_center = $this->get_data_center_from_api_key();

		return $data_center ? $data_center : 'us1';
	}

	/**
	 * Get data center from api
	 *
	 * @return string The datacenter.
	 */
	private function get_data_center_from_api_key() {
		// Explode API key.
		$exploded_key = explode( '-', $this->api_key );

		// Set data center from API key.
		return isset( $exploded_key[1] ) ? $exploded_key[1] : false;
	}

	/**
	 * Get disconnect link.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function get_disconnect_url() {
		return sprintf( 'https://%s.admin.mailchimp.com/account/api/', $this->data_center );
	}
}
