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
