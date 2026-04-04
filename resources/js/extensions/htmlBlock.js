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

export default SnippetBlock
