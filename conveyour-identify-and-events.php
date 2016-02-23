<?php
/*Conveyour*/
//Set subscription status as inactive -- Woo mem
function set_deact_mem_stat($user_id){
	$sub_obj = get_user_by('id', $user_id);
	$identify = $sub_obj->user_email;
	$traits = array('subscription_status' => 'inactive',) ;
	conveyour_identify($identify, $traits);
	}
add_action( 'cancelled_subscription', 'set_deact_mem_stat', 10, 2 );


//Set subscription status as active -- Woo mem
function set_act_mem_stat($user_id){
	$sub_obj = get_user_by('id', $user_id);
	$identify = $sub_obj->user_email;
	$traits = array('subscription_status' => 'active',) ;
	conveyour_identify($identify, $traits);
	}
add_action( 'activated_subscription', 'set_act_mem_stat', 10, 2 );




function send_product_tag ($order_id){

 	  $order = new WC_Order($order_id);
	  $user = $order->billing_email;
	  $items = $order->get_items();
	  $coupons = $order->get_used_coupons();
	  $traits = array('has_purchased' => 'yes',) ;
	  conveyour_identify($user, $traits);

	if(isset($coupons)){
		foreach ( $coupons as $coupon ) {
		conveyour_track($user,'used_coupon',array('coupon' => $coupon));
		}

	}

		foreach ( $items as $item ) {
		  $product_name = $item['name'];
		  $product_name_underscores = str_replace(' ', '_', $product_name);
      	  $product_name_sliced_and_underscored = substr($product_name_underscores,0,6);
		  $myproduct_id = $item['product_id'];
		  $my_product_name = $myproduct_id.'-'.$product_name_sliced_and_underscored;
		  $product_object = new WC_Product($myproduct_id);
    	  $product_price = $product_object->regular_price;
		  $traits = array('product' => $my_product_name,'Item-Price' => $product_price,'Order-ID' => $order_id);

		  conveyour_track($user,'Purchased_product',$traits);
		}
}
add_action('woocommerce_payment_complete', 'send_product_tag', 10, 1);



// is paying CX
function is_paying_cx_tag($order_id){
	global $current_user;
      get_currentuserinfo();
	  $user = $current_user->user_email;
	  $order = new WC_Order($order_id);
	  $items = $order->get_items();
		foreach ( $items as $item ) {
			 $myproduct_id = $item['product_id'];
			 $product_object = new WC_Product($myproduct_id);
			 $product_price = $product_object->regular_price;
		 if($product_price > 0 ){
			 $traits = array('Is_paying' => 'YES',) ;
		  conveyour_identify($user, $traits);
			 }else{
				  return false;
			 }
		}
	}

add_action('woocommerce_order_status_completed', 'is_paying_cx_tag', 10, 1);

//User Affiliate Identify tag
function Affiliate_Identify_func() {
	 global $current_user;
     get_currentuserinfo();
	 $user = $current_user->user_email;
	$traits = array('Affiliate' => 'active',) ;
	conveyour_identify($user, $traits);
}
add_shortcode( 'Affiliate', 'Affiliate_Identify_func' );


?>
