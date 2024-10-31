<?php
/**
 * PHP file to use when rendering the block type on the server to show on the front end.
 *
 * The following variables are exposed to the file:
 *     $attributes (array): The block attributes.
 *     $content (string): The block default content.
 *     $block (WP_Block): The block instance.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

// Generate unique id for aria-controls.
$unique_id = wp_unique_id( 'p-' );

if ( ! empty( $attributes['overlayColor'] ) ) {

	$popper_styles = array(
		array(
			'selector'     => 'dialog[data-id="' . $attributes['uuid'] . '"]::backdrop',
			'declarations' => array( 'background-color' => $attributes['overlayColor'] ),
		),
	);

	$popper_stylesheet = wp_style_engine_get_stylesheet_from_css_rules(
		$popper_styles,
	);

	echo '<style>' . $popper_stylesheet . '</style>';
}

$rules = get_post_meta( $attributes['uuid'], 'popper_rules', true );

$p = new WP_HTML_Tag_Processor( $content );

if ( $p->next_tag( 'dialog' ) ) {
	$p->set_attribute( 'data-devices', wp_json_encode( $rules['device'] ) );
}

echo $p->get_updated_html();
