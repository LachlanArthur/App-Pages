<?php

namespace LA\AppPages\Admin;

use LA\AppPages\Controller;



class Editor {


  /**
   * @var Settings
   */
  private $settings;


  function __construct( Settings $settings ) {

    $this->settings = $settings;

    add_action( 'admin_action_edit', [ $this, 'editAppPageSetup' ] );

    add_action( 'wp_ajax_la_app_pages_slot_preview', [ $this, 'ajaxRenderSlotPreview' ] );

    add_action( 'admin_init', [ $this, 'adminInit' ] );
    add_action( 'admin_head', [ $this, 'adminHead' ] );
    add_action( 'admin_enqueue_scripts', [ $this , 'adminScripts' ] );
  }


  function adminInit() {
    add_editor_style( LA_APP_PAGES_URL . 'assets/css/mce-content.css' );
  }


  function adminHead() {
    if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) return;

    if ( 'true' != get_user_option( 'rich_editing' ) ) return;

    if ( ! $this->settings->getCurrentAppPage() ) return;

    add_filter( 'mce_external_plugins', [ $this ,'registerMcePlugin' ] );
    add_filter( 'mce_buttons_2',        [ $this, 'registerMceButton' ] );
  }


  function registerMcePlugin( $plugins ) {
    $plugins[ 'app_pages_plugin' ] = LA_APP_PAGES_URL . 'assets/js/mce-plugin.js';
    return $plugins;
  }


  function registerMceButton( $buttons ) {
    $buttons[] = 'app_pages_slot_button';
    return $buttons;
  }


  function editAppPageSetup() {
    $app_page = $this->settings->getCurrentAppPage();

    if ( $app_page ) {
      global $post;

      $post = $app_page->asWpPost();
    }
  }


  function adminScripts() {
    $app_page = $this->settings->getCurrentAppPage();

    if ( ! $app_page ) return;

    wp_enqueue_style( 'app-pages-slot-mce', LA_APP_PAGES_URL . 'assets/css/mce-plugin.css' );

    wp_enqueue_script( 'app-pages-slot-mce-view', LA_APP_PAGES_URL . 'assets/js/mce-view.js', [ 'shortcode', 'wp-util', 'jquery', 'mce-view' ], false, true );

    $slotOptions = [];
    foreach ( $app_page->getSlotTitles() as $slotName => $slotTitle ) {
      $slotOptions[] = [
        'text' => $slotName,
        'value' => $slotTitle,
      ];
    }

    $jsConfig = [
      'appPage'       => $app_page->getName(),
      'slotShortcode' => LA_APP_PAGES_SLOT_SHORTCODE,
      'slots'         => $slotOptions,
    ];
    wp_localize_script( 'app-pages-slot-mce-view', 'AppPagesConfig', $jsConfig );
  }


  function render() {
    global $post, $is_IE;

    $app_page = $this->settings->getCurrentAppPage();

    if ( ! $app_page ) {
      wp_redirect( add_query_arg( 'page', 'la-app-pages', admin_url( 'admin.php' ) ) );
    }

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


  function ajaxRenderSlotPreview() {
    global $wp_styles;
    wp_styles();

    $app_page = Controller::instance()->getAppPage( $_REQUEST[ 'app_page' ] ?? '' );

    if ( empty( $app_page ) ) wp_send_json_error( [ 'message' => 'Invalid page' ] );

    // Load frontend styles in preview
    ob_start();
    $wp_styles->reset();
    do_action( 'wp_enqueue_scripts' );
    $wp_styles->do_items();
    $wp_styles->do_footer_items();
    $styles = ob_get_clean();

    try {
      $preview = $app_page->renderSlotPreview( $_REQUEST );
    } catch ( \Exception $e ) {
      wp_send_json_error( [ 'message' => $e->getMessage() ] );
    }

    wp_send_json_success( [
      'head' => $styles,
      'body' => $preview,
    ] );
  }


}
