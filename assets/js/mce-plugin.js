/* global wp, tinyMCE, ajaxurl, AppPagesConfig */
( function () {
  tinyMCE.PluginManager.add( 'app_pages_plugin', function AppPagesMcePlugin( editor ) {
    editor.addButton( 'app_pages_slot_button', {
      icon: 'app-pages-slot',
      tooltip: 'Insert App Page Slot',
      onclick: function () {
        wp.mce[ AppPagesConfig.slotShortcode ].popupwindow( editor );
      },
    } );
  } );
} )();
