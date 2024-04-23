<?php
/**
 * Plugin Name: JS-Add-Product-To-Cart
 * Plugin URI: 
 * Description: Plugin to add a custom product to the cart
 * Version: 1.0
 * Author: p1kus
 * Author URI: https://github.com/P1kus
 * License: Open Source
 */
function add_product_to_cart() {
  // Get the product data from the AJAX request
  $product_data = json_decode(stripslashes($_POST['product']), true);
  $category_name = "blaty";
  $category = get_term_by('slug', $category_name, 'product_cat');
  $category_id = $category->term_id;
  // Create a new product
  $product = new WC_Product();
  $product->set_name($product_data['name']);
  $product->set_regular_price($product_data['price']);  // Set the product price
  $product->set_status("publish");  // Set the product status
  $product->set_category_ids(array($category_id));  // Set the product category
  $product->set_catalog_visibility('visible');  // Set catalog visibility
  $product->set_description('Product description');  // Set product description
  $product->set_weight($product_data['weight']);  // Set product weight

  // Save the product and get its ID
  $product_id = $product->save();

  // Set the product image
  $product_image_id = media_sideload_image($product_data['image_url'], $product_id, $product_data['name'], 'id');
  if (!is_wp_error($product_image_id)) {
    set_post_thumbnail($product_id, $product_image_id);
    // Save the product again to store the image
    $product->save();
  } else {
    wp_send_json_error('Failed to set product image');
  }
}
add_action('wp_ajax_add_product_to_cart', 'add_product_to_cart');
add_action('wp_ajax_nopriv_add_product_to_cart', 'add_product_to_cart');
?>