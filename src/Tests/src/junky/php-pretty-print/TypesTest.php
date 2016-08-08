<?php
class TypesTest extends PHPUnit_Framework_TestCase{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testTypesAreEqual()
    {
        $this->assertEquals('boolean', \Junky\PhpPrettyPrint\Types::TYPE_BOOLEAN);
        $this->assertEquals('integer', \Junky\PhpPrettyPrint\Types::TYPE_INTEGER);
        $this->assertEquals('float', \Junky\PhpPrettyPrint\Types::TYPE_FLOAT);
        $this->assertEquals('string', \Junky\PhpPrettyPrint\Types::TYPE_STRING);
        $this->assertEquals('array', \Junky\PhpPrettyPrint\Types::TYPE_ARRAY);
        $this->assertEquals('object', \Junky\PhpPrettyPrint\Types::TYPE_OBJECT);
        $this->assertEquals('resource', \Junky\PhpPrettyPrint\Types::TYPE_RESOURCE);
        $this->assertEquals('NULL', \Junky\PhpPrettyPrint\Types::TYPE_NULL);
        $this->assertEquals('callable', \Junky\PhpPrettyPrint\Types::TYPE_CALLABLE_CALLBACK);

        // check if getType is working properly
        $this->assertEquals('boolean', \Junky\PhpPrettyPrint\Types::getType(true));
        $this->assertEquals('integer', \Junky\PhpPrettyPrint\Types::getType(1));
        $this->assertEquals('integer', \Junky\PhpPrettyPrint\Types::getType(-1));
        $this->assertEquals('integer', \Junky\PhpPrettyPrint\Types::getType(3232));
        // Will fail - number is larger than PHP_INT_MAX on x64 systems or 32 for that matter
        // $this->assertEquals('integer', \Junky\PhpPrettyPrint\Types::getType(9223372036854775809));
        $this->assertEquals('float', \Junky\PhpPrettyPrint\Types::getType(1.1));
        $this->assertEquals('string', \Junky\PhpPrettyPrint\Types::getType('string'));
        $this->assertEquals('array', \Junky\PhpPrettyPrint\Types::getType([1, 'string']));
        $this->assertEquals('object', \Junky\PhpPrettyPrint\Types::getType(new DateTime()));
        $this->assertEquals('NULL', \Junky\PhpPrettyPrint\Types::getType(NULL));
        $this->assertEquals('callable', \Junky\PhpPrettyPrint\Types::getType(function(){}));
    }
}
