<?php
use \Junky\PhpPrettyPrint\Html\HtmlBuilder;

class HtmlBuilderTest extends PHPUnit_Framework_TestCase{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testHtmlBuilderQuoteFlags()
    {
        $htmlBuilderReflection = new ReflectionClass(new HtmlBuilder());
        $quoteFlags = $htmlBuilderReflection->getDefaultProperties()['quoteFlags'];

        $this->assertArrayHasKey('ENT_COMPAT', $quoteFlags);
        $this->assertArrayHasKey('ENT_QUOTES', $quoteFlags);
        $this->assertArrayHasKey('ENT_NOQUOTES', $quoteFlags);
        $this->assertArrayHasKey('ENT_IGNORE', $quoteFlags);
        $this->assertArrayHasKey('ENT_SUBSTITUTE', $quoteFlags);
        $this->assertArrayHasKey('ENT_DISALLOWED', $quoteFlags);
        $this->assertArrayHasKey('ENT_HTML401', $quoteFlags);
        $this->assertArrayHasKey('ENT_XML1', $quoteFlags);
        $this->assertArrayHasKey('ENT_XHTML', $quoteFlags);
        $this->assertArrayHasKey('ENT_HTML5', $quoteFlags);

        $this->assertContains(2, $quoteFlags);
        $this->assertContains(3, $quoteFlags);
        $this->assertContains(0, $quoteFlags);
        $this->assertContains(4, $quoteFlags);
        $this->assertContains(8, $quoteFlags);
        $this->assertContains(128, $quoteFlags);
        $this->assertContains(16, $quoteFlags);
        $this->assertContains(32, $quoteFlags);
        $this->assertContains(48, $quoteFlags);

        $this->assertEquals(ENT_COMPAT, $quoteFlags['ENT_COMPAT']);
        $this->assertEquals(ENT_QUOTES, $quoteFlags['ENT_QUOTES']);
        $this->assertEquals(ENT_NOQUOTES, $quoteFlags['ENT_NOQUOTES']);
        $this->assertEquals(ENT_IGNORE, $quoteFlags['ENT_IGNORE']);
        $this->assertEquals(ENT_SUBSTITUTE, $quoteFlags['ENT_SUBSTITUTE']);
        $this->assertEquals(ENT_DISALLOWED, $quoteFlags['ENT_DISALLOWED']);
        $this->assertEquals(ENT_HTML401, $quoteFlags['ENT_HTML401']);
        $this->assertEquals(ENT_XML1, $quoteFlags['ENT_XML1']);
        $this->assertEquals(ENT_XHTML, $quoteFlags['ENT_XHTML']);
        $this->assertEquals(ENT_HTML5, $quoteFlags['ENT_HTML5']);
    }

    public function testHtmlBuilderEscape()
    {
        $method = new ReflectionMethod(\Junky\PhpPrettyPrint\Html\HtmlBuilder::class, 'escape');
        $method->setAccessible(true);

        $string = "#000' onload='alert(document.cookie)";
        $this->assertEquals($method->invokeArgs(new HtmlBuilder(), [$string]), $string);

        $string = "A 'quote' is <b>bold</b>";
        $this->assertEquals($method->invokeArgs(new HtmlBuilder(), [$string]), 'A \'quote\' is &lt;b&gt;bold&lt;/b&gt;');
    }

    public function testGetExcerpt()
    {
        $method = new ReflectionMethod(\Junky\PhpPrettyPrint\Html\HtmlBuilder::class, 'getExcerpt');
        $method->setAccessible(true);
        $really_long_string = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.Lorem ipsum dolor sit amet, consectetur adipiscing elit.';

        // Default 250
        // + 3 trailing dots
        $this->assertEquals($method->invokeArgs(new HtmlBuilder(), [$really_long_string => true]), 253);

        // less than 250
        $this->assertEquals($method->invokeArgs(new HtmlBuilder(), [$really_long_string => 153]), 153);

        // excerpt is false
        // god damn it phpunit...
        $this->assertEquals($method->invokeArgs(new HtmlBuilder(), [$really_long_string => strlen($really_long_string)]), strlen($really_long_string));
    }

    public function testGetExcerptException()
    {
        $class = $this->getMock(\Junky\PhpPrettyPrint\Html\HtmlBuilder::class);
        $class->method('getExcerpt', ['excerpt' => 'string'])->willThrowException(new Exception());
    }

    public function testCreate()
    {
        $this->assertInstanceOf(HtmlBuilder::class, HtmlBuilder::create());
    }
}