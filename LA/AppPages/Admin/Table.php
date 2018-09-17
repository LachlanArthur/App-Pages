<?php

namespace LA\AppPages\Admin;

use LA\AppPages\Controller;
use LA\AppPages\AppPage;



if ( ! class_exists( 'WP_List_Table' ) ) {
  require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}


class Table extends \WP_List_Table {


  function __construct( $args = [] ) {

    parent::__construct( [
      'plural'   => __( 'App Pages', 'la_app_pages' ),
      'singular' => __( 'App Page', 'la_app_pages' ),
    ] );

  }


  function get_columns() {
    return [
      //'cb'    => '<input type="checkbox" />',
      'title' => __( 'Title', 'la_app_pages' ),
      'url'   => __( 'URL', 'la_app_pages' ),
      'name'  => __( 'Name', 'la_app_pages' ),
    ];
  }


  function get_sortable_columns() {
    return [
      'title',
      'url',
    ];
  }

  protected function get_default_primary_column_name() {
    return 'title';
  }


  function prepare_items() {

    $this->get_column_info();

    $this->items = Controller::instance()->getPages();


    $total_items = count( $this->items );

    $this->set_pagination_args( [
      'total_items' => $total_items,
      'per_page'    => $total_items,
    ] );

  }


  function no_items() {
    _e( 'No App Pages found.', 'la_app_pages' );
  }


  function get_views() {
    return [
      'all'     => 'All',
      'group_1' => 'Group 1',
    ];
  }


  public function column_title( AppPage $app_page ) {
    $title = sprintf( '<strong><a class="row-title" href="%s" aria-label="%s">%s</a></strong>',
      $app_page->getEditUrl(),
      /* translators: %s: app page name */
      esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $app_page->getTitle() ) ),
      $app_page->getTitle()
    );

    $actions = $this->row_actions( [
      'edit' => sprintf( '<a href="%s">%s</a>', $app_page->getEditUrl(), __( 'Edit' ) ),
      'view' => sprintf( '<a href="%s">%s</a>', esc_attr( $app_page->getPermalink() ), __( 'View' ) ),
    ] );

    return $title . $actions;
  }


  public function column_url( AppPage $app_page ) {
    return sprintf( '<a href="%s">%s</a>', esc_attr( $app_page->getPermalink() ), esc_html( $app_page->getUrl() ) );
  }


  public function column_name( AppPage $app_page ) {
    return sprintf( '<code>%s</code>', esc_html( $app_page->getName() ) );
  }


}
