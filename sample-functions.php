<?php
	
// Use this filter to modify the item name that is passed to Rejoiner

add_filter( 'wc_rejoiner_cart_item_name', 'rw_wcrj_item_name' );

function rw_wcrj_item_name( $name ) {
	
	return str_ireplace( array( '<br>','<br/>' ), '', $name );
	
}

// Use this filter to modify the description of the item variation which is appended to the the item title.

add_filter( 'wc_rejoiner_cart_item_variant', 'rw_wcrj_item_variant' );

function rw_wcrj_item_variant( $variantname ) {

	return null;

}

// Use this filter to specify the image size to be passed to rejoiner. 
// You can return a named image size, eg: 'medium' or an array of dimensions, eg: array( 800, 600 )

add_filter( 'wc_rejoiner_thumb_size', 'rw_wcrj_thumb_size' );

function rw_wcrj_thumb_size( $size ) {
	
	return array( 800, 600 );

}