<?php
/**
 * Contains all methods and hooks callbacks related to the design
 *
 * @author Vertim Coders
 */
class ASOWP_Design {

    /**
     * Set all aso configuration initialization hooks
    */
    public function init_hooks() {
		//cart
		add_action( 'woocommerce_before_calculate_totals', [$this, 'asowp_change_product_price_in_cart'], 10,1 );
		add_filter( 'woocommerce_cart_item_thumbnail', [$this, 'asowp_change_product_image_in_cart'], 99, 3 );
		add_action('woocommerce_after_cart_item_name', [$this,'display_previewBtn_editBtn_in_cart'], 10);
		add_filter('woocommerce_get_item_data', [$this,'display_recaps_config_on_checkout_page'], 20, 2);
		
		//admin data
		add_action( 'woocommerce_after_order_itemmeta',[$this, 'get_order_custom_admin_data'], 10, 3);
		add_action('woocommerce_checkout_create_order_line_item', [$this,'capture_product_metadata_to_order'], 10, 4);
		
		// Emails.
		//add_action( 'woocommerce_order_item_meta_start', [$this, 'mail_template'],10, 3);
		add_filter( 'woocommerce_email_attachments', [$this, 'custom_email_attachments'], 10, 4  );
		add_action( 'woocommerce_order_item_meta_end', [$this, 'mail_template'], 11, 4  );
    }

	/**
	 * 
	 */
	public function asowp_change_product_price_in_cart( $cart ) {
		
		if (is_admin() && !defined('DOING_AJAX')) return;


		foreach ( $cart->cart_contents as $cart_item_key => $cart_item ) {
			if ( $cart_item['variation_id'] ) {
				$variation_id = $cart_item['variation_id'];
			} else {
				$variation_id = $cart_item['product_id'];
			}


			if ( isset( $cart_item["asowp_meta_data"]) ) {
				if ( isset( $cart_item['asowp_meta_data']["recaps"]['custom_price'] ) ) {
					$item_price = apply_filters( 'asowp_cart_item_price', $cart_item['asowp_meta_data']["recaps"]['custom_price'], $variation_id );		
					$cart_item['data']->set_price( $item_price );
				}
				
			}

			// Ajout d'un filtre pour mettre à jour le prix total de l'element dans le panier.
		}
	}

	/**
	 * 
	 */
	public function asowp_change_product_image_in_cart( $product_image_code, $values) {
		if ( isset( $values['asowp_meta_data']["recaps"] ) ) {
			$previews = $values['asowp_meta_data']["recaps"]["designImages"];
			if(isset($previews["face1"])){
				$product_image_code = "<img class='asowp-cartitem-img' src='" . esc_url($previews["face1"][0]) . "'>";	
			}else{
				$product_image_code = "<img class='asowp-cartitem-img' src='" . esc_url($previews[0]) . "'>";	
			}
			return $product_image_code;
		}

	}

