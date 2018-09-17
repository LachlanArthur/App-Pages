<?php

namespace LA\AppPages\Admin;

use LA\AppPages\AppPage;
use LA\AppPages\Controller;


class NavMenus {


  function __construct() {
    $this->init();

    add_filter( 'wp_setup_nav_menu_item',  [ $this, 'navMenuItemSetup' ], 10, 1 );
    add_action( 'wp_update_nav_menu_item', [ $this, 'navMenuItemUpdate' ], 10, 3 );
  }


  function init() {
    add_meta_box( 'add-app-page', 'App Pages', [$this, 'metaBox'], 'nav-menus', 'side', 'default', [] );
  }


  /**
   * Render the AppPage nav menu meta box
   *
   * @param mixed $object
   * @param array $metabox
   * @return void
   */
  function metaBox( $object, $metabox ) {
    global $nav_menu_selected_id;

    $app_pages = Controller::instance()->getPages();

    $args = [
      'child_of'     => 0,
      'exclude'      => '',
      'hide_empty'   => false,
      'hierarchical' => 1,
      'include'      => '',
      'number'       => count( $app_pages ),
      'offset'       => 0,
      'order'        => 'ASC',
      'orderby'      => 'name',
      'pad_counts'   => false,
      'walker'       => new \Walker_Nav_Menu_Checklist(),
    ];

    ?>
    <div id="app-page">
      <div class="tabs-panel-active">
        <ul id="apppagechecklist" data-wp-lists="list:app-page" class="categorychecklist form-no-clear">
          <?=walk_nav_menu_tree( array_map( 'wp_setup_nav_menu_item', $app_pages ), 0, (object) $args );?>
        </ul>
      </div>
    </div>

    <p class="button-controls wp-clearfix">
      <span class="add-to-menu">
        <input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id );?>
          class="button submit-add-to-menu right"
          value="<?php esc_attr_e( 'Add to Menu' );?>"
          name="add-app-page-menu-item"
          id="submit-app-page" />
        <span class="spinner"></span>
      </span>
    </p>
    <?php
  }


  /**
   * Convert an AppPage into a menu item object
   *
   * @param mixed $menu_item
   * @return object
   */
  function navMenuItemSetup( $menu_item ) {

    if ( $menu_item instanceof AppPage ) {
      return (object) [
        'post_type'        => 'nav_menu_item',
        'db_id'            => 0,
        'object_id'        => -1,
        'object'           => $menu_item->getName(),
        'menu_item_parent' => 0,
        'target'           => null,
        'attr_title'       => null,
        'classes'          => [],
        'xfn'              => null,
        'url'              => $menu_item->getPermalink(),
        'type'             => 'app-page',
        'type_label'       => 'App Page',
        'title'            => $menu_item->getTitle(),
      ];
    }

    if ( $menu_item->post_type === 'nav_menu_item' && $menu_item->type === 'app-page' ) {
      $menu_item->type_label = sprintf( '%s [%s]', esc_html( __( 'App Page', 'la_app_pages' ) ), esc_html( $menu_item->object ) );
    }

    return $menu_item;
  }


  function navMenuItemUpdate( $menu_id, $menu_item_db_id, $args ) {
    if ( $args[ 'menu-item-type' ] === 'app-page' ) {

      $app_page = Controller::instance()->getAppPage( $args[ 'menu-item-object' ] );
      if ( $app_page ) {

        update_post_meta( $menu_item_db_id, '_menu_item_url', esc_url_raw( $app_page->getPermalink() ) );

      }

    }
  }


}
