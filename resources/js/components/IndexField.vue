<template>
  <span class="text-sm text-gray-700 dark:text-gray-300">{{ excerpt }}</span>
</template>

<script>
import { FieldValue } from 'laravel-nova'

export default {
  mixins: [FieldValue],
  props: ['resourceName', 'field'],

  computed: {
    excerpt() {
      if (!this.field.value) {
        return ''
      }

      // Strip HTML tags
      const text = this.field.value.replace(/<[^>]*>/g, '')

      return text.length > 100 ? text.slice(0, 100) + '\u2026' : text
    },
  },
}
</script>
