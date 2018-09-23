tinyMCE.PluginManager.add( 'app_pages_plugin', editor => {
  editor.addButton( 'app_pages_slot_button', {
    icon: 'app-pages-slot',
    tooltip: 'Insert App Page Slot',
    onclick: ( e: MouseEvent ) => {
      console.log( arguments );
      //wp.mce.views.get( AppPagesConfig.slotShortcode ).popupwindow( editor );
    },
  } );
} );
