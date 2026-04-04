<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <iframe
        v-if="field.value"
        ref="iframeRef"
        :srcdoc="iframeSrc"
        sandbox="allow-same-origin"
        scrolling="no"
        class="nova-tiptap-iframe w-full border-0"
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
    return { iframeHeight: 100 }
  },

  computed: {
    iframeSrc() {
      const base = `<base href="${window.location.origin}/">`
      const css = this.field.detailCss
        ? `<link rel="stylesheet" href="${this.field.detailCss}">`
        : ''
      const content = this.field.value ?? ''
      return `<!DOCTYPE html><html><head><meta charset="utf-8">${base}${css}<style>body{margin:0;padding:8px;font-family:sans-serif}</style></head><body>${content}</body></html>`
    },
  },

  methods: {
    onIframeLoad() {
      const body = this.$refs.iframeRef?.contentDocument?.body
      if (!body) {
        return
      }
      this.iframeHeight = body.scrollHeight
      const ro = new ResizeObserver(() => {
        this.iframeHeight =
          this.$refs.iframeRef?.contentDocument?.body?.scrollHeight ??
          this.iframeHeight
      })
      ro.observe(body)
    },
  },
}
</script>
