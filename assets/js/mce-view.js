/* global wp, tinyMCE, ajaxurl, AppPagesConfig */
( function ( $ ) {
  var config = AppPagesConfig;
  wp.mce = wp.mce || {};
  wp.mce[ config.slotShortcode ] = {
    shortcode_data: {},
    getContent: function () {
      var plugin = this;
      var options = this.shortcode.attrs.named;
      var placeholderClass = 'app-pages-slot-placeholder-' + Math.random().toString( 36 ).substr( 2 );
      var placeholder = '<div class="' + placeholderClass + '">Loading...</div>';
      $.get( {
        url: ajaxurl,
        method: 'get',
        data: $.extend( {
          action: 'la_app_pages_slot_preview',
          app_page: config.appPage,
        }, options ),
        success: function ( data ) {
          plugin.setIframes( data.head, data.body );
        },
        error: function () {
          plugin.setContent( 'Error loading App Page slot.' );
        }
      } );
      return placeholder;
    },
    edit: function ( data ) {
      var shortcode_data = wp.shortcode.next( config.slotShortcode, data );
      var values = shortcode_data.shortcode.attrs.named;
      wp.mce[ config.slotShortcode ].popupwindow( tinyMCE.activeEditor, values );
    },
    popupwindow: function ( editor, values, onsubmit_callback ) {
      values = values || {};
      if ( typeof onsubmit_callback !== 'function' ) {
        onsubmit_callback = function ( e ) {
          var args = {
            tag: config.slotShortcode,
            type: 'single',
            attrs: {
              slot: e.data.slot,
            },
          };
          editor.insertContent( wp.shortcode.string( args ) );
        };
      }
      editor.windowManager.open( {
        title: 'App Page Slot',
        body: [
          {
            type: 'listbox',
            name: 'slot',
            label: 'Slot',
            value: values.slot,
            values: config.slots,
          },
        ],
        onsubmit: onsubmit_callback,
      } );
    }
  };
  if ( wp.mce.views ) {
    wp.mce.views.register( config.slotShortcode, wp.mce[ config.slotShortcode ] );
  }
} )( jQuery );
