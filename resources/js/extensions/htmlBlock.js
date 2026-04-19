import { Node, mergeAttributes } from '@tiptap/core'

/**
 * SnippetBlock — a container node that wraps editable block content in a
 * styled <div>. Uses a `data-snippet` attribute to distinguish from random
 * divs so ProseMirror's parser matches it unambiguously.
 *
 * The inner content (paragraphs, headings, etc.) is fully editable.
 * The wrapper div preserves its `class` attribute for styling.
 */
export const SnippetBlock = Node.create({
  name: 'snippetBlock',

  group: 'block',

  content: 'block+',

  defining: true,

  addAttributes() {
    return {
      class: {
        default: null,
        parseHTML: (element) => element.getAttribute('class'),
      },
    }
  },

  parseHTML() {
    return [
      {
        tag: 'div[data-snippet]',
      },
    ]
  },

  renderHTML({ HTMLAttributes }) {
    return ['div', mergeAttributes(HTMLAttributes, { 'data-snippet': '' }), 0]
  },
})

/**
 * Prepare snippet HTML for insertion by adding the data-snippet attribute
 * to the outer div so ProseMirror's DOMParser matches our node rule.
 */
export function prepareSnippetHtml(html) {
  return html.replace(/^<div(\s|>)/, '<div data-snippet $1')
}

/**
 * Placeholder token swapped out at snippet-insertion time.
 */
export const CONTENT_PLACEHOLDER = '{{content}}'

/**
 * Placeholder used when the user inserts a snippet without a selection.
 * Appears as editable text so they can overwrite it.
 */
export const EMPTY_CONTENT_FALLBACK = 'Snippet Content'

/**
 * Render a snippet template:
 *
 *   - Any `{{content}}` token is replaced with the user's selected HTML
 *     (or the fallback string when the selection is empty).
 *   - The outer div is tagged with `data-snippet` so the SnippetBlock
 *     parser rule matches it.
 */
export function renderSnippet(templateHtml, selectedHtml = '') {
  const replacement = selectedHtml && selectedHtml.trim() !== ''
    ? selectedHtml
    : EMPTY_CONTENT_FALLBACK
  const withContent = templateHtml.split(CONTENT_PLACEHOLDER).join(replacement)
  return prepareSnippetHtml(withContent)
}

export default SnippetBlock