	public function display_previewBtn_editBtn_in_cart($cart_item){
		$product = $cart_item['data'];
		$have_pages_settings = get_option("asowp_config_page");
		// Construisez les URL pour les aperçus et les éditions (ajustez selon vos besoins)
		//$preview_url = get_permalink($product->get_id());

		//$preview_data = get_transient( 'preview_' . $product->get_id() );

		//$npd_product = new asowp_Product_Config( $product->get_id() );
		if(isset($cart_item['asowp_meta_data']["recaps"])){
			$product_name = '';
			$modal_id = uniqid('asowp-recaps');
			ob_start();
			?>
			
			<div class="omodal fade o-modal wpc_part" id="<?php echo esc_attr($modal_id); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="omodal-dialog">
					<div class="omodal-content">
						<div class="omodal-header">
							<button type="button" class="close" data-dismiss="omodal" aria-hidden="true">&times;</button>
						</div>
						<div class="omodal-body">
							<?php echo wp_kses_post($this->display_custom_recaps($cart_item['asowp_meta_data']["recaps"],false)); ?>
						</div>
					</div>
				</div>
			</div>
			<?php 
				$preview_modal_id = uniqid('as-preview');
			?>
			<div class="omodal fade o-modal wpc_part" id="<?php echo esc_attr($preview_modal_id); ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="omodal-dialog">
					<div class="omodal-content">
						<div class="omodal-header">
							<button type="button" class="close" data-dismiss="omodal" aria-hidden="true">&times;</button>
						</div>
						<div class="omodal-body">
							<?php if(!isset($cart_item['asowp_meta_data']["recaps"]["designImages"]["face1"])){ ?>
								<img src="<?php echo esc_url($cart_item['asowp_meta_data']["recaps"]["designImages"][0])?>" style="
										width: auto;
										height: 500px;"/>
							<?php } else { ?>
								<div>
									<img src="<?php echo esc_url($cart_item['asowp_meta_data']["recaps"]["designImages"]["face1"][0])?>" style="width: auto; height: 500px;"/>
								</div>
								<div>
									<img src="<?php echo esc_url($cart_item['asowp_meta_data']["recaps"]["designImages"]["face2"][0])?>" style="width: auto; height: 500px;"/>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
			<div class="asowp-product-links">
				<span class="asowp-cart-product-preview o-modal-trigger button" data-toggle="o-modal" data-target="#<?php echo esc_attr($modal_id); ?>"><?php echo esc_html($have_pages_settings["buttons"]["recapsButtonOnCart"]) ?></span>
				<span class="asowp-cart-product-preview o-modal-trigger button" data-toggle="o-modal" data-target="#<?php echo esc_attr($preview_modal_id); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 20px;height: 20px;">
						<path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
						<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
					</svg>
				</span>
			</div>
			<?php
			$product_name.=ob_get_clean();		
			echo wp_kses_post($product_name);
		}
	}

	public function display_recaps_config_on_checkout_page($item_data, $cart_item){
		if (is_checkout()) {
			$product = $cart_item['data'];

			if ( $product ) {
				
				$product_id = $product->get_id();
				$product_meta_data = get_post_meta( $product_id, 'product-asowp-metas', true );
				if ( isset( $product_meta_data[ $product_id ]['config-id'] ) && get_post($product_meta_data[ $product_id ]['config-id'])){
					if ( empty( !$product_meta_data[ $product_id ]['config-id'] ) ) {
						$configId = $product_meta_data[ $product_id ]['config-id'];
                    	$config = get_post_meta($configId,"asowp-configs-meta",true);
						if($config["data"]["settings"]["generals"]["product"]["displayRecapsOnCheckout"]){
							echo wp_kses_post($this->display_custom_recaps($cart_item['asowp_meta_data']["recaps"],false));
						}
					}
				}
			}
		}
	}

	private function display_custom_recaps($recaps,$admin=true){
		ob_start();?>
		<div style="display:flex; flex-direction:column;">
			<div class="asowp-custom-options-info">
				<label for=""><?php echo esc_html($recaps["sign"]["size"]["label"])?>: </label>
				<span><?php echo esc_html($recaps["sign"]["size"]["value"]["width"]["label"])?>: <?php echo esc_html($recaps["sign"]["size"]["value"]["width"]["value"])?></span>
				<span><?php echo esc_html($recaps["sign"]["size"]["value"]["height"]["label"])?>: <?php echo esc_html($recaps["sign"]["size"]["value"]["height"]["value"])?></span>
			</div>
			<?php if($recaps["sign"]["size"]["value"]["thickness"]["value"] !=='none') {?>
			<div class="asowp-custom-options-info">
				<label for=""><?php echo esc_html($recaps["sign"]["size"]["value"]["thickness"]["label"])?>: </label>
				<span><?php echo esc_html($recaps["sign"]["size"]["value"]["thickness"]["value"])?></span>
			</div>
			<?php }?>
			<div class="asowp-custom-options-info">
				<label for=""><?php echo esc_html($recaps["sign"]["shape"]["label"])?>: </label>
				<span><?php echo esc_html($recaps["sign"]["shape"]["value"])?></span>
			</div>
			<div class="asowp-custom-options-info">
				<label for=""><?php echo esc_html($recaps["sign"]["fixingMethod"]["label"])?>: </label>
				<span><?php echo esc_html($recaps["sign"]["fixingMethod"]["value"])?></span>
			</div>
			<div class="asowp-custom-options-info">
				<label for=""><?php echo esc_html($recaps["sign"]["border"]["label"])?>: </label>
				<?php if(isset($recaps["sign"]["border"]["value"]["face1"]) || isset($recaps["texts"]["value"]["face2"])) {?>
				<?php foreach ($recaps["sign"]["border"]["value"] as $key => $face) {?>
					<div style="display:flex; justify-content:center; align-items:center;">
						<label for=""style="margin: 0 5px;"><?php echo esc_html($recaps["faces"][$key])?>: </label>
						<span for=""style="margin: 0 5px;"><?php echo esc_html($face["type"])?> </span>
						<?php if(isset($face["codeHex"])) { ?>
						<div class="asowp-cart-color-option" style="background:<?php echo esc_attr($face["codeHex"])?>;"></div>
						<?php } ?>
					</div>
				<?php }} else{?>
					<span for=""style="margin: 0 5px;"><?php echo esc_html($recaps["sign"]["border"]["value"]["type"])?> </span>
					<?php if(isset($recaps["sign"]["border"]["value"]["codeHex"])) { ?>
					<div class="asowp-cart-color-option" style="background:<?php echo esc_attr($recaps["sign"]["border"]["value"]["codeHex"])?>;"></div>
					<?php }?>
				<?php }?>
			</div>
			<div class="asowp-custom-options-info">
				<label for=""><?php echo esc_html($recaps["sign"]["color"]["label"])?>: </label>
				<?php if(isset($recaps["sign"]["color"]["value"]["face1"]) || isset($recaps["texts"]["value"]["face2"])) {?>
				<?php foreach ($recaps["sign"]["color"]["value"] as $key => $color) {?>
					<div style="display:flex; justify-content:center; align-items:center;">
						<label for=""style="margin: 0 5px;"><?php echo esc_html($recaps["faces"][$key])?>: </label>
						<span for=""style="margin: 0 5px;"><?php echo esc_html($color["name"])?> </span>
						<?php if($this->isColorCode($color["codeHex"])) {?>
							<div class="asowp-cart-color-option" style="background:<?php echo esc_attr($color["codeHex"])?>;"></div>
						<?php }else{?>
							<div class="asowp-cart-color-option" style="position:relative;">
								<img src="<?php echo esc_url($color["codeHex"])?>" style="position:absolute; width:100%,height:100%;"/>
							</div>
						<?php }?>
					</div>
				<?php }} else{?>
					<span for=""style="margin: 0 5px;"><?php echo esc_html($recaps["sign"]["color"]["value"]["name"])?> </span>
					<?php if($this->isColorCode($recaps["sign"]["color"]["value"]["codeHex"])) {?>
							<div class="asowp-cart-color-option" style="background:<?php echo esc_attr($recaps["sign"]["color"]["value"]["codeHex"])?>;"></div>
					<?php }else{?>
						<div class="asowp-cart-color-option" style="position:relative;">
							<img src="<?php echo esc_url($recaps["sign"]["color"]["value"]["codeHex"])?>" style="position:absolute; width:100%; height:100%;"/>
						</div>
					<?php }?>
				<?php }?>
			</div>
			<?php if(isset($recaps["texts"]["value"]) && count($recaps["texts"]["value"])>0) {?>
				<div class="asowp-custom-options-info">
					<label for=""><?php echo esc_html($recaps["texts"]["label"])?>: </label>
					<?php if(isset($recaps["texts"]["value"]["face1"]) || isset($recaps["texts"]["value"]["face2"])) {?>
						<?php foreach ($recaps["texts"]["value"] as $key => $face) { ?>
							<div >
								<label for=""style="margin: 0 5px;"><?php echo esc_html($recaps["faces"][$key])?>: </label>
								<?php foreach ($face as $text) {?>
									<div>
										<span><?php echo esc_html($text["textContent"])?></span>
										<div class="asowp-custom-options-info-infos" >
										<?php if($admin) { foreach ($text["values"] as $key => $position) {?>
											<span><?php echo esc_html( $position["label"]). ": " .esc_html( $position["value"]) ;?></span>
										<?php } } ?>
										</div>
									</div>
								<?php } ?>
							</div>
						<?php }
						} else{?>
							<?php foreach ($recaps["texts"]["value"] as $key => $text) {?>
								<div>
									<span><?php echo esc_html($text["textContent"])?></span>
									<div class="asowp-custom-options-info-infos" >
										<?php if($admin) { foreach ($text["values"] as $key => $position) {?>
											<span><?php echo esc_html( $position["label"]). ": " .esc_html( $position["value"]) ;?></span>
										<?php } } ?>
									</div>
								</div>
							<?php }?>
					<?php } ?>
				</div>
			<?php } ?>
			<?php if( isset($recaps["images"]["value"]) && count($recaps["images"]["value"])>0 && $admin) {?>
			<div class="asowp-custom-options-info">
				<label for=""><?php echo esc_html($recaps["images"]["label"])?>: </label>
				<?php if(isset($recaps["images"]["value"]["face1"])) {?>
					<?php foreach ($recaps["images"]["value"] as $key => $face) { ?>
						<div>
							<label for=""style="margin: 0 5px;"><?php echo esc_html($recaps["faces"][$key])?>: </label>
							<?php foreach ($face as $image) {?>
								<div class="asowp-custom-options-info-infos" style="display: block !important;">
									<div>
										<p><?php echo esc_html__("file","all-signs-options-free") . " : ". esc_html($image["infos"]["name"]) ?></p>
									</div>
									<?php if($admin) { foreach ($image["values"] as $key => $position) {?>
										<span><?php echo esc_html( $position["label"]). ": " .esc_html( $position["value"]) ;?></span>
									<?php } } ?>
								</div>
							<?php } ?>
						</div>
					<?php }
					} else{?>
					<div style="display:flex; flex-direction:column; justify-content:center; align-items:center;">
						<?php foreach ($recaps["images"]["value"] as $key => $image) {?>
							<div class="asowp-custom-options-info-infos" style="display: block !important;">
								<div>
									<p><?php echo esc_html__("file","all-signs-options-free") . " : ". esc_html($image["infos"]["name"]) ?></p>
								</div>
								<?php if($admin) { foreach ($image["values"] as $key => $position) {?>
									<span><?php echo esc_html( $position["label"]). ": " .esc_html( $position["value"]) ;?></span>
								<?php } } ?>
							</div>
						<?php }?>
					</div>
			<?php } ?>
			</div>
			<?php } ?>
			<?php if( isset($recaps["additionalComponents"]) && count($recaps["additionalComponents"])>0) {?>
				<?php foreach ($recaps["additionalComponents"] as $key => $value) {?>
					<div class="asowp-custom-options-info">
						<label for=""><?php echo  esc_html($value["option"])?>: </label>
						<span><?php echo esc_html($value["value"])?></span>
					</div>
				<?php } ?>
			<?php } ?>
			<?php if( isset($recaps["additionalOptions"]) && count($recaps["additionalOptions"])>0) {?>
				<?php foreach ($recaps["additionalOptions"] as $key => $value) {?>
					<div class="asowp-custom-options-info">
						<label for=""><?php echo  esc_html($value["label"])?>: </label>
						<span><?php echo esc_html($value["value"])?></span>
					</div>
				<?php } ?>
			<?php } ?>			
			<?php if ($admin) {?>
			<div class="asowp-custom-options-info">
				<label for=""><?php echo esc_html__("Previews","all-signs-options-free")?>: </label>
				<div>
					<?php if(!isset($recaps["designImages"]["face1"])) { ?>
						<?php foreach ($recaps["designImages"] as $key => $image) {?>
							<div style="display:flex; justify-content:center; align-items:center;">
								<div style="position:relative; width:fit-content">
									<img src="<?php echo esc_url($image)?>" style="width: auto; height: 50px;"/>
								</div>
								<div style="margin:10px 0">
									<a class="button alt asowp_admin_download_image" href="<?php echo esc_url($image)?>" download><?php echo esc_html__( 'Download File', "all-signs-options-free" )?></a>
								</div> 
							</div>
						<?php } ?>
					<?php } else {?>
						<?php foreach ($recaps["designImages"] as $key => $face) {
							foreach ($face as $key => $image) {?>
								<div style="display:flex; justify-content:center; align-items:center;">
									<div style="position:relative; width:fit-content">
										<img src="<?php echo esc_url($image)?>" style="width: auto; height: 50px;"/>
									</div>
									<div style="margin:10px 0">
										<a class="button alt asowp_admin_download_image" href="<?php echo esc_url($image)?>" download><?php echo esc_html__( 'Download File', "all-signs-options-free" )?></a>
									</div> 
								</div>
							<?php }
						} ?>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php
		return ob_get_clean(); 
	}

	private function isColorCode($chaine) {
		// Expression régulière pour vérifier les codes couleur hexadécimaux
		$pattern = '/^#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/';
		return preg_match($pattern, $chaine);
	}

    /**
	 * Add in mail the recap data.
	 *
	 * @param int   $item_id The item id.
	 * @param array $item The item data.
	 * @param mixed $order The order data.
	 * @return mixed
	 */
	/* public function set_email_order_item_meta( $item_id, $item, $order) {
		if ( is_order_received_page() ) {
			return;
		}
		$order_data   = wc_get_order_item_meta( $item_id, 'asowp_meta_data' );

		if ( isset( $order_data ) && !empty( $order_data ) ) {
			ob_start();

			$details = ob_get_clean();
			return $details;
		}

	} */

    /**
	 * Add order design to mail.
	 *
	 * @param array $attachments
	 * @param string $status
	 * @param  object $order
	 * @return array
	 */
	function custom_email_attachments( $attachments, $email_id, $order, $email ) {
		// Vérifier si l'e-mail est envoyé au client
		if ( $email->id === 'customer_completed_order' ) {
			$items = $order->get_items();
			foreach ( $items as $item ) {
				if ( isset( $item["asowp_meta_data"]['recaps'] ) ) {
					if( isset($item["asowp_meta_data"]['zip'])){
						$attachments[] = $item["asowp_meta_data"]['zip'];
					}
					
				}
			}
		}
	
		return $attachments;
	}

	/**
	 * 
	 */
	public function get_order_custom_admin_data( $item_id, $item, $_product ) {

		$order_data   = wc_get_order_item_meta( $item_id, 'asowp_meta_data' );
		$order_id = $item->get_order_id();
		if ( $order_id && isset( $order_data ) && !empty( $order_data ) ) {
			ob_start();
			echo wp_kses_post($this->display_custom_recaps($order_data["recaps"],true));
			if(isset($order_data["zip"])){?>
				<div style="margin:10px 0">
					<a class="button alt asowp_admin_download_image" href="<?php echo esc_url($order_data["zip"])?>" download><?php echo esc_html__( 'Download Order Zip file', "all-signs-options-free" )?></a>
				</div> <?php 
			}
			$details = ob_get_clean();
			echo wp_kses_post($details);
			
		}

	

	}

	/**
	 * 
	 */
	function capture_product_metadata_to_order($item, $cart_item_key, $values, $order) {
		$meta_key = 'asowp_meta_data';
		if ( isset( $values[ $meta_key ] ) ) {
			$item->update_meta_data( $meta_key, $values[ $meta_key ] );
		}
	}

	/**
	 * 
	 */
	function mail_template( $item_id, $item, $_product ) {
		
		$order_data   = wc_get_order_item_meta( $item_id, 'asowp_meta_data' );
		if ( isset( $order_data ) && !empty( $order_data ) ) {
			ob_start();
				if (is_account_page()) {
					echo wp_kses_post($this->display_custom_recaps($order_data["recaps"],true));
				}
				$details = ob_get_clean();
			echo wp_kses_post($details);
		}

	}
	
}
