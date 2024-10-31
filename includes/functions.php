<?php
/**
 * Custom functions.
 *
 * @package popper
 */

namespace Popper;

// don't call the file directly.
defined( 'ABSPATH' ) || exit;

/**
 * Filter to add data-popper
 *
 * @param string $block_content The block content.
 * @param array  $block The block.
 */
function popper_add_attribute( $block_content, $block ) {
	// Make sure we have the blockName.
	if ( empty( $block['blockName'] ) ) {
		return $block_content;
	}

	if (
		'core/button' === $block['blockName']
		&& ! empty( $block['attrs']['popper'] )
	) {
		$processor = new \WP_HTML_Tag_Processor( $block_content );
		$processor->next_tag( 'a' );
		$processor->set_attribute( 'data-popper', $block['attrs']['popper'] );
		return $processor->get_updated_html();
	}
	if (
		'core/image' === $block['blockName']
		&& ! empty( $block['attrs']['popper'] )
	) {
		$processor = new \WP_HTML_Tag_Processor( $block_content );
		$processor->next_tag( 'img' );
		$processor->set_attribute( 'data-popper', $block['attrs']['popper'] );
		return $processor->get_updated_html();
	}
	// Return the block content.
	return $block_content;
}

/**
 * Add Popper block category
 *
 * @param  array $block_categories The categories of Gutenberg.
 * @return array
 */
function popper_register_block_category( $block_categories ) {

	$block_categories[] = array(
		'slug'  => 'popper',
		'title' => __( 'Popper popup' ),
	);

	return $block_categories;
}

/**
 * Register a custom menu page.
 */
function menu() {
	$dashboard_hook = add_menu_page(
		__( 'Popper popups', 'popper' ),
		'Popups',
		'edit_posts',
		'popper',
		__NAMESPACE__ . '\popper_page',
		'dashicons-external',
		58
	);

	$settings_hook = add_submenu_page(
		'popper',
		__( 'Popper settings', 'popper' ),
		'Settings',
		'manage_options',
		'popper-settings',
		__NAMESPACE__ . '\popper_page',
		58
	);

	$analytics_hook = add_submenu_page(
		'popper',
		__( 'Popper analytics', 'popper' ),
		'Analytics',
		'manage_options',
		'popper-analytics',
		__NAMESPACE__ . '\popper_page',
		6
	);
}

/**
 * Popper page
 *
 * @return void
 */
function popper_page() {
	wp_enqueue_script( 'popper-dashboard' );
	wp_enqueue_style( 'popper-dashboard' );
	wp_enqueue_script( 'popper-pro-admin' );
	?>
	<div id="popper"></div>
	<?php
}

/**
 * Redirect CPT page to Popper page
 *
 * @return void
 */
function redirect_post_type() {
	$screen = get_current_screen();
	if ( 'edit-popper' === $screen->id ) {
		wp_safe_redirect( 'admin.php?page=popper' );
		exit;
	}
}

add_action( 'admin_menu', __NAMESPACE__ . '\menu' );
add_action( 'load-edit.php', __NAMESPACE__ . '\redirect_post_type' );
add_filter( 'block_categories_all', __NAMESPACE__ . '\popper_register_block_category' );
add_filter( 'render_block', __NAMESPACE__ . '\popper_add_attribute', 10, 2 );
