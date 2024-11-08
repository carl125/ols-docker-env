<?php
/**
 * Flatsome functions and definitions
 *
 * @package flatsome
 */

require get_template_directory() . '/inc/init.php';

/**
 * Note: It's not recommended to add any custom code here. Please use a child theme so that your customizations aren't lost during updates.
 * Learn more here: http://codex.wordpress.org/Child_Themes
 */

// Tắt thông báo cập nhật cho tất cả plugin
add_filter( 'site_transient_update_plugins', '__return_null' );


// ------------ Start Tùy chỉnh các trường trong form thanh toán ----------------------------------
add_filter( 'woocommerce_checkout_fields' , 'custom_checkout_form' );
// add_filter('woocommerce_ship_to_different_address_checked', '__return_false');

function custom_checkout_form( $fields ) {
    unset( $fields['billing']['billing_last_name'] );
    unset( $fields['billing']['billing_company'] );
    unset( $fields['billing']['billing_address_2'] );
    unset( $fields['billing']['billing_city'] );
    unset( $fields['billing']['billing_postcode'] );
    unset( $fields['billing']['billing_country'] );
    unset( $fields['billing']['billing_state'] );
    unset( $fields['billing']['billing_email'] );

	 // Đặt placeholder cho trường tên (First Name)
    $fields['billing']['billing_first_name']['label'] = "Họ và tên";
    
    // Đặt placeholder cho trường số điện thoại
    $fields['billing']['billing_phone']['placeholder'] = "Số điện thoại";
	$fields['billing']['billing_address_1']['label'] = 'Địa chỉ nhận hàng';
    return $fields;
}

// add_filter( 'woocommerce_default_address_fields', 'custom_checkout_field_label' );
// Tùy chỉnh label cho các trường địa chỉ và tên
function custom_checkout_field_label( $fields ) {
    // Đặt label cho trường địa chỉ
    $fields['address_1']['label'] = 'Địa chỉ giao sản phẩm';
    
    // Đặt label cho trường tên (First Name)
    $fields['first_name']['label'] = 'Tên';
    
    return $fields;
}


// Bỏ bắt buộc điền các trường không cần thiết trong form thanh toán
// add_filter( 'woocommerce_billing_fields', 'filter_billing_fields', 20, 1 );

function filter_billing_fields( $billing_fields ) {
    // Chỉ áp dụng trên trang thanh toán
    if( ! is_checkout() ) return $billing_fields;

    // Bỏ bắt buộc nhập email
    $billing_fields['billing_email']['required'] = false;
    
    // Bỏ bắt buộc nhập công ty
    $billing_fields['billing_company']['required'] = false;
    
    // Bỏ bắt buộc nhập thành phố
    $billing_fields['billing_city']['required'] = false;
    
    // Bỏ bắt buộc nhập địa chỉ thứ hai (Address Line 2)
    $billing_fields['billing_address_2']['required'] = false;
    
    // Bỏ bắt buộc nhập bang/hạt
    $billing_fields['billing_state']['required'] = false;
    
    // Bỏ bắt buộc nhập mã bưu điện (postcode)
    $billing_fields['billing_postcode']['required'] = false;
    
    // Bỏ bắt buộc nhập họ (Last Name)
    $billing_fields['billing_last_name']['required'] = false;

    return $billing_fields;
}

// Thêm hình ảnh sản phẩm vào trang thanh toán
add_filter( 'woocommerce_cart_item_name', 'custom_product_image_on_checkout', 10, 3 );

function custom_product_image_on_checkout( $name, $cart_item, $cart_item_key ) {
    // Chỉ thêm hình ảnh trên trang thanh toán
    if ( ! is_checkout() ) {
        return $name;
    }

    // Lấy đối tượng sản phẩm
    $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

    // Lấy ảnh thumbnail của sản phẩm
    $thumbnail = $_product->get_image();

    // Thêm CSS và đóng gói hình ảnh vào thẻ div
    $image = '<div class="ts-product-image">' . $thumbnail . '</div>';

    // Thêm hình ảnh vào trước tên sản phẩm và trả về
    return $image . $name;
}

// add_filter( 'woocommerce_ship_to_different_address_checked', '__return_true' );
// Vô hiệu hóa tùy chọn "Giao hàng tới địa chỉ khác?"
add_filter('woocommerce_cart_needs_shipping_address', '__return_false');



// ------------ End Tùy chỉnh các trường trong form thanh toán ----------------------------------

// ------------ Block REST API cho người dùng không đăng nhập ----------------------------------
// add_filter( 'rest_authentication_errors', function( $result ) {
//     if ( ! is_user_logged_in() ) {
//         return new WP_Error( 'rest_forbidden', 'REST API is restricted to logged-in users.', array( 'status' => 403 ) );
//     }
//     return $result;
// });

function handle_custom_variation_add_to_cart() {
    if ( isset($_POST['add_variations_nonce']) && wp_verify_nonce( $_POST['add_variations_nonce'], 'add-multiple-variations' ) ) {
        if ( isset($_POST['product_id']) && isset($_POST['variation_id']) && isset($_POST['quantity']) ) {
            $product_id = intval($_POST['product_id']);
            
            foreach ($_POST['variation_id'] as $variation_id => $v_id) {
                $quantity = isset($_POST['quantity'][$variation_id]) ? intval($_POST['quantity'][$variation_id]) : 0;

                if ($quantity > 0) {
                    $variation_data = isset($_POST['variation'][$variation_id]) ? $_POST['variation'][$variation_id] : [];
                    WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation_data);
                }
            }

//             wp_safe_redirect( wc_get_cart_url() );
//             exit;
            wc_add_to_cart_message(array($product_id => $quantity), true);
        }
    }
}
add_action('template_redirect', 'handle_custom_variation_add_to_cart');
