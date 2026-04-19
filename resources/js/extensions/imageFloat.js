import ImageResize from 'tiptap-extension-resize-image'

/**
 * ImageResizeWithFloat — extends tiptap-extension-resize-image to add a
 * `dataFloat` attribute. Serialized as `data-float="..."` plus matching
 * inline `float` / margin CSS in the `style` attribute, so the saved HTML
 * produces real text-wrap behavior in the frontend and detail iframe.
 *
 * Commands: setImageFloatLeft / Right / Center / None.
 * Values: 'left' | 'right' | 'center' | null
 */
const FLOAT_STYLES = {
  left: 'float: left; margin: 0 1em 0.5em 0;',
  right: 'float: right; margin: 0 0 0.5em 1em;',
  center: 'display: block; margin-left: auto; margin-right: auto;',
}

export const ImageResizeWithFloat = ImageResize.extend({
  addAttributes() {
    return {
      ...this.parent?.(),
      dataFloat: {
        default: null,
        parseHTML: (el) => el.getAttribute('data-float') || null,
        renderHTML: (attrs) => {
          if (!attrs.dataFloat) {
            return {}
          }
          const style = FLOAT_STYLES[attrs.dataFloat] ?? ''
          return style
            ? { 'data-float': attrs.dataFloat, style }
            : { 'data-float': attrs.dataFloat }
        },
      },
    }
  },

  addCommands() {
    const parentCommands = this.parent?.() ?? {}
    const IMAGE_TYPES = ['image', 'imageResize']

    const findImage = (state) => {
      const { doc, selection } = state
      if (selection.node && IMAGE_TYPES.includes(selection.node.type.name)) {
        return { pos: selection.from, typeName: selection.node.type.name }
      }
      let result = null
      doc.nodesBetween(selection.from, selection.to, (node, pos) => {
        if (result) {
          return false
        }
        if (IMAGE_TYPES.includes(node.type.name)) {
          result = { pos, typeName: node.type.name }
          return false
        }
      })
      if (!result && selection.from === selection.to && selection.from > 0) {
        const before = doc.resolve(selection.from).nodeBefore
        if (before && IMAGE_TYPES.includes(before.type.name)) {
          result = { pos: selection.from - before.nodeSize, typeName: before.type.name }
        }
      }
      return result
    }

    const apply = (value) => ({ chain, state }) => {
      const target = findImage(state)
      if (!target) {
        // eslint-disable-next-line no-console
        console.warn('[NovaTiptapWysiwyg] No image found at selection — click the image first.')
        return false
      }
      return chain()
        .setNodeSelection(target.pos)
        .updateAttributes(target.typeName, { dataFloat: value })
        .run()
    }

    return {
      ...parentCommands,
      setImageFloatLeft: () => apply('left'),
      setImageFloatRight: () => apply('right'),
      setImageFloatCenter: () => apply('center'),
      setImageFloatNone: () => apply(null),
    }
  },
})

export default ImageResizeWithFloat
