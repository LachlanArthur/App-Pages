<?php

namespace LA\AppPages\Admin;

use LA\AppPages\Controller;
use LA\AppPages\AppPage;


class Settings {


  static $option_name = 'la-app-pages';


  private $options;


  /**
   * @var Table
   */
  private $table;


  public function __construct() {
    add_action( 'admin_init', [ $this, 'init' ] );
    add_action( 'admin_menu', [ $this, 'add_pages' ] );
    add_action( 'admin_init', [ $this, 'options_page_1_init' ] );

    add_action( 'admin_action_edit', [ $this, 'editAppPageSetup' ] );
  }


  function init() {
    $this->options = self::get_options();
    $this->table   = new Table();
  }


  function editAppPageSetup() {
    global $post;

    $app_page_url = $_GET[ 'app-page' ] ?? '';

    if ( $app_page_url ) {
      $app_page = Controller::instance()->getAppPage( $app_page_url );

      if ( $app_page ) {
        $post = $app_page->asWpPost();
      }
    }
  }


  public function add_pages() {

    add_menu_page(
      'App Pages', // Page title
      'App Pages', // Menu Title
      'manage_options', // Capability to access page
      'la-app-pages', // Page ID (used in URL)
      [ $this, 'render_page_main' ], // Render callback
      'dashicons-index-card', // Icon
      51 // Position (below comments)
    );

    // Duplicate top-level item to change the menu title
    add_submenu_page(
      'la-app-pages', // Parent slug
      'App Pages', // Page title
      'All App Pages', // Menu Title
      'manage_options', // Capability to access page
      'la-app-pages', // Page ID (used in URL)
      [ $this, 'render_page_main' ] // Render callback
    );

    add_submenu_page(
      'la-app-pages', // Parent slug
      'App Pages Settings', // Page title
      'Settings', // Menu Title
      'manage_options', // Capability to access page
      'la-app-pages-options', // Page ID (used in URL)
      [ $this, 'render_page_options' ] // Render callback
    );

  }


  public static function get_options() {
    $defaults = [
      'foo' => '',
      'bar' => '',
    ];

    $values = get_option( self::$option_name );

    return wp_parse_args( $values, $defaults );
  }


  public function render_page_main() {
    switch ( $_GET[ 'action' ] ?? '' ) {

      default:
        $this->render_page_table();
        break;

      case 'edit':
        $app_page_url = $_GET[ LA_APP_PAGES_QUERY_VAR ] ?? '';

        if ( $app_page_url ) {
          $app_page = Controller::instance()->getAppPage( $app_page_url );
          if ( $app_page ) {
            $this->render_page_edit( $app_page );
            break;
          }
        }

        wp_redirect( add_query_arg( 'page', 'la-app-pages', admin_url( 'admin.php' ) ) );
        break;

    }
  }


  public function render_page_table() {
    $this->table->prepare_items();
    ?>
    <div class="wrap">
      <h2>App Pages</h2>
      <?php //$this->table->views() ?>
      <form method="post">
        <?php
        //$this->table->search_box( 'Search App Pages', 'app-page-search' );
        $this->table->display();
        ?>
      </form>
    </div>
    <?php
  }


  public function render_page_edit( AppPage $app_page ) {
    global $post, $is_IE;

    $post      = $app_page->asWpPost();
    $permalink = $app_page->getPermalink();

    ?>
    <div class="wrap">
      <h1 class="wp-heading-inline"><?php echo esc_html( $app_page->getTitle() ) ?></h1>
      <div id="edit-slug-box">
        <strong><?= __( 'Permalink:' ) ?></strong>
        <span id="sample-permalink"><a href="<?= esc_attr( $permalink ) ?>"><?= esc_html( $permalink ) ?></a></span>
      </div>
      <form method="post">
        <?php wp_editor( $post->post_content, 'content', [
          '_content_editor_dfw' => true,
          'drag_drop_upload'    => true,
          'tabfocus_elements'   => 'content-html,save-post',
          'editor_height'       => 300,
          'tinymce'             => [
            'resize'                  => false,
            'wp_autoresize_on'        => ( get_user_setting( 'editor_expand', 'on' ) === 'on' ),
            'add_unload_trigger'      => false,
            'wp_keep_scroll_position' => ! $is_IE,
          ],
        ] ); ?>
      </form>
    </div>
    <?php
  }


  public function render_page_options() {
    ?>
    <div class="wrap">
      <h2>App Pages Settings</h2>
      <form method="post">
        <?php
        settings_fields( self::$option_name );
        do_settings_sections( 'la-app-pages-options' );
        submit_button();
        ?>
      </form>
    </div>
    <?php
  }


  public function options_page_1_init() {
    register_setting(
      self::$option_name, // Options group name (use the option name for simplicity)
      self::$option_name, // Post param name
      [ $this, 'sanitize' ] // Value sanitisation
    );

    add_settings_section(
      'section_1', // Section ID
      'Section 1', // Title
      [ $this, 'section_info_1' ], // Section info callback
      'la-app-pages-options' // Page ID
    );

    add_settings_field(
      'foo', // Field ID
      'Foo', // Label
      [ $this, 'render_field_foo' ], // Render callback
      'la-app-pages-options', // Page ID
      'section_1', // Section ID
      [ 'id' => 'foo' ] // Extra params for render callback
    );

    add_settings_section(
      'section_2', // Section ID
      'Section 2', // Title
      [ $this, 'section_info_2' ], // Section info callback
      'la-app-pages-options' // Page ID
    );

    add_settings_field(
      'bar', // Field ID
      'Bar', // Label
      [ $this, 'render_field_bar' ], // Render callback
      'la-app-pages-options', // Page ID
      'section_2', // Section ID
      [ 'id' => 'bar' ] // Extra params for render callback
    );
  }


  public function sanitize( $input ) {
    $sanitised = [];

    if ( isset( $input['foo'] ) ) {
      $sanitised['foo'] = sanitize_text_field( $input['foo'] );
    }

    if ( isset( $input['bar'] ) ) {
      $sanitised['bar'] = absint( $input['bar'] );
    }

    return $sanitised;
  }


  public function section_info_1() {
    ?>
    Section 1 info. Can use <em>HTML</em>.
    <?php
  }


  public function section_info_2() {
    ?>
    Section 2 info.
    <?php
  }


  public function render_field_foo( $args ) {
    $value = $this->options[$args['id']];
    printf(
      '<input type="text" id="%2$s" name="%1$s[%2$s]" value="%3$s" />',
      self::$option_name,
      $args['id'],
      isset( $value ) ? esc_attr( $value ) : ''
    );
  }


  public function render_field_bar( $args ) {
    $value = $this->options[$args['id']];
    printf(
      '<input type="checkbox" id="%2$s" name="%1$s[%2$s]" value="1" %3$s />',
      self::$option_name,
      $args['id'],
      checked( 1, $value, false )
    );
  }


}
