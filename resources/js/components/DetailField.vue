<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <iframe
        v-if="field.value"
        ref="iframeRef"
        :srcdoc="iframeSrc"
        sandbox="allow-same-origin"
        scrolling="no"
        class="nova-tiptap-iframe w-full border-0 rounded"
        :style="{ height: iframeHeight + 'px', minHeight: '60px' }"
        @load="onIframeLoad"
      />
      <span v-else class="text-gray-400">&mdash;</span>
    </template>
  </PanelItem>
</template>

<script>
export default {
  props: ['index', 'resource', 'resourceName', 'resourceId', 'field'],

  data() {
    return {
      iframeHeight: 100,
      isDark: document.documentElement.classList.contains('dark'),
    }
  },

  computed: {
    iframeSrc() {
      const base = `<base href="${window.location.origin}/">`
      const css = this.field.detailCss
        ? `<link rel="stylesheet" href="${this.field.detailCss}">`
        : ''
      const bg = this.isDark ? '#111827' : '#ffffff'
      const baseStyle = `<style>html,body{background:${bg}}body{margin:0;padding:8px;font-family:sans-serif}</style>`
      // Dark-mode text overrides — emitted AFTER user's detailCss so they win
      // over light-mode color rules in wysiwyg.css. Mirrors edit-view palette.
      const darkOverride = this.isDark
        ? `<style>body,p,li,td,th,span,div{color:#e5e7eb}h1,h2,h3,h4,h5{color:#f3f4f6}h6{color:#9ca3af}a{color:#60a5fa}blockquote{background:#1e1b4b;color:#a5b4fc;border-left-color:#818cf8}code{background:#374151;border-color:#4b5563;color:#f87171}hr{border-top-color:#4b5563}h2{border-bottom-color:#4b5563}</style>`
        : ''
      const content = this.field.value ?? ''
      return `<!DOCTYPE html><html><head><meta charset="utf-8">${base}${baseStyle}${css}${darkOverride}</head><body>${content}</body></html>`
    },
  },

  mounted() {
    this.themeObserver = new MutationObserver(() => {
      this.isDark = document.documentElement.classList.contains('dark')
    })
    this.themeObserver.observe(document.documentElement, {
      attributes: true,
      attributeFilter: ['class'],
    })
  },

  beforeUnmount() {
    this.themeObserver?.disconnect()
    this.resizeObserver?.disconnect()
  },

  methods: {
    onIframeLoad() {
      const body = this.$refs.iframeRef?.contentDocument?.body
      if (!body) {
        return
      }
      this.iframeHeight = body.scrollHeight
      this.resizeObserver?.disconnect()
      this.resizeObserver = new ResizeObserver(() => {
        this.iframeHeight =
          this.$refs.iframeRef?.contentDocument?.body?.scrollHeight ??
          this.iframeHeight
      })
      this.resizeObserver.observe(body)
    },
  },
}
</script>
