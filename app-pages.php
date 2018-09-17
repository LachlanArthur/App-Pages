<?php
/**
 * Plugin Name: App Pages
 * Description:
 * Author: Lachlan Arthur
 * Version: 1.0
 * Author URI: http://lach.la
 *
 * Virtual Page code forked from https://gist.github.com/gmazzap/1efe17a8cb573e19c086
 */

namespace LA\AppPages;


define( 'LA_APP_PAGES_DIR', trailingslashit( __DIR__ ) );

if ( ! defined( 'LA_APP_PAGES_QUERY_VAR' ) ) define( 'LA_APP_PAGES_QUERY_VAR', 'app-page' );
if ( ! defined( 'LA_APP_PAGES_BODY_CLASS' ) ) define( 'LA_APP_PAGES_BODY_CLASS', 'app-page' );


autoload_namespace_dir( 'LA', __DIR__ );


Controller::instance()->init();

if ( is_admin() ) {
  new Admin\Settings();
}


function autoload_namespace_dir( $namespace, $dir, $strip_namespace = false ) {
  spl_autoload_register( function ( $class_name ) use ( $namespace, $dir, $strip_namespace ) {

    if ( strpos( $class_name, $namespace ) !== 0 ) {
      return;
    }

    $path = str_replace( '\\', '/', $class_name );

    if ( $strip_namespace ) {
      $path = substr( $path, strlen( $namespace ) + 1 );
    }

    require_once trailingslashit( $dir ) . $path . '.php';
  } );
}-



// Test page
add_action( 'init', function() {
  $test = new AppPage( 'test', '/test/', 'Test App Page' );
  $test->register();
} );
