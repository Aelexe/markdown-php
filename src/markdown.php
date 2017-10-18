<?php

namespace Aelchemy;

class Markdown
{
    /**
     * Parses markdown content, returning it as HTML.
     *
     * @api
     * @param string Markdown content to parse as HTML.
     * @return string Parsed markdown content as HTML.
     */
    public static function parseMarkdown(string $markdown)
    {
        // Separate markdown content into lines.
        $content = explode("\n", $markdown);

        $paragraph = false;
        $blockquote = false;

        // Iterate over the lines.
        foreach ($content as &$line) {
            if (preg_match('/^#+ /', $line)) {
                // Parse header lines.
                if (preg_match('/^# (.*)$/', $line)) {
                    $line = '<h1>' . substr($line, 2) . '</h1>';
                } elseif (preg_match('/^## (.*)$/', $line)) {
                    $line = '<h2>' . substr($line, 3) . '</h2>';
                } elseif (preg_match('/^### (.*)$/', $line)) {
                    $line = '<h3>' . substr($line, 4) . '</h3>';
                } elseif (preg_match('/^#### (.*)$/', $line)) {
                    $line = '<h4>' . substr($line, 5) . '</h4>';
                } elseif (preg_match('/^##### (.*)$/', $line)) {
                    $line = '<h5>' . substr($line, 6) . '</h5>';
                } elseif (preg_match('/^###### (.*)$/', $line)) {
                    $line = '<h6>' . substr($line, 7) . '</h6>';
                }
            } elseif (preg_match('/^!\\[.+\\]\\(.+\\)/', $line)) {
                $line = trim($line);
                $line = preg_replace('/^!(?<!\\\\)\\[(.+?)(?<!\\\\)\\](?<!\\\\)\\((\\S+?) "(.+?)"(?<!\\\\)\\)$/', '<img src="${2}" alt="${1}" title="${3}">', $line);
                $line = preg_replace('/^!(?<!\\\\)\\[(.+?)(?<!\\\\)\\](?<!\\\\)\\((\\S+?)(?<!\\\\)\\)$/', '<img src="${2}" alt="${1}">', $line);
            } else {
                // Handle open and close of blockquotes.
                $is_blockquote = preg_match('/^> .+/', $line);
                if ($is_blockquote) {
                    $line = substr($line, 2);
                }

                if (!trim($line) == '') {
                    // Parse line content.
                    if (!$paragraph) {
                        // If the line is the start of a paragraph, open it.
                        $line = '<p>' . $line;
                        $paragraph = true;
                    } else {
                        // Otherwise prepend a break tag.
                        $line = '<br>' . $line;
                    }
                    $line = self::parseInlineMarkdown($line);
                } else {
                    // Parse empty line.
                    if ($paragraph) {
                        // If the new line is the end of a paragraph, close it.
                        $line = '</p>';
                        $paragraph = false;

                        if ($blockquote && !$is_blockquote) {
                            $blockquote = false;
                            $line = $line . '</blockquote>';
                        }
                    }
                }

                if (!$blockquote && $is_blockquote) {
                    $blockquote = true;
                    $line = '<blockquote>' . $line;
                }
            }
        }

        // Concatenate parsed content lines.
        $content = implode($content);

        if ($paragraph) {
            // If the final line was an open paragraph, close it.
            $content = $content . '</p>';
        }
        if ($blockquote) {
            // If the final line was a blockquote, close it.
            $content = $content . '</blockquote>';
        }

        return $content;
    }

    /**
     * Parses a markdown content line, returning it with bold, italics, links and images as HTML.
     *
     * @param string Markdown content to parse as HTML.
     * @return string Parsed markdown content as HTML.
     */
    private static function parseInlineMarkdown(string $markdown_line)
    {
        // Bold and italics.
        $markdown_line = preg_replace('/(?<!\\\\)\\*{3}(.+?)(?<!\\\\)\\*{3}/', '<span class="italic bold">${1}</span>', $markdown_line);
        $markdown_line = preg_replace('/(?<!\\\\)\\*{2}(.+?)(?<!\\\\)\\*{2}/', '<span class="bold">${1}</span>', $markdown_line);
        $markdown_line = preg_replace('/(?<!\\\\)\\*(.+?)(?<!\\\\)\\*/', '<span class="italic">${1}</span>', $markdown_line);

        // links
        $markdown_line = preg_replace('/(?<!\\\\)\\[(.+?)(?<!\\\\)\\](?<!\\\\)\\((\\S+?) "(.+?)"(?<!\\\\)\\)/', '<a href="${2}" title="${3}">${1}</a>', $markdown_line);
        $markdown_line = preg_replace('/(?<!\\\\)\\[(.+?)(?<!\\\\)\\](?<!\\\\)\\((\\S+?)(?<!\\\\)\\)/', '<a href="${2}">${1}</a>', $markdown_line);

        return $markdown_line;
    }
}
