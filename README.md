```
 _   _                   _____ _       _              __        __         _
| \ | | _____   ____ _  |_   _(_)_ __ | |_ __ _ _ __  \ \      / /   _ ___(_) __ _
|  \| |/ _ \ \ / / _` |   | | | | '_ \| __/ _` | '_ \  \ \ /\ / / | | / __| |/ _` |
| |\  | (_) \ V / (_| |   | | | | |_) | || (_| | |_) |  \ V  V /| |_| \__ \ | (_| |
|_| \_|\___/ \_/ \__,_|   |_| |_| .__/ \__\__,_| .__/    \_/\_/  \__, |___/_|\__, |
                                 |_|            |_|               |___/       |___/
```

# Nova Tiptap WYSIWYG

A rich text editor field for [Laravel Nova 5](https://nova.laravel.com) built on [Tiptap v3](https://tiptap.dev) and Vue 3. Drop it into any Nova resource for a fully featured WYSIWYG editing experience with image uploads, HTML snippets, table editing, and more.

## Requirements

- PHP 8.1+
- Laravel 10, 11, 12, or 13
- Laravel Nova 5+
- [Intervention Image](https://image.intervention.io/v3) 3.x (included as a dependency)

## Installation

**1. Add the package via Composer:**

```bash
composer require evanrthompson/nova-tiptap-wysiwyg
```

The service provider is auto-discovered. No manual registration needed.

**2. Publish the config (optional):**

```bash
php artisan vendor:publish --tag=nova-tiptap-wysiwyg-config
```

**3. Build the field assets:**

```bash
cd nova-components/NovaTiptapWysiwyg
npm install && npm run prod
```

**4. Link your storage disk (if using the default `public` disk):**

```bash
php artisan storage:link
```

## Basic Usage

```php
use Evanrthompson\NovaTiptapWysiwyg\NovaTiptapWysiwyg;

public function fields(NovaRequest $request): array
{
    return [
        ID::make()->sortable(),

        NovaTiptapWysiwyg::make('Body', 'body'),
    ];
}
```

That's it. The field works out of the box with a `standard` toolbar preset, image uploads, and HTML sanitization.

## Toolbar Presets

Three built-in presets control which toolbar buttons are displayed:

| Preset     | Buttons                                                                                   |
|------------|-------------------------------------------------------------------------------------------|
| `basic`    | Bold, Italic, Link                                                                        |
| `standard` | Bold, Italic, Underline, Strikethrough, Heading, Bullet List, Ordered List, Blockquote, Link, Image, Float |
| `full`     | All available buttons (see full list below)                                                |

### Set the preset per field:

```php
NovaTiptapWysiwyg::make('Body')->preset('full'),

NovaTiptapWysiwyg::make('Summary')->preset('basic'),
```

### All Available Buttons

`bold` `italic` `underline` `strike` `subscript` `superscript` `heading` `bulletList` `orderedList` `blockquote` `horizontalRule` `hardBreak` `code` `highlight` `color` `textAlign` `table` `link` `image` `snippets` `float`

## Customizing the Toolbar

Use `withButtons()` and `withoutButtons()` to add or remove individual buttons from the resolved preset:

```php
// Start with standard, add table support
NovaTiptapWysiwyg::make('Body')
    ->withButtons(['table', 'code'])

// Start with full, remove features you don't need
NovaTiptapWysiwyg::make('Body')
    ->preset('full')
    ->withoutButtons(['subscript', 'superscript', 'horizontalRule'])
```

## Snippets

Snippets are predefined HTML blocks that editors can insert via the toolbar dropdown. They're useful for callout boxes, cards, multi-column layouts, and other reusable content patterns.

### Per-field snippets:

```php
NovaTiptapWysiwyg::make('Body')->snippets([
    [
        'label' => 'Warning Box',
        'html'  => '<div class="wysiwyg-callout"><p><strong>Warning:</strong> Important note here.</p></div>',
    ],
    [
        'label' => 'Two Columns',
        'html'  => '<table><tr><td><p>Left column</p></td><td><p>Right column</p></td></tr></table>',
    ],
]),
```

### Global default snippets:

Set the `snippets` array in `config/nova-tiptap-wysiwyg.php` to define defaults for all field instances. Per-field `->snippets()` calls replace the global defaults entirely.

### Wrapping selected text with `{{content}}`:

Any snippet whose `html` contains the literal token `{{content}}` folds the user's current selection into the snippet on insert. If the user inserts the snippet with nothing selected, `{{content}}` is replaced with the string `Snippet Content` so there's always something to edit.

```php
NovaTiptapWysiwyg::make('Body')->snippets([
    [
        'label' => 'Blockquote',
        'html'  => '<blockquote><p>{{content}}</p></blockquote>',
    ],
    [
        'label' => 'Warning',
        'html'  => '<div class="wysiwyg-callout"><p><strong>Warning:</strong> {{content}}</p></div>',
    ],
]),
```

Typical workflow: the editor highlights a paragraph, opens the Snippets dropdown, picks "Blockquote", and the selected paragraph is wrapped inside the blockquote in place.

## Image Uploads

Image uploads are handled automatically via a built-in API endpoint. Uploaded images are:

- **Validated** — only `jpg`, `jpeg`, `png`, `gif`, and `webp` files are accepted
- **Orientation-corrected** — EXIF rotation is applied so photos display correctly
- **Resized** — images exceeding the configured max dimensions are scaled down
- **Stripped** — all EXIF metadata is removed for privacy
- **UUID-named** — filenames are random UUIDs to prevent collisions and path traversal

Images can be uploaded via the toolbar button, drag-and-drop, or paste.

### Upload configuration:

```php
// config/nova-tiptap-wysiwyg.php

'upload_disk'      => 'public',    // Laravel filesystem disk
'upload_path'      => 'wysiwyg',   // Directory within the disk
'max_upload_size'  => 10240,       // Max file size in KB (10 MB)
'max_image_width'  => 1280,        // Max width in px (null to disable)
'max_image_height' => 1280,        // Max height in px (null to disable)
```

The upload endpoint requires Nova authentication — it is protected by the `nova`, `nova.auth`, and `Authorize` middleware.

## Float Controls

The float toolbar group lets editors position images and snippet blocks with CSS float. Four options are available:

- **Float Left** — content wraps around the right side
- **Float Right** — content wraps around the left side
- **Center** — centered block with no wrapping
- **None** — removes float, returns to default flow

Include `float` in the toolbar (it's included in `standard` and `full` presets by default).

## Detail View CSS

The detail (read-only) view renders content inside an iframe to prevent Nova's styles from affecting your content. A bundled stylesheet (`wysiwyg.css`) is injected by default, ensuring consistent rendering between the editor and the detail view.

### Override the detail CSS:

```php
// Use your own stylesheet
NovaTiptapWysiwyg::make('Body')->detailCss('/css/my-content-styles.css'),

// Disable the stylesheet entirely
NovaTiptapWysiwyg::make('Body')->detailCss(null),
```

Or set the default globally in the config:

```php
// config/nova-tiptap-wysiwyg.php

'detail_css' => 'default',                   // Use the bundled wysiwyg.css
'detail_css' => '/css/my-styles.css',         // Use a custom URL
'detail_css' => null,                         // No stylesheet
```

## Styling Hooks

Both the edit view and the detail iframe expose ancestor classes so your
overrides can be scoped as loosely or as tightly as you like:

| Class                           | Where                                          |
|---------------------------------|------------------------------------------------|
| `.nova-tiptap-wysiwyg`          | Outer scope on both the edit wrapper and the detail iframe body |
| `.nova-tiptap-wysiwyg-edit`     | Wraps the editor/source-view content area      |
| `.nova-tiptap-wysiwyg-preview`  | Wraps the detail-view content area (available on the iframe *and* inside its body) |

Because the detail iframe body itself carries `.nova-tiptap-wysiwyg` and the
content is wrapped in `.nova-tiptap-wysiwyg-preview`, the same descendant
selector works in any context:

```css
/* Restyle paragraphs in the detail view */
.nova-tiptap-wysiwyg .nova-tiptap-wysiwyg-preview p {
  font-family: "Iowan Old Style", Georgia, serif;
  font-size: 1.125rem;
  line-height: 1.7;
}

/* Restyle H2s only in the edit view */
.nova-tiptap-wysiwyg .nova-tiptap-wysiwyg-edit .tiptap h2 {
  color: #7c3aed;
}

/* Rules that should apply to both */
.nova-tiptap-wysiwyg a { color: #2563eb; }
```

For rules that should appear inside the detail iframe, point `detail_css`
(or `->detailCss(...)`) at a stylesheet that includes them — the same
selectors work there.

## Placeholder Text

```php
NovaTiptapWysiwyg::make('Body')->placeholder('Start writing...'),
```

## Full Configuration Reference

After publishing the config file (`config/nova-tiptap-wysiwyg.php`), you can set app-wide defaults:

| Key                | Default      | Description                                           |
|--------------------|--------------|-------------------------------------------------------|
| `upload_disk`      | `'public'`   | Laravel filesystem disk for image storage              |
| `upload_path`      | `'wysiwyg'`  | Directory within the disk                              |
| `max_upload_size`  | `10240`      | Max upload size in KB                                  |
| `max_image_width`  | `1280`       | Max image width in pixels (null to disable)            |
| `max_image_height` | `1280`       | Max image height in pixels (null to disable)           |
| `preset`           | `'standard'` | Default toolbar preset (`basic`, `standard`, `full`)   |
| `detail_css`       | `'default'`  | CSS URL for detail view iframe (`'default'`, URL, null)|
| `snippets`         | `[...]`      | Array of snippet definitions (label + html)            |

All config values serve as defaults. Every option can be overridden per-field using the fluent API.

## Fluent API Summary

```php
NovaTiptapWysiwyg::make('Content', 'body')
    ->placeholder('Write something...')     // Editor placeholder text
    ->preset('full')                        // Toolbar preset
    ->withButtons(['table'])                // Add buttons to preset
    ->withoutButtons(['strike'])            // Remove buttons from preset
    ->snippets([...])                       // Custom snippet definitions
    ->detailCss('/css/content.css')         // Custom detail view stylesheet
    ->withExtensions(['youtube'])           // Declare expected custom extensions
```

## Extending the Editor

You can register custom Tiptap extensions and toolbar buttons from your host application. This lets you add any Tiptap extension (YouTube embeds, mentions, math blocks, etc.) without forking the package.

### Step 1: Create your extension script

Create a JS file in your host app that registers extensions via `window.NovaTiptapWysiwyg`:

```js
// resources/js/nova/tiptap-extensions.js
import Youtube from '@tiptap/extension-youtube'

// Load-order safe stub — ensures calls work even if this script loads before the field bundle
window.NovaTiptapWysiwyg ??= { _queue: [], registerExtension(...a) { this._queue.push(['registerExtension', a]) }, registerButton(...a) { this._queue.push(['registerButton', a]) }, registerExtensionWithButton(...a) { this._queue.push(['registerExtensionWithButton', a]) } }

// Register an extension with a toolbar button
window.NovaTiptapWysiwyg.registerExtensionWithButton(
  Youtube.configure({ width: 640, height: 480 }),
  {
    name: 'youtube',
    title: 'Insert YouTube Video',
    icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>',
    action: (editor) => {
      const url = prompt('Enter YouTube URL:')
      if (url) editor.commands.setYoutubeVideo({ src: url })
    },
    isActive: (editor) => editor.isActive('youtube'),
  }
)
```

Compile this file with your host app's build tool (Vite, Mix, etc.).

### Step 2: Load the script via Nova

In a service provider, register your compiled script:

```php
use Laravel\Nova\Nova;

public function boot()
{
    Nova::script('my-tiptap-extensions', resource_path('js/nova/tiptap-extensions.js'));
}
```

### Step 3: Declare extensions on the field (optional)

You can optionally declare which custom extensions a field expects. This enables console warnings if an extension was declared but not registered:

```php
NovaTiptapWysiwyg::make('Body')
    ->withExtensions(['youtube'])
    ->preset('standard'),
```

### Registration API

All methods are available on `window.NovaTiptapWysiwyg`:

| Method | Description |
|--------|-------------|
| `registerExtension(extension, options?)` | Register a Tiptap extension |
| `registerButton(buttonDef, options?)` | Register a custom toolbar button |
| `registerExtensionWithButton(extension, buttonDef, options?)` | Register both together |

**Options:** Pass `{ fields: ['body', 'content'] }` to target specific field attributes. Omit to apply globally.

**Button definition:**

```js
{
  name: 'myButton',              // Unique key (required)
  icon: '<svg>...</svg>',        // Inline SVG string (required unless using component)
  title: 'My Button',            // Tooltip text (required)
  action: (editor) => { ... },   // Click handler (required unless using component)
  isActive: (editor) => bool,    // Active state check (optional)
  component: MyVueComponent,     // Vue component for complex UI like dropdowns (optional)
}
```

When using `component`, your Vue component receives `editor` and `btnClass` as props.

### Load Order

Nova may load your script before the field's bundle. The one-line stub at the top of the Step 1 example handles this automatically — it creates a lightweight proxy that queues your registrations and replays them when the real registry initializes. Always include it.

### Simple Button Example (No Build Step)

For scripts that don't need `import` (plain JS loaded via `Nova::script()`), you can register toolbar-only buttons without any build tool:

```js
// resources/js/nova/custom-tiptap-buttons.js (plain JS, no compilation needed)
window.NovaTiptapWysiwyg ??= { _queue: [], registerExtension(...a) { this._queue.push(['registerExtension', a]) }, registerButton(...a) { this._queue.push(['registerButton', a]) }, registerExtensionWithButton(...a) { this._queue.push(['registerExtensionWithButton', a]) } }

window.NovaTiptapWysiwyg.registerButton({
  name: 'clearFormatting',
  title: 'Clear Formatting',
  icon: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4"><path d="M3.27 5L2 6.27l6.97 6.97L6.5 19h3l1.57-3.66L16.73 21 18 19.73 3.27 5zM6 5v.18L8.82 8h2.4l-.72 1.68 2.1 2.1L14.21 8H20V5H6z"/></svg>',
  action: (editor) => editor.chain().focus().clearNodes().unsetAllMarks().run(),
})
```

### Per-Field Targeting

Register extensions or buttons for specific fields only:

```js
// Only add to the 'body' and 'content' fields
window.NovaTiptapWysiwyg.registerButton(
  { name: 'hr', title: 'Divider', icon: '—', action: (e) => e.chain().focus().setHorizontalRule().run() },
  { fields: ['body', 'content'] }
)
```

## Security

- **HTML sanitization** — all saved content is run through a sanitizer that strips `href` and `src` attributes with non-allowlisted protocols (blocks `javascript:`, `data:`, `vbscript:`, etc.)
- **SVG uploads blocked** — SVG files are rejected because they can contain embedded scripts
- **Upload validation** — MIME type, extension, and file size are validated server-side
- **EXIF stripping** — all image metadata is removed before storage
- **UUID filenames** — uploaded files use random UUIDs, preventing path traversal and name collisions
- **Nova auth required** — the upload endpoint is protected by Nova's authentication and authorization middleware

## Building Assets

During development:

```bash
cd nova-components/NovaTiptapWysiwyg
npm run watch
```

For production:

```bash
npm run prod
```

## License

MIT
