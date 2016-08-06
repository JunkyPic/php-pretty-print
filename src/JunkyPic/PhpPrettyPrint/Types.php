<?php
namespace JunkyPic\PhpPrettyPrint;

class Types
{
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_STRING = 'string';
    const TYPE_ARRAY = 'array';
    const TYPE_OBJECT = 'object';
    const TYPE_RESOURCE = 'resource';
    const TYPE_NULL = 'null';
    const TYPE_CALLABLE_CALLBACK = 'callable';

    public static function getType($variable)
    {

        if(is_callable($variable))
        {
            return Types::TYPE_CALLABLE_CALLBACK;
        }

        if(is_array($variable))
        {
            return Types::TYPE_ARRAY;
        }

        if(is_object($variable))
        {
            return Types::TYPE_OBJECT;
        }

        if(is_integer($variable))
        {
            return Types::TYPE_INTEGER;
        }

        if(is_string($variable))
        {
            return Types::TYPE_STRING;
        }


        if(is_resource($variable))
        {
            return Types::TYPE_RESOURCE;
        }

        if(is_null($variable))
        {
            return Types::TYPE_NULL;
        }

        if(is_bool($variable))
        {
            return Types::TYPE_BOOLEAN;
        }

        if(is_float($variable))
        {
            return Types::TYPE_FLOAT;
        }
    }
}