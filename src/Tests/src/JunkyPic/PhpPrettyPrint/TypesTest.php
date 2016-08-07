<?php
class TypesTest extends PHPUnit_Framework_TestCase{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testTypesAreEqual()
    {
        $this->assertEquals('boolean', \JunkyPic\PhpPrettyPrint\Types::TYPE_BOOLEAN);
        $this->assertEquals('integer', \JunkyPic\PhpPrettyPrint\Types::TYPE_INTEGER);
        $this->assertEquals('float', \JunkyPic\PhpPrettyPrint\Types::TYPE_FLOAT);
        $this->assertEquals('string', \JunkyPic\PhpPrettyPrint\Types::TYPE_STRING);
        $this->assertEquals('array', \JunkyPic\PhpPrettyPrint\Types::TYPE_ARRAY);
        $this->assertEquals('object', \JunkyPic\PhpPrettyPrint\Types::TYPE_OBJECT);
        $this->assertEquals('resource', \JunkyPic\PhpPrettyPrint\Types::TYPE_RESOURCE);
        $this->assertEquals('NULL', \JunkyPic\PhpPrettyPrint\Types::TYPE_NULL);
        $this->assertEquals('callable', \JunkyPic\PhpPrettyPrint\Types::TYPE_CALLABLE_CALLBACK);

        // check if getType is working properly
        $this->assertEquals('boolean', \JunkyPic\PhpPrettyPrint\Types::getType(true));
        $this->assertEquals('integer', \JunkyPic\PhpPrettyPrint\Types::getType(1));
        $this->assertEquals('float', \JunkyPic\PhpPrettyPrint\Types::getType(1.1));
        $this->assertEquals('string', \JunkyPic\PhpPrettyPrint\Types::getType('string'));
        $this->assertEquals('array', \JunkyPic\PhpPrettyPrint\Types::getType([1, 'string']));
        $this->assertEquals('object', \JunkyPic\PhpPrettyPrint\Types::getType(new DateTime()));
        $this->assertEquals('NULL', \JunkyPic\PhpPrettyPrint\Types::getType(NULL));
        $this->assertEquals('callable', \JunkyPic\PhpPrettyPrint\Types::getType(function(){}));
    }
}
