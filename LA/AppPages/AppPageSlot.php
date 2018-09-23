<?php

namespace LA\AppPages;


class AppPageSlot {


  /**
   * @var string
   */
  public $name;


  /**
   * @var string
   */
  public $title;


  /**
   * @var callable
   */
  private $renderFunc;


  /**
   * @var callable
   */
  private $previewFunc;


  function __construct( $name, $args ) {

    $args = wp_parse_args( $args, [
      'title'   => $name,
      'render'  => '__return_empty_string',
      'preview' => false,
    ] );

    $this->name        = $name;
    $this->title       = $args[ 'title' ];
    $this->renderFunc  = $args[ 'render' ];
    $this->previewFunc = $args[ 'preview' ] ?: $args[ 'render' ];

  }


  function render( $echo = true ) {
    return $this->output( $this->renderFunc, $echo );
  }


  function preview( $echo = true ) {
    return $this->output( $this->previewFunc, $echo );
  }


  /**
   * @param callable $func
   * @param bool $echo
   * @return string|void
   */
  private function output( callable $func, $echo ) {
    if ( ! $echo ) ob_start();
    $func();
    if ( ! $echo ) return ob_get_clean();
  }


}
