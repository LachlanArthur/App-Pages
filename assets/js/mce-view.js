"use strict";
class AppPageSlot extends wp.mce.View {
    setLoader(dashicon) {
        super.setLoader.call(this, dashicon || 'layout');
    }
    edit(text, update) {
        console.trace('edit', this, text, update);
    }
    initialize() {
        this.action = 'la_app_pages_slot_preview';
        console.trace('initialize', this);
        wp.ajax
            .post(this.action, $.extend({
            app_page: AppPagesConfig.appPage,
        }, this.shortcode.attrs.named))
            .done(this.render.bind(this))
            .fail(response => {
            this.setError(response.message || response.statusText);
        });
        this.render();
    }
}
wp.mce.views.register(AppPagesConfig.slotShortcode, AppPageSlot.prototype);
//# sourceMappingURL=mce-view.js.map