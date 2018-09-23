export = wp;
export as namespace wp;

import Backbone from 'backbone';
import tinymce from 'tinymce';

type ShortcodeType = 'self-closing' | 'closed' | 'single'

type ShortcodeOptions = {
  tag?: string
  attrs?: string | ShortcodeAttrs | { [ key: string ]: string }
  type?: ShortcodeType
  content?: string
}

type ShortcodeAttrs = {
  named: { [ key: string ]: string },
  numeric: string[]
}

declare namespace wp {

  /**
   * wp.ajax
   * ------
   *
   * Tools for sending ajax requests with JSON responses and built in error handling.
   * Mirrors and wraps jQuery's ajax APIs.
   */
  namespace ajax {

    type AjaxResponse = {
      message: string
      [ key: string ]: any
    }

    var settings: object

    /**
     * Sends a POST request to WordPress.
     *
     * @param options Options passed to jQuery.ajax.
     *
     * @return A jQuery promise that represents the request, decorated with an abort() method.
     */
    function post<ResponseType = AjaxResponse>( options: JQueryAjaxSettings ): JQueryPromise<ResponseType>

    /**
     * Sends a POST request to WordPress.
     *
     * @param action The slug of the action to fire in WordPress.
     * @param data   Optional. The data to populate $_POST with.
     *
     * @return A jQuery promise that represents the request, decorated with an abort() method.
     */
    function post<ResponseType = AjaxResponse>( action: string, data?: object ): JQueryPromise<ResponseType>

    /**
     * Sends a POST request to WordPress.
     *
     * @param options Options passed to jQuery.ajax.
     *
     * @return A jQuery promise that represents the request, decorated with an abort() method.
     */
    function send<ResponseType = AjaxResponse>( options: JQueryAjaxSettings ): JQueryPromise<ResponseType>

    /**
     * Sends a POST request to WordPress.
     *
     * @param action  The slug of the action to fire in WordPress.
     * @param options Optional. The options passed to jQuery.ajax.
     *
     * @return A jQuery promise that represents the request, decorated with an abort() method.
     */
    function send<ResponseType = AjaxResponse>( action: string, data?: object ): JQueryPromise<ResponseType>

  }

  namespace mce {

    class View<TModel extends Backbone.Model = any> extends Backbone.View<TModel> {

      /**
       * A Backbone-like View constructor intended for use when rendering a TinyMCE View.
       * The main difference is that the TinyMCE View is not tied to a particular DOM node.
       */
      constructor( options: object )

      [ key: string ]: any

      /**
       * The shortcode name.
       */
      type: string

      /**
       * The content.
       */
      content: any

      /**
       * Whether or not to display a loader.
       */
      loader: boolean

      /**
       * Whether or not to ignore the matched text.
       */
      ignore: boolean

      /**
       * The textual representation of the view.
       *
       * @private
       */
      text: string

      /**
       * The textual representation of the view, url-encoded.
       *
       * @private
       */
      encodedText: string

      /**
       * Calculated height of the content iframe.
       */
      iframeHeight?: number

      /**
       * The tinymce editor.
       */
      editor?: tinymce.Editor

      /**
       * The shortcode object.
       */
      shortcode: shortcode

      /**
       * Runs after the view instance is created.
       */
      initialize(): void

      /**
       * Returns the content to render in the view node.
       */
      getContent(): any

      /**
       * Renders all view nodes tied to this view instance that are not yet rendered.
       *
       * @param content The content to render. Optional.
       * @param force   Rerender all view nodes tied to this view instance. Optional.
       */
      render( content?: string, force?: boolean ): View<TModel>

      /**
       * Binds a given node after its content is added to the DOM.
       */
      bindNode(): void

      /**
       * Unbinds a given node before its content is removed from the DOM.
       */
      unbindNode(): void

      /**
       * Unbinds all view nodes tied to this view instance.
       * Runs before their content is removed from the DOM.
       */
      unbind(): void

      /**
       * Gets all the TinyMCE editor instances that support views.
       *
       * @param callback A callback.
       */
      getEditors( callback: ( editor: tinymce.Editor ) => void ): void

      /**
       * Gets all view nodes tied to this view instance.
       *
       * @param callback A callback.
       * @param rendered Get (un)rendered view nodes. Optional.
       */
      getNodes( callback: ( editor: tinymce.Editor, node: View, content: View ) => void, rendered?: boolean ): void

      /**
       * Gets all marker nodes tied to this view instance.
       *
       * @param callback A callback.
       */
      getMarkers( callback: ( editor: tinymce.Editor, node: View ) => void ): void

      /**
       * Replaces all marker nodes tied to this view instance.
       */
      replaceMarkers(): void

      /**
       * Removes all marker nodes tied to this view instance.
       */
      removeMarkers(): void

      /**
       * Sets the content for all view nodes tied to this view instance.
       *
       * @param content  The content to set.
       * @param callback A callback. Optional.
       * @param rendered Only set for (un)rendered nodes. Optional.
       */
      setContent( content: any, callback?: ( editor: tinymce.Editor, node: View ) => void, rendered?: boolean ): void

      /**
       * Sets the content in an iframe for all view nodes tied to this view instance.
       *
       * @param head     HTML string to be added to the head of the document.
       * @param body     HTML string to be added to the body of the document.
       * @param callback A callback. Optional.
       * @param rendered Only set for (un)rendered nodes. Optional.
       */
      setIframes( head: string, body: string, callback?: ( editor: tinymce.Editor, node: View ) => void, rendered?: boolean ): void

