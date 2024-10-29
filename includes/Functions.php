<?php
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

	/**
	 * save preview image
	 */
	function asowp_save_canvas_image( $images) {
		$upload_dirs = ASOWP_IMAGE_PATH;
		wp_mkdir_p( $upload_dirs );
		$upload_dir = $upload_dirs . DIRECTORY_SEPARATOR;
        $name                     = uniqid( 'asowp-' );
        $preview_img=[];
        if(!isset($images["face1"])){
            foreach ($images as $key => $image) {
                $file        = base64_decode(explode(',', $image["url"])[1]);
                $file_name       = $upload_dir . $name . ".".$image['format'];
                file_put_contents( $file_name, $file ); // phpcs:ignore
                $preview_img[] = ASOWP_IMAGE_URL . '/'.$name . '.'. $image['format'];
            }
            
        }else{
            
            foreach ($images as $key =>$face) {
                foreach ($face as $image) {
                    $file        = base64_decode(explode(',', $image["url"])[1]);
                    $file_name       = $upload_dir. $name. $key. ".". $image['format'];
                    file_put_contents( $file_name, $file ); // phpcs:ignore
                    $preview_img[] = ASOWP_IMAGE_URL . '/'.$name. $key. ".". $image['format'];
                }
            }
        }
        return $preview_img;
	}
	
	/**
	 * add or edit product to cart
	 */
	function asowp_add_custom_design_to_cart_ajax() {
        if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field( wp_unslash ($_POST['nonce'])), 'asowp_add_to_cart_after_custom')) {

            if (isset($_POST['data']['variation_id'])) {
                $redirectToCheckOut = isset($_POST['redirectToCheckOut']) ? sanitize_text_field(wp_unslash($_POST['redirectToCheckOut'])) : false;
                $main_variation_id = intval($_POST['data']['variation_id']);
                $quantity = isset($_POST['data']['quantity']) ? intval($_POST['data']['quantity']) : 1; 
                $cart_item_key = isset($_POST['data']['cart_item_key']) ? sanitize_key($_POST['data']['cart_item_key']): false;
                $recaps = isset($_POST['data']['recaps']) ? map_deep( wp_unslash($_POST['data']['recaps']), 'sanitize_text_field' ) : [];
                $message = '';
                
                
                
                $newly_added_cart_item_key = false;
                $asowp_previews = asowp_save_canvas_image( $recaps["designImages"]);
                //$preview_img = ASOWP_IMAGE_URL . '/' . $file_name . '.png';
                if ( $cart_item_key ) {
                    WC()->cart->cart_contents[ $cart_item_key ]['asowp_recaps'] = $recaps;
                    WC()->cart->calculate_totals();
                    wp_send_json(array(
                        'success'     => true
                    ));
                } else {
                    $newly_added_cart_item_key = asowp_add_designs_to_cart($main_variation_id, $recaps,$asowp_previews,$quantity);

                    if ( $newly_added_cart_item_key ) {
                        $message =  __( 'Product successfully added to cart.', 'all-signs-options-free' );
                        if($redirectToCheckOut === "true" || $redirectToCheckOut === true){
                            $url = wc_get_checkout_url();
                        }else{
                            $url = wc_get_cart_url();
                        }
                        wp_send_json(array(
                            'success'     => true,
                            'cart_item_key'     => $newly_added_cart_item_key,
                            'message'     => $message,
                            'url'         => $url,
                            'form_fields' => $recaps,
            
                        ));
                    } else {
                        $message = __( 'A problem occured while adding the product to the cart. Please try again.', 'all-signs-options-free' );
                        wp_send_json(array(
                            'success'     => false,
                            'message'     => $message,
            
                        ));
                    }
                
                }
            } else {
                wp_send_json(array('message' => __("Missing product ID",'all-signs-options-free')));
            }
        }else{
            wp_send_json(array('message' => 'nonce invalid.'));
        }
	}

	/**
	 *  add product to cart
	 */
	function asowp_add_designs_to_cart( int $product_id,array $recaps,$images,int $quantity=1) {
		$newly_added_cart_item_key = false;
        $product = wc_get_product( $product_id );
        $parent_id = $product->get_parent_id();
        $recaps["designImages"]= $images;
        if($parent_id == 0){
            $newly_added_cart_item_key = WC()->cart->add_to_cart(
                $product_id,
                $quantity,
                0,
                array(),
                array(
                    'asowp_meta_data' => [
                        "recaps"=>$recaps
                    ]
                )
            );
        }else{
            $variation  = $product->get_variation_attributes();
            $newly_added_cart_item_key = WC()->cart->add_to_cart(
                $product_id,
                $quantity,
                0,
                $variation,
                array(
                    'asowp_meta_data' => [
                        "recaps"=>$recaps
                    ]
                )
            );
        }

			/* if ( isset( $_SESSION['npd_key'] ) ) {
				$variations = get_transient( $_SESSION['npd_key'] );
			}

			foreach ( $variation as $key => $value ) {
				if ( isset( $variations[ $key ] ) && '' === $value ) {
					$variation[ $key ] = $variations[ $key ];
				}
			}

			if ( isset( $_SESSION['combinaison'][ $variation_name ] ) ) {
				$variation = $_SESSION['combinaison'][ $variation_name ];
			} */

			if ( method_exists( WC()->cart, 'maybe_set_cart_cookies' ) ) {
				WC()->cart->maybe_set_cart_cookies();
			}
		return $newly_added_cart_item_key;
	}

    /**
     *  includes ajax in plugin 
     */
    add_action( 'wp_ajax_asowp_add_custom_design_to_cart', 'asowp_add_custom_design_to_cart_ajax' );
    add_action( 'wp_ajax_nopriv_asowp_add_custom_design_to_cart', 'asowp_add_custom_design_to_cart_ajax' );
    function asowp_get_price_format() {
        $currency_pos = get_option( 'woocommerce_currency_pos' );
        $format       = '%s%v';

        switch ( $currency_pos ) {
            case 'left':
                $format = '%s%v';
                break;
            case 'right':
                $format = '%v%s';
                break;
            case 'left_space':
                $format = '%s %v';
                break;
            case 'right_space':
                $format = '%v %s';
                break;
            default:
                $format = '%s%v';
                break;
        }
        return $format;
    } 
    function asowp_get_custom_products() {
        $args = [
            'post_type'      => 'product', 
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => [
                [
                    'key'     => 'product-asowp-metas',
                    'value'   => 'config-id";s:1:"0"',
                    'compare' => 'NOT LIKE',
                ],
            ],
        ];

        $product_ids = get_posts($args);
        
        if (is_array($product_ids) && count($product_ids)>0) {
           return $product_ids;
        } else {
            return [];
        }
        
    }