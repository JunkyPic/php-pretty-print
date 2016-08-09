<?php

namespace Junky\PhpPrettyPrint\Html;
use Junky\PhpPrettyPrint\Exception\HtmlBuilderException;

/**
 * Class HtmlOutput
 *
 * @package PrettyDump\Helper
 */
abstract class Html
{
    /**
     * @var null
     */
    protected static $instance = null;

    /**
     * @var array
     */
    protected $quoteFlags = [
        // Will convert double-quotes and leave single-quotes alone
        'ENT_COMPAT'     => 2,
        // Will convert both double and single quotes
        'ENT_QUOTES'     => 3,
        // Will leave both double and single quotes unconverted
        'ENT_NOQUOTES'   => 0,
        // Silently discard invalid code unit sequences instead of returning an empty string
        'ENT_IGNORE'     => 4,
        // Replace invalid code unit sequences with a Unicode Replacement Character U+FFFD (UTF-8) or &#FFFD; (otherwise) instead of returning an empty string
        'ENT_SUBSTITUTE' => 8,
        // Replace invalid code points for the given document type with a Unicode Replacement Character U+FFFD (UTF-8) or &#FFFD; (otherwise) instead of leaving
        // them as is This may be useful, for instance, to ensure the well-formedness of XML documents with embedded external content
        'ENT_DISALLOWED' => 128,
        // Handle code as HTML 401
        'ENT_HTML401'    => 0,
        // Handle code as XML 1
        'ENT_XML1'       => 16,
        // Handle code as XHTML 
        'ENT_XHTML'      => 32,
        // Handle code as HTML 5
        'ENT_HTML5'      => 48,
    ];

    /**
     * @param       $string
     * @param array $options
     *
     * @return string
     * @throws HtmlBuilderException
     */
    protected function getExcerpt($string, array $options = ['excerpt' => false])
    {
        if( ! is_bool($options['excerpt']) && ! is_integer($options['excerpt']))
        {
            throw new HtmlBuilderException("Expected boolean or integer got " . gettype($options['excerpt']));
        }

        if(isset($options['excerpt']) && true === $options['excerpt'] || strlen($string >= 250))
        {
            return substr($string, 0, 250);
        }

        if(isset($options['excerpt']) && is_integer($options['excerpt']))
        {
            return substr($string, 0, 250);
        }

        return $string;
    }

    /**
     * @return null
     */
    public static function create()
    {
        if(null === static::$instance)
        {
            static::$instance = new static;
        }

        return static::$instance;
    }

    /**
     * @param            $string
     * @param int        $quoteStyle
     * @param string     $charset
     * @param bool|false $doubleEncode
     *
     * @return string
     * @throws \Exception
     */
    protected function escape($string, $quoteStyle = ENT_SUBSTITUTE, $charset = 'utf-8', $doubleEncode = false)
    {
        if( ! in_array($quoteStyle, $this->quoteFlags))
        {
            throw new \Exception(vsprintf("Quote style not recognized. Accepted quote styles:" . str_repeat(' %s ', count($this->quoteFlags)), array_keys($this->quoteFlags)));
        }

        return htmlentities($string, $quoteStyle, $charset, $doubleEncode);
    }

    /**
     * @return array
     */
    protected function getAcceptedEncodings()
    {
        return mb_list_encodings();
    }
}