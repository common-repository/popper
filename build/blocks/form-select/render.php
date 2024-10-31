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

$p = new WP_HTML_Tag_Processor( $content );

if ( $p->next_tag( array( 'tag_name' => 'select' ) ) ) {

	if ( $p->get_attribute( 'multiple' ) ) {
		$p->set_attribute( 'name', $p->get_attribute( 'name' ) . '[]' );
	}
}

echo $p->get_updated_html();
