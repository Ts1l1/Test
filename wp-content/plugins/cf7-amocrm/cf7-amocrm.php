<?php
/**
* Plugin Name: Интеграция Contact Form 7 с amoCRM
* Description: Плагин позволяет настроить передачу данных из форм плагина Contact Form 7 в amoCRM.
* Author: DevAmo
* Version: 2024.06.15
* Author URI: https://devamo.ru/wordpress
*/

add_action('init', 'cf7_save_utm_to_cookie');
add_action('admin_menu', 'cf7_amocrm_admin');
add_action( 'admin_enqueue_scripts', 'cf7_amocrm_scripts', 11 );
add_action( 'wp_print_footer_scripts', 'cf7_amocrm_footer' );
register_activation_hook( __FILE__, 'cf7_amocrm_activate' );
require_once (__DIR__.'/functions.php');
