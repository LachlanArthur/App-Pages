<?php

namespace LA\AppPages;


class AppPageSlot {


  /**
   * @var string
   */
  public $name;


  /**
   * @var callable
   */
  private $renderFunc;


  /**
   * @var callable
   */
  private $previewFunc;


  function __construct( $name, $args ) {

    $this->name = $name;

    $args = wp_parse_args( $args, [
      'render'  => '__return_empty_string',
      'preview' => false,
    ] );

    $this->renderFunc  = $args[ 'render' ];
    $this->previewFunc = $args[ 'preview' ] ?: $args[ 'render' ];

  }


  function render() {
    return ($this->renderFunc)();
  }


  function preview() {
    return ($this->previewFunc)();
  }


}
