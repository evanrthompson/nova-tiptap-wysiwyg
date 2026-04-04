import { Extension } from '@tiptap/core'

/**
 * FloatControl — adds a `dataFloat` attribute to image and snippetBlock nodes,
 * with commands to set float direction. Enables text wrapping around images
 * and snippet blocks.
 *
 * Values: 'left', 'right', 'center', 'none' (or null)
 */
export const FloatControl = Extension.create({
  name: 'floatControl',

  addGlobalAttributes() {
    return [
      {
        types: ['image', 'snippetBlock'],
        attributes: {
          dataFloat: {
            default: null,
            parseHTML: (element) => element.getAttribute('data-float') || null,
            renderHTML: (attributes) => {
              if (!attributes.dataFloat) {
                return {}
              }
              return { 'data-float': attributes.dataFloat }
            },
          },
        },
      },
    ]
  },

  addCommands() {
    return {
      setFloatLeft: () => ({ commands }) => {
        return setFloatOnActive(commands, 'left')
      },
      setFloatRight: () => ({ commands }) => {
        return setFloatOnActive(commands, 'right')
      },
      setFloatCenter: () => ({ commands }) => {
        return setFloatOnActive(commands, 'center')
      },
      setFloatNone: () => ({ commands }) => {
        return setFloatOnActive(commands, null)
      },
    }
  },
})

function setFloatOnActive(commands, value) {
  // Try updating image first, then snippetBlock
  const updated = commands.updateAttributes('image', { dataFloat: value })
  if (!updated) {
    return commands.updateAttributes('snippetBlock', { dataFloat: value })
  }
  return updated
}

export default FloatControl
