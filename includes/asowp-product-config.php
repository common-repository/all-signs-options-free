<?php

 /**
 * the class summarizing the actions of aso on a product
 */
 class ASOWP_Product_Config {

    //ID of a product variant or not
    public $variation_id;

    //product root  ID
    public $root_product_id;

    //the product itself
    public $product;

    //product related settings
    public $settings;

    //Class contructor
    public function __construct( $id=0 ) {
		if ( $id!=0 ) {
			$this->root_product_id = $this->get_parent( $id );
			// If it's a variable product
			if ( $id !== $this->root_product_id ) {
				$this->variation_id = $id;
			}// Simple product and others
			else {
				$this->variation_id = $this->root_product_id;
			}
			$this->product = wc_get_product( $id );

			$config        = get_post_meta( $this->variation_id, 'product-asowp-metas', true );

			if ( isset( $config[ $this->variation_id ] ) ) {
				$config_id = $config[ $this->variation_id ];
				if ( isset($config_id['config-id']) ) {
					$this->settings = get_post_meta( $config_id['config-id'], 'asowp-configs-meta', true );
				}
			}
		}
    }
	/**
	 * All hooks related to attaching a product config
	 */
	public function init_hooks(){
		add_action( 'woocommerce_product_options_general_product_data', array($this,'get_product_config_selector'));
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'get_variation_product_config_selector' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation', array($this, 'save_config') );
		add_action( 'save_post_product', array($this, 'save_config' ));
		add_filter( 'manage_edit-product_columns', array($this, 'get_product_columns'),10 );
		add_action( 'manage_product_posts_custom_column', array($this, 'get_products_columns_values'), 10, 2 );
		add_action( 'woocommerce_after_add_to_cart_button', array($this, 'hide_cart_button' ));
		add_action( 'woocommerce_after_add_to_cart_button', array($this, 'get_customize_btn' ));
		add_filter( 'woocommerce_loop_add_to_cart_link', array($this, 'hide_or_display_custom_button_or_add_to_cart_button'), 10, 2 );
		add_action( 'woocommerce_single_product_summary', array($this,'get_button_on_single_product_summary'), 5 );
		add_action('woocommerce_cart_item_removed', [$this,'delete_product_file_when_delete_product'], 10, 2);
		
	}
	
    /**
     * @param int $id lidentifier of the product
     * @return int the identifier of the parent of the product 
     * if it is a variable product otherwise returns the same identifier
     */
    public function get_product_parent_ID(int $id){
        $variable_product = wc_get_product( $id );
		if ( ! $variable_product ) {
			return false;
		}
		if ( $variable_product->is_type('variable') ) {
			$product_id = $id;
		} else {
			$product_id = $variable_product->get_parent_id();
		}

		return $product_id;
    }

	/**
	 * Configuration Selector from product admin page
	 */
	public function get_product_config_selector() {
		$id = get_the_ID();

		$args        = array(
			'post_type' => 'asowp-configs',
			'nopaging'  => true,
		);
		$configs     = get_posts( $args );
		
		$configs_ids = array( ['' => 'None'] );

		if(count($configs)>0){
			for ($i=0; $i < 1; $i++) { 
				$configs_ids[ $configs[$i]->ID ] = ['title'=>$configs[$i]->post_title];
			}
		}
		?>
		<div class="asowp_config_data show_if_simple">
			<?php
			$this->display_asowp_config_on_WC_product_config( $id, $configs_ids, __('Attach this product to All Signs Options configuration','all-signs-options-free') );
			?>
		</div>
		<?php
	}

	/**
	* Assign configuration to product
	*/
	private function display_asowp_config_on_WC_product_config( $pid, $configs_ids, $title ) {
		$meta = get_post_meta($pid,'product-asowp-metas',true);
		$container = "<div class='bg-black p-4'>" .
			"<h2>" . esc_html($title) . "</h2>" .
			"<div>" .
			"<select name='product-asowp-metas[" . esc_attr($pid) . "][config-id]'>";
	
		foreach ($configs_ids as $id => $values) {
			foreach ($values as $value) {
				$container .= "<option value='" . esc_attr($id) . "' ";
				if (isset($meta[$pid]["config-id"]) && $meta[$pid]["config-id"] == $id) {
					$container .= ' selected';
				}
				$container .= ">" . esc_html($value) . "</option>";
			}
		}
	
		$container .= "</select>";
		$container .= "<input type='hidden' name='asowp_config_nonce' value='" . wp_create_nonce('asowp_config_nonce') . "'/>";
		$container .= "</div></div>";
	
		// Définition des balises HTML autorisées et de leurs attributs
		$allowed_html = array(
			'div' => array(
				'class' => array(),
			),
			'h2' => array(),
			'select' => array(
				'name' => array(),
			),
			'option' => array(
				'value' => array(),
				'selected' => array(),
			),
			'input' => array(
				'type' => array(),
				'name' => array(),
				'value' => array(),
			),
		);
	
		// Affichage sécurisé de $container
		echo wp_kses($container, $allowed_html);
	}

	/**
	 * Assign configuration to product variation
	 */
	public function get_variation_product_config_selector( $loop, $variation_data, $variation ) {
		$id = $variation->ID;

		// Ici, vous pouvez ajouter un code spécifique si nécessaire pour chaque variation
		// par exemple, vérifier les propriétés ou les attributs de la variation, si vous avez besoin de personnaliser davantage.

		
		$args        = array(
			'post_type' => 'asowp-configs',
			'nopaging'  => true,
		);
		$configs     = get_posts( $args );
		$configs_ids = array( ['' => 'None'] );
		foreach ( $configs as $config ) {
			$configs_ids[ $config->ID ] = ['title'=> $config->post_title];
		}
		?>
		<tr>
			<td>
				<div class="asowp_config_data show_if_simple">
				<?php
				$this->display_asowp_config_on_WC_product_config( $id, $configs_ids, __('Attach this product to All Signs Options configuration','all-signs-options-free') );
				?>
				</div>
			</td>
		</tr>
		<?php
	}

	/**
	 * Save the chosen configuration to the product
	 */
	public function save_config( $post_id ) {
		$meta_key = 'product-asowp-metas';
		if ( isset($_POST['asowp_config_nonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash ( $_POST['asowp_config_nonce'] ) ) , 'asowp_config_nonce' ) && isset( $_POST[ $meta_key ] ) ) {
			$meta_value = map_deep( wp_unslash($_POST[ $meta_key ]), 'sanitize_text_field' );
			update_post_meta( $post_id, $meta_key, $meta_value );
		}
	}
	
	/**
	 * Adds the Custom column to the default products list to help identify which ones are custom
	 *
	 * @param array $defaults Default columns
	 * @return array $defaults result
	 */
	function get_product_columns( $defaults ) {
		$defaults['is_asowp_customizable'] = __( "All Signs Options", 'all-signs-options-free' );
		return $defaults;
	}
	/**
	 * Sets the Custom column value on the products list to help identify which ones are custom
	 *
	 * @param string $column_name Column name
	 * @param int $id Product ID
	 */
	public function get_products_columns_values( $column_name, $id ) {
		if ( $column_name === 'is_asowp_customizable' ) {
			$asowp_metas = get_post_meta( $id, 'product-asowp-metas', true );
			if ( isset( $asowp_metas[ $id ]['config-id'] ) && get_post($asowp_metas[ $id ]['config-id'])) {
				if ( empty( $asowp_metas[ $id ]['config-id'] ) ) {
					esc_attr_e( 'No', 'all-signs-options-free' );
				} else {
					esc_attr_e( "Yes", 'all-signs-options-free' );
				}
			} else {
				esc_attr_e( 'No', 'all-signs-options-free' );
			}
		}
	}

	public function is_asowp_customizable() {
		return ( ! empty( $this->settings ) );
	}


	public function get_design_buttons( $with_upload = false ) {
		ob_start();
		$content      = '';
		$product      =  $this->product;

		if ( $this->variation_id ) {
			$item_id = $this->variation_id;
		} else {
			$item_id = $this->root_product_id;
		}
		
		if ( $product->is_type('variable')) {
			$variations = $product->get_available_variations();
			foreach ( $variations as $variation ) {
				if ( ! $variation['is_purchasable'] || ! $variation['is_in_stock'] ) {
					continue;
				}
				$asowp_product = new ASOWP_Product_Config( $variation['variation_id'] );
				if( $asowp_product->is_asowp_customizable() ) {
					echo wp_kses_post($asowp_product->get_design_buttons( $with_upload ));
				}
			}
			
		} else {
			
			?>
			<div class="asowp-buttons-wrap-<?php echo esc_attr($product->get_type()); ?>" data-id="<?php echo esc_attr($this->variation_id); ?>">
					
			<?php

			
			$default_design_btn_url = $this->get_design_page_url();
			$have_pages_settings = get_option("asowp_config_page");
			$content               .= '<a  href="' . $default_design_btn_url . '" class="button asowp-design-product">' . $have_pages_settings["buttons"]["productDesignButton"] . '</a>';

			if ( ! isset( $item_id ) ) {
				$item_id = '';
			}
			if ( ! isset( $default_design_btn_url ) ) {
				$default_design_btn_url = '';
			}
			echo wp_kses_post(apply_filters( 'asowp_show_customization_buttons_in_modal', wp_kses_post($content), $item_id, $default_design_btn_url, $product->get_type() ));
			?>
			</div>
			<?php
		}
		
		$output = ob_get_clean();
		return $output;
	}

	/**
	 * Returns the customization page URL
	 *
	 * @global array $asowp_settings
	 * @param int   $design_index Saved design index to load
	 * @param mixed $cart_item_key Cart item key to edit
	 * @param int   $order_item_id Order item ID to load
	 * @param int   $tpl_id ID of the template to load
	 * @return string
	 */
	public function get_design_page_url( $design_index = false, $cart_item_key = false, $order_item_id = false, $tpl_id = false ) {

		//global $wp_query;
		if ( $this->variation_id ) {
			$item_id = $this->variation_id;
		} else {
			$item_id = $this->root_product_id;
		}


		$page_settings = get_option("asowp_config_page");
		if ( !empty($page_settings) && $page_settings != false ) {
			$configPage = $page_settings["configuratorPage"];
			if($configPage != 0){
				$asowp_page_id = $configPage;
			}
			else {
				$asowp_page_id = false;
			}
		} else {
			$asowp_page_id = false;
		}
		


		$asowp_page_url = '';
		if ( $asowp_page_id ) {

			$asowp_page_url = get_permalink( $asowp_page_id );

			if ( $item_id ) {
				$query = wp_parse_url( $asowp_page_url, PHP_URL_QUERY );
				// Returns a string if the URL has parameters or NULL if not
				if ( get_option( 'permalink_structure' ) ) {
					if ( substr( $asowp_page_url, -1 ) !== '/' ) {
						$asowp_page_url .= '/';
					}elseif ( $cart_item_key ) {
						$qty_key       = 'qty_' . $cart_item_key . '_' . $item_id;
						$asowp_page_url .= "edit/$item_id/$cart_item_key/" . '?custom_qty=' . get_option( $qty_key, $this->get_purchase_properties()['min_to_purchase'] );
					} elseif ( $order_item_id ) {
						$asowp_page_url .= "ordered-design/$item_id/$order_item_id/";
						$asowp_page_url  = apply_filters( 'asowp_customized_order_page_url', $asowp_page_url );
					} else {
						$asowp_page_url .= 'asowp-design/' . $item_id . '/';
						if ( $tpl_id ) {
							$asowp_page_url .= "$tpl_id/";
						}
					}
				} else {
					if ( $design_index !== false ) {
						$asowp_page_url .= '&asowp-product-id' . $item_id . '&design_index=' . $design_index;
					} elseif ( $cart_item_key ) {
						$qty_key       = 'qty_' . $cart_item_key . '_' . $item_id;
						$asowp_page_url .= '&asowp-product-id' . $item_id . '&edit=' . $cart_item_key . '&custom_qty=' . get_option( $qty_key, $this->get_purchase_properties()['min_to_purchase'] );
					} elseif ( $order_item_id ) {
						$asowp_page_url .= '&asowp-product-id' . $item_id . '&vcid=' . $order_item_id;
					} else {
						$asowp_page_url .= '&asowp-product-id' . $item_id;
						if ( $tpl_id ) {
							$asowp_page_url .= "&tpl=$tpl_id";
						}
					}
				}
			}
		}

		return $asowp_page_url;
	}
	
	/**
	 * Returns the minimum and maximum order quantities
	 *
	 * @return array
	 */
	public function get_purchase_properties() {
		if ( $this->variation_id ) {
			$defined_min_qty = get_post_meta( $this->variation_id, 'variation_minimum_allowed_quantity', true );
			// We consider the values defined for the all of them
			if ( ! $defined_min_qty ) {
				$defined_min_qty = get_post_meta( $this->root_product_id, 'minimum_allowed_quantity', true );
			}

			$defined_max_qty = get_post_meta( $this->variation_id, 'variation_maximum_allowed_quantity', true );
			// We consider the values defined for the all of them
			if ( ! $defined_max_qty ) {
				$defined_max_qty = get_post_meta( $this->root_product_id, 'maximum_allowed_quantity', true );
			}
		} else {
			$defined_min_qty = get_post_meta( $this->root_product_id, 'minimum_allowed_quantity', true );
			if ( ! $defined_min_qty ) {
				$defined_min_qty = 1;
			}

			$defined_max_qty = get_post_meta( $this->root_product_id, 'variation_maximum_allowed_quantity', true );
		}

		$step    = apply_filters( 'woocommerce_quantity_input_step', '1', $this->product );
		$min_qty = apply_filters( 'woocommerce_quantity_input_min', $defined_min_qty, $this->product );

		if ( ! $defined_max_qty ) {
			$defined_max_qty = apply_filters( 'woocommerce_quantity_input_max', $this->product->backorders_allowed() ? '' : $this->product->get_stock_quantity(), $this->product );
		}

		$min_to_purchase = $min_qty;
		if ( ! $min_qty ) {
			$min_to_purchase = 1;
		}

		$defaults = array(
			'max_value' => $defined_max_qty,
			'min_value' => $min_to_purchase,
			'step'      => $step,
		);
		$args     = apply_filters( 'woocommerce_quantity_input_args', wp_parse_args( array(), $defaults ), $this->product );

		return array(
			'min'             => $args['min_value'],
			'min_to_purchase' => $args['min_value'],
			'max'             => $args['max_value'],
			'step'            => $args['step'],
		);
	}
	/**
	 * Hide add to cart button on product details page
	 */
	public function hide_cart_button() {
		global $product;
		$pid                 = $product->get_id();
		$configs = get_post_meta($pid,"product-asowp-metas",true);
		if ( isset( $configs[ $pid ]['config-id'] )){
			$config_id = $configs[$pid];
			$meta_value = get_post_meta((int)$config_id['config-id'],"asowp-configs-meta",true);
			$general_options  = $meta_value["data"]["settings"]["generals"]["product"]??null;
			$custom_products_ids     = asowp_get_custom_products();
			
			if ( in_array( $pid, $custom_products_ids ) && $general_options!=null && $general_options['hideAddToCartButtonOnDetailPage'] ) {
				wp_localize_script("asowp-product-min","cart_hide_button",[
					"hide_cart_button"=>$general_options['hideAddToCartButtonOnDetailPage']
				]);
				add_action( 'wp_footer', [$this,'asowp_product_page_script_init'] );
			}
		}
	}
	public function asowp_product_page_script_init() { 
		global $product;
		$pid                 = $product->get_id();
		
		$inline_script = "		
			jQuery(\"[value='".esc_html($pid). "']\").parent().find('.add_to_cart_button').hide();
			jQuery(\"[value='".esc_html($pid). "']\").parent().find('.single_add_to_cart_button').hide();
		";
        wp_add_inline_script( 'asowp-product-min', $inline_script );
        wp_add_inline_script( 'asowp-tinymce-script', $inline_script );
	}
	/**
	 * Add Custom button on details product page
	 */
	public function get_customize_btn() {
		$product_id  = get_the_ID();
		$asowp_product = new asowp_Product_Config( $product_id );
		$product     = wc_get_product( $product_id );
		
		if($asowp_product->is_asowp_customizable()){
			if ('simple' === $product->get_type() || 'variable' === $product->get_type()) {
				ob_start(); ?>
				<div class="asowp-buttons-url">
				<?php
					echo wp_kses_post($asowp_product->get_design_buttons( true ));
				?>
				</div>
				<?php
			} 
		}
	}
	/**
	 * Add a personalization button on the shop page
	 */
	public function hide_or_display_custom_button_or_add_to_cart_button( $html, $product ) {
		$productId = $product->get_id();
		$configs = get_post_meta($productId,"product-asowp-metas",true);
		$meta_value = isset($configs[$productId]['config-id']) ? get_post_meta((int)$configs[$productId]['config-id'],"asowp-configs-meta",true) : [];
		$general_options  = $meta_value["data"]["settings"]["generals"]["product"]??null;
		
		if($general_options != null){
			if ( !$general_options["hideDesignButtonsOnShopPage"]) {
				$product_class = get_class( $product );
				if ( $product_class == 'WC_Product_Simple' ) {
					$asowp_product = new asowp_Product_Config( $productId );
					if ( $asowp_product->is_asowp_customizable() ) {
						$html .= wp_kses_post($asowp_product->get_design_buttons());
					}			
				}
			}
			
			if ( $general_options["hideAddToCartButtonOnShopPage"]) {
				$inline_script = "		
					jQuery(\"[data-product_id='" .esc_attr($productId). "']\").hide();
				";
				wp_add_inline_script( 'asowp-product-min', $inline_script );
				wp_add_inline_script( 'asowp-tinymce-script', $inline_script );
			}
		}
		return $html;		
	}

	/**
	 * Add css class on detail product page in order to hide or no add to cart button 
	 */
	public function get_button_on_single_product_summary(  ) {
		global $post;
		global $product;
		$pid                 = $post->ID;
		$configs = get_post_meta($pid,"product-asowp-metas",true);
		if(isset($configs[$pid]['config-id'])) {
			//$config_id = $configs[$pid]['config-id'];
			//$meta_value = get_post_meta($config_id,"asowp-configs-meta",true);
			
			//$general_options  = $meta_value["data"]["settings"]["generals"]["product"] ?? null;
			$custom_products_ids     = asowp_get_custom_products();			
			
			if ( in_array( $pid, $custom_products_ids ) ) {	
				$asowp_product = new ASOWP_Product_Config( $product->get_id() );
				if ( $asowp_product->is_asowp_customizable() ) {
					echo wp_kses_post($asowp_product->get_design_buttons());
				}
			}
		}
	}

		/**
	 * Returns a variation root product ID
	 *
	 * @param int $variation_id Variation ID
	 * @return int
	 */
	public function get_parent( $variation_id ) {
		$variable_product = wc_get_product( $variation_id );
		if ( ! $variable_product ) {
			return false;
		}
		if ( $variable_product->get_type() !== 'variation' ) {
			$product_id = $variation_id;
		} else {
			$product_id = $variable_product->get_parent_id();
		}

		return $product_id;
	}

	public function delete_product_file_when_delete_product($cart_item_key, $cart) {
		$cart_item_content = $cart->removed_cart_contents[$cart_item_key];
		//error_log('ASO Recaps pour le cart_item_key ' . $cart_item_key . ': ' . print_r($cart_item_content, true));
		if (!empty($cart_item_content)  && isset($cart_item_content["asowp_meta_data"]['recaps'])) {
			$meta_data = $cart_item_content["asowp_meta_data"]['recaps'];
			$uploads = [];
			if (isset($meta_data['images']["value"]['face1'])) {
				foreach ($meta_data['images']["value"] as $key => $face) {
					foreach ($face as $key => $image) {
						if (isset($image["infos"])) {
							$uploads[] = $image["infos"];
						}
					}
				}
			} elseif (isset($meta_data['images']["value"])) {
				foreach ($meta_data['images']["value"] as $key => $image) {
					if (isset($image["infos"])) {
						$uploads[] = $image["infos"];
					}
				}
			}
	
			foreach ($uploads as $upload) {
				if (isset($upload["name"])) {
					$file = ASOWP_IMAGE_PATH . DIRECTORY_SEPARATOR . $upload["name"];
					if (file_exists($file)) {
						 wp_delete_file($file);
					}
				}
			}
	
			if (!isset($meta_data["designImages"]["face1"])) {
				if (isset($meta_data["designImages"])) {
					foreach ($meta_data["designImages"] as $key => $image) {
						$path_parts = pathinfo($image);
						if (isset($path_parts["filename"], $path_parts['extension'])) {
							$file = ASOWP_IMAGE_PATH . DIRECTORY_SEPARATOR . $path_parts["filename"] . '.' . $path_parts['extension'];
							if (file_exists($file)) {
								 wp_delete_file($file);
							}
						}
					}
				}
			} else {
				foreach ($meta_data["designImages"] as $key => $face) {
					foreach ($face as $key => $image) {
						$path_parts = pathinfo($image);
						if (isset($path_parts["filename"], $path_parts['extension'])) {
							$file = ASOWP_IMAGE_PATH . DIRECTORY_SEPARATOR . $path_parts["filename"] . '.' . $path_parts['extension'];
							if (file_exists($file)) {
								 wp_delete_file($file);
							}
						}
					}
				}
			}
		}
	}

 }