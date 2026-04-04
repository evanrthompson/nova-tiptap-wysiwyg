<?php

namespace Evanrthompson\NovaTiptapWysiwyg;

use Laravel\Nova\Fields\Field;

class NovaTiptapWysiwyg extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-tiptap-wysiwyg';

    /**
     * Create a new field instance.
     */
    public function __construct(mixed $name, mixed $attribute = null, ?callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $this->withMeta([
            'placeholder' => null,
            'uploadUrl' => url('nova-vendor/nova-tiptap-wysiwyg/upload'),
            'maxUploadSize' => config('nova-tiptap-wysiwyg.max_upload_size', 10240),
            'preset' => config('nova-tiptap-wysiwyg.preset', 'standard'),
            'withButtons' => [],
            'withoutButtons' => [],
            'detailCss' => $this->resolveDetailCss(),
            'snippets' => config('nova-tiptap-wysiwyg.snippets', []),
        ]);

        $this->fillUsing(function ($request, $model, $attribute, $requestAttribute) {
            $value = $request->input($requestAttribute);

            if (is_string($value) && $value !== '') {
                $value = $this->sanitizeHtml($value);
            }

            $model->{$attribute} = is_string($value) && $value !== '' ? $value : null;
        });
    }

    /**
     * Set the placeholder text shown in an empty editor.
     */
    public function placeholder(\Stringable|string|null $text): static
    {
        parent::placeholder($text);

        return $this->withMeta(['placeholder' => $text]);
    }

    /**
     * Set the toolbar preset profile.
     * Supported: 'basic', 'standard', 'full'
     */
    public function preset(string $preset): static
    {
        return $this->withMeta(['preset' => $preset]);
    }

    /**
     * Add buttons to the resolved toolbar set on top of the preset.
     *
     * @param  string[]  $buttons
     */
    public function withButtons(array $buttons): static
    {
        return $this->withMeta(['withButtons' => $buttons]);
    }

    /**
     * Remove buttons from the resolved toolbar set.
     *
     * @param  string[]  $buttons
     */
    public function withoutButtons(array $buttons): static
    {
        return $this->withMeta(['withoutButtons' => $buttons]);
    }

    /**
     * Set the HTML snippets available in the toolbar dropdown.
     * Replaces the global config snippets for this field instance.
     *
     * Each snippet: ['label' => 'Display Name', 'html' => '<div>...</div>']
     *
     * @param  array<int, array{label: string, html: string}>  $snippets
     */
    public function snippets(array $snippets): static
    {
        return $this->withMeta(['snippets' => $snippets]);
    }

    /**
     * Set the CSS path injected into the detail view iframe.
     */
    public function detailCss(?string $url): static
    {
        return $this->withMeta(['detailCss' => $url]);
    }

    /**
     * Resolve the detail CSS URL from config.
     * 'default' serves the bundled wysiwyg.css via the package route.
     * null disables the stylesheet. Any other value is passed through as-is.
     */
    private function resolveDetailCss(): ?string
    {
        $value = config('nova-tiptap-wysiwyg.detail_css', 'default');

        if ($value === 'default') {
            return url('nova-vendor/nova-tiptap-wysiwyg/css/wysiwyg.css');
        }

        return $value;
    }

    /**
     * Strip href/src attributes with non-allowlisted protocols (XSS defence-in-depth).
     * Allows: https://, http://, mailto:, tel:, and relative paths (/ or #).
     */
    private function sanitizeHtml(string $html): string
    {
        return preg_replace(
            '/\s(?:href|src)=["\'](?!https?:\/\/|mailto:|tel:|\/|#)[^"\']*["\']/i',
            '',
            $html
        );
    }
}
