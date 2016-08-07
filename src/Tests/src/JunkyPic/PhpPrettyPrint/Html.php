<?php
class HtmlBuilderTests extends PHPUnit_Framework_TestCase{


    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function assertHtmlBuilderQuoteFlags()
    {
        $htmlBuilderReflection = new ReflectionClass(new \JunkyPic\PhpPrettyPrint\HtmlBuilder());

        $this->assertArrayHasKey('ENT_COMPAT', (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertArrayHasKey('ENT_QUOTES', (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertArrayHasKey('ENT_NOQUOTES', (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertArrayHasKey('ENT_IGNORE', (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertArrayHasKey('ENT_SUBSTITUTE', (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertArrayHasKey('ENT_DISALLOWED', (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertArrayHasKey('ENT_HTML401', (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertArrayHasKey('ENT_XML1', (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertArrayHasKey('ENT_XHTML', (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertArrayHasKey('ENT_HTML5', (array)$htmlBuilderReflection->getProperty('quoteFlags'));

        $this->assertContains(2, (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertContains(3, (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertContains(0, (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertContains(4, (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertContains(8, (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertContains(128, (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertContains(16, (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertContains(32, (array)$htmlBuilderReflection->getProperty('quoteFlags'));
        $this->assertContains(48, (array)$htmlBuilderReflection->getProperty('quoteFlags'));

        $this->assertEquals(ENT_COMPAT, (array)$htmlBuilderReflection->getProperty('quoteFlags')['ENT_COMPAT']);
        $this->assertEquals(ENT_QUOTES, (array)$htmlBuilderReflection->getProperty('quoteFlags')['ENT_QUOTES']);
        $this->assertEquals(ENT_NOQUOTES, (array)$htmlBuilderReflection->getProperty('quoteFlags')['ENT_NOQUOTES']);
        $this->assertEquals(ENT_IGNORE, (array)$htmlBuilderReflection->getProperty('quoteFlags')['ENT_IGNORE']);
        $this->assertEquals(ENT_SUBSTITUTE, (array)$htmlBuilderReflection->getProperty('quoteFlags')['ENT_SUBSTITUTE']);
        $this->assertEquals(ENT_DISALLOWED, (array)$htmlBuilderReflection->getProperty('quoteFlags')['ENT_DISALLOWED']);
        $this->assertEquals(ENT_HTML401, (array)$htmlBuilderReflection->getProperty('quoteFlags')['ENT_HTML401']);
        $this->assertEquals(ENT_XML1, (array)$htmlBuilderReflection->getProperty('quoteFlags')['ENT_XML1']);
        $this->assertEquals(ENT_XHTML, (array)$htmlBuilderReflection->getProperty('quoteFlags')['ENT_XHTML']);
        $this->assertEquals(ENT_HTML5, (array)$htmlBuilderReflection->getProperty('quoteFlags')['ENT_HTML5']);
    }

    public function assertHtmlBuilderEscape()
    {
        $method = new ReflectionMethod(\JunkyPic\PhpPrettyPrint\HtmlBuilder::class, 'escape');
        $method->setAccessible(true);

        $string = "#000' onload='alert(document.cookie)";
        $this->assertEquals($method->invokeArgs(new \JunkyPic\PhpPrettyPrint\HtmlBuilder(), [$string]), $string);

        $string = "A 'quote' is <b>bold</b>";
        $this->assertEquals($method->invokeArgs(new \JunkyPic\PhpPrettyPrint\HtmlBuilder(), [$string, ENT_QUOTES]), $string);
    }

    
}
