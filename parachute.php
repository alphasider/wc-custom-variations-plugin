<?php
  /*
  Plugin Name: Parachute
  Description: Changes variable products view in single product page
  Author: Rustam Ergashev
  */

  if (!defined('ABSPATH')) {
    exit;
  }

  /**
   * Load styles and js
   */
  function load_scripts() {
    wp_enqueue_style('add', plugins_url('assets/css/add.css', __FILE__), [], '1.0');
    wp_enqueue_style('main', plugins_url('assets/css/main.css', __FILE__), [], '1.0');
    wp_enqueue_script('jquery', plugins_url('vendor/js/jquery.min.js', __FILE__), [], '3.3.1', true);
    wp_enqueue_script('add', plugins_url('assets/js/add.js', __FILE__), ['jquery'], '3.3.1', true);
  }

  add_action('wp_enqueue_scripts', 'load_scripts');

