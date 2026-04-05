/**
 * Extension Registry for NovaTiptapWysiwyg
 *
 * Allows host apps to register custom Tiptap extensions and toolbar buttons
 * that get merged into the editor at runtime.
 *
 * Load-order safe: Nova may load host app scripts before this field's bundle.
 * Host apps add a one-line stub at the top of their script to queue calls,
 * and the real registry replays them when it initializes.
 *
 * Host app usage:
 *
 *   // This stub line ensures calls work regardless of load order
 *   window.NovaTiptapWysiwyg ??= { _queue: [], registerExtension(...a) { this._queue.push(['registerExtension', a]) }, registerButton(...a) { this._queue.push(['registerButton', a]) }, registerExtensionWithButton(...a) { this._queue.push(['registerExtensionWithButton', a]) } }
 *
 *   // Then call methods normally
 *   window.NovaTiptapWysiwyg.registerButton({ name: 'myBtn', icon: '<svg>...</svg>', ... })
 */

// Collect any registrations queued by scripts that loaded before us
const pending = window.NovaTiptapWysiwyg?._queue || []

class ExtensionRegistry {
  constructor() {
    this._global = { extensions: [], buttons: [] }
    this._perField = {}
  }

  /**
   * Register a Tiptap extension.
   * @param {object} extension - A configured Tiptap extension instance
   * @param {object} [options] - { fields: ['body', 'content'] } to target specific fields
   */
  registerExtension(extension, options) {
    const targets = this._resolveTargets(options)
    targets.forEach((t) => t.extensions.push(extension))
    return this
  }

  /**
   * Register a custom toolbar button.
   * @param {object} buttonDef - { name, icon, title, action(editor), isActive?(editor), group?, component? }
   * @param {object} [options] - { fields: ['body'] } to target specific fields
   */
  registerButton(buttonDef, options) {
    const targets = this._resolveTargets(options)
    targets.forEach((t) => t.buttons.push(buttonDef))
    return this
  }

  /**
   * Register both a Tiptap extension and its toolbar button together.
   * @param {object} extension - A configured Tiptap extension instance
   * @param {object} buttonDef - Button definition (see registerButton)
   * @param {object} [options] - { fields: ['body'] } to target specific fields
   */
  registerExtensionWithButton(extension, buttonDef, options) {
    this.registerExtension(extension, options)
    this.registerButton(buttonDef, options)
    return this
  }

  /**
   * Get all extensions for a given field attribute (merges global + per-field).
   * @param {string} fieldAttribute - The field's attribute name (e.g., 'body')
   * @returns {object[]} Array of Tiptap extension instances
   */
  getExtensions(fieldAttribute) {
    const perField = this._perField[fieldAttribute]
    return [
      ...this._global.extensions,
      ...(perField ? perField.extensions : []),
    ]
  }

  /**
   * Get all buttons for a given field attribute (merges global + per-field).
   * @param {string} fieldAttribute - The field's attribute name (e.g., 'body')
   * @returns {object[]} Array of button definitions
   */
  getButtons(fieldAttribute) {
    const perField = this._perField[fieldAttribute]
    const buttons = [
      ...this._global.buttons,
      ...(perField ? perField.buttons : []),
    ]
    // Deduplicate by name, last registration wins
    const seen = new Map()
    buttons.forEach((btn) => seen.set(btn.name, btn))
    return [...seen.values()]
  }

  /** @private */
  _resolveTargets(options) {
    if (options?.fields?.length) {
      return options.fields.map((f) => this._ensureField(f))
    }
    return [this._global]
  }

  /** @private */
  _ensureField(fieldAttribute) {
    if (!this._perField[fieldAttribute]) {
      this._perField[fieldAttribute] = { extensions: [], buttons: [] }
    }
    return this._perField[fieldAttribute]
  }
}

export const registry = new ExtensionRegistry()

// Replay any registrations that were queued before this module loaded
pending.forEach(([method, args]) => registry[method](...args))

// Replace the stub (or create) the global with the real registry
window.NovaTiptapWysiwyg = registry
