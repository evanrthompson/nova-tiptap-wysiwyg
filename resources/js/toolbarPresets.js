// Button canonical order — used to sort resolved set for consistent display
export const BUTTON_ORDER = [
  'bold', 'italic', 'underline', 'strike',
  'subscript', 'superscript',
  'heading',
  'bulletList', 'orderedList',
  'blockquote', 'horizontalRule', 'hardBreak', 'code',
  'highlight', 'color',
  'textAlign',
  'table',
  'link', 'image',
  'snippets',
  'float',
]

// Preset definitions
export const PRESETS = {
  basic: ['bold', 'italic', 'link'],
  standard: ['bold', 'italic', 'underline', 'strike', 'heading', 'bulletList', 'orderedList', 'blockquote', 'link', 'image', 'float'],
  full: [...BUTTON_ORDER],
}

/**
 * Resolve final ordered button set from preset name + override arrays.
 * Returns buttons sorted by BUTTON_ORDER for consistent toolbar display.
 *
 * @param {string} preset - Preset name (basic, standard, full)
 * @param {string[]} withButtons - Additional buttons to include
 * @param {string[]} withoutButtons - Buttons to exclude
 * @returns {string[]} Ordered array of button keys
 */
export function resolveButtons(preset, withButtons = [], withoutButtons = []) {
  const base = PRESETS[preset] ?? PRESETS.standard
  const merged = [...new Set([...base, ...withButtons])]
  const filtered = merged.filter((b) => !withoutButtons.includes(b))
  return BUTTON_ORDER.filter((b) => filtered.includes(b))
}