      /**
       * Sets a loader for all view nodes tied to this view instance.
       *
       * @param dashicon A dashicon ID. Optional. {@link https://developer.wordpress.org/resource/dashicons/}
       */
      setLoader( dashicon?: string ): void

      /**
       * Sets an error for all view nodes tied to this view instance.
       *
       * @param message  The error message to set.
       * @param dashicon A dashicon ID. Optional. {@link https://developer.wordpress.org/resource/dashicons/}
       */
      setError( message: string, dashicon?: string ): void

      /**
       * Tries to find a text match in a given string.
       *
       * @param content The string to scan.
       */
      match( content: string ): object

      /**
       *
       * @param text   The existing content text
       * @param update A callback that will update the content text
       */
      edit?( text: string, update: ( text: string, force?: boolean ) => void ): void

      /**
       * Update the text of a given view node.
       *
       * @param text   The new text.
       * @param editor The TinyMCE editor instance the view node is in.
       * @param node   The view node to update.
       * @param force  Recreate the instance. Optional.
       */
      update( text: string, editor: tinymce.Editor, node: HTMLElement, force?: boolean ): void

      /**
       * Remove a given view node from the DOM.
       *
       * @param editor The TinyMCE editor instance the view node is in.
       * @param node   The view node to remove.
       */
      remove( editor?: tinymce.Editor, node?: HTMLElement ): View<TModel>

    }

    /**
     * wp.mce.views
     *
     * A set of utilities that simplifies adding custom UI within a TinyMCE editor.
     * At its core, it serves as a series of converters, transforming text to a
     * custom UI, and back again.
     */
    namespace views {

      /**
       * Registers a new view type.
       *
       * @param type   The view type.
       * @param extend An object to extend wp.mce.View.prototype with.
       */
      function register<T extends Partial<View>>( type: string, extend: T ): void

      /**
       * Unregisters a view type.
       *
       * @param type The view type.
       */
      function unregister( type: string ): void

      /**
       * Returns the settings of a view type.
       *
       * @param type The view type.
       *
       * @return The view constructor.
       */
      function get( type: string ): Function

      /**
       * Unbinds all view nodes.
       * Runs before removing all view nodes from the DOM.
       */
      function unbind(): void

      /**
       * Scans a given string for each view's pattern,
       * replacing any matches with markers,
       * and creates a new instance for every match.
       *
       * @param content The string to scan.
       * @param editor  The editor.
       *
       * @return The string with markers.
       */
      function setMarkers( content: string, editor: tinymce.Editor ): string

      /**
       * Create a view instance.
       *
       * @param type    The view type.
       * @param text    The textual representation of the view.
       * @param options Options.
       * @param force   Recreate the instance. Optional.
       *
       * @return The view instance.
       */
      function createInstance( type: string, text: string, options?: object, force?: boolean ): mce.View

      /**
       * Get a view instance.
       *
       * @param object The textual representation of the view or the view node.
       *
       * @return The view instance or undefined.
       */
      function getInstance( object: string | HTMLElement ): wp.mce.View

      /**
       * Given a view node, get the view's text.
       *
       * @param node The view node.
       *
       * @return The textual representation of the view.
       */
      function getText( node: HTMLElement ): string

      /**
       * Renders all view nodes that are not yet rendered.
       *
       * @param force Rerender all view nodes.
       */
      function render( force?: boolean ): void

      /**
       * Update the text of a given view node.
       *
       * @param text   The new text.
       * @param editor The TinyMCE editor instance the view node is in.
       * @param node   The view node to update.
       * @param force  Recreate the instance. Optional.
       */
      function update( text: string, editor: tinymce.Editor, node: HTMLElement, force?: boolean ): void

      /**
       * Renders any editing interface based on the view type.
       *
       * @param editor The TinyMCE editor instance the view node is in.
       * @param node   The view node to edit.
       */
      function edit( editor: tinymce.Editor, node: HTMLElement ): void

      /**
       * Remove a given view node from the DOM.
       *
       * @param editor The TinyMCE editor instance the view node is in.
       * @param node   The view node to remove.
       */
      function remove( editor: tinymce.Editor, node: HTMLElement ): void

    }

  }

  class shortcode {

    tag: string

    attrs: ShortcodeAttrs

    type: ShortcodeType

    content: string

    /**
     * Shortcode Objects
     * -----------------
     *
     * Shortcode objects are generated automatically when using the main
     * `wp.shortcode` methods: `next()`, `replace()`, and `string()`.
     *
     * To access a raw representation of a shortcode, pass an `options` object,
     * containing a `tag` string, a string or object of `attrs`, a string
     * indicating the `type` of the shortcode ('single', 'self-closing', or
     * 'closed'), and a `content` string.
     */
    constructor( options: ShortcodeOptions )

    /**
     * ### Get a shortcode attribute
     *
     * Automatically detects whether `attr` is named or numeric and routes
     * it accordingly.
     */
    public get( attr: string ): string

    /**
     * ### Set a shortcode attribute
     *
     * Automatically detects whether `attr` is named or numeric and routes
     * it accordingly.
     */
    public set( attr: string, value: string ): this

    /**
     * ### Transform the shortcode match into a string
     */
    public string(): string

  }

  /**
   * Fetch a JavaScript template for an id, and return a templating function for it.
   *
   * @param id A string that corresponds to a DOM element with an id prefixed with "tmpl-". For example, "attachment" maps to "tmpl-attachment".
   *
   * @return A function that lazily-compiles the template requested.
   */
  function template( id: string ): ( ...data: any[] ) => string;

}
