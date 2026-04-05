<template>
  <DefaultField
    :field="field"
    :errors="errors"
    :show-help-text="showHelpText"
    :full-width-content="fullWidthContent"
  >
    <template #field>
      <div class="nova-tiptap-wysiwyg">
        <!-- Toolbar -->
        <div
          v-if="editor && !field.readonly"
          class="nova-tiptap-toolbar flex flex-wrap items-center gap-1 mb-0 p-1.5 border border-gray-200 dark:border-gray-700 rounded-t bg-gray-50 dark:bg-gray-800"
        >
          <!-- Text formatting -->
          <button v-if="buttons.includes('bold')" type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('bold'))" @click="editor.chain().focus().toggleBold().run()" title="Bold">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M7.5 3.75A.75.75 0 0 1 8.25 3h5.25a3.75 3.75 0 0 1 2.7 6.375A3.75 3.75 0 0 1 14.25 15H8.25a.75.75 0 0 1-.75-.75v-10.5ZM9 4.5v3.75h4.5a2.25 2.25 0 0 0 0-4.5H9ZM9 9.75v3.75h5.25a2.25 2.25 0 0 0 0-4.5H9Z" clip-rule="evenodd" /></svg>
          </button>
          <button v-if="buttons.includes('italic')" type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('italic'))" @click="editor.chain().focus().toggleItalic().run()" title="Italic">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M10.497 3.744a.75.75 0 0 1 .75-.744h6a.75.75 0 0 1 0 1.5h-2.173l-3.5 15h2.173a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1 0-1.5h2.173l3.5-15h-2.173a.75.75 0 0 1-.75-.756Z" clip-rule="evenodd" /></svg>
          </button>
          <button v-if="buttons.includes('underline')" type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('underline'))" @click="editor.chain().focus().toggleUnderline().run()" title="Underline">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M7.5 3v9a4.5 4.5 0 0 0 9 0V3m-10.5 18h12" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
          </button>
          <button v-if="buttons.includes('strike')" type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('strike'))" @click="editor.chain().focus().toggleStrike().run()" title="Strikethrough">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M17.25 12H6.75M8.25 6.75A3.75 3.75 0 0 1 12 3c2.071 0 3.75 1.679 3.75 3.75 0 .878-.3 1.685-.804 2.325M15.75 17.25A3.75 3.75 0 0 1 12 21a3.75 3.75 0 0 1-3.75-3.75c0-.878.3-1.685.804-2.325" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round"/></svg>
          </button>

          <!-- Subscript / Superscript -->
          <template v-if="buttons.includes('subscript') || buttons.includes('superscript')">
            <span v-if="['bold','italic','underline','strike'].some(b => buttons.includes(b))" class="border-l border-gray-300 dark:border-gray-600 mx-0.5 h-5"></span>
            <button v-if="buttons.includes('subscript')" type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('subscript'))" @click="editor.chain().focus().toggleSubscript().run()" title="Subscript">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M5.5 4L10.5 12L5.5 20H8.2L12 14L15.8 20H18.5L13.5 12L18.5 4H15.8L12 10L8.2 4H5.5Z" /><text x="19" y="22" font-size="8" font-weight="bold" fill="currentColor">2</text></svg>
            </button>
            <button v-if="buttons.includes('superscript')" type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('superscript'))" @click="editor.chain().focus().toggleSuperscript().run()" title="Superscript">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M5.5 4L10.5 12L5.5 20H8.2L12 14L15.8 20H18.5L13.5 12L18.5 4H15.8L12 10L8.2 4H5.5Z" /><text x="19" y="10" font-size="8" font-weight="bold" fill="currentColor">2</text></svg>
            </button>
          </template>

          <span v-if="['bold','italic','underline','strike','subscript','superscript'].some(b => buttons.includes(b)) && buttons.includes('heading')" class="border-l border-gray-300 dark:border-gray-600 mx-0.5 h-5"></span>

          <!-- Headings -->
          <template v-if="buttons.includes('heading')">
            <button v-for="level in [1, 2, 3, 4, 5, 6]" :key="'h' + level" type="button" class="nova-tiptap-btn px-1.5 text-xs font-bold" :class="btnClass(editor.isActive('heading', { level }))" @click="editor.chain().focus().toggleHeading({ level }).run()" :title="'Heading ' + level">H{{ level }}</button>
          </template>

          <span v-if="buttons.includes('heading') && ['bulletList','orderedList'].some(b => buttons.includes(b))" class="border-l border-gray-300 dark:border-gray-600 mx-0.5 h-5"></span>

          <!-- Lists -->
          <button v-if="buttons.includes('bulletList')" type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('bulletList'))" @click="editor.chain().focus().toggleBulletList().run()" title="Bullet List">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M2.625 6.75a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Zm4.875.75a.75.75 0 0 1 0-1.5h13.5a.75.75 0 0 1 0 1.5H7.5ZM2.625 12a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0ZM7.5 12.75a.75.75 0 0 1 0-1.5h13.5a.75.75 0 0 1 0 1.5H7.5ZM2.625 17.25a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0ZM7.5 18a.75.75 0 0 1 0-1.5h13.5a.75.75 0 0 1 0 1.5H7.5Z" clip-rule="evenodd" /></svg>
          </button>
          <button v-if="buttons.includes('orderedList')" type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('orderedList'))" @click="editor.chain().focus().toggleOrderedList().run()" title="Ordered List">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M2.25 5.25a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 0 1.5h-.75v2.25a.75.75 0 0 1-1.5 0v-3ZM7.5 6.75a.75.75 0 0 1 0-1.5h13.5a.75.75 0 0 1 0 1.5H7.5ZM2.25 10.5a.75.75 0 0 1 .75-.75h1.5a.75.75 0 0 1 .53 1.28l-1.03 1.03h.5a.75.75 0 0 1 0 1.5H3a.75.75 0 0 1-.53-1.28l1.03-1.03H3a.75.75 0 0 1-.75-.75ZM7.5 12.75a.75.75 0 0 1 0-1.5h13.5a.75.75 0 0 1 0 1.5H7.5ZM7.5 18a.75.75 0 0 1 0-1.5h13.5a.75.75 0 0 1 0 1.5H7.5Z" clip-rule="evenodd" /></svg>
          </button>

          <span v-if="['bulletList','orderedList'].some(b => buttons.includes(b)) && ['blockquote','horizontalRule','hardBreak','code'].some(b => buttons.includes(b))" class="border-l border-gray-300 dark:border-gray-600 mx-0.5 h-5"></span>

          <!-- Block elements -->
          <button v-if="buttons.includes('blockquote')" type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('blockquote'))" @click="editor.chain().focus().toggleBlockquote().run()" title="Blockquote">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M4.583 17.321C3.553 16.227 3 15 3 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311C9.17 11.663 10.5 13.026 10.5 14.75a3.25 3.25 0 0 1-3.25 3.25c-1.116 0-2.13-.45-2.667-1.679Zm10.5 0C14.053 16.227 13.5 15 13.5 13.011c0-3.5 2.457-6.637 6.03-8.188l.893 1.378c-3.335 1.804-3.987 4.145-4.247 5.621.537-.278 1.24-.375 1.929-.311 1.565.152 2.895 1.515 2.895 3.239a3.25 3.25 0 0 1-3.25 3.25c-1.116 0-2.13-.45-2.667-1.679Z" /></svg>
          </button>
          <button v-if="buttons.includes('horizontalRule')" type="button" class="nova-tiptap-btn" :class="btnClass(false)" @click="editor.chain().focus().setHorizontalRule().run()" title="Horizontal Rule">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M4.5 12a.75.75 0 0 1 .75-.75h13.5a.75.75 0 0 1 0 1.5H5.25A.75.75 0 0 1 4.5 12Z" clip-rule="evenodd" /></svg>
          </button>
          <button v-if="buttons.includes('hardBreak')" type="button" class="nova-tiptap-btn" :class="btnClass(false)" @click="editor.chain().focus().setHardBreak().run()" title="Line Break">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M19.5 5.25v7.5H7.81l2.72-2.72a.75.75 0 0 0-1.06-1.06l-4.5 4.5a.75.75 0 0 0 0 1.06l4.5 4.5a.75.75 0 1 0 1.06-1.06L7.81 14.25H21a.75.75 0 0 0 .75-.75v-8.25a.75.75 0 0 0-1.5 0Z" /></svg>
          </button>
          <button v-if="buttons.includes('code')" type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('code'))" @click="editor.chain().focus().toggleCode().run()" title="Code">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M14.447 3.026a.75.75 0 0 1 .527.921l-4.5 16.5a.75.75 0 0 1-1.448-.394l4.5-16.5a.75.75 0 0 1 .921-.527ZM16.72 6.22a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06L21.44 12l-4.72-4.72a.75.75 0 0 1 0-1.06Zm-9.44 0a.75.75 0 0 1 0 1.06L2.56 12l4.72 4.72a.75.75 0 0 1-1.06 1.06L.97 12.53a.75.75 0 0 1 0-1.06l5.25-5.25a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" /></svg>
          </button>

          <!-- Highlight & Color -->
          <template v-if="buttons.includes('highlight') || buttons.includes('color')">
            <span v-if="['blockquote','horizontalRule','hardBreak','code'].some(b => buttons.includes(b))" class="border-l border-gray-300 dark:border-gray-600 mx-0.5 h-5"></span>
            <div v-if="buttons.includes('highlight')" class="relative">
              <button type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('highlight'))" @click="showHighlightPicker = !showHighlightPicker" title="Highlight">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M15.243 3.343a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1 0 1.414l-9 9a1 1 0 0 1-.707.293H7a1 1 0 0 1-1-1v-3.95a1 1 0 0 1 .293-.707l9-9ZM8 16.636V17h.364L17.95 7.414 16.586 6.05 8 14.636Z" /><path d="M2 20h20v2H2z" :fill="editor.getAttributes('highlight').color || '#fef08a'" /></svg>
              </button>
              <div v-if="showHighlightPicker" class="absolute top-full left-0 mt-1 p-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-50 flex gap-1">
                <button v-for="c in highlightColors" :key="c" type="button" class="w-6 h-6 rounded border border-gray-300 dark:border-gray-600" :style="{ backgroundColor: c }" :title="c" @click="applyHighlight(c)" />
                <button type="button" class="w-6 h-6 rounded border border-gray-300 dark:border-gray-600 flex items-center justify-center text-xs text-gray-500" title="Remove highlight" @click="removeHighlight">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3"><path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                </button>
              </div>
            </div>
            <div v-if="buttons.includes('color')" class="relative">
              <button type="button" class="nova-tiptap-btn" :class="btnClass(!!editor.getAttributes('textStyle').color)" @click="showColorPicker = !showColorPicker" title="Text Color">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M11 2L5.5 16h2.25l1.12-3h6.26l1.12 3h2.25L13 2h-2Zm-1.38 9L12 4.67 14.38 11H9.62Z" /><path d="M2 20h20v2H2z" :fill="editor.getAttributes('textStyle').color || 'currentColor'" /></svg>
              </button>
              <div v-if="showColorPicker" class="absolute top-full left-0 mt-1 p-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-50 flex gap-1 flex-wrap" style="width: 176px">
                <button v-for="c in textColors" :key="c" type="button" class="w-6 h-6 rounded border border-gray-300 dark:border-gray-600" :style="{ backgroundColor: c }" :title="c" @click="applyColor(c)" />
                <button type="button" class="w-6 h-6 rounded border border-gray-300 dark:border-gray-600 flex items-center justify-center text-xs text-gray-500" title="Remove color" @click="removeColor">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3"><path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
                </button>
              </div>
            </div>
          </template>

          <!-- Text Align -->
          <template v-if="buttons.includes('textAlign')">
            <span class="border-l border-gray-300 dark:border-gray-600 mx-0.5 h-5"></span>
            <button type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive({ textAlign: 'left' }))" @click="editor.chain().focus().setTextAlign('left').run()" title="Align Left">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" /></svg>
            </button>
            <button type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive({ textAlign: 'center' }))" @click="editor.chain().focus().setTextAlign('center').run()" title="Align Center">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM6 12a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H6.75A.75.75 0 0 1 6 12Zm-3 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" /></svg>
            </button>
            <button type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive({ textAlign: 'right' }))" @click="editor.chain().focus().setTextAlign('right').run()" title="Align Right">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM6.75 12a.75.75 0 0 1 .75-.75h13.5a.75.75 0 0 1 0 1.5H7.5a.75.75 0 0 1-.75-.75Zm-3.75 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" /></svg>
            </button>
            <button type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive({ textAlign: 'justify' }))" @click="editor.chain().focus().setTextAlign('justify').run()" title="Justify">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M3 6.75A.75.75 0 0 1 3.75 6h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 6.75ZM3 12a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75A.75.75 0 0 1 3 12Zm0 5.25a.75.75 0 0 1 .75-.75h16.5a.75.75 0 0 1 0 1.5H3.75a.75.75 0 0 1-.75-.75Z" clip-rule="evenodd" /></svg>
            </button>
          </template>

          <!-- Table -->
          <template v-if="buttons.includes('table')">
            <span class="border-l border-gray-300 dark:border-gray-600 mx-0.5 h-5"></span>
            <div class="relative">
              <button type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('table'))" @click="showTableMenu = !showTableMenu" title="Table">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M1.5 5.625c0-1.036.84-1.875 1.875-1.875h17.25c1.035 0 1.875.84 1.875 1.875v12.75c0 1.035-.84 1.875-1.875 1.875H3.375A1.875 1.875 0 0 1 1.5 18.375V5.625ZM3 9.75v4.5h6V9.75H3Zm0 6v2.625c0 .207.168.375.375.375H9v-3H3Zm7.5-6v4.5h7.5V9.75h-7.5Zm7.5 6h-7.5v3h10.125a.375.375 0 0 0 .375-.375V15.75h-3Zm3-6V5.625a.375.375 0 0 0-.375-.375H3.375A.375.375 0 0 0 3 5.625V8.25h18Z" clip-rule="evenodd" /></svg>
              </button>
              <div v-if="showTableMenu" class="absolute top-full left-0 mt-1 p-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-50 min-w-[160px]">
                <button type="button" class="w-full text-left px-2 py-1 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded" @click="insertTable">Insert Table</button>
                <template v-if="editor.isActive('table')">
                  <hr class="my-1 border-gray-200 dark:border-gray-700">
                  <button type="button" class="w-full text-left px-2 py-1 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded" @click="editor.chain().focus().addColumnBefore().run(); showTableMenu = false">Add Column Before</button>
                  <button type="button" class="w-full text-left px-2 py-1 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded" @click="editor.chain().focus().addColumnAfter().run(); showTableMenu = false">Add Column After</button>
                  <button type="button" class="w-full text-left px-2 py-1 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded" @click="editor.chain().focus().deleteColumn().run(); showTableMenu = false">Delete Column</button>
                  <hr class="my-1 border-gray-200 dark:border-gray-700">
                  <button type="button" class="w-full text-left px-2 py-1 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded" @click="editor.chain().focus().addRowBefore().run(); showTableMenu = false">Add Row Before</button>
                  <button type="button" class="w-full text-left px-2 py-1 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded" @click="editor.chain().focus().addRowAfter().run(); showTableMenu = false">Add Row After</button>
                  <button type="button" class="w-full text-left px-2 py-1 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded" @click="editor.chain().focus().deleteRow().run(); showTableMenu = false">Delete Row</button>
                  <hr class="my-1 border-gray-200 dark:border-gray-700">
                  <button type="button" class="w-full text-left px-2 py-1 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded" @click="editor.chain().focus().toggleHeaderRow().run(); showTableMenu = false">Toggle Header Row</button>
                  <button type="button" class="w-full text-left px-2 py-1 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded" @click="editor.chain().focus().mergeCells().run(); showTableMenu = false">Merge Cells</button>
                  <button type="button" class="w-full text-left px-2 py-1 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded" @click="editor.chain().focus().splitCell().run(); showTableMenu = false">Split Cell</button>
                  <hr class="my-1 border-gray-200 dark:border-gray-700">
                  <button type="button" class="w-full text-left px-2 py-1 text-sm text-red-500 hover:bg-red-50 dark:hover:bg-gray-700 rounded" @click="editor.chain().focus().deleteTable().run(); showTableMenu = false">Delete Table</button>
                </template>
              </div>
            </div>
          </template>

          <span v-if="(buttons.includes('highlight') || buttons.includes('color') || buttons.includes('textAlign') || buttons.includes('table')) && (buttons.includes('link') || buttons.includes('image'))" class="border-l border-gray-300 dark:border-gray-600 mx-0.5 h-5"></span>

          <!-- Link -->
          <button v-if="buttons.includes('link')" type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('link'))" @click="toggleLinkPopup" title="Link">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M19.902 4.098a3.75 3.75 0 0 0-5.304 0l-4.5 4.5a3.75 3.75 0 0 0 1.035 6.037.75.75 0 0 1-.646 1.353 5.25 5.25 0 0 1-1.449-8.45l4.5-4.5a5.25 5.25 0 1 1 7.424 7.424l-1.757 1.757a.75.75 0 1 1-1.06-1.06l1.757-1.758a3.75 3.75 0 0 0 0-5.304Zm-7.389 4.267a.75.75 0 0 1 1.04-.208 5.25 5.25 0 0 1 1.449 8.45l-4.5 4.5a5.25 5.25 0 1 1-7.424-7.424l1.757-1.757a.75.75 0 1 1 1.06 1.06l-1.757 1.758a3.75 3.75 0 1 0 5.304 5.304l4.5-4.5a3.75 3.75 0 0 0-1.035-6.037.75.75 0 0 1-.208-1.04Z" clip-rule="evenodd" /></svg>
          </button>
          <button v-if="buttons.includes('link') && editor.isActive('link')" type="button" class="nova-tiptap-btn bg-white dark:bg-gray-700 text-red-500 hover:bg-red-50 dark:hover:bg-gray-600 p-2 rounded" @click="editor.chain().focus().unsetLink().run()" title="Remove Link">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
          </button>

          <!-- Image -->
          <button v-if="buttons.includes('image')" type="button" class="nova-tiptap-btn" :class="btnClass(false)" @click="openImagePicker" title="Insert Image">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 0 1 2.25-2.25h16.5A2.25 2.25 0 0 1 22.5 6v12a2.25 2.25 0 0 1-2.25 2.25H3.75A2.25 2.25 0 0 1 1.5 18V6ZM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0 0 21 18v-1.94l-2.69-2.689a1.5 1.5 0 0 0-2.12 0l-.88.879.97.97a.75.75 0 1 1-1.06 1.06l-5.16-5.159a1.5 1.5 0 0 0-2.12 0L3 16.061Zm10.125-7.81a1.125 1.125 0 1 1 2.25 0 1.125 1.125 0 0 1-2.25 0Z" clip-rule="evenodd" /></svg>
          </button>
          <input ref="imageInput" type="file" accept="image/*" class="hidden" @change="handleImageSelect" />

          <!-- Snippets -->
          <template v-if="snippets.length > 0">
            <span class="border-l border-gray-300 dark:border-gray-600 mx-0.5 h-5"></span>
            <div class="relative">
              <button type="button" class="nova-tiptap-btn" :class="btnClass(false)" @click="showSnippetMenu = !showSnippetMenu" title="Insert Snippet">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M3 6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V6Zm4.5 1.5a.75.75 0 0 0 0 1.5h9a.75.75 0 0 0 0-1.5h-9Zm0 3.75a.75.75 0 0 0 0 1.5h9a.75.75 0 0 0 0-1.5h-9Zm0 3.75a.75.75 0 0 0 0 1.5h5.25a.75.75 0 0 0 0-1.5H7.5Z" clip-rule="evenodd" /></svg>
              </button>
              <div v-if="showSnippetMenu" class="absolute top-full left-0 mt-1 p-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-50 min-w-[180px] max-h-[240px] overflow-y-auto">
                <button
                  v-for="(snippet, i) in snippets"
                  :key="i"
                  type="button"
                  class="w-full text-left px-2 py-1.5 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                  @click="insertSnippet(snippet.html)"
                >{{ snippet.label }}</button>
              </div>
            </div>
          </template>

          <!-- Float controls -->
          <template v-if="buttons.includes('float')">
            <span class="border-l border-gray-300 dark:border-gray-600 mx-0.5 h-5"></span>
            <button type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('image', { dataFloat: 'left' }) || editor.isActive('snippetBlock', { dataFloat: 'left' }))" @click="editor.chain().focus().setFloatLeft().run()" title="Float Left">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><rect x="3" y="4" width="8" height="7" rx="1" /><path d="M14 5.5h7M14 8.5h7M3 14.5h18M3 17.5h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none" /></svg>
            </button>
            <button type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('image', { dataFloat: 'center' }) || editor.isActive('snippetBlock', { dataFloat: 'center' }))" @click="editor.chain().focus().setFloatCenter().run()" title="Center">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><rect x="5" y="3" width="14" height="8" rx="1" /><path d="M3 14.5h18M6 17.5h12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none" /></svg>
            </button>
            <button type="button" class="nova-tiptap-btn" :class="btnClass(editor.isActive('image', { dataFloat: 'right' }) || editor.isActive('snippetBlock', { dataFloat: 'right' }))" @click="editor.chain().focus().setFloatRight().run()" title="Float Right">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><rect x="13" y="4" width="8" height="7" rx="1" /><path d="M3 5.5h7M3 8.5h7M3 14.5h18M3 17.5h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none" /></svg>
            </button>
            <button type="button" class="nova-tiptap-btn" :class="btnClass(false)" @click="editor.chain().focus().setFloatNone().run()" title="No Float">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M3 5.5h18M3 8.5h18M3 11.5h18M3 14.5h18M3 17.5h18" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" fill="none" /></svg>
            </button>
          </template>

          <!-- Custom extension buttons -->
          <template v-if="customButtons.length">
            <span class="border-l border-gray-300 dark:border-gray-600 mx-0.5 h-5"></span>
            <template v-for="btn in customButtons" :key="btn.name">
              <component
                v-if="btn.component"
                :is="btn.component"
                :editor="editor"
                :btn-class="btnClass"
              />
              <button
                v-else
                type="button"
                class="nova-tiptap-btn"
                :class="btnClass(btn.isActive ? btn.isActive(editor) : false)"
                @click="btn.action(editor)"
                :title="btn.title"
                v-html="btn.icon"
              />
            </template>
          </template>

          <span class="border-l border-gray-300 dark:border-gray-600 mx-0.5 h-5"></span>

          <!-- Undo / Redo -->
          <button type="button" class="nova-tiptap-btn" :class="btnClass(false)" @click="editor.chain().focus().undo().run()" title="Undo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M9.53 2.47a.75.75 0 0 1 0 1.06L4.81 8.25H15a6.75 6.75 0 0 1 0 13.5h-3a.75.75 0 0 1 0-1.5h3a5.25 5.25 0 1 0 0-10.5H4.81l4.72 4.72a.75.75 0 1 1-1.06 1.06l-6-6a.75.75 0 0 1 0-1.06l6-6a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd" /></svg>
          </button>
          <button type="button" class="nova-tiptap-btn" :class="btnClass(false)" @click="editor.chain().focus().redo().run()" title="Redo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path fill-rule="evenodd" d="M14.47 2.47a.75.75 0 0 1 1.06 0l6 6a.75.75 0 0 1 0 1.06l-6 6a.75.75 0 1 1-1.06-1.06l4.72-4.72H9a5.25 5.25 0 1 0 0 10.5h3a.75.75 0 0 1 0 1.5H9a6.75 6.75 0 0 1 0-13.5h10.19l-4.72-4.72a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" /></svg>
          </button>
        </div>

        <!-- Link popup -->
        <div
          v-if="showLinkPopup"
          class="nova-tiptap-link-popup flex items-center gap-2 p-2 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800"
        >
          <input
            v-model="linkUrl"
            type="text"
            class="form-control form-input form-control-bordered text-sm flex-1"
            placeholder="Enter URL..."
            @keydown.enter="applyLink"
          />
          <label class="flex items-center gap-1 text-sm text-gray-600 dark:text-gray-400 whitespace-nowrap">
            <input v-model="linkNewTab" type="checkbox" class="form-checkbox" />
            New tab
          </label>
          <button
            type="button"
            class="px-3 py-1 rounded text-sm bg-primary-500 text-white hover:bg-primary-600"
            @click="applyLink"
          >Apply</button>
          <button
            type="button"
            class="px-2 py-1 rounded text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300"
            @click="showLinkPopup = false"
          >Cancel</button>
        </div>

        <!-- Editor content area -->
        <EditorContent
          v-if="editor"
          :editor="editor"
          class="nova-tiptap-content border border-gray-200 dark:border-gray-700 p-3 min-h-[120px] max-w-none"
          :class="{ 'rounded-b': !field.readonly, 'rounded': field.readonly }"
        />
      </div>
    </template>
  </DefaultField>
