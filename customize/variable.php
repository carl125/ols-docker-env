<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 6.1.0
 */
defined( 'ABSPATH' ) || exit;

global $product;

$attribute_keys  = array_keys( $attributes );
$variations_json = wp_json_encode( $available_variations );
$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

$attribute_labels = array();
foreach ( $attributes as $attribute_name => $options ) {
    $attribute_labels[ sanitize_title( $attribute_name ) ] = wc_attribute_label( $attribute_name );
}

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data' onsubmit="return validateForm(event);">
    <?php do_action( 'woocommerce_before_variations_form' ); ?>
    <?php wp_nonce_field( 'add-multiple-variations', 'add_variations_nonce' ); ?>

    <!-- Hidden field to pass the product ID -->
    <input type="hidden" name="product_id" value="<?php echo absint( $product->get_id() ); ?>" />

    <?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
        <p class="stock out-of-stock"><?php echo esc_html( apply_filters( 'woocommerce_out_of_stock_message', __( 'This product is currently out of stock and unavailable.', 'woocommerce' ) ) ); ?></p>
    <?php else : ?>
        <table class="variations" cellspacing="0" role="presentation">
            <tbody>
                <?php foreach ( $product->get_available_variations() as $variation ) :
                    $variation_id = $variation['variation_id'];
                    $variation_attributes = $variation['attributes'];
                    ?>
                    <tr>
                        <?php 
                            // Lấy URL hình ảnh của biến thể này nếu là "Màu sắc"
                            $data_image = '';
                            $data_image = 'data-image="' . esc_url( $variation['image']['src'] ) . '"'; // Lấy URL ảnh của biến thể màu sắc
                            error_log( 'Value of $data_image: ' . print_r( $data_image, true ) ); // Log this in the server log
                        ?>
                        
                        <th class="label" <?php echo $data_image; ?> ontouchstart="updateProductImage(this); highlightActive(this);" onclick="updateProductImage(this); highlightActive(this);">
                            <?php 
                                $combined_label = array();
                                foreach ( $variation_attributes as $attribute_name => $attribute_value ) {
                                    $attribute_key = str_replace( 'attribute_', '', $attribute_name );
                                    $attribute_label = isset( $attribute_labels[ $attribute_key ] ) ? $attribute_labels[ $attribute_key ] : ucfirst( $attribute_key );
                                    $combined_label[] = esc_html( $attribute_value );
                                }
                                echo '<label>' . implode(' - ', $combined_label) . '</label>';
                            ?>
                        </th>
                        <td class="value">
                            <!-- Quantity input with +/- buttons -->
                            <div class="quantity-input">
                                <button type="button" class="minus" onclick="changeQuantity(<?php echo esc_attr( $variation_id ); ?>, -1)">-</button>
                                <input type="number" name="quantity[<?php echo esc_attr( $variation_id ); ?>]" min="0" value="0" id="quantity_<?php echo esc_attr( $variation_id ); ?>" readonly />
                                <button type="button" class="plus" onclick="changeQuantity(<?php echo esc_attr( $variation_id ); ?>, 1)">+</button>
                            </div>

                            <?php foreach ( $variation_attributes as $attribute_name => $attribute_value ) : ?>
                                <input type="hidden" name="variation[<?php echo esc_attr( $variation_id ); ?>][<?php echo esc_attr( $attribute_name ); ?>]" value="<?php echo esc_attr( $attribute_value ); ?>" />
                            <?php endforeach; ?>

                            <input type="hidden" name="variation_id[<?php echo esc_attr( $variation_id ); ?>]" value="<?php echo esc_attr( $variation_id ); ?>" />
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Add to Cart button with validation -->
        <button id="custom_add_to_cart_button" type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
    <?php endif; ?>

    <?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<script>
