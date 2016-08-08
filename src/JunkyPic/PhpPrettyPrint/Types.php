<?php
namespace JunkyPic\PhpPrettyPrint;

/**
 * Class Types
 *
 * @package JunkyPic\PhpPrettyPrint
 */
class Types
{
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT = 'float';
    const TYPE_STRING = 'string';
    const TYPE_ARRAY = 'array';
    const TYPE_OBJECT = 'object';
    const TYPE_RESOURCE = 'resource';
    const TYPE_NULL = 'NULL';
    const TYPE_CALLABLE_CALLBACK = 'callable';

    /**
     * @param $variable
     *
     * @return string
     */
    public static function getType($variable)
    {
        if(is_callable($variable))
        {
            return static::TYPE_CALLABLE_CALLBACK;
        }

        if(is_array($variable))
        {
            return static::TYPE_ARRAY;
        }

        if(is_object($variable))
        {
            return static::TYPE_OBJECT;
        }

        if(is_string($variable))
        {
            return static::TYPE_STRING;
        }

        if(is_resource($variable))
        {
            return static::TYPE_RESOURCE;
        }

        if(is_null($variable))
        {
            return static::TYPE_NULL;
        }

        if(is_bool($variable))
        {
            return static::TYPE_BOOLEAN;
        }

        // Special case to handle integers larger than PHP_INT_MAX - 2147483647, which doesn't bloody work properly...
        // Returns 0 if the two operands are equal, 1 if the left_operand is larger than the right_operand, -1 otherwise.
        // @TODO fix this
//        switch(bccomp($variable, 2147483647))
//        {
//            case 0:
//            case 1:
//                return TYPES::TYPE_INTEGER;
//                break;
//        }

        // if(is_integer($variable))
        if((int)$variable === $variable)
        {
            return static::TYPE_INTEGER;
        }

        if(is_float($variable))
        {
            return static::TYPE_FLOAT;
        }
    }
}
