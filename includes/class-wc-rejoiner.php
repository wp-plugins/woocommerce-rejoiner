<?php
/**
 * Rejoiner Integration
 *
 * Allows Rejoiner tracking code to be inserted into store pages.
 *
 * @class 		WC_Rejoiner
 * @extends		WC_Integration
 */

class WC_Rejoiner extends WC_Integration {

	public function __construct() {
	
		session_start();
		$this->sess = session_id();
		
		$this->id = 'wc_rejoiner';
		$this->method_title = __( 'Rejoiner', 'woocommerce' );
		$this->method_description = __( 'Rejoiner integration', 'woocommerce' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables
		$this->rejoiner_id = $this->get_option( 'rejoiner_id' );
		$this->rejoiner_domain_name = $this->get_option( 'rejoiner_domain_name' );
		
		// Actions
		add_action( 'woocommerce_update_options_integration_wc_rejoiner', array( $this, 'process_admin_options') );
		add_action( 'init', array( $this, 'refill_cart' ) );
		
		// Tracking code
		add_action( 'wp_footer', array( $this, 'rejoiner_tracking_code' ) );
		add_action( 'woocommerce_thankyou', array( $this, 'rejoiner_conversion_code' ) );

	}

	function init_form_fields() {

		$this->form_fields = array(
			'rejoiner_id' => array(
				'title' 			=> __( 'Rejoiner Account', 'woocommerce' ),
				'description' 		=> __( 'You can find your unique Site ID on the Implementation page inside of your Rejoiner dashboard.', 'woocommerce' ),
				'type' 				=> 'text',
		    	'default' 			=> ''
			),
			'rejoiner_domain_name' => array(
				'title' 			=> __( 'Set Domain Name', 'woocommerce' ),
				'description' 		=> __( 'Enter your domain for the tracking code. Example: .domain.com or .www.domain.com', 'woocommerce' ),
				'type' 				=> 'text',
		    	'default' 			=> ''
			)
		);

    } 
    
	function rejoiner_tracking_code() {

		global $woocommerce;
		
		$subtotal = 0;
		$items = array();
		$savecart = array();
			
		foreach( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
			
				$thumb_id = get_post_thumbnail_id( $_product->post->ID );

				$thumb_url = wp_get_attachment_image_src( $thumb_id, 'shop_thumbnail', true) ;

				if( !empty($thumb_url[0]) ) {
				
					$image = $thumb_url[0];
					
				} else {
				
					$image = wc_placeholder_img( 'shop_thumbnail' );
					
				}

				$subtotal = $subtotal+$itemtotal;
				
				if( $_product->variation_id > 0 ) {		
					
					$variantname = '';
					
					foreach ( $cart_item['variation'] as $name => $value ) {
	  
	                      if ( '' === $value )
	                          continue;
	  
	                      $taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );
	  
	                      if ( taxonomy_exists( $taxonomy ) ) {
	                          $term = get_term_by( 'slug', $value, $taxonomy );
	                          if ( ! is_wp_error( $term ) && $term && $term->name ) {
	                              $value = $term->name;
	                          }
	                          $label = wc_attribute_label( $taxonomy );
	 
	                      } else {
	                         $value              = apply_filters( 'woocommerce_variation_option_name', $value );
	                         $product_attributes = $cart_item['data']->get_attributes();
	                         if ( isset( $product_attributes[ str_replace( 'attribute_', '', $name ) ] ) ) {
	                             $label = wc_attribute_label( $product_attributes[ str_replace( 'attribute_', '', $name ) ]['name'] );
	                         } else {
	                             $label = $name;
	                         }
	                     }
						 
						 $variantname.= ', ' . $label . ': ' . $value;
	                     $item_data[$name] = $value;
	                     	                     
	                }
	                
	                $items[] = array(
						'product_id' => $_product->post->ID,
						'name' => apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . $variantname,
						'item_qty' => $cart_item['quantity'],
						'price' => $_product->get_price(),
						'qty_price' => $cart_item['line_total'],
						'image_url' => $this->format_image_url( $image ),
						'description' => $this->format_description( $_product->post->post_excerpt )
					);
	                
   					$savecart[] = array(
						'product_id' => $_product->post->ID,
						'item_qty' => $cart_item['quantity'],
						'variation_data' => $item_data,
						'variation_id' => $_product->variation_id
					);

				} else {
					
					$items[] = array(
						'product_id' => $_product->post->ID,
						'name' => apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ),
						'item_qty' => $cart_item['quantity'],
						'price' => $_product->get_price(),
						'qty_price' => $cart_item['line_total'],
						'image_url' => $this->format_image_url( $image ),
						'description' => $this->format_description( $_product->post->post_excerpt )
					);
					
					$savecart[] = array(
						'product_id' => $_product->post->ID,
						'item_qty' => $cart_item['quantity']
					);
					
				}
					
			}
			
		}
		
		set_transient( 'rjcart_' . $this->sess, $savecart, 168 * HOUR_IN_SECONDS);
		
		$cartdata = array(
			'value' =>  $woocommerce->cart->total,
			'totalItems' => $woocommerce->cart->cart_contents_count,
		);
		
		$js = $this->build_rejoiner_push( $items, $cartdata );
		
		echo $js;
		
	}

	function format_description( $text ) {
		
		$text = str_replace( "'", "\'", strip_tags( $text ) );
		$text = str_replace( array("\r", "\n"), "", $text );
		
		return $text;
		
	}

	function format_image_url( $url ) {
		
		if( stripos( $url, 'http' ) === false ) {
			
			$url = get_site_url() . $url;
			
		}
		
		return $url;
		
	}
	
	function build_rejoiner_push( $items, $cart ) {
	
		global $woocommerce;
		
		$rejoiner_id = $this->rejoiner_id;
		$rejoiner_domain_name = $this->rejoiner_domain_name;		
		
		$returnUrl = $woocommerce->cart->get_cart_url() . '?rjcart=' . $this->sess;
		
		$cart['returnUrl'] = apply_filters( 'rejoiner_returnurl', $returnUrl, $this_sess, $cart );
		
		$cartdata = $this->rejoiner_encode( $cart );
		$cartjs = "_rejoiner.push(['setCartData', $cartdata]);";
		
		foreach( $items as $item ) {
			
			$data = $this->rejoiner_encode( $item );
			$itemjs.= "_rejoiner.push(['setCartItem', $data]);\r\n";
			
		}
		
		if( !empty( $rejoiner_id ) && !empty( $rejoiner_domain_name ) ) {
				
			$js = <<<EOF
<!-- Rejoiner Tracking - added by WooCommerceRejoiner -->

<script type='text/javascript'>
var _rejoiner = _rejoiner || [];
_rejoiner.push(['setAccount', '{$rejoiner_id}']);
_rejoiner.push(['setDomain', '{$rejoiner_domain_name}']);

(function() {
    var s = document.createElement('script'); s.type = 'text/javascript';
    s.async = true;
    s.src = 'https://s3.amazonaws.com/rejoiner/js/v3/t.js';
    var x = document.getElementsByTagName('script')[0];
    x.parentNode.insertBefore(s, x);
})();
</script>

<script type='text/javascript'>
    $cartjs
    $itemjs
</script>

<!-- End Rejoiner Tracking -->
EOF;

		} else {
			
			$js = "\r\n<!-- WooCommerce Rejoiner ERROR: You must enter your details on the integrations settings tab. -->\r\n";	
			
		}
		
		return $js;           
		
	}
	
	function rejoiner_encode( $array ) {
		
		$json = '{';
		
		foreach( $array as $key => $val ) {
			
			$items[]= "'$key' : '$val'";
			
		}
		
		$json.= implode( ', ', $items ) . '}';
		
		return $json;		
		
	}

	function rejoiner_conversion_code( $order_id ) {
		
		$rejoiner_id = $this->rejoiner_id;
		$rejoiner_domain_name = $this->rejoiner_domain_name;

		if ( !$rejoiner_id ) {
			return;
		}
		
		if( !isset( $order_id ) )
			$order_id = $order->get_order_number();
		
		$js = <<<EOF
<!-- Rejoiner Conversion - added by WooCommerce Rejoiner -->

<script type='text/javascript'>
var _rejoiner = _rejoiner || [];
_rejoiner.push(['setAccount', '{$rejoiner_id}']);
_rejoiner.push(['setDomain', '{$rejoiner_domain_name}']);
_rejoiner.push(['sendConversion']);

(function() {
    var s = document.createElement('script');
    s.type = 'text/javascript';
    s.async = true;
    s.src = 'https://s3.amazonaws.com/rejoiner/js/v3/t.js';
    var x = document.getElementsByTagName('script')[0];
    x.parentNode.insertBefore(s, x);
})();
</script>

<script type='text/javascript'>
_rejoiner.push(['setCartData', {'customer_order_number': '$order_id'}]);
</script>

<!-- End Rejoiner Conversion -->                         		
EOF;
		
		echo $js;
		
	}

	function refill_cart() {
		
		if ( isset( $_GET['rjcart'] ) ) {
			
			global $woocommerce;
						
			$this_sess = $_GET['rjcart'];	
			
			$carturl = $woocommerce->cart->get_cart_url();
					  
			$rjcart = get_transient( 'rjcart_' . $this_sess );
									
			if( !empty( $rjcart ) ) {
					
				$woocommerce->cart->empty_cart();
				
				foreach( $rjcart as $product ) {
								
					if( $product['variation_id'] > 0 ) {
						
						$woocommerce->cart->add_to_cart( 
							$product['product_id'], 
							$product['item_qty'], 
							$product['variation_id'], 
							$product['variation_data']
						);
							
					} else {
						
						$woocommerce->cart->add_to_cart(
							$product['product_id'], 
							$product['item_qty']
						);				
					
					}
			
				}

				header( "location:$carturl?utm_source=rejoiner&utm_medium=email&utm_campaign=email" );	
				exit;	

			}	
			
		}
	
	}

}