// Cập nhật hình ảnh khi người dùng click vào biến thể
function updateProductImage(element) {
    var imageSrc = element.getAttribute('data-image');
    var productImage = document.querySelector('.woocommerce-product-gallery__image img');

    if (productImage && imageSrc) {
        // Cập nhật các thuộc tính chính của hình ảnh
        productImage.src = imageSrc;
        productImage.setAttribute('data-src', imageSrc);
        productImage.setAttribute('data-large_image', imageSrc);
        productImage.setAttribute('srcset', imageSrc);
    }
}

function highlightActive(element) {
    // Loại bỏ class 'active' khỏi tất cả các thẻ <th>
    var allLabels = document.querySelectorAll('.label');
    allLabels.forEach(function(label) {
        label.classList.remove('active');
    });

    // Thêm class 'active' vào <th> được click
    element.classList.add('active');
}



// Hàm để tăng hoặc giảm số lượng khi nhấn các nút + hoặc -
function changeQuantity(variationId, delta) {
    var quantityInput = document.getElementById('quantity_' + variationId);
    var currentValue = parseInt(quantityInput.value);
    if (isNaN(currentValue)) currentValue = 0;
    var newValue = currentValue + delta;
    quantityInput.value = newValue > 0 ? newValue : 0;
    validateQuantities(); // Gọi validate mỗi khi thay đổi số lượng
}

// Hàm để kiểm tra xem có ít nhất một số lượng hợp lệ (> 0) không và cập nhật trạng thái nút "Add to Cart"
function validateQuantities() {
    var quantityInputs = document.querySelectorAll('input[name^="quantity"]');
    var addToCartButton = document.getElementById('custom_add_to_cart_button');
    var isAnyQuantityValid = Array.from(quantityInputs).some(function(input) {
        return parseInt(input.value) > 0;
    });

    // Bật hoặc tắt nút "Add to Cart" dựa trên tính hợp lệ của số lượng
    addToCartButton.disabled = !isAnyQuantityValid;
}

// Hàm kiểm tra số lượng hợp lệ trước khi submit form
function validateForm(event) {
    var isValid = Array.from(document.querySelectorAll('input[name^="quantity"]')).some(function(input) {
        return parseInt(input.value) > 0;
    });

    if (!isValid) {
        alert("Please select at least one variation with quantity greater than 0.");
        event.preventDefault(); // Ngăn chặn hành động submit nếu không có số lượng hợp lệ
        return false;
    }

    return true;
}

// Khởi tạo lắng nghe sự kiện khi DOM đã sẵn sàng
document.addEventListener('DOMContentLoaded', function() {
    console.log("Initialization triggered");

    // Lắng nghe sự kiện thay đổi giá trị trên các ô nhập số lượng
    var quantityInputs = document.querySelectorAll('input[name^="quantity"]');
    quantityInputs.forEach(function(input) {
        input.addEventListener('input', validateQuantities); // Gọi validate mỗi khi người dùng thay đổi giá trị trực tiếp
        input.addEventListener('change', validateQuantities); // Gọi validate khi giá trị thay đổi hoàn toàn
    });

    // Khởi tạo trạng thái nút "Add to Cart" khi tải trang
    validateQuantities();
});

</script>

<style>
.variations th.label {
    padding: 2px;
    line-height: 1;
    box-sizing: border-box;
    height: 20px;
    min-height: 30px;
    overflow: hidden; /* Ngăn nội dung tràn ra */
}
/* CSS cho nút + và - */
.quantity-input {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-left: 10px;
}

.quantity-input button {
    width: 20px;
    height: 20px;
    font-size: 12px;
    background-color: #f0f0f0;
    border: 1px solid #ccc;
    cursor: pointer;
    border-radius: 0;
    display: flex;
    justify-content: center; 
    align-items: center;
    box-sizing: border-box;
    margin: 0;
}

.quantity-input input[type="number"] {
    width: 40px;
    height: 20px;
    text-align: center;
    font-size: 12px;
    border: none;
    background-color: transparent;
    box-sizing: border-box;
    padding: 0;
    margin: 0;
    outline: none;
}

.label {
    cursor: pointer; 
    transition: background-color 0.3s ease;
}

.label.active {
    background-color: #fcdcdc; 
}
</style>
