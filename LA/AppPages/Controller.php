<?php

namespace LA\AppPages;


class Controller {


  /**
   * @var \SplObjectStorage
   */
  private $pages;


  /**
   * @var LA\AppPages\AppPage
   */
  private $matched;


  function __construct() {
    $this->pages = new \SplObjectStorage;
  }


  /**
   * @return self
   */
  static function instance() {
    static $instance = null;

    if ( $instance === null ) {
      $instance = new self();
    }

    return $instance;
  }


  /**
   * @return void
   */
  function init() {
    add_filter( 'query_vars',     [ $this, 'registerQueryVars' ], 10, 1 );
    add_action( 'parse_request',  [ $this, 'overrideWpMain' ], PHP_INT_MAX, 1 );
    add_filter( 'the_permalink',  [ $this, 'permalink' ], 10, 1 );
    add_filter( 'body_class',     [ $this, 'bodyClass' ], 10, 1 );
    add_filter( 'edit_post_link', [ $this, 'filterEditLink' ], 10, 3 );
  }


  /**
   * @return AppPage[]
   */
  function getPages() {
    $pages = [];

    $this->pages->rewind();
    while ( $this->pages->valid() ) {
      $pages[] = $this->pages->current();
      $this->pages->next();
    }

    return $pages;
  }


  /**
   * @param string[] $vars
   * @return void
   */
  function registerQueryVars( $vars ) {
    $vars[] = LA_APP_PAGES_QUERY_VAR;
    return $vars;
  }


  /**
   * @param AppPage $page
   * @return void
   */
  function addPage( AppPage $page ) {
    $this->pages->attach( $page );

    $url_regex  = sprintf( '%s/?$', preg_quote( trim( $page->getUrl(), '/' ) ) );
    $url_params = http_build_query( [
      LA_APP_PAGES_QUERY_VAR => $page->getUrl(),
    ] );

    add_rewrite_rule( $url_regex, 'index.php?' . $url_params, 'top' );

    return $page;
  }


  /**
   * @param \WP $wp
   * @return void
   */
  function overrideWpMain( \WP $wp ) {

    if ( $this->checkRequest( $wp ) ) {
      $this->setupQuery( $wp );

      $wp->send_headers();
      $wp->register_globals();
      do_action_ref_array( 'wp', [ &$wp ] );

      $this->loadTemplate( $this->matched );
      exit;
    }

  }


  /**
   * @param string $url
   * @return AppPage
   */
  function getAppPage( $url ) {
    $this->pages->rewind();
    while ( $this->pages->valid() ) {

      if ( $this->pages->current()->getUrl() === $url ) {
        return $this->pages->current();
      }

      $this->pages->next();
    }

    return null;
  }


  /**
   * @param \WP $wp
   * @return void
   */
  private function checkRequest( \WP $wp ) {

    $app_page_url = $wp->query_vars['app-page'] ?? '';
    if ( !$app_page_url ) {
      return;
    }

    $app_page = $this->getAppPage( $app_page_url );

    if ( $app_page ) {
      $this->matched = $app_page;
      return true;
    }

    return false;
  }


  /**
   * @param \WP $wp
   * @return void
   */
  private function setupQuery( \WP $wp ) {
    global $wp_query;

    $wp_query->init();

    $posts = (array) apply_filters( 'the_posts', [$this->matched->asWpPost()], $wp_query );
    $post  = $posts[0];

    $wp_query->query_vars     = $wp->query_vars;
    $wp_query->query          = $wp->query_vars;
    $wp_query->is_page        = true;
    $wp_query->is_singular    = true;
    $wp_query->is_home        = false;
    $wp_query->found_posts    = 1;
    $wp_query->post_count     = 1;
    $wp_query->max_num_pages  = 1;
    $wp_query->posts          = $posts;
    $wp_query->post           = $post;
    $wp_query->queried_object = $post;
    $wp_query->app_page       = $post instanceof \WP_Post && isset( $post->is_app_page ) ? $this->matched : NULL;

    $wp->app_page = $this->matched;
  }


  /**
   * @param AppPage $page
   * @return void
   */
  public function loadTemplate( AppPage $page ) {

    do_action( 'template_redirect' );

    $templates = wp_parse_args(
      ['page.php', 'index.php'],
      (array) $page->getTemplate()
    );

    $template = locate_template( array_filter( $templates ) );

    $filtered = apply_filters( 'template_include',
      apply_filters( 'app_page_template', $template )
    );

    if ( empty( $filtered ) || file_exists( $filtered ) ) {
      $template = $filtered;
    }

    if ( !empty( $template ) && file_exists( $template ) ) {
      require_once $template;
    }

  }


  /**
   * @param string $permalink
   * @return string
   */
  function permalink( $permalink ) {

    if ( $this->isAppPageQuery() ) {
    global $wp_query;

      $permalink = home_url( $wp_query->app_page->getUrl() );
    }

    return $permalink;
  }


  /**
   * @param string[] $classes
   * @return string[]
   */
  function bodyClass( $classes ) {

    if ( $this->isAppPageQuery() ) {
      global $post;

      $classes[] = LA_APP_PAGES_BODY_CLASS;
      $classes[] = LA_APP_PAGES_BODY_CLASS . '-' . sanitize_html_class( $post->post_name );
    }

    return $classes;
  }


  /**
   * @param \WP_Query $_wp_query
   * @return boolean
   */
  function isAppPageQuery( \WP_Query $_wp_query = null ) {

    if ( $_wp_query === null ) {
      global $wp_query;
      $_wp_query = $wp_query;
    }

    if (
      $_wp_query->is_page
      && isset( $_wp_query->app_page )
      && $_wp_query->app_page instanceof AppPage
    ) {
      $post = $_wp_query->get_queried_object();

      if (
        isset( $post->is_app_page )
        && $post->is_app_page
      ) {
        return true;
      }

    }

    return false;
  }


  /**
   * @param string $link
   * @param int $post_id
   * @param string $text
   * @return string
   */
  function filterEditLink( $link, $post_id, $text ) {

    if ( $this->isAppPageQuery() ) {
    global $wp_query;

      $edit_url = $wp_query->app_page->getEditUrl();
      $link = preg_replace( '/href="[^"]*"/', sprintf( 'href="%s"', esc_attr( $edit_url ) ), $link );
    }

    return $link;
  }


}
