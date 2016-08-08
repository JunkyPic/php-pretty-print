<?php
use Junky\PhpPrettyPrint\Types;

class TypesTest extends PHPUnit_Framework_TestCase{
    /**
     * A basic functional test example.
     *
     * @return void
     */
    public function testTypesAreEqual()
    {
        $this->assertEquals('boolean', Types::TYPE_BOOLEAN);
        $this->assertEquals('integer', Types::TYPE_INTEGER);
        $this->assertEquals('float', Types::TYPE_FLOAT);
        $this->assertEquals('string', Types::TYPE_STRING);
        $this->assertEquals('array', Types::TYPE_ARRAY);
        $this->assertEquals('object', Types::TYPE_OBJECT);
        $this->assertEquals('resource', Types::TYPE_RESOURCE);
        $this->assertEquals('NULL', Types::TYPE_NULL);
        $this->assertEquals('callable', Types::TYPE_CALLABLE_CALLBACK);

        // check if getType is working properly
        $this->assertEquals('boolean', Types::getType(true));
        $this->assertEquals('integer', Types::getType(1));
        $this->assertEquals('integer', Types::getType(-1));
        $this->assertEquals('integer', Types::getType(3232));
        // Will fail - number is larger than PHP_INT_MAX on x64 systems or 32 for that matter
        // $this->assertEquals('integer', Types::getType(9223372036854775809));
        $this->assertEquals('float', Types::getType(1.1));
        $this->assertEquals('string', Types::getType('string'));
        $this->assertEquals('array', Types::getType([1, 'string']));
        $this->assertEquals('object', Types::getType(new DateTime()));
        $this->assertEquals('NULL', Types::getType(NULL));
        $this->assertEquals('callable', Types::getType(function(){}));
    }
}
