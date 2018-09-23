"use strict";
tinyMCE.PluginManager.add('app_pages_plugin', function AppPagesMcePlugin(editor) {
    editor.addButton('app_pages_slot_button', {
        icon: 'app-pages-slot',
        tooltip: 'Insert App Page Slot',
        onclick: function () {
            console.log(this, arguments);
            //wp.mce.views.get( AppPagesConfig.slotShortcode ).popupwindow( editor );
        },
    });
});
//# sourceMappingURL=mce-plugin.js.map