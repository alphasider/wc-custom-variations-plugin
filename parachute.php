<?php
  /**
   * Plugin Name: Parachute
   * Description: Changes variable products view in single product page
   * Author: Rustam Ergashev
   * Plugin URI: https://github.com/alphasider/wc-custom-variations-plugin
   */

  if (!defined('ABSPATH')) {
    exit;
  }

  final class Parachute {

    /**
     * Plugin setup
     */
    public function __construct() {
      add_action('init', [$this, 'parachute_setup'], -1);
      require_once('functions.php');
    }

    /**
     * Setup all the things
     */
    public function parachute_setup() {
      add_action('wp_enqueue_scripts', [$this, 'parachute_css'], 999);
      add_action('wp_enqueue_scripts', [$this, 'parachute_js']);
      add_filter('wc_get_template', [$this, 'parachute_wc_get_template'], 11, 5);
    }

    /**
     * Enqueue the CSS
     */
    public function parachute_css() {
      wp_enqueue_style('add', plugins_url('assets/css/add.css', __FILE__), [], '1.0');
      wp_enqueue_style('main', plugins_url('assets/css/main.css', __FILE__), [], '1.0');
    }

    /**
     * Enqueue th JS
     */
    public function parachute_js() {
      wp_enqueue_script('jquery', plugins_url('vendor/js/jquery.min.js', __FILE__), [], '3.3.1', true);
      wp_enqueue_script('add', plugins_url('assets/js/add.js', __FILE__), ['jquery'], '3.3.1', true);
    }

    /**
     * Look in this plugin for WooCommerce template overrides
     *
     * @param $located
     * @param $template_name
     * @param $args
     * @param $template_path
     * @param $default_path
     * @return string
     */
    public function parachute_wc_get_template($located, $template_name, $args, $template_path, $default_path) {
      $plugin_template_path = untrailingslashit(plugin_dir_path(__FILE__)) . '/templates/woocommerce/' . $template_name;

      if (file_exists($plugin_template_path)) {
        $located = $plugin_template_path;
      }

      return $located;
    }

  } // End class

  /**
   * The 'main' function
   */
  function parachute_main() {
    new Parachute();
  }

  /**
   * Plugin initialization
   */
  add_action('plugins_loaded', 'parachute_main');