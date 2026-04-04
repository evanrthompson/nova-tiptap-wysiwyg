import IndexField from './components/IndexField'
import DetailField from './components/DetailField'
import FormField from './components/FormField'
import PreviewField from './components/PreviewField'

Nova.booting((app, store) => {
  app.component('index-nova-tiptap-wysiwyg', IndexField)
  app.component('detail-nova-tiptap-wysiwyg', DetailField)
  app.component('form-nova-tiptap-wysiwyg', FormField)
  // app.component('preview-nova-tiptap-wysiwyg', PreviewField)
})
