<?php

namespace FlexWave\Wysiwyg\Helpers;

class WysiwygHelper
{
    public function __construct(protected array $config) {}

    /**
     * Sanitize HTML output from the editor.
     * Strips disallowed tags and attributes.
     */
    public function sanitize(string $html): string
    {
        $allowedTags = $this->config['allowed_tags'] ?? [];
        $allowedAttrs = $this->config['allowed_attributes'] ?? [];

        if (empty($allowedTags)) {
            return strip_tags($html);
        }

        if (! extension_loaded('dom')) {
            return strip_tags($html, '<' . implode('><', $allowedTags) . '>');
        }

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $this->sanitizeDomNode($dom, $allowedTags, $allowedAttrs);

        return $dom->saveHTML() ?: '';
    }

    /**
     * Recursively remove disallowed nodes and unsafe attributes.
     */
    protected function sanitizeDomNode(\DOMNode $node, array $allowedTags, array $allowedAttrs): void
    {
        for ($index = $node->childNodes->length - 1; $index >= 0; $index--) {
            $child = $node->childNodes->item($index);

            if (! $child) {
                continue;
            }

            if ($child instanceof \DOMElement) {
                $tag = strtolower($child->nodeName);

                if (! in_array($tag, $allowedTags, true)) {
                    $node->removeChild($child);
                    continue;
                }

                $allowed = array_values(array_unique(array_merge($allowedAttrs['*'] ?? [], $allowedAttrs[$tag] ?? [])));

                if ($child->hasAttributes()) {
                    $attributesToRemove = [];

                    foreach ($child->attributes as $attribute) {
                        $name = strtolower($attribute->name);
                        $value = trim(strtolower($attribute->value));

                        if (! in_array($name, $allowed, true)) {
                            $attributesToRemove[] = $attribute->name;
                            continue;
                        }

                        if (in_array($name, ['href', 'src'], true) && str_starts_with($value, 'javascript:')) {
                            $attributesToRemove[] = $attribute->name;
                        }
                    }

                    foreach ($attributesToRemove as $attributeName) {
                        $child->removeAttribute($attributeName);
                    }
                }

                $this->sanitizeDomNode($child, $allowedTags, $allowedAttrs);
                continue;
            }

            if ($child instanceof \DOMComment) {
                $node->removeChild($child);
            }
        }
    }

    /**
     * Convert editor HTML to plain text.
     */
    public function toText(string $html): string
    {
        return strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $html));
    }

    /**
     * Generate a safe excerpt from editor HTML.
     */
    public function excerpt(string $html, int $length = 160, string $suffix = '...'): string
    {
        $text = $this->toText($html);
        $text = preg_replace('/\s+/', ' ', trim($text));

        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $length)) . $suffix;
    }

    /**
     * Get word count from editor HTML.
     */
    public function wordCount(string $html): int
    {
        $text = $this->toText($html);
        return str_word_count(strip_tags($text));
    }

    /**
     * Get the config value.
     */
    public function config(string $key, mixed $default = null): mixed
    {
        return data_get($this->config, $key, $default);
    }
}