</template>

<script>
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Placeholder from '@tiptap/extension-placeholder'
import FileHandler from '@tiptap/extension-file-handler'
import Image from '@tiptap/extension-image'
import TextAlign from '@tiptap/extension-text-align'
import { TextStyle } from '@tiptap/extension-text-style'
import Color from '@tiptap/extension-color'
import Highlight from '@tiptap/extension-highlight'
import Subscript from '@tiptap/extension-subscript'
import Superscript from '@tiptap/extension-superscript'
import { Table } from '@tiptap/extension-table'
import { TableRow } from '@tiptap/extension-table-row'
import { TableCell } from '@tiptap/extension-table-cell'
import { TableHeader } from '@tiptap/extension-table-header'
import Dropcursor from '@tiptap/extension-dropcursor'
import ImageResize from 'tiptap-extension-resize-image'
import SnippetBlock, { prepareSnippetHtml } from '../extensions/htmlBlock'
import FloatControl from '../extensions/floatControl'
import { FormField, HandlesValidationErrors } from 'laravel-nova'
import { resolveButtons } from '../toolbarPresets'
import { registry } from '../extensionRegistry'

export default {
  components: { EditorContent },
  mixins: [FormField, HandlesValidationErrors],
  props: ['resourceName', 'resourceId', 'field'],

  setup(props) {
    const resolvedButtons = resolveButtons(
      props.field.preset ?? 'standard',
      props.field.withButtons ?? [],
      props.field.withoutButtons ?? [],
    )

    // Query the extension registry for custom extensions and buttons
    const fieldAttribute = props.field.attribute
    const customExtensions = registry.getExtensions(fieldAttribute)
    const customButtons = registry.getButtons(fieldAttribute)

    // Warn about declared but unregistered extensions
    const declared = props.field.customExtensions ?? []
    if (declared.length) {
      const registeredNames = customExtensions.map((ext) => ext.name).filter(Boolean)
      declared.forEach((name) => {
        if (!registeredNames.includes(name)) {
          console.warn(`[NovaTiptapWysiwyg] Extension "${name}" was declared via withExtensions() but not registered. Register it using window.NovaTiptapWysiwyg.registerExtension().`)
        }
      })
    }

    const uploadImage = async (editorInstance, file) => {
      const formData = new FormData()
      formData.append('file', file)
      try {
        const { data } = await Nova.request().post(props.field.uploadUrl, formData)
        editorInstance.chain().focus().setImage({ src: data.url }).run()
      } catch {
        Nova.error('Image upload failed.')
      }
    }

    const editor = useEditor({
      extensions: [
        StarterKit.configure({
          bold:           resolvedButtons.includes('bold')           ? {} : false,
          italic:         resolvedButtons.includes('italic')         ? {} : false,
          underline:      resolvedButtons.includes('underline')      ? {} : false,
          strike:         resolvedButtons.includes('strike')         ? {} : false,
          heading:        resolvedButtons.includes('heading')        ? { levels: [1, 2, 3, 4, 5, 6] } : false,
          bulletList:     resolvedButtons.includes('bulletList')     ? {} : false,
          orderedList:    resolvedButtons.includes('orderedList')    ? {} : false,
          blockquote:     resolvedButtons.includes('blockquote')     ? {} : false,
          horizontalRule: resolvedButtons.includes('horizontalRule') ? {} : false,
          hardBreak:      resolvedButtons.includes('hardBreak')      ? {} : false,
          code:           resolvedButtons.includes('code')           ? {} : false,
          link: resolvedButtons.includes('link') ? {
            openOnClick: false,
            HTMLAttributes: {
              target: '_blank',
              rel: 'noopener noreferrer',
            },
            defaultProtocol: 'https',
            protocols: ['http', 'https', 'mailto'],
          } : false,
          // Disable StarterKit's dropcursor — we register our own below
          dropcursor: false,
          // Disable StarterKit's image — we use ImageResize instead
          image: false,
        }),
        Placeholder.configure({
          placeholder: props.field.placeholder ?? '',
        }),
        // Use ImageResize (extends Image) instead of plain Image for drag-to-resize handles
        ImageResize,
        FileHandler.configure({
          allowedMimeTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
          onPaste: (editor, files) => {
            files.forEach((file) => uploadImage(editor, file))
          },
          onDrop: (editor, files) => {
            files.forEach((file) => uploadImage(editor, file))
          },
        }),
        Dropcursor.configure({
          color: '#3b82f6',
          width: 2,
        }),
        TextStyle,
        ...(resolvedButtons.includes('textAlign') ? [
          TextAlign.configure({
            types: ['heading', 'paragraph'],
          }),
        ] : []),
        ...(resolvedButtons.includes('color') ? [
          Color,
        ] : []),
        ...(resolvedButtons.includes('highlight') ? [
          Highlight.configure({
            multicolor: true,
          }),
        ] : []),
        ...(resolvedButtons.includes('subscript') ? [Subscript] : []),
        ...(resolvedButtons.includes('superscript') ? [Superscript] : []),
        ...(resolvedButtons.includes('table') ? [
          Table.configure({
            resizable: true,
          }),
          TableRow,
          TableCell,
          TableHeader,
        ] : []),
        SnippetBlock,
        FloatControl,
        ...customExtensions,
      ],
      editable: !props.field.readonly,
      onCreate: ({ editor }) => {
        if (props.field.value) {
          editor.commands.setContent(props.field.value, false)
        }
      },
    })

    return { editor, uploadImage, customButtons }
  },

  data() {
    return {
      showLinkPopup: false,
      linkUrl: '',
      linkNewTab: true,
      showHighlightPicker: false,
      showColorPicker: false,
      showTableMenu: false,
      showSnippetMenu: false,
      highlightColors: [
        '#fef08a', '#bbf7d0', '#bfdbfe', '#fecaca', '#e9d5ff',
        '#fed7aa', '#d1d5db',
      ],
      textColors: [
        '#000000', '#434343', '#666666', '#999999',
        '#ef4444', '#f97316', '#eab308', '#22c55e',
        '#3b82f6', '#8b5cf6', '#ec4899', '#14b8a6',
      ],
    }
  },

  computed: {
    buttons() {
      return resolveButtons(
        this.field.preset ?? 'standard',
        this.field.withButtons ?? [],
        this.field.withoutButtons ?? [],
      )
    },
    snippets() {
      return this.field.snippets ?? []
    },
  },

  mounted() {
    document.addEventListener('click', this.closePopups)
  },

  beforeUnmount() {
    document.removeEventListener('click', this.closePopups)
  },

  methods: {
    btnClass(isActive) {
      return isActive
        ? 'bg-primary-500 text-white p-2 rounded'
        : 'bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 p-2 rounded'
    },

    closePopups(event) {
      if (!event.target.closest('.relative')) {
        this.showHighlightPicker = false
        this.showColorPicker = false
        this.showTableMenu = false
        this.showSnippetMenu = false
      }
    },

    insertSnippet(html) {
      this.editor.chain().focus().insertContent(prepareSnippetHtml(html)).run()
      this.showSnippetMenu = false
    },

    applyHighlight(color) {
      this.editor.chain().focus().toggleHighlight({ color }).run()
      this.showHighlightPicker = false
    },

    removeHighlight() {
      this.editor.chain().focus().unsetHighlight().run()
      this.showHighlightPicker = false
    },

    applyColor(color) {
      this.editor.chain().focus().setColor(color).run()
      this.showColorPicker = false
    },

    removeColor() {
      this.editor.chain().focus().unsetColor().run()
      this.showColorPicker = false
    },

    insertTable() {
      this.editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run()
      this.showTableMenu = false
    },

    openImagePicker() {
      this.$refs.imageInput.click()
    },

    handleImageSelect(event) {
      const file = event.target.files[0]
      if (file && this.editor) {
        this.uploadImage(this.editor, file)
      }
      event.target.value = ''
    },

    /**
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.value = this.field.value || ''
      if (this.editor) {
        this.editor.commands.setContent(this.value, false)
      }
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      if (this.editor && !this.editor.isEmpty) {
        this.value = this.editor.getHTML()
      } else {
        this.value = ''
      }
      formData.append(this.fieldAttribute, this.value)
    },

    /**
     * Toggle the link insertion popup.
     */
    toggleLinkPopup() {
      if (this.showLinkPopup) {
        this.showLinkPopup = false
        return
      }

      // Pre-fill URL if cursor is on an existing link
      if (this.editor.isActive('link')) {
        this.linkUrl = this.editor.getAttributes('link').href || ''
        this.linkNewTab = this.editor.getAttributes('link').target === '_blank'
      } else {
        this.linkUrl = ''
        this.linkNewTab = true
      }

      this.showLinkPopup = true
    },

    /**
     * Apply the link to the current selection.
     */
    applyLink() {
      if (!this.linkUrl) {
        this.editor.chain().focus().unsetLink().run()
      } else {
        this.editor
          .chain()
          .focus()
          .setLink({
            href: this.linkUrl,
            target: this.linkNewTab ? '_blank' : null,
          })
          .run()
      }
      this.showLinkPopup = false
    },
  },
}
</script>
