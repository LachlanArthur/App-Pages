declare var AppPagesConfig: AppPagesConfig;

class AppPageSlot extends wp.mce.View {

  setLoader( dashicon?: string ) {
    super.setLoader.call( this, dashicon || 'layout' );
  }

  edit( text: string, update: ( text: string, force?: boolean ) => void ) {
    console.trace( 'edit', this, text, update );
  }

  initialize() {
    this.action = 'la_app_pages_slot_preview';

    console.trace( 'initialize', this );

    wp.ajax
      .post( this.action, $.extend( {
        app_page: AppPagesConfig.appPage,
      }, this.shortcode.attrs.named ) )
      .done( this.render.bind( this ) )
      .fail( response => {
        this.setError( response.message || response.statusText );
      } );

    this.render();

  }

}

wp.mce.views.register( AppPagesConfig.slotShortcode, AppPageSlot.prototype );
