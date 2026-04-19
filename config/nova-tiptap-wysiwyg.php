<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Upload Disk
    |--------------------------------------------------------------------------
    | The Laravel filesystem disk to use for image uploads.
    */
    'upload_disk' => 'public',

    /*
    |--------------------------------------------------------------------------
    | Upload Path
    |--------------------------------------------------------------------------
    | The path within the disk where uploaded images are stored.
    */
    'upload_path' => 'wysiwyg',

    /*
    |--------------------------------------------------------------------------
    | Max Upload Size
    |--------------------------------------------------------------------------
    | Maximum allowed upload file size in kilobytes. Default: 10MB (10240 KB).
    | This value is passed directly to Laravel's `max:` validation rule.
    */
    'max_upload_size' => 10240,

    /*
    |--------------------------------------------------------------------------
    | Max Image Dimensions
    |--------------------------------------------------------------------------
    | Maximum width and height (in pixels) for uploaded images. Images larger
    | than this will be resized to fit within this bounding box while keeping
    | their aspect ratio. SVGs are not resized. Set to null to disable resizing.
    */
    'max_image_width' => 1280,
    'max_image_height' => 1280,

    /*
    |--------------------------------------------------------------------------
    | Snippets
    |--------------------------------------------------------------------------
    | Predefined HTML blocks that content editors can insert via a toolbar
    | dropdown. Each snippet has a label (shown in the dropdown) and html
    | (inserted at the cursor). These are the global defaults — individual
    | field instances can override or extend via ->snippets().
    |
    | Use the `{{content}}` placeholder inside a snippet's html to fold the
    | user's selected text into the snippet on insert. If the user inserts
    | the snippet with nothing selected, `{{content}}` is replaced with the
    | literal string "Snippet Content" (editable after insertion).
    |
    | Example:
    |   ['label' => 'Callout', 'html' => '<div class="callout p-4">{{content}}</div>'],
    */
    'snippets' => [
        [
            'label' => 'Callout Box',
            'html' => '<div class="wysiwyg-callout"><p><strong>Note:</strong> {{content}}</p></div>',
        ],
        [
            'label' => 'Info Card',
            'html' => '<div class="wysiwyg-info-card"><h3>Info Title</h3><p>{{content}}</p></div>',
        ],
        [
            'label' => 'Two Column Layout',
            'html' => '<table><tr><td><h4>Left Column</h4><p>{{content}}</p></td><td><h4>Right Column</h4><p>Right column content here.</p></td></tr></table>',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Toolbar Preset
    |--------------------------------------------------------------------------
    | Default toolbar preset profile for all field instances.
    | Supported: 'basic', 'standard', 'full'
    */
    'preset' => 'standard',

    /*
    |--------------------------------------------------------------------------
    | Heading Levels
    |--------------------------------------------------------------------------
    | Which heading levels appear as toolbar buttons when the 'heading' button
    | is enabled. Any subset of [1, 2, 3, 4, 5, 6]. Heading content at all six
    | levels continues to render regardless of what shows in the toolbar.
    */
    'heading_levels' => [1, 2, 3],

    /*
    |--------------------------------------------------------------------------
    | Detail CSS
    |--------------------------------------------------------------------------
    | URL to a CSS file injected into the detail iframe.
    | Set to null to use no custom stylesheet.
    */
    'detail_css' => 'default',
];
