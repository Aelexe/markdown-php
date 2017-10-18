<?php
declare(strict_types=1);

require_once __DIR__ . "/../src/markdown.php";

use PHPUnit\Framework\TestCase;
use Aelchemy\Markdown;

/**
 * @covers Email
 */
final class MarkdownTest extends TestCase
{
    public function testHeadersCanBeParsed()
    {
        $this->assertEquals('<h1>Header One</h1>', Markdown::parseMarkdown('# Header One'));
        $this->assertEquals('<h2>Header Two</h2>', Markdown::parseMarkdown('## Header Two'));
        $this->assertEquals('<h3>Header Three</h3>', Markdown::parseMarkdown('### Header Three'));
        $this->assertEquals('<h4>Header Four</h4>', Markdown::parseMarkdown('#### Header Four'));
        $this->assertEquals('<h5>Header Five</h5>', Markdown::parseMarkdown('##### Header Five'));
        $this->assertEquals('<h6>Header Six</h6>', Markdown::parseMarkdown('###### Header Six'));
    }

    public function testHeaderFalsePositivesArentParsed()
    {
        $this->assertEquals('<p> # Header With Leading Space</p>', Markdown::parseMarkdown(' # Header With Leading Space'));
        $this->assertEquals('<p>#Header With No Space</p>', Markdown::parseMarkdown('#Header With No Space'));
        $this->assertEquals('<h1>Header With a #Header</h1>', Markdown::parseMarkdown('# Header With a #Header'));
    }

    public function testItalicAndBoldCanBeParsed()
    {
        $this->assertEquals('<p><span class="italic">Hello</span></p>', Markdown::parseMarkdown('*Hello*'));
        $this->assertEquals('<p><span class="bold">Hello</span></p>', Markdown::parseMarkdown('**Hello**'));
        $this->assertEquals('<p><span class="italic bold">Hello</span></p>', Markdown::parseMarkdown('***Hello***'));
        $this->assertEquals('<p><span class="italic">Multiple</span> <span class="italic">italics</span></p>', Markdown::parseMarkdown('*Multiple* *italics*'));
        $this->assertEquals('<p><span class="bold">Multiple</span> <span class="bold">bolds</span></p>', Markdown::parseMarkdown('**Multiple** **bolds**'));
        $this->assertEquals('<p><span class="italic bold">Multiple</span> <span class="italic bold">both</span></p>', Markdown::parseMarkdown('***Multiple*** ***both***'));
        $this->assertEquals('<p><span class="italic">A convoluted <span class="bold">mix</span></span></p>', Markdown::parseMarkdown('*A convoluted **mix***'));
    }

    public function testLinksCanBeParsed()
    {
        $this->assertEquals('<p>A paragraph with a <a href="www.google.com">simple link</a>.</p>', Markdown::parseMarkdown('A paragraph with a [simple link](www.google.com).'));
        $this->assertEquals('<p>A paragraph with a <a href="www.google.com" title="Googles Page">titled link</a>.</p>', Markdown::parseMarkdown('A paragraph with a [titled link](www.google.com "Googles Page").'));
    }

    public function testImagesCanBeParsed()
    {
        $this->assertEquals('<img src="www.google.com/image.png" alt="The Image">', Markdown::parseMarkdown('![The Image](www.google.com/image.png)'));
        $this->assertEquals('<img src="www.google.com/image.png" alt="Titled Image" title="The Title">', Markdown::parseMarkdown('![Titled Image](www.google.com/image.png "The Title")'));
    }

    public function testBlockquotesCanBeParsed()
    {
        $this->assertEquals('<blockquote><p>This is a one line blockquote.</p></blockquote>', Markdown::parseMarkdown('> This is a one line blockquote.'));
        $this->assertEquals('<blockquote><p>This is a multi<br>line blockquote.</p></blockquote>', Markdown::parseMarkdown("> This is a multi\n> line blockquote."));
        $this->assertEquals('<blockquote><p>This is two</p></blockquote><blockquote><p>separate blockquotes.</p></blockquote>', Markdown::parseMarkdown("> This is two\n\n> separate blockquotes."));
        $this->assertEquals('<p>This is a paragraph, followed by blockquotes.</p><blockquote><p>This is two</p></blockquote><blockquote><p>separate blockquotes.</p></blockquote>', Markdown::parseMarkdown("This is a paragraph, followed by blockquotes.\n\n> This is two\n\n> separate blockquotes."));
    }
}
