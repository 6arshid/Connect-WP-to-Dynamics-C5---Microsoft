 /**
	* Display the custom text field
	* @since 1.0.0
	*/
   function cfwc_create_custom_field() {
	$args = array(
	'id' => 'old_product_id',
	'label' => __( 'Gammelt produkt-id', 'cfwc' ),
	'class' => 'cfwc-custom-field',
	'desc_tip' => true,
	'description' => __( 'Enter the title of your custom text field.', 'ctwc' ),
	);
	woocommerce_wp_text_input( $args );
   }
   add_action( 'woocommerce_product_options_general_product_data', 'cfwc_create_custom_field' );
   
   /**
	* Save the custom field
	* @since 1.0.0
	*/
   function cfwc_save_custom_field( $post_id ) {
	$product = wc_get_product( $post_id );
	$title = isset( $_POST['old_product_id'] ) ? $_POST['old_product_id'] : '';
	$product->update_meta_data( 'old_product_id', sanitize_text_field( $title ) );
	$product->save();
   }
   add_action( 'woocommerce_process_product_meta', 'cfwc_save_custom_field' );
