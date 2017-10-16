<?php
declare(strict_types=1);

require_once __DIR__ . "/../src/markdown.php";

use PHPUnit\Framework\TestCase;

/**
 * @covers Email
 */
final class MarkdownTest extends TestCase
{
    public function testHeadersCanBeParsed()
    {
        $this->assertEquals('<h1>Header One</h1>', parseMarkdown('# Header One'));
        $this->assertEquals('<h2>Header Two</h2>', parseMarkdown('## Header Two'));
        $this->assertEquals('<h3>Header Three</h3>', parseMarkdown('### Header Three'));
        $this->assertEquals('<h4>Header Four</h4>', parseMarkdown('#### Header Four'));
        $this->assertEquals('<h5>Header Five</h5>', parseMarkdown('##### Header Five'));
        $this->assertEquals('<h6>Header Six</h6>', parseMarkdown('###### Header Six'));
    }

    public function testHeaderFalsePositivesArentParsed()
    {
        $this->assertEquals('<p> # Header With Leading Space</p>', parseMarkdown(' # Header With Leading Space'));
        $this->assertEquals('<p>#Header With No Space</p>', parseMarkdown('#Header With No Space'));
        $this->assertEquals('<h1>Header With a #Header</h1>', parseMarkdown('# Header With a #Header'));
    }

    public function testItalicAndBoldCanBeParsed()
    {
        $this->assertEquals('<p><span class="italic">Hello</span></p>', parseMarkdown('*Hello*'));
        $this->assertEquals('<p><span class="bold">Hello</span></p>', parseMarkdown('**Hello**'));
        $this->assertEquals('<p><span class="italic bold">Hello</span></p>', parseMarkdown('***Hello***'));
        $this->assertEquals('<p><span class="italic">Multiple</span> <span class="italic">italics</span></p>', parseMarkdown('*Multiple* *italics*'));
        $this->assertEquals('<p><span class="bold">Multiple</span> <span class="bold">bolds</span></p>', parseMarkdown('**Multiple** **bolds**'));
        $this->assertEquals('<p><span class="italic bold">Multiple</span> <span class="italic bold">both</span></p>', parseMarkdown('***Multiple*** ***both***'));
        $this->assertEquals('<p><span class="italic">A convoluted <span class="bold">mix</span></span></p>', parseMarkdown('*A convoluted **mix***'));
    }

    public function testLinksCanBeParsed()
    {
        $this->assertEquals('<p>A paragraph with a <a href="www.google.com">simple link</a>.</p>', parseMarkdown('A paragraph with a [simple link](www.google.com).'));
        $this->assertEquals('<p>A paragraph with a <a href="www.google.com" title="Googles Page">titled link</a>.</p>', parseMarkdown('A paragraph with a [titled link](www.google.com "Googles Page").'));
    }

    public function testImagesCanBeParsed()
    {
        $this->assertEquals('<img src="www.google.com/image.png" alt="The Image">', parseMarkdown('![The Image](www.google.com/image.png)'));
        $this->assertEquals('<img src="www.google.com/image.png" alt="Titled Image" title="The Title">', parseMarkdown('![Titled Image](www.google.com/image.png "The Title")'));
    }

    public function testQuotesCanbeParsed()
    {
        $this->assertEquals('<blockquote><p>This is a one line blockquote.</p></blockquote>', parseMarkdown('> This is a one line blockquote.'));
        $this->assertEquals('<blockquote><p>This is a multi<br>line blockquote.</p></blockquote>', parseMarkdown("> This is a multi\nline blockquote."));
    }
}
