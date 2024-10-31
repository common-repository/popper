<?php
/**
 * Plugin Name: Popper
 * Plugin URI:  https://www.francescopepe.com/
 * Description: Popup builder with exit-intent powered by Gutenberg.
 * Version:     0.7.9
 * Author:      Tropicalista
 * Author URI:  https://www.francescopepe.com
 * License:     GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: popper
 *
 * @package     popper
 */

defined( 'ABSPATH' ) || exit;

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/includes/class-rest.php';
require_once __DIR__ . '/includes/class-frontend.php';
require_once __DIR__ . '/includes/class-submission.php';

load_plugin_textdomain( 'popper', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

/**
 * Init hook
 */
function popper_block_init() {
	register_block_type_from_metadata( __DIR__ . '/build/blocks/popper' );
	register_block_type_from_metadata( __DIR__ . '/build/blocks/popper-body' );
	register_block_type_from_metadata( __DIR__ . '/build/blocks/button' );
	register_block_type_from_metadata( __DIR__ . '/build/blocks/form' );
	register_block_type_from_metadata( __DIR__ . '/build/blocks/form-input' );
	register_block_type_from_metadata( __DIR__ . '/build/blocks/form-select' );
	register_block_type_from_metadata( __DIR__ . '/build/blocks/form-multichoices' );
	register_block_type_from_metadata( __DIR__ . '/build/blocks/form-button' );

	register_block_pattern_category(
		'popup',
		array( 'label' => __( 'Popups', 'popper' ) )
	);

	$dir                  = plugin_dir_path( __FILE__ );
	$dashboard_asset_path = "$dir/build/admin.asset.php";

	$dashboard_asset = require $dashboard_asset_path;

	wp_register_script(
		'popper-dashboard',
		plugin_dir_url( __FILE__ ) . '/build/admin.js',
		$dashboard_asset['dependencies'],
		$dashboard_asset['version'],
		true
	);

	wp_register_style(
		'popper-dashboard',
		plugin_dir_url( __FILE__ ) . '/build/style-admin.css',
		array( 'wp-components', 'wp-reset-editor-styles' ),
		$dashboard_asset['version'],
	);

	if ( ! wp_script_is( 'react-jsx-runtime', 'registered' ) ) {
		wp_register_script(
			'react-jsx-runtime',
			plugin_dir_url( __FILE__ ) . 'assets/js/react-jsx-runtime.js',
			array( 'react' ),
			'18.3.1',
			true
		);
	}

	global $wp_filesystem;
	// Initialize the WP filesystem, no more using 'file-put-contents' function.
	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem( true );
	}

	$local_file = __DIR__ . '/assets/patterns.json';

	if ( $wp_filesystem->exists( $local_file ) ) {
		$patterns = json_decode( $wp_filesystem->get_contents( $local_file ), true );
	}

	if ( ! empty( $patterns ) ) {
		foreach ( $patterns as $pattern ) {
			if ( empty( $pattern['name'] ) ) {
				continue;
			}
			register_block_pattern(
				$pattern['name'],
				$pattern
			);
		}
	}
}
add_action( 'init', 'popper_block_init' );

/**
 * Register CPT.
 */
