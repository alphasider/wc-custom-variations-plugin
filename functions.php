<?php

  /**
   * Variation layout
   * Source: WooCommerce core (woocommerce/include/wc-template-functions.php)
   */
  if (!function_exists('parachute_variation_attributes')) {

    /**
     * Output a list of variation attributes for use in the cart forms.
     *
     * @param array $args Arguments.
     * @since 2.4.0
     */
    function parachute_variation_attributes($args = array()) {

      $args = wp_parse_args(
        apply_filters('woocommerce_template_single_add_to_cart', $args),
        array(
          'options' => false,
          'attribute' => false,
          'product' => false,
          'selected' => false,
          'name' => '',
          'id' => '',
          'class' => '',
          'show_option_none' => __('Choose an option', 'woocommerce'),
        )
      );

      // Get selected value.
      if (false === $args['selected'] && $args['attribute'] && $args['product'] instanceof WC_Product) {
        $selected_key = 'attribute_' . sanitize_title($args['attribute']);
        $args['selected'] = isset($_REQUEST[$selected_key]) ? wc_clean(wp_unslash($_REQUEST[$selected_key])) : $args['product']->get_variation_default_attribute($args['attribute']); // WPCS: input var ok, CSRF ok, sanitization ok.
      }

      $options = $args['options'];
      $product = $args['product'];
      $attribute = $args['attribute'];
      $name = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title($attribute);
      $id = $args['id'] ? $args['id'] : sanitize_title($attribute);
      $class = $args['class'];

      if (empty($options) && !empty($product) && !empty($attribute)) {
        $attributes = $product->get_variation_attributes();
        $options = $attributes[$attribute];
      }

      $html = '<div class="nudle-right">';

      if (!empty($options)) {
        if ($product && taxonomy_exists($attribute)) {
          // Get terms if this is a taxonomy - ordered. We need the names too.
          $terms = wc_get_product_terms(
            $product->get_id(),
            $attribute,
            array(
              'fields' => 'all',
            )
          );

          // Bed size
          if ($terms[0]->taxonomy == 'pa_bed-size') {
            $html .= '<h3 class="h3 up">Choose your bed size</h3>';
            $html .= '<div class="bed_attr">';
            $html .= '  <div class="bed-row last-attr">';

            foreach ($terms as $term) {
              $html .= '    <div class="bed-type _size">';
              $html .= '      <div class="bed-image">';
              $html .= '        <img src="http://parachute.1devserver.co.uk/wp-content/uploads/2020/07/Parachute-Recovered-14.svg" alt="" class="image-4">';
              $html .= '      </div>';
              $html .= '      <div class="text-block-2" data-atr="' . esc_html(apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute, $product)) . '">';
              $html .= esc_html(apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute, $product));
              $html .= '      </div>';
              $html .= '    </div>';
            }

            $html .= '  </div>';
            $html .= '</div>';

            // Add another bed
            $html .= '<div data-qty="1" class="bed-row add">';
            $html .= '  <div class="text-block-2 green">+ Add another bed</div>';
            $html .= '</div>';
          }

          // Parachute frequency
          if ($terms[0]->taxonomy == 'pa_parachute-frequency') {
            $html .= '<h3 class="h3 up">Parachute frequency</h3>';
            $html .= '<p class="paragraph">How often would you like to change your bed linen each month?</p>';
            $html .= '<div class="bed-row _2">';
            foreach ($terms as $term) {
              $html .= '  <div class="bed-type _month">';
              $html .= '    <div class="bed-image f">';
              $html .= '      <h1 class="but" data-atr="' . esc_html(apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute, $product)) . '">';
              $html .= esc_html(apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute, $product));
              $html .= '      </h1>';
              $html .= '      <div class="text-block-3">PER&nbsp;MONTH</div>';
              $html .= '    </div>';
              $html .= '  </div>';
            }
            $html .= '</div>';
          }

          // Delivery
          if ($terms[0]->taxonomy == 'pa_delivery') {
            $html .= '<h3 class="h3 up">Delivery</h3>';
            $html .= '<p class="paragraph">We deliver your bundle once per month. Deliveries are always flexible. Skip a month, alter your change over day, or cancel anytime. <br><br>‍<strong>Pick your delivery and change over day.</strong></p>';
            $html .= '<div class="date">';
            foreach ($terms as $term) {
              $html .= '  <div class="day">';
              $html .= '    <div class="text-block-3" data-atr="' . esc_html(apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute, $product)) . '">';
              $html .= esc_html(apply_filters('woocommerce_variation_option_name', $term->name, $term, $attribute, $product));
              $html .= '    </div>';
              $html .= '  </div>';
            }
            $html .= '</div>';
          }

        }
      }

      $html .= '</div>';

      echo apply_filters('woocommerce_template_single_add_to_cart', $html, $args); // WPCS: XSS ok.
    }
  }

  add_shortcode('add_to_cart_btn', 'parachute_add_to_cart_shortcode');

  /**
   * Add to cart btn shortcode
   * @param $atts
   * @return string
   */
  function parachute_add_to_cart_shortcode($atts) {
    $attribute = shortcode_atts([
      'label' => 'Add to cart',
      'product_id' => 0
    ], $atts);

    return '<a href="#" class="add-prods-to-cart button w-button" data-prod-id="' . $attribute['product_id'] . '">' . $attribute['label'] . '</a>';
  }

  /**
   * AJAX
   */

  add_action('wp_enqueue_scripts', 'parachute_data', 99);

  /**
   * Add ajax url to the DOM
   */
  function parachute_data() {
    wp_localize_script(
      'add',
      'parachute_obj',
      [
        'ajax_url' => admin_url('admin-ajax.php')
      ]
    );
  }

  add_action('wp_ajax_parachute_action', 'parachute_action_callback');
  add_action('wp_ajax_nopriv_parachute_action', 'parachute_action_callback');

  /**
   * Main function
   */
  function parachute_action_callback() {
    $product_id = intval($_POST['prod_id']);
    $response_data = parachute_get_requested_data(); // Requested data
    $product_variations = parachute_get_product_variations($product_id); // Get available variations by product ID
    $selected_variations = parachute_generate_selected_variations_attributes($response_data); // Array of selected by customer variations
    $variation_id = parachute_get_selected_variation_id($product_variations, $selected_variations); // Selected variation ID

    try {
      parachute_add_to_cart($product_id, $variation_id, $selected_variations);
    } catch (Exception $e) {
      echo ('Error in parachute_add_to_cart_function: ' . $e);
    }

    wp_die();
  }

  /**
   * HELPER FUNCTIONS
   */

  /**
   * Add to cart single/multiple product if they exists
   * @param int $product_id
   * @param array $variations_ids
   * @param array $variation_args
   * @throws Exception
   */
  function parachute_add_to_cart($product_id, array $variations_ids = [], array $variation_args = []) {
    foreach ($variations_ids as $current_index => $variation_id) {
      if (!$variation_id == 0) {
        WC()->cart->add_to_cart($product_id[$current_index], 1, $variation_id, $variation_args[$current_index]);
      }
    }
  }

  /**
   * Create a brand new array of attributes for each of sizes
   * @param array $response_data
   * @return array
   */
  function parachute_generate_selected_variations_attributes(array $response_data =[]) {

    $all_variations = [];
    $formatted_array = parachute_replace_symbols_with_dash($response_data);

    // Extract all the sizes if there is more than one
    $bed_sizes = explode('%%', $formatted_array['bed_size']);

    foreach ($bed_sizes as $bed_size) {
      $all_variations[] = array_replace([], $formatted_array, ['bed_size' => $bed_size]);
    }
    return $all_variations;
  }

  /**
   * Get selected variation ID if there is one or more
   * @param array $product_variations
   * @param array $all_variations
   * @return array
   */
  function parachute_get_selected_variation_id(array $product_variations, array $all_variations) {
    $variations_array = [];
    foreach ($all_variations as $current_variation) { // Loop through all selected variations
      foreach ($product_variations as $variation) { // Loop through all possible variations
        $matched_variation = array_diff($variation['attributes'], $current_variation);
        if (empty($matched_variation)) {
          $variations_array[] = $variation['variation_id'];
        }
      }
    }
    return $variations_array;
  }

  /**
   * Get requested data
   */
  function parachute_get_requested_data() {
    $bed_size = strtolower($_POST['pa_size']);
    $frequency = strtolower($_POST['pa_month']);
    $delivery = strtolower($_POST['pa_delivery']);

    $response_data = [];
    $response_data['bed_size'] = $bed_size;
    $response_data['frequency'] = $frequency;
    $response_data['delivery'] = $delivery;

    return $response_data;
  }

  /**
   * Get product variations by product ID
   * @param int $product_id
   * @return array
   */
  function parachute_get_product_variations(int $product_id) {
    $product = wc_get_product($product_id);
    return $product->get_available_variations();
  }

  /**
   * Replace slash & whitespace in values with dash character
   * @param $data
   * @return array
   */
  function parachute_replace_symbols_with_dash($data) {
    $cleaned_data = [];
    foreach ($data as $key => $value) {
      $whitespace_free = str_replace(' ', '-', $value);
      $slash_free = str_replace('/', '-', $whitespace_free);
      $cleaned_data[$key] = $slash_free;
    }
    return $cleaned_data;
  }

