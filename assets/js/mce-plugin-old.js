/* global wp, tinyMCE, ajaxurl, AppPagesConfig */
( function ( views ) {
  let config = AppPagesConfig;

  tinyMCE.PluginManager.add( 'app_pages_plugin', function AppPagesMcePlugin( editor ) {
    editor.addButton( 'app_pages_slot_button', {
      icon: 'app-pages-slot',
      tooltip: 'Insert App Page Slot',
      onclick: function () {
        console.log( this, arguments );
        //views[ config.slotShortcode ].popupwindow( editor );
      },
    } );
  } );
} )( window.wp.mce.views );