function popper_register() {
	$args = array(
		'labels'              => array(
			'name'               => _x( 'Popups', 'Post Type General Name', 'popper' ),
			'singular_name'      => _x( 'Popup', 'Post Type Singular Name', 'popper' ),
			'menu_name'          => __( 'Popups', 'popper' ),
			'parent_item_colon'  => __( 'Parent Popup', 'popper' ),
			'all_items'          => __( 'Popups', 'popper' ),
			'view_item'          => __( 'View Popup', 'popper' ),
			'add_new_item'       => __( 'Add New Popup', 'popper' ),
			'add_new'            => __( 'Add New', 'popper' ),
			'edit_item'          => __( 'Edit Popup', 'popper' ),
			'update_item'        => __( 'Update Popup', 'popper' ),
			'search_items'       => __( 'Search Popup', 'popper' ),
			'not_found'          => __( 'Not Found' ),
			'not_found_in_trash' => __( 'Not found in Trash' ),
		),
		'public'              => false,
		'publicly_queryable'  => false,
		'has_archive'         => false,
		'show_ui'             => true,
		'menu_icon'           => 'dashicons-external',
		'exclude_from_search' => true,
		'show_in_nav_menus'   => true,
		'rewrite'             => false,
		'hierarchical'        => false,
		'show_in_menu'        => false,
		'show_in_admin_bar'   => true,
		'show_in_rest'        => true,
		'capability_type'     => 'post',
		'template'            => array(
			array(
				'popper/popup',
				array(
					'lock' => array(
						'move'   => false,
						'remove' => true,
					),
				),
			),
		),
		// removed until this is solved: https://github.com/WordPress/gutenberg/issues/49005.
		// 'template_lock'       => 'insert',
		'supports'            => array(
			'title',
			'editor',
			'custom-fields',
			'revisions',
			'author',
			'excerpt',
		),
	);
	register_post_type( 'popper', $args );

	$defaults = array(
		'running'  => true,
		'location' => array(),
		'exclude'  => array(),
		'user'     => array(),
		'date'     => array(
			array(
				'type'       => 'evergreen',
				'startDate'  => '',
				'endDate'    => '',
				'customTime' => false,
				'customDays' => array(),
			),
		),
		'device'   => array(
			'desktop',
			'tablet',
			'mobile',
		),
	);

	register_meta(
		'post',
		'popper_rules',
		array(
			'single'         => true,
			'type'           => 'object',
			'default'        => $defaults,
			'show_in_rest'   => array(
				'schema' => array(
					'type'                 => 'object',
					'additionalProperties' => true,
					'properties'           => array(
						'running'  => array(
							'type' => 'boolean',
						),
						'location' => array(
							'type' => 'array',
						),
						'exclude'  => array(
							'type' => 'array',
						),
						'user'     => array(
							'type' => 'array',
						),
						'date'     => array(
							'type'                 => 'object',
							'additionalProperties' => true,
							'properties'           => array(
								'type'       => array(
									'type' => 'string',
								),
								'startDate'  => array(
									'type' => 'string',
								),
								'endDate'    => array(
									'type' => 'string',
								),
								'customTime' => array(
									'type' => 'boolean',
								),
								'customDays' => array(
									'type' => 'array',
								),
							),
						),
						'device'   => array(
							'type' => 'array',
						),
					),
				),
			),
			'object_subtype' => 'popper',
		)
	);

	register_post_meta(
		'popper',
		'_popper_actions',
		array(
			'show_in_rest'         => array(
				'schema' => array(
					'items' => array(
						'type'                 => 'object',
						'properties'           => array(),
						'additionalProperties' => true,
					),
				),
			),
			'default'              => array(),
			'single'               => true,
			'type'                 => 'array',
			'additionalProperties' => true,
			'auth_callback'        => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_post_meta(
		'popper',
		'_popper_fields',
		array(
			'show_in_rest'  => array(
				'schema' => array(
					'type'                 => 'object',
					'properties'           => array(),
					'additionalProperties' => true,
				),
			),
			'default'       => array(),
			'single'        => true,
			'type'          => 'object',
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			},
		)
	);

	register_rest_field(
		'popper',
		'popper_locations',
		array(
			'get_callback' => function ( $obj ) {
				$result = array(
					'include' => array(),
					'exclude' => array(),
					'user'    => array(),
					'dates'   => array(),
				);

				foreach ( $obj['meta']['popper_rules']['location'] as $value ) {
					if ( ! empty( $value['rule'] ) ) {
						array_push( $result['include'], \Popper\Conditions::get_saved_label( $value ) );
					}
				}
				foreach ( $obj['meta']['popper_rules']['exclude'] as $value ) {
					array_push( $result['exclude'], \Popper\Conditions::get_saved_label( $value ) );
				}
				$result['date'] = $obj['meta']['popper_rules']['date'];
				$result['user'] = \Popper\Conditions::get_user_label( $obj['meta']['popper_rules']['user'] ) ?? array();
				return $result;
			},
		),
	);
}

add_action( 'init', 'popper_register' );

/**
 * Print default positions options.
 */
function popper_positions() {
	$positions = \Popper\Conditions::get_conditions();
	$users     = \Popper\Conditions::get_user_conditions();

	$screen = get_current_screen();
	if ( 'edit-popper' === $screen->id || 'toplevel_page_popper' === $screen->id ) {
		wp_enqueue_script( 'popper-dashboard' );
	}

	wp_localize_script(
		'wp-block-directory',
		'popper',
		array(
			'positions' => $positions,
			'users'     => $users,
			'pro'       => (bool) is_plugin_active( 'popper-pro/popper-pro.php' ),
		)
	);

	wp_localize_script(
		'popper-dashboard',
		'popper',
		array(
			'positions' => $positions,
			'users'     => $users,
		)
	);
}
add_action( 'admin_enqueue_scripts', 'popper_positions', 10, 2 );

/**
 * Fires after tracking permission allowed (optin)
 *
 * @param array $data The Appsero data.
 *
 * @return void
 */
function popper_tracker_optin( $data ) {
	$data['project'] = 'popper';
	$response        = wp_remote_post(
		'https://hook.eu1.make.com/dplrdfggemll51whv3b21yjabuk8po0b',
		array(
			'headers'     => array( 'Content-Type' => 'application/json; charset=utf-8' ),
			'body'        => wp_json_encode( $data ),
			'method'      => 'POST',
			'data_format' => 'body',
		)
	);
}
add_action( 'popper_tracker_optin', 'popper_tracker_optin', 10 );

/**
 * Initialize the plugin tracker
 *
 * @return void
 */
function popper_appsero_init_tracker() {

	if ( ! class_exists( 'Appsero\Client' ) ) {
		require_once __DIR__ . '/appsero/src/Client.php';
	}

	$client = new Appsero\Client( '3cf9fb47-a835-47f8-be36-df7bdfceda26', 'Popup with exit intent, scroll triggered and anchor click for opt-ins, lead gen &amp; more', __FILE__ );

	// Active insights.
	$client->insights()
		->add_plugin_data()
		->init();
}

popper_appsero_init_tracker();

/**
 * Update popper namespace
 *
 * @return void
 */
function popper_update_namespace() {
	$posts = get_posts(
		array(
			'post_type'   => 'popper',
			'numberposts' => -1,
		)
	);

	foreach ( $posts as $post ) {
		$post->post_content = str_replace( 'wp:formello/popper', 'wp:popper/popup', $post->post_content );
		$post->post_content = str_replace( 'wp-block-formello-popper', 'wp-block-popper-popup', $post->post_content );

		global $wpdb;

		$wpdb->update(
			$wpdb->prefix . 'posts',
			array(
				'post_content' => $post->post_content,
			),
			array( 'id' => intval( $post->ID ) )
		);

	}
}

register_activation_hook( __FILE__, 'popper_update_namespace' );
