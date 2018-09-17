<?php

namespace LA\AppPages;


class AppPage {


  /**
   * @var string
   */
  private $name;


  /**
   * @var string
   */
  private $url;


  /**
   * @var string
   */
  private $title;


  /**
   * @var string
   */
  private $template;


  /**
   * @var WP_Post
   */
  private $wp_post;


  function __construct( $name, $url, $title = 'Untitled', $template = 'page.php' ) {
    $this->name = $name;
    $this->url = $url;
    $this->setTitle( $title );
    $this->setTemplate( $template );
  }


  /**
   * @return string
   */
  function getName() {
    return $this->name;
  }


  /**
   * @return string
   */
  function getUrl() {
    return $this->url;
  }


  /**
   * @return string
   */
  function getPermalink() {
    return home_url( $this->getUrl() );
  }


  /**
   * @return string
   */
  function getEditUrl() {
    return add_query_arg( [
      'page'                 => 'la-app-pages',
      'action'               => 'edit',
      LA_APP_PAGES_QUERY_VAR => $this->getName(),
    ], admin_url( 'admin.php' ) );
  }


  /**
   * @return string
   */
  function getTemplate() {
    return $this->template;
  }


  /**
   * @return string
   */
  function getTitle() {
    return $this->title;
  }


  /**
   * @param string $title
   * @return self
   */
  function setTitle( $title ) {
    $this->title = $title;
    return $this;
  }


  /**
   * @param string $template
   * @return self
   */
  function setTemplate( $template ) {
    $this->template = $template;
    return $this;
  }


  /**
   * @return void
   */
  function register() {
    Controller::instance()->addPage( $this );
  }


  /**
   * @return \WP_Post
   */
  function asWpPost() {

    if ( is_null( $this->wp_post ) ) {
      $post = [
        'ID'             => 0,
        'post_title'     => $this->title,
        'post_name'      => sanitize_title( $this->title ),
        'post_content'   => '',
        'post_excerpt'   => '',
        'post_parent'    => 0,
        'menu_order'     => 0,
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'comment_status' => 'closed',
        'ping_status'    => 'closed',
        'comment_count'  => 0,
        'post_password'  => '',
        'to_ping'        => '',
        'pinged'         => '',
        'guid'           => home_url( $this->getUrl() ),
        'post_date'      => current_time( 'mysql' ),
        'post_date_gmt'  => current_time( 'mysql', 1 ),
        'post_author'    => 0,
        'is_app_page'    => true,
        'filter'         => 'raw',
      ];
      $this->wp_post = new \WP_Post( (object) $post );
    }

    return $this->wp_post;
  }


}
